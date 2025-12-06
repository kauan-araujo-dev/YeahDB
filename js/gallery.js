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
