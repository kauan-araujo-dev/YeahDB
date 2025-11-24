<?php 
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Usuario.php";
require_once "src/Services/UsuarioServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahBD - Sua Conta</title>
    <link rel="stylesheet" href="css/meu-perfil.css">
    <?php require_once "includes/cabecalho.php"; ?>
      <main class="conteudo-principal">
        <section class="secao-conta">
            <h2 class="titulo-conta">SUA <span class="destaque-azul">CONTA</span></h2>
            
            <div class="container-acoes">
                <article class="cartao-acao">
                    <header class="cabecalho-cartao cabecalho-perfil">
                        <h3>ALTERAR INFORMAÇÕES</h3>
                    </header>
                    <div class="conteudo-cartao">
                        <p class="texto-criar">CRIAR<br>SEU</p>
                        <p class="texto-destaque texto-perfil">PERFIL</p>
                    </div>
                </article>

                <article class="cartao-acao">
                    <header class="cabecalho-cartao cabecalho-eventos">
                        <h3>MEUS EVENTOS</h3>
                    </header>
                    <div class="conteudo-cartao">
                        <p class="texto-criar">CRIAR<br>UM</p>
                        <p class="texto-destaque texto-evento">EVENTO</p>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <?php require_once("includes/rodape.php"); ?>
</body>
</html>