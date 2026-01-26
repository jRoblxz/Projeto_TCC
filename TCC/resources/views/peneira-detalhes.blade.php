@extends('navbar') @section('body-class', 'fundo-especial') @section('content')
<div class="container">
    <div class="header">
        <h1>⚽ Editor de Times - Estilo FIFA</h1>
    </div>

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
        <button class="btn-time" onclick="saveTeams()">💾 Salvar Times</button>
        <button class="btn-time btn-time-secondary" onclick="resetTeams()">🔄 Resetar</button>
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

    let teams = {
        A: [
            { id: 1, name: 'K.Navas', pos: 'GK', secondaryPos: '-', rating: 8.5, inField: true },
            { id: 2, name: 'T.Silva', pos: 'CB', secondaryPos: 'RB', rating: 8.7, inField: true },
            { id: 3, name: 'Marquinhos', pos: 'CB', secondaryPos: 'CDM', rating: 9.0, inField: true },
            { id: 4, name: 'Kehrer', pos: 'CB', secondaryPos: 'RB', rating: 7.5, inField: true },
            { id: 5, name: 'Juan Bernat', pos: 'LB', secondaryPos: 'LM', rating: 7.8, inField: true },
            { id: 6, name: 'Verratti', pos: 'CM', secondaryPos: 'CDM', rating: 8.8, inField: true },
            { id: 7, name: 'Gana', pos: 'CM', secondaryPos: 'CDM', rating: 8.2, inField: true },
            { id: 8, name: 'Di María', pos: 'RM', secondaryPos: 'RW', rating: 8.9, inField: true },
            { id: 9, name: 'Neymar Jr', pos: 'LM', secondaryPos: 'LW', rating: 9.5, inField: true },
            { id: 10, name: 'Mbappé', pos: 'ST', secondaryPos: 'LW', rating: 9.6, inField: true },
            { id: 11, name: 'Icardi', pos: 'ST', secondaryPos: 'CF', rating: 8.4, inField: true },
            { id: 12, name: 'Donnarumma', pos: 'GK', secondaryPos: '-', rating: 8.9, inField: false },
            { id: 13, name: 'Hakimi', pos: 'RB', secondaryPos: 'RM', rating: 8.6, inField: false }
        ],
        B: [
            { id: 14, name: 'Courtois', pos: 'GK', secondaryPos: '-', rating: 9.0, inField: true },
            { id: 15, name: 'Carvajal', pos: 'RB', secondaryPos: 'RM', rating: 8.3, inField: true },
            { id: 16, name: 'Sergio Ramos', pos: 'CB', secondaryPos: 'CDM', rating: 9.1, inField: true },
            { id: 17, name: 'R.Varane', pos: 'CB', secondaryPos: '-', rating: 8.8, inField: true },
            { id: 18, name: 'Marcelo', pos: 'LB', secondaryPos: 'LM', rating: 8.5, inField: true },
            { id: 19, name: 'Casemiro', pos: 'CDM', secondaryPos: 'CB', rating: 8.9, inField: true },
            { id: 20, name: 'Modrić', pos: 'CM', secondaryPos: 'CAM', rating: 9.2, inField: true },
            { id: 21, name: 'Kroos', pos: 'CM', secondaryPos: 'CDM', rating: 9.0, inField: true },
            { id: 22, name: 'Benzema', pos: 'ST', secondaryPos: 'CF', rating: 9.3, inField: true },
            { id: 23, name: 'Bale', pos: 'RW', secondaryPos: 'ST', rating: 8.4, inField: true },
            { id: 24, name: 'Hazard', pos: 'LW', secondaryPos: 'CAM', rating: 8.6, inField: true },
            { id: 25, name: 'Militão', pos: 'CB', secondaryPos: 'RB', rating: 8.2, inField: false }
        ]
    };

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
                    <div class="player-rating">${player.rating.toFixed(1)}</div>
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
                    <span class="rating-badge">${player.rating.toFixed(1)}</span>
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
        const data = JSON.stringify(teams, null, 2);
        console.log('Times salvos:', data);
        alert('Times salvos com sucesso! Verifique o console para ver os dados.');
    }

    function resetTeams() {
        if (confirm('Deseja resetar todos os times para a configuração inicial?')) {
            location.reload();
        }
    }

    // Inicializar
    init();
</script>
</body>

</html>