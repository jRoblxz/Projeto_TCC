import React from "react";
import {
  Radar,
  RadarChart,
  PolarGrid,
  PolarAngleAxis,
  PolarRadiusAxis,
  ResponsiveContainer,
  Tooltip,
} from "recharts";
import { getAttributesByPosition } from "@/utils/playerAttributes";

interface PlayerRadarChartProps {
  avaliacao: any;
  posicao: string; // <-- O TS estava cobrando isso!
}

const PlayerRadarChart: React.FC<PlayerRadarChartProps> = ({
  avaliacao,
  posicao,
}) => {
  // Puxa as 6 categorias corretas da posição (ex: Reflexo se for goleiro)
  const atributosPosicao = getAttributesByPosition(posicao);

  const data = atributosPosicao.map((attr) => ({
    atributo: attr.label,
    nota: Number(avaliacao?.[attr.key] || 0),
    max: 10,
  }));

  const CustomTooltip = ({ active, payload }: any) => {
    if (active && payload && payload.length) {
      return (
        <div className="bg-white dark:bg-gray-800 p-3 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
          <p className="font-bold text-brand-primary dark:text-white">
            {payload[0].payload.atributo}
          </p>
          <p className="text-brand-darkred font-extrabold text-lg">
            Nota: {payload[0].value.toFixed(1)}
          </p>
        </div>
      );
    }
    return null;
  };

  return (
    <div className="w-full h-[300px] sm:h-[350px] bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center">
      <h3 className="text-lg font-bold text-brand-primary dark:text-white mb-2 self-start">
        Análise de Atributos
      </h3>

      <ResponsiveContainer width="100%" height="100%">
        <RadarChart cx="50%" cy="50%" outerRadius="70%" data={data}>
          <PolarGrid stroke="#e5e7eb" />
          <PolarAngleAxis
            dataKey="atributo"
            tick={{ fill: "#6b7280", fontSize: 12, fontWeight: "bold" }}
          />
          <PolarRadiusAxis
            angle={30}
            domain={[0, 10]}
            tick={{ fill: "#9ca3af", fontSize: 10 }}
            tickCount={6}
          />
          <Tooltip content={<CustomTooltip />} />
          <Radar
            name="Jogador"
            dataKey="nota"
            stroke="#8B0000"
            fill="#8B0000"
            fillOpacity={0.5}
            dot={{ r: 4, fill: "#14244D" }}
            activeDot={{ r: 6, fill: "#14244D" }}
          />
        </RadarChart>
      </ResponsiveContainer>
    </div>
  );
};

export default PlayerRadarChart;
