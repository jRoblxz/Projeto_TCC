// ========== AGUARDA O DOM CARREGAR ==========
console.log('🔥 player-filter.js carregado!');

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== FILTRO DE BUSCA ==========
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.card:not(.add-card)');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            cards.forEach(card => {
                const name = card.dataset.name || '';
                const position = card.dataset.position || '';
                const subdivisao = card.dataset.subdivisao?.toLowerCase() || '';
                
                const matches = name.includes(searchTerm) || 
                               position.includes(searchTerm) || 
                               subdivisao.includes(searchTerm);
                
                if (matches) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
            
            updateNoResultsMessage();
        });
    }

    // ========== FILTROS POR CATEGORIA ==========
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Filtro clicado:', this.dataset.filter);
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            cards.forEach(card => {
                const subdivisao = card.dataset.subdivisao || '';
                const rating = parseFloat(card.dataset.rating) || 0;
                
                let shouldShow = false;
                
                if (filter === 'all') {
                    shouldShow = true;
                } else if (filter === 'high-rating') {
                    shouldShow = rating >= 8;
                } else {
                    shouldShow = subdivisao === filter;
                }
                
                if (shouldShow) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
            
            if (searchInput) {
                searchInput.value = '';
            }
            updateNoResultsMessage();
        });
    });

    // ========== MENSAGEM "SEM RESULTADOS" ==========
    function updateNoResultsMessage() {
        const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
        const cardsGrid = document.getElementById('cardsGrid');
        let noResultsMsg = document.getElementById('noResultsMessage');
        
        if (visibleCards.length === 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.innerHTML = `
                    <div class="empty-state-icon">🔍</div>
                    <p>Nenhum jogador encontrado</p>
                `;
                cardsGrid.insertBefore(noResultsMsg, cardsGrid.firstChild);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }

    // ========== SUBMIT DO FORMULÁRIO DE RATING ==========
    const ratingForm = document.getElementById('ratingForm');
    
    if (ratingForm) {
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const csrfToken = document.querySelector('input[name="_token"]');
            
            if (!csrfToken) {
                console.error('Token CSRF não encontrado');
                showNotification('Erro: Token de segurança não encontrado', 'error');
                return;
            }
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.querySelector(`.card[data-id="${data.jogador_id}"]`);
                    if (card) {
                        const ratingElement = card.querySelector('.rating');
                        if (ratingElement) {
                            ratingElement.textContent = parseFloat(data.novo_rating).toFixed(1);
                            card.dataset.rating = data.novo_rating;
                        }
                    }
                    
                    closeRatingModal();
                    showNotification('Rating atualizado com sucesso!', 'success');
                } else {
                    showNotification('Erro ao atualizar rating', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao atualizar rating', 'error');
            });
        });
    }

    // ========== EVENTOS DOS BOTÕES DE AÇÃO ==========
    // Botões de editar rating
    document.querySelectorAll('.edit-rating-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🎯 Botão de editar clicado!');
            
            const card = this.closest('.card');
            if (!card) {
                console.error('Card não encontrado');
                return;
            }
            
            const jogadorId = card.dataset.id;
            const playerName = card.querySelector('.back-header')?.textContent || 'Jogador';
            const rating = card.dataset.rating || 0;
            
            console.log('Dados do jogador:', { jogadorId, playerName, rating });
            
            openRatingModal(jogadorId, playerName, rating);
        });
    });

    // Botões de deletar
    document.querySelectorAll('.button-card-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('🗑️ Botão de deletar clicado!');
        });
    });

    // Previne flip do card ao clicar na área de ações
    document.querySelectorAll('.card-actions-top').forEach(element => {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

}); // FIM DO DOMContentLoaded

// ========== FUNÇÕES GLOBAIS ==========

// ========== MODAL DE EDIÇÃO DE RATING ==========
function openRatingModal(jogadorId, playerName, currentRating) {
    console.log('🎯 Abrindo modal para jogador:', jogadorId);
    console.log('Dados do jogador:', { jogadorId, playerName, currentRating });
    
    const modal = document.getElementById('ratingModal');
    const form = document.getElementById('ratingForm');
    const ratingInput = document.getElementById('rating');
    const playerNameModal = document.getElementById('playerNameModal');
    const ratingValue = document.getElementById('ratingValue');
    
    // Debug detalhado
    console.log('Elementos encontrados:', {
        modal: !!modal,
        form: !!form,
        ratingInput: !!ratingInput,
        playerNameModal: !!playerNameModal,
        ratingValue: !!ratingValue
    });
    
    if (!modal) {
        console.error('❌ Modal (#ratingModal) não encontrado!');
        alert('Erro: Modal não encontrado no HTML');
        return;
    }
    
    if (!form) {
        console.error('❌ Form (#ratingForm) não encontrado!');
        alert('Erro: Formulário não encontrado no HTML');
        return;
    }
    
    if (!ratingInput) {
        console.error('❌ Input (#rating) não encontrado!');
        alert('Erro: Campo de rating não encontrado no HTML');
        return;
    }
    
    if (!playerNameModal) {
        console.error('❌ PlayerName (#playerNameModal) não encontrado!');
        alert('Erro: Nome do jogador não encontrado no HTML');
        return;
    }
    
    if (!ratingValue) {
        console.error('❌ RatingValue (#ratingValue) não encontrado!');
        alert('Erro: Preview do rating não encontrado no HTML');
        return;
    }
    
    // Configura o formulário
    form.action = `/player_upd/${jogadorId}`;
    playerNameModal.textContent = playerName;
    ratingInput.value = currentRating;
    ratingValue.textContent = parseFloat(currentRating).toFixed(1);
    
    console.log('✅ Modal configurado com sucesso!');
    
    // Mostra o modal
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Atualiza preview ao digitar
    const newRatingInput = ratingInput.cloneNode(true);
    ratingInput.parentNode.replaceChild(newRatingInput, ratingInput);
    
    newRatingInput.addEventListener('input', function() {
        const value = parseFloat(this.value || 0);
        const ratingValueElement = document.getElementById('ratingValue');
        if (ratingValueElement) {
            ratingValueElement.textContent = value.toFixed(1);
        }
    });
    
    console.log('✅ Modal aberto com sucesso!');
}

function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }
}

// Fecha modal ao clicar fora
window.addEventListener('click', function(event) {
    const modal = document.getElementById('ratingModal');
    if (event.target === modal) {
        closeRatingModal();
    }
});

// ========== NOTIFICAÇÕES ==========
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}