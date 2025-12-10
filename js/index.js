document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("infiniteScroll");
    const inner = document.getElementById("inner");

    if (!container || !inner) return;

    // Duplicar conteúdo
    inner.innerHTML += inner.innerHTML;

    const imagens = inner.querySelectorAll("img");
    const carregamento = Array.from(imagens).map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(res => (img.onload = img.onerror = res));
    });

    Promise.all(carregamento).then(() => iniciarCarrossel());

    function iniciarCarrossel() {
        const larguraOriginal = inner.scrollWidth / 2;
        let velocidade = 50;       // px por segundo (ajuste aqui)
        let pos = 0;
        let ultimoTempo = null;

        function animar(tempo) {
            if (!ultimoTempo) ultimoTempo = tempo;
            const delta = (tempo - ultimoTempo) / 1000;
            ultimoTempo = tempo;

            pos -= velocidade * delta;

            if (Math.abs(pos) >= larguraOriginal) {
                pos += larguraOriginal; // reset invisível
            }

            inner.style.transform = `translateX(${pos}px)`;

            requestAnimationFrame(animar);
        }

        requestAnimationFrame(animar);
    }
});

