<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();
$estilosMusicaisServicos = new EstilosMusicaisServicos();

$estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidadeFiltro = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estiloFiltro = isset($_GET['estilo']) && $_GET['estilo'] !== '' ? trim($_GET['estilo']) : null;

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 4;
$isAjax = isset($_GET['ajax']) && ($_GET['ajax'] === '1' || $_GET['ajax'] === 'true');

$pdo = $artistaServico->conexao;

function carregarArtistas($pdo, $artistaServico, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $page, $perPage)
{
    $offset = ($page - 1) * $perPage;

    if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
        $seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : mt_rand();
        $sql = "SELECT artistas.id, artistas.nome,
                (SELECT foto_artista.url_imagem FROM foto_artista WHERE foto_artista.id_artista = artistas.id ORDER BY foto_artista.id ASC LIMIT 1) AS imagens,
                (SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',') 
                 FROM artista_estilo 
                 JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo 
                 WHERE artista_estilo.id_artista = artistas.id) AS estilos_musicais
                FROM artistas
                ORDER BY RAND(:seed)
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':seed', $seed, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $artistas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM artistas');
        $countStmt->execute();
        $totalArtistas = intval($countStmt->fetchColumn() ?: 0);
        return [$artistas, $totalArtistas, $seed];
    } else {
        $allArtistas = $artistaServico->buscarArtistasPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
        $totalArtistas = count($allArtistas);
        $artistas = array_slice($allArtistas, $offset, $perPage);
        return [$artistas, $totalArtistas, null];
    }
}

function renderizarCardsArtistas($artistas)
{
    $html = '';
    foreach ($artistas as $artista) {
        $id = intval($artista['id']);
        $nome = htmlspecialchars($artista['nome']);

        $imagens = explode("||", $artista['imagens'] ?? '');
        $imgPrincipal = $imagens[0] ?? "sem-imagem.png";

        $caminhoImagem = "img/artistas/{$id}/fotos_artistas/{$imgPrincipal}";
        if (!file_exists($caminhoImagem)) $caminhoImagem = "img/sem-imagem.png";

        $html .= '<a href="artista.php?artista=' . $id . '" class="caixa_banda">';
        $html .= '<img src="' . htmlspecialchars($caminhoImagem) . '" alt="' . $nome . '">';
        $html .= '<div class="texto_banda_overlay">';
        $html .= '<h3 class="titulo_banda">' . $nome . '</h3>';
        $html .= '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(", ", explode(",", $artista['estilos_musicais'])) ?? '') . '</h4>';
        $html .= '</div></a>';
    }
    return $html;
}

if ($isAjax) {
    $pageAjax = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    list($artistasAjax, $totalArtistasAjax, $seedAjax) = carregarArtistas($pdo, $artistaServico, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $pageAjax, $perPage);
    $htmlCards = renderizarCardsArtistas($artistasAjax);
    $offsetAjax = ($pageAjax - 1) * $perPage;
    $hasMore = ($offsetAjax + count($artistasAjax)) < $totalArtistasAjax;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'html' => $htmlCards,
        'hasMore' => $hasMore,
        'nextPage' => $pageAjax + 1,
        'seed' => $seedAjax,
        'total' => $totalArtistasAjax,
        'countReturned' => count($artistasAjax),
    ]);
    exit;
}

list($artistas, $totalArtistas, $seed) = carregarArtistas($pdo, $artistaServico, $estadoFiltro, $cidadeFiltro, $estiloFiltro, $page, $perPage);
$estilos_musicais = $estilosMusicaisServicos->buscarEstilosComLimite();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>YeahDB - Artistas</title>
    <link rel="stylesheet" href="css/pagina_artistas_menu.css">
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
        <h3 id="titulo_secao" class="titulo_secao">ENCONTRE UM <span style="color:#FB8B24;">ARTISTA!</span></h3>

        <nav class="nav-selects" data-source="artistas">
            <!-- Estados -->
            <div class="custom-select" data-field="estado">
                <div class="select-header">
                    <span class="selected-option"><?= $estadoFiltro ? htmlspecialchars($estadoFiltro) : 'ESTADO' ?></span>
                    <button type="button" class="reset-select" title="Limpar">✕</button>
                    <i class="arrow"></i>
                </div>
                <ul class="select-list">
                    <?php
                    $estados = json_decode(@file_get_contents("https://servicodados.ibge.gov.br/api/v1/localidades/estados"), true);
                    if (!empty($estados)) {
                        usort($estados, fn($a, $b) => strcmp($a['sigla'], $b['sigla']));
                        foreach ($estados as $est) echo '<li data-value="' . htmlspecialchars($est['sigla']) . '">' . htmlspecialchars($est['sigla']) . '</li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Cidades -->
            <div class="custom-select" data-field="cidade">
                <div class="select-header">
                    <span class="selected-option"><?= $cidadeFiltro ? htmlspecialchars($cidadeFiltro) : 'CIDADE' ?></span>
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

            <!-- Estilos -->
            <div class="custom-select" data-field="estilo">
                <div class="select-header">
                    <span class="selected-option"><?= $estiloFiltro ? htmlspecialchars($estiloFiltro) : 'ESTILO MUSICAL' ?></span>
                    <button type="button" class="reset-select" title="Limpar">✕</button>
                    <i class="arrow"></i>
                </div>
                <ul class="select-list">
                    <?php foreach ($estilos_musicais as $est) echo '<li data-value="' . htmlspecialchars($est['nome']) . '">' . htmlspecialchars($est['nome']) . '</li>'; ?>
                </ul>
            </div>
        </nav>

        <div class="linha_cards">
            <?= renderizarCardsArtistas($artistas) ?: '<p>Nenhum artista encontrado para os filtros selecionados.</p>'; ?>
        </div>

        <?php
        $offset = ($page - 1) * $perPage;
        if ($offset + count($artistas) < $totalArtistas):
            $nextPage = $page + 1;
            $qs = $_GET;
            $qs['page'] = $nextPage;
            if (!empty($seed)) $qs['seed'] = $seed;
            $href = 'pagina_artistas.php?' . htmlspecialchars(http_build_query($qs));
        ?>
            <div class="linha_cards">
                <a class="mostrar-mais" href="<?= $href ?>" data-source="artistas" data-page="<?= $nextPage ?>" data-seed="<?= htmlspecialchars($seed ?? '') ?>" role="button">Mostrar mais</a>
            </div>
        <?php endif; ?>
    </section>

    <script src="js/pag_artistas.js">
    </script>

    <?php require_once "includes/rodape.php"; ?>
</body>

</html>