import React from "react";
import { Loader2, Check, X } from "lucide-react";

interface PlayerEditActionsProps {
  saving: boolean;
  onCancel: () => void;
}

// Componente de botões salvar/cancelar para evitar duplicação e manter o principal limpo.
const PlayerEditActions: React.FC<PlayerEditActionsProps> = ({
  saving,
  onCancel,
}) => {
  return (
    <div className="flex justify-center gap-4 pb-10 mt-4">
      <button
        type="submit"
        disabled={saving}
        className="bg-[#479440] border-2 border-black text-white text-xl uppercase font-bold py-3 px-8 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-[#48ff00] hover:rounded-[3px] hover:-translate-y-1 hover:rotate-1 transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {saving ? <Loader2 className="animate-spin" /> : <Check />}
        {saving ? "Salvando..." : "Salvar Foto"}
      </button>

      <button
        type="button"
        onClick={onCancel}
        className="bg-[#bb4838] border-2 border-black text-white text-xl uppercase font-bold py-3 px-8 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-red-600 hover:rounded-[3px] hover:-translate-y-1 hover:-rotate-1 transition-all duration-300 flex items-center gap-2"
      >
        <X /> Cancelar
      </button>
    </div>
  );
};

export default PlayerEditActions;
