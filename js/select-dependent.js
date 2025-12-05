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
      return fetch('/Yeahdb/ajax/filter_options.php?' + params.toString())
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

    // attach click handlers via event delegation
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
    });

    // on load, refresh dependent lists once
    refreshAll();
  });
});
