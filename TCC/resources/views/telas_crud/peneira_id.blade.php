{{--
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Peneira</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .btn-voltar {
            background: white;
            color: #667eea;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .btn-voltar:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .header-peneira-id {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .header-peneira-id::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .titulo-peneira {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            background: #c6f6d5;
            color: #22543d;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 10px;
        }

        .info-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .info-content h4 {
            font-size: 12px;
            color: #718096;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content p {
            font-size: 16px;
            color: #2d3748;
            font-weight: 600;
        }

        .descricao-peneira {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .descricao-peneira h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .descricao-peneira p {
            color: #4a5568;
            line-height: 1.6;
        }

        .acoes-principais {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        .btn-warning {
            background: #ed8936;
            color: white;
        }

        .btn-warning:hover {
            background: #dd6b20;
        }

        .content-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-header h2 {
            color: #2d3748;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 12px;
            color: white;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .search-filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .filter-select {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }

        tbody tr {
            transition: background 0.3s;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .jogador-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .jogador-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .jogador-nome {
            font-weight: 600;
            color: #2d3748;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-atacante {
            background: #fed7d7;
            color: #742a2a;
        }

        .badge-meio {
            background: #bee3f8;
            color: #2c5282;
        }

        .badge-defesa {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-goleiro {
            background: #fef5e7;
            color: #975a16;
        }

        .avaliacao {
            display: flex;
            gap: 3px;
        }

        .star {
            color: #f6ad55;
            font-size: 16px;
        }

        .star.empty {
            color: #e2e8f0;
        }

        .btn-table {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-view {
            background: #667eea;
            color: white;
        }

        .btn-view:hover {
            background: #5568d3;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 1200px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            color: #2d3748;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 32px;
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #2d3748;
        }

        .formacao-container {
            background: #0d9488;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            min-height: 500px;
        }

        .campo-fundo {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            border-radius: 15px;
        }

        .linha-meio {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        .circulo-central {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
        }

        .formacao-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .controles-formacao {
            background: #f7fafc;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .controles-formacao h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }

        .formacao-select {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .formacao-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
    </style>
</head> --}}
@extends('navbar')
@section('content')
    <div class="container">
        <button class="btn-voltar-peneira-id" onclick="history.back()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Voltar
        </button>

        <!-- Header da Peneira -->
        <div class="header-peneira-id">
            <div class="header-top">
                <div>
                    <h1 class="titulo-peneira">{{ $peneiras->nome_evento }}</h1>
                </div>
                <span class="status-badge">{{ $peneiras->status }}</span>

            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h4>Data e Horário</h4>
                        <p>{{ $peneiras->data_evento }}</p>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h4>Local</h4>
                        <p>{{ $peneiras->local }}</p>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h4>Faixa Etária</h4>
                        <p>{{ $peneiras->sub_divisao }}</p>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 12h8"></path>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h4>Vagas Disponíveis</h4>
                        <p>30 vagas</p>
                    </div>
                </div>
            </div>

            <div class="descricao-peneira">
                <h3>Sobre a Peneira</h3>
                <p>{{ $peneiras->descricao }}</p>
            </div>

            <div class="acoes-principais">
                <h3>Gerador de Equipes</h3>

                <form action="{{ route('equipes.gerar-e-editar', ['id' => $peneiras->id]) }}" method="POST">
                    @csrf
                    <p>Clique no botão abaixo para montar automaticamente as equipes com os jogadores inscritos e disponíveis.</p>

                     <button type="submit" class="button-save">
                        Gerar Equipes
                    </button>
                </form>

                @if (session('success'))
                    <div style="color: green; margin-top: 10px; font-weight: bold;">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div style="color: red; margin-top: 10px; font-weight: bold;">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Lista de Jogadores -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Jogadores Inscritos
                </h2>
            </div>

            <div class="search-filter-bar">
                <div class="search-box">
                    <input type="text" placeholder="Buscar jogador por nome...">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
                <select class="filter-select">
                    <option>Todas Posições</option>
                    <option>Goleiro</option>
                    <option>Defesa</option>
                    <option>Meio-Campo</option>
                    <option>Atacante</option>
                </select>
                <select class="filter-select">
                    <option>Todos Status</option>
                    <option>Avaliado</option>
                    <option>Pendente</option>
                    <option>Aprovado</option>
                </select>
            </div>

            <div class="table-container">
                <div class="cards-grid" id="cardsGrid">
                    @if(isset($jogadores) && $jogadores->count() > 0)
                        @foreach($jogadores as $jogador)
                            <div class="card" data-id="{{ $jogador->jogador_id }}">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <div class="card-photo">
                                            <img src="{{ $jogador->pessoa->foto_perfil_url_complete }}" alt="sem foto"
                                                class="player-photo">
                                            <form action="{{ route('jogadores.delete', $jogador->id) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este jogador?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button-card-delete">
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
                                            </form>
                                            <div class="rating">{{ $jogador->rating_medio ?? 0 }}</div>
                                            <div class="position-badge">{{ $jogador->posicao_abreviada }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="back-header ">{{ $jogador->pessoa->nome_completo }}</div>
                                        <div class="player-info back-content-scrollable">
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
                                            onclick="window.location.href='{{ route('jogadores.info', ['jogadores' => $jogador->id]) }}'">Ver
                                            Mais</span>
                                        {{-- **AQUI ESTÁ O FORMULÁRIO E BOTÃO DE EXCLUIR ADICIONADOS** --}}

                                        {{-- **FIM DA ADIÇÃO** --}}
                                    </div>
                                </div>
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

    <script src="{{ asset('js/player.js') }}"></script>
    </body>

    </html>

@endsection