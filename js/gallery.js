document.addEventListener('DOMContentLoaded', function () {
  const main = document.getElementById('main-event-image');
  if (!main) return;

  const thumbs = document.querySelectorAll('.thumb-event');
  if (!thumbs || thumbs.length === 0) return;

  // Marca a primeira miniatura como ativa, se existir
  if (thumbs[0]) thumbs[0].classList.add('active-thumb');

  thumbs.forEach(function (thumb) {
    thumb.style.cursor = 'pointer';
    thumb.addEventListener('click', function () {
      const large = thumb.getAttribute('data-large') || thumb.src;
      main.src = large;
      main.alt = thumb.alt || main.alt;

      // Atualiza classe ativa
      thumbs.forEach(function (t) { t.classList.remove('active-thumb'); });
      thumb.classList.add('active-thumb');

      // Rola suavemente para o destaque (centraliza em tela quando possível)
      try {
        main.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } catch (e) {
        // fallback: sem erro
      }
    });
  });
});

// Troca de miniatura dentro dos cards (delegation)
document.addEventListener('click', function (e) {
  const target = e.target;
  if (!target.classList || !target.classList.contains('card-thumb')) return;

  // encontrar a caixa do card e a imagem principal dentro dela
  const card = target.closest('.caixa_banda');
  if (!card) return;

  // a primeira imagem direta dentro do card é o destaque
  const mainImg = card.querySelector(':scope > img');
  if (!mainImg) return;

  // substituir a src do destaque pela src da miniatura clicada
  try {
    mainImg.src = target.src;
    // marcar miniatura ativa
    const siblings = card.querySelectorAll('.card-thumb');
    siblings.forEach(function (s) { s.classList.remove('active-card-thumb'); });
    target.classList.add('active-card-thumb');
  } catch (err) {
    console.error('Erro ao trocar miniatura do card:', err);
  }
});
