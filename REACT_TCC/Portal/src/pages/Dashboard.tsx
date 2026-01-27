import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import Card from "@/components/ui/Card"; // Usando seu componente de Card existente
import { api } from "../config/api"; // Usando a config do Axios
import { useNavigate } from "react-router-dom";
import {
  Users,
  Trophy,
  Calendar,
  MapPin,
  ClipboardList,
  UserCheck,
  Loader2,
  Filter,
} from "lucide-react";
import toast from "react-hot-toast";
import Logo from "../assets/img/logo-copia.png";


// Interfaces para Tipagem (Typescript)
interface Stats {
  total_candidatos: number;
  peneiras_ativas: number;
  aprovados: number;
  em_avaliacao: number;
  avaliadores: number;
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
  rating: number; // ou rating_medio
  pessoa: {
    nome_completo: string;
    data_nascimento: string; // ou calcular idade no back
    foto_perfil_url?: string;
    sub_divisao?: string;
    idade?: number; // Se vier calculado do back
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
      setPeneiras(data.recent_events || data.peneiras); // Adapte conforme o retorno exato do seu Controller
      // Se seu endpoint dashboard retornar jogadores, use aqui.
      // Caso contrário, precisaria de um endpoint separado ou ajustar o DashboardController.
      setJogadores(data.jogadores || []);
    } catch (error) {
      console.error("Erro ao carregar dashboard", error);
      toast.error("Erro ao carregar informações.");
    } finally {
      setLoading(false);
    }
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
            <Loader2 className="h-10 w-10 text-[#14244D] animate-spin" />
          </div>
        )}

        {/* ================= HEADER ================= */}
        <div className="bg-white dark:bg-gray-900 rounded-xl p-6 text-gray-900 dark:text-white shadow-lg gap-6">
          <div className="flex flex-col md:flex-row items-center justify-between gap-6">
            <div className="flex items-center gap-4">
              {/* Logo - Certifique-se que a imagem existe em public/img */}
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
                  Gestão e Formação • Champions FC
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
                className="bg-white text-[#14244D] font-bold py-1 px-3 rounded cursor-pointer outline-none focus:ring-2 focus:ring-blue-400"
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
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4 mt-3">
            <StatCard
              icon={<Users className="h-6 w-6 text-white" />}
              label="Candidatos"
              value={stats?.total_candidatos || 0}
              color="bg-[#14244D]"
            />
            <StatCard
              icon={<Calendar className="h-6 w-6 text-white" />}
              label="Peneiras Ativas"
              value={stats?.peneiras_ativas || 0}
              color="bg-[#941B1B]"
            />
            <StatCard
              icon={<Trophy className="h-6 w-6 text-white" />}
              label="Aprovados"
              value={stats?.aprovados || 0}
              color="bg-green-600"
            />
            <StatCard
              icon={<ClipboardList className="h-6 w-6 text-white" />}
              label="Em Avaliação"
              value={stats?.em_avaliacao || 0}
              color="bg-orange-500"
            />
            <StatCard
              icon={<UserCheck className="h-6 w-6 text-white" />}
              label="Avaliadores"
              value={stats?.avaliadores || 0}
              color="bg-purple-600"
            />
          </div>
        </div>

        {/* ================= STATS CARDS ================= */}

        {/* ================= MAIN GRID ================= */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* ----- COLUNA 1: PENEIRAS ----- */}
          <Card className="p-0 overflow-hidden border border-gray-200 shadow-md">
            <div className="bg-gray-50 p-4 border-b border-gray-200 flex justify-between items-center">
              <h2 className="text-xl font-bold text-[#14244D] flex items-center gap-2">
                <span className="text-2xl">⚽</span> Peneiras em Andamento
              </h2>
              <button
                onClick={() => navigate("/peneiras/nova")} // Ajuste a rota conforme necessário
                className="text-xs font-bold uppercase tracking-wider text-[#941B1B] hover:text-red-700 border border-[#941B1B] hover:bg-red-50 px-3 py-1 rounded transition"
              >
                Nova Peneira
              </button>
            </div>

            <div className="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
              {peneiras.length > 0 ? (
                peneiras.map((peneira) => (
                  <div
                    key={peneira.id}
                    onClick={() => navigate(`/peneiras/${peneira.id}`)} // Ajuste a rota
                    className={`
                      relative group cursor-pointer bg-white rounded-lg p-4 border shadow-sm hover:shadow-md transition-all duration-200
                      border-l-4 ${getStatusColor(peneira.status).split(" ")[2]}
                    `}
                  >
                    <div className="flex justify-between items-start mb-2">
                      <h3 className="font-bold text-gray-800 text-lg group-hover:text-[#14244D] transition">
                        {peneira.nome_evento}
                      </h3>
                      <span
                        className={`text-xs font-bold px-2 py-1 rounded uppercase ${getStatusColor(peneira.status).replace("border-", "")}`}
                      >
                        {peneira.status.replace("_", " ")}
                      </span>
                    </div>

                    <div className="grid grid-cols-2 gap-y-1 text-sm text-gray-600">
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
          <Card className="p-0 overflow-hidden border border-gray-200 shadow-md">
            <div className="bg-gray-50 p-4 border-b border-gray-200">
              <h2 className="text-xl font-bold text-[#14244D] flex items-center gap-2">
                <span className="text-2xl">🏆</span> Jogadores Destaques
              </h2>
            </div>

            <div className="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
              {jogadores.length > 0 ? (
                jogadores.map((jogador) => (
                  <div
                    key={jogador.id}
                    // onClick={() => navigate(`/players/${jogador.id}`)}
                    className="flex items-center gap-4 bg-white p-3 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer"
                  >
                    {/* Avatar */}
                    <div className="h-12 w-12 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm">
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
                      <h4 className="font-bold text-gray-800 leading-tight">
                        {jogador.pessoa.nome_completo}
                      </h4>
                      <div className="text-xs text-gray-500 mt-1 flex gap-2">
                        <span className="bg-gray-100 px-2 py-0.5 rounded">
                          {/* Lógica simples de idade se não vier do back */}
                          {jogador.pessoa.idade
                            ? `${jogador.pessoa.idade} anos`
                            : "N/A"}
                        </span>
                        <span className="bg-gray-100 px-2 py-0.5 rounded">
                          {jogador.pessoa.sub_divisao || "Geral"}
                        </span>
                      </div>
                    </div>

                    {/* Rating */}
                    <div className="flex flex-col items-center justify-center bg-[#14244D] text-white h-10 w-10 rounded-lg font-bold shadow-sm">
                      <span>{Number(jogador.rating || 0).toFixed(1)}</span>
                    </div>
                  </div>
                ))
              ) : (
                <EmptyState message="Nenhum jogador cadastrado" icon="🏆" />
              )}
            </div>
          </Card>
        </div>
      </div>
    </Layout>
  );
};

// Componente Pequeno para os Stats
const StatCard = ({ icon, label, value, color }: any) => (
  <div
    // Adicionei 'flex flex-col items-center justify-center' aqui
    className={`bg-white rounded-lg p-4 text-black shadow-md transform border-1 border-black hover:scale-105 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center`}
  >
    {/* Removi o 'm-auto' pois o pai já está centralizando tudo com 'items-center' */}
    <div className="mb-2">
      <span className="text-3xl font-bold text-center text-[#851114]">{value}</span>
    </div>

    <div className="text-xs md:text-sm font-medium opacity-90 text-center text-[#333]">
      {label}
    </div>
  </div>
);

// Componente para lista vazia
const EmptyState = ({ message, icon }: { message: string; icon: string }) => (
  <div className="flex flex-col items-center justify-center py-10 text-gray-400">
    <span className="text-4xl mb-2 grayscale opacity-50">{icon}</span>
    <p className="text-sm font-medium">{message}</p>
  </div>
);

export default Dashboard;
