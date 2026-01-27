import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import PlayerCard from "@/components/ui/PlayerCard"; // Certifique-se de importar o Card que criamos
import {
  ArrowLeft,
  Calendar,
  MapPin,
  Users,
  Info,
  Search,
  Filter,
  Shuffle,
  Loader2,
} from "lucide-react";
import toast from "react-hot-toast";
import { isUserAdmin } from "../utils/auth";

// Interfaces para tipagem
interface Peneira {
  id: number;
  nome_evento: string;
  data_evento: string;
  local: string;
  sub_divisao: string;
  status: string;
  descricao: string;
  equipes?: any[];
}

interface Jogador {
  id: number;
  jogador_id?: number; // Dependendo de como vem do backend (relation pivot)
  posicao_principal: string;
  rating_medio?: number;
  altura_cm?: number;
  peso_kg?: number;
  pe_preferido?: string;
  pessoa: {
    nome_completo: string;
    foto_url_completa: string;
    sub_divisao: string;
  };
  ultima_avaliacao?: {
    observacoes: string;
  };
}

const PeneiraDetalhes: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();

  // Estados de Dados
  const [peneira, setPeneira] = useState<Peneira | null>(null);
  const [jogadores, setJogadores] = useState<Jogador[]>([]);
  const [loading, setLoading] = useState(true);
  const [generatingTeams, setGeneratingTeams] = useState(false);

  // Estados de Filtro
  const [searchTerm, setSearchTerm] = useState("");
  const [positionFilter, setPositionFilter] = useState("Todas Posições");
  const [statusFilter, setStatusFilter] = useState("Todos Status");
  const [hasTeams, setHasTeams] = useState(false); // NOVO ESTADO
  const isAdmin = isUserAdmin();

  // --- CARREGAR DADOS ---
  useEffect(() => {
    const loadData = async () => {
      try {
        // A API precisa retornar { peneira: {...}, jogadores: [...] }
        // Se sua API atual retorna separado, faça duas chamadas.
        const response = await api.get(`/peneiras/${id}`);

        // Ajuste conforme o retorno real do seu Laravel Resource
        const data = response.data.data || response.data;

        setPeneira(data.peneira || data);
        setJogadores(data.jogadores || []);

        // [CORREÇÃO] Verifica se tem equipes (Compatível com Array e Objeto)
        // Se vier no objeto principal:
        if (
          data.peneira &&
          data.peneira.equipes &&
          data.peneira.equipes.length > 0
        ) {
          setHasTeams(true);
        } else {
          // Fallback: Busca na API de times
          try {
            const tr = await api.get(`/peneiras/${id}/teams`);
            const teamsData = tr.data;

            // Verifica se é Array (Novo formato) E tem itens
            if (Array.isArray(teamsData) && teamsData.length > 0) {
              setHasTeams(true);
            }
            // Verifica formato antigo (Objeto com chaves)
            else if (teamsData && (teamsData.A || teamsData.B)) {
              setHasTeams(true);
            }
          } catch (e) {
            console.error("Erro ao verificar times", e);
          }
        }
      } catch (error) {
        toast.error("Erro ao carregar detalhes da peneira.");
        navigate("/peneiras");
      } finally {
        setLoading(false);
      }
    };
    loadData();
  }, [id, navigate]);

  // --- AÇÕES ---

  // Função atualizada para gerar
  const handleGenerateTeams = async (force = false) => {
    if (
      force &&
      !confirm("Isso apagará as equipes atuais e gerará novas. Continuar?")
    )
      return;

    setGeneratingTeams(true);
    const toastId = toast.loading("Gerando equipes...");

    try {
      // Chama o endpoint que criamos no TeamController
      await api.post(`/peneiras/${id}/teams/generate`);
      toast.success("Equipes geradas com sucesso!");
      setHasTeams(true);
    } catch (error: any) {
      toast.error("Erro ao gerar equipes.");
    } finally {
      setGeneratingTeams(false);
      toast.dismiss(toastId);
    }
  };

  const filteredPlayers = jogadores.filter((player) => {
    const matchesSearch = player.pessoa.nome_completo
      .toLowerCase()
      .includes(searchTerm.toLowerCase());
    const matchesPosition =
      positionFilter === "Todas Posições" ||
      player.posicao_principal === positionFilter;
    return matchesSearch && matchesPosition;
  });

  const handleDeletePlayer = async (player: any) => {
    /* Lógica de delete igual */
  };

  // Componente Auxiliar InfoBox
  const InfoBox = ({
    icon,
    title,
    value,
  }: {
    icon: React.ReactNode;
    title: string;
    value: string;
  }) => (
    <div className="flex items-center gap-4 dark:bg-gray-800  dark:border-gray-700  bg-[#f7fafc] p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
      <div className="w-12 h-12 rounded-xl bg-[#14244D] flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-950 shrink-0">
        {icon}
      </div>
      <div>
        <h4 className="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-300 font-bold mb-1">
          {title}
        </h4>
        <p className="text-[#2d3748] dark:text-gray-100 font-bold text-lg leading-tight">
          {value}
        </p>
      </div>
    </div>
  );

  // Helpers de Estilo
  const getStatusBadgeColor = (status: string) => {
    switch (status?.toUpperCase()) {
      case "EM_ANDAMENTO":
        return "bg-green-100 text-green-800 border-green-200";
      case "FINALIZADA":
        return "bg-red-100 text-red-800 border-red-200";
      case "AGENDADA":
        return "bg-blue-100 text-blue-800 border-blue-200";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  if (loading)
    return (
      <div className="flex justify-center mt-20">
        <Loader2 className="animate-spin h-12 w-12 text-[#14244D]" />
      </div>
    );
  if (!peneira) return null;

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-6 space-y-8 ">
        {/* BOTÃO VOLTAR */}
        <button
          onClick={() => navigate(-1)}
          className="flex items-center gap-2 bg-white  text-[#667eea] px-6 py-3 rounded-lg shadow-sm hover:shadow-md hover:-translate-x-1 transition-all font-bold border border-gray-100"
        >
          <ArrowLeft size={20} /> Voltar
        </button>

        {/* HEADER DA PENEIRA */}
        <div className="bg-white dark:bg-gray-900 transition rounded-2xl shadow-xl overflow-hidden relative">
          {/* Barra colorida superior */}
          <div className="h-2 bg-[#8B0000]" />

          <div className="p-8">
            {/* Título e Status */}
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
              <h1 className="text-3xl md:text-4xl font-bold dark:text-gray-100 text-[#2d3748]">
                {peneira.nome_evento}
              </h1>
              <span
                className={`px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wider border ${getStatusBadgeColor(peneira.status)}`}
              >
                {peneira.status?.replace("_", " ")}
              </span>
            </div>

            {/* Grid de Informações */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <InfoBox
                icon={<Calendar className="text-white" />}
                title="Data e Horário"
                value={peneira.data_evento}
              />
              <InfoBox
                icon={<MapPin className="text-white" />}
                title="Local"
                value={peneira.local}
              />
              <InfoBox
                icon={<Users className="text-white" />}
                title="Faixa Etária"
                value={peneira.sub_divisao}
              />
              <InfoBox
                icon={<Info className="text-white" />}
                title="Vagas"
                value="30 Vagas"
              />
            </div>

            {/* Descrição */}
            <div className="bg-gray-50 dark:bg-gray-800 transition dark:border-gray-700 p-6 rounded-xl border border-gray-100 mb-8">
              <h3 className="text-[#2d3748] dark:text-gray-100 transition font-bold text-lg mb-2">
                Sobre a Peneira
              </h3>
              <p className="text-gray-600 dark:text-gray-300 transition leading-relaxed">
                {peneira.descricao || "Sem descrição informada."}
              </p>
            </div>

            {/* Ações Principais (Gerador) */}
            <div className="border-t border-gray-100 dark:border-gray-700 pt-8">
              <h3 className="text-[#2d3748] dark:text-gray-100 font-bold text-xl mb-4 flex items-center gap-2">
                <Shuffle size={24} className="text-[#8B0000]" />
                Gerador de Equipes
              </h3>
              {isAdmin ? (
                <div className="flex flex-col sm:flex-row items-center gap-4 dark:bg-gray-800 dark:border-gray-700  bg-[#f8fafc] p-4 rounded-xl border border-dashed border-gray-300">
                  {hasTeams ? (
                    <>
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                          <Shuffle size={20} />
                        </div>
                        <div>
                          <p className="font-bold text-gray-700">
                            Equipes formadas!
                          </p>
                          <p className="text-sm text-gray-500">
                            Visualize ou edite a escalação atual.
                          </p>
                        </div>
                      </div>
                      <div className="flex gap-3">
                        <button
                          onClick={() => handleGenerateTeams(true)}
                          className="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200 text-sm font-bold transition"
                        >
                          Gerar Novamente
                        </button>
                        <button
                          onClick={() =>
                            navigate(`/peneiras/${id}/editor-times`)
                          }
                          className="bg-[#14244D] hover:bg-[#1e3a8a] text-white px-6 py-3 rounded-lg font-bold shadow-md transition flex items-center gap-2"
                        >
                          <Users size={18} /> Ver / Editar Times
                        </button>
                      </div>
                    </>
                  ) : (
                    <>
                      <div>
                        <p className="font-bold text-gray-700">
                          Nenhuma equipe formada
                        </p>
                        <p className="text-sm text-gray-500">
                          Distribua os {jogadores.length} jogadores
                          automaticamente em 2 times.
                        </p>
                      </div>
                      <button
                        onClick={() => handleGenerateTeams(false)}
                        disabled={generatingTeams || jogadores.length === 0}
                        className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-md transition flex items-center gap-2 disabled:opacity-50"
                      >
                        {generatingTeams ? (
                          <Loader2 className="animate-spin" />
                        ) : (
                          <Shuffle size={18} />
                        )}
                        Gerar Equipes Agora
                      </button>
                    </>
                  )}
                </div>
              ) : (
                // SE FOR JOGADOR: MOSTRA SÓ UM AVISO OU O BOTÃO DE VER TIMES (SE EXISTIREM)
                <div className="text-center p-4">
                  {hasTeams ? (
                    <button
                      onClick={() => navigate(`/peneiras/${id}/editor-times`)}
                      className="bg-[#14244D] text-white px-6 py-3 rounded-lg font-bold"
                    >
                      Ver Escalação dos Times
                    </button>
                  ) : (
                    <p className="text-gray-500">
                      As equipes ainda não foram definidas pelo treinador.
                    </p>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* LISTA DE JOGADORES */}
        <div className="bg-whit dark:bg-gray-800 dark:border-gray-700  p-8 rounded-2xl shadow-xl border border-gray-100">
          <div className="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-100 dark:border-gray-700 pb-6 gap-4">
            <h2 className="text-2xl font-bold text-[#2d3748] dark:text-gray-200 flex items-center gap-2">
              <Users className="text-[#8B0000]" />
              Jogadores Inscritos
              <span className="text-sm font-normal text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                {jogadores.length}
              </span>
            </h2>
          </div>

          {/* Barra de Filtros */}
          <div className="flex flex-col lg:flex-row gap-4 mb-8">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Buscar jogador por nome..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-10 pr-4 py-3 border dark:bg-gray-800 dark:border-gray-700  border-gray-200 rounded-xl focus:ring-2 focus:ring-[#667eea] outline-none transition"
              />
            </div>

            <div className="relative w-full lg:w-64">
              <Filter className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
              <select
                value={positionFilter}
                onChange={(e) => setPositionFilter(e.target.value)}
                className="w-full pl-10 pr-10 py-3 border dark:bg-gray-800 dark:border-gray-700  border-gray-200 rounded-xl focus:ring-2 focus:ring-[#667eea] outline-none transition appearance-none bg-white cursor-pointer"
              >
                <option>Todas Posições</option>
                <option>Goleiro</option>
                <option>Defesa</option>
                <option>Meio-Campo</option>
                <option>Atacante</option>
              </select>
            </div>
          </div>

          {/* Grid de Cards */}
          {filteredPlayers.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 justify-items-center">
              {filteredPlayers.map((jogador) => (
                <PlayerCard
                  key={jogador.id}
                  player={jogador} // O componente Card espera o objeto jogador completo
                  isAdmin={isAdmin} // Permite ver botões de excluir
                  onEditRating={() => {}} // Lógica de editar rating (pode implementar depois)
                  onDelete={handleDeletePlayer}
                  onViewMore={(id) => navigate(`/jogadores/${id}`)}
                />
              ))}
            </div>
          ) : (
            <div className="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
              <Users className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-500 text-lg">
                Nenhum jogador encontrado com os filtros atuais.
              </p>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default PeneiraDetalhes;
