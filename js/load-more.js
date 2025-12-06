document.addEventListener('DOMContentLoaded', function () {
  function onClickMostrarMais(e) {
    const a = e.currentTarget;
    e.preventDefault();

    const source = a.dataset.source;
    let page = parseInt(a.dataset.page || '2', 10);
    const perPage = a.dataset.perPage ? parseInt(a.dataset.perPage, 10) : undefined;

    // Construir URL do endpoint AJAX relativo à página atual
    const ajaxUrl = new URL('ajax/load_more.php', window.location.href);

    // Copiar parâmetros existentes do href (filtros) para o endpoint
    try {
      const pageUrl = new URL(a.href, window.location.href);
      pageUrl.searchParams.forEach((v, k) => ajaxUrl.searchParams.set(k, v));
    } catch (e) {
      // fallback: nada
    }

    ajaxUrl.searchParams.set('source', source);
    ajaxUrl.searchParams.set('page', page);
    if (perPage) ajaxUrl.searchParams.set('perPage', perPage);

    a.classList.add('loading');
    a.textContent = 'Carregando...';

    fetch(ajaxUrl.toString(), { credentials: 'same-origin' })
      .then(function (res) {
        // tentar parsear JSON, se falhar mostrar texto para debug
        return res.text().then(function (txt) {
          try {
            return JSON.parse(txt);
          } catch (err) {
            throw new Error('Resposta inválida do servidor: ' + txt);
          }
        });
      })
      .then(function (json) {
        if (json.error) throw new Error(json.error);

        // Encontra o container de cards mais próximo acima do link
        let container = a.closest('.linha_cards');
        if (container && container.parentNode) {
          const wrapper = document.createElement('div');
          wrapper.className = 'linha_cards';
          wrapper.innerHTML = json.html;
          container.parentNode.insertBefore(wrapper, a.parentNode);
        }

        if (json.hasMore) {
          // incrementar page para o próximo fetch
          a.dataset.page = String(page + 1);
          a.textContent = 'Mostrar mais';
          a.classList.remove('loading');
        } else {
          // remover ou esconder o link
          a.remove();
        }
      })
      .catch(function (err) {
        console.error(err);
        a.textContent = 'Erro — tente novamente';
        a.classList.remove('loading');
      });
  }

  document.querySelectorAll('a.mostrar-mais').forEach(function (a) {
    a.addEventListener('click', onClickMostrarMais);
  });
});
