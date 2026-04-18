import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "@/config/api";
import Layout from "@/components/layouts/Layout";
import {
  ArrowLeft,
  UserPlus,
  User,
  Mail,
  Lock,
  ShieldCheck,
  Save,
  Loader2,
  ImagePlus,
  Building,
  Briefcase,
  Hash,
  Award,
  FileText,
} from "lucide-react";
import toast from "react-hot-toast";

const CadastroAdmin: React.FC = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  // Estado para a foto
  const [foto, setFoto] = useState<File | null>(null);
  const [fotoPreview, setFotoPreview] = useState<string | null>(null);

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "treinador",
    // Campos opcionais do treinador
    clube_organizacao: "",
    cargo: "",
    cref: "",
    anos_experiencia: "",
    biografia_resumo: "",
  });

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setFoto(file);
      const previewUrl = URL.createObjectURL(file);
      setFotoPreview(previewUrl);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (formData.password !== formData.password_confirmation) {
      toast.error("As senhas não coincidem!");
      return;
    }
    if (formData.password.length < 6) {
      toast.error("A senha deve ter no mínimo 6 caracteres.");
      return;
    }

    setLoading(true);
    try {
      // Como tem foto, precisamos usar FormData
      const data = new FormData();
      data.append("name", formData.name);
      data.append("email", formData.email);
      data.append("password", formData.password);
      data.append("password_confirmation", formData.password_confirmation);
      data.append("role", formData.role);

      if (foto) {
        data.append("foto_perfil", foto);
      }

      // Adiciona os campos extras apenas se for treinador e se estiverem preenchidos
      if (formData.role === "treinador") {
        if (formData.clube_organizacao)
          data.append("clube_organizacao", formData.clube_organizacao);
        if (formData.cargo) data.append("cargo", formData.cargo);
        if (formData.cref) data.append("cref", formData.cref);
        if (formData.anos_experiencia)
          data.append("anos_experiencia", formData.anos_experiencia);
        if (formData.biografia_resumo)
          data.append("biografia_resumo", formData.biografia_resumo);
      }

      await api.post("/register", data, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      toast.success("Usuário cadastrado com sucesso!");
      // Zera o formulário em vez de recarregar a página!
      setFoto(null);
      setFotoPreview(null);
      setFormData({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "treinador",
        clube_organizacao: "",
        cargo: "",
        cref: "",
        anos_experiencia: "",
        biografia_resumo: "",
      });
    } catch (error: any) {
      console.error(error);
      const errorMsg =
        error.response?.data?.message || "Erro ao cadastrar usuário.";
      toast.error(errorMsg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout>
      <div className="max-w-[900px] mx-auto p-4 sm:p-6 lg:p-8 pb-20">
        {/* HEADER */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate(-1)}
              className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
            >
              <ArrowLeft className="text-gray-700 dark:text-gray-300" />
            </button>
            <div>
              <h1 className="text-2xl font-bold text-brand-primary dark:text-white flex items-center gap-2">
                <UserPlus className="text-brand-darkred" /> Novo Usuário
              </h1>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Cadastre novos membros da comissão técnica ou administradores.
              </p>
            </div>
          </div>
        </div>

        {/* FORMULÁRIO */}
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div className="h-2 bg-brand-darkred w-full"></div>

          <form onSubmit={handleSubmit} className="p-8 md:p-10 space-y-8">
            {/* Nível de Acesso (Role) */}
            <div className="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
              <h3 className="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2 uppercase tracking-wider">
                <ShieldCheck
                  size={18}
                  className="text-brand-primary dark:text-blue-400"
                />{" "}
                Nível de Acesso
              </h3>
              <div className="flex flex-col sm:flex-row gap-4">
                <label
                  className={`flex-1 flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all ${formData.role === "treinador" ? "border-brand-darkred bg-red-50 dark:bg-red-900/20" : "border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500"}`}
                >
                  <input
                    type="radio"
                    name="role"
                    value="treinador"
                    checked={formData.role === "treinador"}
                    onChange={(e) => handleInputChange("role", e.target.value)}
                    className="w-5 h-5 text-brand-darkred focus:ring-brand-darkred"
                  />
                  <div className="ml-3">
                    <span className="block font-bold text-gray-800 dark:text-gray-200">
                      Treinador / Avaliador
                    </span>
                    <span className="text-xs text-gray-500 dark:text-gray-400">
                      Pode avaliar jogadores e ver relatórios.
                    </span>
                  </div>
                </label>

                <label
                  className={`flex-1 flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all ${formData.role === "adm" ? "border-brand-primary bg-blue-50 dark:bg-blue-900/20" : "border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500"}`}
                >
                  <input
                    type="radio"
                    name="role"
                    value="adm"
                    checked={formData.role === "adm"}
                    onChange={(e) => handleInputChange("role", e.target.value)}
                    className="w-5 h-5 text-brand-primary focus:ring-brand-primary"
                  />
                  <div className="ml-3">
                    <span className="block font-bold text-gray-800 dark:text-gray-200">
                      Administrador
                    </span>
                    <span className="text-xs text-gray-500 dark:text-gray-400">
                      Acesso total às configurações do sistema.
                    </span>
                  </div>
                </label>
              </div>
            </div>

            {/* Área de Dados e Foto (Lado a Lado no Desktop) */}
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_200px] gap-8">
              {/* Campos Obrigatórios */}
              <div className="space-y-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Nome Completo *
                  </label>
                  <div className="relative">
                    <User className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input
                      type="text"
                      autoComplete="username"
                      required
                      value={formData.name}
                      onChange={(e) =>
                        handleInputChange("name", e.target.value)
                      }
                      placeholder="Ex: Luiz Felipe Scolari"
                      className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    E-mail de Acesso *
                  </label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input
                      type="email"
                      autoComplete="username"
                      required
                      value={formData.email}
                      onChange={(e) =>
                        handleInputChange("email", e.target.value)
                      }
                      placeholder="treinador@gdprudente.com.br"
                      className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                      Senha *
                    </label>
                    <div className="relative">
                      <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="password"
                        autoComplete="new-password"
                        required
                        value={formData.password}
                        onChange={(e) =>
                          handleInputChange("password", e.target.value)
                        }
                        placeholder="Mínimo 6 caracteres"
                        className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                      Confirmar Senha *
                    </label>
                    <div className="relative">
                      <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="password"
                        autoComplete="new-password"
                        required
                        value={formData.password_confirmation}
                        onChange={(e) =>
                          handleInputChange(
                            "password_confirmation",
                            e.target.value,
                          )
                        }
                        placeholder="Repita a senha"
                        className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                </div>
              </div>

              {/* Upload de Foto */}
              <div className="flex flex-col items-center justify-start">
                <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 text-center w-full">
                  Foto de Perfil
                </label>
                <label className="w-40 h-40 rounded-full border-4 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 cursor-pointer hover:border-brand-darkred hover:bg-red-50 dark:hover:bg-red-900/10 transition-all overflow-hidden relative group">
                  {fotoPreview ? (
                    <>
                      <img
                        src={fotoPreview}
                        alt="Preview"
                        className="w-full h-full object-cover"
                      />
                      <div className="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center">
                        <ImagePlus className="text-white w-8 h-8" />
                      </div>
                    </>
                  ) : (
                    <>
                      <ImagePlus className="w-8 h-8 text-gray-400 mb-2" />
                      <span className="text-xs text-gray-500 font-medium">
                        Adicionar Foto
                      </span>
                    </>
                  )}
                  <input
                    type="file"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="hidden"
                  />
                </label>
              </div>
            </div>

            {/* SE FOR TREINADOR, EXIBE OS CAMPOS EXTRAS */}
            {formData.role === "treinador" && (
              <div className="pt-6 border-t border-gray-100 dark:border-gray-800 animate-in fade-in slide-in-from-top-4">
                <h3 className="text-lg font-bold text-brand-primary dark:text-white mb-6">
                  Informações Profissionais (Opcional)
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label className="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase">
                      Clube / Organização Atual
                    </label>
                    <div className="relative">
                      <Building className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="text"
                        value={formData.clube_organizacao}
                        onChange={(e) =>
                          handleInputChange("clube_organizacao", e.target.value)
                        }
                        placeholder="Ex: Fatec Prudente"
                        className="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase">
                      Cargo
                    </label>
                    <div className="relative">
                      <Briefcase className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="text"
                        value={formData.cargo}
                        onChange={(e) =>
                          handleInputChange("cargo", e.target.value)
                        }
                        placeholder="Ex: Treinador Sub-20, Olheiro..."
                        className="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase">
                      Registro CREF
                    </label>
                    <div className="relative">
                      <Hash className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="text"
                        value={formData.cref}
                        onChange={(e) =>
                          handleInputChange("cref", e.target.value)
                        }
                        placeholder="000000-G/SP"
                        className="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase">
                      Anos de Experiência
                    </label>
                    <div className="relative">
                      <Award className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                      <input
                        type="number"
                        min="0"
                        value={formData.anos_experiencia}
                        onChange={(e) =>
                          handleInputChange("anos_experiencia", e.target.value)
                        }
                        placeholder="Ex: 5"
                        className="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white"
                      />
                    </div>
                  </div>
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase">
                    Biografia / Resumo
                  </label>
                  <div className="relative">
                    <FileText className="absolute left-3 top-4 text-gray-400 w-5 h-5" />
                    <textarea
                      rows={4}
                      value={formData.biografia_resumo}
                      onChange={(e) =>
                        handleInputChange("biografia_resumo", e.target.value)
                      }
                      placeholder="Fale um pouco sobre a sua trajetória no futebol..."
                      className="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-darkred outline-none transition text-gray-800 dark:text-white resize-none"
                    />
                  </div>
                </div>
              </div>
            )}

            <div className="pt-8 border-t border-gray-100 dark:border-gray-800 flex flex-col-reverse md:flex-row justify-end gap-4">
              <button
                type="button"
                onClick={() => navigate(-1)}
                className="px-6 py-3 font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors"
              >
                Cancelar
              </button>
              <button
                type="submit"
                disabled={loading}
                className="px-8 py-3 font-bold text-white bg-brand-primary hover:bg-[#1e3a8a] rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
              >
                {loading ? (
                  <Loader2 className="animate-spin w-5 h-5" />
                ) : (
                  <Save className="w-5 h-5" />
                )}
                Salvar Usuário
              </button>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  );
};

export default CadastroAdmin;
