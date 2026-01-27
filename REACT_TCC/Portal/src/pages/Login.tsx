import React, { useState, FormEvent, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import toast from "react-hot-toast";
import { api } from "../config/api"; // Sua instância configurada
import "../App.css"; // Se tiver reset global
import Logo from '@/assets/img/logo-copia.png';

import { isUserAdmin } from "../utils/auth";

const Login: React.FC = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [loading, setLoading] = useState<boolean>(false);
  
    const isAdmin = isUserAdmin(); 

    // 2. ADICIONE ESSE BLOCO LOGO NO INÍCIO DO COMPONENTE
  useEffect(() => {
    // Verifica se já existe um tema salvo. Se NÃO existir, força o Light.
    const savedTheme = localStorage.getItem("theme");
    
    if (!savedTheme) {
      document.documentElement.classList.remove("dark"); // Remove classe dark do HTML
      localStorage.setItem("theme", "light"); // Salva light como padrão
    }
  }, []);
  

  const handleLogin = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!email || !password) {
      toast.error("Por favor, preencha todos os campos");
      return;
    }

    setLoading(true);
    const toastId = toast.loading("Fazendo login...");

    try {
      const response = await api.post('/login', { 
        email, 
        password 
      });

      const data = response.data;
      toast.dismiss(toastId);

      if (data.access_token) {
        localStorage.setItem("auth_token", data.access_token);
        localStorage.setItem("user_data", JSON.stringify(data.user)); // Opcional

        toast.success("Login realizado com sucesso!");

        // --- LÓGICA DE REDIRECIONAMENTO ---
        // Verifica se o role é 'candidato'. Se for, manda para Players.
        // Caso contrário (treinador/admin), manda para o Dashboard.
        if (data.user.role === 'candidato') {
            navigate(`/jogadores/${data.user.jogador_id}`);
        } else {
            navigate("/dashboard");
        }

      } else {
        toast.error("Erro inesperado: Token não recebido.");
      }

    } catch (err: any) {
      toast.dismiss(toastId);
      console.error(err);

      if (err.response) {
        const mensagemErro = err.response.data.message || "Email ou senha incorretos.";
        toast.error(mensagemErro);
      } else if (err.request) {
        toast.error("Não foi possível conectar ao servidor.");
      } else {
        toast.error("Ocorreu um erro ao tentar fazer login.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    // Fundo Azul Escuro (#14244D) cobrindo a tela inteira
    <div className="min-h-screen bg-[#14244D] flex items-center justify-center p-4">
      
      {/* Container Principal (Card) com Sombra */}
      <div className="flex flex-col md:flex-row w-full max-w-[800px] bg-white rounded-[10px] overflow-hidden shadow-[0px_15px_15px_rgba(0,0,0,0.5)]">
        
        {/* Lado Esquerdo (Branco com Logo) */}
        <div className="w-full md:w-1/2 bg-white p-10 flex flex-col justify-center items-center">
          {/* Certifique-se que a imagem está na pasta public/img/logo.png */}
          <img 
            src={Logo}
            alt="Logo Prudente" 
            className="w-[150px] mb-5 object-contain" 
          />
          <h2 className="text-4xl font-bold mb-2.5 text-[#851114]">GRÊMIO PRUDENTE</h2>
        </div>

        {/* Lado Direito (Vermelho #851114) */}
        <div className="w-full md:w-1/2 bg-[#851114] text-white p-10 flex flex-col justify-center">
          
          {/* Cabeçalho */}
          <h3 className="text-2xl font-bold m-0 mb-2.5 relative after:content-[''] after:block after:w-[300px] after:h-[1px] after:bg-white after:mt-[5px]">
            BEM VINDO DE VOLTA!
          </h3>
          <p className="mb-5 text-sm">Faça seu login</p>

          {/* Formulário */}
          <form onSubmit={handleLogin} className="w-full">
            
            <div className="mb-4 w-full">
              <input
                type="email"
                name="email"
                placeholder="Email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full p-2.5 border-none rounded-[5px] text-black focus:outline-none focus:ring-2 focus:ring-red-300"
              />
            </div>

            <div className="mb-4 w-full">
              <input
                type="password"
                name="password"
                placeholder="Senha"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full p-2.5 border-none rounded-[5px] text-black focus:outline-none focus:ring-2 focus:ring-red-300"
              />
            </div>

            {/* Botão Estilizado (Verde #16801b) */}
            <button
              type="submit"
              disabled={loading}
              className="
                group relative flex items-center justify-between 
                w-full max-w-[200px] mx-auto mt-4 px-2 py-1.5
                bg-[#16801b] border-[4px] border-black rounded-[15px]
                text-white font-bold cursor-pointer
                shadow-[4px_6px_0px_rgb(0,0,0)] 
                transition-all duration-200
                hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_3px_0px_black]
                active:saturate-75 disabled:opacity-70 disabled:cursor-not-allowed
              "
            >
              {/* Texto do Botão */}
              <div className="flex-1 flex justify-center items-center overflow-hidden">
                <span className="relative transition-transform duration-200 transform translate-x-0 group-hover:translate-x-2">
                  {loading ? "..." : "Login"}
                </span>
              </div>

              {/* Ícone da Seta (Arrow Container) */}
              <div className="
                p-[1px] border-[4px] border-black rounded-full bg-white 
                relative overflow-hidden z-10 w-[35px] h-[35px] flex items-center justify-center
                transition-transform duration-200 group-hover:translate-x-[2px]
              ">
                 {/* Fundo verde que desliza no hover da seta */}
                 <div className="absolute inset-0 bg-[#16801b] transform -translate-x-full transition-transform duration-200 group-hover:translate-x-0 -z-10"></div>
                 
                 {/* SVG Original */}
                 <svg width="18" height="18" viewBox="0 0 45 38" fill="none" xmlns="http://www.w3.org/2000/svg" className="z-20">
                    <path
                        d="M43.7678 20.7678C44.7441 19.7915 44.7441 18.2085 43.7678 17.2322L27.8579 1.32233C26.8816 0.34602 25.2986 0.34602 24.3223 1.32233C23.346 2.29864 23.346 3.88155 24.3223 4.85786L38.4645 19L24.3223 33.1421C23.346 34.1184 23.346 35.7014 24.3223 36.6777C25.2986 37.654 26.8816 37.654 27.8579 36.6777L43.7678 20.7678ZM0 21.5L42 21.5V16.5L0 16.5L0 21.5Z"
                        fill="black"
                    />
                </svg>
              </div>
            </button>
          </form>

          <a href="#" className="mt-4 text-xs text-[#c9c9c9] no-underline hover:text-white text-center block">
            Esqueceu a senha
          </a>

        </div>
      </div>
    </div>
  );
};

export default Login;