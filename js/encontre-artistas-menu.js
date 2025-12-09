'use strict';

const selects = document.querySelectorAll(".custom-select");

selects.forEach(select => {
    const header = select.querySelector(".select-header");
    const list = select.querySelector(".select-list");
    const arrow = select.querySelector(".arrow");
    const selected = select.querySelector(".selected-option");
    const listItems = list.querySelectorAll("li");

    // --- 1. ORGANIZAÇÃO EM ORDEM ALFABÉTICA ---
    // Cria um array a partir dos NodeList dos itens da lista
    const itemsArray = Array.from(listItems);

    // Ordena o array com base no conteúdo de texto (em ordem alfabética, ignorando maiúsculas/minúsculas)
    itemsArray.sort((a, b) => {
        const textA = a.textContent.trim().toUpperCase();
        const textB = b.textContent.trim().toUpperCase();
        if (textA < textB) return -1;
        if (textA > textB) return 1;
        return 0;
    });

    // Remove os itens atuais da lista (para evitar duplicatas)
    list.innerHTML = '';

    // Adiciona os itens ordenados de volta ao elemento 'list' (ul/ol)
    itemsArray.forEach(item => {
        list.appendChild(item);
    });
    // --- FIM DA ORGANIZAÇÃO ---

    // GARANTE QUE AS OPÇÕES ORDENADAS TÊM O EVENT LISTENER
    list.querySelectorAll("li").forEach(item => {
        item.addEventListener("click", () => {
            selected.textContent = item.textContent;

            // FECHA TODOS OS MENUS AO SELECIONAR
            selects.forEach(s => {
                s.querySelector(".select-list").classList.remove("show");
                s.querySelector(".arrow").classList.remove("rotate");
            });
        });
    });

    // Evento de abrir/fechar o menu ao clicar no cabeçalho
    header.addEventListener("click", () => {
        // FECHA TODOS OS OUTROS MENUS
        selects.forEach(other => {
            if (other !== select) {
                other.querySelector(".select-list").classList.remove("show");
                other.querySelector(".arrow").classList.remove("rotate");
            }
        });

        // ABRE / FECHA O CLICADO
        list.classList.toggle("show");
        arrow.classList.toggle("rotate");
    });
});