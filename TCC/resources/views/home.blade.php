@extends('navbar')
@section('content')
    <div class="dashboard-container">
        <!-- Header com informações do sistema -->
        <div class="header-home animate-in">
            <div class="header-content">
                <div class="club-info">
                    <img src="{{asset('img/logo-copia.png')}}" alt="logo" class="club-logo">
                    <div class="club-details">
                        <h1>Sistema de Peneiras</h1>
                        <p>Gestão e Formação • Champions FC • 2024</p>
                    </div>
                    
                    {{-- ✅ FORMULÁRIO CORRIGIDO --}}
                    <form method="GET" action="{{ route('home.index') }}" style="display: flex; align-items: center; gap: 10px;">
                        <label for="subdivisao" style="color: #14244d; font-weight: bold;">Filtrar por subdivisão:</label>
                        <select name="subdivisao" id="subdivisao" onchange="this.form.submit()" 
                                style="padding: 8px 12px; border-radius: 8px; border: 2px solid #14244d; font-family: 'fonte_player', sans-serif; color: #333; cursor: pointer;">
                            <option value="">Todas</option>
                            <option value="Sub-7" {{ request('subdivisao') == 'Sub-7' ? 'selected' : '' }}>Sub-7</option>
                            <option value="Sub-9" {{ request('subdivisao') == 'Sub-9' ? 'selected' : '' }}>Sub-9</option>
                            <option value="Sub-11" {{ request('subdivisao') == 'Sub-11' ? 'selected' : '' }}>Sub-11</option>
                            <option value="Sub-13" {{ request('subdivisao') == 'Sub-13' ? 'selected' : '' }}>Sub-13</option>
                            <option value="Sub-15" {{ request('subdivisao') == 'Sub-15' ? 'selected' : '' }}>Sub-15</option>
                            <option value="Sub-17" {{ request('subdivisao') == 'Sub-17' ? 'selected' : '' }}>Sub-17</option>
                            <option value="Sub-20" {{ request('subdivisao') == 'Sub-20' ? 'selected' : '' }}>Sub-20</option>
                        </select>
                        
                        {{-- Botão para limpar filtro --}}
                        @if(request('subdivisao'))
                            <a href="{{ route('home.index') }}" style="padding: 8px 15px; background: #e74c3c; color: white; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 12px;">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>

                <div class="quick-stats">
                    <div class="stat-item" onclick="animateStats(this)">
                        <div class="stat-number" data-target="{{ $stats['total_candidatos'] ?? $totalJogadores }}">{{ $stats['total_candidatos'] ?? $totalJogadores }}</div>
                        <div class="stat-label">Candidatos Total</div>
                    </div>
                    <div class="stat-item" onclick="animateStats(this)">
                        <div class="stat-number" data-target="{{ $stats['peneiras_ativas'] ?? 0 }}">{{ $stats['peneiras_ativas'] ?? 0 }}</div>
                        <div class="stat-label">Peneiras Ativas</div>
                    </div>
                    <div class="stat-item" onclick="animateStats(this)">
                        <div class="stat-number" data-target="{{ $stats['aprovados'] ?? 0 }}">{{ $stats['aprovados'] ?? 0 }}</div>
                        <div class="stat-label">Aprovados</div>
                    </div>
                    <div class="stat-item" onclick="animateStats(this)">
                        <div class="stat-number" data-target="{{ $stats['em_avaliacao'] ?? 0 }}">{{ $stats['em_avaliacao'] ?? 0 }}</div>
                        <div class="stat-label">Em Avaliação</div>
                    </div>
                    <div class="stat-item" onclick="animateStats(this)">
                        <div class="stat-number" data-target="{{ $stats['avaliadores'] ?? 0 }}">{{ $stats['avaliadores'] ?? 0 }}</div>
                        <div class="stat-label">Avaliadores</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid principal -->
        <div class="dashboard-grid">
            <!-- Peneiras ativas -->
            <div class="card-home animate-in" style="animation-delay: 0.2s;">
                <h2 class="card-title">
                    Peneiras em Andamento
                    @if(request('subdivisao'))
                        <span style="font-size: 1rem; color: #666; font-weight: normal;">({{ request('subdivisao') }})</span>
                    @endif
                </h2>

                {{-- ✅ LISTAGEM DINÂMICA DE PENEIRAS DO BANCO --}}
                @if(isset($peneiras) && $peneiras->count() > 0)
                    @foreach($peneiras as $peneira)
                        <div class="peneira-item {{ $peneira->status == 'EM_ANDAMENTO' ? 'ativa' : '' }} {{ $peneira->status == 'FINALIZADA' ? 'encerrada' : '' }} {{ $peneira->status == 'CANCELADA' ? 'encerrada' : '' }}" 
                             onclick="window.location.href='{{ route('peneiras.show', $peneira->id) }}'">
                            <div class="peneira-header">
                                <div class="peneira-title">{{ $peneira->nome_evento }}</div>
                                <div class="card-status-peneira status-{{ $peneira->status ?? 'aberta' }}">
                                    {{ ucfirst($peneira->status ?? 'Aberta') }}
                                </div>
                            </div>
                            <div class="peneira-info">
                                <div class="peneira-detail">
                                    <strong>Data:</strong>
                                    <span>{{ \Carbon\Carbon::parse($peneira->data_evento)->format('d/M - H:i') }}h</span>
                                </div>
                                <div class="peneira-detail">
                                    <strong>Inscritos:</strong>
                                    <span>{{ $peneira->inscricoes->count() ?? 0 }} candidatos</span>
                                </div>
                                <div class="peneira-detail">
                                    <strong>Subdivisão:</strong>
                                    <span>{{ $peneira->sub_divisao ?? 'Todas' }}</span>
                                </div>
                                <div class="peneira-detail">
                                    <strong>Local:</strong>
                                    <span>{{ $peneira->local ?? 'A definir' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">⚽</div>
                        <p>Nenhuma peneira encontrada
                            @if(request('subdivisao'))
                                para a subdivisão {{ request('subdivisao') }}
                            @endif
                        </p>
                    </div>
                @endif

                <button class="button-peneira" onclick="window.location.href='{{ route('peneiras.create') }}'">
                    nova peneira
                </button>
            </div>

            <!-- Candidatos recentes -->
            <div class="card-home animate-in" style="animation-delay: 0.3s;">
                <h2 class="card-title">
                    Jogadores Destaques
                    @if(request('subdivisao'))
                        <span style="font-size: 1rem; color: #666; font-weight: normal;">({{ request('subdivisao') }})</span>
                    @endif
                </h2>

                <div id="candidates-list">
                    @if(isset($jogadores) && $jogadores->count() > 0)
                        @foreach($jogadores as $jogador)
                            <div class="candidate-card" data-status="pending"
                                onclick="window.location.href='{{ route('jogadores.info', ['jogadores' => $jogador->id]) }}'">
                                <div class="candidate-avatar">
                                    @if($jogador->pessoa->foto_perfil_url)
                                        <img src="{{ $jogador->pessoa->foto_perfil_url_complete }}" alt="jogador" class="candidate-avatar-foto">
                                    @else
                                        <span style="font-size: 2rem;">👤</span>
                                    @endif
                                </div>
                                <div class="candidate-info">
                                    <div class="candidate-name">{{ $jogador->pessoa->nome_completo }}</div>
                                    <div class="candidate-details">
                                        <span>{{ $jogador->pessoa->data_nascimento->age }} anos</span>
                                        <span>{{ $jogador->pessoa->sub_divisao ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="candidate-rating rating-excellent">{{ number_format($jogador->rating_medio ?? 0, 1) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">🏆</div>
                            <p>Nenhum jogador cadastrado
                                @if(request('subdivisao'))
                                    para a subdivisão {{ request('subdivisao') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection