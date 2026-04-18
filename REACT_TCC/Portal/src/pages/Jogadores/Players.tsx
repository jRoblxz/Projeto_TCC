import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import { api } from "@/config/api";
import { useNavigate } from "react-router-dom";
import PlayerCard from "@/components/ui/PlayerCard";
import {
  Search,
  Plus,
  Loader2,
  X,
  ChevronLeft,
  ChevronRight,
} from "lucide-react";
import toast from "react-hot-toast";
import { isUserAdmin } from "../../utils/auth";
import { getAttributesByPosition } from "@/utils/playerAttributes";

// 1. INTERFACE CORRIGIDA
interface Player {
  id: number;
  rating_medio: number;
  posicao_principal: string;
  altura_cm: number;
  peso_kg: number;
  pe_preferido: string;
  pessoa: {
    nome_completo: string;
    foto_url_completa?: string;
    sub_divisao: string;
  };
  ultima_avaliacao?: any;
}

const FILTERS = [
  { label: "Todos", value: "Todos" },
  { label: "Sub-17", value: "Sub-17" },
  { label: "Sub-20", value: "Sub-20" },
  { label: "Profissional", value: "Profissional" },
  { label: "Rating Alto (8+)", value: "high-rating" },
];

const Players: React.FC = () => {
  const navigate = useNavigate();

  // Estados de Dados
  const [players, setPlayers] = useState<Player[]>([]);
  const [loading, setLoading] = useState(true);
  const [meta, setMeta] = useState<any>(null);
  const [page, setPage] = useState(1);

  // Estados de Filtro
  const [search, setSearch] = useState("");
  const [activeFilter, setActiveFilter] = useState("Todos");

  // 2. ESTADOS DO MODAL ATUALIZADOS
  const [showRatingModal, setShowRatingModal] = useState(false);
  const [selectedPlayer, setSelectedPlayer] = useState<Player | null>(null);
  const [attributes, setAttributes] = useState<any>({});

  const isAdmin = isUserAdmin();

  // --- CARREGAR DADOS ---
  const loadPlayers = async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams();
      params.append("page", page.toString());
      if (search) params.append("search", search);
      if (activeFilter !== "Todos") params.append("sub_divisao", activeFilter);

      const response = await api.get(`/players?${params.toString()}`);

      setPlayers(response.data.data);
      setMeta(response.data.meta || response.data);
    } catch (error) {
      console.error(error);
      toast.error("Erro ao carregar jogadores");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const timer = setTimeout(() => {
      loadPlayers();
    }, 500);
    return () => clearTimeout(timer);
  }, [page, search, activeFilter]);

  // --- AÇÕES ---
  const handleDelete = async (player: Player) => {
    if (
      !confirm(`Tem certeza que deseja excluir ${player.pessoa.nome_completo}?`)
    )
      return;

    try {
      await api.delete(`/players/${player.id}`);
      toast.success("Jogador excluído!");
      loadPlayers();
    } catch (error) {
      toast.error("Erro ao excluir");
    }
  };

  // 3. FUNÇÕES DO MODAL DINÂMICO
  const openRatingModal = (player: Player) => {
    setSelectedPlayer(player);

    const posAttributes = getAttributesByPosition(player.posicao_principal);
    const initialAttributes: any = {
      observacoes: player.ultima_avaliacao?.observacoes || "",
    };

    posAttributes.forEach((attr) => {
      initialAttributes[attr.key] = player.ultima_avaliacao?.[attr.key] || 0;
    });

    setAttributes(initialAttributes);
    setShowRatingModal(true);
  };

  const handleAttributeChange = (field: string, value: string) => {
    // 1. Permite que o usuário apague o campo (deixe vazio) para poder digitar outro número
    if (value === "") {
      setAttributes((prev: any) => ({
        ...prev,
        [field]: "",
      }));
      return;
    }

    // 2. Converte para número
    let numValue = Number(value);

    // 3. Trava o valor entre 0 e 10
    if (numValue < 0) numValue = 0;
    if (numValue > 10) numValue = 10;

    setAttributes((prev: any) => ({
      ...prev,
      [field]: numValue,
    }));
  };

  const handleUpdateRating = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedPlayer) return;

    try {
      await api.put(`/players/${selectedPlayer.id}`, { atributos: attributes });
      toast.success("Avaliação registrada com sucesso!");
      setShowRatingModal(false);
      loadPlayers();
    } catch (error) {
      toast.error("Erro ao registrar avaliação");
    }
  };

  // Cálculo da média em tempo real no topo do modal
  const numericValues = Object.keys(attributes)
    .filter((key) => key !== "observacoes")
    .map((key) => Number(attributes[key]) || 0);

  const currentAverage =
    numericValues.length > 0
      ? (
          numericValues.reduce((a, b) => a + b, 0) / numericValues.length
        ).toFixed(1)
      : "0.0";

  return (
    <Layout>
      <div className="p-6 min-h-screen pb-20">
        {/* === HEADER === */}
        <div className="text-center mb-8 bg-brand-primary dark:bg-gray-900 rounded-xl p-6 shadow-md text-white">
          <h1 className="text-3xl font-bold mb-2 ">Jogadores Inscritos</h1>
          <p className="text-gray-500">
            Sistema de Avaliação de Atletas
            <p className="text-sm text-gray-500 dark:text-gray-400 font-medium">
              Gerencie os jogadores inscritos e suas avaliações (Para avaliar
              clique no icone de editar).
            </p>
          </p>
        </div>

        {/* === BARRA DE FILTROS E BUSCA === */}
        <div className="bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm border dark:border-gray-700 border-gray-100 mb-8 flex flex-col lg:flex-row gap-4 justify-between items-center sticky top-4 z-40">
          <div className="relative w-full lg:w-1/3 ">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
            <input
              type="text"
              placeholder="Buscar por nome ou posição..."
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
                setPage(1);
              }}
              className="w-full pl-10 pr-4 py-3 border dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-full focus:ring-2 focus:ring-brand-darkred outline-none transition"
            />
          </div>

          <div className="flex gap-2 overflow-x-auto pb-2 w-full lg:w-auto scrollbar-hide">
            {FILTERS.map((f) => (
              <button
                key={f.value}
                onClick={() => {
                  setActiveFilter(f.value);
                  setPage(1);
                }}
                className={`px-4 py-2 rounded-full whitespace-nowrap text-sm font-bold transition border ${
                  activeFilter === f.value
                    ? "bg-brand-darkred text-white border-brand-darkred shadow-md transform scale-105"
                    : "bg-white text-gray-600 dark:text-white hover:bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                }`}
              >
                {f.label}
              </button>
            ))}
          </div>
        </div>

        {/* === GRID DE JOGADORES === */}
        {loading ? (
          <div className="flex justify-center h-64 items-center">
            <Loader2 className="h-12 w-12 text-brand-darkred animate-spin" />
          </div>
        ) : (
          <div className="grid gap-8 justify-items-center grid-cols-[repeat(auto-fit,minmax(280px,1fr))] w-full">
            {players.map((player) => (
              <PlayerCard
                key={player.id}
                player={player}
                isAdmin={isAdmin}
                onEditRating={() => navigate(`/jogadores/${player.id}/edit`)}
                onDelete={handleDelete}
                onViewMore={(id) => navigate(`/jogadores/${id}`)}
              />
            ))}

            {players.length === 0 && !loading && (
              <div className="col-span-full text-center text-gray-400 py-10">
                <p className="text-xl">Nenhum jogador encontrado.</p>
              </div>
            )}

            {/* Botão Adicionar */}
            {isAdmin && (
              <div
                onClick={() => navigate("/instrucoes")}
                className="w-[280px] h-[380px] border-4 border-dashed border-brand-darkred/30 bg-brand-darkred/5 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-brand-darkred/10 hover:border-brand-darkred transition-all group"
              >
                <div className="w-20 h-20 bg-brand-darkred/10 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                  <Plus className="h-10 w-10 text-brand-darkred" />
                </div>
                <span className="mt-4 text-brand-darkred font-bold text-lg">
                  Novo Jogador
                </span>
              </div>
            )}
          </div>
        )}

        {/* === PAGINAÇÃO === */}
        {meta && meta.last_page > 1 && (
          <div className="flex justify-center items-center gap-4 mt-8">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page === 1}
              className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <span className="text-sm font-medium text-gray-600">
              Página {meta.current_page} de {meta.last_page}
            </span>
            <button
              onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
              disabled={page === meta.last_page}
              className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              <ChevronRight className="h-5 w-5" />
            </button>
          </div>
        )}

        {/* === MODAL EDITAR RATING (AGORA DINÂMICO) === */}
        {showRatingModal && selectedPlayer && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative">
              <button
                onClick={() => setShowRatingModal(false)}
                className="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
              >
                <X size={24} />
              </button>

              <h2 className="text-2xl font-bold text-brand-darkred mb-1">
                Avaliação Técnica
              </h2>
              <p className="text-gray-600 mb-4 font-medium">
                {selectedPlayer.pessoa.nome_completo}
              </p>

              {/* Nota Média Calculada no Topo */}
              <div className="flex justify-center items-center mb-6 bg-gray-50 rounded-xl py-3 border border-gray-100">
                <div className="text-center">
                  <span className="block text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">
                    Overall (Média)
                  </span>
                  <span className="text-4xl font-extrabold text-brand-primary">
                    {currentAverage}
                  </span>
                </div>
              </div>

              <form onSubmit={handleUpdateRating}>
                {/* Atributos Mapeados Dinamicamente baseados na Posição */}
                <div className="grid grid-cols-2 gap-4 mb-4">
                  {getAttributesByPosition(
                    selectedPlayer.posicao_principal,
                  ).map((attr) => (
                    <div key={attr.key} className="flex flex-col">
                      <label className="text-xs font-bold text-gray-700 mb-1">
                        {attr.label}
                      </label>
                      <input
                        type="number"
                        min="0"
                        max="10"
                        step="0.1"
                        value={
                          attributes[attr.key] !== undefined
                            ? attributes[attr.key]
                            : ""
                        }
                        onChange={(e) =>
                          handleAttributeChange(attr.key, e.target.value)
                        }
                        className="w-full text-lg font-bold text-gray-800 border-2 border-gray-200 rounded-lg p-2 focus:border-brand-darkred focus:ring-1 focus:ring-brand-darkred outline-none transition"
                      />
                    </div>
                  ))}
                </div>

                {/* Campo de Observações */}
                <div className="mb-6">
                  <label className="text-xs font-bold text-gray-700 mb-1 block">
                    Observações Gerais
                  </label>
                  <textarea
                    rows={3}
                    value={attributes.observacoes || ""}
                    onChange={(e) =>
                      setAttributes((prev: any) => ({
                        ...prev,
                        observacoes: e.target.value,
                      }))
                    }
                    placeholder="Adicione comentários sobre a prestação do atleta..."
                    className="w-full text-sm font-medium text-gray-800 border-2 border-gray-200 rounded-lg p-3 focus:border-brand-darkred focus:ring-1 focus:ring-brand-darkred outline-none transition resize-none"
                  />
                </div>

                <div className="flex gap-3">
                  <button
                    type="button"
                    onClick={() => setShowRatingModal(false)}
                    className="flex-1 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="flex-1 py-3 bg-brand-darkred text-white rounded-lg font-bold hover:bg-[#a01519] transition shadow-lg"
                  >
                    Salvar Avaliação
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default Players;
