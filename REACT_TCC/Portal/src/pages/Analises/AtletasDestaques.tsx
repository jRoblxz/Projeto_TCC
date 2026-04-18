import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../../config/api";
import Layout from "@/components/layouts/Layout";
import {
  ArrowLeft,
  Loader2,
  Trophy,
  Medal,
  Star,
  Target,
  Activity,
} from "lucide-react";
import { getAttributesByPosition } from "../../utils/playerAttributes";

const AtletasDestaque: React.FC = () => {
  const navigate = useNavigate();
  const [players, setPlayers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchElitePlayers = async () => {
      try {
        // Usa o filtro que já existia na sua API para pegar notas 8+
        const response = await api.get(
          `/players?sub_divisao=high-rating&per_page=50`,
        );
        const data = response.data.data || response.data;
        // Garante a ordenação pela nota (do maior pro menor)
        const sorted = data.sort(
          (a: any, b: any) => b.rating_medio - a.rating_medio,
        );
        setPlayers(sorted);
      } catch (error) {
        console.error("Erro ao carregar destaques", error);
      } finally {
        setLoading(false);
      }
    };
    fetchElitePlayers();
  }, []);

  // Componente de Barra de Progresso Analítica
  const AttributeBar = ({ label, value }: { label: string; value: number }) => (
    <div className="mb-2">
      <div className="flex justify-between text-xs font-bold mb-1">
        <span className="text-gray-600 dark:text-gray-300 uppercase">
          {label}
        </span>
        <span className="text-brand-primary dark:text-white">
          {Number(value || 0).toFixed(1)}
        </span>
      </div>
      <div className="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
        <div
          className="bg-gradient-to-r from-brand-primary to-brand-darkred h-full transition-all duration-1000"
          style={{ width: `${((value || 0) / 10) * 100}%` }}
        ></div>
      </div>
    </div>
  );

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-4 sm:p-6 lg:p-8">
        {/* HEADER ELITE */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-gradient-to-r from-brand-primary to-[#0a1226] p-8 rounded-2xl shadow-xl mb-8 relative overflow-hidden">
          <div className="absolute -right-10 -top-10 opacity-10">
            <Trophy size={200} />
          </div>
          <div className="flex items-center gap-6 z-10">
            <button
              onClick={() => navigate(-1)}
              className="p-3 bg-white/10 hover:bg-white/20 rounded-full text-white transition backdrop-blur-sm"
            >
              <ArrowLeft />
            </button>
            <div className="text-white">
              <h1 className="text-3xl font-extrabold flex items-center gap-3">
                <Star className="text-yellow-400" fill="currentColor" />{" "}
                Relatório Scout Elite
              </h1>
              <p className="text-gray-300 mt-1 font-medium">
                Análise detalhada dos prospectos com Overall superior a 8.0
              </p>
            </div>
          </div>
        </div>

        {loading ? (
          <div className="flex justify-center py-20">
            <Loader2 className="animate-spin h-12 w-12 text-brand-darkred" />
          </div>
        ) : players.length === 0 ? (
          <div className="bg-white dark:bg-gray-900 p-12 rounded-2xl text-center border border-gray-100 dark:border-gray-800">
            <Target size={64} className="mx-auto text-gray-300 mb-4" />
            <h2 className="text-2xl font-bold text-gray-600 dark:text-gray-300">
              Nenhum talento Elite ainda
            </h2>
            <p className="text-gray-500 mt-2">
              Os jogadores avaliados ainda não atingiram a nota média de 8.0.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            {players.map((player, index) => {
              // Pegamos os atributos certos para a posição dele
              const attrs = getAttributesByPosition(player.posicao_principal);

              // Pegamos os 3 melhores atributos dele para destacar
              const avaliacao = player.ultima_avaliacao || {};
              const topAttrs = [...attrs]
                .sort(
                  (a, b) =>
                    (Number(avaliacao[b.key]) || 0) -
                    (Number(avaliacao[a.key]) || 0),
                )
                .slice(0, 4);

              return (
                <div
                  key={player.id}
                  className="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden group hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                >
                  {/* Cabeçalho do Card */}
                  <div className="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-start">
                    <div className="flex gap-4">
                      <div className="relative">
                        <img
                          src={
                            player.pessoa?.foto_url_completa ||
                            "/img/avatar_padrao.png"
                          }
                          className="w-16 h-16 rounded-full object-cover border-2 border-brand-primary shadow-sm"
                          onError={(e) =>
                            (e.currentTarget.src = "/img/avatar_padrao.png")
                          }
                        />
                        {/* Medalha para o Top 3 */}
                        {index < 3 && (
                          <div
                            className={`absolute -bottom-2 -right-2 w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-gray-900 shadow-md
                            ${index === 0 ? "bg-yellow-500" : index === 1 ? "bg-gray-400" : "bg-amber-700"}`}
                          >
                            {index + 1}º
                          </div>
                        )}
                      </div>
                      <div>
                        <h3 className="font-extrabold text-lg text-brand-primary dark:text-white leading-tight">
                          {player.pessoa?.nome_completo}
                        </h3>
                        <p className="text-sm text-gray-500 dark:text-gray-400 font-bold">
                          {player.posicao_principal}
                        </p>
                        <p className="text-xs text-gray-400 mt-1">
                          {player.altura_cm ? `${player.altura_cm}cm` : ""} •{" "}
                          {player.pe_preferido || "Pé indf."}
                        </p>
                      </div>
                    </div>

                    {/* Nota Overall Destaque */}
                    <div className="flex flex-col items-center justify-center bg-gradient-to-br from-yellow-400 to-yellow-600 w-14 h-14 rounded-xl shadow-md text-white transform group-hover:scale-110 transition-transform">
                      <span className="text-[10px] font-bold uppercase opacity-90 leading-none mt-1">
                        OVR
                      </span>
                      <span className="text-xl font-extrabold leading-none mt-1">
                        {Number(player.rating_medio).toFixed(1)}
                      </span>
                    </div>
                  </div>

                  {/* Corpo Analítico (Atributos) */}
                  <div className="p-6">
                    <h4 className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                      <Activity size={14} /> Atributos em Destaque
                    </h4>
                    <div className="space-y-3">
                      {topAttrs.map((attr) => (
                        <AttributeBar
                          key={attr.key}
                          label={attr.label}
                          value={avaliacao[attr.key] || 0}
                        />
                      ))}
                    </div>

                    <button
                      onClick={() => navigate(`/jogadores/${player.id}`)}
                      className="mt-6 w-full py-2.5 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                    >
                      Análise Completa
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </Layout>
  );
};

export default AtletasDestaque;
