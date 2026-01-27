import React, { useState, FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { Eye, EyeOff, Lock, Mail, LogIn, Loader2 } from "lucide-react";
import toast from "react-hot-toast";
import "../App.css";
// [CORREÇÃO 1] Importamos a instância 'api' configurada com Axios
import { api } from "../config/api"; 

const Login: React.FC = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const handleLogin = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!email || !password) {
      toast.error("Por favor, preencha todos os campos");
      return;
    }

    setLoading(true);
    const toastId = toast.loading("Fazendo login...");

    try {
      // [CORREÇÃO 2] Usamos api.post em vez de fetch
      // A URL base já é ".../api/v1", então só colocamos "/login"
      const response = await api.post('/login', { 
        email, 
        password 
      });

      // O Axios joga a resposta do Laravel dentro de response.data
      const data = response.data;
      
      toast.dismiss(toastId);

      // [CORREÇÃO 3] O Laravel Sanctum retorna 'access_token'
      if (data.access_token) {
        // [CORREÇÃO 4] Salvamos como 'auth_token' para bater com a config do api.tsx
        localStorage.setItem("auth_token", data.access_token);
        
        // Opcional: Salvar dados do usuário se precisar mostrar nome depois
        localStorage.setItem("user_data", JSON.stringify(data.user));

        toast.success("Login realizado com sucesso!");
        navigate("/dashboard");
      } else {
        toast.error("Erro inesperado: Token não recebido.");
      }

    } catch (err: any) {
      toast.dismiss(toastId);
      console.error(err);

      // Tratamento de erro específico do Axios
      if (err.response) {
        // Erro 401 (Credenciais erradas) ou 422 (Validação)
        const mensagemErro = err.response.data.message || "Email ou senha incorretos.";
        toast.error(mensagemErro);
      } else if (err.request) {
        // Erro de conexão (Servidor desligado)
        toast.error("Não foi possível conectar ao servidor.");
      } else {
        toast.error("Ocorreu um erro ao tentar fazer login.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center p-4">
      {/* Loader */}
      {loading && (
        <div className="absolute inset-0 flex items-center justify-center bg-white/70 z-50 rounded-lg">
          <Loader2 className="h-10 w-10 text-blue-600 animate-spin" />
        </div>
      )}
      
      <div className="max-w-md w-full space-y-8">
        <div className="text-center">
          <div className="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-4">
            <LogIn className="h-8 w-8 text-white" />
          </div>
          <h2 className="text-3xl font-bold text-gray-900 dark:text-white">
            Bem-vindo de volta
          </h2>
          <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Faça login em sua conta para continuar
          </p>
        </div>

        <div className="bg-white dark:bg-gray-800 py-8 px-6 shadow-xl rounded-lg">
          <form className="space-y-6" onSubmit={handleLogin}>
            <div>
              <label
                htmlFor="email"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Email
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Mail className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="email"
                  name="email"
                  type="email"
                  autoComplete="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-400"
                  placeholder="Digite seu email"
                />
              </div>
            </div>

            <div>
              <label
                htmlFor="password"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                Senha
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Lock className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="password"
                  name="password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="block w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-400"
                  placeholder="Digite sua senha"
                />
                <button
                  type="button"
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? (
                    <EyeOff className="h-5 w-5 text-gray-400 hover:text-gray-600" />
                  ) : (
                    <Eye className="h-5 w-5 text-gray-400 hover:text-gray-600" />
                  )}
                </button>
              </div>
            </div>

            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <input
                  id="remember-me"
                  name="remember-me"
                  type="checkbox"
                  className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label
                  htmlFor="remember-me"
                  className="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                >
                  Lembrar-me
                </label>
              </div>

              <div className="text-sm">
                <a
                  href="#"
                  className="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
                >
                  Esqueceu a senha?
                </a>
              </div>
            </div>

            <div>
              <button
                type="submit"
                disabled={loading}
                className="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
              >
                {loading ? (
                  <div className="flex items-center">
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Entrando...
                  </div>
                ) : (
                  "Entrar"
                )}
              </button>
            </div>
          </form>
        </div>

        <div className="text-center">
          <p className="text-sm text-gray-600 dark:text-gray-400">
            Não tem uma conta?{" "}
            <a
              href="#"
              className="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
            >
              Cadastre-se aqui
            </a>
          </p>
        </div>
      </div>
    </div>
  );
};

export default Login;