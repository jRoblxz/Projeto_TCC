@extends('navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Perfil do Jogador</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>

        <div class="content">
            <div class="player-infos">
                <button class="edit-button"
                    onclick="window.location.href='{{ route('jogadores.edit', $jogador->id ?? $jogador->jogador_id) }}'">
                    <svg class="edit-svgIcon" viewBox="0 0 512 512">
                        <path
                            d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z">
                        </path>
                    </svg>
                </button>
                <div class="player-avatar-info"><img src="{{ asset('storage/' . $jogador->pessoa->foto_perfil_url) }}" alt="sem foto"></div>
                <div class="player-name">{{ $jogador->pessoa->nome_completo }}</div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Idade</div>
                        <div class="stat-value">{{ $jogador->pessoa->idade ?? 'N/A' }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Altura (cm)</div>
                        <div class="stat-value">{{ $jogador->altura_cm }} cm</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Pé</div>
                        <div class="stat-value">{{ $jogador->pe_preferido }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Peso</div>
                        <div class="stat-value">{{ $jogador->peso_kg }} kg</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label ">Posição Principal</div>
                        <div class="stat-value-prim">{{ $jogador->posicao_principal }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Posição Secundaria</div>
                        <div class="stat-value-sec">{{ $jogador->posicao_secundaria }}</div>
                    </div>
                </div>

                <div class="field-position">
                    <div class="field-lines"></div>
                    <div class="player-position-prim"></div>
                    <div class="player-position-sec"></div>
                </div>
            </div>

            <div class="details-section">
                <div class="overall-score">
                    <h3>Overall Score</h3>
                    <div class="score-value">{{ $jogador->rating_medio ?? 0 }}</div>
                </div>

                <div class="recommendations">
                    <h4>Notas e Recomendações</h4>
                    <p><strong>Análise Técnica:</strong> O jogador demonstra excelente técnica de finalização. Real Sporting
                        FC (Copa do Brasil). Real Sporting 1x1 Nova Venécia Esporte 2016 Nova Venécia Vitória Esporte 2016.
                    </p>

                    <p><strong>Pontos Fortes:</strong> Ótima força e explosão muscular, boa velocidade principalmente nas
                        jogadas dos primeiros 15m.</p>

                    <p><strong>Desenvolvimento:</strong> Recomenda-se melhorar seus remates. Pode melhorar o cabeceio. Pensa
                        bem as jogadas e são bem intencionadas no meio campo. Apresenta baixa capacidade para dribles fintos
                        e jogadas de ataque. É visto com bons olhos para sua alegada pretensão a anos.</p>

                    <p><strong>Observações:</strong> Atleta sempre bem posicionado e busca pelo Futsal! Apresenta boa
                        técnica necessária para merecer um olhar mais atento. Tem bons movimentos, tem times e é jogador do
                        coração com as características do jogo infantil-juvenil. E deve se ter um agente interessado para a
                        ascensão.</p>
                </div>

                <div class="evaluations">
                    <div class="eval-item">
                        <h5>Técnica</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                    <div class="eval-item">
                        <h5>Condicionamento</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                    <div class="eval-item">
                        <h5>Finalização</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                    <div class="eval-item">
                        <h5>Velocidade</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                    <div class="eval-item">
                        <h5>Posicionamento</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                    <div class="eval-item">
                        <h5>Cabeceio</h5>
                        <div class="eval-score">N/A</div>
                    </div>
                </div>
            </div>
            <button class="button-delete delete-section">DELETAR JOGADOR</button>
        </div>
    </div>
    <script>// Executar quando o DOM estiver completamente carregado
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
        });</script>
@endsection