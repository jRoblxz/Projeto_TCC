<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Times</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .teams-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .team-section {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
        }

        .team-name {
            font-size: 24px;
            font-weight: bold;
        }

        .formation-select {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            background: rgba(255,255,255,0.9);
            color: #1e3c72;
            font-weight: bold;
            cursor: pointer;
        }

        .field-container {
            background: linear-gradient(to bottom, #2d5016 0%, #3d6b1f 50%, #2d5016 100%);
            border-radius: 10px;
            padding: 20px;
            position: relative;
            min-height: 500px;
            background-image: 
                repeating-linear-gradient(0deg, transparent, transparent 49px, rgba(255,255,255,0.1) 49px, rgba(255,255,255,0.1) 50px),
                repeating-linear-gradient(90deg, transparent, transparent 49px, rgba(255,255,255,0.1) 49px, rgba(255,255,255,0.1) 50px);
            border: 3px solid #fff;
        }

        .field-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 2px solid rgba(255,255,255,0.4);
            border-radius: 50%;
        }

        .player-position {
            position: absolute;
            transform: translate(-50%, -50%);
            cursor: move;
            transition: all 0.3s ease;
        }

        .player-card {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 8px;
            min-width: 80px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        .player-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }

        .player-card.dragging {
            opacity: 0.5;
        }

        .player-rating {
            font-size: 20px;
            font-weight: bold;
            color: #ffd700;
            margin-bottom: 3px;
        }

        .player-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-pos {
            font-size: 10px;
            color: #90caf9;
        }

        .players-list {
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
            background: rgba(0,0,0,0.2);
            border-radius: 8px;
            padding: 10px;
        }

        .list-player {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: move;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .list-player:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }

        .list-player.dragging {
            opacity: 0.5;
        }

        .player-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .player-info span {
            font-size: 14px;
        }

        .rating-badge {
            background: #ffd700;
            color: #1a237e;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            min-width: 40px;
            text-align: center;
        }

        .controls {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            background: linear-gradient(135deg, #00c853 0%, #00e676 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            transition: all 0.3s;
            margin: 0 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ff6f00 0%, #ff8f00 100%);
        }

        .drop-zone {
            border: 3px dashed rgba(255,215,0,0.5);
            background: rgba(255,215,0,0.1);
        }

        @media (max-width: 1024px) {
            .teams-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚽ Editor de Times - Estilo FIFA</h1>
        
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
            <button class="btn" onclick="saveTeams()">💾 Salvar Times</button>
            <button class="btn btn-secondary" onclick="resetTeams()">🔄 Resetar</button>
        </div>
    </div>

    <script>
        const formations = {
            '4-4-2': [
                {pos: 'GK', x: 50, y: 90},
                {pos: 'LB', x: 20, y: 70}, {pos: 'CB', x: 40, y: 75}, {pos: 'CB', x: 60, y: 75}, {pos: 'RB', x: 80, y: 70},
                {pos: 'LM', x: 20, y: 45}, {pos: 'CM', x: 40, y: 50}, {pos: 'CM', x: 60, y: 50}, {pos: 'RM', x: 80, y: 45},
                {pos: 'ST', x: 40, y: 20}, {pos: 'ST', x: 60, y: 20}
            ],
            '4-3-3': [
                {pos: 'GK', x: 50, y: 90},
                {pos: 'LB', x: 20, y: 70}, {pos: 'CB', x: 40, y: 75}, {pos: 'CB', x: 60, y: 75}, {pos: 'RB', x: 80, y: 70},
                {pos: 'CM', x: 30, y: 50}, {pos: 'CM', x: 50, y: 55}, {pos: 'CM', x: 70, y: 50},
                {pos: 'LW', x: 20, y: 25}, {pos: 'ST', x: 50, y: 20}, {pos: 'RW', x: 80, y: 25}
            ],
            '3-5-2': [
                {pos: 'GK', x: 50, y: 90},
                {pos: 'CB', x: 30, y: 75}, {pos: 'CB', x: 50, y: 78}, {pos: 'CB', x: 70, y: 75},
                {pos: 'LM', x: 15, y: 50}, {pos: 'CM', x: 35, y: 55}, {pos: 'CM', x: 50, y: 50}, {pos: 'CM', x: 65, y: 55}, {pos: 'RM', x: 85, y: 50},
                {pos: 'ST', x: 40, y: 20}, {pos: 'ST', x: 60, y: 20}
            ],
            '4-2-3-1': [
                {pos: 'GK', x: 50, y: 90},
                {pos: 'LB', x: 20, y: 70}, {pos: 'CB', x: 40, y: 75}, {pos: 'CB', x: 60, y: 75}, {pos: 'RB', x: 80, y: 70},
                {pos: 'CDM', x: 40, y: 55}, {pos: 'CDM', x: 60, y: 55},
                {pos: 'LM', x: 25, y: 35}, {pos: 'CAM', x: 50, y: 40}, {pos: 'RM', x: 75, y: 35},
                {pos: 'ST', x: 50, y: 15}
            ]
        };

        let teams = {
            A: [
                {id: 1, name: 'K.Navas', pos: 'GK', secondaryPos: '-', rating: 8.5, inField: true},
                {id: 2, name: 'T.Silva', pos: 'CB', secondaryPos: 'RB', rating: 8.7, inField: true},
                {id: 3, name: 'Marquinhos', pos: 'CB', secondaryPos: 'CDM', rating: 9.0, inField: true},
                {id: 4, name: 'Kehrer', pos: 'CB', secondaryPos: 'RB', rating: 7.5, inField: true},
                {id: 5, name: 'Juan Bernat', pos: 'LB', secondaryPos: 'LM', rating: 7.8, inField: true},
                {id: 6, name: 'Verratti', pos: 'CM', secondaryPos: 'CDM', rating: 8.8, inField: true},
                {id: 7, name: 'Gana', pos: 'CM', secondaryPos: 'CDM', rating: 8.2, inField: true},
                {id: 8, name: 'Di María', pos: 'RM', secondaryPos: 'RW', rating: 8.9, inField: true},
                {id: 9, name: 'Neymar Jr', pos: 'LM', secondaryPos: 'LW', rating: 9.5, inField: true},
                {id: 10, name: 'Mbappé', pos: 'ST', secondaryPos: 'LW', rating: 9.6, inField: true},
                {id: 11, name: 'Icardi', pos: 'ST', secondaryPos: 'CF', rating: 8.4, inField: true},
                {id: 12, name: 'Donnarumma', pos: 'GK', secondaryPos: '-', rating: 8.9, inField: false},
                {id: 13, name: 'Hakimi', pos: 'RB', secondaryPos: 'RM', rating: 8.6, inField: false}
            ],
            B: [
                {id: 14, name: 'Courtois', pos: 'GK', secondaryPos: '-', rating: 9.0, inField: true},
                {id: 15, name: 'Carvajal', pos: 'RB', secondaryPos: 'RM', rating: 8.3, inField: true},
                {id: 16, name: 'Sergio Ramos', pos: 'CB', secondaryPos: 'CDM', rating: 9.1, inField: true},
                {id: 17, name: 'R.Varane', pos: 'CB', secondaryPos: '-', rating: 8.8, inField: true},
                {id: 18, name: 'Marcelo', pos: 'LB', secondaryPos: 'LM', rating: 8.5, inField: true},
                {id: 19, name: 'Casemiro', pos: 'CDM', secondaryPos: 'CB', rating: 8.9, inField: true},
                {id: 20, name: 'Modrić', pos: 'CM', secondaryPos: 'CAM', rating: 9.2, inField: true},
                {id: 21, name: 'Kroos', pos: 'CM', secondaryPos: 'CDM', rating: 9.0, inField: true},
                {id: 22, name: 'Benzema', pos: 'ST', secondaryPos: 'CF', rating: 9.3, inField: true},
                {id: 23, name: 'Bale', pos: 'RW', secondaryPos: 'ST', rating: 8.4, inField: true},
                {id: 24, name: 'Hazard', pos: 'LW', secondaryPos: 'CAM', rating: 8.6, inField: true},
                {id: 25, name: 'Militão', pos: 'CB', secondaryPos: 'RB', rating: 8.2, inField: false}
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
                    <div class="player-name">${player.name}</div>
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
                <div class="player-info">
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