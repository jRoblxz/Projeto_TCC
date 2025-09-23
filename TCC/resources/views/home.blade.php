<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Peneiras</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            color: white;
            overflow-x: hidden;
        }

        .dashboard-container {
            margin-left: 80px;
            padding: 20px;
            min-height: 100vh;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="2"/><path d="M50 10 L50 90 M10 50 L90 50" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></svg>') center/200px;
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .club-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .club-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .club-details h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .club-details p {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .stat-item:hover::before {
            left: 100%;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #4ECDC4;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .card:hover::before {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .peneira-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid #4ECDC4;
        }

        .peneira-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .peneira-item.ativa {
            border-left-color: #FF6B6B;
            background: rgba(255, 107, 107, 0.1);
        }

        .peneira-item.encerrada {
            border-left-color: #666;
            opacity: 0.7;
        }

        .peneira-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 10px;
        }

        .peneira-title {
            font-weight: bold;
            font-size: 1.1rem;
            color: #4ECDC4;
        }

        .peneira-status {
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .peneira-status.ativa {
            background: linear-gradient(45deg, #FF6B6B, #E74C3C);
        }

        .peneira-status.encerrada {
            background: linear-gradient(45deg, #666, #555);
        }

        .peneira-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            font-size: 0.9rem;
        }

        .peneira-detail {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .peneira-detail strong {
            color: #4ECDC4;
        }

        .candidate-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .candidate-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
        }

        .candidate-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .candidate-info {
            flex: 1;
        }

        .candidate-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .candidate-details {
            font-size: 0.85rem;
            opacity: 0.8;
            display: flex;
            gap: 15px;
        }

        .candidate-rating {
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .rating-excellent { background: linear-gradient(45deg, #4ECDC4, #44A08D); }
        .rating-good { background: linear-gradient(45deg, #3498db, #2980b9); }
        .rating-average { background: linear-gradient(45deg, #f39c12, #e67e22); }
        .rating-poor { background: linear-gradient(45deg, #e74c3c, #c0392b); }

        .action-button {
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(78, 205, 196, 0.3);
        }

        .action-button.secondary {
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .action-button.danger {
            background: linear-gradient(45deg, #FF6B6B, #E74C3C);
        }

        .evaluation-chart {
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            margin: 20px 0;
            display: flex;
            align-items: end;
            justify-content: space-around;
            padding: 20px;
            gap: 10px;
        }

        .chart-bar {
            background: linear-gradient(to top, #FF6B6B, #4ECDC4);
            border-radius: 5px 5px 0 0;
            min-width: 40px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: end;
        }

        .chart-bar:hover {
            transform: scaleY(1.1);
            box-shadow: 0 5px 20px rgba(78, 205, 196, 0.5);
        }

        .chart-bar::before {
            content: attr(data-value);
            position: absolute;
            top: -25px;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            font-weight: bold;
        }

        .chart-bar:hover::before {
            opacity: 1;
        }

        .chart-label {
            position: absolute;
            bottom: -30px;
            font-size: 0.7rem;
            text-align: center;
            opacity: 0.8;
        }

        .floating-action {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            font-size: 24px;
            z-index: 1000;
        }

        .floating-action:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 15px 40px rgba(78, 205, 196, 0.4);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            z-index: 1001;
            max-width: 300px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            color: white;
            position: relative;
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .modal-close:hover {
            opacity: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4ECDC4;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                margin-left: 0;
                padding: 10px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .club-info {
                flex-direction: column;
                text-align: center;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .candidate-details {
                flex-direction: column;
                gap: 5px;
            }
        }

        .animate-in {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-bar {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 12px 20px;
            margin-bottom: 20px;
            color: white;
            font-size: 14px;
            width: 100%;
        }

        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .filter-tab.active {
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
        }

        .filter-tab:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            opacity: 0.7;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header com informações do sistema -->
        <div class="header animate-in">
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
            <div class="card animate-in" style="animation-delay: 0.2s;">
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
            <div class="card animate-in" style="animation-delay: 0.3s;">
                <h2 class="card-title">
                    <div class="card-icon">👥</div>
                    Candidatos Recentes
                </h2>

                <input type="text" class="search-bar" placeholder="Buscar candidato..." onkeyup="filterCandidates(this.value)">

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
        <div class="card animate-in" style="animation-delay: 0.4s;">
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
                <div class="chart-bar" style="height: 70%;" data-value="7.