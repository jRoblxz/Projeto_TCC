import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import PlayerRadarChart from "@/components/ui/PlayerRadarChart";
import {
  ArrowLeft,
  Loader2,
  BarChart3,
  User,
  Users,
  Activity,
  Target,
  Search,
  ChevronLeft,
  ChevronRight,
  Star,
} from "lucide-react";


import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip as RechartsTooltip,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
} from "recharts";
import toast from "react-hot-toast";
import { getAttributesByPosition } from "@/utils/playerAttributes";


const COLORS = [
  "#14244D",
  "#8B0000",
  "#3b82f6",
  "#10b981",
  "#f59e0b",
  "#8b5cf6",
];

const AnaliseDados: React.FC = () => {
  const navigate = useNavigate();

  // Estados de Carregamento
  const [loadingStats, setLoadingStats] = useState(true);
  const [loadingPlayers, setLoadingPlayers] = useState(false);

  // Dados Consolidados (Para a aba Geral)
  const [stats, setStats] = useState<any>(null);

  // Dados de Jogadores (Para a aba Individual)
  const [players, setPlayers] = useState<any[]>([]);
  const [meta, setMeta] = useState<any>(null); // Controle de paginação
  const [page, setPage] = useState(1);

  // Controle de Abas e Seleção
  const [activeTab, setActiveTab] = useState<"geral" | "individual">("geral");
  const [selectedPlayerId, setSelectedPlayerId] = useState<number | "">("");
  const [searchTerm, setSearchTerm] = useState("");

  // 1. CARREGAR ESTATÍSTICAS GERAIS (Apenas 1 vez)
  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await api.get(`/players-stats`);
        setStats(response.data);
      } catch (error) {
        console.error(error);
        toast.error("Erro ao carregar estatísticas.");
      } finally {
        setLoadingStats(false);
      }
    };
    fetchStats();
  }, []);

  // 2. CARREGAR JOGADORES PAGINADOS COM DEBOUNCE (Busca no Backend)
  useEffect(() => {
    const fetchPlayers = async () => {
      setLoadingPlayers(true);
      try {
        // Pede apenas 12 jogadores por página. O backend faz o filtro de busca (`search`)
        const response = await api.get(
          `/players?search=${searchTerm}&page=${page}&per_page=12`,
        );
        const data = response.data.data || response.data;

        setPlayers(data);
        setMeta(response.data.meta || response.data);

        // Se achou jogadores e não tem nenhum selecionado, seleciona o primeiro
        if (data.length > 0 && !selectedPlayerId) {
          setSelectedPlayerId(data[0].id);
        }
      } catch (error) {
        console.error(error);
        toast.error("Erro ao buscar jogadores.");
      } finally {
        setLoadingPlayers(false);
      }
    };

    // Debounce: Aguarda 500ms após o usuário parar de digitar para chamar a API
    const delayTimer = setTimeout(() => {
      fetchPlayers();
    }, 500);

    return () => clearTimeout(delayTimer);
  }, [searchTerm, page]); // Executa sempre que a busca ou a página mudar

  // Reseta a página para 1 quando o usuário digita algo novo
  useEffect(() => {
    setPage(1);
  }, [searchTerm]);

  const selectedPlayer = players.find((p) => p.id === Number(selectedPlayerId));

  if (loadingStats) {
    return (
      <Layout>
        <div className="flex justify-center items-center h-screen">
          <Loader2 className="animate-spin h-12 w-12 text-[#8B0000]" />
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-4 sm:p-6 lg:p-8">
        {/* HEADER DA PÁGINA */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-6">
          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate(-1)}
              className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
            >
              <ArrowLeft className="text-gray-700 dark:text-gray-300" />
            </button>
            <div>
              <h1 className="text-2xl font-bold text-[#14244D] dark:text-white">
                Central de Análise de Dados
              </h1>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Estatísticas gerais das peneiras em andamento e acompanhamento individual.
              </p>
            </div>
          </div>
        </div>

        {/* NAVEGAÇÃO DE ABAS */}
        <div className="flex gap-4 mb-6 border-b border-gray-200 dark:border-gray-700 pb-2 overflow-x-auto">
          <button
            onClick={() => setActiveTab("geral")}
            className={`flex items-center gap-2 px-4 py-2 font-bold transition-all whitespace-nowrap ${
              activeTab === "geral"
                ? "text-[#8B0000] border-b-4 border-[#8B0000]"
                : "text-gray-500 hover:text-[#14244D]"
            }`}
          >
            <BarChart3 size={20} /> Visão Geral (Plantel)
          </button>
          <button
            onClick={() => setActiveTab("individual")}
            className={`flex items-center gap-2 px-4 py-2 font-bold transition-all whitespace-nowrap ${
              activeTab === "individual"
                ? "text-[#8B0000] border-b-4 border-[#8B0000]"
                : "text-gray-500 hover:text-[#14244D]"
            }`}
          >
            <User size={20} /> Análise Individual
          </button>
        </div>

        {/* =========================================
            ABA 1: VISÃO GERAL (Consumindo do novo endpoint)
        ========================================= */}
        {activeTab === "geral" && stats && (
          <div className="animate-in fade-in duration-500">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
              <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center gap-4">
                <div className="w-14 h-14 bg-[#14244D]/10 rounded-full flex items-center justify-center text-[#14244D]">
                  <Users size={28} />
                </div>
                <div>
                  <p className="text-sm font-bold text-gray-500 uppercase tracking-wider">
                    Atletas Cadastrados
                  </p>
                  <p className="text-3xl font-extrabold text-gray-800 dark:text-white">
                    {stats.total}
                  </p>
                </div>
              </div>
              <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center gap-4">
                <div className="w-14 h-14 bg-[#8B0000]/10 rounded-full flex items-center justify-center text-[#8B0000]">
                  <Activity size={28} />
                </div>
                <div>
                  <p className="text-sm font-bold text-gray-500 uppercase tracking-wider">
                    Média Overall
                  </p>
                  <p className="text-3xl font-extrabold text-gray-800 dark:text-white">
                    {stats.media_geral}
                  </p>
                </div>
              </div>
              <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center gap-4">
                <div className="w-14 h-14 bg-green-500/10 rounded-full flex items-center justify-center text-green-600">
                  <Target size={28} />
                </div>
                <div>
                  <p className="text-sm font-bold text-gray-500 uppercase tracking-wider">
                    Atletas Avaliados
                  </p>
                  <p className="text-3xl font-extrabold text-gray-800 dark:text-white">
                    {stats.total_avaliados}
                  </p>
                </div>
              </div>
              <div className="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border-2 border-yellow-400 dark:border-yellow-600 flex flex-col justify-between relative overflow-hidden group">
                <div className="absolute top-0 right-0 w-24 h-24 bg-yellow-400/10 rounded-bl-full -z-10 transition-transform group-hover:scale-125"></div>
                <div className="flex items-center gap-3 mb-4 z-10">
                  <div className="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white shadow-md">
                    <Star size={24} fill="currentColor" />
                  </div>
                  <div>
                    <p className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Atletas em Destaque</p>
                    <p className="text-2xl font-extrabold text-gray-800 dark:text-white">{stats.total_destaques}</p>
                  </div>
                </div>
                <button 
                  onClick={() => navigate('/destaques')}
                  className="w-full py-2 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 font-bold rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/50 transition-colors flex items-center justify-center gap-1 text-sm z-10"
                >
                  Ver Relatório <ChevronRight size={16} />
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Gráfico de Barras: Inscritos por Categoria */}
              <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <h3 className="text-lg font-bold text-[#14244D] dark:text-white mb-6">
                  Inscritos por Categoria (Em Andamento)
                </h3>
                <div className="h-[300px] w-full">
                  <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={stats.inscritos_subdivisao} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                      <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e5e7eb" />
                      <XAxis dataKey="name" tick={{ fontSize: 12 }} axisLine={false} tickLine={false} />
                      <YAxis tick={{ fontSize: 12 }} axisLine={false} tickLine={false} />
                      <RechartsTooltip 
                        cursor={{ fill: 'transparent' }} 
                        contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} 
                      />
                      {/* Cor vermelha para dar contraste com o azul do outro gráfico */}
                      <Bar dataKey="quantidade" fill="#8B0000" radius={[4, 4, 0, 0]} />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
                {stats.inscritos_subdivisao?.length === 0 && (
                  <p className="text-center text-sm text-gray-500 mt-2">Nenhuma peneira em andamento no momento.</p>
                )}
              </div>

              <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col items-center">
                <h3 className="text-lg font-bold text-[#14244D] dark:text-white mb-2 self-start">
                  Proporção do Plantel
                </h3>
                <div className="h-[300px] w-full">
                  <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                      <Pie
                        data={stats.posicoes}
                        cx="50%"
                        cy="50%"
                        innerRadius={60}
                        outerRadius={100}
                        paddingAngle={5}
                        dataKey="quantidade"
                      >
                        {stats.posicoes.map((entry: any, index: number) => (
                          <Cell
                            key={`cell-${index}`}
                            fill={COLORS[index % COLORS.length]}
                          />
                        ))}
                      </Pie>
                      <RechartsTooltip
                        contentStyle={{ borderRadius: "8px", border: "none" }}
                      />
                    </PieChart>
                  </ResponsiveContainer>
                </div>
                <div className="flex flex-wrap justify-center gap-4 mt-2">
                  {stats.posicoes.map((entry: any, index: number) => (
                    <div
                      key={index}
                      className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 font-medium"
                    >
                      <div
                        className="w-3 h-3 rounded-full"
                        style={{
                          backgroundColor: COLORS[index % COLORS.length],
                        }}
                      ></div>
                      {entry.name} ({entry.quantidade})
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* =========================================
            ABA 2: ANÁLISE INDIVIDUAL (Paginada e Debounced)
        ========================================= */}
        {activeTab === "individual" && (
          <div className="animate-in fade-in duration-500">
            <div className="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">
              <div className="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm flex flex-col h-[650px]">
                <div className="p-4 border-b border-gray-100 dark:border-gray-800">
                  <h3 className="font-bold text-[#14244D] dark:text-white mb-3">
                    Selecionar Atleta
                  </h3>
                  <div className="relative">
                    <Search
                      className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                      size={18}
                    />
                    <input
                      type="text"
                      placeholder="Buscar no servidor..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl outline-none focus:ring-2 focus:ring-[#8B0000] text-sm dark:text-white transition-all"
                    />
                  </div>
                </div>

                <div className="flex-1 overflow-y-auto p-2 space-y-1 scrollbar-thin relative">
                  {loadingPlayers && (
                    <div className="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-10 flex justify-center items-center backdrop-blur-[1px]">
                      <Loader2 className="animate-spin text-[#8B0000]" />
                    </div>
                  )}
                  {players.length > 0
                    ? players.map((p) => (
                        <button
                          key={p.id}
                          onClick={() => setSelectedPlayerId(p.id)}
                          className={`w-full flex items-center gap-3 p-3 rounded-xl transition-all text-left group
                          ${selectedPlayerId === p.id ? "bg-[#8B0000] text-white shadow-md" : "hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200"}`}
                        >
                          <img
                            src={
                              p.pessoa?.foto_url_completa ||
                              "/img/avatar_padrao.png"
                            }
                            className={`w-10 h-10 rounded-full object-cover border-2 ${selectedPlayerId === p.id ? "border-white/30" : "border-transparent group-hover:border-gray-200"}`}
                            onError={(e) => {
                            e.currentTarget.src =
                              "https://cdn-icons-png.flaticon.com/512/149/149071.png";
                          }}
                          />
                          <div className="flex-1 overflow-hidden">
                            <p className="font-bold text-sm truncate">
                              {p.pessoa?.nome_completo}
                            </p>
                            <p
                              className={`text-xs ${selectedPlayerId === p.id ? "text-white/80" : "text-gray-400 dark:text-gray-500"}`}
                            >
                              {p.posicao_principal}
                            </p>
                          </div>
                        </button>
                      ))
                    : !loadingPlayers && (
                        <div className="p-6 text-center text-gray-400 text-sm">
                          Nenhum atleta encontrado.
                        </div>
                      )}
                </div>

                {/* Controles de Paginação */}
                {meta && meta.last_page > 1 && (
                  <div className="p-3 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                    <button
                      onClick={() => setPage((p) => Math.max(1, p - 1))}
                      disabled={page === 1}
                      className="p-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 disabled:opacity-50 hover:bg-gray-100 transition"
                    >
                      <ChevronLeft size={18} />
                    </button>
                    <span className="text-xs font-bold text-gray-500">
                      Pág {meta.current_page} de {meta.last_page}
                    </span>
                    <button
                      onClick={() =>
                        setPage((p) => Math.min(meta.last_page, p + 1))
                      }
                      disabled={page === meta.last_page}
                      className="p-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 disabled:opacity-50 hover:bg-gray-100 transition"
                    >
                      <ChevronRight size={18} />
                    </button>
                  </div>
                )}
              </div>

              {/* LADO DIREITO: Fica exatamente igual ao anterior (Detalhes e Gráfico) */}
              {selectedPlayer ? (
                <div className="grid grid-cols-1 md:grid-cols-[1fr_1.5fr] gap-6">
                  <div className="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col items-center">
                    <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-[#14244D] mb-4 shadow-md">
                      <img
                        src={
                          selectedPlayer.pessoa?.foto_url_completa ||
                          "/img/avatar_padrao.png"
                        }
                        alt="Avatar"
                        className="w-full h-full object-cover object-top"
                        onError={(e) =>
                          (e.currentTarget.src = "/img/avatar_padrao.png")
                        }
                      />
                    </div>
                    <h2 className="text-xl font-bold text-[#14244D] dark:text-white text-center mb-1">
                      {selectedPlayer.pessoa?.nome_completo}
                    </h2>
                    <span className="bg-[#8B0000] text-white px-3 py-1 rounded-full text-xs font-bold uppercase mb-6 shadow-sm">
                      {selectedPlayer.posicao_principal}
                    </span>

                    <div className="w-full space-y-3 bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                      <div className="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                        <span className="text-gray-500 font-bold text-sm">
                          Overall
                        </span>
                        <span className="text-[#8B0000] font-extrabold text-lg">
                          {Number(selectedPlayer.rating_medio).toFixed(1)}
                        </span>
                      </div>
                      <div className="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2 pt-1">
                        <span className="text-gray-500 font-bold text-sm">
                          Altura
                        </span>
                        <span className="text-gray-800 dark:text-gray-200 font-bold">
                          {selectedPlayer.altura_cm
                            ? `${selectedPlayer.altura_cm} cm`
                            : "-"}
                        </span>
                      </div>
                      <div className="flex justify-between pt-1">
                        <span className="text-gray-500 font-bold text-sm">
                          Pé Dominante
                        </span>
                        <span className="text-gray-800 dark:text-gray-200 font-bold">
                          {selectedPlayer.pe_preferido || "-"}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div className="h-full min-h-[400px]">
                    {selectedPlayer.ultima_avaliacao ? (
                      <PlayerRadarChart
                        avaliacao={selectedPlayer.ultima_avaliacao}
                        posicao={selectedPlayer.posicao_principal}
                      />
                    ) : (
                      <div className="h-full w-full bg-white dark:bg-gray-900 rounded-2xl flex flex-col items-center justify-center border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center shadow-sm">
                        <Activity size={48} className="text-gray-300 mb-4" />
                        <p className="text-gray-500 dark:text-gray-400 font-bold text-lg">
                          Atleta sem avaliação registrada
                        </p>
                        <p className="text-gray-400 dark:text-gray-500 text-sm mt-2 max-w-xs">
                          O olheiro ainda não preencheu as notas técnicas deste
                          jogador.
                        </p>
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                <div className="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl flex flex-col items-center justify-center text-gray-400 h-[600px]">
                  <User size={64} className="mb-4 opacity-50" />
                  <p className="font-bold text-lg">Nenhum atleta selecionado</p>
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default AnaliseDados;
