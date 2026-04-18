import React, { useState, useEffect } from "react";
import {
  Calendar,
  MapPin,
  Trophy,
  Loader2,
  CheckCircle,
  ArrowRight,
} from "lucide-react";
import { api } from "@/config/api";
import toast from "react-hot-toast";

interface PeneiraDisponivel {
  id: number;
  nome_evento: string; // Ajustado para bater com seu JSON
  data_evento: string; // Ajustado para bater com seu JSON
  local: string;
  sub_divisao: string; // Ajustado para bater com seu JSON
  status: string;
}

const ProximasOportunidades: React.FC = () => {
  const [peneiras, setPeneiras] = useState<PeneiraDisponivel[]>([]);
  const [minhasInscricoes, setMinhasInscricoes] = useState<any[]>([]); // Para peneiras já inscritas
  const [loading, setLoading] = useState(true);
  const [enrollingId, setEnrollingId] = useState<number | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);

      // Busca Novas Oportunidades (Independente)
      try {
        const resDisp = await api.get("/my-available-peneiras");
        setPeneiras(resDisp.data);
      } catch (error) {
        console.error("Erro ao buscar peneiras disponíveis", error);
      }

      // Busca Minhas Inscrições (Independente)
      try {
        const resInsc = await api.get("/my-enrollments");
        setMinhasInscricoes(resInsc.data);
      } catch (error) {
        console.error("Erro ao buscar inscrições atuais", error);
      }

      setLoading(false);
    };

    fetchData();
  }, []);

  const handleQuickEnroll = async (peneiraId: number, nomePeneira: string) => {
    if (!window.confirm(`Confirmar inscrição para: ${nomePeneira}?`)) return;
    setEnrollingId(peneiraId);
    try {
      await api.post("/enroll-again", { peneira_id: peneiraId });
      toast.success("Inscrição confirmada!");
      setPeneiras(peneiras.filter((p) => p.id !== peneiraId));
      // Recarregar inscrições para atualizar a lista de cima
      const resInsc = await api.get("/my-enrollments");
      setMinhasInscricoes(resInsc.data);
    } catch (error: any) {
      toast.error(error.response?.data?.error || "Erro na inscrição.");
    } finally {
      setEnrollingId(null);
    }
  };

  if (loading)
    return (
      <div className="flex justify-center p-8">
        <Loader2 className="animate-spin text-brand-secondary" size={32} />
      </div>
    );

  return (
    <div className="space-y-8">
      {/* SEÇÃO: MINHAS INSCRIÇÕES */}
      {minhasInscricoes.length > 0 && (
        <div className="bg-green-50 dark:bg-green-900/10 rounded-2xl p-6 border border-green-100 dark:border-green-800">
          <h2 className="text-xl font-bold text-green-800 dark:text-green-400 flex items-center gap-2 mb-4">
            <CheckCircle size={20} /> Já Inscrito
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {minhasInscricoes.map((insc) => (
              <div
                key={insc.id}
                className="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-green-200"
              >
                <p className="font-bold text-gray-800 dark:text-white">
                  {insc.nome_evento}
                </p>
                <p className="text-xs text-gray-500 uppercase">
                  {insc.status_inscricao || "Pendente"}
                </p>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* SEÇÃO: NOVAS OPORTUNIDADES */}
      <div className="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-800">
        <div className="mb-6">
          <h2 className="text-2xl font-bold text-brand-primary dark:text-white flex items-center gap-2">
            <Trophy className="text-brand-secondary" /> Oportunidades para{" "}
            {peneiras[0]?.sub_divisao || "Sua Categoria"}
          </h2>
        </div>

        {peneiras.length === 0 ? (
          <div className="text-center py-10 opacity-60">
            Sem novas peneiras disponíveis no momento.
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {peneiras.map((peneira) => (
              <div
                key={peneira.id}
                className="group border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:border-brand-primary transition-all bg-gray-50 dark:bg-gray-800 flex flex-col"
              >
                <div className="bg-brand-primary p-3 text-white">
                  <span className="text-xs font-bold uppercase tracking-wider bg-white/20 px-2 py-1 rounded mb-2 inline-block">
                    {peneira.sub_divisao || "Geral"}
                  </span>
                  <h3 className="font-bold text-lg truncate">
                    {peneira.nome_evento}
                  </h3>
                </div>
                <div className="p-4 flex-1 flex flex-col justify-between">
                  <div className="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-300">
                    <div className="flex items-center gap-2">
                      <Calendar size={14} className="text-brand-secondary" />{" "}
                      {new Date(peneira.data_evento).toLocaleDateString(
                        "pt-BR",
                      )}
                    </div>
                    <div className="flex items-center gap-2">
                      <MapPin size={14} className="text-brand-secondary" />{" "}
                      {peneira.local}
                    </div>
                  </div>
                  <button
                    onClick={() =>
                      handleQuickEnroll(peneira.id, peneira.nome_evento)
                    }
                    disabled={enrollingId === peneira.id}
                    className="w-full bg-brand-secondary hover:bg-red-800 text-white py-2 rounded-lg font-bold transition-all flex items-center justify-center gap-2"
                  >
                    {enrollingId === peneira.id ? (
                      <Loader2 size={18} className="animate-spin" />
                    ) : (
                      <>
                        Participar <ArrowRight size={18} />
                      </>
                    )}
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default ProximasOportunidades;
