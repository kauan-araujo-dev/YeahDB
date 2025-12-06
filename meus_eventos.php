<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$idUsuario = Utils::sanitizar($_SESSION['id'], 'inteiro');
if (!$idUsuario) Utils::redirecionarPara('login.php');

$eventoServico = new EventoServicos();

try {
    $eventos = $eventoServico->buscarEventosUsuario($idUsuario);
} catch (\Throwable $e) {
    $erro = "Erro ao exibir eventos. <br>" . $e->getMessage();
    $eventos = [];
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA</title>

    <!-- Mantido: seu CSS personalizado -->
    <link rel="stylesheet" href="css/meus_eventos.css">

    <?php require_once "includes/cabecalho.php"; ?>
</head>

<body>

<main class="conteudo-principal">

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash_message <?= isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : '' ?>">
            <?= $_SESSION['flash_message'] ?>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_message_type']); ?>
    <?php endif; ?>

    <section id="secao_eventos">

        <?php if (!empty($eventos)): ?>

            <h2 class="titulo_secao" id="titulo_evento">
                MEUS <span>EVENTOS</span>
            </h2>

            <div class="linha_cards">

            <?php 
            foreach ($eventos as $evento):

                // CARREGAR A PRIMEIRA IMAGEM REAL DO EVENTO
                $pdo = Conecta::getConexao();
                $stmt = $pdo->prepare("
                    SELECT url_imagem 
                    FROM foto_evento 
                    WHERE id_evento = :id 
                    ORDER BY id ASC 
                    LIMIT 1
                ");
                $stmt->execute([":id" => $evento['id']]);
                $img = $stmt->fetchColumn();

                // CASO NÃO TENHA FOTO, COLOCA UM PLACEHOLDER
                $caminhoImagem = $img 
                    ? "img/eventos/{$evento['id']}/fotos_eventos/{$img}"
                    : "img/sem-imagem.png";
            ?>

                <div class="caixa_eventos">

                    <!-- Mantido exatamente o HTML que seu CSS usa -->
                    <img src="<?= $caminhoImagem ?>" 
                         alt="<?= htmlspecialchars($evento['nome']) ?>" />

                    <div class="texto_evento_overlay">
                        <h3 class="titulo_evento"><?= htmlspecialchars($evento['nome']) ?></h3>

                        <div class="botoes_evento">
                            <a href="editar_evento.php?id=<?= intval($evento['id']) ?>" class="evento_editar">
                                EDITAR
                            </a>

                            <form method="post" action="excluir_evento.php" style="display:inline">
                                <input type="hidden" name="id" value="<?= intval($evento['id']) ?>">
                                <button type="submit" class="evento_excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este evento?')">
                                    EXCLUIR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            </div>

        <?php else: ?>

            <h2 class="titulo_secao" id="titulo_eventos">
                Não encontramos nenhum evento!
            </h2>

        <?php endif; ?>

    </section>
</main>

<?php require_once "includes/rodape.php"; ?>

</body>
</html>
