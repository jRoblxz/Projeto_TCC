import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "@/config/api";
import Layout from "@/components/layouts/Layout";
import {
  Video,
  Plus,
  Calendar,
  Trash2,
  Edit2,
  CheckCircle,
  Loader2,
  AlertTriangle,
  Clock,
  ChevronLeft,
  ChevronRight,
  X,
  Save,
} from "lucide-react";
import toast from "react-hot-toast";

// Tipagem baseada no backend
interface VideoJob {
  id: number;
  titulo: string;
  status: "pending" | "processing" | "done" | "failed";
  original_filename: string;
  output_video_url: string | null;
  output_csv_url: string | null;
  created_at: string;
}

const AnalisesGaleria: React.FC = () => {
  const navigate = useNavigate();

  const [jobs, setJobs] = useState<VideoJob[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [meta, setMeta] = useState<any>(null);

  // Estados para edição do título
  const [editingId, setEditingId] = useState<number | null>(null);
  const [editTitle, setEditTitle] = useState("");

  const fetchJobs = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/video-jobs?page=${page}`);
      setJobs(response.data.data);
      setMeta(response.data); // O Laravel envia os dados de paginação na raiz
    } catch (error) {
      console.error(error);
      toast.error("Erro ao carregar as análises.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchJobs();
  }, [page]);

  // Função para Deletar
  const handleDelete = async (id: number, titulo: string) => {
    if (
      !window.confirm(
        `Tem certeza que deseja excluir a análise "${titulo}"? Todos os vídeos e planilhas gerados serão apagados permanentemente.`,
      )
    ) {
      return;
    }

    const toastId = toast.loading("Excluindo arquivos da nuvem...");
    try {
      await api.delete(`/video-jobs/${id}`);
      toast.success("Análise excluída com sucesso!", { id: toastId });

      // Se era o último item da página e não é a primeira página, volta uma página
      if (jobs.length === 1 && page > 1) {
        setPage(page - 1);
      } else {
        fetchJobs();
      }
    } catch (error) {
      console.error(error);
      toast.error("Erro ao excluir análise.", { id: toastId });
    }
  };

  // Funções de Edição de Título
  const startEditing = (job: VideoJob) => {
    setEditingId(job.id);
    setEditTitle(job.titulo || job.original_filename);
  };

  const cancelEditing = () => {
    setEditingId(null);
    setEditTitle("");
  };

  const saveTitle = async (id: number) => {
    if (!editTitle.trim()) {
      toast.error("O título não pode ser vazio.");
      return;
    }

    try {
      await api.put(`/video-jobs/${id}`, { titulo: editTitle });
      toast.success("Título atualizado!");

      // Atualiza a lista localmente sem precisar recarregar a página
      setJobs(
        jobs.map((job) =>
          job.id === id ? { ...job, titulo: editTitle } : job,
        ),
      );
      setEditingId(null);
    } catch (error) {
      console.error(error);
      toast.error("Erro ao atualizar o título.");
    }
  };

  // Helper para cores e ícones do Status
  const getStatusDisplay = (status: string) => {
    switch (status) {
      case "done":
        return {
          color: "text-green-600 bg-green-100 border-green-200",
          icon: <CheckCircle size={16} />,
          text: "Concluído",
        };
      case "processing":
        return {
          color: "text-blue-600 bg-blue-100 border-blue-200",
          icon: <Loader2 size={16} className="animate-spin" />,
          text: "Processando (IA)",
        };
      case "pending":
        return {
          color: "text-yellow-600 bg-yellow-100 border-yellow-200",
          icon: <Clock size={16} />,
          text: "Na Fila",
        };
      case "failed":
        return {
          color: "text-red-600 bg-red-100 border-red-200",
          icon: <AlertTriangle size={16} />,
          text: "Falha",
        };
      default:
        return {
          color: "text-gray-600 bg-gray-100 border-gray-200",
          icon: <Clock size={16} />,
          text: status,
        };
    }
  };

  return (
    <Layout>
      <div className="p-6 min-h-screen pb-20 max-w-[1400px] mx-auto">
        {/* HEADER */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
          <div>
            <h1 className="text-2xl font-bold text-brand-primary dark:text-white flex items-center gap-2">
              <Video className="text-brand-darkred" /> Galeria de Análises
              Táticas
            </h1>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Gerencie seus vídeos processados pela Inteligência Artificial.
            </p>
          </div>

          <button
            onClick={() => navigate("/tracking/novo")}
            className="mt-4 md:mt-0 px-6 py-3 bg-brand-darkred text-white font-bold rounded-xl hover:bg-[#a01519] transition shadow-md flex items-center gap-2"
          >
            <Plus size={20} /> Nova Análise
          </button>
        </div>

        {/* LISTAGEM (GRID) */}
        {loading ? (
          <div className="flex justify-center items-center h-64">
            <Loader2 className="h-12 w-12 text-brand-darkred animate-spin" />
          </div>
        ) : (
          <>
            <div className="grid gap-6 grid-cols-[repeat(auto-fit,minmax(320px,1fr))]">
              {jobs.map((job) => {
                const statusInfo = getStatusDisplay(job.status);

                return (
                  <div
                    key={job.id}
                    className="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300"
                  >
                    {/* Imagem de Capa Fake (Placeholder) */}
                    {/* Imagem de Capa ou Thumbnail do Vídeo */}
                    <div
                      className="h-40 bg-gray-200 dark:bg-gray-800 relative cursor-pointer group overflow-hidden"
                      onClick={() => navigate(`/tracking/${job.id}`)}
                    >
                      {/* Se o vídeo estiver pronto, carrega o frame 0.001 como capa! */}
                      {job.output_video_url ? (
                        <video
                          src={`${job.output_video_url}#t=0.001`}
                          className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                          preload="metadata"
                          muted
                          playsInline
                        />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-800">
                          <Video className="text-gray-400" size={36} />
                        </div>
                      )}

                      {/* Overlay Escuro com Play ao passar o mouse */}
                      <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <div className="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/40 scale-75 group-hover:scale-100 transition-transform duration-300">
                          <Video className="text-white ml-1" size={24} />
                        </div>
                      </div>

                      {/* Badge de Status */}
                      <div
                        className={`absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 border ${statusInfo.color} shadow-sm backdrop-blur-md bg-white/90 dark:bg-gray-900/90`}
                      >
                        {statusInfo.icon} {statusInfo.text}
                      </div>
                    </div>

                    {/* Corpo do Card */}
                    <div className="p-5 flex-1 flex flex-col">
                      {/* Lógica de Edição de Título */}
                      {editingId === job.id ? (
                        <div className="flex items-center gap-2 mb-3">
                          <input
                            type="text"
                            value={editTitle}
                            onChange={(e) => setEditTitle(e.target.value)}
                            className="flex-1 px-3 py-1.5 text-sm border-2 border-brand-primary rounded-lg outline-none font-bold text-gray-800 dark:text-white dark:bg-gray-800"
                            autoFocus
                          />
                          <button
                            onClick={() => saveTitle(job.id)}
                            className="p-1.5 text-green-600 hover:bg-green-50 rounded-md transition"
                          >
                            <Save size={18} />
                          </button>
                          <button
                            onClick={cancelEditing}
                            className="p-1.5 text-gray-400 hover:bg-gray-100 rounded-md transition"
                          >
                            <X size={18} />
                          </button>
                        </div>
                      ) : (
                        <h3
                          className="text-lg font-bold text-gray-800 dark:text-white mb-2 line-clamp-2 cursor-pointer hover:text-brand-darkred transition-colors"
                          onClick={() => navigate(`/tracking/${job.id}`)}
                          title={job.titulo || job.original_filename}
                        >
                          {job.titulo || job.original_filename}
                        </h3>
                      )}

                      <div className="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4 mt-auto">
                        <Calendar size={14} className="mr-1.5" />
                        {new Date(job.created_at).toLocaleDateString("pt-BR", {
                          day: "2-digit",
                          month: "short",
                          year: "numeric",
                          hour: "2-digit",
                          minute: "2-digit",
                        })}
                      </div>

                      {/* Botões de Ação */}
                      <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center gap-2">
                        <button
                          onClick={() => navigate(`/tracking/${job.id}`)}
                          className="flex-1 py-2 bg-gray-50 dark:bg-gray-800 hover:bg-brand-primary hover:text-white text-brand-primary dark:text-gray-300 font-bold rounded-lg transition-colors text-sm"
                        >
                          Abrir Análise
                        </button>
                        <div className="flex gap-1">
                          <button
                            onClick={() => startEditing(job)}
                            className="p-2 text-gray-500 hover:text-brand-primary hover:bg-blue-50 dark:hover:bg-gray-800 rounded-lg transition-colors"
                            title="Editar Nome"
                          >
                            <Edit2 size={18} />
                          </button>
                          <button
                            onClick={() =>
                              handleDelete(
                                job.id,
                                job.titulo || job.original_filename,
                              )
                            }
                            className="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Excluir Análise"
                          >
                            <Trash2 size={18} />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>

            {jobs.length === 0 && (
              <div className="flex flex-col items-center justify-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                <Video size={48} className="text-gray-300 mb-4" />
                <p className="text-gray-500 font-bold text-lg">
                  Nenhuma análise processada ainda.
                </p>
                <p className="text-gray-400 text-sm mt-1">
                  Clique em "Nova Análise" para enviar o seu primeiro vídeo.
                </p>
              </div>
            )}
          </>
        )}

        {/* PAGINAÇÃO */}
        {meta && meta.last_page > 1 && (
          <div className="flex justify-center items-center gap-4 mt-10">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page === 1}
              className="p-2 rounded-lg border bg-white dark:bg-gray-800 dark:border-gray-700 disabled:opacity-50 hover:bg-gray-50 transition shadow-sm"
            >
              <ChevronLeft size={20} />
            </button>
            <span className="text-sm font-bold text-gray-600 dark:text-gray-300">
              Página {meta.current_page} de {meta.last_page}
            </span>
            <button
              onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
              disabled={page === meta.last_page}
              className="p-2 rounded-lg border bg-white dark:bg-gray-800 dark:border-gray-700 disabled:opacity-50 hover:bg-gray-50 transition shadow-sm"
            >
              <ChevronRight size={20} />
            </button>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default AnalisesGaleria;
