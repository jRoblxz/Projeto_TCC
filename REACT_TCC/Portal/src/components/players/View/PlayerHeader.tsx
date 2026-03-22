import React from "react";
import { ArrowLeft } from "lucide-react";

interface PlayerHeaderProps {
  onBack: () => void;
}

const PlayerHeader: React.FC<PlayerHeaderProps> = ({ onBack }) => {
  return (
    <div className="bg-[#14244D] dark:bg-gray-900 p-8 text-white text-center mb-5 rounded-none md:rounded-t-lg relative shadow-md">
      <button
        onClick={onBack}
        className="absolute left-5 top-1/2 -translate-y-1/2 text-white/80 hover:text-white hover:scale-110 transition-transform"
      >
        <ArrowLeft size={32} />
      </button>
      <h1 className="text-4xl mb-2 drop-shadow-md">Perfil do Jogador</h1>
      <p className="opacity-90 text-lg">Sistema de Avaliação de Atletas</p>
    </div>
  );
};

export default PlayerHeader;
