<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";
$id = $_GET['evento'] ?? null;


$eventoServico = new EventoServicos();
$id = intval($id);
$dadosEvento = $eventoServico->buscarEventosId($id);

if (empty($dadosEvento)) Utils::redirecionarPara("index.php");
if (!is_int($id)) Utils::redirecionarPara("index.php");

$artistas = $eventoServico->buscarArtistaEvento($id);
$integrantes = $eventoServico->buscarIntegrantes($id);
$contador = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dadosEvento['nome'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/evento.css">
</head>

<body>

<?php require_once "includes/cabecalho.php" ?>

<h2 id="titulo_evento"><?= $dadosEvento['nome'] ?></h2>

<section class="evento_topo">

    <div class="imagem_evento_principal">
        <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_eventos/<?= explode(",", $dadosEvento['url_imagem'])[0] ?>" 
             alt="<?= $dadosEvento['nome'] ?>">
    </div>

    <div class="dados_evento">

        <div>
            <p><b>CIDADE: </b><?= $dadosEvento['cidade'] ?> - <?= $dadosEvento['estado'] ?></p>
            <p><b>LOCAL: </b><?= $dadosEvento['endereco'] ?></p>
            <p><b>DATA: </b><?= Utils::formatarData($dadosEvento['dia'], true) ?></p>
            <p><b>HORÁRIO: </b><?= $dadosEvento['horario'] ?></p>
            <p><b>CONTATO: </b><?= $dadosEvento['contato'] ?></p>
            <p><b>ESTILOS: </b><?= implode(", ", explode(",", $dadosEvento['estilos_musicais'])) ?></p>
        </div>

        <div class="botao_ingresso">
            <a href="<?= $dadosEvento['link_compra'] ?>" target="_blank">
                COMPRAR INGRESSO <i class="fas fa-ticket-alt"></i>
            </a>
        </div>
    </div>

</section>

<section id="sobre_o_evento">
    <p class="titulo_sobre"><b>SOBRE O EVENTO</b></p>
    <p class="texto_evento">
        <?= $dadosEvento['descricao'] ?>
    </p>
</section>

<!-- GALERIA -->
<div id="conteudo-principal">
    <section id="galeria-evento">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                <?php foreach (explode(",", $dadosEvento['url_imagem']) as $imagem) { ?>
                    <div class="carousel-item <?= $contador == 0 ? "active" : "" ?>">
                        <div class="container_carrossel">
                            <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_eventos/<?= $imagem ?>"
                                id="imagem-principal" />
                        </div>
                    </div>
                <?php $contador++; } ?>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

    </section>

    <!-- ARTISTAS DO EVENTO -->
    <section id="secao-artistas">
        <h3 id="subtitulo-artistas">ARTISTAS PRESENTES</h3>

        <div id="container-cartoes-artistas">

            <?php if(!empty($artistas)){
                foreach ($artistas as $artista) { ?>
                <a href="artista.php?artista=<?= $artista['id'] ?>" class="cartao-artista">
                    <img src="img/artistas/<?= $artista['id'] ?>/fotos_artistas/<?= explode(",", $artista['url_imagem'])[0] ?>">
                    <div class="overlay-artista">
                        <h3><?= $artista['nome'] ?></h3>
                        <h4><?= implode(", ", explode(",", $artista['estilos_musicais'])) ?></h4>
                    </div>
                </a>
            <?php } }?>

            <?php if(!empty($integrantes)){
                foreach ($integrantes as $artista) { ?>
                <a href="artista.php?artista=<?= $artista['id'] ?>" class="cartao-artista">
                    <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_participantes/<?=$artista['url_imagem'] ?>">
                    <div class="overlay-artista">
                        <h3><?= $artista['nome'] ?></h3>
                        <h4><?= implode(", ", explode(",", $artista['estilo_musical'])) ?></h4>
                    </div>
                </a>
            <?php } }?>

        </div>

    </section>
</div>

<?php require_once "includes/rodape.php" ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>