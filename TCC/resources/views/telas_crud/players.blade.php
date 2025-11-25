@extends(auth()->user()->role === 'adm' ? 'navbar' : 'navbarCandidato')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Jogadores inscritos</h1>
            <p>Sistema de Avaliação de Atletas</p>
        </div>

        {{-- ✅ BARRA DE FILTROS --}}
        <div class="filter-section">
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input"
                    placeholder="🔍 Buscar por nome, posição ou subdivisão...">
            </div>

            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Todos ({{ $jogadores->count() }})</button>
                <button class="filter-btn" data-filter="Sub-17">Sub-17</button>
                <button class="filter-btn" data-filter="Sub-20">Sub-20</button>
                <button class="filter-btn" data-filter="Profissional">Profissional</button>
                <button class="filter-btn" data-filter="high-rating">Rating Alto (8+)</button>
            </div>
        </div>

        {{-- GRID DE CARDS --}}
        <div class="cards-grid" id="cardsGrid">
            @if(isset($jogadores) && $jogadores->count() > 0)
                @foreach($jogadores as $jogador)
                    <div class="card" data-id="{{ $jogador->id }}" data-name="{{ strtolower($jogador->pessoa->nome_completo) }}"
                        data-position="{{ strtolower($jogador->posicao_principal ?? '') }}"
                        data-subdivisao="{{ $jogador->pessoa->sub_divisao ?? '' }}" data-rating="{{ $jogador->rating_medio ?? 0 }}">
                        <div class="card-inner">
                            {{-- FRENTE DO CARD --}}
                            <div class="card-front">
                                <div class="card-photo">
                                    <img src="{{ $jogador->pessoa->foto_perfil_url_complete }}" alt="sem foto" class="player-photo">

                                    {{-- Botões de ação (apenas para ADM) --}}
                                    

                                    <div class="rating">{{ number_format($jogador->rating_medio ?? 0, 1) }}</div>
                                    <div class="position-badge">{{ $jogador->posicao_abreviada }}</div>
                                </div>
                            </div>

                            {{-- VERSO DO CARD --}}
                            <div class="card-back">
                                <div class="back-header">{{ $jogador->pessoa->nome_completo }}</div>
                                <div class="player-info back-content-scrollable">
                                    <div class="info-row">
                                        <span class="info-label">Divisão:</span>
                                        <span class="info-value">{{ $jogador->pessoa->sub_divisao ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Altura:</span>
                                        <span class="info-value">{{ $jogador->altura_cm }} cm</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Peso:</span>
                                        <span class="info-value">{{ $jogador->peso_kg }}kg</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Pé:</span>
                                        <span class="info-value">{{ $jogador->pe_preferido }}</span>
                                    </div>
                                    <div class="evaluation">
                                        <div class="evaluation-title">Avaliação</div>
                                        <div class="evaluation-text">
                                            {{ $jogador->ultima_avaliacao?->observacoes ?? 'Nenhuma observação.' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="ver_mais-btn btn-teste"
                                    onclick="window.location.href='{{ route('jogadores.info', ['jogadores' => $jogador->id]) }}'">
                                    Ver Mais
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-players">
                    <p>Nenhum jogador cadastrado ainda.</p>
                </div>
            @endif

            <div class="card add-card" onclick="window.location.href='{{ route('users.create') }}'">
                <div class="add-icon">+</div>
            </div>
        </div>
    </div>

    {{-- ✅ MODAL PARA EDITAR RATING --}}
    <div id="ratingModal" class="modal-rating">
        <div class="modal-rating-content">
            <span class="close-modal" onclick="closeRatingModal()">&times;</span>
            <h2>Editar Rating</h2>
            <p id="playerNameModal" style="color: #666; margin-bottom: 20px;"></p>

            <form id="ratingForm" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group-modal">
                    <label for="rating">Novo Rating (0-10):</label>
                    <input type="number" id="rating" name="rating_medio" min="0" max="10" step="0.1" required
                        class="rating-input">

                    <div class="rating-preview">
                        <span id="ratingValue">0.0</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel-modal" onclick="closeRatingModal()">Cancelar</button>
                    <button type="submit" class="btn-save-modal">Salvar Rating</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/player.js') }}"></script>
    <script src="{{ asset('js/player-filter.js') }}"></script>
@endsection