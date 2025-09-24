@extends('navbar')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Jogadores inscritos</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>
        <div class="cards-grid" id="cardsGrid">
            @if(isset($players) && $players->count() > 0)
                @foreach($players as $player)
                <div class="card" data-id="{{ $player->jogador_id }}">
                    <div class="card-inner">
                        <div class="card-front">
                            <div class="card-actions">
                                <button class="action-btn delete-btn" onclick="deleteCard({{ $player->jogador_id }}, event)"
                                    title="Deletar">🗑️</button>
                            </div>
                            <div class="card-photo">
                                <img src="{{ asset('storage/' . $player->foto) }}" 
                                     alt="{{ $player->nome_completo }}" class="player-photo">
                                <div class="rating">{{ $player->nota ?? 0 }}</div>
                                <div class="position-badge">{{ strtoupper(substr($player->posicao_principal ?? 'JOG', 0, 3)) }}</div>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="back-header">{{ $player->nome_completo }}</div>
                            <div class="player-info">
                                <div class="info-row">
                                    <span class="info-label">Altura:</span>
                                    <span class="info-value">{{ $player->altura_cm }}cm</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Peso:</span>
                                    <span class="info-value">{{ $player->peso_kg }}kg</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Pé:</span>
                                    <span class="info-value">{{ $player->pe_preferido }}</span>
                                </div>
                            </div>
                            <div class="evaluation">
                                <div class="evaluation-title">Avaliação</div>
                                <div class="evaluation-text">
                                    Principal: {{ $player->posicao_principal }}<br>
                                    Secundária: {{ $player->posicao_secundaria ?? 'Não informado' }}<br>
                                    Email: {{ $player->email }}
                                </div>
                                <div class="evaluation-options">
                                    <span class="ver_mais-btn" onclick="window.location.href='/player_info/{{ $player->jogador_id }}'">Ver Mais</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @else
                <div class="no-players">
                    <p>Nenhum jogador cadastrado ainda.</p>
                </div>
            @endif
            
            <div class="card add-card" onclick="window.location.href='forms1'">
                <div class="add-icon">+</div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/player.js') }}"></script>
    </body>
    </html>
@endsection