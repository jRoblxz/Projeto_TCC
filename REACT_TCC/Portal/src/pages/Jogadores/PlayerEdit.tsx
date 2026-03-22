import React, { useEffect, useState, ChangeEvent, FormEvent } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../../config/api";
import Layout from "@/components/layouts/Layout";
import toast from "react-hot-toast";
import { isUserAdmin } from "../../utils/auth";
import PlayerEditHeader from "@/components/players/Edit/PlayerEditHeader";
import PlayerAvatarUploader from "@/components/players/Edit/PlayerAvatarUploader";
import PlayerPersonalInputs from "@/components/players/Edit/PlayerPersonalInputs";
import PlayerFieldPreview from "@/components/players/Edit/PlayerFieldPreview";
import PlayerEditInfoCard from "@/components/players/Edit/PlayerEditInfoCard";
import PlayerEditActions from "@/components/players/Edit/PlayerEditActions";
import { getFieldCoordinates } from "../../utils/soccerFieldLogic";
import { Loader2 } from "lucide-react";

type PlayerEditFormData = {
  nome_completo: string;
  altura_cm: string | number;
  peso_kg: string | number;
  pe_preferido: string;
  posicao_principal: string;
  posicao_secundaria: string;
};

const PlayerEdit: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();

  // Estado de carregamento inicial.
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false);

  // Estados de imagem.
  const [previewImage, setPreviewImage] = useState<string>("");
  const [selectedFile, setSelectedFile] = useState<File | null>(null);

  const [formData, setFormData] = useState<PlayerEditFormData>({
    nome_completo: "",
    altura_cm: "",
    peso_kg: "",
    pe_preferido: "Direito",
    posicao_principal: "Goleiro",
    posicao_secundaria: "",
  });

  useEffect(() => {
    // Checa o perfil e carrega os dados do jogador.
    setIsAdmin(isUserAdmin());

    const fetchPlayer = async () => {
      try {
        const response = await api.get(`/players/${id}`);
        const data = response.data.data || response.data;

        setFormData({
          nome_completo: data.pessoa.nome_completo,
          altura_cm: data.altura_cm,
          peso_kg: data.peso_kg,
          pe_preferido: data.pe_preferido,
          posicao_principal: data.posicao_principal,
          posicao_secundaria: data.posicao_secundaria || "",
        });

        setPreviewImage(data.pessoa.foto_url_completa);
      } catch (error) {
        toast.error("Erro ao carregar dados.");
        navigate(-1);
      } finally {
        setLoading(false);
      }
    };

    fetchPlayer();
  }, [id, navigate]);

  // Atualiza o estado dos inputs quando usuário admin faz modificações.
  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    if (!isAdmin) return;
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Lê o arquivo de imagem e gera preview temporário.
  const handleImageChange = (e: ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0];
      setSelectedFile(file);
      setPreviewImage(URL.createObjectURL(file));
    }
  };

  // Envia formData e imagem para a API.
  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setSaving(true);
    const toastId = toast.loading("Salvando...");

    try {
      if (isAdmin) {
        await api.put(`/players/${id}`, formData);
      }

      if (selectedFile) {
        const imageFormData = new FormData();
        imageFormData.append("image", selectedFile);
        await api.post(`/players/${id}/upload-photo`, imageFormData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
      }

      toast.success("Perfil atualizado!");

      if (!isAdmin) {
        window.location.reload();
      } else {
        navigate(`/jogadores/${id}`);
      }
    } catch (error) {
      console.error(error);
      toast.error("Erro ao salvar.");
    } finally {
      setSaving(false);
      toast.dismiss(toastId);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center mt-20">
        <Loader2 className="animate-spin h-10 w-10 text-[#8B0000]" />
      </div>
    );
  }

  const primCoords = getFieldCoordinates(formData.posicao_principal);
  const secCoords = getFieldCoordinates(formData.posicao_secundaria);

  const baseInputClass =
    "w-full border-2 rounded text-center py-1 px-2 transition-colors focus:outline-none";
  const editableClass =
    "bg-[rgba(133,17,20,0.05)] border-dashed border-[rgba(133,17,20,0.3)] text-[#333] dark:text-white/60 focus:border-[#851114] hover:bg-[rgba(190,18,23,0.1)]";
  const disabledClass =
    "bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-500 cursor-not-allowed opacity-70";

  const currentInputClass = `${baseInputClass} ${isAdmin ? editableClass : disabledClass}`;

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-5">
        <PlayerEditHeader isAdmin={isAdmin} />

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-8 p-4">
            <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)]">
              <PlayerAvatarUploader
                previewImage={previewImage}
                onImageChange={handleImageChange}
              />

              <PlayerPersonalInputs
                formData={formData}
                isAdmin={isAdmin}
                currentInputClass={currentInputClass}
                onChange={handleChange}
              />

              <PlayerFieldPreview
                principal={formData.posicao_principal}
                secundario={formData.posicao_secundaria}
              />
            </div>

            <PlayerEditInfoCard isAdmin={isAdmin} />
          </div>

          <PlayerEditActions saving={saving} onCancel={() => navigate(-1)} />
        </form>

        {/*
          CÓDIGO LEGADO: Mantido como comentário para referência e manutenção.
          Se precisar reverter uma lógica de renderização de UI, consulte o commit anterior.
        */}
      </div>
    </Layout>
  );
};

export default PlayerEdit;
