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
                                    @if(auth()->user()->role === 'adm')
                                        <div class="card-actions-top">
                                            {{-- Botão Editar Rating --}}
                                            <button class="action-btn-card edit-rating-btn"
                                                onclick="event.stopPropagation(); openRatingModal({{ $jogador->id }}, '{{ $jogador->pessoa->nome_completo }}', {{ $jogador->rating_medio ?? 0 }})"
                                                title="Editar Rating">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>

                                            {{-- Botão Deletar --}}
                                            <form action="{{ route('jogadores.delete', $jogador->id) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este jogador?');"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn-card button-card-delete"
                                                    onclick="event.stopPropagation();">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 69 14"
                                                        class="svgIcon bin-top">
                                                        <g clip-path="url(#clip0_35_24)">
                                                            <path fill="black"
                                                                d="M20.8232 2.62734L19.9948 4.21304C19.8224 4.54309 19.4808 4.75 19.1085 4.75H4.92857C2.20246 4.75 0 6.87266 0 9.5C0 12.1273 2.20246 14.25 4.92857 14.25H64.0714C66.7975 14.25 69 12.1273 69 9.5C69 6.87266 66.7975 4.75 64.0714 4.75H49.8915C49.5192 4.75 49.1776 4.54309 49.0052 4.21305L48.1768 2.62734C47.3451 1.00938 45.6355 0 43.7719 0H25.2281C23.3645 0 21.6549 1.00938 20.8232 2.62734ZM64.0023 20.0648C64.0397 19.4882 63.5822 19 63.0044 19H5.99556C5.4178 19 4.96025 19.4882 4.99766 20.0648L8.19375 69.3203C8.44018 73.0758 11.6746 76 15.5712 76H53.4288C57.3254 76 60.5598 73.0758 60.8062 69.3203L64.0023 20.0648Z">
                                                            </path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_35_24">
                                                                <rect fill="white" height="14" width="69"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 69 57"
                                                        class="svgIcon bin-bottom">
                                                        <g clip-path="url(#clip0_35_22)">
                                                            <path fill="black"
                                                                d="M20.8232 -16.3727L19.9948 -14.787C19.8224 -14.4569 19.4808 -14.25 19.1085 -14.25H4.92857C2.20246 -14.25 0 -12.1273 0 -9.5C0 -6.8727 2.20246 -4.75 4.92857 -4.75H64.0714C66.7975 -4.75 69 -6.8727 69 -9.5C69 -12.1273 66.7975 -14.25 64.0714 -14.25H49.8915C49.5192 -14.25 49.1776 -14.4569 49.0052 -14.787L48.1768 -16.3727C47.3451 -17.9906 45.6355 -19 43.7719 -19H25.2281C23.3645 -19 21.6549 -17.9906 20.8232 -16.3727ZM64.0023 1.0648C64.0397 0.4882 63.5822 0 63.0044 0H5.99556C5.4178 0 4.96025 0.4882 4.99766 1.0648L8.19375 50.3203C8.44018 54.0758 11.6746 57 15.5712 57H53.4288C57.3254 57 60.5598 54.0758 60.8062 50.3203L64.0023 1.0648Z">
                                                            </path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_35_22">
                                                                <rect fill="white" height="57" width="69"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </button>
                                        </div>
                                    @endif

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