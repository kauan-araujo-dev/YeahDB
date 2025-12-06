<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasComLimite(4);

$eventoServicos = new EventoServicos();

// Ler filtros da query string (se houver)
$estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidadeFiltro = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estiloFiltro = isset($_GET['estilo']) && $_GET['estilo'] !== '' ? trim($_GET['estilo']) : null;

// Paginação simples: ler página atual
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 4;

// Se não houver filtros, gerar uma seed e usar consulta determinística ORDER BY RAND(:seed)
if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
    $seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : mt_rand();
    $offset = ($page - 1) * $perPage;

    $pdo = $eventoServicos->conexao;
    $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,
            (
                SELECT foto_evento.url_imagem
                FROM foto_evento
                WHERE foto_evento.id_evento = eventos.id
                ORDER BY foto_evento.id ASC
                LIMIT 1
            ) AS url_imagem,
            (
                SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
                FROM evento_estilo
                JOIN estilo_musical ON estilo_musical.id = evento_estilo.id_estilo
                WHERE evento_estilo.id_evento = eventos.id
                ORDER BY estilo_musical.id ASC
                LIMIT 1
            ) AS estilos_musicais
        FROM eventos
        ORDER BY RAND(:seed)
        LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':seed', $seed, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // total de eventos para controle do botão
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM eventos');
    $countStmt->execute();
    $totalEventos = intval($countStmt->fetchColumn() ?: 0);

} else {
    $allEventos = $eventoServicos->buscarEventosPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
    $totalEventos = count($allEventos);
    $offset = ($page - 1) * $perPage;
    $eventos = array_slice($allEventos, $offset, $perPage);
    $seed = null;
}

$contador = 0;

$estilosMusicaisServicos = new EstilosMusicaisServicos();

$estilos_musicais = $estilosMusicaisServicos->buscarEstilosComLimite();



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

    <nav class="nav-selects" data-source="eventos">

        <div class="custom-select" data-field="estado">
            <div class="select-header">
                <span class="selected-option">estado</span>
                <button type="button" class="reset-select" title="Limpar">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar estados distintos na tabela eventos
                $stmt = $eventoServicos->conexao->prepare("SELECT DISTINCT estado FROM eventos WHERE estado IS NOT NULL AND estado != '' ORDER BY estado ASC");
                $stmt->execute();
                $estados = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($estados)) {
                    foreach ($estados as $est) {
                        echo '<li>' . htmlspecialchars($est) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

        <div class="custom-select" data-field="cidade">
            <div class="select-header">
                <span class="selected-option">cidade</span>
                <button type="button" class="reset-select" title="Limpar">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar cidades distintas na tabela eventos
                $stmt = $eventoServicos->conexao->prepare("SELECT DISTINCT cidade FROM eventos WHERE cidade IS NOT NULL AND cidade != '' ORDER BY cidade ASC");
                $stmt->execute();
                $cidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($cidades)) {
                    foreach ($cidades as $cid) {
                        echo '<li>' . htmlspecialchars($cid) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

        <div class="custom-select" data-field="estilo">
            <div class="select-header">
                <span class="selected-option">estilo musical</span>
                <button type="button" class="reset-select" title="Limpar">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar estilos musicais associados a eventos
                $sql = "SELECT DISTINCT em.nome
                        FROM evento_estilo ee
                        JOIN estilo_musical em ON em.id = ee.id_estilo
                        ORDER BY em.nome ASC";
                $stmt = $eventoServicos->conexao->prepare($sql);
                $stmt->execute();
                $estilos_evento = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($estilos_evento)) {
                    foreach ($estilos_evento as $estNome) {
                        echo '<li>' . htmlspecialchars($estNome) . '</li>';
                    }
                } else {
                    echo '<li>Nenhuma informação localizada</li>';
                }
                ?>
            </ul>
        </div>

    </nav>


    <?php
    if (empty($eventos)) {
        echo '<div class="linha_cards"><p>Nenhum evento encontrado para os filtros selecionados.</p></div>';
    } else {
        $rows = array_chunk($eventos, 2);
        foreach ($rows as $row) {
            echo '<div class="linha_cards">';
            foreach ($row as $evento) {
                    $evento['estilos_musicais'] = explode(",", $evento['estilos_musicais']);
                    // imagens múltiplas separadas por '||' (quando vindas da consulta com GROUP_CONCAT)
                    $imgs = isset($evento['imagens']) ? array_filter(explode('||', $evento['imagens'])) : [];
                    $mainImg = $imgs[0] ?? '';
                    echo '<a href="evento.php?evento=' . intval($evento['id']) . '" class="caixa_banda">';
                    $nome = htmlspecialchars($evento['nome']);
                    echo '<img src="img/eventos/' . intval($evento['id']) . '/fotos_eventos/' . htmlspecialchars($mainImg) . '" alt="' . $nome . '" />';
                    echo '<div class="texto_banda_overlay">';
                    echo '<h3 class="titulo_banda">' . $nome . '</h3>';
                    echo '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(', ', $evento['estilos_musicais'])) . '</h4>';
                    echo '<h4 class="data_evento">' . Utils::formatarData($evento['dia'], true) . '</h4>';
                    echo '</div>';
                    if (!empty($imgs)) {
                        echo '<div class="thumb-strip">';
                        foreach ($imgs as $t) {
                            echo '<img class="card-thumb" src="img/eventos/' . intval($evento['id']) . '/fotos_eventos/' . htmlspecialchars($t) . '" alt="' . $nome . '" />';
                        }
                        echo '</div>';
                    }
                    echo '</a>';
            }
            echo '</div>';
        }
    }

    // Link "Mostrar mais" se existirem mais eventos além dos exibidos
    if ($offset + count($eventos) < $totalEventos) {
        $nextPage = $page + 1;
        // preserva filtros na querystring
        $qs = $_GET;
        $qs['page'] = $nextPage;
        if (!empty($seed)) $qs['seed'] = $seed;
        $href = 'pagina_eventos.php?' . htmlspecialchars(http_build_query($qs));
        echo '<div class="linha_cards"><a class="mostrar-mais" href="' . $href . '" data-source="eventos" data-page="' . $nextPage . '" data-seed="' . ($seed ?? '') . '">Mostrar mais</a></div>';
    }
    ?>

</section>

<script src="js/encontre-artistas-menu.js"></script>
<script src="js/select-dependent.js"></script>
<script src="js/gallery.js"></script>
<script src="js/load-more.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>