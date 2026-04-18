import React, { useState, useRef } from "react";
import axios from "axios";
import { useNavigate, useParams } from "react-router-dom";
import Layout from "@/components/layouts/Layout";
import {
  ArrowLeft,
  UploadCloud,
  FileVideo,
  X,
  PlayCircle,
  Loader2,
  CheckCircle,
  AlertTriangle,
  Download,
  BarChart3,
  User,
  Flame,
  Activity,
} from "lucide-react";
import toast from "react-hot-toast";
import { api } from "@/config/api";
import { useVideoJob } from "@/hooks/useVideoJob";

const TrackingPartida: React.FC = () => {
  const navigate = useNavigate();
  const { id } = useParams();

  const [videoFile, setVideoFile] = useState<File | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [analiseTitulo, setAnaliseTitulo] = useState<string>("");

  // Estados do Dashboard
  const [activeTab, setActiveTab] = useState<"geral" | "individual">("geral");
  const [selectedPlayerId, setSelectedPlayerId] = useState<number>(1);

  // Novos estados para a integração
  const [isUploading, setIsUploading] = useState(false);
  const [jobId, setJobId] = useState<number | null>(id ? Number(id) : null);

  // O seu hook assumindo o controle
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
      toast.error(
        "Por favor, selecione apenas arquivos de vídeo (MP4, AVI, etc).",
      );
    }
  };

  const removeFile = () => {
    setVideoFile(null);
    setJobId(null);
    if (fileInputRef.current) fileInputRef.current.value = "";
  };

  // --- O DISPARO DIRETO PARA A NUVEM (SIGNED URL) ---
  const handleContinue = async () => {
    if (!videoFile) {
      toast.error("Selecione um vídeo primeiro!");
      return;
    }

    if (!analiseTitulo.trim()) {
      toast.error("Por favor, dê um título para a análise.");
      return;
    }

    setIsUploading(true);

    try {
      const authResponse = await api.post("/video-jobs/upload-url");
      const { upload_url, gcs_path } = authResponse.data;

      await axios.put(upload_url, videoFile, {
        headers: {
          "Content-Type": videoFile.type || "video/mp4",
        },
      });

      const jobResponse = await api.post("/video-jobs", {
        titulo: analiseTitulo,
        gcs_path: gcs_path,
        original_filename: videoFile.name,
      });

      setJobId(jobResponse.data.job_id);
      toast.success("Vídeo enviado! A IA começou a trabalhar.");
    } catch (error: any) {
      console.error("Erro no processo:", error);
      toast.error(
        error.response?.data?.message || "Erro ao enviar o vídeo para análise.",
      );
    } finally {
      setIsUploading(false);
    }
  };

  const formatFileSize = (bytes: number) => {
    return (bytes / (1024 * 1024)).toFixed(2) + " MB";
  };

  // 1. Tela de Upload / Preview (Antes de enviar)
  if (!jobId) {
    return (
      <Layout>
        <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8">
          <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
            <div className="flex items-center gap-4">
              <button
                onClick={() => navigate(-1)}
                className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
              >
                <ArrowLeft className="text-gray-700 dark:text-gray-300" />
              </button>
              <div>
                <h1 className="text-2xl font-bold text-brand-primary dark:text-white">
                  Tracking de Partida
                </h1>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                  Importe um vídeo para análise tática e rastreamento.
                </p>
              </div>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-8 md:p-12 flex flex-col items-center justify-center min-h-[400px]">
            {/* NOVO CAMPO DE TÍTULO */}
            <div className="w-full max-w-2xl mb-8">
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                Título/Nome da Análise *
              </label>
              <input
                type="text"
                value={analiseTitulo}
                onChange={(e) => setAnaliseTitulo(e.target.value)}
                placeholder="Ex: Análise Tática Fatec vs Santos Sub-20"
                className="w-full pl-4 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                required
              />
              <p className="text-xs text-gray-500 mt-1">
                Dê um nome para identificar esta análise mais tarde na galeria.
              </p>
            </div>

            {!videoFile ? (
              <div
                className={`w-full max-w-2xl border-4 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer ${isDragging ? "border-brand-darkred bg-brand-darkred/5 scale-105" : "border-gray-300 hover:border-brand-primary hover:bg-gray-50 dark:border-gray-700"}`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                onClick={() => fileInputRef.current?.click()}
              >
                <div className="w-20 h-20 bg-brand-primary/10 text-brand-primary rounded-full flex items-center justify-center mb-6">
                  <UploadCloud size={40} />
                </div>
                <h3 className="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                  Arraste e solte o vídeo aqui
                </h3>
                <p className="text-gray-500 mb-6">
                  ou clique para procurar nos seus arquivos (MP4, MOV, AVI)
                </p>
                <button className="px-6 py-2.5 bg-brand-primary text-white font-bold rounded-lg hover:bg-[#1e3a8a] transition shadow-md">
                  Selecionar Arquivo
                </button>
                <input
                  type="file"
                  ref={fileInputRef}
                  onChange={handleFileSelect}
                  accept="video/*"
                  className="hidden"
                />
              </div>
            ) : (
              <div className="w-full max-w-2xl bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-8 flex flex-col items-center relative animate-in fade-in zoom-in duration-300">
                {!isUploading && (
                  <button
                    onClick={removeFile}
                    className="absolute top-4 right-4 p-2 bg-white dark:bg-gray-700 rounded-full shadow-sm text-gray-500 hover:text-red-500 transition"
                  >
                    <X size={20} />
                  </button>
                )}
                <div className="w-24 h-24 bg-brand-darkred/10 text-brand-darkred rounded-full flex items-center justify-center mb-4">
                  <FileVideo size={48} />
                </div>
                <h3 className="text-xl font-bold text-gray-800 dark:text-white text-center break-all mb-1">
                  {videoFile.name}
                </h3>
                <p className="text-gray-500 font-medium mb-8">
                  Tamanho: {formatFileSize(videoFile.size)}
                </p>

                <div className="flex gap-4 w-full justify-center">
                  {!isUploading ? (
                    <>
                      <button
                        onClick={removeFile}
                        className="px-6 py-3 bg-white text-gray-700 font-bold border rounded-xl hover:bg-gray-100 transition"
                      >
                        Trocar Arquivo
                      </button>
                      <button
                        onClick={handleContinue}
                        className="px-8 py-3 bg-brand-darkred text-white font-bold rounded-xl hover:bg-[#a01519] transition shadow-lg flex items-center gap-2"
                      >
                        <PlayCircle size={20} /> Iniciar Tracking
                      </button>
                    </>
                  ) : (
                    <div className="flex items-center gap-3 text-brand-primary font-bold">
                      <Loader2 className="animate-spin" size={24} /> Enviando
                      vídeo para o servidor...
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

  // 2. Tela de Processamento / Resultado
  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8">
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-8 flex flex-col items-center justify-center min-h-[400px]">
          {/* Status: Processando */}
          {(job?.status === "pending" || job?.status === "processing") && (
            <div className="flex flex-col items-center text-center animate-in fade-in duration-500 py-10">
              <div className="w-24 h-24 mb-6 relative flex items-center justify-center">
                <div className="absolute inset-0 border-4 border-t-brand-darkred border-gray-200 rounded-full animate-spin"></div>
                <Loader2
                  className="text-brand-darkred animate-pulse"
                  size={40}
                />
              </div>
              <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                Analisando o Jogo...
              </h2>
              <p className="text-gray-500 max-w-md">
                A nossa Inteligência Artificial está rastreando os jogadores e a
                bola. Isso pode levar alguns minutos dependendo do tamanho do
                vídeo.
              </p>
            </div>
          )}

          {/* Status: Concluído - DASHBOARD DE ANÁLISE */}
          {job?.status === "done" && (
            <div className="w-full animate-in zoom-in duration-500">
              {/* Cabeçalho do Resultado com o Título Customizado */}
              <div className="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-100 dark:border-gray-800 pb-6">
                <div className="flex items-center gap-4 mb-4 md:mb-0">
                  <div className="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center shadow-sm">
                    <CheckCircle size={28} />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-gray-800 dark:text-white">
                      {job.titulo || "Análise de Vídeo"}
                    </h2>
                    <p className="text-sm text-gray-500">
                      Dados processados pela IA de Visão Computacional
                    </p>
                  </div>
                </div>

                {job.csv_url && (
                  <a
                    href={job.csv_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="px-6 py-2.5 bg-brand-primary text-white font-bold rounded-xl hover:bg-[#1e3a8a] transition shadow-md flex items-center gap-2"
                  >
                    <Download size={20} /> Exportar CSV
                  </a>
                )}
              </div>

              {/* NAVEGAÇÃO DE ABAS */}
              <div className="flex gap-4 mb-6 border-b border-gray-200 dark:border-gray-700 pb-2 overflow-x-auto">
                <button
                  onClick={() => setActiveTab("geral")}
                  className={`flex items-center gap-2 px-4 py-2 font-bold transition-all whitespace-nowrap ${activeTab === "geral" ? "text-brand-darkred border-b-4 border-brand-darkred" : "text-gray-500 hover:text-brand-primary"}`}
                >
                  <BarChart3 size={20} /> Panorama Geral
                </button>
                <button
                  onClick={() => setActiveTab("individual")}
                  className={`flex items-center gap-2 px-4 py-2 font-bold transition-all whitespace-nowrap ${activeTab === "individual" ? "text-brand-darkred border-b-4 border-brand-darkred" : "text-gray-500 hover:text-brand-primary"}`}
                >
                  <User size={20} /> Análise Individual
                </button>
              </div>

              {/* === ABA 1: PANORAMA GERAL === */}
              {activeTab === "geral" && (
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div className="flex flex-col gap-4">
                    <h3 className="font-bold text-brand-primary dark:text-white text-lg">
                      Câmera Tática (Tracking)
                    </h3>
                    {job.video_url && (
                      <div className="w-full bg-black rounded-2xl overflow-hidden shadow-lg aspect-video border border-gray-200 dark:border-gray-800">
                        <video
                          src={job.video_url}
                          controls
                          className="w-full h-full object-cover"
                        />
                      </div>
                    )}
                  </div>

                  <div className="flex flex-col gap-4">
                    <h3 className="font-bold text-brand-primary dark:text-white text-lg">
                      Estatísticas da Partida
                    </h3>
                    <div className="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-inner">
                      <div className="flex justify-between items-center mb-6">
                        <div className="text-center">
                          <div className="w-10 h-10 bg-brand-primary rounded-full mx-auto mb-1 shadow-sm"></div>
                          <span className="font-bold text-gray-800 dark:text-white text-sm">
                            Time A
                          </span>
                        </div>
                        <div className="text-2xl font-black text-gray-400">
                          vs
                        </div>
                        <div className="text-center">
                          <div className="w-10 h-10 bg-brand-darkred rounded-full mx-auto mb-1 shadow-sm"></div>
                          <span className="font-bold text-gray-800 dark:text-white text-sm">
                            Time B
                          </span>
                        </div>
                      </div>

                      <div className="space-y-5">
                        {[
                          { label: "Posse de Bola", a: 41, b: 59, isPct: true },
                          {
                            label: "Grandes Chances",
                            a: 2,
                            b: 1,
                            isPct: false,
                          },
                          { label: "Finalizações", a: 11, b: 14, isPct: false },
                          { label: "Escanteios", a: 5, b: 8, isPct: false },
                          { label: "Desarmes", a: 15, b: 12, isPct: false },
                        ].map((stat, idx) => {
                          const total = stat.a + stat.b;
                          const pctA = total > 0 ? (stat.a / total) * 100 : 50;
                          const pctB = total > 0 ? (stat.b / total) * 100 : 50;

                          return (
                            <div key={idx} className="flex flex-col gap-1">
                              <div className="flex justify-between text-sm font-bold">
                                <span
                                  className={
                                    stat.a > stat.b
                                      ? "text-brand-primary dark:text-blue-400"
                                      : "text-gray-500"
                                  }
                                >
                                  {stat.a}
                                  {stat.isPct ? "%" : ""}
                                </span>
                                <span className="text-gray-600 dark:text-gray-300 font-medium">
                                  {stat.label}
                                </span>
                                <span
                                  className={
                                    stat.b > stat.a
                                      ? "text-brand-darkred dark:text-red-400"
                                      : "text-gray-500"
                                  }
                                >
                                  {stat.b}
                                  {stat.isPct ? "%" : ""}
                                </span>
                              </div>
                              <div className="flex w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden gap-1 shadow-inner">
                                <div
                                  style={{ width: `${pctA}%` }}
                                  className="h-full bg-brand-primary rounded-r-full"
                                ></div>
                                <div
                                  style={{ width: `${pctB}%` }}
                                  className="h-full bg-brand-darkred rounded-l-full"
                                ></div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  </div>
                </div>
              )}

              {/* === ABA 2: ANÁLISE INDIVIDUAL === */}
              {activeTab === "individual" && (
                <div className="grid grid-cols-1 lg:grid-cols-[250px_1fr] gap-8">
                  <div className="bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 h-fit shadow-inner">
                    <h3 className="font-bold text-brand-primary dark:text-white mb-4">
                      Atletas Rastreáveis
                    </h3>
                    <div className="space-y-2">
                      {[
                        { id: 1, name: "Jogador #7", team: "A" },
                        { id: 2, name: "Jogador #10", team: "B" },
                        { id: 3, name: "Jogador #9", team: "A" },
                      ].map((player) => (
                        <button
                          key={player.id}
                          onClick={() => setSelectedPlayerId(player.id)}
                          className={`w-full flex items-center justify-between p-3 rounded-xl transition-all text-sm font-bold ${
                            selectedPlayerId === player.id
                              ? "bg-brand-darkred text-white shadow-md"
                              : "hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"
                          }`}
                        >
                          <span>{player.name}</span>
                          <span
                            className={`w-2 h-2 rounded-full ${player.team === "A" ? "bg-brand-primary" : "bg-white"}`}
                          ></span>
                        </button>
                      ))}
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm flex flex-col items-center">
                      <div className="w-full flex justify-between items-center mb-6">
                        <h3 className="font-bold text-brand-primary dark:text-white flex items-center gap-2">
                          <Flame size={20} className="text-orange-500" /> Mapa
                          de Calor
                        </h3>
                      </div>

                      <div className="relative w-full aspect-[2/3] max-w-[250px] bg-green-600 rounded-md border-2 border-white/50 overflow-hidden shadow-inner flex flex-col justify-between p-4">
                        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-1/6 border-2 border-white/40 border-t-0 rounded-b-md"></div>
                        <div className="absolute top-1/2 left-0 w-full border border-white/40"></div>
                        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-16 h-16 border-2 border-white/40 rounded-full"></div>
                        <div className="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-1/6 border-2 border-white/40 border-b-0 rounded-t-md"></div>

                        <div
                          className={`absolute blur-[20px] rounded-full mix-blend-screen opacity-80 transition-all duration-500 ${selectedPlayerId === 1 ? "top-1/4 right-4 w-24 h-40 bg-red-500" : selectedPlayerId === 2 ? "bottom-1/3 left-1/4 w-32 h-32 bg-yellow-500" : "top-1/2 left-1/2 -translate-x-1/2 w-20 h-48 bg-orange-500"}`}
                        ></div>
                      </div>
                    </div>

                    <div className="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                      <h3 className="font-bold text-brand-primary dark:text-white flex items-center gap-2 mb-6">
                        <Activity size={20} className="text-brand-darkred" />{" "}
                        Movimentação
                      </h3>

                      <div className="space-y-6">
                        <div>
                          <p className="text-sm text-gray-500 mb-1">
                            Distância Percorrida
                          </p>
                          <p className="text-3xl font-black text-gray-800 dark:text-white">
                            {selectedPlayerId === 1
                              ? "8.2"
                              : selectedPlayerId === 2
                                ? "9.4"
                                : "7.1"}{" "}
                            <span className="text-lg font-medium text-gray-400">
                              km
                            </span>
                          </p>
                        </div>
                        <div>
                          <p className="text-sm text-gray-500 mb-1">
                            Velocidade Máxima (Sprints)
                          </p>
                          <p className="text-3xl font-black text-gray-800 dark:text-white">
                            {selectedPlayerId === 1
                              ? "31.5"
                              : selectedPlayerId === 2
                                ? "33.1"
                                : "29.8"}{" "}
                            <span className="text-lg font-medium text-gray-400">
                              km/h
                            </span>
                          </p>
                        </div>

                        <div className="pt-4 border-t border-gray-100 dark:border-gray-800">
                          <p className="text-xs text-gray-400 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            Nota: Os dados táticos reais serão injetados aqui a
                            partir do arquivo CSV gerado pela Inteligência
                            Artificial.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Status: Falha */}
          {(job?.status === "failed" || jobError) && (
            <div className="flex flex-col items-center text-center animate-in fade-in duration-500 py-10">
              <div className="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-6 shadow-sm">
                <AlertTriangle size={40} />
              </div>
              <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                Erro no Processamento
              </h2>
              <p className="text-gray-500 mb-6 max-w-md">
                {job?.error ||
                  jobError ||
                  "Ocorreu um erro desconhecido ao processar o seu vídeo."}
              </p>
              <button
                onClick={() => {
                  removeFile();
                  navigate("/tracking/novo");
                }}
                className="px-6 py-3 bg-gray-200 text-gray-800 font-bold rounded-xl hover:bg-gray-300 transition shadow-sm"
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
