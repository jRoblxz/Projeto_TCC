import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { api } from "@/config/api";
import Layout from "@/components/layouts/Layout";
import { 
  ArrowLeft, UserCheck, User, Mail, Lock, ShieldCheck, 
  Save, Loader2, ImagePlus, Building, Briefcase, Hash, Award, FileText
} from "lucide-react";
import toast from "react-hot-toast";

const UserEdit: React.FC = () => {
  const navigate = useNavigate();
  const { id } = useParams(); // Pega o ID do usuário na URL
  const [loading, setLoading] = useState(false);
  const [initialLoading, setInitialLoading] = useState(true);

  const [foto, setFoto] = useState<File | null>(null);
  const [fotoPreview, setFotoPreview] = useState<string | null>(null);

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "", // Opcional na edição
    password_confirmation: "",
    role: "treinador",
    clube_organizacao: "",
    cargo: "",
    cref: "",
    anos_experiencia: "",
    biografia_resumo: "",
  });

  // Carregar dados do usuário ao entrar na tela
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await api.get(`/users/${id}`);
        const data = response.data;
        
        setFormData({
          name: data.pessoa?.nome_completo || data.name,
          email: data.email,
          password: "", 
          password_confirmation: "",
          role: data.role,
          clube_organizacao: data.pessoa?.treinador?.clube_organizacao || "",
          cargo: data.pessoa?.treinador?.cargo || "",
          cref: data.pessoa?.treinador?.cref || "",
          anos_experiencia: data.pessoa?.treinador?.anos_experiencia || "",
          biografia_resumo: data.pessoa?.treinador?.biografia_resumo || "",
        });

        if (data.pessoa?.foto_url_completa) {
          setFotoPreview(data.pessoa.foto_url_completa);
        }
      } catch (error) {
        toast.error("Erro ao carregar os dados do usuário.");
        navigate("/usuarios");
      } finally {
        setInitialLoading(false);
      }
    };

    if (id) fetchUser();
  }, [id, navigate]);

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setFoto(file);
      setFotoPreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (formData.password && formData.password !== formData.password_confirmation) {
      toast.error("As senhas não coincidem!");
      return;
    }
    if (formData.password && formData.password.length < 6) {
      toast.error("A senha deve ter no mínimo 6 caracteres.");
      return;
    }

    setLoading(true);
    try {
      const data = new FormData();
      // O Laravel entende o _method PUT mesmo vindo via POST multipart
      data.append("_method", "PUT"); 
      
      data.append("name", formData.name);
      data.append("email", formData.email);
      data.append("role", formData.role);

      // Só envia a senha se o usuário digitou alguma coisa
      if (formData.password) {
        data.append("password", formData.password);
      }

      if (foto) data.append("foto_perfil", foto);

      if (formData.role === "treinador") {
        if (formData.clube_organizacao) data.append("clube_organizacao", formData.clube_organizacao);
        if (formData.cargo) data.append("cargo", formData.cargo);
        if (formData.cref) data.append("cref", formData.cref);
        if (formData.anos_experiencia) data.append("anos_experiencia", formData.anos_experiencia);
        if (formData.biografia_resumo) data.append("biografia_resumo", formData.biografia_resumo);
      }

      await api.post(`/users/${id}`, data, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      
      toast.success("Usuário atualizado com sucesso!");
      navigate("/usuarios");
    } catch (error: any) {
      console.error(error);
      const errorMsg = error.response?.data?.message || "Erro ao atualizar usuário.";
      toast.error(errorMsg);
    } finally {
      setLoading(false);
    }
  };

  if (initialLoading) {
    return (
      <Layout>
        <div className="flex items-center justify-center min-h-[60vh]">
          <Loader2 className="w-10 h-10 animate-spin text-[#14244D]" />
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="max-w-[900px] mx-auto p-4 sm:p-6 lg:p-8 pb-20">
        
        {/* HEADER */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
          <div className="flex items-center gap-4">
            <button onClick={() => navigate(-1)} className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition">
              <ArrowLeft className="text-gray-700 dark:text-gray-300" />
            </button>
            <div>
              <h1 className="text-2xl font-bold text-[#14244D] dark:text-white flex items-center gap-2">
                <UserCheck className="text-[#8B0000]" /> Editar Usuário
              </h1>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Atualize as informações, nível de acesso ou senha deste membro.
              </p>
            </div>
          </div>
        </div>

        {/* FORMULÁRIO */}
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div className="h-2 bg-[#8B0000] w-full"></div>
          
          <form onSubmit={handleSubmit} className="p-8 md:p-10 space-y-8" autoComplete="off">
            
            {/* Nível de Acesso */}
            <div className="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
              <h3 className="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2 uppercase tracking-wider">
                <ShieldCheck size={18} className="text-[#14244D] dark:text-blue-400" /> Nível de Acesso
              </h3>
              <div className="flex flex-col sm:flex-row gap-4">
                <label className={`flex-1 flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all ${formData.role === 'treinador' ? 'border-[#8B0000] bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'}`}>
                  <input type="radio" name="role" value="treinador" checked={formData.role === "treinador"} onChange={(e) => handleInputChange("role", e.target.value)} className="w-5 h-5 text-[#8B0000] focus:ring-[#8B0000]" />
                  <div className="ml-3">
                    <span className="block font-bold text-gray-800 dark:text-gray-200">Treinador / Avaliador</span>
                  </div>
                </label>

                <label className={`flex-1 flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all ${formData.role === 'adm' ? 'border-[#14244D] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'}`}>
                  <input type="radio" name="role" value="adm" checked={formData.role === "adm"} onChange={(e) => handleInputChange("role", e.target.value)} className="w-5 h-5 text-[#14244D] focus:ring-[#14244D]" />
                  <div className="ml-3">
                    <span className="block font-bold text-gray-800 dark:text-gray-200">Administrador</span>
                  </div>
                </label>
              </div>
            </div>

            {/* Dados Principais */}
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_200px] gap-8">
              <div className="space-y-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome Completo *</label>
                  <div className="relative">
                    <User className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input type="text" required value={formData.name} onChange={(e) => handleInputChange("name", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#8B0000] outline-none transition" />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">E-mail de Acesso *</label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input type="email" required value={formData.email} onChange={(e) => handleInputChange("email", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#8B0000] outline-none transition" />
                  </div>
                </div>

                <div className="bg-orange-50 dark:bg-orange-900/10 p-4 rounded-xl border border-orange-100 dark:border-orange-900/30">
                  <p className="text-xs font-bold text-orange-800 dark:text-orange-300 mb-4 uppercase">Alterar Senha (Deixe em branco para manter a atual)</p>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="relative">
                      <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input type="password" value={formData.password} onChange={(e) => handleInputChange("password", e.target.value)} placeholder="Nova senha" autoComplete="new-password" className="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 rounded-lg outline-none transition text-sm" />
                    </div>
                    <div className="relative">
                      <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input type="password" value={formData.password_confirmation} onChange={(e) => handleInputChange("password_confirmation", e.target.value)} placeholder="Confirmar nova senha" autoComplete="new-password" className="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 rounded-lg outline-none transition text-sm" />
                    </div>
                  </div>
                </div>
              </div>

              {/* Foto de Perfil */}
              <div className="flex flex-col items-center justify-start">
                <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 text-center w-full">Foto de Perfil</label>
                <label className="w-40 h-40 rounded-full border-4 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 cursor-pointer hover:border-[#8B0000] transition-all overflow-hidden relative group">
                  {fotoPreview ? (
                    <>
                      <img src={fotoPreview} alt="Preview" className="w-full h-full object-cover" />
                      <div className="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center"><ImagePlus className="text-white w-8 h-8" /></div>
                    </>
                  ) : (
                    <>
                      <ImagePlus className="w-8 h-8 text-gray-400 mb-2" />
                      <span className="text-xs text-gray-500 font-medium">Trocar Foto</span>
                    </>
                  )}
                  <input type="file" accept="image/*" onChange={handleImageChange} className="hidden" />
                </label>
              </div>
            </div>

            {/* SE FOR TREINADOR */}
            {formData.role === "treinador" && (
              <div className="pt-6 border-t border-gray-100 dark:border-gray-800 animate-in fade-in">
                <h3 className="text-lg font-bold text-[#14244D] dark:text-white mb-6">Informações Profissionais</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label className="block text-xs font-bold text-gray-600 mb-2 uppercase">Clube Atual</label>
                    <div className="relative"><Building className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" /><input type="text" value={formData.clube_organizacao} onChange={(e) => handleInputChange("clube_organizacao", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl" /></div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 mb-2 uppercase">Cargo</label>
                    <div className="relative"><Briefcase className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" /><input type="text" value={formData.cargo} onChange={(e) => handleInputChange("cargo", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl" /></div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 mb-2 uppercase">Registro CREF</label>
                    <div className="relative"><Hash className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" /><input type="text" value={formData.cref} onChange={(e) => handleInputChange("cref", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl" /></div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 mb-2 uppercase">Experiência (Anos)</label>
                    <div className="relative"><Award className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" /><input type="number" value={formData.anos_experiencia} onChange={(e) => handleInputChange("anos_experiencia", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl" /></div>
                  </div>
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-600 mb-2 uppercase">Biografia</label>
                  <div className="relative"><FileText className="absolute left-3 top-4 text-gray-400 w-5 h-5" /><textarea rows={4} value={formData.biografia_resumo} onChange={(e) => handleInputChange("biografia_resumo", e.target.value)} className="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl resize-none" /></div>
                </div>
              </div>
            )}

            <div className="pt-8 border-t border-gray-100 flex flex-col-reverse md:flex-row justify-end gap-4">
              <button type="button" onClick={() => navigate(-1)} className="px-6 py-3 font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancelar</button>
              <button type="submit" disabled={loading} className="px-8 py-3 font-bold text-white bg-[#14244D] hover:bg-[#1e3a8a] rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                {loading ? <Loader2 className="animate-spin w-5 h-5" /> : <Save className="w-5 h-5" />} Salvar Alterações
              </button>
            </div>
          </form>

        </div>
      </div>
    </Layout>
  );
};

export default UserEdit;