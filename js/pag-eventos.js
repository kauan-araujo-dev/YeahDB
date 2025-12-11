   document.addEventListener('DOMContentLoaded', function() {

            // função para abrir/fechar selects e reset
            function updateSelects() {
                document.querySelectorAll('.custom-select').forEach(function(cs) {
                    const header = cs.querySelector('.select-header');
                    const list = cs.querySelector('.select-list');
                    const resetBtn = cs.querySelector('.reset-select');
                    let open = false;

                    header.addEventListener('click', function() {
                        list.style.display = open ? 'none' : 'block';
                        open = !open;
                    });
                    document.addEventListener('click', function(e) {
                        if (!cs.contains(e.target)) {
                            list.style.display = 'none';
                            open = false;
                        }
                    });

                    list.querySelectorAll('li').forEach(function(li) {
                        li.addEventListener('click', function() {
                            cs.querySelector('.selected-option').textContent = li.dataset.value;

                            const estado = document.querySelector('.custom-select[data-field="estado"] .selected-option').textContent;
                            const cidade = document.querySelector('.custom-select[data-field="cidade"] .selected-option').textContent;
                            const estilo = document.querySelector('.custom-select[data-field="estilo"] .selected-option').textContent;
                            const params = new URLSearchParams();
                            if (estado && estado !== 'ESTADO') params.set('estado', estado);
                            if (cidade && cidade !== 'CIDADE') params.set('cidade', cidade);
                            if (estilo && estilo !== 'ESTILO MUSICAL') params.set('estilo', estilo);
                            params.set('page', '1');
                            window.location.href = window.location.pathname + '?' + params.toString();
                        });
                    });

                    // Reset button
                    resetBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const field = cs.dataset.field;
                        const estadoSpan = document.querySelector('.custom-select[data-field="estado"] .selected-option');
                        const cidadeSpan = document.querySelector('.custom-select[data-field="cidade"] .selected-option');
                        const estiloSpan = document.querySelector('.custom-select[data-field="estilo"] .selected-option');

                        if (field === 'estado') {
                            estadoSpan.textContent = 'ESTADO';
                            cidadeSpan.textContent = 'CIDADE'; // limpa cidade junto
                        } else if (field === 'cidade') cidadeSpan.textContent = 'CIDADE';
                        else if (field === 'estilo') estiloSpan.textContent = 'ESTILO MUSICAL';

                        const params = new URLSearchParams();
                        const estadoVal = estadoSpan.textContent;
                        if (estadoVal && estadoVal !== 'ESTADO') params.set('estado', estadoVal);
                        const cidadeVal = cidadeSpan.textContent;
                        if (cidadeVal && cidadeVal !== 'CIDADE') params.set('cidade', cidadeVal);
                        const estiloVal = estiloSpan.textContent;
                        if (estiloVal && estiloVal !== 'ESTILO MUSICAL') params.set('estilo', estiloVal);
                        params.set('page', '1');
                        window.location.href = window.location.pathname + '?' + params.toString();
                    });
                });
            }

            updateSelects();

            // Mostrar mais
            document.querySelectorAll('a.mostrar-mais').forEach(function(a) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(a.href);
                    url.searchParams.set('ajax', '1');
                    a.classList.add('loading');
                    a.textContent = 'Carregando...';
                    fetch(url.toString(), {
                        credentials: 'same-origin'
                    }).then(r => r.json()).then(json => {
                        const container = document.querySelector('.linha_cards.linha-eventos');
                        container.insertAdjacentHTML('beforeend', json.html);
                        if (json.hasMore) {
                            a.dataset.page = json.nextPage;
                            a.textContent = 'Mostrar mais';
                            a.classList.remove('loading');
                        } else a.remove();
                    }).catch(() => {
                        a.textContent = 'Erro';
                        a.classList.remove('loading');
                    });
                });
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
  const selects = document.querySelectorAll('.custom-select');

  selects.forEach(select => {
    const header = select.querySelector('.select-header');
    const list = select.querySelector('.select-list');
    const resetBtn = select.querySelector('.reset-select');

    header.tabIndex = 0;

    function closeAllExcept(except) {
      selects.forEach(s => {
        if (s !== except) s.classList.remove('open');
      });
    }

    function openSelect() {
      closeAllExcept(select);
      select.classList.toggle('open');
    }

    header.addEventListener('click', openSelect);

    // resetar filtros
    resetBtn.addEventListener('click', e => {
      e.stopPropagation();
      const field = select.dataset.field;

      // reset texto
      if (field === 'estado') {
        select.querySelector('.selected-option').textContent = 'ESTADO';
        // reset cidade também
        const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"]');
        if (cidadeSelect) cidadeSelect.querySelector('.selected-option').textContent = 'CIDADE';
      } else if (field === 'cidade') {
        select.querySelector('.selected-option').textContent = 'CIDADE';
      } else if (field === 'estilo') {
        select.querySelector('.selected-option').textContent = 'ESTILO MUSICAL';
      }

      // recarregar página sem filtros
      const url = new URL(window.location.href);
      url.searchParams.delete('estado');
      url.searchParams.delete('cidade');
      url.searchParams.delete('estilo');
      url.searchParams.set('page', '1');
      window.location.href = url.toString();
    });

    // clicar fora fecha select
    document.addEventListener('click', e => {
      if (!select.contains(e.target)) select.classList.remove('open');
    });

    // seleção de item
    list.querySelectorAll('li').forEach(li => {
      li.addEventListener('click', () => {
        const field = select.dataset.field;
        select.querySelector('.selected-option').textContent = li.textContent.trim();

        // reset cidade se estado mudou
        if (field === 'estado') {
          const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"]');
          if (cidadeSelect) cidadeSelect.querySelector('.selected-option').textContent = 'CIDADE';
        }

        // reload com filtros
        const estado = document.querySelector('.custom-select[data-field="estado"] .selected-option').textContent;
        const cidade = document.querySelector('.custom-select[data-field="cidade"] .selected-option').textContent;
        const estilo = document.querySelector('.custom-select[data-field="estilo"] .selected-option').textContent;

        const url = new URL(window.location.href);
        url.searchParams.set('page', '1');

        if (estado.toLowerCase() !== 'estado') url.searchParams.set('estado', estado);
        else url.searchParams.delete('estado');

        if (cidade.toLowerCase() !== 'cidade') url.searchParams.set('cidade', cidade);
        else url.searchParams.delete('cidade');

        if (estilo.toLowerCase() !== 'estilo musical') url.searchParams.set('estilo', estilo);
        else url.searchParams.delete('estilo');

        window.location.href = url.toString();
      });
    });
  });
});
