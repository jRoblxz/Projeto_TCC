import React, { useEffect, useState, ChangeEvent, FormEvent } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import { getFieldCoordinates } from "../utils/soccerFieldLogic";
import { Loader2, Upload, X, Check } from "lucide-react";
import toast from "react-hot-toast";

const PlayerEdit: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  
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

  const handleChange = (e: ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleImageChange = (e: ChangeEvent<HTMLInputElement>) => {
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
      await api.put(`/players/${id}`, formData);

      if (selectedFile) {
        const imageFormData = new FormData();
        imageFormData.append('image', selectedFile);
        await api.post(`/players/${id}/upload-photo`, imageFormData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
      }

      toast.success("Jogador atualizado!");
      navigate(`/jogadores/${id}`);
    } catch (error) {
      console.error(error);
      toast.error("Erro ao salvar.");
    } finally {
      setSaving(false);
      toast.dismiss(toastId);
    }
  };

  if (loading) return <div className="flex justify-center mt-20"><Loader2 className="animate-spin h-10 w-10 text-[#8B0000]" /></div>;

  const primCoords = getFieldCoordinates(formData.posicao_principal);
  const secCoords = getFieldCoordinates(formData.posicao_secundaria);

  // Estilo base para inputs editáveis (replicação do .editable-field)
  const inputClass = "w-full bg-[rgba(133,17,20,0.05)] border-2 border-dashed border-[rgba(133,17,20,0.3)] rounded text-center py-1 px-2 text-[#333] dark:text-white/60 focus:outline-none focus:bg-[rgba(133,17,20,0.05)] focus:border-[#851114] hover:bg-[rgba(190,18,23,0.1)] transition-colors";
  const selectClass = "w-full bg-[rgba(133,17,20,0.05)] border-2 border-dashed border-[rgba(133,17,20,0.3)] rounded text-center py-1 px-2 text-[#333] dark:text-white/60 focus:outline-none focus:border-[#851114] cursor-pointer hover:bg-[rgba(190,18,23,0.1)] appearance-none";

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-5">
        <div className="bg-[#14244D] dark:bg-gray-900 p-8 text-white  text-center mb-5 rounded-lg shadow-md">
            <h1 className="text-4xl mb-2 drop-shadow-md">Editar Jogador</h1>
            <p className="opacity-90 text-lg">Sistema de Avaliação de Atletas</p>
        </div>

        <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-8 p-4">
                
                {/* COLUNA ESQUERDA (INPUTS PRINCIPAIS) */}
                <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)]">
                    
                    {/* Upload de Imagem */}
                    <div className="group relative w-[150px] h-[150px] rounded-full mx-auto mb-5 overflow-hidden bg-black/70 border-4 border-dashed border-[#851114]/50 hover:border-[#851114] transition-all cursor-pointer">
                        <img 
                            src={previewImage || "/img/avatar_padrao.png"} 
                            alt="avatar" 
                            className="w-full h-full object-cover object-top opacity-100 group-hover:opacity-50 transition-opacity"
                        />
                        <div className="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white font-bold bg-black/40">
                            <Upload size={24} className="mb-1"/>
                            <span className="text-xs">Alterar</span>
                        </div>
                        <input 
                            type="file" 
                            name="image" 
                            accept="image/*" 
                            onChange={handleImageChange}
                            className="absolute inset-0 opacity-0 cursor-pointer"
                        />
                    </div>

                    <div className="mb-6">
                        <input 
                            type="text" 
                            name="nome_completo" 
                            value={formData.nome_completo}
                            onChange={handleChange}
                            placeholder="Nome do Jogador"
                            className="w-full bg-[#851114] text-white text-center text-xl font-bold  p-3 rounded shadow-md border-2 border-transparent focus:border-white focus:outline-none placeholder-white/70"
                        />
                    </div>

                    <div className="grid grid-cols-2 gap-4 mb-5">
                        <div className="bg-white dark:bg-gray-800 dark:border-gray-700  p-3 rounded-lg shadow-sm text-center">
                            <label className="text-xs text-gray-500 dark:text-gray-200  block mb-1">Altura (cm)</label>
                            <input type="number" name="altura_cm" value={formData.altura_cm} onChange={handleChange} className={inputClass} />
                        </div>
                        
                        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
                            <label className="text-xs text-gray-500 dark:text-gray-300  block mb-1">Peso (kg)</label>
                            <input type="number" name="peso_kg" value={formData.peso_kg} onChange={handleChange} className={inputClass} />
                        </div>

                        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
                            <label className="text-xs text-gray-500 dark:text-gray-300  block mb-1">Pé</label>
                            <select name="pe_preferido" value={formData.pe_preferido} onChange={handleChange} className={selectClass}>
                                <option value="Direito">Direito</option>
                                <option value="Esquerdo">Esquerdo</option>
                                <option value="Ambos">Ambos</option>
                            </select>
                        </div>

                        <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg shadow-sm text-center">
                            <label className="text-xs text-gray-500 dark:text-gray-300  block mb-1">Pos. Principal</label>
                            <select name="posicao_principal" value={formData.posicao_principal} onChange={handleChange} className={`${selectClass} text-[#ff4757] font-bold`}>
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
                            <label className="text-xs text-gray-500 dark:text-gray-300  block mb-1">Pos. Secundária</label>
                            <select name="posicao_secundaria" value={formData.posicao_secundaria} onChange={handleChange} className={`${selectClass} text-[#4787ff] font-bold`}>
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

                    {/* Campo Visual */}
                    <div className="w-full h-[200px] bg-[#28a745] rounded-[10px] relative mt-4 shadow-inner overflow-hidden border-2 border-[#1e7e34]">
                        <div className="absolute top-0 left-0 w-full h-full border-[3px] border-white/90 rounded-[10px]"></div>
                        <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/90 -translate-y-1/2"></div>
                        <div className="absolute top-1/2 left-1/2 w-[80px] h-[80px] border-[2px] border-white/90 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                        
                        {primCoords && (
                            <div className="absolute w-5 h-5 bg-[#ff4757] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 animate-pulse" style={{ top: `${primCoords.top}%`, left: `${primCoords.left}%` }}></div>
                        )}
                        {secCoords && (
                            <div className="absolute w-5 h-5 bg-[#4787ff] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 animate-pulse" style={{ top: `${secCoords.top}%`, left: `${secCoords.left}%` }}></div>
                        )}
                    </div>
                </div>

                {/* COLUNA DIREITA (NOTAS E AVISOS) */}
                <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] h-fit">
                    <div className="bg-white dark:bg-gray-800 dark:border-gray-700 p-5 rounded-[10px] border-l-[5px] border-[#14244D] shadow-sm mb-6">
                        <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4 ">Atenção</h4>
                        <p className="text-[#666] dark:text-gray-300 leading-relaxed text-sm">
                            Certifique-se de salvar as alterações após modificar os dados. As notas de avaliação (Overall) são calculadas automaticamente com base nas avaliações realizadas pelos treinadores e não podem ser editadas manualmente aqui.
                        </p>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                       {['Técnica', 'Condicionamento', 'Finalização', 'Velocidade'].map((item) => (
                           <div key={item} className="bg-white dark:bg-gray-800 dark:border-gray-700 p-4 rounded-[10px] text-center shadow-sm border-2 border-dashed border-[#e9ecef] opacity-70">
                               <h5 className="text-[#333] dark:text-white mb-2 font-bold ">{item}</h5>
                               <div className="text-xl text-gray-400 ">Auto</div>
                           </div>
                       ))}
                    </div>
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
                    {saving ? "Salvando..." : "Salvar Alterações"}
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