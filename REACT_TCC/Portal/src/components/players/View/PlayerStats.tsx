import React from "react";
import { Edit } from "lucide-react";
import { getFieldCoordinates } from "@/utils/soccerFieldLogic";
import PlayerRadarChart from "@/components/ui/PlayerRadarChart";
import { getAttributesByPosition } from "@/utils/playerAttributes";

interface PlayerStatsProps {
  player: any;
  canViewDetails: boolean;
  onEdit: () => void;
  calculateAge: (birthDate: string) => string;
  pulseAnimation: string;
}

const PlayerStats: React.FC<PlayerStatsProps> = ({
  player,
  canViewDetails,
  onEdit,
  calculateAge,
  pulseAnimation,
}) => {
  const primCoords = getFieldCoordinates(player.posicao_principal);
  const secCoords = getFieldCoordinates(player.posicao_secundaria);

  return (
    <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] relative">
      {canViewDetails && (
        <button
          className="absolute top-4 right-4 w-10 h-10 rounded-full bg-[#141414] text-white flex items-center justify-center shadow-lg hover:w-[120px] hover:bg-[#ff4545] transition-all duration-300 group overflow-hidden z-10"
          onClick={onEdit}
        >
          <Edit className="w-4 h-4 group-hover:rotate-[360deg] transition-transform duration-300 shrink-0" />
          <span className="hidden group-hover:block ml-2 text-sm font-bold whitespace-nowrap">
            Editar
          </span>
        </button>
      )}

      <div className="w-[150px] h-[150px] rounded-full mx-auto mb-5 relative overflow-hidden bg-black/70 border-4 border-white shadow-sm">
        <img
          src={player.pessoa?.foto_url_completa || "/img/avatar_padrao.png"}
          alt="Foto"
          className="w-full h-full object-cover object-top"
          onError={(e) => {
            e.currentTarget.src =
              "https://cdn-icons-png.flaticon.com/512/149/149071.png";
          }}
        />
      </div>

      <div className="bg-[#851114] text-white p-4 text-center mb-5 text-2xl font-bold shadow-md rounded-sm">
        {player.pessoa?.nome_completo || "Nome Indisponível"}
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-2 gap-4 mb-5">
        {[
          {
            label: "Idade",
            value: calculateAge(player.pessoa?.data_nascimento),
          },
          {
            label: "Altura",
            value: player.altura_cm ? `${player.altura_cm} cm` : "N/A",
          },
          { label: "Pé", value: player.pe_preferido || "N/A" },
          {
            label: "Peso",
            value: player.peso_kg ? `${player.peso_kg} kg` : "N/A",
          },
        ].map((stat, i) => (
          <div
            key={i}
            className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg text-center shadow-sm border-2 border-[#e9ecef] hover:-translate-y-0.5 hover:shadow-md hover:border-[#851114] transition-all"
          >
            <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">
              {stat.label}
            </div>
            <div className="text-lg font-bold text-[#333] dark:text-white">
              {stat.value}
            </div>
          </div>
        ))}

        <div className="bg-white dark:bg-gray-800 p-3 rounded-lg text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 transition-all">
          <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">
            Posição Principal
          </div>
          <div className="text-lg font-bold text-[#ff4757]">
            {player.posicao_principal || "N/A"}
          </div>
        </div>
        <div className="bg-white dark:bg-gray-800 p-3 rounded-lg text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 transition-all">
          <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">
            Posição Secundária
          </div>
          <div className="text-lg font-bold text-[#4787ff]">
            {player.posicao_secundaria || "-"}
          </div>
        </div>
      </div>

      {/* Campo de Futebol */}
      <div className="w-full h-[200px] bg-[#28a745] rounded-[10px] relative mb-5 shadow-inner overflow-hidden border-2 border-[#1e7e34]">
        <div className="absolute top-0 left-0 w-full h-full border-[3px] border-white/90 rounded-[10px]"></div>
        <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/90 -translate-y-1/2"></div>
        <div className="absolute top-1/2 left-1/2 w-[80px] h-[80px] border-[2px] border-white/90 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        {primCoords && (
          <div
            className={`absolute w-5 h-5 bg-[#ff4757] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 ${pulseAnimation}`}
            style={{
              top: `${primCoords.top}%`,
              left: `${primCoords.left}%`,
            }}
            title="Posição Principal"
          ></div>
        )}
        {secCoords && (
          <div
            className={`absolute w-5 h-5 bg-[#4787ff] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 ${pulseAnimation}`}
            style={{
              top: `${secCoords.top}%`,
              left: `${secCoords.left}%`,
            }}
            title="Posição Secundária"
          ></div>
        )}
      </div>

      {/* Gráfico Radar — agora à esquerda, depois do campo */}
      {canViewDetails && (
        <div className="mt-4">
          <PlayerRadarChart
            avaliacao={player.ultima_avaliacao}
            posicao={player.posicao_principal}
          />
        </div>
      )}

      {/* Atributos Dinâmicos movidos para PlayerDetails (lugar original) */}
    </div>
  );
};

export default PlayerStats;
