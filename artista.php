<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/AutenticarServico.php";

$id = $_GET['artista'] ?? null;


if (!$id) Utils::redirecionarPara("index.php");

$artistaServico = new ArtistaServicos();
$id = intval($id);
$dadosArtista = $artistaServico->buscarArtistaId($id);
if (empty($dadosArtista)) Utils::redirecionarPara("index.php");
if (!is_int($id)) Utils::redirecionarPara("index.php");

// Buscar integrantes corretamente filtrados pelo artista
$integrantes = $artistaServico->buscarIntegrantes($id);

// Buscar eventos em que o artista participa
$eventoServicos = new EventoServicos();
$eventosArtista = $eventoServicos->buscarEventosUsuario($id) ?: [];



$contador = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dadosArtista['nome'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/artista.css">
    <?php require_once "includes/cabecalho.php" ?>

    <h2 id="titulo_artista"><?= $dadosArtista['nome'] ?></h2>
    <section class="banda">

        <div class="imagem_banda">

            <img src="img/artistas/<?= $dadosArtista['id'] ?>/fotos_artistas/<?= explode(",", $dadosArtista['url_imagem'])[0] ?>" alt="<?= $dadosArtista['nome'] ?>">

        </div>


        <div class="estilo_da_banda">

            <div>
                <p><b>REGIÃO: </b><?= $dadosArtista['cidade'] ?> - <?= $dadosArtista['estado'] ?></p>

                <?php if (!empty($dadosArtista['estilos_musicais']) && trim($dadosArtista['estilos_musicais']) !== ''): ?>
                    <p><b>ESTILOS MUSICAIS: </b><?= implode(", ", array_filter(array_map('trim', explode(",", $dadosArtista['estilos_musicais'])))) ?></p>
                <?php endif; ?>


                <p><b>CONTATO: </b><?= $dadosArtista['contato'] ?></p>

                <div class="redes_sociais">
                    <a href="" target="_blank" class="rede"><i class="fab fa-instagram"></i></a>
                    <a href="" target="_blank" class="rede"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            <div class="botao_whatsapp">
                <a href="<?= $dadosArtista['cidade'] ?>" target="_blank">
                    FALE COM O ARTISTA <i class="fab fa-whatsapp"></i>
                </a>

            </div>
            <p class="cache">CACHÊ: <span class="valor">R$<?= number_format($dadosArtista['cache_artista'], 2, ",", ".") ?> </span></p>





        </div>
    </section>

    <section id="sobre_a_banda">
        <p class="sobre">
            <b>SOBRE A BANDA</b>
        </p>
        <p class="texto_banda">
            <?= $dadosArtista['descricao'] ?>
        </p>
    </section>

    <div id="conteudo-principal">
        <section id="galeria-artista">
            <div id="bloco-texto">
                <h2 id="titulo-secao">
                    <span>GALERIA</span>
                    <span>DO</span>
                    <span id="destaque-titulo">ARTISTA</span>
                </h2>
            </div>



            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <?php foreach (explode(",", $dadosArtista['url_imagem']) as $imagem) {
                    ?>
                        <div class="carousel-item <?= $contador == 0 ? "active" : "" ?>">
                            <div class="container_carrossel">
                                <img src="img/artistas/<?= $dadosArtista['id'] ?>/fotos_artistas/<?= $imagem ?>" alt="Festival do Sol" id="imagem-principal" />

                            </div>
                        </div>

                    <?php
                        $contador++;
                    } ?>


                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

        </section>

        <section id="secao-integrantes">
            <h3 id="subtitulo-integrantes">INTEGRANTES</h3>
            <div id="container-cartoes-integrantes">
                <?php foreach ($integrantes as $integrante) {
                ?>
                    <div class="caixa_integrante">
                        <img src="img/artistas/<?= $dadosArtista['id'] ?>/fotos_integrantes/<?= $integrante['url_imagem'] ?>" alt="<?= $integrante['nome'] ?>" />
                        <div class="texto_integrante_overlay">
                            <h3 class="titulo_integrante"><?= $integrante['nome'] ?></h3>
                            <h4 class="instrumento_integrante"><?= $integrante['instrumento'] ?></h4>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </section>

        <section id="secao-eventos">
            <h3 id="subtitulo-eventos">EVENTOS QUE ESTÃO PARTICIPANDO</h3>
            <div id="container-cartoes-eventos">
                <?php
                if (empty($eventosArtista)) {
                    echo '<p>Nenhum evento encontrado para este artista.</p>';
                } else {
                    foreach ($eventosArtista as $evento) {
                        $estilos = explode(',', $evento['estilos_musicais'] ?? '');
                        $img = htmlspecialchars($evento['url_imagem'] ?? '');
                        $nome = htmlspecialchars($evento['nome']);
                        echo '<a href="evento.php?evento=' . intval($evento['id']) . '" class="cartao-evento">';
                        echo '<img src="img/eventos/' . intval($evento['id']) . '/fotos_eventos/' . $img . '" alt="' . $nome . '" class="imagem-evento">';
                        echo '<div class="informacao-evento">';
                        echo '<p class="nome-evento">' . $nome . '</p>';
                        echo '<p class="detalhes-evento">' . htmlspecialchars($evento['estado']) . ' | ' . Utils::formatarData($evento['dia'], true) . '</p>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </section>
    </div>

    <?php require_once "includes/rodape.php" ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>

</html>