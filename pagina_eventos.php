<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasComLimite(4);

$eventoServicos = new EventoServicos();
$contador = 0;

$eventos = $eventoServicos->buscarEventosComLimite(4);

$estilosMusicaisServicos = new EstilosMusicaisServicos();

$estilos_musicais = $estilosMusicaisServicos->buscarEstilosComLimite();



?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahDB</title>
    <link rel="stylesheet" href="css/style.css">    
    <link rel="stylesheet" href="css/pagina_eventos.css">
    <?php
    require_once "includes/cabecalho.php";
    ?>
</head>

<section id="secao_bandas">

    <h2 class="titulo_secao" id="titulo_artistas">
        ENCONTRE UM <span style="color: #04A777;">EVENTO!</span>
    </h2>

    <nav class="nav-selects">

        <div class="custom-select">
            <div class="select-header">
                <span class="selected-option">Selecionar opção</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <li>Opção 1</li>
                <li>Opção 2</li>
                <li>Opção 3</li>
            </ul>
        </div>

        <div class="custom-select">
            <div class="select-header">
                <span class="selected-option">Selecionar opção</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <li>Opção A</li>
                <li>Opção B</li>
                <li>Opção C</li>
            </ul>
        </div>

        <div class="custom-select">
            <div class="select-header">
                <span class="selected-option">Selecionar opção</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <li>Item 01</li>
                <li>Item 02</li>
                <li>Item 03</li>
            </ul>
        </div>

    </nav>



    <section id="secao_bandas">
        <div class="linha_cards">

            <?php foreach ($artistas as $artista) {
                $artista['estilos_musicais'] = explode(",", $artista['estilos_musicais']); ?>
                <a href="artista.php?artista=<?= $artista['id'] ?>" class="caixa_banda">
                    <img src="img/artistas/<?= $artista['id'] ?>/fotos_artistas/<?= $artista['url_imagem'] ?>" alt="<?= $artista['nome'] ?>" />
                    <div class="texto_banda_overlay">
                        <h3 class="titulo_banda"><?= $artista['nome'] ?></h3>
                        <h4 class="estilo_musical_banda"><?= implode(", ", $artista['estilos_musicais']) ?></h4>
                    </div>
                </a>

            <?php } ?>

        </div>

    </section>

</section>

<script src="js/encontre-artistas-menu.js"></script>
<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>