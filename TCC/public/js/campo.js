// Executar quando o DOM estiver completamente carregado
document.addEventListener('DOMContentLoaded', function () {

    // Definindo as coordenadas das posições no campo (em percentual)
    const fieldPositions = {
        'goleiro': { top: 85, left: 50 },
        'zagueiro-central': { top: 75, left: 50 },
        'zagueiro-direito': { top: 75, left: 70 },
        'zagueiro-esquerdo': { top: 75, left: 30 },
        'lateral-direito': { top: 60, left: 85 },
        'lateral-esquerdo': { top: 60, left: 15 },
        'volante': { top: 60, left: 50 },
        'meio-central': { top: 45, left: 50 },
        'meio-direito': { top: 45, left: 70 },
        'meio-esquerdo': { top: 45, left: 30 },
        'atacante-central': { top: 30, left: 50 },
        'ponta-direita': { top: 25, left: 80 },
        'ponta-esquerda': { top: 25, left: 20 },
        'centroavante': { top: 15, left: 50 }
    };

    /**
     * Encontra as coordenadas de uma posição com base no nome.
     * A função agora é mais robusta para lidar com variações de nomes.
     * @param {string} position - O nome da posição vindo do banco de dados.
     * @returns {object|null} - O objeto com as coordenadas {top, left} ou null se não encontrar.
     */
    function findCoordinates(position) {
        if (!position) return null;

        // 1. Tenta a busca exata primeiro (o caso ideal)
        if (fieldPositions[position]) {
            return fieldPositions[position];
        }

        // 2. Lógica de busca por palavras-chave (se a busca exata falhar)
        // A ordem aqui é importante para evitar conflitos (ex: "zagueiro direito" antes de "zagueiro")
        if (position.includes('goleiro')) return fieldPositions['goleiro'];

        if (position.includes('zagueiro')) {
            if (position.includes('direito')) return fieldPositions['zagueiro-direito'];
            if (position.includes('esquerdo')) return fieldPositions['zagueiro-esquerdo'];
            return fieldPositions['zagueiro-central']; // Padrão para "zagueiro"
        }

        if (position.includes('lateral')) {
            if (position.includes('direito')) return fieldPositions['lateral-direito'];
            if (position.includes('esquerdo')) return fieldPositions['lateral-esquerdo'];
        }

        if (position.includes('volante')) return fieldPositions['volante'];

        if (position.includes('meio') || position.includes('meia')) {
            if (position.includes('direito')) return fieldPositions['meio-direito'];
            if (position.includes('esquerdo')) return fieldPositions['meio-esquerdo'];
            return fieldPositions['meio-central']; // Padrão para "meia"
        }

        if (position.includes('ponta')) {
            if (position.includes('direita')) return fieldPositions['ponta-direita'];
            if (position.includes('esquerda')) return fieldPositions['ponta-esquerda'];
        }

        if (position.includes('centroavante')) return fieldPositions['centroavante'];
        if (position.includes('atacante')) return fieldPositions['atacante-central'];

        // Se nada for encontrado, retorna nulo
        return null;
    }

    /**
     * Posiciona os pontos no campo de acordo com as posições do jogador.
     */
    function positionPlayers() {
        // Pega as posições dos dados do jogador vindos do Blade
        const primaryPositionName = "{{ strtolower(trim($jogador->posicao_principal ?? '')) }}";
        const secondaryPositionName = "{{ strtolower(trim($jogador->posicao_secundaria ?? '')) }}";

        const primaryDot = document.querySelector('.player-position-prim');
        const secondaryDot = document.querySelector('.player-position-sec');

        // Encontra as coordenadas para a posição principal
        const primaryCoords = findCoordinates(primaryPositionName);
        if (primaryCoords && primaryDot) {
            primaryDot.style.top = primaryCoords.top + '%';
            primaryDot.style.left = primaryCoords.left + '%';
            primaryDot.style.display = 'block'; // Garante que o ponto seja visível
        }

        // Encontra as coordenadas para a posição secundária
        const secondaryCoords = findCoordinates(secondaryPositionName);
        if (secondaryCoords && secondaryDot) {
            secondaryDot.style.top = secondaryCoords.top + '%';
            secondaryDot.style.left = secondaryCoords.left + '%';
            secondaryDot.style.display = 'block'; // Garante que o ponto seja visível
        }

        // Dica de Debug: Verifique o console do navegador (F12) para ver os valores
        console.log('Posição Principal:', primaryPositionName, 'Coordenadas:', primaryCoords);
        console.log('Posição Secundária:', secondaryPositionName, 'Coordenadas:', secondaryCoords);
    }

    // Chama a função principal para posicionar os jogadores
    positionPlayers();
});