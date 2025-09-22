<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartas de Jogadores</title>
    <link rel="stylesheet" href="/css/style_crud.css">
</head>
<body>
    <div class="container">
        <h1>Cartas de Jogadores</h1>
        
        <div class="cards-grid" id="cardsGrid">
            <div class="card" data-id="1">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn delete-btn" onclick="deleteCard(1, event)" title="Deletar">🗑️</button>
                        </div>
                        <div class="card-photo">
                            <img src="{{ asset('img/neymar.jpeg') }}" alt="neymar" class="player-photo">
                            <div class="rating">89</div>                            
                            <div class="position-badge">ATA</div>
                        </div>
                        
                    </div>
                    <div class="card-back">
                        <div class="back-header">CRISTIANO RONALDO</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Nome:</span>
                                <span class="info-value">Cristiano Ronaldo</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">1,87m</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">84kg</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">Direito</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">
                                Lenda do futebol, máquina de gols. Finalização impecável, cabeceio excepcional e mentalidade vencedora. Um dos maiores da história.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" data-id="2">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn edit-btn" onclick="editCard(2, event)" title="Editar">✏️</button>
                            <button class="action-btn delete-btn" onclick="deleteCard(2, event)" title="Deletar">🗑️</button>
                        </div>
                        <div class="card-header">
                            <div class="rating">91</div>
                            <div class="position-badge">MEI</div>
                        </div>
                        <div class="card-photo">
                            <div class="placeholder-photo">⚽</div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="back-header">LIONEL MESSI</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Nome:</span>
                                <span class="info-value">Lionel Messi</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">1,70m</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">72kg</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">Esquerdo</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">
                                Gênio do futebol, drible desconcertante, passes milimétricos. Visão de jogo única, finalização certeira. O melhor de todos os tempos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" data-id="3">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn edit-btn" onclick="editCard(3, event)" title="Editar">✏️</button>
                            <button class="action-btn delete-btn" onclick="deleteCard(3, event)" title="Deletar">🗑️</button>
                        </div>
                        <div class="card-header">
                            <div class="rating">85</div>
                            <div class="position-badge">ZAG</div>
                        </div>
                        <div class="card-photo">
                            <div class="placeholder-photo">⚽</div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="back-header">VIRGIL VAN DIJK</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Nome:</span>
                                <span class="info-value">Virgil van Dijk</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">1,95m</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">92kg</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">Direito</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">
                                Zagueiro imponente, liderança defensiva excepcional. Jogo aéreo dominante, passes precisos e velocidade surpreendente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" data-id="4">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn edit-btn" onclick="editCard(4, event)" title="Editar">✏️</button>
                            <button class="action-btn delete-btn" onclick="deleteCard(4, event)" title="Deletar">🗑️</button>
                        </div>
                        <div class="card-header">
                            <div class="rating">87</div>
                            <div class="position-badge">GOL</div>
                        </div>
                        <div class="card-photo">
                            <div class="placeholder-photo">🧤</div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="back-header">ALISSON BECKER</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Nome:</span>
                                <span class="info-value">Alisson Becker</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">1,91m</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">88kg</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">Direito</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">
                                Goleiro seguro, reflexos excepcionais. Distribuição precisa, liderança na área. Um dos melhores goleiros da atualidade.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card add-card" onclick="addNewCard()">
                <div class="add-icon">+</div>
            </div>
        </div>
    </div>

    <script>
        let cardIdCounter = 5;

        function flipCard(cardElement) {
            cardElement.classList.toggle('flipped');
        }

        function editCard(id, event) {
            event.stopPropagation();
            const card = document.querySelector(`[data-id="${id}"]`);
            const nameElement = card.querySelector('.back-header');
            const currentName = nameElement.textContent;
            
            const newName = prompt('Editar nome do jogador:', currentName);
            if (newName && newName.trim()) {
                nameElement.textContent = newName.toUpperCase();
                const infoValue = card.querySelector('.info-value');
                if (infoValue) {
                    infoValue.textContent = newName;
                }
            }
        }

        function deleteCard(id, event) {
            event.stopPropagation();
            
            if (confirm('Tem certeza que deseja deletar esta carta?')) {
                const card = document.querySelector(`[data-id="${id}"]`);
                card.style.transform = 'scale(0) rotate(180deg)';
                card.style.opacity = '0';
                
                setTimeout(() => {
                    card.remove();
                }, 300);
            }
        }

        function addNewCard() {
            const name = prompt('Nome do jogador:');
            if (!name || !name.trim()) return;
            
            const position = prompt('Posição (ATA, MEI, ZAG, LAT, VOL, GOL):') || 'ATA';
            const rating = prompt('Rating (50-99):') || '75';
            const height = prompt('Altura (ex: 1,80m):') || '1,75m';
            const weight = prompt('Peso (ex: 75kg):') || '75kg';
            const foot = prompt('Pé preferido (Direito/Esquerdo):') || 'Direito';
            const evaluation = prompt('Avaliação do jogador:') || 'Novo talento promissor do futebol mundial.';
            
            const playerIcons = {
                'GOL': '🧤',
                'ZAG': '🛡️',
                'LAT': '⚡',
                'VOL': '🎯',
                'MEI': '🎨',
                'ATA': '⚽'
            };
            const icon = playerIcons[position.toUpperCase()] || '⚽';
            
            const newCard = document.createElement('div');
            newCard.className = 'card';
            newCard.setAttribute('data-id', cardIdCounter);
            
            newCard.innerHTML = `
                <div class="card-inner">
                    <div class="card-front">
                        <div class="card-actions">
                            <button class="action-btn edit-btn" onclick="editCard(${cardIdCounter}, event)" title="Editar">✏️</button>
                            <button class="action-btn delete-btn" onclick="deleteCard(${cardIdCounter}, event)" title="Deletar">🗑️</button>
                        </div>
                        <div class="card-header">
                            <div class="rating">${rating}</div>
                            <div class="position-badge">${position.toUpperCase()}</div>
                        </div>
                        <div class="card-photo">
                            <div class="placeholder-photo">${icon}</div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="back-header">${name.toUpperCase()}</div>
                        <div class="player-info">
                            <div class="info-row">
                                <span class="info-label">Nome:</span>
                                <span class="info-value">${name}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Altura:</span>
                                <span class="info-value">${height}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Peso:</span>
                                <span class="info-value">${weight}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pé:</span>
                                <span class="info-value">${foot}</span>
                            </div>
                        </div>
                        <div class="evaluation">
                            <div class="evaluation-title">AVALIAÇÃO</div>
                            <div class="evaluation-text">${evaluation}</div>
                        </div>
                    </div>
                </div>
            `;
            
            const addCard = document.querySelector('.add-card');
            const cardsGrid = document.getElementById('cardsGrid');
            cardsGrid.insertBefore(newCard, addCard);
            
            // Adicionar event listener para virar a carta
            newCard.addEventListener('click', function() {
                flipCard(this);
            });
            
            // Animação de entrada
            newCard.style.transform = 'scale(0)';
            newCard.style.opacity = '0';
            
            setTimeout(() => {
                newCard.style.transition = 'all 0.5s';
                newCard.style.transform = 'scale(1)';
                newCard.style.opacity = '1';
            }, 100);
            
            cardIdCounter++;
        }

        // Adicionar event listeners para todas as cartas existentes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card:not(.add-card)');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    flipCard(this);
                });
            });
        });
    </script>
</body>
</html>