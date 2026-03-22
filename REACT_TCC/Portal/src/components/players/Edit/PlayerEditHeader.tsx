import React from "react";

interface PlayerEditHeaderProps {
  isAdmin: boolean;
}

// Componente responsável apenas pelo cabeçalho da página de edição de jogador.
const PlayerEditHeader: React.FC<PlayerEditHeaderProps> = ({ isAdmin }) => {
  return (
    <div className="bg-[#14244D] dark:bg-gray-900 p-8 text-white text-center mb-5 rounded-lg shadow-md">
      <h1 className="text-4xl mb-2 drop-shadow-md">
        {isAdmin ? "Editar Jogador" : "Meu Perfil"}
      </h1>
      <p className="opacity-90 text-lg">
        {isAdmin
          ? "Sistema de Avaliação de Atletas"
          : "Mantenha sua foto atualizada"}
      </p>
    </div>
  );
};

export default PlayerEditHeader;
