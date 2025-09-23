@extends('navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Jogadores inscritos</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>
        <div class="cards-grid" id="cardsGrid">
            <div class="card" data-id="1">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn delete-btn" onclick="deleteCard(1, event)"
                                title="Deletar">🗑️</button>
                        </div>
                        <div class="card-photo">
                            <img src="{{ asset('img/neymar.jpeg') }}" alt="neymar" class="player-photo">
                            <div class="rating">89</div>
                            <div class="position-badge">ATA</div>
                        </div>

                    </div>
                    <div class="card-back">
                        <div class="back-header">Neymar</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">1,87m</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">84kg</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">Direito</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">
                                Lenda do futebol, máquina de gols. Finalização impecável, cabeceio excepcional e mentalidade
                                vencedora. Um dos maiores da história.
                            </div>

                            <!--
                            <div class="evaluation-options">
                                <span class="ver_mais-btn" onclick="window.location.href='/player_info'">Ver Mais</span>
                            </div>
                            -->
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="card add-card" onclick="window.location.href='forms1'">
                <div class="add-icon">+</div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/player.js') }}"></script>
    </body>

    </html>
@endsection