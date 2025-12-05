document.addEventListener('DOMContentLoaded', function () {
  function qs(selector, el = document) { return el.querySelector(selector); }
  function qsa(selector, el = document) { return Array.from(el.querySelectorAll(selector)); }

  const navs = qsa('.nav-selects');

  navs.forEach(function(nav){
    const source = nav.dataset.source || 'eventos';

    function fetchOptions(field, state, city) {
      const params = new URLSearchParams({ source: source, field: field });
      if (state) params.set('state', state);
      if (city) params.set('city', city);
      return fetch('ajax/filter_options.php?' + params.toString())
        .then(r => r.json())
        .catch(() => []);
    }

    function setList(ul, items){
      ul.innerHTML = '';
      if (!items || items.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'Nenhuma informação localizada';
        ul.appendChild(li);
        return;
      }
      items.forEach(function(it){
        const li = document.createElement('li');
        li.textContent = it;
        li.dataset.value = it;
        ul.appendChild(li);
      });
    }

    function getSelected(field){
      const sel = qs(`.custom-select[data-field="${field}"] .selected-option`, nav);
      return sel ? sel.textContent.trim() : '';
    }

    function applyFiltersToUrl(){
      const estado = getSelected('estado');
      const cidade = getSelected('cidade');
      const estilo = getSelected('estilo');
      const params = new URLSearchParams();
      if (estado && estado.toLowerCase() !== 'estado') params.set('estado', estado);
      if (cidade && cidade.toLowerCase() !== 'cidade') params.set('cidade', cidade);
      if (estilo && estilo.toLowerCase() !== 'estilo musical' && estilo.toLowerCase() !== 'estilo') params.set('estilo', estilo);
      const base = window.location.pathname;
      // reload page with new querystring
      window.location.href = base + (params.toString() ? '?' + params.toString() : '');
    }

    // initial: ensure lists have data-value attributes (if static)
    qsa('.custom-select', nav).forEach(function(cs){
      const ul = qs('.select-list', cs);
      qsa('li', ul).forEach(function(li){ if (!li.dataset.value) li.dataset.value = li.textContent.trim(); });
    });

    async function refreshAll(){
      const state = getSelected('estado');
      const city = getSelected('cidade');

      // fetch cities filtered by state
      const cidades = await fetchOptions('cidade', state || null, null);
      const ulCidade = qs('.custom-select[data-field="cidade"] .select-list', nav);
      if (ulCidade) setList(ulCidade, cidades);

      // fetch estilos filtered by state and city
      const estilos = await fetchOptions('estilo', state || null, city || null);
      const ulEstilo = qs('.custom-select[data-field="estilo"] .select-list', nav);
      if (ulEstilo) setList(ulEstilo, estilos);
    }

    // set initial selected values from querystring if present
    (function populateFromQuery(){
      const qp = new URLSearchParams(window.location.search);
      const e = qp.get('estado');
      const c = qp.get('cidade');
      const s = qp.get('estilo');
      if (e) {
        const span = qs('.custom-select[data-field="estado"] .selected-option', nav);
        if (span) span.textContent = e;
      }
      if (c) {
        const span = qs('.custom-select[data-field="cidade"] .selected-option', nav);
        if (span) span.textContent = c;
      }
      if (s) {
        const span = qs('.custom-select[data-field="estilo"] .selected-option', nav);
        if (span) span.textContent = s;
      }
    })();

    // reset button handler (delegated)
    nav.addEventListener('click', function(e){
      const resetBtn = e.target.closest('.reset-select');
      if (!resetBtn) return;
      e.preventDefault();
      e.stopPropagation();
      // clear all selected-option spans within this nav
      qsa('.custom-select', nav).forEach(function(cs){
        const field = cs.dataset.field;
        const span = qs('.selected-option', cs);
        if (!span) return;
        if (field === 'estado') span.textContent = 'estado';
        else if (field === 'cidade') span.textContent = 'cidade';
        else if (field === 'estilo') span.textContent = 'estilo musical';
      });
      // reload page without filters
      applyFiltersToUrl();
    });

    // attach click handlers via event delegation for selecting options
    nav.addEventListener('click', async function(e){
      const li = e.target.closest('li');
      if (!li) return;
      const cs = e.target.closest('.custom-select');
      if (!cs) return;
      const field = cs.dataset.field;
      // set selected text
      const selectedSpan = qs('.selected-option', cs);
      if (selectedSpan) selectedSpan.textContent = li.textContent.trim();

      // after selection, refresh dependent lists
      await refreshAll();
      // apply filters to page (reload with querystring)
      applyFiltersToUrl();
    });

    // on load, refresh dependent lists once
    refreshAll();
  });
});
