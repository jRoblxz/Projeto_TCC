import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "@/config/api";
import {
  UserPlus,
  User,
  Mail,
  Lock,
  Save,
  Loader2,
  ImagePlus,
  ArrowLeft,
} from "lucide-react";
import toast from "react-hot-toast";
import Logo from "@/assets/img/logo-copia.png"; // Importando a sua logo

const CadastroTestes: React.FC = () => {
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
      const data = new FormData();
      data.append("name", formData.name);
      data.append("email", formData.email);
      data.append("password", formData.password);
      data.append("password_confirmation", formData.password_confirmation);

      // FORÇANDO ACESSO TOTAL PARA OS TESTADORES:
      data.append("role", "adm");

      if (foto) {
        data.append("foto_perfil", foto);
      }

      // ATENÇÃO: Usando a rota pública '/cadastro' configurada no seu backend
      await api.post("/cadastro", data, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      toast.success("Conta de teste criada com sucesso! Faça o login.");

      // Redireciona para o login após criar a conta
      setTimeout(() => {
        navigate("/login");
      }, 1500);
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
    <div className="min-h-screen bg-brand-primary flex items-center justify-center p-4">
      <div className="w-full max-w-[800px] bg-white rounded-[10px] overflow-hidden shadow-[0px_15px_15px_rgba(0,0,0,0.5)] flex flex-col">
        {/* HEADER DO FORMULÁRIO */}
        <div className="bg-gray-50 border-b border-gray-200 p-6 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <img src={Logo} alt="Logo" className="w-12 h-12 object-contain" />
            <div>
              <h1 className="text-2xl font-bold text-[#851114] flex items-center gap-2">
                Crie sua conta de Teste
              </h1>
              <p className="text-sm text-gray-500 font-medium">
                Você terá acesso de Administrador para testar o sistema.
              </p>
            </div>
          </div>
          <button
            onClick={() => navigate("/login")}
            className="flex items-center gap-1 text-sm font-bold text-gray-500 hover:text-[#851114] transition-colors"
          >
            <ArrowLeft size={16} /> Voltar
          </button>
        </div>

        {/* CORPO DO FORMULÁRIO */}
        <form onSubmit={handleSubmit} className="p-8 md:p-10 space-y-8">
          <div className="grid grid-cols-1 lg:grid-cols-[1fr_200px] gap-8">
            {/* Lado Esquerdo: Textos */}
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">
                  Nome Completo *
                </label>
                <div className="relative">
                  <User className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) => handleInputChange("name", e.target.value)}
                    placeholder="Seu nome"
                    className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#851114] outline-none transition text-gray-800"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">
                  E-mail de Acesso *
                </label>
                <div className="relative">
                  <Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                  <input
                    type="email"
                    required
                    value={formData.email}
                    onChange={(e) => handleInputChange("email", e.target.value)}
                    placeholder="seuemail@teste.com"
                    autoComplete="nope"
                    className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#851114] outline-none transition text-gray-800"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    Senha *
                  </label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input
                      type="password"
                      required
                      value={formData.password}
                      onChange={(e) =>
                        handleInputChange("password", e.target.value)
                      }
                      placeholder="Mínimo 6 caracteres"
                      autoComplete="new-password"
                      className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#851114] outline-none transition text-gray-800"
                    />
                  </div>
                </div>
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    Confirmar Senha *
                  </label>
                  <div className="relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                    <input
                      type="password"
                      required
                      value={formData.password_confirmation}
                      onChange={(e) =>
                        handleInputChange(
                          "password_confirmation",
                          e.target.value,
                        )
                      }
                      placeholder="Repita a senha"
                      autoComplete="new-password"
                      className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#851114] outline-none transition text-gray-800"
                    />
                  </div>
                </div>
              </div>
            </div>

            {/* Lado Direito: Foto */}
            <div className="flex flex-col items-center justify-start">
              <label className="block text-sm font-bold text-gray-700 mb-2 text-center w-full">
                Foto de Perfil
              </label>
              <label className="w-40 h-40 rounded-full border-4 border-dashed border-gray-200 flex flex-col items-center justify-center bg-gray-50 cursor-pointer hover:border-[#851114] hover:bg-red-50 transition-all overflow-hidden relative group">
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

          <div className="pt-8 border-t border-gray-100 flex justify-end">
            <button
              type="submit"
              disabled={loading}
              className="w-full md:w-auto px-10 py-4 font-bold text-white bg-[#851114] hover:bg-[#630b0e] rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-70 text-lg"
            >
              {loading ? (
                <Loader2 className="animate-spin w-5 h-5" />
              ) : (
                <UserPlus className="w-5 h-5" />
              )}
              Criar Conta e Testar
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default CadastroTestes;
