import React from 'react';
import Layout from '../components/layouts/LayoutForm';

// Mock data (viria do state ou rota)
const inscricao = {
  numero_inscricao: '2025-0042'
};

const Confirmacao: React.FC = () => {
  return (
    <Layout>
      {/* Background Balls Animation */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div className="absolute w-[60px] h-[60px] bg-white/10 rounded-full top-[20%] left-[10%] animate-float" style={{ animationDuration: '15s' }}></div>
        <div className="absolute w-[60px] h-[60px] bg-white/10 rounded-full top-[60%] right-[20%] animate-float" style={{ animationDuration: '18s', animationDelay: '2s' }}></div>
        <div className="absolute w-[60px] h-[60px] bg-white/10 rounded-full bottom-[20%] left-[30%] animate-float" style={{ animationDuration: '20s', animationDelay: '4s' }}></div>
      </div>

      {/* Confetti (simplificado com CSS puro ou use uma lib) */}
      <div className="absolute inset-0 pointer-events-none z-0">
         {/* Adicionar elementos de confete aqui se desejar, ou usar uma lib como 'react-confetti' */}
      </div>

      <div className="relative z-10 max-w-[500px] w-[90%] mx-auto mt-[50px] bg-white/95 backdrop-blur-md rounded-[20px] p-10 text-center shadow-[0_20px_60px_rgba(0,0,0,0.3)] animate-fadeIn">
        
        {/* Success Icon */}
        <div className="w-[80px] h-[80px] mx-auto mb-5 bg-gradient-to-br from-[#4CAF50] to-[#45a049] rounded-full flex items-center justify-center animate-scaleIn">
          <svg className="w-[35px] h-[35px] stroke-white stroke-[3] fill-none animate-drawCheck" viewBox="0 0 52 52">
            <path d="M14 27 L 22 35 L 38 16"></path>
          </svg>
        </div>

        <h1 className="text-[#851114] text-2xl font-bold mb-4 animate-fadeIn">Inscrição Confirmada!</h1>
        <p className="text-[#666] text-base mb-8 leading-relaxed animate-fadeIn">
          Parabéns! Sua inscrição na peneira foi realizada com sucesso.
          Prepare-se para mostrar seu talento!
        </p>

        <div className="bg-[#14244D] p-4 rounded-xl my-6 animate-fadeIn">
          <label className="block text-xs text-gray-400 mb-1 uppercase tracking-wide">Número de Inscrição</label>
          <div className="text-2xl font-bold text-white tracking-[2px]">
            {inscricao.numero_inscricao}
          </div>
        </div>

        <div className="w-[50px] h-[3px] bg-gradient-to-r from-[#667eea] to-[#764ba2] mx-auto my-6 rounded-full"></div>

        <div className="bg-black/5 rounded-[15px] p-5 my-6 text-left animate-fadeIn delay-500">
          <InfoItem icon="📧" title="Confirmação por E-mail" text="Enviamos todos os detalhes para seu e-mail cadastrado" />
          <InfoItem icon="📅" title="Data da Peneira" text="15 de Dezembro de 2024 - 08:00h" />
          <InfoItem icon="📍" title="Local" text="Centro de Treinamento - Campo Principal" />
          <InfoItem icon="👕" title="O que levar" text="Chuteira, caneleira, roupa esportiva e documento com foto" />
        </div>
      </div>

      <footer className="text-center p-5 mt-10 text-[#fff] text-sm relative z-10">
        <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
      </footer>
    </Layout>
  );
};

const InfoItem = ({ icon, title, text }: { icon: string, title: string, text: string }) => (
  <div className="flex items-center mb-4 last:mb-0">
    <div className="w-[40px] h-[40px] bg-white rounded-[10px] flex items-center justify-center mr-4 shrink-0 shadow-sm text-lg">
      {icon}
    </div>
    <div>
      <strong className="block text-[#333] text-sm mb-0.5">{title}</strong>
      <span className="text-[#666] text-xs leading-tight block">{text}</span>
    </div>
  </div>
);

export default Confirmacao;