document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("senha");
    const toggle = document.getElementById("toggleSenha");

    toggle.addEventListener("click", () => {
        const tipo = input.type === "password" ? "text" : "password";
        input.type = tipo;

        // Troca do ícone: olho aberto ↔ olho fechado
        if (tipo === "password") {
            toggle.classList.remove("fa-eye-slash");
            toggle.classList.add("fa-eye");
        } else {
            toggle.classList.remove("fa-eye");
            toggle.classList.add("fa-eye-slash");
        }
    });
});