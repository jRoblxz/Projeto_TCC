import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import { api } from "../config/api";
import { useNavigate } from "react-router-dom";
import PlayerCard from "../components/ui/PlayerCard"; // Importe o card que criamos
import { Search, Plus, Loader2, X, ChevronLeft, ChevronRight } from "lucide-react";
import toast from "react-hot-toast";
import { isUserAdmin } from "../utils/auth"; // Importe a função

// Interface dos dados (Baseado no seu BD)
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
  ultima_avaliacao?: {
    observacoes: string;
  };
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
  const [meta, setMeta] = useState<any>(null); // Paginação
  const [page, setPage] = useState(1);
  
  // Estados de Filtro
  const [search, setSearch] = useState("");
  const [activeFilter, setActiveFilter] = useState("Todos");

  // Estados de Modal
  const [showRatingModal, setShowRatingModal] = useState(false);
  const [selectedPlayer, setSelectedPlayer] = useState<Player | null>(null);
  const [newRating, setNewRating] = useState<number | string>("");

  // Permissões (Simulado - você pode pegar do Contexto de Auth)
  const isAdmin = isUserAdmin(); // Use a função importada

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
      setMeta(response.data.meta || response.data); // Ajuste conforme retorno do Laravel
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
    }, 500); // Debounce na busca
    return () => clearTimeout(timer);
  }, [page, search, activeFilter]);

  // --- AÇÕES ---
  const handleDelete = async (player: Player) => {
    if (!confirm(`Tem certeza que deseja excluir ${player.pessoa.nome_completo}?`)) return;
    
    try {
        await api.delete(`/players/${player.id}`);
        toast.success("Jogador excluído!");
        loadPlayers();
    } catch (error) {
        toast.error("Erro ao excluir");
    }
  };

  const handleUpdateRating = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedPlayer) return;

    try {
        await api.put(`/players/${selectedPlayer.id}`, { rating_medio: newRating });
        toast.success("Rating atualizado!");
        setShowRatingModal(false);
        loadPlayers();
    } catch (error) {
        toast.error("Erro ao atualizar rating");
    }
  };

  const openRatingModal = (player: Player) => {
    setSelectedPlayer(player);
    setNewRating(player.rating_medio);
    setShowRatingModal(true);
  };

  return (
    <Layout>
      <div className="p-6 min-h-screen pb-20">
        
        {/* === HEADER === */}
        <div className="text-center mb-8 bg-[#14244D] dark:bg-gray-900 rounded-xl p-6 shadow-md text-white">
            <h1 className="text-3xl font-bold mb-2 ">
                Jogadores Inscritos
            </h1>
            <p className="text-gray-500">Sistema de Avaliação de Atletas</p>
        </div>

        {/* === BARRA DE FILTROS E BUSCA === */}
        <div className="bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm border dark:border-gray-700 border-gray-100 mb-8 flex flex-col lg:flex-row gap-4 justify-between items-center sticky top-4 z-40">
            
            {/* Busca */}
            <div className="relative w-full lg:w-1/3 ">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input 
                    type="text" 
                    placeholder="uscar por nome ou posição..."
                    value={search}
                    onChange={(e) => { setSearch(e.target.value); setPage(1); }}
                    className="w-full pl-10 pr-4 py-3 border dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-full focus:ring-2 focus:ring-[#8B0000] outline-none transition"
                />
            </div>

            {/* Botões de Filtro */}
            <div className="flex gap-2 overflow-x-auto pb-2 w-full lg:w-auto scrollbar-hide">
                {FILTERS.map((f) => (
                    <button
                        key={f.value}
                        onClick={() => { setActiveFilter(f.value); setPage(1); }}
                        className={`
                            px-4 py-2 rounded-full whitespace-nowrap text-sm font-bold transition border
                            ${activeFilter === f.value 
                                ? "bg-[#8B0000] text-white border-[#8B0000] shadow-md transform scale-105" 
                                : "bg-white text-gray-600 dark:text-white hover:bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700"}
                        `}
                    >
                        {f.label}
                    </button>
                ))}
            </div>
        </div>

        {/* === GRID DE JOGADORES === */}
        {loading ? (
             <div className="flex justify-center h-64 items-center">
                <Loader2 className="h-12 w-12 text-[#8B0000] animate-spin" />
             </div>
        ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 justify-items-center">
                
                {/* Cards dos Jogadores */}
                {players.map((player) => (
                    <PlayerCard 
                        key={player.id}
                        player={player}
                        isAdmin={isAdmin}
                        onEditRating={openRatingModal}
                        onDelete={handleDelete}
                        onViewMore={(id) => navigate(`/jogadores/${id}`)}
                    />
                ))}

                {players.length === 0 && !loading && (
                    <div className="col-span-full text-center text-gray-400 py-10">
                        <p className="text-xl">Nenhum jogador encontrado.</p>
                    </div>
                )}
            </div>
        )}

        
                {/* Botão Adicionar (Card Fixo) */}
                {isAdmin && (
                    <div 
                        onClick={() => navigate('/instrucoes')} // Ajuste sua rota de criação
                        className="w-[280px] h-[380px] border-4 border-dashed border-[#8B0000]/30 bg-[#8B0000]/5 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-[#8B0000]/10 hover:border-[#8B0000] transition-all group"
                    >
                        <div className="w-20 h-20 bg-[#8B0000]/10 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                            <Plus className="h-10 w-10 text-[#8B0000]" />
                        </div>
                        <span className="mt-4 text-[#8B0000] font-bold text-lg">Novo Jogador</span>
                    </div>
                )}

        {/* === PAGINAÇÃO === */}
        {meta && meta.last_page > 1 && (
            <div className="flex justify-center items-center gap-4 mt-8">
                <button
                    onClick={() => setPage(p => Math.max(1, p - 1))}
                    disabled={page === 1}
                    className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                    <ChevronLeft className="h-5 w-5" />
                </button>
                <span className="text-sm font-medium text-gray-600">
                    Página {meta.current_page} de {meta.last_page}
                </span>
                <button
                    onClick={() => setPage(p => Math.min(meta.last_page, p + 1))}
                    disabled={page === meta.last_page}
                    className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                    <ChevronRight className="h-5 w-5" />
                </button>
            </div>
        )}

        {/* === MODAL EDITAR RATING === */}
        {showRatingModal && selectedPlayer && (
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in">
                <div className="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 relative">
                    <button 
                        onClick={() => setShowRatingModal(false)}
                        className="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                    >
                        <X size={24} />
                    </button>
                    
                    <h2 className="text-2xl font-bold text-[#8B0000] mb-2 font-['Jersey25']">Editar Rating</h2>
                    <p className="text-gray-600 mb-6">{selectedPlayer.pessoa.nome_completo}</p>
                    
                    <form onSubmit={handleUpdateRating}>
                        <div className="mb-6 text-center">
                            <label className="block text-sm font-bold text-gray-700 mb-2">Novo Rating (0 - 10)</label>
                            <input 
                                type="number" 
                                min="0" max="10" step="0.1"
                                value={newRating}
                                onChange={(e) => setNewRating(e.target.value)}
                                className="w-full text-center text-4xl font-bold text-[#8B0000] border-2 border-gray-200 rounded-lg p-4 focus:border-[#8B0000] outline-none"
                            />
                        </div>
                        
                        <div className="flex gap-3">
                            <button 
                                type="button" 
                                onClick={() => setShowRatingModal(false)}
                                className="flex-1 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                className="flex-1 py-3 bg-[#8B0000] text-white rounded-lg font-bold hover:bg-[#a01519] transition shadow-lg"
                            >
                                Salvar
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