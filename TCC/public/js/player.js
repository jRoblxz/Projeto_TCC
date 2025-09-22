let cardIdCounter = 5;

function flipCard(cardElement) {
    cardElement.classList.toggle('flipped');
}
// Adicionar event listeners para todas as cartas existentes
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.card:not(.add-card)');
    cards.forEach(card => {
        card.addEventListener('click', function () {
            flipCard(this);
        });
    });
});