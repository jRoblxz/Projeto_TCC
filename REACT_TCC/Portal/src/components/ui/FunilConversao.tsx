import React from "react";
import { Filter } from "lucide-react";

interface FunilItem {
  etapa: string;
  valor: number;
}

const FunilConversao = ({ dadosFunil }: { dadosFunil: FunilItem[] }) => {
  const maxValor =
    dadosFunil && dadosFunil.length > 0 ? dadosFunil[0].valor : 1;

  // As cores ficam aqui no React para o Tailwind não apagá-las!
  const colorStyles = [
    "bg-blue-100 text-blue-800 border-blue-200", // Inscritos
    "bg-purple-100 text-purple-800 border-purple-200", // Avaliados
    "bg-green-100 text-green-800 border-green-200", // Aprovados
  ];

  return (
    <div className="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
      <div className="flex items-center gap-2 mb-6">
        <div className="p-2 bg-brand-darkred/10 rounded-lg text-brand-darkred">
          <Filter size={20} />
        </div>
        <h3 className="text-lg font-bold text-brand-primary dark:text-white">
          Funil de Captação
        </h3>
      </div>

      <div className="space-y-4 flex flex-col items-center overflow-x-auto pb-2">
        {dadosFunil?.map((item, index) => {
          // Aumentei o mínimo para 30% para não sumir no mobile
          const larguraPercentual = Math.max((item.valor / maxValor) * 100, 30);
          const conversao =
            index === 0
              ? 100
              : ((item.valor / dadosFunil[index - 1].valor) * 100).toFixed(1);
          const currentStyle =
            colorStyles[index] || "bg-gray-100 text-gray-800 border-gray-200";

          return (
            <div
              key={index}
              className="w-full flex flex-col items-center animate-in fade-in zoom-in duration-500"
            >
              {index > 0 && (
                <div className="h-6 border-l-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center relative">
                  <span className="absolute left-4 text-xs font-bold text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded-full border border-gray-100 dark:border-gray-800 shadow-sm whitespace-nowrap">
                    {conversao}% converteram
                  </span>
                </div>
              )}

              {/* Adicionado: min-w-fit, whitespace-nowrap e gap-4 para não quebrar no mobile */}
              <div
                className={`relative flex items-center justify-between p-3 rounded-xl border-2 transition-all hover:scale-105 cursor-default min-w-fit whitespace-nowrap gap-4 ${currentStyle}`}
                style={{ width: `${larguraPercentual}%` }}
              >
                <span className="font-bold text-sm truncate pr-2">
                  {item.etapa}
                </span>
                <span className="font-black text-lg bg-white/50 px-2 py-0.5 rounded-md shadow-sm">
                  {item.valor}
                </span>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};

export default FunilConversao;
