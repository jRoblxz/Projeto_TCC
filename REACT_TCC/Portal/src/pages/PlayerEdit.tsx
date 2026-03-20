import React, { useEffect, useState, ChangeEvent, FormEvent } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import { getFieldCoordinates } from "../utils/soccerFieldLogic";
import { Loader2, Upload, X, Check, Lock } from "lucide-react"; // Adicionei Lock
import toast from "react-hot-toast";
import { isUserAdmin } from "../utils/auth"; // Importe o helper

const PlayerEdit: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false); // Estado para permissão

  const [previewImage, setPreviewImage] = useState<string>("");
  const [selectedFile, setSelectedFile] = useState<File | null>(null);

  const [formData, setFormData] = useState({
    nome_completo: "",
    altura_cm: "",
    peso_kg: "",
    pe_preferido: "Direito",
    posicao_principal: "Goleiro",
    posicao_secundaria: "",
  });

  useEffect(() => {
    setIsAdmin(isUserAdmin()); // Verifica permissão ao carregar

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

  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    // Se não for admin, não deixa alterar o state dos textos
    if (!isAdmin) return;
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleImageChange = (e: ChangeEvent<HTMLInputElement>) => {
    // Foto é permitida para todos
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0];
      setSelectedFile(file);
      setPreviewImage(URL.createObjectURL(file));
    }
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setSaving(true);
    const toastId = toast.loading("Salvando...");

    try {
      // Se for Admin, salva os dados de texto
      if (isAdmin) {
        await api.put(`/players/${id}`, formData);
      }

      // Se selecionou arquivo, salva a foto (Admin ou Jogador)
      if (selectedFile) {
        const imageFormData = new FormData();
        imageFormData.append("image", selectedFile);
        await api.post(`/players/${id}/upload-photo`, imageFormData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
      }

      toast.success("Perfil atualizado!");

      // Se for o próprio jogador, talvez queira recarregar a página para atualizar a foto na Sidebar
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

  if (loading)
    return (
      <div className="flex justify-center mt-20">
        <Loader2 className="animate-spin h-10 w-10 text-[#8B0000]" />
      </div>
    );

  const primCoords = getFieldCoordinates(formData.posicao_principal);
  const secCoords = getFieldCoordinates(formData.posicao_secundaria);

  // Estilos Condicionais
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

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-8 p-4">
            {/* COLUNA ESQUERDA */}
            <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)]">
              {/* Upload de Imagem (SEMPRE HABILITADO) */}
              <div className="group relative w-[150px] h-[150px] rounded-full mx-auto mb-5 overflow-hidden bg-black/70 border-4 border-dashed border-[#851114]/50 hover:border-[#851114] transition-all cursor-pointer">
                <img
                  src={previewImage || "/img/avatar_padrao.png"}
                  alt="avatar"
                  className="w-full h-full object-cover object-top opacity-100 group-hover:opacity-50 transition-opacity"
                />
                <div className="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white font-bold bg-black/40">
                  <Upload size={24} className="mb-1" />
                  <span className="text-xs">Alterar Foto</span>
                </div>
                <input
                  type="file"
                  name="image"
                  accept="image/*"
                  onChange={handleImageChange}
                  className="absolute inset-0 opacity-0 cursor-pointer"
                />
              </div>

              {/* Nome (Bloqueado se não for Admin) */}
              <div className="mb-6 relative">
                <input
                  type="text"
                  name="nome_completo"
                  disabled={!isAdmin}
                  value={formData.nome_completo}
                  onChange={handleChange}
                  className={`w-full text-center text-xl font-bold p-3 rounded shadow-md border-2 
                                ${
                                  isAdmin
                                    ? "bg-[#851114] text-white border-transparent focus:border-white"
                                    : "bg-gray-300 text-gray-600 cursor-not-allowed border-gray-300"
                                }`}
                />
                {!isAdmin && (
                  <Lock className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4" />
                )}
              </div>

              {/* Inputs Bloqueáveis */}
              <div className="grid grid-cols-2 gap-4 mb-5 relative">
                {/* Overlay de Bloqueio Visual (Opcional, ou use o disabled em cada input) */}
                {!isAdmin && (
                  <div
                    className="absolute inset-0 z-10 cursor-not-allowed"
                    title="Contate o administrador para alterar dados técnicos"
                  ></div>
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
                    onChange={handleChange}
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
                    onChange={handleChange}
                    className={currentInputClass}
                  />
                </div>

                {/* ... Outros inputs iguais, só adicionando disabled={!isAdmin} e className={currentInputClass} ... */}

                <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
                  <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
                    Pé
                  </label>
                  <select
                    name="pe_preferido"
                    disabled={!isAdmin}
                    value={formData.pe_preferido}
                    onChange={handleChange}
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
                    onChange={handleChange}
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

                {/* Pos secundária */}
                <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center col-span-2">
                  <label className="text-xs text-gray-500 dark:text-gray-300 block mb-1">
                    Pos. Secundária
                  </label>
                  <select
                    name="posicao_secundaria"
                    disabled={!isAdmin}
                    value={formData.posicao_secundaria}
                    onChange={handleChange}
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

              {/* Campo Visual (Apenas Exibição) */}
              <div className="w-full h-[200px] bg-[#28a745] rounded-[10px] relative mt-4 shadow-inner overflow-hidden border-2 border-[#1e7e34] opacity-90">
                <div className="absolute top-0 left-0 w-full h-full border-[3px] border-white/90 rounded-[10px]"></div>
                        <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/90 -translate-y-1/2"></div>
                        <div className="absolute top-1/2 left-1/2 w-[80px] h-[80px] border-[2px] border-white/90 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                {primCoords && (
                  <div
                    className="absolute w-5 h-5 bg-[#ff4757] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10"
                    style={{
                      top: `${primCoords.top}%`,
                      left: `${primCoords.left}%`,
                    }}
                  ></div>
                )}

                {secCoords && (
                  <div
                    className="absolute w-5 h-5 bg-[#4787ff] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 animate-pulse"
                    style={{
                      top: `${secCoords.top}%`,
                      left: `${secCoords.left}%`,
                    }}
                  ></div>
                )}
              </div>
            </div>

            {/* COLUNA DIREITA (NOTAS - Sempre bloqueado para edição manual aqui, mesmo para admin, conforme seu código anterior) */}
            <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] h-fit">
              <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-5 rounded-[10px] border-l-[5px] border-[#14244D] shadow-sm mb-6">
                <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4 ">
                  {isAdmin ? "Atenção" : "Meus Dados"}
                </h4>
                <p className="text-[#666] dark:text-gray-300 leading-relaxed text-sm">
                  {isAdmin
                    ? "Certifique-se de salvar as alterações."
                    : "Você pode atualizar sua foto de perfil aqui. Para alterar dados técnicos (altura, peso, posição), fale com seu treinador."}
                </p>
              </div>
              {/* ... (Grids de notas mantidos iguais) ... */}
            </div>
          </div>

          {/* BOTÕES DE AÇÃO */}
          <div className="flex justify-center gap-4 pb-10 mt-4">
            <button
              type="submit"
              disabled={saving}
              className="bg-[#479440] border-2 border-black text-white text-xl uppercase font-bold py-3 px-8 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-[#48ff00] hover:rounded-[3px] hover:-translate-y-1 hover:rotate-1 transition-all duration-300  flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {saving ? <Loader2 className="animate-spin" /> : <Check />}
              {saving ? "Salvando..." : "Salvar Foto"}
            </button>

            <button
              type="button"
              onClick={() => navigate(-1)}
              className="bg-[#bb4838] border-2 border-black text-white text-xl uppercase font-bold py-3 px-8 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-red-600 hover:rounded-[3px] hover:-translate-y-1 hover:-rotate-1 transition-all duration-300  flex items-center gap-2"
            >
              <X /> Cancelar
            </button>
          </div>
        </form>
      </div>
    </Layout>
  );
};

export default PlayerEdit;
