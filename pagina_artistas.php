<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

$artistaServico = new ArtistaServicos();

// Ler filtros da query string (sequencial: estado -> cidade -> estilo)
$estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidadeFiltro = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estiloFiltro = isset($_GET['estilo']) && $_GET['estilo'] !== '' ? trim($_GET['estilo']) : null;

// suporte a filtro por id de estilo (vindo de pagina_categorias)
$estiloIdFiltro = isset($_GET['estilo_id']) && $_GET['estilo_id'] !== '' ? intval($_GET['estilo_id']) : null;

// Paginação simples para artistas
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;

// Se filtro por id de estilo foi fornecido, usar método direto por id (mais robusto)
    if ($estiloIdFiltro !== null) {
    $allArtistas = $artistaServico->buscarArtistasPorEstiloId($estiloIdFiltro) ?: [];
    $totalArtistas = count($allArtistas);
    $offset = ($page - 1) * $perPage;
    $artistas = array_slice($allArtistas, $offset, $perPage);
} else {
    if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
        $seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : mt_rand();
        $offset = ($page - 1) * $perPage;
        $pdo = $artistaServico->conexao;
        $sql = "SELECT artistas.id, artistas.nome, artistas.cidade, artistas.estado, (
            SELECT foto_artista.url_imagem
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
            LIMIT 1
        ) AS url_imagem, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilos_musicais
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
    } else {
        $allArtistas = $artistaServico->buscarArtistasPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
        $totalArtistas = count($allArtistas);
        $offset = ($page - 1) * $perPage;
        $artistas = array_slice($allArtistas, $offset, $perPage);
    }
}

$estilosMusicaisServicos = new EstilosMusicaisServicos();

$estilos_musicais = $estilosMusicaisServicos->buscarEstilosComLimite();



?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahDB</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/encontre-artistas.css">
    <?php
    require_once "includes/cabecalho.php";
    ?>
</head>
 
<section id="secao_bandas">

   <h2 class="titulo_secao">
        ENCONTRE UM <span style="color: #FB8B24;">ARTISTA!</span>
    </h2>

    <nav class="nav-selects" data-source="artistas">

        <div class="custom-select" data-field="estado">
            <div class="select-header">
                <span class="selected-option">estado</span>
                <button type="button" class="reset-select" title="Limpar">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php
                // Buscar estados distintos na tabela artistas
                $stmt = $artistaServico->conexao->prepare("SELECT DISTINCT estado FROM artistas WHERE estado IS NOT NULL AND estado != '' ORDER BY estado ASC");
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
                // Buscar cidades distintas na tabela artistas
                $stmt = $artistaServico->conexao->prepare("SELECT DISTINCT cidade FROM artistas WHERE cidade IS NOT NULL AND cidade != '' ORDER BY cidade ASC");
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
                // Buscar estilos musicais associados a artistas
                $sql = "SELECT DISTINCT em.nome
                        FROM artista_estilo ae
                        JOIN estilo_musical em ON em.id = ae.id_estilo
                        ORDER BY em.nome ASC";
                $stmt = $artistaServico->conexao->prepare($sql);
                $stmt->execute();
                $estilos_artista = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($estilos_artista)) {
                    foreach ($estilos_artista as $estNome) {
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
    if (empty($artistas)) {
        echo '<div class="linha_cards"><p>Nenhum artista encontrado para os filtros selecionados.</p></div>';
    } else {
        $rows = array_chunk($artistas, 3);
        foreach ($rows as $row) {
            echo '<div class="linha_cards">';
            foreach ($row as $artista) {
                $artista['estilos_musicais'] = explode(",", $artista['estilos_musicais']);
                $id = intval($artista['id']);
                $mainImg = $artista['url_imagem'] ?? '';
                $nome = htmlspecialchars($artista['nome']);
                echo '<a href="artista.php?artista=' . $id . '" class="caixa_banda">';
                echo '<img src="img/artistas/' . $id . '/fotos_artistas/' . htmlspecialchars($mainImg) . '" alt="' . $nome . '" />';
                echo '<div class="texto_banda_overlay">';
                echo '<h3 class="titulo_banda">' . $nome . '</h3>';
                echo '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(', ', $artista['estilos_musicais'])) . '</h4>';
                echo '</div>';
                if (!empty($imgs)) {
                    echo '<div class="thumb-strip">';
                    foreach ($imgs as $t) {
                        echo '<img class="card-thumb" src="img/artistas/' . $id . '/fotos_artistas/' . htmlspecialchars($t) . '" alt="' . $nome . '" />';
                    }
                    echo '</div>';
                }
                echo '</a>';
            }
            echo '</div>';
        }
    }
    ?>

    <?php
    // Link "Mostrar mais" se existirem mais artistas além dos exibidos
    if ($offset + count($artistas) < $totalArtistas) {
        $nextPage = $page + 1;
        $qs = $_GET;
        $qs['page'] = $nextPage;
        $href = 'encontre_artistas.php?' . htmlspecialchars(http_build_query($qs));
        echo '<div class="linha_cards"><a class="mostrar-mais" href="' . $href . '" data-source="artistas" data-page="' . $nextPage . '" data-seed="' . ($seed ?? '') . '">Mostrar mais</a></div>';
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