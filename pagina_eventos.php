<?php
// pagina_eventos.php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasComLimite(4);

$eventoServicos = new EventoServicos();
$estilosMusicaisServicos = new EstilosMusicaisServicos();

$estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidadeFiltro = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estiloFiltro = isset($_GET['estilo']) && $_GET['estilo'] !== '' ? trim($_GET['estilo']) : null;

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 4;
$isAjax = isset($_GET['ajax']) && ($_GET['ajax'] === '1' || $_GET['ajax'] === 'true');

$pdo = $eventoServicos->conexao;

function carregarEventos($pdo, $eventoServicos, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $page, $perPage)
{
    $offset = ($page - 1) * $perPage;
    if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
        $seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : mt_rand();
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,
                (SELECT foto_evento.url_imagem FROM foto_evento WHERE foto_evento.id_evento = eventos.id ORDER BY foto_evento.id ASC LIMIT 1) AS url_imagem,
                (SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',') FROM evento_estilo JOIN estilo_musical ON estilo_musical.id = evento_estilo.id_estilo WHERE evento_estilo.id_evento = eventos.id ORDER BY estilo_musical.id ASC) AS estilos_musicais
                FROM eventos
                ORDER BY RAND(:seed)
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':seed', $seed, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM eventos');
        $countStmt->execute();
        $totalEventos = intval($countStmt->fetchColumn() ?: 0);
        return [$eventos, $totalEventos, $seed];
    } else {
        $allEventos = $eventoServicos->buscarEventosPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
        $totalEventos = count($allEventos);
        $eventos = array_slice($allEventos, $offset, $perPage);
        return [$eventos, $totalEventos, null];
    }
}

function renderizarCardsHTML($eventos)
{
    $html = '';
    foreach ($eventos as $evento) {
        $estilos = [];
        if (!empty($evento['estilos_musicais'])) {
            $estilos = is_string($evento['estilos_musicais']) ? explode(',', $evento['estilos_musicais']) : $evento['estilos_musicais'];
        }
        $nome = htmlspecialchars($evento['nome']);
        $idEvento = intval($evento['id']);
        $imgFile = isset($evento['url_imagem']) ? $evento['url_imagem'] : '';
        $imgPath = "img/eventos/{$idEvento}/fotos_eventos/" . $imgFile;
        if (empty($imgFile) || !file_exists($imgPath)) $imgPath = "img/sem-imagem.png";

        $html .= '<a href="evento.php?evento=' . $idEvento . '" class="caixa_eventos">';
        $html .= '<img src="' . htmlspecialchars($imgPath) . '" alt="' . $nome . '"/>';
        $html .= '<div class="textos_eventos">';
        $html .= '<div class="texto_superior">';
        $html .= '<h3>' . $nome . '</h3>';
        $html .= '<h4>' . htmlspecialchars($evento['cidade']) . ' - ' . htmlspecialchars($evento['estado']) . '</h4>';
        $html .= '</div>';
        $html .= '<div class="texto_inferior">';
        $html .= '<h4 class="estilos_eventos">' . htmlspecialchars(implode(', ', array_map('trim', $estilos))) . '</h4>';
        $dataFormatada = Utils::formatarData($evento['dia'], true);
        $html .= '<h4>' . $dataFormatada . '</h4>';
        $html .= '</div></div></a>';
    }
    return $html;
}

if ($isAjax) {
    $pageAjax = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    list($eventosAjax, $totalEventosAjax, $seedAjax) = carregarEventos($pdo, $eventoServicos, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $pageAjax, $perPage);
    $htmlCards = renderizarCardsHTML($eventosAjax);
    $offsetAjax = ($pageAjax - 1) * $perPage;
    $hasMore = ($offsetAjax + count($eventosAjax)) < $totalEventosAjax;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'html' => $htmlCards,
        'hasMore' => $hasMore,
        'nextPage' => $pageAjax + 1,
        'seed' => $seedAjax,
        'total' => $totalEventosAjax,
        'countReturned' => count($eventosAjax),
    ]);
    exit;
}

list($eventos, $totalEventos, $seed) = carregarEventos($pdo, $eventoServicos, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $page, $perPage);
$estilos_musicais = $estilosMusicaisServicos->buscarEstilosComLimite();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>YeahDB - Eventos</title>
    <link rel="stylesheet" href="css/pagina_eventos.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <style>
        .mostrar-mais.loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <section id="secao_bandas">
        <h2 id="titulo_evento">ENCONTRE UM <span style="color:#04A777;">EVENTO!</span></h2>

        <nav class="nav-selects" data-source="eventos">

            <div class="custom-select" data-field="estado">
                <div class="select-header">
                    <span class="selected-option"><?php echo $estadoFiltro ? htmlspecialchars($estadoFiltro) : 'ESTADO'; ?></span>
                    <button type="button" class="reset-select" title="Limpar">✕</button>
                    <i class="arrow"></i>
                </div>
                <ul class="select-list">
                    <?php
                    $estados = json_decode(@file_get_contents("https://servicodados.ibge.gov.br/api/v1/localidades/estados"), true);

                    if (!empty($estados)) {

                        // Ordena alfabeticamente pela sigla
                        usort($estados, function ($a, $b) {
                            return strcmp($a['sigla'], $b['sigla']);
                        });

                        foreach ($estados as $est) {
                            echo '<li data-value="' . htmlspecialchars($est['sigla']) . '">'
                                . htmlspecialchars($est['sigla']) .
                                '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="custom-select" data-field="cidade">
                <div class="select-header">
                    <span class="selected-option"><?php echo $cidadeFiltro ? htmlspecialchars($cidadeFiltro) : 'CIDADE'; ?></span>
                    <button type="button" class="reset-select" title="Limpar">✕</button>
                    <i class="arrow"></i>
                </div>
                <ul class="select-list">
                    <?php
                    if ($estadoFiltro) {
                        $uf = $estadoFiltro;
                        $cidadesJson = @file_get_contents("https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios");
                        $cidadesArr = json_decode($cidadesJson, true) ?: [];
                        foreach ($cidadesArr as $c) echo '<li data-value="' . htmlspecialchars($c['nome']) . '">' . htmlspecialchars($c['nome']) . '</li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="custom-select" data-field="estilo">
                <div class="select-header">
                    <span class="selected-option"><?php echo $estiloFiltro ? htmlspecialchars($estiloFiltro) : 'ESTILO MUSICAL'; ?></span>
                    <button type="button" class="reset-select" title="Limpar">✕</button>
                    <i class="arrow"></i>
                </div>
                <ul class="select-list">
                    <?php
                    foreach ($estilos_musicais as $est) echo '<li data-value="' . htmlspecialchars($est['nome']) . '">' . htmlspecialchars($est['nome']) . '</li>';
                    ?>
                </ul>
            </div>

        </nav>

        <div class="linha_cards linha-eventos">
            <?php
            $cardsHtml = renderizarCardsHTML($eventos);
            echo $cardsHtml ?: '<p>Nenhum evento encontrado para os filtros selecionados.</p>';
            ?>
        </div>

        <?php
        $offset = ($page - 1) * $perPage;
        if ($offset + count($eventos) < $totalEventos) {
            $nextPage = $page + 1;
            $qs = $_GET;
            $qs['page'] = $nextPage;
            if (!empty($seed)) $qs['seed'] = $seed;
            $href = 'pagina_eventos.php?' . htmlspecialchars(http_build_query($qs));
        ?>
            <div class="linha_cards">
                <a class="mostrar-mais" href="<?php echo $href; ?>" data-source="eventos" data-page="<?php echo $nextPage; ?>" data-seed="<?php echo htmlspecialchars($seed ?? ''); ?>" role="button">Mostrar mais</a>
            </div>
        <?php } ?>

    </section>

    <script src="js/pag-eventos.js"></script>

    <?php require_once "includes/rodape.php"; ?>
</body>

</html>