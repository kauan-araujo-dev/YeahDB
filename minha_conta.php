<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Usuario.php";
require_once "src/Services/UsuarioServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA</title>
    <link rel="stylesheet" href="css/minha_conta.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <main class="conteudo-principal">
        <section class="secao-conta">
            <h2 class="titulo-conta">MINHA <span class="destaque-azul">CONTA</span></h2>

            <div class="container-acoes">
                <article class="cartao-acao">
                    <a href="meus_perfis.php" class="cabecalho-cartao cabecalho-perfil">
                        <h3>MEUS PERFILS</h3>
                    </a>
                    <a href="criar_perfil.php" class="conteudo-cartao">
                        <p class="texto-criar">CRIAR</p>
                        <p class="texto-criar">UM</p>
                        <p class="texto-destaque texto-perfil">PERFIL</p>
                    </a>
                </article>

                <article class="cartao-acao">
                    <a href="meus_eventos.php" class="cabecalho-cartao cabecalho-eventos">
                        <h3>MEUS EVENTOS</h3>
                    </a>
                    <a href="criar_evento.php" class="conteudo-cartao">
                        <p class="texto-criar">CRIAR<br>UM</p>
                        <p class="texto-destaque texto-evento">EVENTO</p>
                    </a>
                </article>
            </div>
        </section>
    </main>

    <?php require_once("includes/rodape.php"); ?>
    </body>

</html>