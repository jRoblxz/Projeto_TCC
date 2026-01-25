import React, { useEffect, useState, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import { ArrowLeft, Save, RotateCcw, Shirt } from "lucide-react";
import toast from "react-hot-toast";

// === CONFIGURAÇÕES DE FORMAÇÃO (X, Y em %) ===
const FORMATIONS: any = {
  '4-4-2': [
      { pos: 'GK', x: 50, y: 90 },
      { pos: 'LB', x: 15, y: 75 }, { pos: 'CB', x: 38, y: 80 }, { pos: 'CB', x: 62, y: 80 }, { pos: 'RB', x: 85, y: 75 },
      { pos: 'LM', x: 15, y: 45 }, { pos: 'CM', x: 38, y: 50 }, { pos: 'CM', x: 62, y: 50 }, { pos: 'RM', x: 85, y: 45 },
      { pos: 'ST', x: 38, y: 20 }, { pos: 'ST', x: 62, y: 20 }
  ],
  '4-3-3': [
      { pos: 'GK', x: 50, y: 90 },
      { pos: 'LB', x: 15, y: 75 }, { pos: 'CB', x: 38, y: 80 }, { pos: 'CB', x: 62, y: 80 }, { pos: 'RB', x: 85, y: 75 },
      { pos: 'CM', x: 30, y: 50 }, { pos: 'CM', x: 50, y: 55 }, { pos: 'CM', x: 70, y: 50 },
      { pos: 'LW', x: 15, y: 25 }, { pos: 'ST', x: 50, y: 20 }, { pos: 'RW', x: 85, y: 25 }
  ],
  '3-5-2': [
      { pos: 'GK', x: 50, y: 90 },
      { pos: 'CB', x: 30, y: 80 }, { pos: 'CB', x: 50, y: 82 }, { pos: 'CB', x: 70, y: 80 },
      { pos: 'LM', x: 10, y: 50 }, { pos: 'CDM', x: 35, y: 55 }, { pos: 'CAM', x: 50, y: 45 }, { pos: 'CDM', x: 65, y: 55 }, { pos: 'RM', x: 90, y: 50 },
      { pos: 'ST', x: 40, y: 20 }, { pos: 'ST', x: 60, y: 20 }
  ]
};

const EditorTimes: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  
  const [teams, setTeams] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  // Refs para controlar o arrasto sem depender apenas do state (performance)
  const draggingItem = useRef<{ id: number, fromTeam: string } | null>(null);
  const fieldRefs = {
      A: useRef<HTMLDivElement>(null),
      B: useRef<HTMLDivElement>(null)
  };

  useEffect(() => {
    fetchTeams();
  }, [id]);

  const applyFormation = (players: any[], formationName: string) => {
      const coords = FORMATIONS[formationName] || FORMATIONS['4-4-2'];
      let fieldIndex = 0;
      
      return players.map(p => {
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
      const data = response.data;
      
      // Auto-distribuição se estiverem no centro
      ['A', 'B'].forEach(key => {
          if (data[key]) {
              const team = data[key];
              // Conta quantos estão no "limbo" (50,50) vindo do backend
              const playersInCenter = team.players.filter((p: any) => p.x === 50 && p.y === 50 && p.inField).length;
              
              if (playersInCenter > 5) {
                  team.formation = team.formation || '4-4-2';
                  team.players = applyFormation(team.players, team.formation);
              }
          }
      });

      setTeams(data);
    } catch (error) {
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
    }
  };

  const handleReset = async () => {
    if(!confirm("Resetar fará uma nova geração automática. Continuar?")) return;
    try {
        setLoading(true);
        await api.post(`/peneiras/${id}/teams/generate`);
        window.location.reload();
    } catch(e) { 
        setLoading(false); 
        toast.error("Erro ao resetar"); 
    }
  };

  const handleFormationChange = (teamKey: string, newFormation: string) => {
      const team = teams[teamKey];
      const newPlayers = applyFormation(team.players, newFormation);
      setTeams({ ...teams, [teamKey]: { ...team, formation: newFormation, players: newPlayers } });
  };

  // =========================================================================
  // LÓGICA NOVA DE ARRASTO (POINTER EVENTS)
  // =========================================================================

  const handlePointerDown = (e: React.PointerEvent, player: any, teamKey: string) => {
      e.preventDefault(); // Impede seleção de texto
      e.stopPropagation();
      
      // Registra quem estamos arrastando
      draggingItem.current = { id: player.id, fromTeam: teamKey };
      
      // Adiciona listeners globais para mover e soltar
      window.addEventListener('pointermove', onPointerMove);
      window.addEventListener('pointerup', onPointerUp);
  };

  const onPointerMove = (e: PointerEvent) => {
      if (!draggingItem.current) return;
      
      // Aqui poderíamos atualizar a posição visualmente em tempo real
      // Mas para simplificar, o React atualiza no final ou você pode implementar
      // um "ghost" element.
      // Para este exemplo, vamos atualizar o state direto para ver o movimento.
      
      // ATENÇÃO: Atualizar state no mousemove pode ser pesado, mas para 22 jogadores é ok.
      // Para otimizar, usaríamos refs e transform CSS direto.
      
      const { id, fromTeam } = draggingItem.current;
      
      // Precisamos descobrir em qual campo o mouse está em cima
      let targetTeam = fromTeam;
      let targetRect = fieldRefs[fromTeam as 'A'|'B'].current?.getBoundingClientRect();

      // Verifica se o mouse está sobre o campo A
      const rectA = fieldRefs['A'].current?.getBoundingClientRect();
      if (rectA && e.clientX >= rectA.left && e.clientX <= rectA.right && e.clientY >= rectA.top && e.clientY <= rectA.bottom) {
          targetTeam = 'A';
          targetRect = rectA;
      }
      
      // Verifica se o mouse está sobre o campo B
      const rectB = fieldRefs['B'].current?.getBoundingClientRect();
      if (rectB && e.clientX >= rectB.left && e.clientX <= rectB.right && e.clientY >= rectB.top && e.clientY <= rectB.bottom) {
          targetTeam = 'B';
          targetRect = rectB;
      }

      if (targetRect) {
          // Calcula nova posição %
          const x = ((e.clientX - targetRect.left) / targetRect.width) * 100;
          const y = ((e.clientY - targetRect.top) / targetRect.height) * 100;

          setTeams((prevTeams: any) => {
              const newTeams = JSON.parse(JSON.stringify(prevTeams));
              
              // Remove do time antigo
              const sourcePlayers = newTeams[fromTeam].players;
              const pIndex = sourcePlayers.findIndex((p: any) => p.id === id);
              if (pIndex === -1) return prevTeams;
              
              const playerObj = sourcePlayers[pIndex];
              
              // Se mudou de time
              if (fromTeam !== targetTeam) {
                  sourcePlayers.splice(pIndex, 1); // Remove do antigo
                  playerObj.inField = true;
                  playerObj.x = Math.max(0, Math.min(100, x));
                  playerObj.y = Math.max(0, Math.min(100, y));
                  newTeams[targetTeam].players.push(playerObj); // Adiciona no novo
                  
                  // Atualiza a ref para continuar o rastreio no time novo
                  draggingItem.current = { id, fromTeam: targetTeam };
              } else {
                  // Mesmo time, só atualiza posição
                  playerObj.inField = true; // Se está movendo no campo, é titular
                  playerObj.x = Math.max(0, Math.min(100, x));
                  playerObj.y = Math.max(0, Math.min(100, y));
              }
              
              return newTeams;
          });
      }
  };

  const onPointerUp = () => {
      window.removeEventListener('pointermove', onPointerMove);
      window.removeEventListener('pointerup', onPointerUp);
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

  const TeamSection = ({ teamKey, colorClass }: { teamKey: string, colorClass: string }) => {
    if (!teams || !teams[teamKey]) return null;
    const team = teams[teamKey];
    const fieldPlayers = team.players.filter((p: any) => p.inField);
    const benchPlayers = team.players.filter((p: any) => !p.inField);

    return (
        <div className={`bg-white p-5 rounded-xl shadow-lg border-t-4 ${colorClass} h-full flex flex-col`}>
            <div className="flex justify-between items-center mb-4">
                <h3 className="text-xl font-bold text-[#14244D]">{team.nome}</h3>
                <select 
                    value={team.formation || '4-4-2'}
                    onChange={(e) => handleFormationChange(teamKey, e.target.value)}
                    className="border border-gray-300 rounded px-2 py-1 text-sm bg-gray-50 outline-none"
                >
                    {Object.keys(FORMATIONS).map(f => <option key={f} value={f}>{f}</option>)}
                </select>
            </div>

            {/* --- CAMPO --- */}
            <div 
                // @ts-ignore
                ref={fieldRefs[teamKey]}
                className="relative w-full aspect-[3/4] bg-green-700 rounded-lg border-4 border-white shadow-inner mb-4 overflow-hidden select-none touch-none"
                style={{
                    background: 'linear-gradient(to bottom, #2d5016 0%, #3d6b1f 50%, #2d5016 100%)'
                }}
            >
                {/* Linhas */}
                <div className="absolute inset-0 pointer-events-none opacity-60">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5%" y="5%" width="90%" height="90%" fill="none" stroke="white" strokeWidth="2" />
                        <line x1="5%" y1="50%" x2="95%" y2="50%" stroke="white" strokeWidth="2" />
                        <circle cx="50%" cy="50%" r="10%" fill="none" stroke="white" strokeWidth="2" />
                    </svg>
                </div>

                {/* Jogadores */}
                {fieldPlayers.map((player: any) => (
                    <div 
                        key={player.id}
                        onPointerDown={(e) => handlePointerDown(e, player, teamKey)}
                        className="absolute flex flex-col items-center cursor-move hover:scale-110 active:scale-110 transition-transform z-20 touch-none"
                        style={{ 
                            left: `${player.x}%`, 
                            top: `${player.y}%`, 
                            transform: 'translate(-50%, -50%)',
                        }}
                    >
                        {/* Botão para mandar pro banco rápido (opcional, ajuda na usabilidade) */}
                        <div 
                            onClick={(e) => { e.stopPropagation(); moveToBench(player, teamKey); }}
                            className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] cursor-pointer hover:bg-red-700 z-30"
                            title="Mandar para o banco"
                        >
                            x
                        </div>

                        <div className={`w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-2 border-yellow-400 ${teamKey === 'A' ? 'bg-blue-800' : 'bg-red-800'}`}>
                            {player.rating}
                        </div>
                        <div className="bg-black/60 text-white text-[10px] px-2 py-0.5 rounded mt-1 font-bold whitespace-nowrap shadow-sm pointer-events-none">
                            {player.name.split(' ')[0]}
                        </div>
                        <div className="text-[9px] text-yellow-300 font-bold drop-shadow-md pointer-events-none">
                            {player.pos}
                        </div>
                    </div>
                ))}
            </div>

            {/* --- BANCO --- */}
            <div className="bg-gray-100 p-3 rounded-lg border-2 border-dashed border-gray-300 min-h-[120px] flex-1">
                <div className="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-1">
                    <Shirt size={14}/> Banco de Reservas ({benchPlayers.length})
                </div>
                <div className="flex flex-wrap gap-2">
                    {benchPlayers.map((player: any) => (
                        <div 
                            key={player.id}
                            onClick={() => moveFromBenchToField(player, teamKey)}
                            className="bg-white px-2 py-1.5 rounded border border-gray-200 shadow-sm text-xs flex items-center gap-2 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-colors"
                            title="Clique para enviar ao campo"
                        >
                            <span className="font-bold text-gray-700">{player.name}</span>
                            <span className="bg-yellow-100 text-yellow-800 px-1 rounded font-bold">{player.rating}</span>
                        </div>
                    ))}
                    {benchPlayers.length === 0 && <span className="text-gray-400 text-xs italic">Vazio</span>}
                </div>
            </div>
        </div>
    );
  };

  if (loading || !teams) return <Layout><div className="flex justify-center p-20 text-[#14244D]">Carregando...</div></Layout>;

  return (
    <Layout>
      <div className="max-w-[1600px] mx-auto p-4 ">
        <div className="flex flex-col md:flex-row justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div className="flex items-center gap-4">
                <button onClick={() => navigate(-1)} className="p-2 hover:bg-gray-100 rounded-full transition"><ArrowLeft /></button>
                <div>
                    <h1 className="text-2xl font-bold text-[#14244D]">Editor de Times</h1>
                    <p className="text-sm text-gray-500">Arraste os jogadores no campo. Clique nos reservas para subir.</p>
                </div>
            </div>
            <div className="flex gap-3 mt-4 md:mt-0">
                <button onClick={handleReset} className="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 flex items-center gap-2 transition">
                    <RotateCcw size={18}/> Resetar
                </button>
                <button onClick={handleSave} disabled={saving} className="px-6 py-2 bg-[#14244D] text-white font-bold rounded-lg hover:bg-[#1e3a8a] shadow-md flex items-center gap-2 disabled:opacity-50 transition">
                    <Save size={18}/> {saving ? 'Salvando...' : 'Salvar Times'}
                </button>
            </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <TeamSection teamKey="A" colorClass="border-blue-500" />
            <TeamSection teamKey="B" colorClass="border-red-500" />
        </div>
      </div>
    </Layout>
  );
};

export default EditorTimes;