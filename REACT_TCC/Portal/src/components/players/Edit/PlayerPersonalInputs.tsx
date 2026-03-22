import React from "react";
import { Lock } from "lucide-react";

type PlayerEditFormData = {
  nome_completo: string;
  altura_cm: string | number;
  peso_kg: string | number;
  pe_preferido: string;
  posicao_principal: string;
  posicao_secundaria: string;
};

interface PlayerPersonalInputsProps {
  formData: PlayerEditFormData;
  isAdmin: boolean;
  currentInputClass: string;
  onChange: (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => void;
}

// Componente que renderiza o gradil de campos de edição de dados pessoais.
const PlayerPersonalInputs: React.FC<PlayerPersonalInputsProps> = ({
  formData,
  isAdmin,
  currentInputClass,
  onChange,
}) => {
  return (
    <>
      <div className="mb-6 relative">
        <input
          type="text"
          name="nome_completo"
          disabled={!isAdmin}
          value={formData.nome_completo}
          onChange={onChange}
          className={`w-full text-center text-xl font-bold p-3 rounded shadow-md border-2 ${
            isAdmin
              ? "bg-[#851114] text-white border-transparent focus:border-white"
              : "bg-gray-300 text-gray-600 cursor-not-allowed border-gray-300"
          }`}
        />
        {!isAdmin && (
          <Lock className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4" />
        )}
      </div>

      <div className="grid grid-cols-2 gap-4 mb-5 relative">
        {!isAdmin && (
          <div
            className="absolute inset-0 z-10 cursor-not-allowed"
            title="Contate o administrador para alterar dados técnicos"
          />
        )}

        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
          <label className="text-xs text-gray-500 dark:text-gray-200 block mb-1">
            Altura (cm)
          </label>
          <input
            type="number"
            name="altura_cm"
            disabled={!isAdmin}
            value={formData.altura_cm}
            onChange={onChange}
            className={currentInputClass}
          />
        </div>

        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
          <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
            Peso (kg)
          </label>
          <input
            type="number"
            name="peso_kg"
            disabled={!isAdmin}
            value={formData.peso_kg}
            onChange={onChange}
            className={currentInputClass}
          />
        </div>

        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
          <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
            Pé
          </label>
          <select
            name="pe_preferido"
            disabled={!isAdmin}
            value={formData.pe_preferido}
            onChange={onChange}
            className={currentInputClass}
          >
            <option value="Direito">Direito</option>
            <option value="Esquerdo">Esquerdo</option>
            <option value="Ambos">Ambos</option>
          </select>
        </div>

        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
          <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
            Pos. Principal
          </label>
          <select
            name="posicao_principal"
            disabled={!isAdmin}
            value={formData.posicao_principal}
            onChange={onChange}
            className={`${currentInputClass} ${isAdmin ? "text-[#ff4757] font-bold" : ""}`}
          >
            <option value="Goleiro">Goleiro</option>
            <option value="Zagueiro Direito">Zagueiro Direito</option>
            <option value="Zagueiro Esquerdo">Zagueiro Esquerdo</option>
            <option value="Lateral Direito">Lateral Direito</option>
            <option value="Lateral Esquerdo">Lateral Esquerdo</option>
            <option value="Volante">Volante</option>
            <option value="Meia">Meia</option>
            <option value="Ponta Direita">Ponta Direita</option>
            <option value="Ponta Esquerda">Ponta Esquerda</option>
            <option value="Atacante">Atacante</option>
          </select>
        </div>

        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center col-span-2">
          <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
            Pos. Secundária
          </label>
          <select
            name="posicao_secundaria"
            disabled={!isAdmin}
            value={formData.posicao_secundaria}
            onChange={onChange}
            className={`${currentInputClass} ${isAdmin ? "text-[#4787ff] font-bold" : ""}`}
          >
            <option value="">Nenhuma</option>
            <option value="Goleiro">Goleiro</option>
            <option value="Zagueiro Direito">Zagueiro Direito</option>
            <option value="Zagueiro Esquerdo">Zagueiro Esquerdo</option>
            <option value="Lateral Direito">Lateral Direito</option>
            <option value="Lateral Esquerdo">Lateral Esquerdo</option>
            <option value="Volante">Volante</option>
            <option value="Meia">Meia</option>
            <option value="Ponta Direita">Ponta Direita</option>
            <option value="Ponta Esquerda">Ponta Esquerda</option>
            <option value="Atacante">Atacante</option>
          </select>
        </div>
      </div>
    </>
  );
};

export default PlayerPersonalInputs;
