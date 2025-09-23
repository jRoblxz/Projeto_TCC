@extends('navbar')
@section('content')
<div class="dashboard-container">
    <!-- Header com informações do sistema -->
    <div class="header-home animate-in">
        <div class="header-content">
            <div class="club-info">
                <div class="club-logo">FC</div>
                <div class="club-details">
                    <h1>Sistema de Peneiras</h1>
                    <p>Gestão e Formação • Champions FC • 2024</p>
                </div>
            </div>

            <div class="quick-stats">
                <div class="stat-item" onclick="animateStats(this)">
                    <div class="stat-number" data-target="145">0</div>
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
                <div class="card-icon">🏃‍♂️</div>
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

            <button class="action-button" onclick="openModal('newPeneira')">
                ➕ Nova Peneira
            </button>
        </div>

        <!-- Candidatos recentes -->
        <div class="card-home animate-in" style="animation-delay: 0.3s;">
            <h2 class="card-title">
                <div class="card-icon">👥</div>
                Candidatos Recentes
            </h2>

            <input type="text" class="search-bar" placeholder="Buscar candidato..."
                onkeyup="filterCandidates(this.value)">

            <div class="filter-tabs">
                <div class="filter-tab active" onclick="filterByStatus(this, 'all')">Todos</div>
                <div class="filter-tab" onclick="filterByStatus(this, 'pending')">Pendentes</div>
                <div class="filter-tab" onclick="filterByStatus(this, 'approved')">Aprovados</div>
                <div class="filter-tab" onclick="filterByStatus(this, 'rejected')">Rejeitados</div>
            </div>

            <div id="candidates-list">
                <div class="candidate-card" data-status="pending" onclick="showCandidateDetails('Carlos Silva')">
                    <div class="candidate-avatar">CS</div>
                    <div class="candidate-info">
                        <div class="candidate-name">Carlos Silva</div>
                        <div class="candidate-details">
                            <span>17 anos</span>
                            <span>Atacante</span>
                            <span>Sub-17</span>
                        </div>
                    </div>
                    <div class="candidate-rating rating-excellent">9.2</div>
                </div>

                <div class="candidate-card" data-status="pending" onclick="showCandidateDetails('Marina Santos')">
                    <div class="candidate-avatar">MS</div>
                    <div class="candidate-info">
                        <div class="candidate-name">Marina Santos</div>
                        <div class="candidate-details">
                            <span>19 anos</span>
                            <span>Meio-campo</span>
                            <span>Sub-20</span>
                        </div>
                    </div>
                    <div class="candidate-rating rating-good">8.5</div>
                </div>

                <div class="candidate-card" data-status="approved" onclick="showCandidateDetails('João Pedro')">
                    <div class="candidate-avatar">JP</div>
                    <div class="candidate-info">
                        <div class="candidate-name">João Pedro</div>
                        <div class="candidate-details">
                            <span>18 anos</span>
                            <span>Goleiro</span>
                            <span>Sub-20</span>
                        </div>
                    </div>
                    <div class="candidate-rating rating-excellent">9.8</div>
                </div>

                <div class="candidate-card" data-status="pending" onclick="showCandidateDetails('Ana Costa')">
                    <div class="candidate-avatar">AC</div>
                    <div class="candidate-info">
                        <div class="candidate-name">Ana Costa</div>
                        <div class="candidate-details">
                            <span>16 anos</span>
                            <span>Zagueiro</span>
                            <span>Sub-17</span>
                        </div>
                    </div>
                    <div class="candidate-rating rating-average">7.3</div>
                </div>

                <div class="candidate-card" data-status="rejected" onclick="showCandidateDetails('Pedro Lima')">
                    <div class="candidate-avatar">PL</div>
                    <div class="candidate-info">
                        <div class="candidate-name">Pedro Lima</div>
                        <div class="candidate-details">
                            <span>20 anos</span>
                            <span>Lateral</span>
                            <span>Profissional</span>
                        </div>
                    </div>
                    <div class="candidate-rating rating-poor">5.8</div>
                </div>
            </div>

            <button class="action-button secondary" onclick="openModal('newCandidate')">
                ➕ Cadastrar Candidato
            </button>
        </div>
    </div>

    <!-- Gráfico de avaliações -->
    <div class="card-home animate-in" style="animation-delay: 0.4s;">
        <h2 class="card-title">
            <div class="card-icon">📊</div>
            Estatísticas de Avaliação - Última Peneira
        </h2>

        <div class="evaluation-chart">
            <div class="chart-bar" style="height: 85%;" data-value="8.5">
                <div class="chart-label">Técnica</div>
            </div>
            <div class="chart-bar" style="height: 92%;" data-value="9.2">
                <div class="chart-label">Físico</div>
            </div>
            <div class="chart-bar" style="height: 76%;" data-value="7.6">
                <div class="chart-label">Tático</div>
            </div>
            <div class="chart-bar" style="height: 88%;" data-value="8.8">
                <div class="chart-label">Mental</div>
            </div>
        </div>
    </div>
</div>