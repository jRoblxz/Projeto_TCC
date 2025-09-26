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
                            <div class="stat-value">{{ $jogador->pessoa->data_nascimento->age}}</div>
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
                                <select name="posicao_principal" class="form-control">
                                    <option value="{{ $jogador->posicao_principal }}">{{ $jogador->posicao_principal }}
                                    </option>
                                    <option value="goleiro" {{ $jogador->posicao_principal == 'goleiro' ? 'selected' : '' }}>
                                        Goleiro</option>
                                    <option value="zagueiro-central" {{ $jogador->posicao_principal == 'zagueiro-central' ? 'selected' : '' }}>Zagueiro Central</option>
                                    <option value="zagueiro-direito" {{ $jogador->posicao_principal == 'zagueiro-direito' ? 'selected' : '' }}>Zagueiro Direito</option>
                                    <option value="zagueiro-esquerdo" {{ $jogador->posicao_principal == 'zagueiro-esquerdo' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
                                    <option value="lateral-direito" {{ $jogador->posicao_principal == 'lateral-direito' ? 'selected' : '' }}>Lateral Direito</option>
                                    <option value="lateral-esquerdo" {{ $jogador->posicao_principal == 'lateral-esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
                                    <option value="volante" {{ $jogador->posicao_principal == 'volante' ? 'selected' : '' }}>
                                        Volante</option>
                                    <option value="meio-central" {{ $jogador->posicao_principal == 'meio-central' ? 'selected' : '' }}>Meio Central</option>
                                    <option value="meio-direito" {{ $jogador->posicao_principal == 'meio-direito' ? 'selected' : '' }}>Meio Direito</option>
                                    <option value="meio-esquerdo" {{ $jogador->posicao_principal == 'meio-esquerdo' ? 'selected' : '' }}>Meio Esquerdo</option>
                                    <option value="atacante-central" {{ $jogador->posicao_principal == 'atacante-central' ? 'selected' : '' }}>Atacante Central</option>
                                    <option value="ponta-direita" {{ $jogador->posicao_principal == 'ponta-direita' ? 'selected' : '' }}>Ponta Direita</option>
                                    <option value="ponta-esquerda" {{ $jogador->posicao_principal == 'ponta-esquerda' ? 'selected' : '' }}>Ponta Esquerda</option>
                                    <option value="centroavante" {{ $jogador->posicao_principal == 'centroavante' ? 'selected' : '' }}>Centroavante</option>
                                </select>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição Secundaria</div>
                            <div class="stat-value-sec editable-field">
                                <select name="posicao_secundaria" class="form-control">
                                    <option value="">{{ $jogador->posicao_secundaria }}</option>
                                    <option value="goleiro" {{ $jogador->posicao_secundaria == 'goleiro' ? 'selected' : '' }}>
                                        Goleiro</option>
                                    <option value="zagueiro-central" {{ $jogador->posicao_secundaria == 'zagueiro-central' ? 'selected' : '' }}>Zagueiro Central</option>
                                    <option value="zagueiro-direito" {{ $jogador->posicao_secundaria == 'zagueiro-direito' ? 'selected' : '' }}>Zagueiro Direito</option>
                                    <option value="zagueiro-esquerdo" {{ $jogador->posicao_secundaria == 'zagueiro-esquerdo' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
                                    <option value="lateral-direito" {{ $jogador->posicao_secundaria == 'lateral-direito' ? 'selected' : '' }}>Lateral Direito</option>
                                    <option value="lateral-esquerdo" {{ $jogador->posicao_secundaria == 'lateral-esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
                                    <option value="volante" {{ $jogador->posicao_secundaria == 'volante' ? 'selected' : '' }}>
                                        Volante</option>
                                    <option value="meio-central" {{ $jogador->posicao_secundaria == 'meio-central' ? 'selected' : '' }}>Meio Central</option>
                                    <option value="meio-direito" {{ $jogador->posicao_secundaria == 'meio-direito' ? 'selected' : '' }}>Meio Direito</option>
                                    <option value="meio-esquerdo" {{ $jogador->posicao_secundaria == 'meio-esquerdo' ? 'selected' : '' }}>Meio Esquerdo</option>
                                    <option value="atacante-central" {{ $jogador->posicao_secundaria == 'atacante-central' ? 'selected' : '' }}>Atacante Central</option>
                                    <option value="ponta-direita" {{ $jogador->posicao_secundaria == 'ponta-direita' ? 'selected' : '' }}>Ponta Direita</option>
                                    <option value="ponta-esquerda" {{ $jogador->posicao_secundaria == 'ponta-esquerda' ? 'selected' : '' }}>Ponta Esquerda</option>
                                    <option value="centroavante" {{ $jogador->posicao_secundaria == 'centroavante' ? 'selected' : '' }}>Centroavante</option>
                                </select>
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
                            <!<input type="text" value="{{ $jogador->rating_medio ?? 0 }}" placeholder="Score">
                                <input type="number" step="0.1" name="nota"
                                    value="{{ old('nota', $jogador->ultima_avaliacao->nota) }}" placeholder="Nota de 0 a 10"
                                    class="form-control">
                        </div>
                    </div>

                    <div class="recommendations">
                        <h4>Notas e Recomendações</h4>
                        <h4>Informacoes e Notas</h4>
                        <p><strong>Data de Nascimento:</strong> {{ $jogador->pessoa->data_nascimento }} <br>
                            <strong> Email:</strong> {{ $jogador->pessoa->email }}<br>
                            <strong>CPF:</strong> {{ $jogador->pessoa->cpf }} <br>
                            <strong>Telefone:</strong> {{ $jogador->pessoa->telefone }}<br>
                            <strong>RG:</strong> {{ $jogador->pessoa->rg }}<br>
                            <strong>Cirurgia:</strong> {{ $jogador->historico_lesoes_cirurgias }}<br>
                            <strong>Cidade:</strong> n/a <br>
                            <strong>Video Skills:</strong> {{ $jogador->video_apresentacao_url }}<br>
                            <!--   <strong> Email:</strong><input type="email" name="email" value="{{ $jogador->pessoa->email }}"
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
                                                    </div>-->
                        </p><br>
                        <h4>Avaliação</h4>
                        <p>{{ $jogador->ultima_avaliacao?->observacoes ?? 'Nenhuma observação.' }}</p>
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

            / JavaScript do upload de imagem
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

        // OBJETO COM AS POSIÇÕES DO CAMPO - ESTA PARTE ESTAVA FALTANDO!
        const fieldPositions = {
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

        // JavaScript atualizado para trabalhar com selects
        function updateFieldPositions() {
            const primarySelect = document.querySelector('select[name="posicao_principal"]');
            const secondarySelect = document.querySelector('select[name="posicao_secundaria"]');

            const primaryPos = fieldPositions[primarySelect.value];
            const secondaryPos = fieldPositions[secondarySelect.value];

            const primaryDot = document.querySelector('.player-position-prim');
            const secondaryDot = document.querySelector('.player-position-sec');

            if (primaryPos && primaryDot) {
                primaryDot.style.top = primaryPos.top + '%';
                primaryDot.style.left = primaryPos.left + '%';
                primaryDot.style.display = 'block';
            } else if (primaryDot) {
                primaryDot.style.display = 'none';
            }

            if (secondaryPos && secondaryDot) {
                secondaryDot.style.top = secondaryPos.top + '%';
                secondaryDot.style.left = secondaryPos.left + '%';
                secondaryDot.style.display = 'block';
            } else if (secondaryDot) {
                secondaryDot.style.display = 'none';
            }
        }

        // Função para posicionar jogadores baseado nos dados do banco
        function positionPlayers() {
            // Pega as posições dos dados do jogador vindos do Blade
            const primaryPositionName = "{{ strtolower(trim($jogador->posicao_principal ?? '')) }}";
            const secondaryPositionName = "{{ strtolower(trim($jogador->posicao_secundaria ?? '')) }}";

            const primaryDot = document.querySelector('.player-position-prim');
            const secondaryDot = document.querySelector('.player-position-sec');

            // Encontra as coordenadas para a posição principal
            const primaryCoords = fieldPositions[primaryPositionName];
            if (primaryCoords && primaryDot) {
                primaryDot.style.top = primaryCoords.top + '%';
                primaryDot.style.left = primaryCoords.left + '%';
                primaryDot.style.display = 'block';
            }

            // Encontra as coordenadas para a posição secundária
            const secondaryCoords = fieldPositions[secondaryPositionName];
            if (secondaryCoords && secondaryDot) {
                secondaryDot.style.top = secondaryCoords.top + '%';
                secondaryDot.style.left = secondaryCoords.left + '%';
                secondaryDot.style.display = 'block';
            }

            // Debug - verifique no console do navegador (F12)
            console.log('Posição Principal do Banco:', "{{ $jogador->posicao_principal ?? 'null' }}");
            console.log('Posição Secundária do Banco:', "{{ $jogador->posicao_secundaria ?? 'null' }}");
            console.log('Posição Principal Normalizada:', primaryPositionName);
            console.log('Posição Secundária Normalizada:', secondaryPositionName);
            console.log('Coordenadas Principais:', primaryCoords);
            console.log('Coordenadas Secundárias:', secondaryCoords);
        }

        // Inicialização quando a página carregar
        document.addEventListener('DOMContentLoaded', function () {
            // Primeiro posiciona baseado nos dados do banco
            positionPlayers();

            // Depois adiciona os listeners para mudanças nos selects
            const primarySelect = document.querySelector('select[name="posicao_principal"]');
            const secondarySelect = document.querySelector('select[name="posicao_secundaria"]');

            if (primarySelect) {
                primarySelect.addEventListener('change', updateFieldPositions);
            }

            if (secondarySelect) {
                secondarySelect.addEventListener('change', updateFieldPositions);
            }
        });
    </script>
@endsection