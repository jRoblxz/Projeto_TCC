import React, { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { Search, Menu, X } from "lucide-react";

import { SocialIcon } from 'react-social-icons'
// 1. IMPORTANDO A IMAGEM DE FUNDO DO ESTÁDIO
import FundoPrudentao from "@/assets/img/prudentao.png";

interface LayoutProps {
  children: React.ReactNode;
}

const LayoutSiteGremio: React.FC<LayoutProps> = ({ children }) => {
  const navigate = useNavigate();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const navLinks = [
    { name: "O Time", href: "https://www.gdprudente.com.br/historia/" },
    { name: "Notícias", href: "https://www.gdprudente.com.br/noticias/" },
    {
      name: "Loja",
      href: "https://www.futfanatics.com.br/clubes-brasileiros/sao-paulo?loja=311840&categoria=60",
    },
    { name: "Jogos", href: "https://www.gdprudente.com.br/jogos/" },
    {
      name: "Patrocinadores",
      href: "https://www.gdprudente.com.br/patrocinadores/",
    },
    { name: "Contato", href: "https://www.gdprudente.com.br/contato/" },
    { name: "Projetos", href: "https://www.gdprudente.com.br/projetos/" },
    { name: "Sócio Torcedor", href: "https://socio.gdprudente.com.br/" },
  ];

  const patrocinadores = [
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2020/08/futfanatics.png",
      alt: "FutFanatics",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/liano-branco.png",
      alt: "Liane",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2024/01/244x100-branca-2.png",
      alt: "Tray",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2022/01/200x100-oestesaude.png",
      alt: "Oeste Saúde",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/ANDORINHABRANCO-1.png",
      alt: "Andorinha",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2022/01/200x100-unoeste.png",
      alt: "Unoeste",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/LIDERBRANCO-1.png",
      alt: "Lider",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/RIVALOBRANCO-1.png",
      alt: "Rivalo",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/exportarbranco.png",
      alt: "Funada",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2020/08/kappa.png",
      alt: "Kappa",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2026/02/COUREBRANCO.png",
      alt: "Cuore",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2024/01/244x100-BRANCA_.png",
      alt: "Energisa",
    },
    {
      url: "https://www.gdprudente.com.br/wp-content/uploads/2020/10/medrad.png",
      alt: "Med Rad",
    },
  ];

  return (
    // Removido o bg-[#F3F3F3] para o fundo transparente e adicionado relative
    <div className="min-h-screen flex flex-col font-sans relative overflow-x-hidden">
      {/* 2. BACKGROUND IMAGE COM OVERLAY CLARINHO (FIXO NA TELA INTEIRA) */}
      <div
        className="fixed inset-0 z-[-1] bg-cover bg-center bg-no-repeat"
        style={{
          backgroundImage: `linear-gradient(rgba(255, 255, 255, 0.9), rgba(240, 240, 240, 0.9)), url(${FundoPrudentao})`,
        }}
      />

      {/* ================= HEADER ORIGINAL GRÊMIO ================= */}
      <header className="bg-white border-b-4 border-[#941B1B] shadow-md sticky top-0 z-50">
        <div className="max-w-[1400px] mx-auto px-4 h-24 flex items-center justify-between">
          <a
            href="https://www.gdprudente.com.br"
            className="flex-shrink-0 cursor-pointer"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="160"
              height="66"
              viewBox="0 0 124.727 52"
            >
              <g transform="translate(-898 -1029)">
                <g transform="translate(725.124 896.169)">
                  <g transform="translate(223.603 160.842)">
                    <path
                      d="M336.456,184.831c-.45-.534-5.254-6.224-5.254-6.228h-.574a1.405,1.405,0,0,0-1.551,1.505v7.6h2.163l-.025-6.228c1.754,2.08,3.5,4.149,5.254,6.227h.574a1.4,1.4,0,0,0,1.55-1.505V178.6h-2.162Z"
                      transform="translate(-282.564 -178.603)"
                      fill="#9c100a"
                    />
                    <path
                      d="M381.425,185.635l-5.276-.007a.358.358,0,0,1-.341-.318V184.2h3.7l-.03-2.089H375.8l0-1.2a.241.241,0,0,1,.222-.224h5.4l-.009-2.086h-6.261a1.476,1.476,0,0,0-1.585,1.505l.016,5.267a2.542,2.542,0,0,0,2.5,2.338h5.357Q381.436,186.587,381.425,185.635Z"
                      transform="translate(-307.435 -178.604)"
                      fill="#9c100a"
                    />
                    <path
                      d="M352.929,180.676h3.013l-.066,7.038h2.155l.039-7.038h2.916l.008-2.072H352.93Z"
                      transform="translate(-295.899 -178.603)"
                      fill="#9c100a"
                    />
                    <path
                      d="M311.717,185.628a.358.358,0,0,1-.34-.318V184.2h3.7l-.03-2.089h-3.681l0-1.2a.242.242,0,0,1,.222-.224h5.4l-.009-2.086h-6.261a1.413,1.413,0,0,0-1.585,1.505l.015,5.267a2.543,2.543,0,0,0,2.5,2.338H317q0-1.127-.01-2.079Z"
                      transform="translate(-271.416 -178.604)"
                      fill="#9c100a"
                    />
                    <path
                      d="M293.449,178.6h-4.97a1,1,0,0,0-1.067.991l.03,8.119h6.975a1.467,1.467,0,0,0,1.578-1.505c0-1.756,0-3.514-.016-5.27A2.539,2.539,0,0,0,293.449,178.6Zm.3,7.024h-4.06l0-4.938h3.721a.358.358,0,0,1,.34.318C293.745,182.548,293.788,184.089,293.747,185.628Z"
                      transform="translate(-259.273 -178.604)"
                      fill="#9c100a"
                    />
                    <path
                      d="M252.142,184.764a1.5,1.5,0,0,0,1.572-1.5c0-.848,0-1.549-.007-2.324a2.54,2.54,0,0,0-2.474-2.335h-4.417a1.453,1.453,0,0,0-1.559,1.5l.011,7.6h2.257l-.023-2.944,1.4.008,2.484,2.95h2.409l-2.474-2.95Zm-4.535-2.114q-.034-1.743-.034-1.743a.225.225,0,0,1,.209-.223h3.485a.37.37,0,0,1,.34.318q-.028,1.459-.029,1.459a.2.2,0,0,1-.23.189Z"
                      transform="translate(-235.708 -178.604)"
                      fill="#9c100a"
                    />
                    <path
                      d="M232.181,180.939a2.551,2.551,0,0,0-2.509-2.335h-4.255c-1.034,0-1.674.284-1.814,1.505.005,2.535,0,5.07.006,7.6h2.282l-.014-2.964s4.068.026,4.728.027a1.68,1.68,0,0,0,1.577-1.507C232.18,182.422,232.185,181.716,232.181,180.939Zm-2.16,1.529a.2.2,0,0,1-.23.189h-3.808l-.026-1.743a.216.216,0,0,1,.215-.224h3.525a.36.36,0,0,1,.348.318Z"
                      transform="translate(-223.603 -178.604)"
                      fill="#9c100a"
                    />
                    <path
                      d="M273.825,178.6a1.466,1.466,0,0,0-1.552,1.47q0,2.763,0,5.526h-3.553a.187.187,0,0,1-.208-.2c.018-2.268.038-4.532.04-6.8h-2.181l-.013,7.6a1.454,1.454,0,0,0,1.552,1.512h6.509q0-4.56,0-9.11Z"
                      transform="translate(-247.504 -178.604)"
                      fill="#9c100a"
                    />
                  </g>
                  <g transform="translate(223.603 146.831)">
                    <path
                      d="M253.051,154.515a1.505,1.505,0,0,0,1.572-1.5c0-.847,0-1.549-.007-2.324a2.54,2.54,0,0,0-2.474-2.335h-4.417a1.453,1.453,0,0,0-1.559,1.5l.011,7.6h2.257l-.023-2.944,1.4.007,2.484,2.95H254.7l-2.474-2.95Zm-4.534-2.114q-.034-1.743-.034-1.743a.225.225,0,0,1,.208-.224h3.486a.37.37,0,0,1,.34.318q-.028,1.459-.029,1.459a.2.2,0,0,1-.231.189Z"
                      transform="translate(-236.216 -147.683)"
                      fill="#9c100a"
                    />
                    <path
                      d="M270.07,154.527a.358.358,0,0,1-.34-.318V153.1h3.7l-.03-2.089h-3.681l0-1.2a.241.241,0,0,1,.222-.224h5.4l-.009-2.086h-1.911a2.812,2.812,0,0,0-3.649,0h-.7a1.413,1.413,0,0,0-1.585,1.505l.015,5.268a2.543,2.543,0,0,0,2.5,2.337h5.357q0-1.127-.01-2.079Z"
                      transform="translate(-248.135 -146.831)"
                      fill="#9c100a"
                    />
                    <path
                      d="M323.254,157.9h5.064a1.467,1.467,0,0,0,1.578-1.506c0-1.756,0-3.514-.016-5.27a2.54,2.54,0,0,0-2.53-2.335h-4.97a1,1,0,0,0-1.067.991l.023,6.208A1.918,1.918,0,0,0,323.254,157.9Zm4.053-7.024a.358.358,0,0,1,.34.318c0,1.54.043,3.08,0,4.62h-4.06l0-4.938Z"
                      transform="translate(-278.225 -147.926)"
                      fill="#9c100a"
                    />
                    <path
                      d="M223.6,149.781l.023,6.208a1.918,1.918,0,0,0,1.918,1.911h5.064a1.467,1.467,0,0,0,1.578-1.506v-3.751h-5.83v1.94l3.478-.007.007,1.227-3.964.011,0-4.938h6.31V148.79h-7.516A1,1,0,0,0,223.6,149.781Z"
                      transform="translate(-223.603 -147.926)"
                      fill="#9c100a"
                    />
                    <path
                      d="M290.267,152.644l1.926,4.257a.7.7,0,0,0,1.257,0l1.913-4.255V157.9h2.158v-9.11h-2.365l-2.45,5.022-2.454-5.022h-.574a1.406,1.406,0,0,0-1.55,1.506v7.6h2.163Z"
                      transform="translate(-259.673 -147.926)"
                      fill="#9c100a"
                    />
                    <path
                      d="M314.653,148.79h-.59a1.405,1.405,0,0,0-1.55,1.506v7.6h2.162Z"
                      transform="translate(-273.305 -147.926)"
                      fill="#9c100a"
                    />
                  </g>
                </g>
                <g transform="translate(898.039 1029)">
                  <path
                    d="M19.226,11.682a9.275,9.275,0,0,1,9.333,10.453l-3.951,2.753-.4-.809c.264-.56.389-.809.653-1.369l1.2-.28a.6.6,0,0,0,.467-.544l.062-1.182a.589.589,0,0,0-.4-.591l-1.167-.389c-.218-.591-.311-.84-.513-1.431l.653-1.042a.609.609,0,0,0-.062-.716l-.793-.871a.6.6,0,0,0-.7-.124l-1.089.544c-.56-.264-.809-.389-1.369-.653l-.28-1.2a.6.6,0,0,0-.544-.467l-1.2-.062a.589.589,0,0,0-.591.4l-.389,1.167c-.591.218-.84.311-1.431.513l-1.027-.653a.611.611,0,0,0-.716.062l-.871.793a.591.591,0,0,0-.124.7l.544,1.1c-.264.529-.373.793-.622,1.291l-2.94-2.053A9.286,9.286,0,0,1,19.226,11.682ZM19.319,18,21.9,19.428V22.4l-1.727,1.027L16.83,21.155V19.49ZM3.593,11.884V3.593H7.186V7.186a1.806,1.806,0,0,0,1.8,1.8h7.186a1.806,1.806,0,0,0,1.8-1.8V3.593h3.593V7.186a1.806,1.806,0,0,0,1.8,1.8h7.186a1.806,1.806,0,0,0,1.8-1.8V3.593h3.562V16.955l3.593-2.52V1.8a1.806,1.806,0,0,0-1.8-1.8H30.534a1.806,1.806,0,0,0-1.8,1.8V5.4H25.137V1.8a1.806,1.806,0,0,0-1.8-1.8H16.146a1.806,1.806,0,0,0-1.8,1.8V5.4H10.78V1.8A1.8,1.8,0,0,0,8.991,0H1.8A1.806,1.806,0,0,0,0,1.8V9.38l2.193,1.54Z"
                    transform="translate(-0.033)"
                    fill="#9c1006"
                  />
                  <path
                    d="M-.033,88.4v19.988A5.619,5.619,0,0,0,.5,111.577a10.291,10.291,0,0,0,3.1,2.6l10.437-7.264-6.844-4.791v5.118l-3.624,2.535V95.2l13.611,9.52,3.049-2.131Zm22.508,30.145,13.984-9.8c1.9-1.338,3.033-2.1,3.033-4.526V93.471L6.827,116.352l14.715,10.3v-4.386L13.1,116.352l22.8-15.975v4.386l-16.55,11.588Z"
                    transform="translate(0 -74.649)"
                    fill="#18274d"
                  />
                </g>
              </g>
            </svg>
          </a>

          <nav className="hidden lg:flex items-center gap-6">
            {navLinks.map((link) => (
              <a
                key={link.name}
                href={link.href}
                target="_blank"
                rel="noreferrer"
                className="text-[#18274d] font-bold uppercase text-[13px] tracking-wide hover:border-b-[3px] hover:border-[#941B1B] transition-all pb-1"
              >
                {link.name}
              </a>
            ))}
            <Link
              to="/instrucoes"
              className="text-[#941B1B] font-extrabold uppercase text-[13px] tracking-wide hover:text-[#18274d] hover:border-b-[3px] hover:border-[#941B1B] transition-all pb-1"
            >
              Peneiras
            </Link>
            
          </nav>

          <div className="flex items-center gap-4">
            <button className="hidden lg:flex p-2 hover:bg-gray-100 rounded-full transition">
              <Search className="text-[#18274d]" size={22} />
            </button>
            <button
              className="lg:hidden p-2 text-[#18274d]"
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            >
              {isMobileMenuOpen ? <X size={28} /> : <Menu size={28} />}
            </button>
          </div>
        </div>

        {isMobileMenuOpen && (
          <div className="lg:hidden bg-white border-t border-gray-200 absolute w-full left-0 top-24 shadow-xl z-50">
            <div className="flex flex-col px-4 py-4 space-y-4">
              <Link
                to="/instrucoes"
                className="text-[#941B1B] font-bold uppercase text-sm border-b border-gray-100 pb-2"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                Peneiras
              </Link>
              {navLinks.map((link) => (
                <a
                  key={link.name}
                  href={link.href}
                  className="text-[#18274d] font-bold uppercase text-sm border-b border-gray-100 pb-2"
                >
                  {link.name}
                </a>
              ))}
            </div>
          </div>
        )}
      </header>

      {/* ================= BANNER DE FUNDO (A IMAGEM COM DEGRADE ESCURO/VERMELHO) ================= */}
      <div
        className="w-full h-[250px] md:h-[350px] bg-cover bg-center bg-no-repeat relative"
        style={{
          backgroundImage: `url('https://www.gdprudente.com.br/wp-content/themes/gremio_prudente_theme/images/banner_home/bannertopohome_desk.jpg')`,
        }}
      >
        <div className="absolute inset-0 bg-[#18274d]/30"></div>
      </div>

      {/* ================= CONTEÚDO DA PÁGINA ================= */}
      <main className="flex-1 w-full relative z-10 flex flex-col">
        {children}
      </main>

      {/* ================= FOOTER ORIGINAL GRÊMIO ================= */}
      <footer className="bg-[#18274d] mt-auto relative z-20">
        <div className="max-w-[1200px] mx-auto px-5 py-12">
          <div className="flex items-center justify-center gap-4 mb-10">
            <svg
              width="40"
              height="40"
              viewBox="0 0 52 52"
              className="opacity-80"
            >
              <path
                d="M19.319,18,21.9,19.428V22.4l-1.727,1.027L16.83,21.155V19.49ZM3.593,11.884V3.593H7.186V7.186a1.806,1.806,0,0,0,1.8,1.8h7.186a1.806,1.806,0,0,0,1.8-1.8V3.593h3.593V7.186a1.806,1.806,0,0,0,1.8,1.8h7.186a1.806,1.806,0,0,0,1.8-1.8V3.593h3.562V16.955l3.593-2.52V1.8a1.806,1.806,0,0,0-1.8-1.8H30.534a1.806,1.806,0,0,0-1.8,1.8V5.4H25.137V1.8a1.806,1.806,0,0,0-1.8-1.8H16.146a1.806,1.806,0,0,0-1.8,1.8V5.4H10.78V1.8A1.8,1.8,0,0,0,8.991,0H1.8A1.806,1.806,0,0,0,0,1.8V9.38l2.193,1.54Z"
                fill="#ffffff"
                opacity="0.6"
              />
              <path
                d="M-.033,88.4v19.988A5.619,5.619,0,0,0,.5,111.577a10.291,10.291,0,0,0,3.1,2.6l10.437-7.264-6.844-4.791v5.118l-3.624,2.535V95.2l13.611,9.52,3.049-2.131Zm22.508,30.145,13.984-9.8c1.9-1.338,3.033-2.1,3.033-4.526V93.471L6.827,116.352l14.715,10.3v-4.386L13.1,116.352l22.8-15.975v4.386l-16.55,11.588Z"
                transform="translate(0 -74.649)"
                fill="#ffffff"
                opacity="0.6"
              />
            </svg>
            <h3 className="text-white text-2xl font-bold tracking-widest opacity-80">
              PATROCINADORES
            </h3>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 items-center justify-items-center mb-12">
            {patrocinadores.map((pat, index) => (
              <img
                key={index}
                src={pat.url}
                alt={pat.alt}
                className="max-w-[120px] max-h-[60px] object-contain opacity-70 hover:opacity-100 transition-opacity cursor-pointer filter grayscale hover:grayscale-0"
              />
            ))}
          </div>
          <div className="border-t border-white/10 pt-8 flex flex-col items-center justify-center text-center gap-6">
            {/* Redes Sociais */}
            <div className="flex justify-center gap-4">
              
              
              <SocialIcon  bgColor="white" fgColor="transparent" network="x" url="https://X.com/gdprudente" />
              
              <SocialIcon bgColor="white" fgColor="transparent" network="facebook" url="https://www.facebook.com/GremioDPrudenteOficial/" />
              
              <SocialIcon bgColor="white" fgColor="transparent" network="instagram" url="https://www.instagram.com/gremioprudenteoficial/" />
           
            </div>

            {/* Texto de Direitos Autorais */}
            <p className="text-white/60 text-sm">
              Grêmio Prudente &copy; 2025. Todos os direitos reservados.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default LayoutSiteGremio;
