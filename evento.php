<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EventoServicos.php";

$id = isset($_GET['evento']) ? intval($_GET['evento']) : 0;
if ($id <= 0) {
    header('Location: pagina_eventos.php');
    exit;
}

$serv = new EventoServicos();
$evento = $serv->obterEventoPorId($id);
if (!$evento) {
    header('Location: pagina_eventos.php');
    exit;
}

$pdo = Conecta::getConexao();

// ===========================
// FOTOS DO EVENTO
// ===========================
$stmt = $pdo->prepare('
    SELECT url_imagem 
    FROM foto_evento 
    WHERE id_evento = :id 
    ORDER BY id ASC
');
$stmt->execute([':id' => $id]);
$fotos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ===========================
// ESTILOS
// ===========================
$stmt = $pdo->prepare('
    SELECT em.nome 
    FROM evento_estilo ee 
    JOIN estilo_musical em ON em.id = ee.id_estilo 
    WHERE ee.id_evento = :id 
    ORDER BY em.nome ASC
');
$stmt->execute([':id' => $id]);
$estilos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ===========================
// PARTICIPANTES
// ===========================
$stmt = $pdo->prepare('
    SELECT a.id, a.nome 
    FROM artista_evento ae 
    JOIN artistas a ON a.id = ae.id_artista 
    WHERE ae.id_evento = :id
');
$stmt->execute([':id' => $id]);
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($evento['nome']) ?> — YeahDB</title>

    <link rel="stylesheet" href="css/pagina_eventos.css">

    <style>
        .thumb-event {
            transition: transform .12s, box-shadow .12s, outline .12s;
            cursor: pointer;
        }
        .thumb-event:hover {
            transform: scale(1.05);
        }
        .thumb-event.active-thumb {
            outline: 3px solid #04A777;
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }
    </style>

    <link rel="stylesheet" href="css/evento.css">
    <?php require_once "includes/cabecalho.php"; ?>
    
<main style="padding:20px; max-width:1100px; margin:0 auto;">
    <a href="pagina_eventos.php">&larr; Voltar</a>
    <h3 style="margin:12px 0;"><?= htmlspecialchars($evento['nome']) ?></h3>

    <div style="display:flex; gap:20px; flex-wrap:wrap;">

        <!-- ======================= -->
        <!--   IMAGEM PRINCIPAL     -->
        <!-- ======================= -->
        <div style="flex:1 1 480px; min-width:280px;">

            <?php if (!empty($fotos)) : ?>
                <img 
                    id="main-event-image" 
                    src="img/eventos/<?= intval($id) ?>/fotos_eventos/<?= htmlspecialchars($fotos[0]) ?>" 
                    alt="<?= htmlspecialchars($evento['nome']) ?>" 
                    style="width:100%; height:360px; object-fit:cover; border-radius:8px;" 
                />

                <!-- ======================= -->
                <!-- MINIATURAS - TODAS AS FOTOS -->
                <!-- ======================= -->
                <?php if (count($fotos) > 1) : ?>
                    <div id="thumbnails-event" 
                         style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap; width:100%;">

                        <?php foreach ($fotos as $k => $f) : if ($k === 0) continue; ?>
                            <img 
                                class="thumb-event"
                                src="img/eventos/<?= intval($id) ?>/fotos_eventos/<?= htmlspecialchars($f) ?>"
                                data-large="img/eventos/<?= intval($id) ?>/fotos_eventos/<?= htmlspecialchars($f) ?>"
                                alt="foto <?= $k ?>"
                                style="width:120px; height:90px; object-fit:cover; border-radius:6px; flex:0 0 auto;"
                            />
                        <?php endforeach; ?>

                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="background:#eee; height:360px; display:flex; 
                            align-items:center; justify-content:center; border-radius:8px;">
                    Sem imagem
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================= -->
        <!--      INFORMAÇÕES       -->
        <!-- ======================= -->
        <div style="flex:1 1 380px; min-width:260px;">
            <h3>Informações</h3>
            <p><strong>Data:</strong> <?= Utils::formatarData($evento['dia'], true) ?></p>
            <p><strong>Horário:</strong> <?= htmlspecialchars($evento['horario']) ?></p>
            <p><strong>Local:</strong> <?= htmlspecialchars($evento['endereco']) ?> — <?= htmlspecialchars($evento['cidade']) ?> / <?= htmlspecialchars($evento['estado']) ?></p>
            <p><strong>Contato:</strong> <?= htmlspecialchars($evento['contato']) ?></p>

            <?php if (!empty($estilos)) : ?>
                <p><strong>Estilos:</strong> <?= htmlspecialchars(implode(', ', $estilos)) ?></p>
            <?php endif; ?>

            <?php if (!empty($participantes)) : ?>
                <h4>Participantes</h4>
                <ul>
                    <?php foreach ($participantes as $p) : ?>
                        <li><a href="artista.php?artista=<?= intval($p['id']) ?>"><?= htmlspecialchars($p['nome']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($evento['link_compra'])) : ?>
                <p><a href="<?= htmlspecialchars($evento['link_compra']) ?>" target="_blank" rel="noopener">
                    Comprar ingresso / Mais info
                </a></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($evento['descricao'])) : ?>
    <section style="margin-top:20px;">
        <h3>Descrição</h3>
        <p><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
    </section>
    <?php endif; ?>

</main>

<?php require_once "includes/rodape.php"; ?>
<script src="js/gallery.js"></script>
</body>
</html>
