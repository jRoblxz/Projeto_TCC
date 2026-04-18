import React from "react";
import styled from "styled-components";
// Importando a logo do Fatec Prudente exatamente como você usa nos outros arquivos
import Logo from "@/assets/img/logo-copia.png";

const Loader: React.FC = () => {
  return (
    <StyledWrapper>
      <div className="loader">
        <div className="box">
          <div className="logo">
            <img src={Logo} alt="Fatec Prudente" className="logo-img" />
          </div>
        </div>
        <div className="box" />
        <div className="box" />
        <div className="box" />
        <div className="box" />
      </div>
    </StyledWrapper>
  );
};

const StyledWrapper = styled.div`
  .loader {
    --size: 250px;
    --duration: 2s;

    /* Cores Oficiais do Fatec Prudente */
    --color-blue: rgba(20, 36, 77, 1); /* #14244D */
    --color-red: rgba(133, 17, 20, 1); /* #851114 */

    /* Fundo dos círculos com um tom azul bem transparente */
    --background: linear-gradient(
      0deg,
      rgba(20, 36, 77, 0.1) 0%,
      rgba(20, 36, 77, 0.25) 100%
    );

    height: var(--size);
    aspect-ratio: 1;
    position: relative;
  }

  .loader .box {
    position: absolute;
    background: var(--background);
    border-radius: 50%;
    /* Borda superior principal em vermelho */
    border-top: 2px solid var(--color-red);
    box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 10px -0px;
    backdrop-filter: blur(3px);
    animation: ripple var(--duration) infinite ease-in-out;
  }

  .loader .box:nth-child(1) {
    inset: 40%;
    z-index: 99;
  }

  /* Alternando as bordas entre Vermelho e Azul para dar um efeito do clube */
  .loader .box:nth-child(2) {
    inset: 30%;
    z-index: 98;
    border-color: rgba(133, 17, 20, 0.9); /* Vermelho */
    animation-delay: 0.2s;
  }

  .loader .box:nth-child(3) {
    inset: 20%;
    z-index: 97;
    border-color: rgba(20, 36, 77, 0.8); /* Azul */
    animation-delay: 0.4s;
  }

  .loader .box:nth-child(4) {
    inset: 10%;
    z-index: 96;
    border-color: rgba(133, 17, 20, 0.6); /* Vermelho */
    animation-delay: 0.6s;
  }

  .loader .box:nth-child(5) {
    inset: 0%;
    z-index: 95;
    border-color: rgba(20, 36, 77, 0.4); /* Azul */
    animation-delay: 0.8s;
  }

  .loader .logo {
    position: absolute;
    inset: 0;
    display: grid;
    place-content: center;
    /* Ajuste o padding se a imagem ficar muito grande ou pequena */
    padding: 15%;
  }

  .loader .logo-img {
    width: 100%;
    height: auto;
    object-fit: contain;
    /* Como é uma imagem, animamos o tamanho (pulso) em vez da cor */
    animation: pulse var(--duration) infinite ease-in-out;
  }

  @keyframes ripple {
    0% {
      transform: scale(1);
      box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 10px -0px;
    }
    50% {
      transform: scale(1.3);
      box-shadow: rgba(0, 0, 0, 0.2) 0px 30px 20px -0px;
    }
    100% {
      transform: scale(1);
      box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 10px -0px;
    }
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
      opacity: 0.8;
    }
    50% {
      transform: scale(1.15);
      opacity: 1;
    }
    100% {
      transform: scale(1);
      opacity: 0.8;
    }
  }
`;

export default Loader;
