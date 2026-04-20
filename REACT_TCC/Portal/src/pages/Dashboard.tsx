import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import Card from "@/components/ui/Card";
import { api } from "@/config/api";
import { useNavigate } from "react-router-dom";
import {
  Users,
  Trophy,
  Calendar,
  MapPin,
  Loader2,
  Filter,
  ChevronRight,
} from "lucide-react";
import toast from "react-hot-toast";
import Logo from "../assets/img/fatec.png";
import StatCard from "@/components/ui/StatCard";
import FunilConversao from "@/components/ui/FunilConversao";

// Interfaces para Tipagem
interface Stats {
  total_candidatos: number;
  peneiras_ativas: number;
  peneiras_agendadas: number;
  total_peneiras: number;
  funil_conversao?: {
    etapa: string;
    valor: number;
    cor: string;
  }[];
}
interface Peneira {
  id: number;
  nome_evento: string;
  data_evento: string;
  local: string;
  status: "AGENDADA" | "EM_ANDAMENTO" | "FINALIZADA" | "CANCELADA";
  sub_divisao: string;
  inscricoes_count?: number;
}
interface Jogador {
  id: number;
  rating_medio: number;
  pessoa: {
    nome_completo: string;
    data_nascimento: string;
    foto_perfil_url?: string;
    sub_divisao?: string;
    idade?: number;
    foto_url_completa?: string;
  };
}

const Dashboard: React.FC = () => {
  const navigate = useNavigate();

  // Estados
  const [loading, setLoading] = useState<boolean>(true);
  const [stats, setStats] = useState<Stats | null>(null);
  const [peneiras, setPeneiras] = useState<Peneira[]>([]);
  const [jogadores, setJogadores] = useState<Jogador[]>([]);
  const [filterSub, setFilterSub] = useState<string>("");

  // Carregar Dados
  const loadDashboardData = async () => {
    setLoading(true);
    try {
      // Passamos o filtro como query param
      const params = filterSub ? { subdivisao: filterSub } : {};

      const response = await api.get("/dashboard", { params });
      const data = response.data;

      setStats(data.stats);
      setPeneiras(data.recent_events || data.peneiras);
      setJogadores(data.jogadores || []);
    } catch (error) {
      console.error("Erro ao carregar dashboard", error);
      toast.error("Erro ao carregar informações.");
    } finally {
      setLoading(false);
    }
  };

  const calculateAge = (birthDateString: string): string => {
    if (!birthDateString) return "N/A";
    const birthDate = new Date(birthDateString);
    if (isNaN(birthDate.getTime())) return "N/A";
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age.toString();
  };

  // Efeito para carregar ao iniciar ou mudar o filtro
  useEffect(() => {
    loadDashboardData();
  }, [filterSub]);

  // Função auxiliar para cor do status
  const getStatusColor = (status: string) => {
    switch (status) {
      case "EM_ANDAMENTO":
        return "bg-green-100 text-green-800 border-green-500";
      case "AGENDADA":
        return "bg-blue-100 text-blue-800 border-blue-500";
      case "FINALIZADA":
        return "bg-gray-100 text-gray-800 border-gray-500";
      case "CANCELADA":
        return "bg-red-100 text-red-800 border-red-500";
      default:
        return "bg-gray-100 text-gray-600 border-gray-400";
    }
  };

  return (
    <Layout>
      <div className="space-y-6 h-fit relative p-6">
        {/* Loading Overlay */}
        {loading && (
          <div className="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-50 rounded-lg">
            <Loader2 className="h-10 w-10 text-brand-primary animate-spin" />
          </div>
        )}

        {/* ================= HEADER ================= */}
        <div className="rounded-xl p-6 text-gray-900 bg-white dark:bg-gray-900  dark:text-white shadow-lg gap-6 transition">
          <div className="flex flex-col md:flex-row items-center justify-between gap-6">
            <div className="flex items-center gap-4">
              <img
                src={Logo}
                alt="Logo"
                className="h-20 w-auto object-contain p-1"
              />
              <div>
                <h1 className="text-2xl md:text-3xl font-bold tracking-wider uppercase font-Jersey">
                  Sistema de Peneiras
                </h1>
                <p className="text-[#333] dark:text-gray-400 text-sm">
                  Gestão e Formação de Atletas • Fatec Prudente
                </p>
              </div>
            </div>

            {/* Filtro */}
            <div className="flex items-center bg-white/10 p-2 rounded-lg backdrop-blur-md border border-white/20">
              <Filter className="h-5 w-5 mr-2 text-blue-200" />
              <label
                htmlFor="subdivisao"
                className="font-semibold mr-2 text-sm"
              >
                Filtrar:
              </label>
              <select
                id="subdivisao"
                value={filterSub}
                onChange={(e) => setFilterSub(e.target.value)}
                className="bg-white text-brand-primary font-bold py-1 px-3 rounded cursor-pointer outline-none focus:ring-2 focus:ring-blue-400"
              >
                <option value="">Todas</option>
                {[
                  "Sub-7",
                  "Sub-9",
                  "Sub-11",
                  "Sub-13",
                  "Sub-15",
                  "Sub-17",
                  "Sub-20",
                ].map((sub) => (
                  <option key={sub} value={sub}>
                    {sub}
                  </option>
                ))}
              </select>
              {filterSub && (
                <button
                  onClick={() => setFilterSub("")}
                  className="ml-2 text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded transition"
                >
                  Limpar
                </button>
              )}
            </div>
          </div>

          {/* ================= STATS CARDS ================= */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-20 mt-3">
            <StatCard label="Candidatos" value={stats?.total_candidatos || 0} />
            <StatCard
              label="Total de Peneiras"
              value={stats?.total_peneiras || 0}
            />{" "}
            {/* <- Arrumei o label aqui */}
            <StatCard
              label="Peneiras Ativas"
              value={stats?.peneiras_ativas || 0}
            />
            <StatCard
              label="Peneiras Agendadas"
              value={stats?.peneiras_agendadas || 0}
            />
          </div>
        </div>

        {/* ================= MAIN GRID ================= */}

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* ----- COLUNA 1: PENEIRAS ----- */}
          <Card className="p-0 overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md transition">
            <div className="bg-white dark:bg-gray-900  dark:text-white text-brand-primary  p-4 border-b dark:border-gray-700 border-gray-200 flex justify-between items-center transition">
              <h2 className="text-2xl font-bold  flex items-center gap-2">
                Peneiras em Andamento
              </h2>
              <button
                onClick={() => navigate("/peneiras")}
                className="text-xs font-bold uppercase tracking-wider bg-[#941B1B] text-white hover:text-red-700 border border-[#941B1B] hover:bg-red-50 px-3 py-1 rounded transition"
              >
                Nova Peneira
              </button>
            </div>

            <div className="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar transition dark:bg-gray-900 ">
              {peneiras.length > 0 ? (
                peneiras.map((peneira) => (
                  <div
                    key={peneira.id}
                    onClick={() => navigate(`/peneiras/${peneira.id}`)}
                    className={`
                      relative group cursor-pointer  bg-white dark:bg-gray-800  rounded-lg p-4 border shadow-sm hover:shadow-md transition-all duration-200
                      border-l-4 ${getStatusColor(peneira.status).split(" ")[2]}
                    `}
                  >
                    <div className="flex justify-between items-start mb-2">
                      <h3 className="font-bold text-gray-800 dark:text-gray-200 text-lg transition">
                        {peneira.nome_evento}
                      </h3>
                      <span
                        className={`text-xs font-bold px-2 py-1 rounded uppercase ${getStatusColor(peneira.status).replace("border-", "")}`}
                      >
                        {peneira.status.replace("_", " ")}
                      </span>
                    </div>

                    <div className="grid grid-cols-2 gap-y-1 text-sm text-gray-600 dark:text-gray-400 transition">
                      <div className="flex items-center gap-1">
                        <Calendar className="h-3 w-3" />
                        {new Date(peneira.data_evento).toLocaleDateString(
                          "pt-BR",
                        )}
                      </div>
                      <div className="flex items-center gap-1">
                        <Users className="h-3 w-3" />
                        {peneira.inscricoes_count || 0} inscritos
                      </div>
                      <div className="flex items-center gap-1">
                        <Trophy className="h-3 w-3" />
                        {peneira.sub_divisao || "Todas"}
                      </div>
                      <div className="flex items-center gap-1">
                        <MapPin className="h-3 w-3" />
                        {peneira.local}
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <EmptyState message="Nenhuma peneira encontrada" icon="⚽" />
              )}
            </div>
          </Card>

          {/* ----- COLUNA 2: JOGADORES DESTAQUE ----- */}
          <Card className="p-0 overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md transition">
            <div className="bg-white dark:bg-gray-900  dark:text-white text-brand-primary p-4 border-b dark:border-gray-700 border-gray-200 transition flex justify-between items-center">
              <h2 className="text-2xl font-bold  flex items-center gap-2">
                Jogadores Destaques
              </h2>
              <button
                onClick={() => navigate("/destaques")}
                className="p-2 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 font-bold rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/50 transition-colors flex items-center gap-1 text-sm z-10"
              >
                Ver Relatório <ChevronRight size={16} />
              </button>
            </div>

            <div className="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar dark:bg-gray-900 transition">
              {jogadores.length > 0 ? (
                jogadores.map((jogador) => (
                  <div
                    key={jogador.id}
                    className="flex items-center gap-4 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition cursor-pointer"
                  >
                    {/* Avatar */}
                    <div className="transition h-12 w-12 rounded-full bg-gray-200  overflow-hidden flex-shrink-0 border-2 border-white shadow-sm">
                      {jogador.pessoa.foto_perfil_url ? (
                        <img
                          src={
                            jogador.pessoa.foto_url_completa ||
                            "/img/avatar_padrao.png"
                          }
                          alt={jogador.pessoa.nome_completo}
                          className="h-full w-full object-cover"
                          onError={(e) => {
                            e.currentTarget.src =
                              "https://cdn-icons-png.flaticon.com/512/149/149071.png";
                          }}
                        />
                      ) : (
                        <div className="h-full w-full flex items-center justify-center text-xl">
                          👤
                        </div>
                      )}
                    </div>

                    {/* Info */}
                    <div className="flex-1">
                      <h4 className="font-bold text-gray-800 dark:text-gray-200 leading-tight">
                        {jogador.pessoa.nome_completo}
                      </h4>
                      <div className="text-xs text-gray-500 dark:text-gray-400 mt-1 flex gap-2">
                        <span className="bg-gray-100 px-2 py-0.5 rounded">
                          {jogador.pessoa.data_nascimento
                            ? calculateAge(jogador.pessoa.data_nascimento)
                            : "N/A"}
                        </span>
                        <span className="bg-gray-100 px-2 py-0.5 rounded">
                          {jogador.pessoa.sub_divisao || "Geral"}
                        </span>
                      </div>
                    </div>

                    {/* Rating */}
                    <div className="flex flex-col items-center justify-center bg-brand-primary dark:bg-[#941B1B] text-white h-10 w-10 rounded-lg font-bold shadow-sm">
                      <span>
                        {Number(jogador.rating_medio || 0).toFixed(1)}
                      </span>
                    </div>
                  </div>
                ))
              ) : (
                <EmptyState message="Nenhum jogador cadastrado" icon="🏆" />
              )}
            </div>
          </Card>
        </div>
        {/* ================= Funil ================= 
        {stats?.funil_conversao && (
          <FunilConversao dadosFunil={stats.funil_conversao} />
        )}        
        */}        
      </div>
    </Layout>
  );
};

// Componente para lista vazia
const EmptyState = ({ message, icon }: { message: string; icon: string }) => (
  <div className="flex flex-col items-center justify-center py-10 text-gray-400">
    <span className="text-4xl mb-2 grayscale opacity-50">{icon}</span>
    <p className="text-sm font-medium">{message}</p>
  </div>
);

export default Dashboard;
