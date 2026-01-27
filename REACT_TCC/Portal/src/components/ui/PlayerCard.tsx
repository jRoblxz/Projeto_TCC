import React, { useState } from "react";
import { Edit, Trash, Eye } from "lucide-react";
import clsx from "clsx";
import DefaultImage from "../../assets/img/default.png";

interface PlayerCardProps {
  player: any;
  isAdmin: boolean;
  onEditRating: (player: any) => void;
  onDelete: (player: any) => void;
  onViewMore: (id: number) => void;
}

const PlayerCard: React.FC<PlayerCardProps> = ({ 
  player, isAdmin, onEditRating, onDelete, onViewMore 
}) => {
  const [isFlipped, setIsFlipped] = useState(false);

  return (
    <div 
      className="group relative w-[280px] h-[380px] cursor-pointer mx-auto mb-8 transition-transform duration-300 hover:-translate-y-3"
      style={{ perspective: "1000px", WebkitPerspective: "1000px" }} // [1] Perspective Inline
      onClick={() => setIsFlipped(!isFlipped)}
    >
      <div 
        className={clsx(
          "relative w-full h-full transition-transform duration-700 shadow-[8px_8px_0px_1px_#14244D]",
          isFlipped ? "[transform:rotateY(180deg)]" : ""
        )}
        style={{ 
            transformStyle: "preserve-3d", 
            WebkitTransformStyle: "preserve-3d" // [2] TransformStyle Habilitado pro Safari
        }}
      >
        {/* === FRENTE DO CARD === */}
        <div 
            className="absolute w-full h-full bg-white overflow-hidden border-[10px] border-[#8B0000] flex flex-col"
            style={{ 
                backfaceVisibility: "hidden", 
                WebkitBackfaceVisibility: "hidden", // [3] O SEGREDO DO SAFARI
                transform: "rotateY(0deg)",         // Força o eixo 0
                zIndex: 2                           // Garante empilhamento
            }}
        >
          
          {/* Badge de Rating */}
          <div className="absolute top-0 left-0 bg-[#8B0000] text-white font-bold text-5xl w-20 h-14 flex items-center justify-center rounded-br-2xl z-10 shadow-md">
            {Number(player.rating_medio || 0).toFixed(1)}
          </div>

          {/* Badge de Posição */}
          <div className="absolute bottom-8 right-0 bg-[#8B0000] text-white px-3 py-1 text-xl font-bold rounded-l-lg z-10 shadow-md">
            {player.posicao_principal || "N/A"}
          </div>

          {/* Foto do Jogador */}
          <div className="w-full h-full relative bg-gray-200">
            <img 
              src={player.pessoa?.foto_url_completa || "/img/avatar_padrao.png"} 
              alt={player.pessoa?.nome_completo}
              className="w-full h-full object-cover"
              onError={(e) => {
                  e.currentTarget.src = DefaultImage;
              }}
            />
            {/* Overlay sutil */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none" />
          </div>

          {/* Ações de Admin */}
          {isAdmin && (
            <div 
              className="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20"
              onClick={(e) => e.stopPropagation()} 
            >
              <button 
                onClick={(e) => { e.stopPropagation(); onEditRating(player); }}
                className="w-9 h-9 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 hover:scale-110 transition shadow-lg"
              >
                <Edit size={16} />
              </button>
              <button 
                onClick={(e) => { e.stopPropagation(); onDelete(player); }}
                className="w-9 h-9 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 hover:scale-110 transition shadow-lg"
              >
                <Trash size={16} />
              </button>
            </div>
          )}
        </div>

        {/* === VERSO DO CARD === */}
        <div 
            className="absolute w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 border-4 border-[#8B0000] p-5 flex flex-col justify-between text-[#14244D]"
            style={{ 
                backfaceVisibility: "hidden", 
                WebkitBackfaceVisibility: "hidden", // [3] O SEGREDO DO SAFARI
                transform: "rotateY(180deg)",       // Já começa virado
                zIndex: 1
            }}
        >
            
          {/* Nome no Topo */}
          <div className="bg-[#8B0000] text-white -mx-5 -mt-5 p-4 text-center font-bold text-xl mb-2 shadow-sm">
             {player.pessoa?.nome_completo}
          </div>

          {/* Informações */}
          <div className="space-y-3 flex-1 overflow-y-auto pr-1 text-sm font-semibold">
            <div className="flex justify-between border-b border-[#8B0000]/30 pb-1">
              <span className="text-[#8B0000]">Divisão:</span>
              <span>{player.pessoa?.sub_divisao || "N/A"}</span>
            </div>
            <div className="flex justify-between border-b border-[#8B0000]/30 pb-1">
              <span className="text-[#8B0000]">Altura:</span>
              <span>{player.altura_cm ? `${player.altura_cm} cm` : "-"}</span>
            </div>
            <div className="flex justify-between border-b border-[#8B0000]/30 pb-1">
              <span className="text-[#8B0000]">Peso:</span>
              <span>{player.peso_kg ? `${player.peso_kg} kg` : "-"}</span>
            </div>
            <div className="flex justify-between border-b border-[#8B0000]/30 pb-1">
              <span className="text-[#8B0000]">Pé:</span>
              <span>{player.pe_preferido || "-"}</span>
            </div>

            {/* Última Avaliação */}
            <div className="mt-4">
              <p className="text-[#8B0000] underline mb-1">Avaliação Recente:</p>
              <p className="text-xs text-gray-600 italic leading-relaxed">
                "{player.ultima_avaliacao?.observacoes || "Nenhuma observação registrada."}"
              </p>
            </div>
          </div>

          {/* Botão Ver Mais */}
          <button 
            onClick={(e) => { e.stopPropagation(); onViewMore(player.id); }}
            className="w-full mt-4 bg-[#14244D] text-white py-2 rounded-lg font-bold hover:bg-[#1e3a8a] transition shadow-md flex items-center justify-center gap-2"
          >
            Ver Detalhes <Eye size={16}/>
          </button>
        </div>
      </div>
    </div>
  );
};

export default PlayerCard;