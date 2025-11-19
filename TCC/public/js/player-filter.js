// ========== FILTRO DE BUSCA ==========
console.log('🔥 player-filter.js carregado!');

const searchInput = document.getElementById('searchInput');
const cards = document.querySelectorAll('.card:not(.add-card)');

searchInput?.addEventListener('input', function() {
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

// ========== FILTROS POR CATEGORIA ==========
const filterButtons = document.querySelectorAll('.filter-btn');

filterButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Remove active de todos
        filterButtons.forEach(btn => btn.classList.remove('active'));
        // Adiciona active no clicado
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
        
        // Limpa o campo de busca ao clicar em filtro
        searchInput.value = '';
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

// ========== MODAL DE EDIÇÃO DE RATING ==========
function openRatingModal(jogadorId, playerName, currentRating) {
    const modal = document.getElementById('ratingModal');
    const form = document.getElementById('ratingForm');
    const ratingInput = document.getElementById('rating');
    const playerNameModal = document.getElementById('playerNameModal');
    const ratingValue = document.getElementById('ratingValue');
    
    // Configura o formulário
    form.action = `/player_upd/${jogadorId}`;
    playerNameModal.textContent = playerName;
    ratingInput.value = currentRating;
    ratingValue.textContent = parseFloat(currentRating).toFixed(1);
    
    // Mostra o modal
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Atualiza preview ao digitar
    ratingInput.addEventListener('input', function() {
        ratingValue.textContent = parseFloat(this.value || 0).toFixed(1);
    });
}

function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

// Fecha modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('ratingModal');
    if (event.target === modal) {
        closeRatingModal();
    }
}

// ========== SUBMIT DO FORMULÁRIO DE RATING ==========
document.getElementById('ratingForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Adiciona o rating ao FormData
    //formData.append('rating_medio', document.getElementById('rating').value);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualiza o card sem recarregar a página
            const card = document.querySelector(`.card[data-id="${data.jogador_id}"]`);
            if (card) {
                const ratingElement = card.querySelector('.rating');
                ratingElement.textContent = parseFloat(data.novo_rating).toFixed(1);
                card.dataset.rating = data.novo_rating;
            }
            
            closeRatingModal();
            
            // Mostra mensagem de sucesso
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