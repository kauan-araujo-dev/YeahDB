document.addEventListener('DOMContentLoaded', function () {
  function ensureLinhaEventosExists(fallbackInsertBefore) {
    let container = document.querySelector('.linha-eventos');
    if (!container) {
      container = document.createElement('div');
      container.className = 'linha_cards linha-eventos';
      if (fallbackInsertBefore && fallbackInsertBefore.parentNode) {
        fallbackInsertBefore.parentNode.insertBefore(container, fallbackInsertBefore);
      } else {
        document.getElementById('secao_bandas').appendChild(container);
      }
    }
    return container;
  }

  function onClickMostrarMais(e) {
    const a = e.currentTarget;
    e.preventDefault();

    const source = a.dataset.source || 'eventos';
    let page = parseInt(a.dataset.page || '2', 10);
    const perPage = a.dataset.perPage ? parseInt(a.dataset.perPage, 10) : undefined;
    const seed = a.dataset.seed || '';

    const ajaxUrl = new URL(window.location.origin + window.location.pathname);
    ajaxUrl.searchParams.set('ajax', '1');
    ajaxUrl.searchParams.set('source', source);
    ajaxUrl.searchParams.set('page', page);
    if (perPage) ajaxUrl.searchParams.set('perPage', perPage);
    if (seed) ajaxUrl.searchParams.set('seed', seed);

    const currentParams = new URL(window.location.href).searchParams;
    ['estado','cidade','estilo'].forEach(function(k){
      if (currentParams.get(k)) ajaxUrl.searchParams.set(k, currentParams.get(k));
    });

    a.classList.add('loading');
    const originalText = a.textContent;
    a.textContent = 'Carregando...';

    fetch(ajaxUrl.toString(), { credentials: 'same-origin' })
      .then(function (res) { return res.text(); })
      .then(function (txt) {
        let json;
        try {
          json = JSON.parse(txt);
        } catch (err) {
          console.error('Resposta inválida do servidor:', txt);
          a.textContent = 'Erro — tente novamente';
          a.classList.remove('loading');
          return;
        }

        if (json.error) {
          console.error('Erro do servidor:', json.error);
          a.textContent = 'Erro — tente novamente';
          a.classList.remove('loading');
          return;
        }

        // garantir que existe um único container .linha-eventos
        const container = ensureLinhaEventosExists(a.parentNode);

        try {
          // Inserir os cards no final do container
          container.insertAdjacentHTML('beforeend', json.html);
        } catch (err) {
          console.error('Erro inserindo HTML:', err);
        }

        if (json.hasMore) {
          page = page + 1;
          a.dataset.page = String(page);
          if (json.seed) a.dataset.seed = String(json.seed);
          a.textContent = originalText;
          a.classList.remove('loading');
        } else {
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

  // --- selects customizados (manter comportamento existente) ---
  document.querySelectorAll('.custom-select').forEach(function (sel) {
    const header = sel.querySelector('.select-header');
    const list = sel.querySelector('.select-list');
    const resetBtn = sel.querySelector('.reset-select');

    header.addEventListener('click', function () {
      list.classList.toggle('show');
      const arrow = header.querySelector('.arrow');
      if (arrow) arrow.classList.toggle('rotate');
    });

    document.addEventListener('click', function (ev) {
      if (!sel.contains(ev.target)) {
        list.classList.remove('show');
        const arrow = header.querySelector('.arrow');
        if (arrow) arrow.classList.remove('rotate');
      }
    });

    list.querySelectorAll('li').forEach(function (li) {
      li.addEventListener('click', function () {
        const field = sel.dataset.field;
        const value = li.textContent.trim();
        const url = new URL(window.location.href);
        url.searchParams.set(field, value);
        url.searchParams.set('page', '1');
        url.searchParams.delete('seed');
        window.location.href = url.toString();
      });
    });

    resetBtn.addEventListener('click', function (ev) {
      ev.stopPropagation();
      const field = sel.dataset.field;
      const url = new URL(window.location.href);
      url.searchParams.delete(field);
      url.searchParams.set('page', '1');
      url.searchParams.delete('seed');
      window.location.href = url.toString();
    });
  });
});