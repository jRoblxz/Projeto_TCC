@extends('navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Perfil do Jogador</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>
        <form action="{{ route('jogadores.update', ['jogador' => $jogador->id ]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="content">
                <div class="player-infos">
                    <div class="player-avatar">
                        <img src="{{ asset('storage/' . $jogador->pessoa->foto_perfil_url) }}" alt="sem foto" id="playerImage">
                        <input type="file" name="image" id="imageUploade" accept="image/*">
                    </div>
                    <div class="player-name editable-field">
                        <input type="text" name="nome_completo" value="{{ $jogador->pessoa->nome_completo }}" placeholder="Nome do jogador">
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">Idade</div>
                            <div class="stat-value editable-field">
                                <input type="text" name="idade" value="{{ $jogador->pessoa->idade ?? 'N/A' }}" placeholder="Idade">
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
                                <input type="text" name="pe_preferido" value="{{ $jogador->pe_preferido }}" placeholder="Pé preferido">
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
                                <input type="text" name="posicao_principal" value="{{ $jogador->posicao_principal }}" placeholder="Posição">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição Secundaria</div>
                            <div class="stat-value-sec editable-field">
                                <input type="text"  name="posicao_secundaria" value="{{ $jogador->posicao_secundaria }}" placeholder="Posição">
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
                        <div class="editable-field">
                            <textarea
                                placeholder="Análise técnica, pontos fortes, desenvolvimento e observações...">Análise Técnica: O jogador demonstra excelente técnica de finalização. Real Sporting FC (Copa do Brasil). Real Sporting 1x1 Nova Venécia Esporte 2016 Nova Venécia Vitória Esporte 2016.

                        Pontos Fortes: Ótima força e explosão muscular, boa velocidade principalmente nas jogadas dos primeiros 15m.

                        Desenvolvimento: Recomenda-se melhorar seus remates. Pode melhorar o cabeceio. Pensa bem as jogadas e são bem intencionadas no meio campo. Apresenta baixa capacidade para dribles fintos e jogadas de ataque. É visto com bons olhos para sua alegada pretensão a anos.

                        Observações: Atleta sempre bem posicionado e busca pelo Futsal! Apresenta boa técnica necessária para merecer um olhar mais atento. Tem bons movimentos, tem times e é jogador do coração com as características do jogo infantil-juvenil. E deve se ter um agente interessado para a ascensão.</textarea>
                        </div>
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

                <button class="button-save delete-section" type="submit">SALVAR</button>
                <form method="POST" action="{{ route('jogadores.delete', ['pessoas' => $jogador->pessoa->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="button-delete save-section" type="submit">DELETAR JOGADOR</button>
                </form>
                


            </div>
        </form>
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
                'goleiro': { top: 90, left: 50 },
                'zagueiro central': { top: 75, left: 50 },
                // ... resto das posições
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
                // ... código anterior
            }
        </script>
@endsection