<?php
// editar_evento.php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Eventos.php";
require_once "src/Models/IntegranteEvento.php";
require_once "src/Models/FotoEvento.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/AutenticarServico.php";

session_start();
AutenticarServico::exigirLogin();

$eventoServico = new EventoServicos();
$estilos_musicais = (new EstilosMusicaisServicos())->buscarEstilos();

$erro = null;

// Ler id do evento (GET ?id=)
$idEvento = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idEvento <= 0) {
    Utils::redirecionarPara('minha_conta.php');
}

// carregar evento
$evento = $eventoServico->obterEventoPorId($idEvento);
if (!$evento) {
    Utils::redirecionarPara('minha_conta.php');
}

// garantir propriedade: só o dono pode editar
if (intval($evento['id_usuario']) !== intval($_SESSION['id'])) {
    Utils::redirecionarPara('minha_conta.php');
}

// carregar dados relacionados: imagens, integrantes, estilos atuais
$pdo = Conecta::getConexao();

// imagens
$stmt = $pdo->prepare('SELECT url_imagem FROM foto_evento WHERE id_evento = :id ORDER BY id ASC');
$stmt->execute([':id' => $idEvento]);
$fotos = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

// integrantes
$stmt = $pdo->prepare('SELECT id, nome, estilo_musical, url_imagem FROM integrante_evento WHERE id_evento = :id ORDER BY id ASC');
$stmt->execute([':id' => $idEvento]);
$integrantes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// estilos associados
$stmt = $pdo->prepare('SELECT id_estilo FROM evento_estilo WHERE id_evento = :id');
$stmt->execute([':id' => $idEvento]);
$estilos_assoc = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];


// Quando o formulário é submetido (editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // organizar arquivos (mesma API do criar_evento.php)
    $arquivosEvento = isset($_FILES['fotos_evento']) ? Utils::organizarArquivos($_FILES['fotos_evento']) : [];
    $arquivosIntegrantes = isset($_FILES['participante_foto']) ? Utils::organizarArquivos($_FILES['participante_foto']) : [];

    $estilos_musicais_form = $_POST['estilos'] ?? [];
    $participantes_estilo_musical = $_POST['participantes_estilo_musical'] ?? [];
    $participante_nome = $_POST['participante_nome'] ?? [];

    // flags de validação (mesma lógica do criar)
    $fotos_eventos_validas = true;
    $fotos_participantes_validas = true;
    $participante_estilo_musicals_validas = true;
    $nomes_validos = true;
    $estilos_validos = true;

    if (empty($estilos_musicais_form) || count($estilos_musicais_form) === 0) {
        $estilos_validos = false;
    }

    foreach ($arquivosEvento as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_eventos_validas = false;
            break;
        }
    }

    foreach ($arquivosIntegrantes as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_participantes_validas = false;
            break;
        }
    }

    foreach ($participantes_estilo_musical as $participante_estilo_musical) {
        if (empty(trim($participante_estilo_musical))) {
            $participante_estilo_musicals_validas = false;
            break;
        }
    }

    foreach ($participante_nome as $nome) {
        if (empty(trim($nome))) {
            $nomes_validos = false;
            break;
        }
    }

    // checagem dos campos obrigatórios (os mesmos do criar_evento.php)
    if (
        !empty($_POST['nome']) &&
        !empty($_POST['estado']) &&
        !empty($_POST['cidade']) &&
        !empty($_POST['endereco']) &&
        !empty($_POST['dia']) &&
        !empty($_POST['horario']) &&
        !empty($_POST['instagram']) &&
        !empty($_POST['contato']) &&
        !empty($_POST['descricao']) &&
        !empty($_POST['estilos']) &&
        $fotos_eventos_validas
    ) {
        // sanitizar campos
        $nome       = Utils::sanitizar($_POST['nome']);
        $estado     = Utils::sanitizar($_POST['estado']);
        $cidade     = Utils::sanitizar($_POST['cidade']);
        $endereco   = Utils::sanitizar($_POST['endereco']);
        $dia        = Utils::sanitizar($_POST['dia']);
        $horario    = Utils::sanitizar($_POST['horario']);
        $instagram  = Utils::sanitizar($_POST['instagram']);
        $contato    = Utils::sanitizar($_POST['contato']);
        $link_compra = Utils::sanitizar($_POST['link_compra'] ?? '');
        $descricao  = Utils::sanitizar($_POST['descricao']);
        $estilos = $_POST['estilos'];

        try {
            // Atualizar tabela eventos (diretamente aqui)
            $sqlUpd = "UPDATE eventos SET
                        nome = :nome,
                        descricao = :descricao,
                        estado = :estado,
                        cidade = :cidade,
                        endereco = :endereco,
                        dia = :dia,
                        horario = :horario,
                        instagram = :instagram,
                        contato = :contato,
                        link_compra = :link_compra
                    WHERE id = :id AND id_usuario = :id_usuario";
            $stmtUpd = $pdo->prepare($sqlUpd);
            $stmtUpd->bindValue(':nome', $nome);
            $stmtUpd->bindValue(':descricao', $descricao);
            $stmtUpd->bindValue(':estado', $estado);
            $stmtUpd->bindValue(':cidade', $cidade);
            $stmtUpd->bindValue(':endereco', $endereco);
            $stmtUpd->bindValue(':dia', $dia);
            $stmtUpd->bindValue(':horario', $horario);
            $stmtUpd->bindValue(':instagram', $instagram);
            $stmtUpd->bindValue(':contato', $contato);
            $stmtUpd->bindValue(':link_compra', $link_compra);
            $stmtUpd->bindValue(':id', $idEvento, PDO::PARAM_INT);
            $stmtUpd->bindValue(':id_usuario', $_SESSION['id'], PDO::PARAM_INT);
            $stmtUpd->execute();

            // ======= estilos: apagar existentes e inserir os novos =======
            $stmtDelEst = $pdo->prepare('DELETE FROM evento_estilo WHERE id_evento = :id');
            $stmtDelEst->execute([':id' => $idEvento]);

            foreach ($estilos as $est) {
                $estIdSan = Utils::sanitizar($est, 'inteiro');
                // Reusar método do serviço
                $eventoServico->inserirEstilo($idEvento, $estIdSan);
            }

            // ======= imagens: inserir novas fotos (mantendo as antigas) =======
            // garantir pastas
            $root = dirname(__DIR__, 0); // pasta raiz do projeto (onde está este arquivo)
            $dirEvento = __DIR__ . "/img/eventos/{$idEvento}/fotos_eventos";
            if (!file_exists($dirEvento)) {
                mkdir($dirEvento, 0777, true);
            }

            for ($i = 0; $i < count($arquivosEvento); $i++) {
                $nomeArquivo = $arquivosEvento[$i]['name'];
                // inserir registro no DB
                $fotoEvento = new FotoEvento(null, $nomeArquivo, $idEvento);
                $eventoServico->inserirFotoEvento($fotoEvento);
                // salvar arquivo fisico
                Utils::salvarArquivo($arquivosEvento[$i], "img/eventos/$idEvento/fotos_eventos");
            }

            // ======= integrantes: remover existentes e inserir os novos (se enviados) =======
            $stmtDelInt = $pdo->prepare('DELETE FROM integrante_evento WHERE id_evento = :id');
            $stmtDelInt->execute([':id' => $idEvento]);

            // garantir pasta de participantes
            $dirPart = __DIR__ . "/img/eventos/{$idEvento}/fotos_participantes";
            if (!file_exists($dirPart)) mkdir($dirPart, 0777, true);

            for ($i = 0; $i < count($participante_nome); $i++) {
                $nomePart = Utils::sanitizar($participante_nome[$i]);
                $estiloPart = Utils::sanitizar($participantes_estilo_musical[$i]);

                $fileName = $arquivosIntegrantes[$i]['name'] ?? null;

                $inte = new IntegranteEvento(null, $nomePart, $estiloPart, $fileName, $idEvento);
                $eventoServico->inserirIntegrante($inte);

                if ($fileName && isset($arquivosIntegrantes[$i])) {
                    Utils::salvarArquivo($arquivosIntegrantes[$i], "img/eventos/$idEvento/fotos_participantes");
                }
            }

            // sucesso -> redirecionar para minha_conta.php
            $_SESSION['flash_message'] = 'Evento atualizado com sucesso.';
            $_SESSION['flash_message_type'] = 'success';
            Utils::redirecionarPara('minha_conta.php');
            exit;

        } catch (\Throwable $e) {
            $erro = "Erro ao atualizar evento: " . $e->getMessage();
        }

    } else {
        $erro = "Preencha todos os campos obrigatórios corretamente.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento</title>
    <!-- Mantive os mesmos CSS do criar_evento.php -->
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/criar_evento.css">
    <?php require_once "includes/cabecalho.php"; ?>
</head>

<body>
<section id="sessao-cadastro">
    <h2>EDITE SEU <span>Evento</span></h2>

    <?php if ($erro) : ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form id="form_evento" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= intval($idEvento) ?>">

        <div id="container-inputs">

            <div>
                <label for="nome">Nome do evento:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? $evento['nome']) ?>" />
            </div>

            <div>
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" maxlength="2" value="<?= htmlspecialchars($_POST['estado'] ?? $evento['estado']) ?>" />
            </div>

            <div>
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade" value="<?= htmlspecialchars($_POST['cidade'] ?? $evento['cidade']) ?>" />
            </div>

            <div>
                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($_POST['endereco'] ?? $evento['endereco']) ?>" />
            </div>

            <div>
                <label for="dia">Dia do evento:</label>
                <input type="date" id="dia" name="dia" value="<?= htmlspecialchars($_POST['dia'] ?? $evento['dia']) ?>" />
            </div>

            <div>
                <label for="horario">Horário:</label>
                <input type="time" id="horario" name="horario" value="<?= htmlspecialchars($_POST['horario'] ?? $evento['horario']) ?>" />
            </div>

            <div>
                <label for="instagram">Instagram:</label>
                <input type="text" id="instagram" name="instagram" value="<?= htmlspecialchars($_POST['instagram'] ?? $evento['instagram']) ?>" />
            </div>

            <div>
                <label for="contato">Contato:</label>
                <input type="text" id="contato" name="contato" value="<?= htmlspecialchars($_POST['contato'] ?? $evento['contato']) ?>" />
            </div>

            <div>
                <label for="link_compra">Link de compra (opcional):</label>
                <input type="text" id="link_compra" name="link_compra" value="<?= htmlspecialchars($_POST['link_compra'] ?? $evento['link_compra']) ?>" />
            </div>

            <div>
                <label>Estilos musicais:</label>
                <div class="checkbox-group" id="estilos_musicais">
                    <?php foreach ($estilos_musicais as $estilo_musical) {
                        $checked = in_array($estilo_musical['id'], $estilos_assoc) ? 'checked' : '';
                        // se o formulário foi submetido com erros, respeitar o POST
                        if (!empty($_POST['estilos'])) {
                            $checked = in_array($estilo_musical['id'], $_POST['estilos']) ? 'checked' : '';
                        }
                        ?>
                        <label>
                            <input type="checkbox" name="estilos[]" value="<?= $estilo_musical['id'] ?>" <?= $checked ?>>
                            <?= htmlspecialchars($estilo_musical['nome']) ?>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao"><?= htmlspecialchars($_POST['descricao'] ?? $evento['descricao']) ?></textarea>
            </div>

            <!-- participantes: replicando a mesma estrutura do criar_evento.php -->
            <div id="container-participantes">
                <label>PARTICIPANTES: </label>
                <div id="participantes-container">
                    <?php
                    // Preencher participantes existentes
                    if (!empty($integrantes)) {
                        foreach ($integrantes as $iIndex => $i) {
                            $iname = htmlspecialchars($i['nome']);
                            $iest = htmlspecialchars($i['estilo_musical']);
                            ?>
                            <div class="participante-item">
                                <input type="text" name="participante_nome[]" value="<?= $iname ?>" placeholder="Nome do integrante" />
                                <input type="text" name="participantes_estilo_musical[]" value="<?= $iest ?>" placeholder="Instrumento / Estilo" />
                                <label>Foto (opcional): <input type="file" name="participante_foto[]" accept="image/*" /></label>
                                <?php if (!empty($i['url_imagem'])): ?>
                                    <div class="preview-existente">
                                        <img src="img/eventos/<?= intval($idEvento) ?>/fotos_participantes/<?= htmlspecialchars($i['url_imagem']) ?>" alt="<?= $iname ?>" style="width:90px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <button type="button" id="btn-add">ADICIONAR INTEGRANTE +</button>
            </div>

            <!-- fotos do evento: input único múltiplo -->
            <div style="margin-top:12px;">
                <label>FOTOS DO EVENTO (envie novas para adicionar):</label><br>
                <button id="uploadButton" onclick="fileInput.click(); return false;" type="button" class="upBtn">Adicionar foto</button>
                <input type="file" id="fileInput" accept="image/*" multiple style="display: none;" name="fotos_evento[]">
                <div id="previewContainer">
                    <?php if (!empty($fotos)) {
                        foreach ($fotos as $k => $f) {
                            echo '<img src="img/eventos/' . intval($idEvento) . '/fotos_eventos/' . htmlspecialchars($f) . '" style="width:120px;height:90px;object-fit:cover;margin:6px;border-radius:6px;border:1px solid #ddd;" />';
                        }
                    } ?>
                </div>
            </div>

        </div>

        <input type="submit" value="Salvar alterações">
    </form>

</section>

<script src="js/perfil_evento.js"></script>
<?php require_once "includes/rodape.php"; ?>
</body>
</html>
