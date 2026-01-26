import React from 'react';
import Layout from '../components/layouts/LayoutForm';
import { useNavigate } from 'react-router-dom'; // Assumindo react-router

const Instrucao: React.FC = () => {
  const navigate = useNavigate();

  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto px-5 relative -top-[40px] md:-top-[80px]">
        <h2 className="text-center my-10 text-[2rem] text-[#941B1B] relative after:content-[''] after:block after:w-[80px] after:h-[4px] after:bg-[#941B1B] after:mx-auto after:mt-2 after:rounded-sm">
          Como funciona?
        </h2>

        <div className="flex flex-col md:flex-row justify-between gap-8 mb-12">
          {/* Card 1 */}
          <StepCard 
            number="01" 
            img="/img/google-forms.png" 
            title="Inscrição online" 
            desc="Preencha o formulário com seus dados pessoais, posição, características e anexo dos documentos solicitados." 
          />
          
          {/* Card 2 */}
          <StepCard 
            number="02" 
            img="/img/football.png" 
            title="Jogos" 
            desc="O jogador passará por uma primeira avaliação por meio de um vídeo de apresentação e depois após habilitação para algum dos times da equipe." 
          />

          {/* Card 3 */}
          <StepCard 
            number="03" 
            img="/img/defensive-wall.png" 
            title="Avaliação" 
            desc="Nossa equipe irá avaliar criteriosamente cada etapa do processo e irá selecionar os que chamam atenção." 
          />
        </div>

        <div className="text-center py-16 px-5 bg-[#14244D] text-white rounded-xl max-w-[800px] mx-auto">
          <p className="mb-5 text-lg">Visite nosso site para mais informações:</p>
          <button 
            onClick={() => navigate('/inscricao')} 
            className="text-[1.5rem] md:text-[2rem] my-5 text-white no-underline inline-block px-6 py-2 bg-white/10 rounded hover:bg-white/20 hover:scale-105 transition-all duration-300"
          >
            INSCREVA-SE
          </button>
          <div className="text-[2rem] md:text-[3rem] font-bold tracking-[3px] uppercase mt-4">
            PRUDENTE
          </div>
        </div>

        <footer className="text-center p-5 mt-10 text-[#666] text-sm">
          <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
        </footer>
      </div>
    </Layout>
  );
};

// Componente auxiliar para os Cards de Passo
const StepCard = ({ number, img, title, desc }: { number: string, img: string, title: string, desc: string }) => (
  <div className="flex-1 min-w-[280px] bg-[#941B1B] rounded-tr-[75px] rounded-bl-[75px] pt-6 pl-5 shadow-[-5px_5px_20px_5px_rgba(0,0,0,0.5)]">
    <div className="bg-[#14244D] rounded-tr-[50px] rounded-bl-[50px] p-6 text-center shadow-[-5px_0_10px_-5px_rgba(0,0,0,0.5)] h-full">
      <div className="flex justify-start gap-[100px] md:gap-[140px] mb-4">
        <div className="text-[3rem] font-bold text-[#d0d0d0] -ml-6 -mt-6 bg-[#941B1B] pl-5 pr-2 w-[100px] text-left">
          {number}
        </div>
        <img src={img} alt="Icon" className="w-[60px] h-[60px] -mt-2 object-contain" />
      </div>
      <h3 className="text-[1.5rem] mb-4 text-[#941B1B] inline-block px-4 py-1 relative after:content-[''] after:block after:w-[150px] after:h-[1px] after:bg-[#f1f1f1] after:mx-auto after:mt-2">
        {title}
      </h3>
      <div className="text-[#efefef] text-justify text-sm md:text-base leading-relaxed">
        <p>{desc}</p>
        <strong className="text-transparent">.</strong>
      </div>
    </div>
  </div>
);

export default Instrucao;