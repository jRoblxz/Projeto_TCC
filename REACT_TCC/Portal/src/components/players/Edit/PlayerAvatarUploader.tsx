import React, { ChangeEvent } from "react";
import { Upload } from "lucide-react";

interface PlayerAvatarUploaderProps {
  previewImage: string;
  onImageChange: (e: ChangeEvent<HTMLInputElement>) => void;
}

// Componente de upload de imagem do jogador (independente de ser admin ou não).
const PlayerAvatarUploader: React.FC<PlayerAvatarUploaderProps> = ({
  previewImage,
  onImageChange,
}) => {
  return (
    <div className="group relative w-[150px] h-[150px] rounded-full mx-auto mb-5 overflow-hidden bg-black/70 border-4 border-dashed border-[#851114]/50 hover:border-[#851114] transition-all cursor-pointer">
      <img
        src={previewImage || "/img/avatar_padrao.png"}
        alt="avatar"
        className="w-full h-full object-cover object-top opacity-100 group-hover:opacity-50 transition-opacity"
        onError={(e) => {
          e.currentTarget.src =
            "https://cdn-icons-png.flaticon.com/512/149/149071.png";
        }}
      />
      <div className="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white font-bold bg-black/40">
        <Upload size={24} className="mb-1" />
        <span className="text-xs">Alterar Foto</span>
      </div>
      <input
        type="file"
        name="image"
        accept="image/*"
        onChange={onImageChange}
        className="absolute inset-0 opacity-0 cursor-pointer"
      />
    </div>
  );
};

export default PlayerAvatarUploader;
