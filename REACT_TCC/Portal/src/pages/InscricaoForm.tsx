import React, { useState, useEffect, useMemo } from "react";
import Layout from "../components/layouts/LayoutForm";
import CustomInput from "../components/ui/CustomInput";
import { useNavigate } from "react-router-dom";
import { api } from "../config/api";
import toast from "react-hot-toast";

interface Peneira {
  id: number;
  title: string;
  subdivision: string;
  date: string;
  status: string; // Adicionado campo status
}

const InscricaoForm: React.FC = () => {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [peneiras, setPeneiras] = useState<Peneira[]>([]);
  const [loading, setLoading] = useState(true);

  const [formData, setFormData] = useState({
    peneira_id: "",
    nome_completo: "",
    data_nascimento: "",
    cidade: "",
    cpf: "",
    rg: "",
    email: "",
    telefone: "",
    pe_preferido: "",
    posicao_principal: "",
    posicao_secundaria: "",
    altura_cm: "",
    peso_kg: "",
    historico_lesoes_cirurgias: "nao",
    video_apresentacao_url: "",
    foto_perfil_url: null as File | null,
  });

  // 1. BUSCAR TODAS AS PENEIRAS
  useEffect(() => {
    const fetchPeneiras = async () => {
      try {
        const response = await api.get("/peneiras?per_page=100");
        const lista = Array.isArray(response.data) ? response.data : (response.data.data || []);
        setPeneiras(lista);
      } catch (error) {
        console.error("Erro ao buscar peneiras", error);
        toast.error("Não foi possível carregar as peneiras.");
      } finally {
        setLoading(false);
      }
    };
    fetchPeneiras();
  }, []);

  const handleInputChange = (field: string, value: any) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  // 2. Calcular Categoria (Lógica Mantida)
  const categoriaUsuario = useMemo(() => {
    if (!formData.data_nascimento) return null;
    const hoje = new Date();
    const nascimento = new Date(formData.data_nascimento);
    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const mes = hoje.getMonth() - nascimento.getMonth();
    if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) idade--;

    if (idade >= 6 && idade <= 7) return "Sub-7";
    if (idade >= 8 && idade <= 9) return "Sub-9";
    if (idade >= 10 && idade <= 11) return "Sub-11";
    if (idade >= 12 && idade <= 13) return "Sub-13";
    if (idade >= 14 && idade <= 15) return "Sub-15";
    if (idade >= 16 && idade <= 17) return "Sub-17";
    if (idade >= 18 && idade <= 20) return "Sub-20";

    return null;
  }, [formData.data_nascimento]);

  const extrairNumero = (str: string) => {
    const match = str && str.match(/(\d+)/);
    return match ? parseInt(match[0], 10) : null;
  }

  // 3. Filtrar Peneiras (CORRIGIDO: STATUS + PRIORIDADE DE SUBDIVISION)
  const peneirasDisponiveis = useMemo(() => {
    if (!categoriaUsuario) return [];

    const numUsuario = extrairNumero(categoriaUsuario);

    return peneiras.filter((p) => {
      // REGRA 1: Filtra Status (Ignora canceladas/finalizadas)
      const statusValido = ["AGENDADA", "EM_ANDAMENTO"].includes(p.status?.toUpperCase());
      if (!statusValido) return false;

      // REGRA 2: Filtra Categoria
      // Prioridade absoluta para o campo 'subdivision' do banco
      let numApi = extrairNumero(p.subdivision || "");

      // Só tenta ler do título se a subdivision for nula/vazia
      // Isso impede que uma peneira "Sub-15" com título errado "Sub-20" apareça para o usuário de 20 anos
      if (numApi === null && p.title) {
         numApi = extrairNumero(p.title);
      }

      return numApi === numUsuario;
    });
  }, [peneiras, categoriaUsuario]);

  const handleNext = () => {
    if (step === 1 && !formData.data_nascimento) {
      toast.error("Preencha a data de nascimento.");
      return;
    }
    if (step < 3) setStep(step + 1);
    else handleSubmit();
  };

  const handlePrev = () => { if (step > 1) setStep(step - 1); };

  const handleSubmit = async () => {
    // 1. Validação básica
    if (!formData.peneira_id) {
      toast.error("Selecione uma peneira para continuar.");
      return;
    }

    try {
      const data = new FormData();

      // 2. Adicionar campos de texto OBRIGATÓRIOS
      // Como inicializamos o state com "", eles irão como string vazia se não preenchidos
      data.append("peneira_id", formData.peneira_id);
      data.append("nome_completo", formData.nome_completo);
      data.append("data_nascimento", formData.data_nascimento);
      data.append("cidade", formData.cidade);
      data.append("cpf", formData.cpf);
      data.append("rg", formData.rg);
      data.append("email", formData.email);
      data.append("telefone", formData.telefone);
      data.append("pe_preferido", formData.pe_preferido);
      data.append("posicao_principal", formData.posicao_principal);
      data.append("posicao_secundaria", formData.posicao_secundaria);
      data.append("altura_cm", formData.altura_cm);
      data.append("peso_kg", formData.peso_kg);
      data.append("historico_lesoes_cirurgias", formData.historico_lesoes_cirurgias);
      
      // Campos opcionais de texto (verificar se tem valor)
      if (formData.video_apresentacao_url) {
          data.append("video_apresentacao_url", formData.video_apresentacao_url);
      }

      // 3. [IMPORTANTE] Adicionar Foto SOMENTE se for um Arquivo Válido
      // O 'instanceof File' garante que não é null, nem string, nem undefined
      if (formData.foto_perfil_url instanceof File) {
          data.append("foto_perfil_url", formData.foto_perfil_url);
      } else {
          console.log("Foto não selecionada ou inválida, enviando sem foto.");
      }

      // 4. Envia para o Backend
      await api.post("/register/candidate", data);
      
      navigate("/confirmacao");

    } catch (error: any) {
      console.error(error);
      if (error.response?.data?.message) {
          toast.error(error.response.data.message);
          // Mostra os erros específicos de cada campo se houver
          if (error.response.data.errors) {
             console.log("Erros de validação:", error.response.data.errors);
          }
      } else {
          toast.error("Erro ao realizar inscrição.");
      }
    }
  };

  return (
    <Layout>
      <div className="max-w-[900px] mx-auto relative -top-[30px] rounded-[24px] p-8 md:p-[50px] bg-[#F3F3F3] shadow-lg">
        <div className="flex justify-around mb-8 border-b pb-4">
          <div className={`text-center text-sm md:text-base ${step === 1 ? "text-[#1B294E] font-bold text-lg" : "text-gray-400"}`}>1. Informações básicas</div>
          <div className={`text-center text-sm md:text-base ${step === 2 ? "text-[#1B294E] font-bold text-lg" : "text-gray-400"}`}>2. Informações jogador</div>
          <div className={`text-center text-sm md:text-base ${step === 3 ? "text-[#1B294E] font-bold text-lg" : "text-gray-400"}`}>3. Finalização</div>
        </div>

        <form>
          {step === 1 && (
            <div className="animate-fadeIn">
              <div className="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                <div className="md:col-span-9">
                  <CustomInput label="Nome completo:" value={formData.nome_completo} onChange={(v) => handleInputChange("nome_completo", v)} placeholder="Informe seu nome" />
                </div>
                <div className="md:col-span-3">
                  <CustomInput type="date" label="Nascimento:" value={formData.data_nascimento} onChange={(v) => handleInputChange("data_nascimento", v)} />
                </div>
              </div>
              <div className="mb-4">
                <div className="md:w-1/2">
                  <CustomInput label="Cidade:" value={formData.cidade} onChange={(v) => handleInputChange("cidade", v)} placeholder="Informe sua cidade" />
                </div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                <div className="md:col-span-5"><CustomInput label="CPF:" value={formData.cpf} onChange={(v) => handleInputChange("cpf", v)} mask="cpf" /></div>
                <div className="md:col-span-4"><CustomInput label="RG:" value={formData.rg} onChange={(v) => handleInputChange("rg", v)} type="number" /></div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <CustomInput type="email" label="E-mail:" value={formData.email} onChange={(v) => handleInputChange("email", v)} />
                <CustomInput label="Celular:" value={formData.telefone} onChange={(v) => handleInputChange("telefone", v)} mask="telefone" />
              </div>
            </div>
          )}

          {step === 2 && (
            <div className="animate-fadeIn">
              <div className="mb-4 md:w-1/3">
                <CustomInput type="select" label="Pé dominante:" value={formData.pe_preferido} onChange={(v) => handleInputChange("pe_preferido", v)}>
                  <option value="" disabled>Selecione</option>
                  <option value="direito">Direito</option>
                  <option value="esquerdo">Esquerdo</option>
                </CustomInput>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <CustomInput type="select" label="Posição principal:" value={formData.posicao_principal} onChange={(v) => handleInputChange("posicao_principal", v)}>
                    <option value="" disabled>Selecione</option>
                    <option value="Goleiro">Goleiro</option>
                    <option value="Atacante">Atacante</option>
                    <option value="Zagueiro">Zagueiro</option>
                    <option value="Volante">Volante</option>
                    <option value="Meia">Meia</option>
                    <option value="Lateral Direito">Lateral Direito</option>
                    <option value="Lateral Esquerdo">Lateral Esquerdo</option>
                </CustomInput>
                <CustomInput type="select" label="Posição secundária:" value={formData.posicao_secundaria} onChange={(v) => handleInputChange("posicao_secundaria", v)}>
                    <option value="" disabled>Selecione</option>
                    <option value="Goleiro">Goleiro</option>
                    <option value="Atacante">Atacante</option>
                    <option value="Zagueiro">Zagueiro</option>
                    <option value="Volante">Volante</option>
                    <option value="Meia">Meia</option>
                    <option value="Lateral Direito">Lateral Direito</option>
                    <option value="Lateral Esquerdo">Lateral Esquerdo</option>
                </CustomInput>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <CustomInput label="Altura (cm):" value={formData.altura_cm} onChange={(v) => handleInputChange("altura_cm", v)} type="number" />
                <CustomInput label="Peso (kg):" value={formData.peso_kg} onChange={(v) => handleInputChange("peso_kg", v)} type="number" />
              </div>
              <div className="mb-4">
                <p className="text-[#333] mb-2 font-semibold">Já fez cirurgia?</p>
                <div className="flex gap-4 checkbox-wrapper-1">
                  <div className="flex items-center">
                    <input className="substituted" type="radio" name="cirurgia" id="cirurgia_sim" checked={formData.historico_lesoes_cirurgias === "sim"} onChange={() => handleInputChange("historico_lesoes_cirurgias", "sim")} />
                    <label htmlFor="cirurgia_sim" className="ml-1 cursor-pointer">Sim</label>
                  </div>
                  <div className="flex items-center">
                    <input className="substituted" type="radio" name="cirurgia" id="cirurgia_nao" checked={formData.historico_lesoes_cirurgias === "nao"} onChange={() => handleInputChange("historico_lesoes_cirurgias", "nao")} />
                    <label htmlFor="cirurgia_nao" className="ml-1 cursor-pointer">Não</label>
                  </div>
                </div>
              </div>
            </div>
          )}

          {step === 3 && (
            <div className="animate-fadeIn">
              <div className="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800">
                <p className="font-bold">Categoria Identificada: {categoriaUsuario || "Indefinida"}</p>
                <p className="text-sm">Listando peneiras encontradas: {peneirasDisponiveis.length}</p>
              </div>

              <div className="mb-6">
                <CustomInput
                  type="select"
                  label="Selecione a peneira desejada:"
                  value={formData.peneira_id}
                  onChange={(v) => handleInputChange("peneira_id", v)}
                  disabled={peneirasDisponiveis.length === 0}
                >
                  <option value="" disabled>
                    {loading ? "Carregando..." : peneirasDisponiveis.length === 0 ? "Nenhuma disponível" : "Selecione"}
                  </option>
                  {peneirasDisponiveis.map((peneira) => {
                    let dataShow = "Data a definir";
                    if(peneira.date) {
                        try { dataShow = new Date(peneira.date.replace(" ", "T")).toLocaleDateString(); } catch(e){}
                    }
                    return (
                        <option key={peneira.id} value={peneira.id}>
                        {peneira.title} - {dataShow}
                        </option>
                    );
                  })}
                </CustomInput>
                {peneirasDisponiveis.length === 0 && categoriaUsuario && (
                  <p className="text-red-500 text-sm mt-1">
                    Não há peneiras abertas (agendadas) para a categoria {categoriaUsuario} no momento.
                  </p>
                )}
              </div>

              <div className="mb-6 md:w-3/4">
                <CustomInput label="Vídeo de apresentação (YouTube):" value={formData.video_apresentacao_url} onChange={(v) => handleInputChange("video_apresentacao_url", v)} />
              </div>
              <div className="mb-6 md:w-3/4">
                <label className="block text-[#851114] font-bold mb-1 text-sm">Foto 3x4:</label>
                <CustomInput type="file" value="" onChange={(file) => handleInputChange("foto_perfil_url", file)} />
              </div>
            </div>
          )}

          <div className="flex justify-between mt-8 pt-4 border-t border-gray-300">
            <button type="button" onClick={handlePrev} disabled={step === 1} className="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50 transition">Voltar</button>
            <button type="button" onClick={handleNext} className="px-6 py-2 bg-[#007bff] text-white rounded hover:bg-blue-700 transition shadow-md">{step === 3 ? "Enviar Inscrição" : "Avançar"}</button>
          </div>
        </form>
      </div>
      <footer className="text-center p-5 mt-10 text-[#666] text-sm">
        <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
      </footer>
    </Layout>
  );
};

export default InscricaoForm;