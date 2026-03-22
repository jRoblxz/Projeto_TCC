import React from "react";
import { getFieldCoordinates } from "@/utils/soccerFieldLogic";

interface PlayerFieldPreviewProps {
  principal: string;
  secundario: string;
}

// Componente que desenha posição principal e secundária em um pequeno campo visual.
const PlayerFieldPreview: React.FC<PlayerFieldPreviewProps> = ({
  principal,
  secundario,
}) => {
  const primCoords = getFieldCoordinates(principal);
  const secCoords = getFieldCoordinates(secundario);

  return (
    <div className="w-full h-[200px] bg-[#28a745] rounded-[10px] relative mt-4 shadow-inner overflow-hidden border-2 border-[#1e7e34] opacity-90">
      <div className="absolute top-0 left-0 w-full h-full border-[3px] border-white/90 rounded-[10px]"></div>
      <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/90 -translate-y-1/2"></div>
      <div className="absolute top-1/2 left-1/2 w-[80px] h-[80px] border-[2px] border-white/90 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

      {primCoords && (
        <div
          className="absolute w-5 h-5 bg-[#ff4757] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10"
          style={{ top: `${primCoords.top}%`, left: `${primCoords.left}%` }}
          title="Posição Principal"
        />
      )}

      {secCoords && (
        <div
          className="absolute w-5 h-5 bg-[#4787ff] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 animate-pulse"
          style={{ top: `${secCoords.top}%`, left: `${secCoords.left}%` }}
          title="Posição Secundária"
        />
      )}
    </div>
  );
};

export default PlayerFieldPreview;
