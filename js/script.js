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
