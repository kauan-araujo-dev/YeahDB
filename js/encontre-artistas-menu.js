'use strict';

const selects = document.querySelectorAll(".custom-select");

// TEMPO PARA FECHAR AUTOMATICAMENTE (3 segundos)
const AUTO_CLOSE_TIME = 3000;

selects.forEach(select => {

    let autoCloseTimer;

    const header = select.querySelector(".select-header");
    const list = select.querySelector(".select-list");
    const arrow = select.querySelector(".arrow");
    const selected = select.querySelector(".selected-option");

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

        // REINICIA O TIMER
        clearTimeout(autoCloseTimer);

        // SE ABRIU → INICIA CONTAGEM
        if (list.classList.contains("show")) {
            autoCloseTimer = setTimeout(() => {
                list.classList.remove("show");
                arrow.classList.remove("rotate");
            }, AUTO_CLOSE_TIME);
        }
    });

    // SELEÇÕES
    list.querySelectorAll("li").forEach(item => {
        item.addEventListener("click", () => {
            selected.textContent = item.textContent;

            // FECHA TODOS OS MENUS AO SELECIONAR
            selects.forEach(s => {
                s.querySelector(".select-list").classList.remove("show");
                s.querySelector(".arrow").classList.remove("rotate");
            });

            clearTimeout(autoCloseTimer);
        });
    });
});


