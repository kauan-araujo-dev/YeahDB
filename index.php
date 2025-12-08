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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>YeahDB</title>
  <link rel="stylesheet" href="css/style.css">
  <?php
  require_once "includes/cabecalho.php";
  ?>
  <main id="main-index">

    <section id="secao_banner">

      <div id="carouselExampleFade" class="carousel slide carousel-fade">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="img/banner-de-fundo.jpg" class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img src="img/banner-de-fundo_02.jpg" class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img src="img/banner-de-fundo_03.jpg" class="d-block w-100" alt="...">
          </div>
          <div class="texto">
            <h3>DESCUBRA BANDAS E ARTISTAS INCRÍVEIS PERTO DE VOCÊ</h3>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

    </section>

    <section id="secao_bandas">
      <h2 class="titulo_secao" id="titulo_artistas">ARTISTAS EM <span>DESTAQUE</span></h2>
      <div class="linha_cards">

        <?php foreach ($artistas as $artista) {
          $artista['estilos_musicais'] = explode(",", $artista['estilos_musicais']);
          $imgs = isset($artista['imagens']) ? array_filter(explode('||', $artista['imagens'])) : [];
          $mainImg = $imgs[0] ?? '';
        ?>
          <a href="artista.php?artista=<?= $artista['id'] ?>" class="caixa_banda">
            <img src="img/artistas/<?= $artista['id'] ?>/fotos_artistas/<?= htmlspecialchars($mainImg) ?>" alt="<?= htmlspecialchars($artista['nome']) ?>" />
            <div class="texto_banda_overlay">
              <h3 class="titulo_banda"><?= htmlspecialchars($artista['nome']) ?></h3>
              <h4 class="estilo_musical_banda"><?= htmlspecialchars(implode(", ", $artista['estilos_musicais'])) ?></h4>
            </div>
            <?php if (!empty($imgs)) {
              echo '<div class="thumb-strip">';
              foreach ($imgs as $t) {
                echo '<img class="card-thumb" src="img/artistas/' . $artista['id'] . '/fotos_artistas/' . htmlspecialchars($t) . '" alt="' . htmlspecialchars($artista['nome']) . '" />';
              }
              echo '</div>';
            } ?>
          </a>

        <?php } ?>

      </div>
    </section>

    <section id="secao_eventos">
      <h2 class="titulo_secao" id="titulo_eventos">ONDE A <span>MÚSICA</span> VAI ESTAR</h2>
      <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

          <?php foreach ($eventos as $evento) {
            $evento['estilos_musicais'] = array_map('trim', explode(",", $evento['estilos_musicais']));
            $imgs_e = isset($evento['imagens']) ? array_filter(explode('||', $evento['imagens'])) : [];
            $mainE = $imgs_e[0] ?? '';
          ?>
            <div class="carousel-item <?= $contador == 0 ? "active" : "" ?>">
              <a href="evento.php?evento=<?= $evento['id'] ?>" class="container_carrossel">
                <img src="img/eventos/<?= $evento['id'] ?>/fotos_eventos/<?= htmlspecialchars($mainE) ?>" alt="<?= htmlspecialchars($evento['nome']) ?>" />
                <div class="textos_carrossel">
                  <div class="texto_superior">
                    <h3><?= htmlspecialchars($evento['nome']) ?></h3>
                    <h4><?= htmlspecialchars($evento['cidade']) ?> - <?= htmlspecialchars($evento['estado']) ?></h4>
                  </div>
                  <div class="texto_inferior">
                    <h4><?= htmlspecialchars(implode(", ", $evento['estilos_musicais'])) ?></h4>
                    <h4><?= Utils::formatarData($evento['dia'], true) ?></h4>
                  </div>
                </div>
              </a>
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

    <section id="secao_estilos_musicais">
      <h2 class="titulo_secao" id="titulo_estilos_musicais">ENCONTRE O SEU <span>ESTILO</span></h2>


      <div class="cards_estilos_musicais">
        <?php foreach ($estilos_musicais as $estilo_musical) { ?>
          <div class="card_estilos_musicais">
            <img src="img/estilos_musicais/<?= $estilo_musical['imagem'] ?>" alt="band de <?= $estilo_musical['nome'] ?>" class="estilo_musical">
            <h3 class="texto-estilo"><?= $estilo_musical['nome'] ?></h3>
          </div>
        <?php } ?>
      </div>
    </section>


    <section class="secao_criar_conta">
      <div class="textos_criar_conta">
        <p><b>QUER DIVULGAR O SEU</b></p>
        <p class="texto_talento"><b>TALENTO?</b></p>
        <p><b>CRIE SUA CONTA!</b></p>
      </div>

      <a href="cadastrar.php">
        <button class="botao_criar_conta">
          <b><i>CRIAR CONTA</i></b>
        </button>
      </a>
    </section>

  </main>

  <?php require_once "includes/rodape.php" ?>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script src="js/gallery.js"></script>
  </body>

</html>