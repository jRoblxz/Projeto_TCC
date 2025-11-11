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
                                <select name="pe_preferido" class="form-control">
                                    <option value="{{ $jogador->pe_preferido }}">{{ $jogador->pe_preferido }}
                                    </option>
                                    <option value="Direito" {{ $jogador->pe_preferido == 'Direito' ? 'selected' : '' }}>
                                        Direito</option>
                                    <option value="Esquerdo" {{ $jogador->pe_preferido == 'Esquerdo' ? 'selected' : '' }}>
                                        Esquerdo</option>
                                </select>
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
                                    <option value="Goleiro" {{ $jogador->posicao_principal == 'Goleiro' ? 'selected' : '' }}>
                                        Goleiro</option>
                                    <option value="Zagueiro Direito" {{ $jogador->posicao_principal == 'Zagueiro Direito' ? 'selected' : '' }}>Zagueiro Direito</option>
                                    <option value="Zagueiro Esquerdo" {{ $jogador->posicao_principal == 'Zagueiro Esquerdo' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
                                    <option value="Lateral Direito" {{ $jogador->posicao_principal == 'Lateral Direito' ? 'selected' : '' }}>Lateral Direito</option>
                                    <option value="Lateral Esquerdo" {{ $jogador->posicao_principal == 'Lateral Esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
                                    <option value="Volante" {{ $jogador->posicao_principal == 'Volante' ? 'selected' : '' }}>
                                        Volante</option>
                                    <option value="Meia" {{ $jogador->posicao_principal == 'Meia' ? 'selected' : '' }}>Meio
                                        Central</option>
                                    <option value="Ponta Direita" {{ $jogador->posicao_principal == 'Ponta Direita' ? 'selected' : '' }}>Ponta Direita</option>
                                    <option value="Ponta Esquerda" {{ $jogador->posicao_principal == 'Ponta Esquerda' ? 'selected' : '' }}>Ponta Esquerda</option>
                                    <option value="Atacante" {{ $jogador->posicao_principal == 'Atacante' ? 'selected' : '' }}>Centroavante</option>
                                </select>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição Secundaria</div>
                            <div class="stat-value-sec editable-field">
                                <select name="posicao_secundaria" class="form-control">
                                    <option value="">{{ $jogador->posicao_secundaria }}</option>
                                    <option value="Goleiro" {{ $jogador->posicao_secundaria == 'Goleiro' ? 'selected' : '' }}>
                                        Goleiro</option>
                                    <option value="Zagueiro Direito" {{ $jogador->posicao_secundaria == 'Zagueiro Direito' ? 'selected' : '' }}>Zagueiro Direito</option>
                                    <option value="Zagueiro Esquerdo" {{ $jogador->posicao_secundaria == 'Zagueiro Esquerdo' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
                                    <option value="Lateral Direito" {{ $jogador->posicao_secundaria == 'Lateral Direito' ? 'selected' : '' }}>Lateral Direito</option>
                                    <option value="Lateral Esquerdo" {{ $jogador->posicao_secundaria == 'Lateral Esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
                                    <option value="Volante" {{ $jogador->posicao_secundaria == 'Volante' ? 'selected' : '' }}>
                                        Volante</option>
                                    <option value="Meia" {{ $jogador->posicao_secundaria == 'Meia' ? 'selected' : '' }}>Meia
                                    </option>
                                    <option value="Ponta Direita" {{ $jogador->posicao_secundaria == 'Ponta Direita' ? 'selected' : '' }}>Ponta Direita</option>
                                    <option value="Ponta Esquerda" {{ $jogador->posicao_secundaria == 'Ponta Esquerda' ? 'selected' : '' }}>Ponta Esquerda</option>
                                    <option value="Atacante" {{ $jogador->posicao_secundaria == 'Atacante' ? 'selected' : '' }}>Centroavante</option>
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
                        <div class="score-value">
                            {{ $jogador->rating_medio ?? 0 }}
                            {{-- <input type="number" name="nota" value="{{ $jogador->rating_medio ?? 0 }}" placeholder="Nota">                            --}}
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
                        {{-- <h4>Avaliação</h4>
                        <textarea name="observacoes" placeholder="Observações" rows="6"
                            class="form-control">{{ $jogador->ultima_avaliacao?->observacoes ?? '' }}</textarea>

                        <!-- <p>{{ $jogador->ultima_avaliacao?->observacoes ?? 'Nenhuma observação.' }}</p>--> --}}
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
        // JavaScript do upload de imagem
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

        // MAPEAMENTO CORRETO DAS POSIÇÕES - valores exatamente como no HTML
        const fieldPositions = {
            // Valores em português (exatamente como no HTML)
            'goleiro': { top: 95, left: 50 },
            'zagueiro direito': { top: 80, left: 70 },
            'zagueiro esquerdo': { top: 80, left: 30 },
            'lateral direito': { top: 60, left: 85 },
            'lateral esquerdo': { top: 60, left: 15 },
            'volante': { top: 60, left: 50 },
            'meia': { top: 45, left: 50 },
            'ponta direita': { top: 25, left: 80 },
            'ponta esquerda': { top: 25, left: 20 },
            'atacante': { top: 15, left: 50 }
        };

        // Função para normalizar posição
        function normalizePosition(position) {
            if (!position) return '';
            return position.toLowerCase().trim();
        }

        // Função para atualizar posições no campo quando select muda
        function updateFieldPositions() {
            const primarySelect = document.querySelector('select[name="posicao_principal"]');
            const secondarySelect = document.querySelector('select[name="posicao_secundaria"]');

            if (!primarySelect || !secondarySelect) return;

            const primaryValue = normalizePosition(primarySelect.value);
            const secondaryValue = normalizePosition(secondarySelect.value);

            const primaryPos = fieldPositions[primaryValue];
            const secondaryPos = fieldPositions[secondaryValue];

            const primaryDot = document.querySelector('.player-position-prim');
            const secondaryDot = document.querySelector('.player-position-sec');

            // Posição principal
            if (primaryPos && primaryDot) {
                primaryDot.style.top = primaryPos.top + '%';
                primaryDot.style.left = primaryPos.left + '%';
                primaryDot.style.display = 'block';
            } else if (primaryDot) {
                primaryDot.style.display = 'none';
            }

            // Posição secundária
            if (secondaryPos && secondaryDot) {
                secondaryDot.style.top = secondaryPos.top + '%';
                secondaryDot.style.left = secondaryPos.left + '%';
                secondaryDot.style.display = 'block';
            } else if (secondaryDot) {
                secondaryDot.style.display = 'none';
            }
        }

        // Função para posicionar baseado nos dados do banco
        function positionPlayers() {
            const primaryPositionName = normalizePosition("{{ $jogador->posicao_principal ?? '' }}");
            const secondaryPositionName = normalizePosition("{{ $jogador->posicao_secundaria ?? '' }}");

            const primaryDot = document.querySelector('.player-position-prim');
            const secondaryDot = document.querySelector('.player-position-sec');

            // Posição principal
            const primaryCoords = fieldPositions[primaryPositionName];
            if (primaryCoords && primaryDot) {
                primaryDot.style.top = primaryCoords.top + '%';
                primaryDot.style.left = primaryCoords.left + '%';
                primaryDot.style.display = 'block';
            } else if (primaryDot) {
                primaryDot.style.display = 'none';
            }

            // Posição secundária
            const secondaryCoords = fieldPositions[secondaryPositionName];
            if (secondaryCoords && secondaryDot) {
                secondaryDot.style.top = secondaryCoords.top + '%';
                secondaryDot.style.left = secondaryCoords.left + '%';
                secondaryDot.style.display = 'block';
            } else if (secondaryDot) {
                secondaryDot.style.display = 'none';
            }

            // Debug completo
            console.log('=== DEBUG POSIÇÕES ===');
            console.log('Posição Principal (banco):', "{{ $jogador->posicao_principal ?? 'null' }}");
            console.log('Posição Secundária (banco):', "{{ $jogador->posicao_secundaria ?? 'null' }}");
            console.log('Principal normalizada:', primaryPositionName);
            console.log('Secundária normalizada:', secondaryPositionName);
            console.log('Coordenadas principal:', primaryCoords);
            console.log('Coordenadas secundária:', secondaryCoords);
            console.log('Posições disponíveis:', Object.keys(fieldPositions));
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function () {
            // Primeiro posiciona baseado nos dados do banco
            positionPlayers();

            // Depois adiciona listeners para os selects
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