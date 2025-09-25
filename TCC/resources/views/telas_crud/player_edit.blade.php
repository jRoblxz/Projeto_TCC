@extends('navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Editar do Jogador</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>
        <form action="{{ route('jogadores.update', ['jogadores' => $jogador->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="content">
                <div class="player-infos">
                    <div class="player-avatar">
                        <img src="{{ asset('storage/' . $jogador->pessoa->foto_perfil_url) }}" alt="sem foto"
                            id="playerImage">
                        <input type="file" name="image" id="imageUploade" accept="image/*">
                    </div>
                    <div class="player-name editable-field">
                        <input type="text" name="nome_completo" value="{{ $jogador->pessoa->nome_completo }}"
                            placeholder="Nome do jogador">
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">Idade</div>
                            <div class="stat-value editable-field">
                                <input type="date" name="data_nascimento" value="{{ $jogador->pessoa->data_nascimento}}"
                                    placeholder="Idade">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Altura</div>
                            <div class="stat-value editable-field">
                                <input type="text" name="altura_cm" value="{{ $jogador->altura_cm }}" placeholder="Altura">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Pé</div>
                            <div class="stat-value editable-field">
                                <input type="text" name="pe_preferido" value="{{ $jogador->pe_preferido }}"
                                    placeholder="Pé preferido">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Peso</div>
                            <div class="stat-value editable-field">
                                <input type="text" name="peso_kg" value="{{ $jogador->peso_kg }} " placeholder="Peso">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição Principal</div>
                            <div class="stat-value-prim editable-field">
                                <input type="text" name="posicao_principal" value="{{ $jogador->posicao_principal }}"
                                    placeholder="Posição">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição Secundaria</div>
                            <div class="stat-value-sec editable-field">
                                <input type="text" name="posicao_secundaria" value="{{ $jogador->posicao_secundaria }}"
                                    placeholder="Posição">
                            </div>
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
                        <div class="score-value editable-field">
                            <input type="text" value="{{ $jogador->rating_medio ?? 0 }}" placeholder="Score">
                        </div>
                    </div>

                    <div class="recommendations">
                        <h4>Notas e Recomendações</h4>
                        <h4>Informacoes e Notas</h4>
                        <p><strong>Data de Nascimento:</strong><input type="date" name="data_nascimento"
                                value="{{ $jogador->pessoa->data_nascimento}}" placeholder="Data de nascuimentoi"> <br>
                            <strong> Email:</strong><input type="email" name="email" value="{{ $jogador->pessoa->email }}"
                                placeholder="email"> <br>
                            <strong>Cidade:</strong><input type="text" name="cidade" value="{{ $jogador->pessoa->cidade }}"
                                placeholder="cidade"> <br>
                            <strong>CPF:</strong><input type="number" name="cpf" value="{{ $jogador->pessoa->cpf }}"
                                placeholder="cpf"> <br>
                            <strong>Telefone:</strong><input type="number" name="telefone"
                                value="{{ $jogador->pessoa->telefone }}" placeholder="telefone"> <br>
                            <strong>RG:</strong><input type="number" name="rg" value="{{ $jogador->pessoa->rg }}"
                                placeholder="Score">
                            <br>
                            <strong>Video Skills:</strong><input type="url" name="video_apresentacao_url"
                                value="{{ $jogador->video_apresentacao_url }}" placeholder="video"> <br>
                            <strong>Fez cirugias:</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="historico_lesoes_cirurgias" id="cirurgia_sim"
                                value="sim" {{ $jogador->historico_lesoes_cirurgias == 'sim' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cirurgia_sim">Sim</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="historico_lesoes_cirurgias" id="cirurgia_nao"
                                value="nao" {{ $jogador->historico_lesoes_cirurgias == 'nao' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cirurgia_nao">Não</label>
                        </div>
                        </p>
                    </div>

                    <div class="evaluations">
                        <div class="eval-item">
                            <h5>Técnica</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Condicionamento</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Finalização</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Velocidade</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Posicionamento</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Cabeceio</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="N/A" placeholder="Nota">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="centralizar">
                <button class="button-save delete-section" type="submit">SALVAR ALTERAÇÕES</button>
                <a href="{{ route('jogadores.info', ['jogadores' => $jogador->id]) }}"
                    class="button-cancel delete-section">CANCELAR</a>
            </div>
        </form>
    </div>
    <script>
        // JavaScript do upload de imagem (que você já tem)
        const imageUpload = document.getElementById('imageUploade');
        const playerImage = document.getElementById('playerImage');

        imageUpload.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    playerImage.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // JavaScript para posicionamento dinâmico no campo
        const fieldPositions = {
            // Suas posições aqui (mesmo código anterior)
            'goleiro': { top: 95, left: 50 },
            'zagueiro-central': { top: 80, left: 50 },
            'zagueiro-direito': { top: 80, left: 70 },
            'zagueiro-esquerdo': { top: 80, left: 30 },
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

        function updateFieldPositions() {
            const primaryInput = document.querySelector('input[name="posicao_principal"]');
            const secondaryInput = document.querySelector('input[name="posicao_secundaria"]');

            const primaryPos = findPosition(primaryInput.value.toLowerCase());
            const secondaryPos = findPosition(secondaryInput.value.toLowerCase());

            if (primaryPos) {
                document.querySelector('.player-position-prim').style.top = primaryPos.top + '%';
                document.querySelector('.player-position-prim').style.left = primaryPos.left + '%';
            }

            if (secondaryPos) {
                document.querySelector('.player-position-sec').style.top = secondaryPos.top + '%';
                document.querySelector('.player-position-sec').style.left = secondaryPos.left + '%';
            }
        }

        // Atualizar posições quando os inputs mudarem
        document.addEventListener('DOMContentLoaded', function () {
            updateFieldPositions();

            document.querySelector('input[name="posicao_principal"]').addEventListener('input', updateFieldPositions);
            document.querySelector('input[name="posicao_secundaria"]').addEventListener('input', updateFieldPositions);
        });

        // Função para buscar posição (mesmo do código anterior)
        function findPosition(position) {
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
    </script>
@endsection