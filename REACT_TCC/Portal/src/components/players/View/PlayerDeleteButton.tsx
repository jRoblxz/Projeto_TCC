import React from "react";
import { Trash } from "lucide-react";

interface PlayerDeleteButtonProps {
  isAdmin: boolean;
  onDelete: () => void;
}

const PlayerDeleteButton: React.FC<PlayerDeleteButtonProps> = ({
  isAdmin,
  onDelete,
}) => {
  if (!isAdmin) return null;

  return (
    <div className="flex justify-center pb-10 mt-6">
      <button
        className="bg-[#ff6363] border-2 border-black text-white text-xl uppercase font-bold py-3 px-6 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-red-600 hover:rounded-[3px] hover:-translate-y-1 hover:rotate-1 transition-all duration-300 flex items-center gap-2"
        onClick={onDelete}
      >
        <Trash size={20} /> DELETAR JOGADOR
      </button>
    </div>
  );
};

export default PlayerDeleteButton;
