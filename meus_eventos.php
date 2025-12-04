<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$eventoServico = new EventoServicos();
$id = Utils::sanitizar($_SESSION['id'], 'inteiro');
if (!$id) Utils::redirecionarPara('login.php');
$eventos = $eventoServico->buscarEventosUsuario($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA</title>
    <link rel="stylesheet" href="css/meus_eventos.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <main class="conteudo-principal">
        <section id="secao_eventos">
            <?php
            if (!empty($eventos)) { ?>
                <h2 class="titulo_secao" id="titulo_evento">MEUS <span>EVENTOS</span></h2>
                <div class="linha_cards">


                    <?php foreach ($eventos as $evento) {
                        $evento['estilos_musicais'] = explode(",", $evento['estilos_musicais']); ?>
                        <div class="caixa_eventos">
                            <img src="img/eventos/<?= $evento['id'] ?>/fotos_eventos/<?= $evento['url_imagem'] ?>" alt="<?= $evento['nome'] ?>" />
                            <div class="texto_evento_overlay">
                                <h3 class="titulo_evento"><?= $evento['nome'] ?></h3>
                                <div class="botoes_evento">
                                    <a href="" class="evento_editar">EDITAR</a>
                                    <a href="" class="evento_excluir">EXCLUIR</a>
                                </div>
                            </div>
                        </div>

                <?php }
                ?>

                </div>

                <?php } else { ?>
                    <h2 class="titulo_secao" id="titulo_eventos">Não encontramos nenhum evento!</h2>
                <?php } ?>
        </section>
    </main>

    <?php require_once("includes/rodape.php"); ?>
    </body>

</html>