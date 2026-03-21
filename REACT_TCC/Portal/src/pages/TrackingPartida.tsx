import React, { useState, useRef } from "react";
import { useNavigate } from "react-router-dom";
import Layout from "@/components/layouts/Layout";
import { ArrowLeft, UploadCloud, FileVideo, X, PlayCircle } from "lucide-react";
import toast from "react-hot-toast";

const TrackingPartida: React.FC = () => {
  const navigate = useNavigate();
  const [videoFile, setVideoFile] = useState<File | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Manipuladores de Drag & Drop
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
      const file = e.dataTransfer.files[0];
      validateAndSetFile(file);
    }
  };

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      validateAndSetFile(e.target.files[0]);
    }
  };

  const validateAndSetFile = (file: File) => {
    // Valida se é um arquivo de vídeo
    if (file.type.startsWith("video/")) {
      setVideoFile(file);
    } else {
      toast.error("Por favor, selecione apenas arquivos de vídeo (MP4, AVI, etc).");
    }
  };

  const removeFile = () => {
    setVideoFile(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = ""; // Reseta o input oculto
    }
  };

  const handleContinue = () => {
    if (!videoFile) {
      toast.error("Selecione um vídeo primeiro!");
      return;
    }
    
    // Aqui entrará a lógica futura de envio para o Laravel/Python
    toast.success("Vídeo pronto para análise! (Integração futura)");
    console.log("Arquivo selecionado para tracking:", videoFile);
  };

  // Converte bytes para MB
  const formatFileSize = (bytes: number) => {
    return (bytes / (1024 * 1024)).toFixed(2) + " MB";
  };

  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8">
        
        {/* HEADER DA PÁGINA */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate(-1)}
              className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
            >
              <ArrowLeft className="text-gray-700 dark:text-gray-300" />
            </button>
            <div>
              <h1 className="text-2xl font-bold text-[#14244D] dark:text-white">
                Tracking de Partida
              </h1>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Importe um vídeo para análise tática e rastreamento de jogadores.
              </p>
            </div>
          </div>
        </div>

        {/* ÁREA DE UPLOAD */}
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-8 md:p-12 flex flex-col items-center justify-center min-h-[400px]">
          
          {!videoFile ? (
            <div
              className={`w-full max-w-2xl border-4 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center transition-all duration-300 ease-in-out cursor-pointer
                ${isDragging 
                  ? "border-[#8B0000] bg-[#8B0000]/5 scale-105" 
                  : "border-gray-300 hover:border-[#14244D] hover:bg-gray-50 dark:hover:bg-gray-800"
                }`}
              onDragOver={handleDragOver}
              onDragLeave={handleDragLeave}
              onDrop={handleDrop}
              onClick={() => fileInputRef.current?.click()}
            >
              <div className="w-20 h-20 bg-[#14244D]/10 text-[#14244D] rounded-full flex items-center justify-center mb-6">
                <UploadCloud size={40} />
              </div>
              <h3 className="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2 text-center">
                Arraste e solte o vídeo aqui
              </h3>
              <p className="text-gray-500 dark:text-gray-400 text-center mb-6">
                ou clique para procurar nos seus arquivos (MP4, MOV, AVI)
              </p>
              
              <button className="px-6 py-2.5 bg-[#14244D] text-white font-bold rounded-lg hover:bg-[#1e3a8a] transition shadow-md">
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
            // PREVIEW DO ARQUIVO SELECIONADO
            <div className="w-full max-w-2xl bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-8 flex flex-col items-center relative animate-in fade-in zoom-in duration-300">
              <button 
                onClick={removeFile}
                className="absolute top-4 right-4 p-2 bg-white dark:bg-gray-700 rounded-full shadow-sm text-gray-500 hover:text-red-500 transition"
                title="Remover vídeo"
              >
                <X size={20} />
              </button>

              <div className="w-24 h-24 bg-[#8B0000]/10 text-[#8B0000] rounded-full flex items-center justify-center mb-4">
                <FileVideo size={48} />
              </div>
              
              <h3 className="text-xl font-bold text-gray-800 dark:text-white text-center break-all mb-1">
                {videoFile.name}
              </h3>
              <p className="text-gray-500 font-medium mb-8">
                Tamanho: {formatFileSize(videoFile.size)}
              </p>

              <div className="flex gap-4 w-full justify-center">
                <button 
                  onClick={removeFile}
                  className="px-6 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition"
                >
                  Trocar Arquivo
                </button>
                <button 
                  onClick={handleContinue}
                  className="px-8 py-3 bg-[#8B0000] text-white font-bold rounded-xl hover:bg-[#a01519] transition shadow-lg flex items-center gap-2"
                >
                  <PlayCircle size={20} />
                  Continuar Tracking
                </button>
              </div>
            </div>
          )}

        </div>
      </div>
    </Layout>
  );
};

export default TrackingPartida;