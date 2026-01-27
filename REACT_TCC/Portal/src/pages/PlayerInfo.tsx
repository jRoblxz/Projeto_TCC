import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { api } from "../config/api";
import Layout from "@/components/layouts/Layout";
import { getFieldCoordinates } from "../utils/soccerFieldLogic";
import { Edit, Trash, ArrowLeft, Loader2 } from "lucide-react";
import toast from "react-hot-toast";
import CustomModalExcluir from "@/components/ui/CustomModalExcluir"; // [1] Importe o Modal

const pulseAnimation = "animate-[pulse_2s_infinite]";

const PlayerInfo: React.FC = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [player, setPlayer] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  // [2] Estados para controlar o Modal
  const [showDeleteModal, setShowDeleteModal] = useState(false);

  useEffect(() => {
    const fetchPlayer = async () => {
      try {
        const response = await api.get(`/players/${id}`);
        const data = response.data.data || response.data;
        setPlayer(data);
      } catch (error) {
        toast.error("Erro ao carregar jogador");
        navigate("/players");
      } finally {
        setLoading(false);
      }
    };
    fetchPlayer();
  }, [id, navigate]);

  // [3] Função que REALMENTE deleta (chamada pelo modal)
  const confirmDelete = async () => {
    try {
      await api.delete(`/players/${id}`);
      toast.success("Jogador deletado!");
      setShowDeleteModal(false); // Fecha o modal
      navigate("/players");
    } catch (error) {
      console.error(error);
      toast.error("Erro ao deletar.");
      setShowDeleteModal(false);
    }
  };

  const calculateAge = (birthDateString: string) => {
    if (!birthDateString) return "N/A";
    const birthDate = new Date(birthDateString);
    if (isNaN(birthDate.getTime())) return "N/A";
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
  };

  const getDisplayRating = () => {
    if (!player) return "0.0";
    if (player.rating_medio && Number(player.rating_medio) > 0) return Number(player.rating_medio).toFixed(1);
    if (player.rating_calculado) return Number(player.rating_calculado).toFixed(1);
    return "0.0";
  };

  if (loading) return <div className="flex justify-center mt-20"><Loader2 className="animate-spin h-10 w-10 text-[#8B0000]" /></div>;
  if (!player) return null;

  const primCoords = getFieldCoordinates(player.posicao_principal);
  const secCoords = getFieldCoordinates(player.posicao_secundaria);

  return (
    <Layout>
      <div className="max-w-[1400px] mx-auto p-5">
        
        {/* ... (HEADER E GRID MANTIDOS IGUAIS) ... */}
        
        <div className="bg-[#14244D] dark:bg-gray-900 p-8 text-white text-center mb-5 rounded-none md:rounded-t-lg relative shadow-md">
            <button 
                onClick={() => navigate(-1)} 
                className="absolute left-5 top-1/2 -translate-y-1/2 text-white/80 hover:text-white hover:scale-110 transition-transform"
            >
                <ArrowLeft size={32} />
            </button>
            <h1 className="text-4xl mb-2 drop-shadow-md  ">Perfil do Jogador</h1>
            <p className="opacity-90 text-lg">Sistema de Avaliação de Atletas</p>
        </div>

        {/* CONTENT GRID */}
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-8 p-4">
          
          {/* COLUNA ESQUERDA */}
          <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] relative">
            <button 
                className="absolute top-4 right-4 w-10 h-10 rounded-full bg-[#141414] text-white flex items-center justify-center shadow-lg hover:w-[120px] hover:bg-[#ff4545] transition-all duration-300 group overflow-hidden z-10"
                onClick={() => navigate(`/jogadores/${id}/edit`)}
            >
                <Edit className="w-4 h-4 group-hover:rotate-[360deg] transition-transform duration-300 shrink-0" />
                <span className="hidden group-hover:block ml-2 text-sm font-bold whitespace-nowrap">Editar</span>
            </button>

            <div className="w-[150px] h-[150px] rounded-full mx-auto mb-5 relative overflow-hidden bg-black/70 border-4 border-white shadow-sm">
              <img 
                src={player.pessoa?.foto_url_completa || "/img/avatar_padrao.png"} 
                alt="Foto" 
                className="w-full h-full object-cover object-top"
                onError={(e) => e.currentTarget.src = "/img/avatar_padrao.png"}
              />
            </div>

            <div className="bg-[#851114] text-white p-4 text-center mb-5 text-2xl font-bold   shadow-md rounded-sm">
                {player.pessoa?.nome_completo || "Nome Indisponível"}
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-2 gap-4 mb-5">
              {[
                { label: "Idade", value: calculateAge(player.pessoa?.data_nascimento) },
                { label: "Altura", value: player.altura_cm ? `${player.altura_cm} cm` : "N/A" },
                { label: "Pé", value: player.pe_preferido || "N/A" },
                { label: "Peso", value: player.peso_kg ? `${player.peso_kg} kg` : "N/A" },
              ].map((stat, i) => (
                <div key={i} className="bg-white dark:bg-gray-800 dark:border-gray-700 p-3 rounded-lg text-center shadow-sm border-2  border-[#e9ecef] hover:-translate-y-0.5 hover:shadow-md hover:border-[#851114] transition-all">
                    <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">{stat.label}</div>
                    <div className="text-lg font-bold text-[#333] dark:text-white">{stat.value}</div>
                </div>
              ))}
              
              <div className="bg-white dark:bg-gray-800 p-3 rounded-lg text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 transition-all">
                  <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">Posição Principal</div>
                  <div className="text-lg font-bold text-[#ff4757]">{player.posicao_principal || "N/A"}</div>
              </div>
              <div className="bg-white dark:bg-gray-800 p-3 rounded-lg text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 transition-all">
                  <div className="text-sm text-gray-500 dark:text-gray-300 mb-1">Posição Secundária</div>
                  <div className="text-lg font-bold text-[#4787ff] ">{player.posicao_secundaria || "-"}</div>
              </div>
            </div>

            {/* Campo de Futebol */}
            <div className="w-full h-[200px] bg-[#28a745] rounded-[10px] relative mb-5 shadow-inner overflow-hidden border-2 border-[#1e7e34]">
                <div className="absolute top-0 left-0 w-full h-full border-[3px] border-white/90 rounded-[10px]"></div>
                <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/90 -translate-y-1/2"></div>
                <div className="absolute top-1/2 left-1/2 w-[80px] h-[80px] border-[2px] border-white/90 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                
                {primCoords && (
                    <div 
                        className={`absolute w-5 h-5 bg-[#ff4757] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 ${pulseAnimation}`}
                        style={{ top: `${primCoords.top}%`, left: `${primCoords.left}%` }}
                        title="Posição Principal"
                    ></div>
                )}
                {secCoords && (
                    <div 
                        className={`absolute w-5 h-5 bg-[#4787ff] border-[3px] border-white rounded-full -translate-x-1/2 -translate-y-1/2 shadow-lg z-10 ${pulseAnimation}`}
                        style={{ top: `${secCoords.top}%`, left: `${secCoords.left}%` }}
                        title="Posição Secundária"
                    ></div>
                )}
            </div>
          </div>

          {/* COLUNA DIREITA */}
          <div className="bg-[#f8f9fa] dark:bg-gray-900 rounded-[15px] p-6 shadow-[0_5px_20px_rgba(0,0,0,0.05)] flex flex-col gap-6">
            
            {/* OVERALL SCORE */}
            <div className="bg-[#851114] text-white p-5 rounded-[15px] text-center shadow-lg">
              <h3 className="text-2xl mb-2  ">Overall Score</h3>
              <div className="text-5xl font-bold drop-shadow-md  ">
                  {getDisplayRating()}
              </div>
            </div>

            <div className="bg-white dark:bg-gray-800 p-5 rounded-[10px] border-l-[5px] border-[#14244D] shadow-sm">
              <h4 className="text-[#333] dark:text-white text-lg font-bold mb-4  ">Informações e Notas</h4>
              <div className="text-[#666] dark:text-gray-300 leading-relaxed space-y-2 text-sm md:text-base">
                <p><strong>Data de Nascimento:</strong> {new Date(player.pessoa?.data_nascimento).toLocaleDateString()}</p>
                <p><strong>Email:</strong> {player.pessoa?.email || "N/A"}</p>
                <p><strong>CPF:</strong> {player.pessoa?.cpf || "N/A"}</p>
                <p><strong>Telefone:</strong> {player.pessoa?.telefone || "N/A"}</p>
                <p><strong>RG:</strong> {player.pessoa?.rg || "N/A"}</p>
                <p><strong>Cirurgia:</strong> {player.historico_lesoes_cirurgias || "N/A"}</p>
                <p>
                    <strong>Video Skills:</strong>{' '}
                    {player.video_apresentacao_url ? (
                        <a href={player.video_apresentacao_url} target="_blank" rel="noreferrer" className="text-blue-600 underline hover:text-blue-800">
                            Link do Vídeo
                        </a>
                    ) : "N/A"}
                </p>
              </div>
              
              <div className="mt-6 pt-4 border-t border-gray-100">
                  <h4 className="text-[#333] dark:text-white text-lg font-bold mb-2  ">Avaliação Recente</h4>
                  <p className="text-[#666] dark:text-gray-300 italic dark:bg-gray-700 dark:border-gray-300 bg-gray-50 p-3 rounded">"{player.ultima_avaliacao?.observacoes ?? 'Nenhuma observação.'}"</p>
              </div>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
               {['Técnica', 'Condicionamento', 'Finalização', 'Velocidade', 'Posicionamento', 'Cabeceio'].map((item) => (
                   <div key={item} className="bg-white dark:bg-gray-800 p-4 rounded-[10px] text-center shadow-sm border-2 dark:border-gray-700 border-[#e9ecef] hover:-translate-y-0.5 hover:border-[#851114] transition-all">
                       <h5 className="text-[#333] dark:text-white mb-2 font-bold text-lg  ">{item}</h5>
                       <div className="text-2xl text-[#851114]  ">N/A</div>
                   </div>
               ))}
            </div>
          </div>
        </div>

        {/* BOTÃO DELETAR QUE ABRE O MODAL */}
        <div className="flex justify-center pb-10 mt-6">
            <button 
                className="bg-[#ff6363] border-2 border-black text-white text-xl uppercase font-bold py-3 px-6 rounded-lg shadow-[0_2px_4px_rgba(0,0,0,0.4)] hover:bg-red-600 hover:rounded-[3px] hover:-translate-y-1 hover:rotate-1 transition-all duration-300 flex items-center gap-2  "
                onClick={() => setShowDeleteModal(true)} // [4] Abre o modal
            >
                <Trash size={20} /> DELETAR JOGADOR
            </button>
        </div>

        {/* [5] Componente do Modal Renderizado */}
        {showDeleteModal && (
            <CustomModalExcluir
                itemToDelete={player.pessoa?.nome_completo || "este jogador"}
                confirmDelete={confirmDelete}
                setShowModalFalse={() => setShowDeleteModal(false)}
            />
        )}

      </div>
    </Layout>
  );
};

export default PlayerInfo;