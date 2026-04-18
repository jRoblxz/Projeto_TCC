import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import { api } from "@/config/api";
import {
  Calendar,
  MapPin,
  Trophy,
  Loader2,
  Search,
  Info,
  CheckCircle2,
  AlertCircle,
  CheckCircle,
} from "lucide-react";
import toast from "react-hot-toast";

interface PeneiraDisponivel {
  id: number;
  nome_evento: string;
  data_evento: string;
  local: string;
  sub_divisao: string;
}

const NovaInscricao: React.FC = () => {
  const [peneiras, setPeneiras] = useState<PeneiraDisponivel[]>([]);
  const [minhasInscricoes, setMinhasInscricoes] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [enrollingId, setEnrollingId] = useState<number | null>(null);
  const [searchTerm, setSearchTerm] = useState("");

  const loadData = async () => {
    setLoading(true);

    // 1. Busca Novas Oportunidades
    try {
      const response = await api.get("/my-available-peneiras");
      setPeneiras(response.data);
    } catch (error) {
      console.error("Erro ao carregar peneiras:", error);
    }

    // 2. Busca Inscrições Atuais (Agora sim vai aparecer no Network!)
    try {
      const resInsc = await api.get("/my-enrollments");
      setMinhasInscricoes(resInsc.data);
    } catch (error) {
      console.error("Erro ao buscar inscrições:", error);
    }

    setLoading(false);
  };

  useEffect(() => {
    loadData();
  }, []);

  const handleQuickEnroll = async (id: number, nome: string) => {
    setEnrollingId(id);
    try {
      await api.post("/enroll-again", { peneira_id: id });
      toast.success(`Inscrição confirmada em: ${nome}`);

      // Remove da lista de disponíveis
      setPeneiras(peneiras.filter((p) => p.id !== id));

      // Atualiza a lista verde de "Já inscrito"
      const resInsc = await api.get("/my-enrollments");
      setMinhasInscricoes(resInsc.data);
    } catch (error: any) {
      toast.error(error.response?.data?.error || "Erro ao realizar inscrição.");
    } finally {
      setEnrollingId(null);
    }
  };

  const filteredPeneiras = peneiras.filter(
    (p) =>
      p.nome_evento.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.local.toLowerCase().includes(searchTerm.toLowerCase()),
  );

  return (
    <Layout>
      <div className="max-w-6xl mx-auto p-4 sm:p-6 space-y-8">
        {/* Header com Busca */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 className="text-3xl font-bold text-brand-primary dark:text-white">
              Peneiras e Avaliações
            </h1>
            <p className="text-gray-500 mt-1 text-sm sm:text-base">
              Acompanhe suas inscrições e explore novas avaliações abertas.
            </p>
            <div className="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-bold">
              <Trophy size={14} /> Categoria:{" "}
              {peneiras[0]?.sub_divisao || "Geral"}
            </div>
          </div>

          <div className="relative w-full md:w-80">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4" />
            <input
              type="text"
              placeholder="Buscar por local ou nome..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-brand-secondary outline-none transition-all"
            />
          </div>
        </div>

        {loading ? (
          <div className="h-64 flex flex-col items-center justify-center gap-4">
            <Loader2 className="animate-spin text-brand-secondary" size={40} />
            <p className="text-gray-400 font-medium">Carregando dados...</p>
          </div>
        ) : (
          <>
            {/* SEÇÃO 1: JÁ INSCRITO */}
            {minhasInscricoes.length > 0 && (
              <div className="bg-green-50 dark:bg-green-900/10 rounded-2xl p-6 border border-green-100 dark:border-green-800">
                <h2 className="text-xl font-bold text-green-800 dark:text-green-400 flex items-center gap-2 mb-4">
                  <CheckCircle size={20} /> Minhas Inscrições Atuais
                </h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                  {minhasInscricoes.map((insc) => (
                    <div
                      key={insc.id}
                      className="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-green-200"
                    >
                      <p className="font-bold text-brand-primary dark:text-white line-clamp-1">
                        {insc.nome_evento}
                      </p>
                      <div className="flex justify-between items-center mt-2">
                        <span className="text-xs text-gray-500 flex items-center gap-1">
                          <Calendar size={12} />{" "}
                          {new Date(insc.data_evento).toLocaleDateString(
                            "pt-BR",
                          )}
                        </span>
                        <span className="text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded font-bold uppercase">
                          {insc.status_inscricao || "Pendente"}
                        </span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* SEÇÃO 2: NOVAS OPORTUNIDADES */}
            <div>
              <h2 className="text-xl font-bold text-brand-primary dark:text-white flex items-center gap-2 mb-4">
                <Trophy className="text-brand-secondary" size={20} />{" "}
                Oportunidades Disponíveis
              </h2>

              {filteredPeneiras.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {filteredPeneiras.map((peneira) => (
                    <div
                      key={peneira.id}
                      className="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col"
                    >
                      <div className="p-5 flex-1 space-y-4">
                        <div className="flex justify-between items-start">
                          <span className="bg-brand-secondary/10 text-brand-secondary px-3 py-1 rounded-full text-xs font-bold uppercase">
                            {peneira.sub_divisao}
                          </span>
                          <Info
                            className="text-gray-300 group-hover:text-brand-primary cursor-help transition-colors"
                            size={18}
                          />
                        </div>

                        <h3 className="text-xl font-bold text-brand-primary dark:text-white line-clamp-2 min-h-[3.5rem]">
                          {peneira.nome_evento}
                        </h3>

                        <div className="space-y-3 pt-2">
                          <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <Calendar className="h-4 w-4 text-brand-secondary" />
                            {new Date(peneira.data_evento).toLocaleDateString(
                              "pt-BR",
                              {
                                day: "2-digit",
                                month: "short",
                                year: "numeric",
                              },
                            )}
                          </div>
                          <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <MapPin className="h-4 w-4 text-brand-secondary" />
                            <span className="truncate">{peneira.local}</span>
                          </div>
                        </div>
                      </div>

                      <div className="p-5 border-t border-gray-50 dark:border-gray-800">
                        <button
                          onClick={() =>
                            handleQuickEnroll(peneira.id, peneira.nome_evento)
                          }
                          disabled={enrollingId === peneira.id}
                          className="w-full bg-brand-primary hover:bg-[#1a2e63] dark:bg-brand-secondary dark:hover:bg-red-800 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50"
                        >
                          {enrollingId === peneira.id ? (
                            <Loader2 className="animate-spin" size={20} />
                          ) : (
                            <>Confirmar Inscrição</>
                          )}
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="bg-gray-50 dark:bg-gray-800/50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 mt-4">
                  <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white dark:bg-gray-800 shadow-sm mb-4">
                    <CheckCircle2 className="text-green-500 h-10 w-10" />
                  </div>
                  <h2 className="text-xl font-bold text-brand-primary dark:text-white">
                    Tudo em dia!
                  </h2>
                  <p className="text-gray-500 max-w-sm mx-auto mt-2">
                    Você já está inscrito em todas as peneiras disponíveis para
                    sua categoria ou não há novos eventos no momento.
                  </p>
                </div>
              )}
            </div>
          </>
        )}

        <div className="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl text-blue-800 dark:text-blue-300 text-sm mt-8">
          <AlertCircle className="shrink-0 h-5 w-5" />
          <p>
            <b>Atenção Atleta:</b> Sua inscrição é vinculada ao seu perfil
            atual. Certifique-se de que seus dados técnicos estão atualizados.
          </p>
        </div>
      </div>
    </Layout>
  );
};

export default NovaInscricao;
