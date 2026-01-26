import React from 'react';
import Logo from '@/assets/img/logo-copia.png';
// 1. Importe a imagem aqui
import FundoPrudentao from '@/assets/img/prudentao.png'; 

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  return (
    <div className="min-h-screen relative overflow-x-hidden font-['Segoe_UI'] text-[#333] leading-relaxed">
      {/* Background Image com Overlay */}
      <div 
        className="fixed inset-0 z-[-1] bg-cover bg-center bg-no-repeat"
        style={{
          // 2. Use a variável importada dentro da template string
          backgroundImage: `linear-gradient(rgba(255, 255, 255, 0.9), rgba(156, 156, 156, 0.9)), url(${FundoPrudentao})`
        }}
      />

      <header className="bg-transparent text-white py-5 text-center relative z-10">
        <div className="max-w-[1200px] mx-auto px-5">
          <img 
            src={Logo}
            alt="Logo Prudente" 
            className="h-[20vh] md:h-[30vh] mx-auto mb-1 object-contain"
          />
          <h1 className="text-[2rem] md:text-[2.5rem] font-bold uppercase tracking-[2px] text-[#941B1B] font-sans">
            GRÊMIO PRUDENTE
          </h1>
          <h2 className="text-[1.1rem] md:text-[1.3rem] font-normal mb-8 text-[#d9d9d9] bg-[#14244D] px-4 py-1 inline-block rounded-sm">
            Seleção de jogadores
          </h2>
        </div>
      </header>

      <main className="relative z-10 pb-10">
        {children}
      </main>
    </div>
  );
};

export default Layout;