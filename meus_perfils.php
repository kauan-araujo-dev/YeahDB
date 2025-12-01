<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasUsuario($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA</title>
    <link rel="stylesheet" href="css/meus_perfils.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <main class="conteudo-principal">
        <section id="secao_bandas">
             <?php
            if (!empty($artistas)) { ?>
            <h2 class="titulo_secao" id="titulo_artistas">MEUS <span>PERFILS</span></h2>
            <div class="linha_cards">

                <?php foreach ($artistas as $artista) {
                    $artista['estilos_musicais'] = explode(",", $artista['estilos_musicais']); ?>
                    <div class="caixa_banda">
                        <img src="img/artistas/<?= $artista['id'] ?>/fotos_artistas/<?= $artista['url_imagem'] ?>" alt="<?= $artista['nome'] ?>" />
                        <div class="texto_banda_overlay">
                            <h3 class="titulo_banda"><?= $artista['nome'] ?></h3>
                            <div class="botoes_banda">
                                <a href="" class="banda_editar">EDITAR</a>
                                <a href="" class="banda_excluir">EXCLUIR</a>
                            </div>
                        </div>
                </div>

                <?php } ?>

            </div>
            <?php } else { ?>
                    <h2 class="titulo_secao" id="titulo_artistas">Não encontramos nenhum evento</h2>
                <?php } ?>
        </section>
    </main>

    <?php require_once("includes/rodape.php"); ?>
    </body>

</html>