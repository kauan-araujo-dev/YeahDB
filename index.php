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
    <section id="secao_bandas">
      <h2 class="titulo_secao" id="titulo_artistas">ARTISTAS EM <span>DESTAQUE</span></h2>
      <div class="linha_cards">
        <a href="artista.php?" class="caixa_banda">
          <img src="img/banda_rock.jpg" alt="Metal Boss" />
          <div class="texto_banda_overlay">
            <h3 class="titulo_banda">Metal Boss</h3>
            <h4 class="estilo_musical">Classic Rock</h4>
          </div>
        </a>

        <a href="artista.php?" class="caixa_banda">
          <img src="img/dupla_sertaneja.jpg" alt="Toguro e Macacheira" />
          <div class="texto_banda_overlay">
            <h3 class="titulo_banda">Toguro e Macacheira</h3>
            <h4 class="estilo_musical">Sertanejo</h4>
          </div>
        </a>

        <a href="artista.php?" class="caixa_banda">
          <img src="img/hiphop.jpg" alt="HipZica" />
          <div class="texto_banda_overlay">
            <h3 class="titulo_banda">HipZica</h3>
            <h4 class="estilo_musical">Hip Hop</h4>
          </div>
        </a>
      </div>
    </section>

    <section id="secao_eventos">
      <h2 class="titulo_secao" id="titulo_eventos">ONDE A <span>MÚSICA</span> VAI ESTAR</h2>
      <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <a href="#" class="container_carrossel">
              <img src="img/um festival sertanej.png" alt="Festival do Sol" />
              <div class="textos_carrossel">
                <div class="texto_superior">
                  <h3>Festival do Sol</h3>
                  <h4>São Paulo - SP</h4>
                </div>
                <div class="texto_inferior">
                  <h4>Sertanejo</h4>
                  <h4>09/11/25</h4>
                </div>
              </div>
            </a>
          </div>

          <div class="carousel-item">
            <a href="#" class="container_carrossel">
              <img src="img/um festival sertanej.png" alt="Festival do Sol" />
              <div class="textos_carrossel">
                <div class="texto_superior">
                  <h3>Festival do Sol</h3>
                  <h4>São Paulo - SP</h4>
                </div>
                <div class="texto_inferior">
                  <h4>Sertanejo</h4>
                  <h4>09/11/25</h4>
                </div>
              </div>
            </a>
          </div>

          <div class="carousel-item">
            <a href="#" class="container_carrossel">
              <img src="img/um festival sertanej.png" alt="Festival do Sol" />
              <div class="textos_carrossel">
                <div class="texto_superior">
                  <h3>Festival do Sol</h3>
                  <h4>São Paulo - SP</h4>
                </div>
                <div class="texto_inferior">
                  <h4>Sertanejo</h4>
                  <h4>09/11/25</h4>
                </div>
              </div>
            </a>
          </div>
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

    <section class="secao_criar_conta">
      <div class="textos_criar_conta">
        <p><b>QUER DIVULGAR O SEU</b></p>
        <p class="texto_talento"><b>TALENTO?</b></p>
        <p><b>CRIE SUA CONTA!</b></p>
      </div>

      <a href="#">
        <button class="botao_criar_conta">
          <b><i>CRIAR CONTA</i></b>
        </button>
      </a>
    </section>
</main>

  <?php require_once "includes/rodape.php" ?>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>

</html>