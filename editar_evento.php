<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$eventoServico = new EventoServicos();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    Utils::definirMensagem("Evento inválido!", "erro");
    Utils::redirecionarPara("meus_eventos.php");
}

$idEvento = Utils::sanitizar($_GET['id'], "inteiro");

// Carrega dados do evento
try {
    $evento = $eventoServico->buscarEventoPorId($idEvento);
    if (!$evento) {
        Utils::definirMensagem("Evento não encontrado!", "erro");
        Utils::redirecionarPara("meus_eventos.php");
    }

    // Carrega imagens do evento
    $imagens = $eventoServico->buscarImagensEvento($idEvento);

} catch (\Throwable $e) {
    die("Erro ao carregar evento: " . $e->getMessage());
}


// ----- PROCESSA O FORMULÁRIO -----
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $dadosAtualizados = [
        "nome" => Utils::sanitizar($_POST['nome']),
        "descricao" => Utils::sanitizar($_POST['descricao']),
        "cidade" => Utils::sanitizar($_POST['cidade']),
        "estado" => Utils::sanitizar($_POST['estado']),
        "data_evento" => Utils::sanitizar($_POST['data_evento']),
        "estilos_musicais" => isset($_POST['estilos_musicais'])
            ? implode(",", $_POST['estilos_musicais'])
            : "",
    ];

    try {

        // Atualiza dados básicos
        $eventoServico->atualizarEvento($idEvento, $dadosAtualizados);

        // Upload de novas imagens (caso enviadas)
        if (!empty($_FILES['imagens']['name'][0])) {
            $eventoServico->adicionarImagensEvento($idEvento, $_FILES['imagens']);
        }

        Utils::definirMensagem("Evento atualizado com sucesso!", "sucesso");
        Utils::redirecionarPara("meus_eventos.php");

    } catch (\Throwable $e) {
        $erro = "Erro ao atualizar evento: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Evento</title>
    <link rel="stylesheet" href="css/criar_evento.css">
    <?php require_once "includes/cabecalho.php"; ?>
</head>

<body>

<main class="conteudo-principal">

    <h2 class="titulo_secao">EDITAR <span>EVENTO</span></h2>

    <?php if (isset($erro)): ?>
        <div class="erro">
            <?= $erro ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form_evento">

        <label>Nome do Evento:</label>
        <input type="text" name="nome" value="<?= $evento['nome'] ?>" required>

        <label>Descrição:</label>
        <textarea name="descricao" required><?= $evento['descricao'] ?></textarea>

        <label>Cidade:</label>
        <input type="text" name="cidade" value="<?= $evento['cidade'] ?>" required>

        <label>Estado:</label>
        <input type="text" name="estado" value="<?= $evento['estado'] ?>" required>

        <label>Data do Evento:</label>
        <input type="date" name="data_evento" value="<?= $evento['data_evento'] ?>" required>

        <label>Estilos Musicais:</label>
        <div class="checkbox_estilos">
            <?php
            $estilosMarcados = explode(",", $evento['estilos_musicais']);
            $estilos = ["Rock", "Pop", "Jazz", "Metal", "Samba", "Blues", "Dance", "Rap"];

            foreach ($estilos as $est):
                $checked = in_array($est, $estilosMarcados) ? "checked" : "";
            ?>
                <label><input type="checkbox" name="estilos_musicais[]" value="<?= $est ?>" <?= $checked ?>> <?= $est ?></label>
            <?php endforeach; ?>
        </div>

        <h3>Imagens Atuais</h3>
        <div class="galeria_imagens">
            <?php if (!empty($imagens)): ?>
                <?php foreach ($imagens as $img): ?>
                    <div class="imagem_evento">
                        <img src="img/eventos/<?= $evento['id'] ?>/fotos_eventos/<?= $img['url_imagem'] ?>" alt="">
                        <a class="btn_excluir_foto" href="excluir_imagem_evento.php?id=<?= $img['id'] ?>&evento=<?= $evento['id'] ?>" onclick="return confirm('Excluir esta imagem?')">Excluir</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma imagem cadastrada.</p>
            <?php endif; ?>
        </div>

        <label>Adicionar novas imagens:</label>
        <input type="file" name="imagens[]" multiple>

        <button type="submit" class="btn_salvar">SALVAR ALTERAÇÕES</button>

        <a href="meus_eventos.php" class="btn_cancelar">Cancelar</a>

    </form>
</main>

<?php require_once "includes/rodape.php"; ?>

</body>
</html>
