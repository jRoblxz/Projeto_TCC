@extends('navbar')
@section('body-class', 'fundo-especial')
@section('content')
<div class="container">
    <div class="header">
        <h1>Editor de Times - {{ $peneira->nome_evento }}</h1>
        <p class="subtitle">Peneira em {{ $peneira->local }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="teams-container">
        <!-- Time A -->
        <div class="team-section">
            <div class="team-header">
                <div class="team-name">Time A</div>
                <select class="formation-select" id="formationA" onchange="changeFormation('A')">
                    <option value="4-4-2">4-4-2</option>
                    <option value="4-3-3">4-3-3</option>
                    <option value="3-5-2">3-5-2</option>
                    <option value="4-2-3-1">4-2-3-1</option>
                </select>
            </div>
            <div class="field-container" id="fieldA" ondrop="drop(event, 'A')" ondragover="allowDrop(event)">
                <div class="field-lines">
                    <div class="center-circle"></div>
                </div>
            </div>
            <div class="players-list" id="listA"></div>
        </div>

        <!-- Time B -->
        <div class="team-section">
            <div class="team-header">
                <div class="team-name">Time B</div>
                <select class="formation-select" id="formationB" onchange="changeFormation('B')">
                    <option value="4-4-2">4-4-2</option>
                    <option value="4-3-3">4-3-3</option>
                    <option value="3-5-2">3-5-2</option>
                    <option value="4-2-3-1">4-2-3-1</option>
                </select>
            </div>
            <div class="field-container" id="fieldB" ondrop="drop(event, 'B')" ondragover="allowDrop(event)">
                <div class="field-lines">
                    <div class="center-circle"></div>
                </div>
            </div>
            <div class="players-list" id="listB"></div>
        </div>
    </div>

    <div class="controls">
        <button class="btn-time" onclick="saveTeams()">Salvar Times</button>
        <button class="btn-time btn-time-secondary" onclick="resetTeams()">Resetar</button>
        <a href="{{ route('peneira.index', $peneira->id) }}" class="btn-time btn-time-secondary">← Voltar</a>
    </div>
</div>

<script>
    const formations = {
        '4-4-2': [
            { pos: 'GK', x: 50, y: 90 },
            { pos: 'LB', x: 20, y: 70 }, { pos: 'CB', x: 40, y: 75 }, { pos: 'CB', x: 60, y: 75 }, { pos: 'RB', x: 80, y: 70 },
            { pos: 'LM', x: 20, y: 45 }, { pos: 'CM', x: 40, y: 50 }, { pos: 'CM', x: 60, y: 50 }, { pos: 'RM', x: 80, y: 45 },
            { pos: 'ST', x: 40, y: 20 }, { pos: 'ST', x: 60, y: 20 }
        ],
        '4-3-3': [
            { pos: 'GK', x: 50, y: 90 },
            { pos: 'LB', x: 20, y: 70 }, { pos: 'CB', x: 40, y: 75 }, { pos: 'CB', x: 60, y: 75 }, { pos: 'RB', x: 80, y: 70 },
            { pos: 'CM', x: 30, y: 50 }, { pos: 'CM', x: 50, y: 55 }, { pos: 'CM', x: 70, y: 50 },
            { pos: 'LW', x: 20, y: 25 }, { pos: 'ST', x: 50, y: 20 }, { pos: 'RW', x: 80, y: 25 }
        ],
        '3-5-2': [
            { pos: 'GK', x: 50, y: 90 },
            { pos: 'CB', x: 30, y: 75 }, { pos: 'CB', x: 50, y: 78 }, { pos: 'CB', x: 70, y: 75 },
            { pos: 'LM', x: 15, y: 50 }, { pos: 'CM', x: 35, y: 55 }, { pos: 'CM', x: 50, y: 50 }, { pos: 'CM', x: 65, y: 55 }, { pos: 'RM', x: 85, y: 50 },
            { pos: 'ST', x: 40, y: 20 }, { pos: 'ST', x: 60, y: 20 }
        ],
        '4-2-3-1': [
            { pos: 'GK', x: 50, y: 90 },
            { pos: 'LB', x: 20, y: 70 }, { pos: 'CB', x: 40, y: 75 }, { pos: 'CB', x: 60, y: 75 }, { pos: 'RB', x: 80, y: 70 },
            { pos: 'CDM', x: 40, y: 55 }, { pos: 'CDM', x: 60, y: 55 },
            { pos: 'LM', x: 25, y: 35 }, { pos: 'CAM', x: 50, y: 40 }, { pos: 'RM', x: 75, y: 35 },
            { pos: 'ST', x: 50, y: 15 }
        ]
    };

    // Carrega os dados do backend
    let teams = {!! $teamsJson !!};

    // Se não tem dados, inicializa vazio
    if (!teams.A) teams.A = [];
    if (!teams.B) teams.B = [];

    let draggedPlayer = null;
    let draggedFromTeam = null;

    function init() {
        renderTeam('A');
        renderTeam('B');
    }

    function renderTeam(team) {
        const field = document.getElementById('field' + team);
        const list = document.getElementById('list' + team);
        const formation = document.getElementById('formation' + team).value;

        // Limpar campo
        field.querySelectorAll('.player-position').forEach(el => el.remove());
        list.innerHTML = '';

        const positions = formations[formation];
        let posIndex = 0;

        teams[team].forEach(player => {
            if (player.inField && posIndex < positions.length) {
                // Adicionar ao campo
                const pos = positions[posIndex];
                const playerEl = createPlayerCard(player, team);
                playerEl.style.left = pos.x + '%';
                playerEl.style.top = pos.y + '%';
                field.appendChild(playerEl);
                posIndex++;
            } else {
                // Adicionar à lista
                list.appendChild(createListPlayer(player, team));
            }
        });
    }

    function createPlayerCard(player, team) {
        const div = document.createElement('div');
        div.className = 'player-position';
        div.draggable = true;
        div.dataset.playerId = player.id;
        div.dataset.team = team;

        div.innerHTML = `
            <div class="player-card">
                <div class="player-rating">${Number(player.rating).toFixed(1)}</div>
                <div class="player-name-time">${player.name}</div>
                <div class="player-pos">${player.pos}${player.secondaryPos !== '-' ? '/' + player.secondaryPos : ''}</div>
            </div>
        `;

        div.addEventListener('dragstart', dragStart);
        div.addEventListener('dragend', dragEnd);

        return div;
    }

    function createListPlayer(player, team) {
        const div = document.createElement('div');
        div.className = 'list-player';
        div.draggable = true;
        div.dataset.playerId = player.id;
        div.dataset.team = team;

        div.innerHTML = `
            <div class="player-info-time">
                <span class="rating-badge">${Number(player.rating).toFixed(1)}</span>
                <span><strong>${player.name}</strong></span>
                <span>${player.pos}${player.secondaryPos !== '-' ? '/' + player.secondaryPos : ''}</span>
            </div>
        `;

        div.addEventListener('dragstart', dragStart);
        div.addEventListener('dragend', dragEnd);

        return div;
    }

    function dragStart(e) {
        draggedPlayer = parseInt(e.target.closest('[data-player-id]').dataset.playerId);
        draggedFromTeam = e.target.closest('[data-player-id]').dataset.team;
        e.target.closest('[data-player-id]').classList.add('dragging');

        // Destacar zonas de drop
        document.querySelectorAll('.field-container, .players-list').forEach(el => {
            el.classList.add('drop-zone');
        });
    }

    function dragEnd(e) {
        e.target.closest('[data-player-id]').classList.remove('dragging');
        document.querySelectorAll('.drop-zone').forEach(el => {
            el.classList.remove('drop-zone');
        });
    }

    function allowDrop(e) {
        e.preventDefault();
    }

    function drop(e, targetTeam) {
        e.preventDefault();

        if (!draggedPlayer) return;

        const targetIsField = e.target.closest('.field-container');
        const targetIsList = e.target.closest('.players-list');

        // Remover jogador do time original
        const playerIndex = teams[draggedFromTeam].findIndex(p => p.id === draggedPlayer);
        const player = teams[draggedFromTeam][playerIndex];

        if (draggedFromTeam !== targetTeam) {
            // Mover entre times
            teams[draggedFromTeam].splice(playerIndex, 1);
            player.inField = targetIsField ? true : false;
            teams[targetTeam].push(player);
        } else {
            // Mover dentro do mesmo time
            if (targetIsField) {
                player.inField = true;
            } else if (targetIsList) {
                player.inField = false;
            }
        }

        renderTeam('A');
        renderTeam('B');

        draggedPlayer = null;
        draggedFromTeam = null;
    }

    function changeFormation(team) {
        renderTeam(team);
    }

    function saveTeams() {
        const saveButton = event.target;
        saveButton.disabled = true;
        saveButton.textContent = '💾 Salvando...';

        fetch('{{ route("peneira.salvar-equipes", $peneira->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ teams: teams })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('❌ Erro ao salvar os times. Tente novamente.');
        })
        .finally(() => {
            saveButton.disabled = false;
            saveButton.textContent = 'Salvar Times';
        });
    }

    function resetTeams() {
        if (confirm('Deseja resetar todos os times para a configuração inicial?')) {
            location.reload();
        }
    }

    // Inicializar
    init();
</script>

<style>
    .subtitle {
        text-align: center;
        color: #666;
        margin-top: -10px;
        margin-bottom: 20px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
@endsection