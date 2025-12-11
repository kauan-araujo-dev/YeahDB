document.getElementById("btn-add").addEventListener("click", () => addIntegrante(false));
window.onload = () => addIntegrante(true); // primeiro integrante

function addIntegrante(isFirst) {
    const container = document.getElementById("integrantes-container");

    const wrapper = document.createElement("div");
    wrapper.classList.add("integrante-wrapper");

    if (!isFirst) {
        wrapper.innerHTML = `
            <button type="button" class="btn-remove-x" onclick="removeIntegrante(this)">❌</button>
        `;
    }

    const box = document.createElement("div");
    box.classList.add("integrante-box");

    box.innerHTML = `
        <label class="foto-area">
            <span>ESCOLHA UMA FOTO</span>
            <input type="file" accept="image/*" name="integrante_foto[]" 
                   style="display:none" onchange="previewFoto(this)">
        </label>

        <div class="inputs-area">
            <label>Nome do integrante:</label>
            <input type="text" name="integrante_nome[]">

            <label>Instrumento:</label>
            <input type="text" name="integrante_instrumento[]">
        </div>
    `;

    wrapper.appendChild(box);
    container.appendChild(wrapper);
}

function removeIntegrante(btn) {
    btn.closest(".integrante-wrapper").remove();
}

/* ----------------------------------------------------
   PREVIEW DE FOTO DO INTEGRANTE (corrigido)
---------------------------------------------------- */
function previewFoto(input) {
    const file = input.files[0];
    if (!file) return;

    const box = input.parentElement;

    // Mantém o input ORIGINAL (não recriar, não mover)
    const previewArea = document.createElement("div");
    previewArea.classList.add("preview-area");

    const img = document.createElement("img");
    img.src = URL.createObjectURL(file);

    previewArea.appendChild(img);

    // Limpa somente o texto, mas mantém o input escondido
    box.innerHTML = "";
    box.appendChild(previewArea);
    box.appendChild(input); // mantém input com o arquivo correto
}


/* ----------------------------------------------------
   FOTO DO ARTISTA (múltiplas) — corrigido
---------------------------------------------------- */
const fileInput = document.getElementById('fileInput');
const previewContainer = document.getElementById('previewContainer');
const uploadButton = document.getElementById('uploadButton');

fileInput.addEventListener('change', (event) => {
    previewContainer.innerHTML = '';

    const files = Array.from(event.target.files);

    if (files.length > 5) {
        alert("Máximo 5 imagens.");
        fileInput.value = "";
        return;
    }

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const imageUrl = e.target.result;

            const previewDiv = document.createElement('div');
            previewDiv.classList.add('img-preview');

            const imgElement = document.createElement('img');
            imgElement.src = imageUrl;

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'X';

            deleteButton.onclick = () => {
                removeArtistImage(file);
            };

            previewDiv.appendChild(imgElement);
            previewDiv.appendChild(deleteButton);
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });

    updateUploadButton();
});

/* remove corretamente sem destruir input type="file" */
function removeArtistImage(fileToRemove) {
    const dt = new DataTransfer();

    Array.from(fileInput.files).forEach(file => {
        if (file !== fileToRemove) {
            dt.items.add(file);
        }
    });

    fileInput.files = dt.files;
    updatePreviewArtist();
}


function updatePreviewArtist() {
    previewContainer.innerHTML = '';
    [...fileInput.files].forEach(file => {
        const reader = new FileReader();

        reader.onload = (e) => {
            const previewDiv = document.createElement('div');
            previewDiv.classList.add('img-preview');

            const img = document.createElement('img');
            img.src = e.target.result;

            const btn = document.createElement('button');
            btn.textContent = 'X';
            btn.onclick = () => removeArtistImage(file);

            previewDiv.appendChild(img);
            previewDiv.appendChild(btn);
            previewContainer.appendChild(previewDiv);
        };

        reader.readAsDataURL(file);
    });

    updateUploadButton();
}

function updateUploadButton() {
    const qtd = fileInput.files.length;

    if (qtd >= 5) {
        uploadButton.disabled = true;
        uploadButton.style.color = "#ccc";
        uploadButton.classList.remove("upBtn");
    } else {
        uploadButton.disabled = false;
        uploadButton.classList.add("upBtn");
    }

    uploadButton.innerHTML = `ESCOLHER FOTO (${qtd}/5)`;
}


document.addEventListener("DOMContentLoaded", () => {

    function criarSpanErro(input) {
        let span = input.parentElement.querySelector(".span-erro");
        if (!span) {
            span = document.createElement("span");
            span.classList.add("span-erro");
            input.parentElement.appendChild(span);
        }
        return span;
    }

    function validarCampo(input, validacao, mensagemErro) {
        const span = criarSpanErro(input);

        if (!validacao(input.value.trim())) {
            input.classList.add("erro-input");
            span.textContent = mensagemErro;
            return false;
        }

        input.classList.remove("erro-input");
        span.textContent = "";
        return true;
    }

    // ------------------------------
    // Regras de validação
    // ------------------------------
    const regras = {
        nome: v => v.length >= 3,
        estado: v => /^[A-Za-z]{2}$/.test(v),
        cidade: v => v.length >= 2,
        cache_artista: v => !isNaN(v) && parseFloat(v) > 0,
        whatsapp: v => /^[0-9]{10,13}$/.test(v),
        instagram: v => v.length >= 3,
        contato: v => v.length >= 3,
        descricao: v => v.length >= 10
    };

    // ------------------------------
    // Lista de ids para validação simples
    // ------------------------------
    const camposSimples = [
        "nome", "estado", "cidade", "cache_artista",
        "whatsapp", "instagram", "contato", "descricao"
    ];

    camposSimples.forEach(id => {
        const input = document.getElementById(id);

        criarSpanErro(input);

        input.addEventListener("blur", () => {

            const mensagens = {
                nome: "Nome deve ter pelo menos 3 caracteres.",
                estado: "Use apenas duas letras (SP, RJ...).",
                cidade: "Cidade inválida.",
                cache_artista: "Digite um valor numérico válido.",
                whatsapp: "Whatsapp deve conter apenas números (10 a 13 dígitos).",
                instagram: "Usuário do Instagram inválido.",
                contato: "Preencha este campo.",
                descricao: "A descrição deve ter no mínimo 10 caracteres."
            };

            validarCampo(input, regras[id], mensagens[id]);
        });
    });

    // ----------------------------------
    // Validar estilos musicais (checkboxes)
    // ----------------------------------
    const estilosContainer = document.getElementById("estilos_musicais");

    function validarEstilos() {
        const span = document.querySelector("#estilos_musicais_erro") ||
                     (() => {
                        let s = document.createElement("span");
                        s.id = "estilos_musicais_erro";
                        s.classList.add("span-erro");
                        estilosContainer.parentElement.appendChild(s);
                        return s;
                     })();

        const checkados = estilosContainer.querySelectorAll("input:checked");

        if (checkados.length === 0) {
            span.textContent = "Selecione ao menos um estilo musical.";
            return false;
        }

        span.textContent = "";
        return true;
    }

    estilosContainer.addEventListener("change", validarEstilos);

    // ----------------------------------
    // Validar integrantes
    // ----------------------------------
    function validarIntegrantes() {
    let valido = true;

    // pegar TODOS os inputs atuais criados dinamicamente
    const nomes = document.querySelectorAll('input[name="integrante_nome[]"]');
    const instrumentos = document.querySelectorAll('input[name="integrante_instrumento[]"]');
    const fotos = document.querySelectorAll('input[name="integrante_foto[]"]');

    nomes.forEach(input => {
        const ok = validarCampo(input, v => v.length >= 2, "Nome do integrante inválido.");
        if (!ok) valido = false;
    });

    instrumentos.forEach(input => {
        const ok = validarCampo(input, v => v.length >= 2, "Instrumento inválido.");
        if (!ok) valido = false;
    });

    fotos.forEach(input => {
        const id = input.id || "foto_integrante";
        const ok = input.files.length > 0;
        const span = criarSpanErro(input);

        if (!ok) {
            input.classList.add("erro-input");
            span.textContent = "Envie uma foto para este integrante.";
            valido = false;
        } else {
            input.classList.remove("erro-input");
            span.textContent = "";
        }
    });

    return valido;
}

    // ----------------------------------
    // Validar fotos enviadas
    // ----------------------------------
    function validarFotos() {
    const fileInput = document.getElementById("fileInput");
    const files = fileInput.files;

    let span = document.getElementById("fotos_erro");
    
    if (!span) {
        span = document.createElement("span");
        span.id = "fotos_erro";
        span.classList.add("span-erro");

        // adiciona logo abaixo do botão, onde está visível
        document.getElementById("uploadButton").insertAdjacentElement("afterend", span);
    }

    if (files.length === 0) {
        span.textContent = "Envie ao menos uma foto do artista.";
        return false;
    }

    span.textContent = "";
    return true;
}
    document.getElementById("fileInput")
        .addEventListener("change", validarFotos);

    // ----------------------------------
    // Submit Final
    // ----------------------------------
    const form = document.getElementById("form_artista");

    form.addEventListener("submit", (e) => {

        let valido = true;

        camposSimples.forEach(id => {
            const input = document.getElementById(id);
            const mensagens = {
                nome: "Nome deve ter pelo menos 3 caracteres.",
                estado: "Use apenas duas letras (SP, RJ...).",
                cidade: "Cidade inválida.",
                cache_artista: "Digite um valor válido.",
                whatsapp: "Whatsapp inválido.",
                instagram: "Instagram inválido.",
                contato: "Preencha este campo.",
                descricao: "Descrição insuficiente."
            };

            if (!validarCampo(input, regras[id], mensagens[id])) valido = false;
        });

        if (!validarEstilos()) valido = false;
        if (!validarIntegrantes()) valido = false;
        if (!validarFotos()) valido = false;

        if (!valido) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    });

});