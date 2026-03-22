import React from 'react';
import Layout from '@/components/layouts/LayoutSiteGremio'; // O NOVO LAYOUT OFICIAL
import { useNavigate } from 'react-router-dom';

// Imagens
import Form from '@/assets/img/google-forms.png';
import Jogos from '@/assets/img/football.png';
import Avaliacao from '@/assets/img/defensive-wall.png';
import Logo from '@/assets/img/logo-copia.png'; // Trazendo a logo de volta!

const Instrucao: React.FC = () => {
  const navigate = useNavigate();

  return (
    <Layout>
      {/* A mágica acontece aqui: -mt-20 ou -mt-32 "puxa" esse conteúdo 
        para cima do banner do estádio, criando um efeito 3D sem entrar debaixo do menu branco 
      */}
      <div className="max-w-[1200px] mx-auto px-5 relative -mt-[100px] md:-mt-[220px] z-20 pb-20">
        
        {/* === CARD DE BOAS VINDAS (Substitui o seu antigo header) === */}
        <div className="bg-white/95 backdrop-blur-sm rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] p-8 md:p-12 mb-16 border-b-8 border-[#941B1B] text-center">
          <img 
            src={Logo} 
            alt="Logo Prudente" 
            className="h-28 md:h-40 mx-auto mb-4 object-contain drop-shadow-md hover:scale-105 transition-transform" 
          />
          <h1 className="text-3xl md:text-5xl font-extrabold uppercase tracking-widest text-[#14244D] mb-3">
            Grêmio Prudente
          </h1>
          <h2 className="text-lg md:text-xl font-bold bg-[#941B1B] text-white px-8 py-2.5 inline-block rounded-full uppercase tracking-wider mb-6 shadow-md">
            Seleção de Jogadores
          </h2>
          <p className="text-gray-600 max-w-3xl mx-auto text-base md:text-lg font-medium leading-relaxed">
            Bem-vindo ao portal oficial de captação de talentos. Siga as instruções abaixo para realizar a sua inscrição e participar das peneiras do maior clube do Oeste Paulista.
          </p>
        </div>

        {/* === SESSÃO COMO FUNCIONA === */}
        <h2 className="text-center my-12 text-[2rem] font-extrabold text-[#14244D] uppercase relative after:content-[''] after:block after:w-[80px] after:h-[5px] after:bg-[#941B1B] after:mx-auto after:mt-3 after:rounded-sm">
          Como funciona?
        </h2>

        <div className="flex flex-col md:flex-row justify-between gap-8 mb-16">
          <StepCard 
            number="01" 
            img={Form}
            title="Inscrição online" 
            desc="Preencha o formulário com seus dados pessoais, posição, características e anexe os documentos solicitados." 
          />
          <StepCard 
            number="02" 
            img={Jogos}
            title="Avaliação Técnica" 
            desc="Apresente-se no dia e local marcados com antecedência para a realização dos testes práticos com nossos olheiros." 
          />
          <StepCard 
            number="03" 
            img={Avaliacao}
            title="Resultado" 
            desc="Os atletas que se destacarem serão contatados pela nossa comissão técnica para a próxima fase do processo." 
          />
        </div>

        {/* === SESSÃO DE AVISOS / REGRAS === */}
        <div className="bg-[#14244D] rounded-3xl p-8 md:p-12 text-white shadow-xl flex flex-col md:flex-row items-center gap-10">
          <div className="flex-1">
            <h3 className="text-2xl md:text-3xl font-bold text-yellow-400 mb-4 uppercase flex items-center gap-3">
              <span className="text-4xl">⚠️</span> Atenção Atleta
            </h3>
            <ul className="space-y-4 text-gray-200 font-medium">
              <li className="flex items-start gap-3">
                <div className="w-2 h-2 rounded-full bg-[#941B1B] mt-2 shrink-0"></div>
                <p>É <strong>obrigatória</strong> a apresentação do RG original ou certidão de nascimento no dia do teste.</p>
              </li>
              <li className="flex items-start gap-3">
                <div className="w-2 h-2 rounded-full bg-[#941B1B] mt-2 shrink-0"></div>
                <p>Compareça com material de treino completo: chuteira, meião, calção e camisa (não é permitido usar camisas de outros clubes).</p>
              </li>
              <li className="flex items-start gap-3">
                <div className="w-2 h-2 rounded-full bg-[#941B1B] mt-2 shrink-0"></div>
                <p>Obrigatório a apresentação de Atestado Médico de aptidão física recente (emitido há menos de 6 meses).</p>
              </li>
            </ul>
          </div>
          
          <div className="shrink-0 w-full md:w-auto">
            <button 
              onClick={() => navigate('/inscricao')}
              className="w-full md:w-auto bg-[#941B1B] hover:bg-[#b02222] text-white text-xl font-extrabold uppercase tracking-wide py-5 px-10 rounded-xl shadow-[0_5px_15px_rgba(148,27,27,0.4)] hover:-translate-y-1 transition-all duration-300"
            >
              Fazer Minha Inscrição
            </button>
          </div>
        </div>

      </div>
    </Layout>
  );
};

// === COMPONENTE DOS CARDS MANTENDO SUA IDENTIDADE VISUAL ===
const StepCard = ({ number, img, title, desc }: { number: string, img: string, title: string, desc: string }) => (
  <div className="flex-1 min-w-[280px] bg-[#941B1B] rounded-tr-[50px] rounded-bl-[50px] pt-4 pl-4 shadow-lg hover:-translate-y-2 transition-transform duration-300">
    <div className="bg-[#14244D] rounded-tr-[40px] rounded-bl-[40px] p-8 text-center shadow-inner h-full flex flex-col relative overflow-hidden group">
      
      {/* O número gigante de fundo */}
      <div className="absolute -top-4 -right-0.5 text-[5rem] font-black text-gray-100 group-hover:text-gray-200 transition-colors z-0 select-none mr-5">
        {number}
      </div>

      <div className="z-10 flex flex-col h-full">
        <div className="w-20 h-20  rounded-full flex items-center justify-center mb-6 shadow-sm ">
          <img src={img} alt={title} className="w-12 h-12 object-contain" />
        </div>
        
        <h3 className="text-gray-300 text-xl font-extrabold uppercase mb-3 text-left">
          <span className="text-[#941B1B] mr-2">{number}.</span> {title}
        </h3>
        
        <p className="text-gray-600 text-sm text-left leading-relaxed font-medium">
          {desc}
        </p>
      </div>
    </div>
  </div>
);

export default Instrucao;