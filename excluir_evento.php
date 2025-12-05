<?php
    require_once "src/Database/Conecta.php";
    require_once "src/Helpers/Utils.php";
    require_once "src/Models/Eventos.php";
    require_once "src/Models/IntegranteEvento.php";
    require_once "src/Models/FotoEvento.php";
    require_once "src/Services/EventoServicos.php";
    require_once "src/Services/EstilosMusicaisServicos.php";
    require_once "src/Services/AutenticarServico.php";
    session_start();
    $eventoServico = new EventoServicos();

    $id = Utils::sanitizar($_GET['id'], 'inteiro');

    if(!$id) Utils::redirecionarPara('meus_eventos.php');


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



    <!-- <div class="linha_cards">

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

        <?php } ?> -->

    </div>

</section>

<script src="js/encontre-artistas-menu.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>