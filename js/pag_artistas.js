        document.addEventListener('DOMContentLoaded', function() {
            // Selects
            document.querySelectorAll('.custom-select').forEach(function(cs) {
                const header = cs.querySelector('.select-header');
                const list = cs.querySelector('.select-list');
                const resetBtn = cs.querySelector('.reset-select');
                header.addEventListener('click', () => list.style.display = (list.style.display === 'block' ? 'none' : 'block'));
                document.addEventListener('click', e => {
                    if (!cs.contains(e.target)) list.style.display = 'none';
                });
                list.querySelectorAll('li').forEach(li => li.addEventListener('click', () => {
                    cs.querySelector('.selected-option').textContent = li.dataset.value;

                    const estadoSelect = document.querySelector('.custom-select[data-field="estado"] .selected-option');
                    const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"] .selected-option');
                    const estiloSelect = document.querySelector('.custom-select[data-field="estilo"] .selected-option');

                    const params = new URLSearchParams();

                    // Se mudou o estado, zera a cidade
                    if (cs.dataset.field === 'estado') {
                        cidadeSelect.textContent = 'CIDADE';
                        params.delete('cidade');
                    }

                    if (estadoSelect.textContent !== 'ESTADO') params.set('estado', estadoSelect.textContent);
                    if (cidadeSelect.textContent !== 'CIDADE') params.set('cidade', cidadeSelect.textContent);
                    if (estiloSelect.textContent !== 'ESTILO MUSICAL') params.set('estilo', estiloSelect.textContent);

                    params.set('page', '1');
                    window.location.href = window.location.pathname + '?' + params.toString();
                }));
                resetBtn.addEventListener('click', e => {
                    e.stopPropagation();
                    const field = cs.dataset.field;
                    if (field === 'estado') {
                        cs.querySelector('.selected-option').textContent = 'ESTADO';
                        const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"]');
                        if (cidadeSelect) cidadeSelect.querySelector('.selected-option').textContent = 'CIDADE';
                    } else if (field === 'cidade') cs.querySelector('.selected-option').textContent = 'CIDADE';
                    else if (field === 'estilo') cs.querySelector('.selected-option').textContent = 'ESTILO MUSICAL';
                    const url = new URL(window.location.href);
                    url.searchParams.delete('estado');
                    url.searchParams.delete('cidade');
                    url.searchParams.delete('estilo');
                    url.searchParams.set('page', '1');
                    window.location.href = url.toString();
                });
            });

            // Mostrar mais AJAX
            document.querySelectorAll('a.mostrar-mais').forEach(function(a) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(a.href);
                    url.searchParams.set('ajax', '1');
                    a.classList.add('loading');
                    a.textContent = 'Carregando...';
                    fetch(url.toString(), {
                            credentials: 'same-origin'
                        })
                        .then(r => r.json())
                        .then(json => {
                            const container = document.querySelector('.linha_cards');
                            container.insertAdjacentHTML('beforeend', json.html);
                            if (json.hasMore) {
                                a.dataset.page = json.nextPage;
                                a.textContent = 'Mostrar mais';
                                a.classList.remove('loading');
                            } else a.remove();
                        })
                        .catch(() => {
                            a.textContent = 'Erro';
                            a.classList.remove('loading');
                        });
                });
            });
        });