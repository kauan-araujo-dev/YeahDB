<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Eventos.php";
require_once "src/Models/IntegranteEvento.php";
require_once "src/Models/FotoEvento.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::iniciarSecao();
AutenticarServico::exigirLogin();

$eventoServico = new EventoServicos();

// Se for POST, processa a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = isset($_POST['id']) ? Utils::sanitizar($_POST['id'], 'inteiro') : null;
    if (!$idPost) {
        Utils::redirecionarPara('meus_eventos.php');
    }

    $usuarioLogado = isset($_SESSION['id']) ? Utils::sanitizar($_SESSION['id'], 'inteiro') : null;
    if (!$usuarioLogado) Utils::redirecionarPara('login.php');

    $evento = $eventoServico->obterEventoPorId($idPost);
    if (!$evento) {
        $_SESSION['flash_message'] = 'Evento não encontrado.';
        $_SESSION['flash_message_type'] = 'error';
        Utils::redirecionarPara('meus_eventos.php');
    }

    if (intval($evento['id_usuario']) !== intval($usuarioLogado)) {
        $_SESSION['flash_message'] = 'Você não tem permissão para excluir este evento.';
        $_SESSION['flash_message_type'] = 'error';
        Utils::redirecionarPara('meus_eventos.php');
    }

    $linhas = $eventoServico->excluirEvento($idPost, $usuarioLogado);
    if ($linhas > 0) {
        $_SESSION['flash_message'] = 'Evento excluído com sucesso.';
        $_SESSION['flash_message_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Falha ao excluir o evento.';
        $_SESSION['flash_message_type'] = 'error';
    }

    Utils::redirecionarPara('meus_eventos.php');
}

// Se for GET, exibimos a página de confirmação
$id = isset($_GET['id']) ? Utils::sanitizar($_GET['id'], 'inteiro') : null;
if (!$id) Utils::redirecionarPara('meus_eventos.php');

$evento = $eventoServico->obterEventoPorId($id);
if (!$evento) Utils::redirecionarPara('meus_eventos.php');

// Somente o dono vê a página de confirmação
$usuarioLogado = isset($_SESSION['id']) ? Utils::sanitizar($_SESSION['id'], 'inteiro') : null;
if (intval($evento['id_usuario']) !== intval($usuarioLogado)) {
    Utils::redirecionarPara('meus_eventos.php');
}

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

    <div class="confirmacao_exclusao">
        <h3>Confirmação de exclusão</h3>
        <p>Você tem certeza que deseja excluir o evento: <strong><?= htmlspecialchars($evento['nome']) ?></strong>?</p>
        <?php if (!empty($evento['id'])): ?>
            <div class="thumb_evento">
                <img src="img/eventos/<?= $evento['id'] ?>/fotos_eventos/<?= isset($evento['url_imagem']) ? $evento['url_imagem'] : '' ?>" alt="<?= htmlspecialchars($evento['nome']) ?>" style="max-width:300px;" />
            </div>
        <?php endif; ?>

        <form method="post" action="excluir_evento.php">
            <input type="hidden" name="id" value="<?= $evento['id'] ?>">
            <button type="submit" class="confirmar">Confirmar exclusão</button>
            <a href="meus_eventos.php" class="cancelar">Cancelar</a>
        </form>
    </div>

</section>

<script src="js/encontre-artistas-menu.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>