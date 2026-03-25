import React, { useEffect, useState, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "@/config/api";
import Layout from "@/components/layouts/Layout";
import TeamCard from "@/components/ui/TeamCard";
import { ArrowLeft, Save, RotateCcw } from "lucide-react";
import toast from "react-hot-toast";
import { isUserAdmin } from "@/utils/auth";
import { FORMATIONS } from "@/utils/formations";

const EditorTimes: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isAdmin = isUserAdmin();
  const [teams, setTeams] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  // Refs para controlar o arrasto sem depender apenas do state (performance)
  const draggingItem = useRef<{ id: number; fromTeam: string } | null>(null);
  const fieldRefs = {
    A: useRef<HTMLDivElement>(null),
    B: useRef<HTMLDivElement>(null),
  };

  useEffect(() => {
    fetchTeams();
  }, [id]);

  const applyFormation = (players: any[], formationName: string) => {
    const coords = FORMATIONS[formationName] || FORMATIONS["4-4-2"];
    let fieldIndex = 0;

    return players.map((p) => {
      if (p.inField) {
        const pos = coords[fieldIndex] || { x: 50, y: 50 };
        fieldIndex++;
        return { ...p, x: pos.x, y: pos.y };
      }
      return p;
    });
  };

  const fetchTeams = async () => {
    try {
      const response = await api.get(`/peneiras/${id}/teams`);
      let data = response.data;

      // BLINDAGEM: Times padrão caso o banco de dados retorne vazio
      let teamA = { name: "Equipe A", players: [], formation: "4-4-2" };
      let teamB = { name: "Equipe B", players: [], formation: "4-4-2" };

      if (Array.isArray(data)) {
        teamA = data.find((t: any) => t.name?.includes("A")) || data[0] || teamA;
        teamB = data.find((t: any) => t.name?.includes("B")) || data[1] || teamB;
      }

      // Garante que o objeto SEMPRE tenha as chaves A e B
      const safeData: any = { A: teamA, B: teamB };

      // Auto-distribuição se estiverem no centro
      ["A", "B"].forEach((key) => {
        const team = safeData[key];
        if (team && team.players) {
          const playersInCenter = team.players.filter(
            (p: any) => p.x === 50 && p.y === 50 && p.inField,
          ).length;

          if (playersInCenter > 5) {
            team.formation = team.formation || "4-4-2";
            team.players = applyFormation(team.players, team.formation);
          }
        }
      });

      setTeams(safeData);
    } catch (error) {
      console.error(error);
      toast.error("Erro ao carregar times.");
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    setSaving(true);
    try {
      await api.post(`/peneiras/${id}/teams/save`, { teams });
      toast.success("Times salvos com sucesso!");
    } catch (error) {
      toast.error("Erro ao salvar.");
    } finally {
      setSaving(false);
      navigate(`/peneiras/${id}`);
    }
  };

  const handleReset = async () => {
    if (!confirm("Resetar fará uma nova geração automática. Continuar?"))
      return;
    try {
      setLoading(true);
      await api.post(`/peneiras/${id}/teams/generate`);
      window.location.reload();
    } catch (e) {
      setLoading(false);
      toast.error("Erro ao resetar");
    }
  };

  const handleFormationChange = (teamKey: string, newFormation: string) => {
    const team = teams[teamKey];
    const newPlayers = applyFormation(team.players, newFormation);
    setTeams({
      ...teams,
      [teamKey]: { ...team, formation: newFormation, players: newPlayers },
    });
  };

  // =========================================================================
  // LÓGICA DE ARRASTO (POINTER EVENTS)
  // =========================================================================

  const handlePointerDown = (
    e: React.PointerEvent,
    player: any,
    teamKey: string,
  ) => {
    if (!isAdmin) return;
    e.preventDefault(); // Impede seleção de texto
    e.stopPropagation();

    // Registra quem estamos arrastando
    draggingItem.current = { id: player.id, fromTeam: teamKey };

    // Adiciona listeners globais para mover e soltar
    window.addEventListener("pointermove", onPointerMove);
    window.addEventListener("pointerup", onPointerUp);
  };

  const onPointerMove = (e: PointerEvent) => {
    if (!draggingItem.current) return;

    const { id, fromTeam } = draggingItem.current;

    let targetTeam = fromTeam;
    let targetRect = fieldRefs[fromTeam as "A" | "B"].current?.getBoundingClientRect();

    const rectA = fieldRefs["A"].current?.getBoundingClientRect();
    if (
      rectA &&
      e.clientX >= rectA.left && e.clientX <= rectA.right &&
      e.clientY >= rectA.top && e.clientY <= rectA.bottom
    ) {
      targetTeam = "A";
      targetRect = rectA;
    }

    const rectB = fieldRefs["B"].current?.getBoundingClientRect();
    if (
      rectB &&
      e.clientX >= rectB.left && e.clientX <= rectB.right &&
      e.clientY >= rectB.top && e.clientY <= rectB.bottom
    ) {
      targetTeam = "B";
      targetRect = rectB;
    }

    if (targetRect) {
      const x = ((e.clientX - targetRect.left) / targetRect.width) * 100;
      const y = ((e.clientY - targetRect.top) / targetRect.height) * 100;

      // ATUALIZAÇÃO SEGURA DA REF: Fora do setTeams para não bugar a memória!
      if (fromTeam !== targetTeam) {
        draggingItem.current = { id, fromTeam: targetTeam };
      }

      setTeams((prevTeams: any) => {
        const newTeams = JSON.parse(JSON.stringify(prevTeams));

        // BLINDAGEM MÁXIMA: Previne a tela branca caso arraste rápido demais
        if (!newTeams[fromTeam] || !newTeams[fromTeam].players) return prevTeams;
        if (!newTeams[targetTeam]) newTeams[targetTeam] = { players: [], name: `Equipe ${targetTeam}` };
        if (!newTeams[targetTeam].players) newTeams[targetTeam].players = [];

        const sourcePlayers = newTeams[fromTeam].players;
        const pIndex = sourcePlayers.findIndex((p: any) => p.id === id);
        
        if (pIndex === -1) return prevTeams;

        const playerObj = sourcePlayers[pIndex];

        if (fromTeam !== targetTeam) {
          sourcePlayers.splice(pIndex, 1);
          playerObj.inField = true;
          playerObj.x = Math.max(0, Math.min(100, x));
          playerObj.y = Math.max(0, Math.min(100, y));
          newTeams[targetTeam].players.push(playerObj);
        } else {
          playerObj.inField = true;
          playerObj.x = Math.max(0, Math.min(100, x));
          playerObj.y = Math.max(0, Math.min(100, y));
        }

        return newTeams;
      });
    }
  };

  const onPointerUp = () => {
    window.removeEventListener("pointermove", onPointerMove);
    window.removeEventListener("pointerup", onPointerUp);
    draggingItem.current = null;
  };

  // Função para mover do banco para o campo (clique simples ou arrasto simplificado)
  const moveFromBenchToField = (player: any, teamKey: string) => {
    setTeams((prev: any) => {
      const newTeams = { ...prev };
      const p = newTeams[teamKey].players.find((p: any) => p.id === player.id);
      if (p) {
        p.inField = true;
        p.x = 50;
        p.y = 50;
      }
      return newTeams;
    });
  };

  // Função para mover do campo para o banco (botão X)
  const moveToBench = (player: any, teamKey: string) => {
    setTeams((prev: any) => {
      const newTeams = { ...prev };
      const p = newTeams[teamKey].players.find((p: any) => p.id === player.id);
      if (p) p.inField = false;
      return newTeams;
    });
  };

  if (loading || !teams)
    return (
      <Layout>
        <div className="flex justify-center p-20 text-[#14244D]">
          Carregando...
        </div>
      </Layout>
    );

  return (
    <Layout>
      <div className="max-w-[1600px] mx-auto p-4 ">
        <div className="flex flex-col md:flex-row justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate(`/peneiras/${id}`)}
              className="p-2 hover:bg-gray-100 rounded-full transition"
            >
              <ArrowLeft />
            </button>
            <div>
              <h1 className="text-2xl font-bold text-[#14244D]">
                Editor de Times
              </h1>
              <p className="text-sm text-gray-500">
                Arraste os jogadores no campo. Clique nos reservas para subir.
              </p>
            </div>
          </div>
          <div className="flex gap-3 mt-4 md:mt-0">
            {isAdmin && (
              <>
                <button
                  onClick={handleReset}
                  className="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 flex items-center gap-2 transition"
                >
                  <RotateCcw size={18} /> Resetar
                </button>
                <button
                  onClick={handleSave}
                  disabled={saving}
                  className="px-6 py-2 bg-[#14244D] text-white font-bold rounded-lg hover:bg-[#1e3a8a] shadow-md flex items-center gap-2 disabled:opacity-50 transition"
                >
                  <Save size={18} /> {saving ? "Salvando..." : "Salvar Times"}
                </button>
              </>
            )}
            {!isAdmin && (
              <span className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded font-bold text-sm">
                Modo Visualização
              </span>
            )}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <TeamCard
            teamKey="A"
            team={teams.A}
            colorClass="border-blue-500"
            isAdmin={isAdmin}
            onFormationChange={handleFormationChange}
            onPointerDown={handlePointerDown}
            onMoveToBench={moveToBench}
            onMoveFromBenchToField={moveFromBenchToField}
            fieldRef={fieldRefs.A}
          />
          <TeamCard
            teamKey="B"
            team={teams.B}
            colorClass="border-red-500"
            isAdmin={isAdmin}
            onFormationChange={handleFormationChange}
            onPointerDown={handlePointerDown}
            onMoveToBench={moveToBench}
            onMoveFromBenchToField={moveFromBenchToField}
            fieldRef={fieldRefs.B}
          />
        </div>
      </div>
    </Layout>
  );
};

export default EditorTimes;
