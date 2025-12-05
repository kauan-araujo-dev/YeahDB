<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasComLimite(3);

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
    <link rel="stylesheet" href="css/encontre-artistas.css">
    <?php
    require_once "includes/cabecalho.php";
    ?>
</head>
 
<section id="secao_bandas">

   <h2 class="titulo_secao">
        ENCONTRE UM <span style="color: #FB8B24;">ARTISTA!</span>
    </h2>

    <nav class="nav-selects" data-source="artistas">

        <div class="custom-select" data-field="estado">
            <div class="select-header">
                <span class="selected-option">estado</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar estados distintos na tabela artistas
                $stmt = $artistaServico->conexao->prepare("SELECT DISTINCT estado FROM artistas WHERE estado IS NOT NULL AND estado != '' ORDER BY estado ASC");
                $stmt->execute();
                $estados = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($estados)) {
                    foreach ($estados as $est) {
                        echo '<li>' . htmlspecialchars($est) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

        <div class="custom-select" data-field="cidade">
            <div class="select-header">
                <span class="selected-option">cidade</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar cidades distintas na tabela artistas
                $stmt = $artistaServico->conexao->prepare("SELECT DISTINCT cidade FROM artistas WHERE cidade IS NOT NULL AND cidade != '' ORDER BY cidade ASC");
                $stmt->execute();
                $cidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($cidades)) {
                    foreach ($cidades as $cid) {
                        echo '<li>' . htmlspecialchars($cid) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

        <div class="custom-select" data-field="estilo">
            <div class="select-header">
                <span class="selected-option">estilo musical</span>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar estilos musicais associados a artistas
                $sql = "SELECT DISTINCT em.nome
                        FROM artista_estilo ae
                        JOIN estilo_musical em ON em.id = ae.id_estilo
                        ORDER BY em.nome ASC";
                $stmt = $artistaServico->conexao->prepare($sql);
                $stmt->execute();
                $estilos_artista = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($estilos_artista)) {
                    foreach ($estilos_artista as $estNome) {
                        echo '<li>' . htmlspecialchars($estNome) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

    </nav>



    
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

<script src="js/encontre-artistas-menu.js"></script>
<script src="js/select-dependent.js"></script>
<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>