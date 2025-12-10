<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/AutenticarServico.php";

$id = $_GET['evento'] ?? null;


if (!$id) Utils::redirecionarPara("index.php");

$eventoServico = new EventoServicos();
$id = intval($id);
$dadosEvento = $eventoServico->buscarEventosId($id);
Utils::dump($dadosEvento);
if (empty($dadosEvento)) Utils::redirecionarPara("index.php");
if (!is_int($id)) Utils::redirecionarPara("index.php");

$participantes = $eventoServico->buscarParticipantes($id);

$artistas = $eventoServico->buscarArtistaEvento($id);
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
    <?php require_once "includes/cabecalho.php" ?>

    <h2 id="titulo_evento"><?= $dadosEvento['nome'] ?></h2>
    <section class="banda">

        <div class="imagem_banda">

            <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_eventos/<?= explode(",", $dadosEvento['url_imagem'])[0] ?>" alt="<?= $dadosEvento['nome'] ?>">

        </div>


        <div class="estilo_da_banda">

            <div>
                <p><b>REGIÃO: </b><?= $dadosEvento['cidade'] ?> - <?= $dadosEvento['estado'] ?></p>

                <p><b>ESTILOS MUSICAIS: </b><?= implode(", ", explode(",", $dadosEvento['estilos_musicais'])) ?></p>


                <p><b>CONTATO: </b><?= $dadosEvento['contato'] ?></p>

                <div class="redes_sociais">
                    <a href="<?= $dadosEvento['instagram']?>" target="_blank" class="rede"><i class="fab fa-instagram"></i></a>
                    
                </div>
            </div>
            <div class="botao_whatsapp">
                <a href="<?= $dadosEvento['whatsapp'] ?>" target="_blank">
                    FALE COM O ARTISTA <i class="fab fa-whatsapp"></i>
                </a>

            </div>
            <p class="cache">CACHÊ: <span class="valor">R$<?= number_format($dadosEvento['cache_evento'], 2, ",", ".") ?> </span></p>





        </div>
    </section>

    <section id="sobre_a_banda">
        <p class="sobre">
            <b>SOBRE A BANDA</b>
        </p>
        <p class="texto_banda">
            <?= $dadosEvento['descricao'] ?>
        </p>
    </section>

    <div id="conteudo-principal">
        <section id="galeria-evento">
            <div id="bloco-texto">
                <h2 id="titulo-secao">
                    <span>GALERIA</span>
                    <span>DO</span>
                    <span id="destaque-titulo">ARTISTA</span>
                </h2>
            </div>



            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <?php foreach (explode("||", $dadosEvento['imagens']) as $imagem) {
                    ?>
                        <div class="carousel-item <?= $contador == 0 ? "active" : "" ?>">
                            <div class="container_carrossel">
                                <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_eventos/<?= $imagem ?>" alt="Festival do Sol" id="imagem-principal" />

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

        <section id="secao-participantes">
            <h3 id="subtitulo-participantes">INTEGRANTES</h3>
            <div id="container-cartoes-participantes">
                <?php foreach ($participantes as $participante) {
                ?>
                    <div class="caixa_participante">
                        <img src="img/eventos/<?= $dadosEvento['id'] ?>/fotos_participantes/<?= $participante['url_imagem'] ?>" alt="<?= $participante['nome'] ?>" />
                        <div class="texto_participante_overlay">
                            <h3 class="titulo_participante"><?= $participante['nome'] ?></h3>
                            <h4 class="instrumento_participante"><?= $participante['instrumento'] ?></h4>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </section>
        <?php if (!empty($artistas)) { ?>
            <section id="secao-artistas">
                <h3 id="subtitulo-artistas">EVENTOS QUE ESTÃO PARTICIPANDO</h3>

                <div class="linha_cards">
                    <?php foreach ($artistas as $artista) {
                        $artista['estilos_musicais'] = explode(",", $artista['estilos_musicais']); ?>
                        <a href="artista.php?artista=<?= $artista['id'] ?>" class="caixa_artistas">
                            <img src="img/artistas/<?= $artista['id'] ?>/fotos_artistas/<?= $artista['url_imagem'] ?>" alt="Festival do Sol" />
                            <div class="textos_artistas">
                                <div class="texto_superior">
                                    <h3><?= $artista['nome'] ?></h3>
                                    <h4><?= $artista['cidade'] ?> - <?= $artista['estado'] ?></h4>
                                </div>
                                <div class="texto_inferior">
                                    <h4 class="estilos_artistas"><?= implode(", ", $artista['estilos_musicais']) ?></h4>
                                    <h4><?= Utils::formatarData($artista['dia'], true) ?></h4>
                                </div>
                            </div>
                        </a>

                    <?php }
                    ?>

                </div>


    </div>
    </section>
<?php } ?>
</div>

<?php require_once "includes/rodape.php" ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>