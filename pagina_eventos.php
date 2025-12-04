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
    <!--  <link rel="stylesheet" href="css/style.css">  -->
    <link rel="stylesheet" href="css/pagina_eventos.css">

    <?php
    require_once "includes/cabecalho.php";
    ?>
</head>


<section id="secao_bandas">
    
    <h2 id="titulo_evento">
        ENCONTRE UM <span style="color: #04A777;">EVENTO!</span>
    </h2>

    <nav class="nav-selects">

        <div class="custom-select">
            <div class="select-header">
                <span class="selected-option">estado</span>
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
                <span class="selected-option">cidade</span>
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
                <span class="selected-option">estilo musical</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <li>Item 01</li>
                <li>Item 02</li>
                <li>Item 03</li>
            </ul>
        </div>

    </nav>


    <div class="linha_cards">

        <?php foreach ($eventos as $evento) {
            $evento['estilos_musicais'] = explode(",", $evento['estilos_musicais']); ?>

            <a href="artista.php?artista=<?= $evento['id'] ?>" class="caixa_banda">

                <img src="img/eventos/<?= $evento['id'] ?>/fotos_eventos/<?= $evento['url_imagem'] ?>" alt="<?= $evento['nome'] ?>" />

                <div class="texto_banda_overlay">
                    <h3 class="titulo_banda"><?= $evento['nome'] ?></h3>
                    <h4 class="estilo_musical_banda"><?= implode(", ", $evento['estilos_musicais']) ?></h4>
                    <h4 class="data_evento"><?= Utils::formatarData($evento['dia'], true) ?></h4>

                </div>
            </a>

        <?php } ?>

    </div>

</section>

<script src="js/encontre-artistas-menu.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>