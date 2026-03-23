import React from "react";
import { Shirt } from "lucide-react";
import { FORMATIONS } from "@/utils/formations";

interface TeamCardProps {
  teamKey: string;
  team: any;
  colorClass: string;
  isAdmin: boolean;
  onFormationChange: (teamKey: string, formation: string) => void;
  onPointerDown: (e: React.PointerEvent, player: any, teamKey: string) => void;
  onMoveToBench: (player: any, teamKey: string) => void;
  onMoveFromBenchToField: (player: any, teamKey: string) => void;
  fieldRef: React.RefObject<HTMLDivElement>;
}

const TeamCard: React.FC<TeamCardProps> = ({
  teamKey,
  team,
  colorClass,
  isAdmin,
  onFormationChange,
  onPointerDown,
  onMoveToBench,
  onMoveFromBenchToField,
  fieldRef,
}) => {
  const teamPlayers = Array.isArray(team?.players) ? team.players : [];
  const fieldPlayers = teamPlayers.filter((p: any) => p?.inField);
  const benchPlayers = teamPlayers.filter((p: any) => !p?.inField);

  return (
    <div
      className={`bg-white p-5 rounded-xl shadow-lg border-t-4 ${colorClass} h-full flex flex-col`}
    >
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-xl font-bold text-[#14244D]">
          {team?.nome || `Time ${teamKey}`}
        </h3>
        <select
          value={team?.formation || "4-4-2"}
          onChange={(e) => onFormationChange(teamKey, e.target.value)}
          className="border border-gray-300 rounded px-2 py-1 text-sm bg-gray-50 outline-none"
        >
          {Object.keys(FORMATIONS).map((f) => (
            <option key={f} value={f}>
              {f}
            </option>
          ))}
        </select>
      </div>

      {/* --- CAMPO --- */}
      <div
        ref={fieldRef}
        className="relative w-full aspect-[3/4] bg-green-700 rounded-lg border-4 border-white shadow-inner mb-4 overflow-hidden select-none touch-none"
        style={{
          background:
            "linear-gradient(to bottom, #2d5016 0%, #3d6b1f 50%, #2d5016 100%)",
        }}
      >
        {/* Linhas */}
        <div className="absolute inset-0 pointer-events-none opacity-60">
          <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <rect
              x="5%"
              y="5%"
              width="90%"
              height="90%"
              fill="none"
              stroke="white"
              strokeWidth="2"
            />
            <line
              x1="5%"
              y1="50%"
              x2="95%"
              y2="50%"
              stroke="white"
              strokeWidth="2"
            />
            <circle
              cx="50%"
              cy="50%"
              r="10%"
              fill="none"
              stroke="white"
              strokeWidth="2"
            />
          </svg>
        </div>

        {/* Jogadores */}
        {fieldPlayers.map((player: any) => (
          <div
            key={player.id}
            onPointerDown={(e) => onPointerDown(e, player, teamKey)}
            className="absolute flex flex-col items-center cursor-move hover:scale-110 active:scale-110 transition-transform z-20 touch-none"
            style={{
              left: `${player.x}%`,
              top: `${player.y}%`,
              transform: "translate(-50%, -50%)",
            }}
          >
            {/* Botão para mandar pro banco rápido */}
            <div
              onClick={(e) => {
                e.stopPropagation();
                onMoveToBench(player, teamKey);
              }}
              className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] cursor-pointer hover:bg-red-700 z-30"
              title="Mandar para o banco"
            >
              x
            </div>

            <div
              className={`w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-2 border-yellow-400 ${teamKey === "A" ? "bg-blue-800" : "bg-red-800"}`}
            >
              {player.rating}
            </div>
            <div className="bg-black/60 text-white text-[10px] px-2 py-0.5 rounded mt-1 font-bold whitespace-nowrap shadow-sm pointer-events-none">
              {player.name.split(" ")[0]}
            </div>
            <div className="text-[9px] text-yellow-300 font-bold drop-shadow-md pointer-events-none">
              {player.pos}
            </div>
          </div>
        ))}
      </div>

      {/* --- BANCO --- */}
      <div className="bg-gray-100 p-3 rounded-lg border-2 border-dashed border-gray-300 min-h-[120px] flex-1">
        <div className="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-1">
          <Shirt size={14} /> Banco de Reservas ({benchPlayers.length})
        </div>
        <div className="flex flex-wrap gap-2">
          {benchPlayers.map((player: any) => (
            <div
              key={player.id}
              onClick={() => onMoveFromBenchToField(player, teamKey)}
              className="bg-white px-2 py-1.5 rounded border border-gray-200 shadow-sm text-xs flex items-center gap-2 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-colors"
              title="Clique para enviar ao campo"
            >
              <span className="font-bold text-gray-700">{player.name}</span>
              <span className="bg-yellow-100 text-yellow-800 px-1 rounded font-bold">
                {player.rating}
              </span>
            </div>
          ))}
          {benchPlayers.length === 0 && (
            <span className="text-gray-400 text-xs italic">Vazio</span>
          )}
        </div>
      </div>
    </div>
  );
};

export default TeamCard;
