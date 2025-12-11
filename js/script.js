"use strict";


const btnContinuar = document.querySelector("#btn-continuar");
const btnCadastrar = document.querySelector("#btn-cadastrar");
const voltar = document.querySelector("#btn-voltar");
const form1 = document.querySelector("#form1");
const form2 = document.querySelector("#form2")

if(btnContinuar){
    btnContinuar.addEventListener("click", (e)=>{
    form1.submit();
})
}

if(btnCadastrar){
    btnCadastrar.addEventListener("click", (e)=>{
    form2.submit();
})
}

if(voltar){
    voltar.addEventListener("click", (e)=>{
        const inputHidden = document.createElement("input");
        inputHidden.type = "hidden";
        inputHidden.name = "voltar";
        inputHidden.value = "voltar";
        form2.appendChild(inputHidden);
        form2.submit();
    })
}
document.addEventListener("DOMContentLoaded", () => {

    // --------------------------
    // Função para validar campos
    // --------------------------
    function validarCampo(input, validacao, mensagemErro) {
        const spanErro = input.parentElement.querySelector(".span-erro");

        if (!validacao(input.value.trim())) {
            input.classList.add("erro-input");
            spanErro.textContent = mensagemErro;
            return false;
        }

        input.classList.remove("erro-input");
        spanErro.textContent = "";
        return true;
    }

    // --------------------------
    // Regras de validação
    // --------------------------
    const regras = {
        nome: v => v.length >= 3,
        data_nascimento: v => v !== "",
        cep: v => /^[0-9]{8}$/.test(v),
        estado: v => /^[A-Za-z]{2}$/.test(v),
        cidade: v => v.length >= 2,
        rua: v => v.length >= 2,
        numero: v => /^[0-9]+$/.test(v),

        email: v => /\S+@\S+\.\S+/.test(v),
        confirmarEmail: (v, email) => v === email,

        senha: v => v.length >= 6,
        confirmarSenha: (v, senha) => v === senha
    };

    // --------------------------
    // Selecionar todos inputs
    // --------------------------
    const form1 = document.getElementById("form1");
    const form2 = document.getElementById("form2");

    // Validação do CEP com ViaCEP ao sair (blur)
    if (form1) {
        document.getElementById("cep").addEventListener("blur", function () {
            if (!regras.cep(this.value)) {
                validarCampo(this, regras.cep, "CEP deve conter 8 números.");
                return;
            }

            // Buscar endereço
            fetch(`https://viacep.com.br/ws/${this.value}/json/`)
                .then(res => res.json())
                .then(data => {
                    if (data.erro) {
                        validarCampo(this, () => false, "CEP não encontrado.");
                        return;
                    }

                    // Preencher campos
                    document.getElementById("rua").value = data.logradouro;
                    document.getElementById("cidade").value = data.localidade;
                    document.getElementById("estado").value = data.uf;

                    validarCampo(this, regras.cep, "");
                });
        });

        // Validação individual ao sair de cada input
        const campos1 = ["nome", "data_nascimento", "cep", "estado", "cidade", "rua", "numero"];
        campos1.forEach(id => {
            const input = document.getElementById(id);
            const span = document.createElement("span");
            span.classList.add("span-erro");
            input.parentElement.appendChild(span);

            input.addEventListener("blur", () => {
                let validacao = regras[id];
                let msg = "Campo inválido.";

                if (id === "nome") msg = "Nome deve ter ao menos 3 caracteres.";
                if (id === "estado") msg = "Use apenas 2 letras (ex: SP).";
                if (id === "numero") msg = "Digite apenas números.";

                validarCampo(input, validacao, msg);
            });
        });

        // Validação final do form1 antes de enviar
        form1.addEventListener("submit", (e) => {
            let valido = true;

            campos1.forEach(id => {
                const input = document.getElementById(id);
                const span = input.parentElement.querySelector(".span-erro");

                if (!validarCampo(input, regras[id], span.textContent || "Campo inválido.")) {
                    valido = false;
                }
            });

            if (!valido) e.preventDefault();
        });
    }

    // --------------------------
    // Formulário 2 (email e senha)
    // --------------------------
    if (form2) {
        const ids2 = ["email", "confirmar-email", "senha", "confirmar-senha"];

        ids2.forEach(id => {
            const input = document.getElementById(id);
            const span = document.createElement("span");
            span.classList.add("span-erro");
            input.parentElement.appendChild(span);

            input.addEventListener("blur", () => {
                let msg = "Campo inválido.";

                if (id === "email") msg = "Digite um e-mail válido.";
                if (id === "confirmar-email") msg = "Os e-mails não coincidem.";
                if (id === "senha") msg = "A senha deve ter pelo menos 6 caracteres.";
                if (id === "confirmar-senha") msg = "As senhas não coincidem.";

                let validacao;
                if (id === "confirmar-email") {
                    validacao = v => regras.confirmarEmail(v, document.getElementById("email").value);
                } else if (id === "confirmar-senha") {
                    validacao = v => regras.confirmarSenha(v, document.getElementById("senha").value);
                } else {
                    validacao = regras[id.replace("-", "")];
                }

                validarCampo(input, validacao, msg);
            });
        });

        // Botão cadastrar
        document.getElementById("btn-cadastrar").addEventListener("click", () => {
            form2.submit();
        });

        // Validação final do form2 antes de enviar
        form2.addEventListener("submit", (e) => {
            let valido = true;

            ids2.forEach(id => {
                const input = document.getElementById(id);
                const span = input.parentElement.querySelector(".span-erro");

                let validacao;
                if (id === "confirmar-email") {
                    validacao = v => regras.confirmarEmail(v, document.getElementById("email").value);
                } else if (id === "confirmar-senha") {
                    validacao = v => regras.confirmarSenha(v, document.getElementById("senha").value);
                } else {
                    validacao = regras[id.replace("-", "")];
                }

                if (!validarCampo(input, validacao, span.textContent || "Campo inválido.")) {
                    valido = false;
                }
            });

            if (!valido) e.preventDefault();
        });
    }

});
