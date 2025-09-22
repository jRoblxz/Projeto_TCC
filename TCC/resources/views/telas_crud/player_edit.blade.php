@extends('telas_crud.navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Perfil do Jogador</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>
        <form method="POST" action="/player_update" enctype="multipart/form-data"> <!-- JOAP: KAYNAN ALTERA AQUI O ACTION CERTO, E VE SE O FORM TA CORRETO-->
            @csrf
            @method('PUT')

            <div class="content">
                <div class="player-infos">
                    <div class="player-avatar">
                        <img id="playerImage" src="{{ asset('img/neymar.jpeg') }}" alt="Jogador" />
                        <input type="file" name="image" id="imageUploade" accept="image/*">
                    </div>
                    <div class="player-name editable-field">
                        <input type="text" value="Neymar Jr" placeholder="Nome do jogador">
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">Idade</div>
                            <div class="stat-value editable-field">
                                <input type="text" value="32" placeholder="Idade">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Altura</div>
                            <div class="stat-value editable-field">
                                <input type="text" value="1,87m" placeholder="Altura">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Pé</div>
                            <div class="stat-value editable-field">
                                <input type="text" value="Direito" placeholder="Pé preferido">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Peso</div>
                            <div class="stat-value editable-field">
                                <input type="text" value="84kg" placeholder="Peso">
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Posição</div>
                            <div class="stat-value editable-field">
                                <input type="text" value="ATA" placeholder="Posição">
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
                            <input type="text" value="89" placeholder="Score">
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
                                <input type="text" value="82" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Condicionamento</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="88" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Finalização</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="85" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Velocidade</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="79" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Posicionamento</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="90" placeholder="Nota">
                            </div>
                        </div>
                        <div class="eval-item">
                            <h5>Cabeceio</h5>
                            <div class="eval-score editable-field">
                                <input type="text" value="73" placeholder="Nota">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="button-save delete-section" onclick="window.location.href='player_info'">SALVAR</button>
                <button class="button-delete save-section">DELETAR JOGADOR</button>
            </div>
    </div>
    </form>
    <script>
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
    </script>
@endsection