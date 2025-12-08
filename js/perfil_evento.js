document.getElementById("btn-add").addEventListener("click", () => addParticipante(false));
window.onload = () => addParticipante(true); // primeiro participante

function addParticipante(isFirst) {
    const container = document.getElementById("participantes-container");

    const wrapper = document.createElement("div");
    wrapper.classList.add("participante-wrapper");

    if (!isFirst) {
        wrapper.innerHTML = `
            <button type="button" class="btn-remove-x" onclick="removeParticipante(this)">X</button>
        `;
    }

    const box = document.createElement("div");
    box.classList.add("participante-box");
box.innerHTML = `
    <label class="foto-area">
        <span>ESCOLHA UMA FOTO</span>
        <input type="file" accept="image/*" class="foto-input" name="participante_foto[]" 
               style="display:none" onchange="previewFoto(this)">
    </label>

    <div class="inputs-area">
        <label>Email do participante:</label>
        <input type="text" class="codigo-input" name="participante_codigo[]" 
               placeholder="Digite o codigo (opcional)" oninput="toggleParticipanteMode(this)">

        <label>Nome do participante:</label>
        <input type="text" class="nome-input" name="participante_nome[]">

        <label>Estilo musical:</label>
        <input type="text" class="estilo-input" name="participantes_estilo_musical[]">
    </div>
`;

    wrapper.appendChild(box);
    container.appendChild(wrapper);
}

function removeParticipante(btn) {
    btn.closest(".participante-wrapper").remove();
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
                removeEventoImage(file);
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
function removeEventoImage(fileToRemove) {
    const dt = new DataTransfer();

    Array.from(fileInput.files).forEach(file => {
        if (file !== fileToRemove) {
            dt.items.add(file);
        }
    });

    fileInput.files = dt.files;
    updatePreviewEvento();
}


function updatePreviewEvento() {
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
            btn.onclick = () => removeEventoImage(file);

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
function toggleParticipanteMode(input) {
    const wrapper = input.closest(".participante-wrapper");

    const fotoInput = wrapper.querySelector(".foto-input");
    const codigoInput = wrapper.querySelector(".codigo-input");
    const nomeInput = wrapper.querySelector(".nome-input");
    const estiloInput = wrapper.querySelector(".estilo-input");
    const fotoArea = wrapper.querySelector(".foto-area");

    const codigo = codigoInput.value.trim();
    const nome = nomeInput.value.trim();
    const estilo = estiloInput.value.trim();
    const hasFoto = fotoInput.files.length > 0;

    /* ==========================================================
       CASO 1: EMAIL PREENCHIDO → limpa & bloqueia os outros
    ========================================================== */
    if (codigo !== "") {

        // Zera foto
        fotoInput.value = "";
        const preview = wrapper.querySelector(".preview-area");
        if (preview) preview.remove();
        fotoArea.innerHTML = `<span>ESCOLHA UMA FOTO</span>`;
        fotoArea.appendChild(fotoInput);

        // Zera textos
        nomeInput.value = "";
        estiloInput.value = "";

        // Desabilitar nome, estilo e foto
        nomeInput.disabled = true;
        estiloInput.disabled = true;
        fotoInput.disabled = true;
        fotoArea.style.pointerEvents = "none";
        fotoArea.style.opacity = "0.4";

        // Habilita codigo
        codigoInput.disabled = false;

        return;
    }

    /* ==========================================================
       CASO 2: NOME / ESTILO / FOTO PREENCHIDOS → limpa codigo
    ========================================================== */
    if (nome !== "" || estilo !== "" || hasFoto) {

        // Zera codigo
        codigoInput.value = "";
        codigoInput.disabled = true;

        // Habilita campos manuais
        nomeInput.disabled = false;
        estiloInput.disabled = false;
        fotoInput.disabled = false;
        fotoArea.style.pointerEvents = "auto";
        fotoArea.style.opacity = "1";

        return;
    }

    /* ==========================================================
       CASO 3: TUDO VAZIO → habilita tudo normalmente
    ========================================================== */
    codigoInput.disabled = false;
    nomeInput.disabled = false;
    estiloInput.disabled = false;
    fotoInput.disabled = false;
    fotoArea.style.pointerEvents = "auto";
    fotoArea.style.opacity = "1";
}
