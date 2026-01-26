import React, { FC } from 'react';
import styled from 'styled-components';

// Definindo o tipo para o componente, embora para um componente sem props
// como este, o uso de FC<object> ou apenas FC seja suficiente.
const Loader: FC = () => {
  return (
    <StyledWrapper>
      {/* O atributo viewBox define a área de visualização do SVG. 
        Aqui, estamos focando em uma área interna de 25x25 a 75x75.
      */}
      <svg viewBox="25 25 50 50">
        {/* O círculo tem um raio de 20, com centro em (50, 50) dentro do viewBox.
        */}
        <circle r={20} cy={50} cx={50} />
      </svg>
    </StyledWrapper>
  );
}

// Estilos usando styled-components
const StyledWrapper = styled.div`
  /* Estilos para o elemento SVG */
  svg {
    width: 3.25em;
    transform-origin: center;
    /* Animação de rotação constante */
    animation: rotate4 2s linear infinite;
  }

  /* Estilos para o elemento CIRCLE */
  circle {
    fill: none; /* Sem preenchimento interno */
    stroke: hsl(214, 97%, 59%); /* Cor do traço */
    stroke-width: 2; /* Espessura do traço */
    /* Define o padrão de traços e espaços. Inicialmente um traço de 1 e um espaço de 200 (quase o círculo inteiro) */
    stroke-dasharray: 1, 200; 
    stroke-dashoffset: 0; /* Ponto de início do traço */
    stroke-linecap: round; /* Terminais arredondados */
    /* Animação que faz o traço crescer e encolher */
    animation: dash4 1.5s ease-in-out infinite;
  }

  /* Keyframes para a rotação do SVG */
  @keyframes rotate4 {
    100% {
      transform: rotate(360deg);
    }
  }

  /* Keyframes para a animação do traço do círculo */
  @keyframes dash4 {
    /* Início: Traço pequeno (1) */
    0% {
      stroke-dasharray: 1, 200;
      stroke-dashoffset: 0;
    }

    /* Meio: Traço longo (90), deslocamento para frente */
    50% {
      stroke-dasharray: 90, 200;
      stroke-dashoffset: -35px;
    }

    /* Fim: Traço volta a ser pequeno (devido à redefinição implícita), deslocamento para trás */
    100% {
      stroke-dashoffset: -125px;
    }
  }
`;

export default Loader;