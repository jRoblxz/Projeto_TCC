import React from "react";

interface PlayerEditInfoCardProps {
  isAdmin: boolean;
}

// Componente com instruções textuais + chamada para ação dentro da coluna direita.
const PlayerEditInfoCard: React.FC<PlayerEditInfoCardProps> = ({ isAdmin }) => {
  return (
    <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] h-fit">
      <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-5 rounded-[10px] border-l-[5px] border-brand-primary shadow-sm mb-6">
        <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4 ">
          {isAdmin ? "Atenção" : "Meus Dados"}
        </h4>
        <p className="text-[#666] dark:text-gray-300 leading-relaxed text-sm">
          {isAdmin
            ? "Certifique-se de salvar as alterações."
            : "Você pode atualizar sua foto de perfil aqui. Para alterar dados técnicos (altura, peso, posição), fale com seu treinador."}
        </p>
      </div>

      <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-5 rounded-[10px] border-l-[5px] border-brand-primary shadow-sm">
        <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4 ">
          Informações
        </h4>
        <p className="text-[#666] dark:text-gray-300 leading-relaxed text-sm">
          Use este painel para confirmar se os campos do lado esquerdo estão
          coerentes.
        </p>
      </div>
    </div>
  );
};

export default PlayerEditInfoCard;
