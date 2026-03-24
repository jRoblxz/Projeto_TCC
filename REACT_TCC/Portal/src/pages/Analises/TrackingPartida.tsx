import React, { useState, useRef } from "react";
import { useNavigate } from "react-router-dom";
import Layout from "@/components/layouts/Layout";
import { ArrowLeft, UploadCloud, FileVideo, X, PlayCircle, Loader2, CheckCircle, AlertTriangle, Download } from "lucide-react";
import toast from "react-hot-toast";
import { api } from "@/config/api"; // Certifique-se de que o caminho do seu axios está correto
import { useVideoJob } from "@/hooks/useVideoJob"; // Ajuste o caminho do seu hook

const TrackingPartida: React.FC = () => {
  const navigate = useNavigate();
  const [videoFile, setVideoFile] = useState<File | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Novos estados para a integração
  const [isUploading, setIsUploading] = useState(false);
  const [jobId, setJobId] = useState<number | null>(null);

  // O seu hook assumindo o controle após o upload!
  const { job, error: jobError } = useVideoJob(jobId);

  // --- Manipuladores de Drag & Drop ---
  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      validateAndSetFile(e.dataTransfer.files[0]);
    }
  };

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      validateAndSetFile(e.target.files[0]);
    }
  };

  const validateAndSetFile = (file: File) => {
    if (file.type.startsWith("video/")) {
      setVideoFile(file);
    } else {
      toast.error("Por favor, selecione apenas arquivos de vídeo (MP4, AVI, etc).");
    }
  };

  const removeFile = () => {
    setVideoFile(null);
    setJobId(null);
    if (fileInputRef.current) fileInputRef.current.value = "";
  };

  // --- O DISPARO PARA O LARAVEL ---
  const handleContinue = async () => {
    if (!videoFile) {
      toast.error("Selecione um vídeo primeiro!");
      return;
    }
    
    setIsUploading(true);
    const formData = new FormData();
    formData.append("video", videoFile);

    try {
      // Bate no VideoJobController@store
      const response = await api.post("/video-jobs", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      // O Laravel vai devolver o job_id. Salvamos ele para o hook começar o polling.
      setJobId(response.data.job_id);
      toast.success("Vídeo enviado! A IA começou a trabalhar.");
    } catch (error: any) {
      console.error("Erro no upload:", error);
      toast.error(error.response?.data?.message || "Erro ao enviar o vídeo para análise.");
    } finally {
      setIsUploading(false);
    }
  };

  const formatFileSize = (bytes: number) => {
    return (bytes / (1024 * 1024)).toFixed(2) + " MB";
  };

  // --- RENDERIZAÇÃO DAS TELAS DE STATUS ---
  
  // 1. Tela de Upload / Preview (Antes de enviar)
  if (!jobId) {
    return (
      <Layout>
        <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8">
          <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
            <div className="flex items-center gap-4">
              <button onClick={() => navigate(-1)} className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition">
                <ArrowLeft className="text-gray-700 dark:text-gray-300" />
              </button>
              <div>
                <h1 className="text-2xl font-bold text-[#14244D] dark:text-white">Tracking de Partida</h1>
                <p className="text-sm text-gray-500 dark:text-gray-400">Importe um vídeo para análise tática e rastreamento.</p>
              </div>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-8 md:p-12 flex flex-col items-center justify-center min-h-[400px]">
            {!videoFile ? (
              <div
                className={`w-full max-w-2xl border-4 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer ${isDragging ? "border-[#8B0000] bg-[#8B0000]/5 scale-105" : "border-gray-300 hover:border-[#14244D] hover:bg-gray-50 dark:border-gray-700"}`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                onClick={() => fileInputRef.current?.click()}
              >
                <div className="w-20 h-20 bg-[#14244D]/10 text-[#14244D] rounded-full flex items-center justify-center mb-6">
                  <UploadCloud size={40} />
                </div>
                <h3 className="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Arraste e solte o vídeo aqui</h3>
                <p className="text-gray-500 mb-6">ou clique para procurar nos seus arquivos (MP4, MOV, AVI)</p>
                <button className="px-6 py-2.5 bg-[#14244D] text-white font-bold rounded-lg hover:bg-[#1e3a8a] transition shadow-md">
                  Selecionar Arquivo
                </button>
                <input type="file" ref={fileInputRef} onChange={handleFileSelect} accept="video/*" className="hidden" />
              </div>
            ) : (
              <div className="w-full max-w-2xl bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-8 flex flex-col items-center relative animate-in fade-in zoom-in duration-300">
                {!isUploading && (
                  <button onClick={removeFile} className="absolute top-4 right-4 p-2 bg-white dark:bg-gray-700 rounded-full shadow-sm text-gray-500 hover:text-red-500 transition">
                    <X size={20} />
                  </button>
                )}

                <div className="w-24 h-24 bg-[#8B0000]/10 text-[#8B0000] rounded-full flex items-center justify-center mb-4">
                  <FileVideo size={48} />
                </div>
                
                <h3 className="text-xl font-bold text-gray-800 dark:text-white text-center break-all mb-1">{videoFile.name}</h3>
                <p className="text-gray-500 font-medium mb-8">Tamanho: {formatFileSize(videoFile.size)}</p>

                <div className="flex gap-4 w-full justify-center">
                  {!isUploading ? (
                    <>
                      <button onClick={removeFile} className="px-6 py-3 bg-white text-gray-700 font-bold border rounded-xl hover:bg-gray-100 transition">
                        Trocar Arquivo
                      </button>
                      <button onClick={handleContinue} className="px-8 py-3 bg-[#8B0000] text-white font-bold rounded-xl hover:bg-[#a01519] transition shadow-lg flex items-center gap-2">
                        <PlayCircle size={20} /> Iniciar Tracking
                      </button>
                    </>
                  ) : (
                    <div className="flex items-center gap-3 text-[#14244D] font-bold">
                      <Loader2 className="animate-spin" size={24} />
                      Enviando vídeo para o servidor...
                    </div>
                  )}
                </div>
              </div>
            )}
          </div>
        </div>
      </Layout>
    );
  }

  // 2. Tela de Processamento / Resultado (Quando já temos o jobId)
  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8">
        
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-8 md:p-12 flex flex-col items-center justify-center min-h-[400px]">
          
          {/* Status: Processando */}
          {(job?.status === 'pending' || job?.status === 'processing') && (
             <div className="flex flex-col items-center text-center animate-in fade-in duration-500">
                <div className="w-24 h-24 mb-6 relative flex items-center justify-center">
                  <div className="absolute inset-0 border-4 border-t-[#8B0000] border-gray-200 rounded-full animate-spin"></div>
                  <Loader2 className="text-[#8B0000] animate-pulse" size={40} />
                </div>
                <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">Analisando o Jogo...</h2>
                <p className="text-gray-500 max-w-md">
                  A nossa Inteligência Artificial está rastreando os jogadores e a bola. Isso pode levar alguns minutos dependendo do tamanho do vídeo.
                </p>
             </div>
          )}

          {/* Status: Concluído */}
          {job?.status === 'done' && (
            <div className="flex flex-col items-center w-full animate-in zoom-in duration-500">
              <div className="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
                <CheckCircle size={40} />
              </div>
              <h2 className="text-3xl font-bold text-gray-800 dark:text-white mb-6">Análise Concluída!</h2>
              
              {/* Player do Vídeo Processado */}
              {job.video_url && (
                <div className="w-full max-w-4xl bg-black rounded-xl overflow-hidden shadow-lg mb-8 aspect-video">
                  <video src={job.video_url} controls className="w-full h-full object-cover" />
                </div>
              )}

              {/* Botão de Download do CSV com Coordenadas */}
              {job.csv_url && (
                <a 
                  href={job.csv_url} 
                  target="_blank"
                  rel="noopener noreferrer"
                  className="px-8 py-4 bg-[#14244D] text-white font-bold rounded-xl hover:bg-[#1e3a8a] transition shadow-lg flex items-center gap-3"
                >
                  <Download size={24} />
                  Baixar Coordenadas (CSV)
                </a>
              )}
            </div>
          )}

          {/* Status: Falha */}
          {(job?.status === 'failed' || jobError) && (
             <div className="flex flex-col items-center text-center animate-in fade-in duration-500">
                <div className="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-6">
                  <AlertTriangle size={40} />
                </div>
                <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">Erro no Processamento</h2>
                <p className="text-gray-500 mb-6 max-w-md">
                  {job?.error || jobError || "Ocorreu um erro desconhecido ao processar o seu vídeo."}
                </p>
                <button 
                  onClick={removeFile}
                  className="px-6 py-3 bg-gray-200 text-gray-800 font-bold rounded-xl hover:bg-gray-300 transition"
                >
                  Tentar Outro Vídeo
                </button>
             </div>
          )}

        </div>
      </div>
    </Layout>
  );
};

export default TrackingPartida;