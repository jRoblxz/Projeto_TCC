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
            </div>

            <div class="quick-stats">
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="145">{{ $totalJogadores }}</div>
                    <div class="stat-label">Candidatos Total</div>
                </div>
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="3">0</div>
                    <div class="stat-label">Peneiras Ativas</div>
                </div>
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="28">0</div>
                    <div class="stat-label">Aprovados</div>
                </div>
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="67">0</div>
                    <div class="stat-label">Em Avaliação</div>
                </div>
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="12">0</div>
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
            </h2>

            <div class="peneira-item ativa" onclick="showPeneiraDetails('Sub-17')">
                <div class="peneira-header">
                    <div class="peneira-title">Peneira Sub-17</div>
                    <div class="peneira-status ativa">ATIVA</div>
                </div>
                <div class="peneira-info">
                    <div class="peneira-detail">
                        <strong>Data:</strong>
                        <span>25/Nov - 10:00h</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Inscritos:</strong>
                        <span>45 candidatos</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Posições:</strong>
                        <span>Todas</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Local:</strong>
                        <span>CT Principal</span>
                    </div>
                </div>
            </div>

            <div class="peneira-item ativa" onclick="showPeneiraDetails('Sub-20')">
                <div class="peneira-header">
                    <div class="peneira-title">Peneira Sub-20 - Goleiros</div>
                    <div class="peneira-status ativa">ATIVA</div>
                </div>
                <div class="peneira-info">
                    <div class="peneira-detail">
                        <strong>Data:</strong>
                        <span>28/Nov - 14:30h</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Inscritos:</strong>
                        <span>12 candidatos</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Posições:</strong>
                        <span>Goleiro</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Local:</strong>
                        <span>Campo 2</span>
                    </div>
                </div>
            </div>

            <div class="peneira-item" onclick="showPeneiraDetails('Profissional')">
                <div class="peneira-header">
                    <div class="peneira-title">Seletiva Profissional</div>
                    <div class="peneira-status">PROGRAMADA</div>
                </div>
                <div class="peneira-info">
                    <div class="peneira-detail">
                        <strong>Data:</strong>
                        <span>5/Dez - 08:00h</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Inscritos:</strong>
                        <span>23 candidatos</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Posições:</strong>
                        <span>Meio-campo</span>
                    </div>
                    <div class="peneira-detail">
                        <strong>Local:</strong>
                        <span>Estádio Principal</span>
                    </div>
                </div>
            </div>

            <button class="button-peneira">nova peneira</button>
        </div>

        <!-- Candidatos recentes -->
        <div class="card-home animate-in" style="animation-delay: 0.3s;">
            <h2 class="card-title">
                Jogadores Destaques
            </h2>

            <div id="candidates-list">
                @if(isset($jogadores) && $jogadores->count() > 0)
                    @foreach($jogadores as $jogador)
                        <div class="candidate-card" data-status="pending"
                            onclick="window.location.href='{{ route('jogadores.info', ['jogadores' => $jogador->id]) }}'">
                            <div class="candidate-avatar">
                                <img src="{{ asset('storage/' . $jogador->pessoa->foto_perfil_url) }}" alt="jogador"
                                    class="candidate-avatar-foto">
                            </div>
                            <div class="candidate-info">
                                <div class="candidate-name">{{ $jogador->pessoa->nome_completo }} </div>
                                <div class="candidate-details">
                                    <span>{{ $jogador->pessoa->data_nascimento->age}} anos</span>
                                    <span>{{ $jogador->pessoa->sub_divisao }}</span>
                                </div>
                            </div>
                            <div class="candidate-rating rating-excellent">{{ $jogador->rating_medio ?? 0 }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="no-players">
                        <p>Nenhum jogador cadastrado ainda.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>