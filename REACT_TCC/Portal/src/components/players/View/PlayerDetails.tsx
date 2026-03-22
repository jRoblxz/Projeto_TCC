import React from "react";
import { getAttributesByPosition } from "@/utils/playerAttributes";

interface PlayerDetailsProps {
  player: any;
  canViewDetails: boolean;
  getDisplayRating: () => string;
}

const PlayerDetails: React.FC<PlayerDetailsProps> = ({
  player,
  canViewDetails,
  getDisplayRating,
}) => {
  return (
    <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] flex flex-col gap-6">
      {/* OVERALL SCORE */}
      <div className="bg-[#851114] text-white p-5 rounded-[15px] text-center shadow-lg">
        <h3 className="text-2xl mb-2">Overall Score</h3>
        <div className="text-5xl font-bold drop-shadow-md">
          {getDisplayRating()}
        </div>
      </div>

      <div className="bg-white dark:bg-gray-800 p-5 rounded-[10px] border-l-[5px] border-[#14244D] shadow-sm">
        {canViewDetails && (
          <>
            <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4">
              Informações e Notas
            </h4>
            <div className="text-[#666] dark:text-gray-300 leading-relaxed space-y-2 text-sm md:text-base">
              <p>
                <strong>Data de Nascimento:</strong>{" "}
                {new Date(player.pessoa?.data_nascimento).toLocaleDateString()}
              </p>
              <p>
                <strong>Email:</strong> {player.pessoa?.email || "N/A"}
              </p>
              <p>
                <strong>CPF:</strong> {player.pessoa?.cpf || "N/A"}
              </p>
              <p>
                <strong>Telefone:</strong> {player.pessoa?.telefone || "N/A"}
              </p>
              <p>
                <strong>RG:</strong> {player.pessoa?.rg || "N/A"}
              </p>
              <p>
                <strong>Cirurgia:</strong>{" "}
                {player.historico_lesoes_cirurgias || "N/A"}
              </p>
              <p>
                <strong>Video Skills:</strong>{" "}
                {player.video_apresentacao_url ? (
                  <a
                    href={player.video_apresentacao_url}
                    target="_blank"
                    rel="noreferrer"
                    className="text-blue-600 underline hover:text-blue-800"
                  >
                    Link do Vídeo
                  </a>
                ) : (
                  "N/A"
                )}
              </p>
            </div>
          </>
        )}

        <div className="mt-6 pt-4 border-t border-gray-100">
          <h4 className="text-[#333] dark:text-white text-lg font-bold mb-2">
            Avaliação Recente
          </h4>
          <p className="text-[#666] dark:text-gray-300 italic dark:bg-gray-700 dark:border-gray-300 bg-gray-50 p-3 rounded">
            "{player.ultima_avaliacao?.observacoes ?? "Nenhuma observação."}"
          </p>
        </div>

        {canViewDetails && (
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4 mt-5">
            {getAttributesByPosition(player.posicao_principal).map((attr) => (
              <div
                key={attr.key}
                className="bg-white dark:bg-gray-800 p-4 rounded-[10px] text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 hover:border-[#851114] transition-all"
              >
                <h5 className="text-[#333] dark:text-white mb-2 font-bold text-sm lg:text-base">
                  {attr.label}
                </h5>
                <div className="text-xl lg:text-2xl text-[#851114] font-bold">
                  {player.ultima_avaliacao?.[attr.key] !== undefined &&
                  player.ultima_avaliacao?.[attr.key] !== null
                    ? Number(player.ultima_avaliacao[attr.key]).toFixed(1)
                    : "N/A"}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* O gráfico de radar foi movido para PlayerStats */}
    </div>
  );
};

export default PlayerDetails;
