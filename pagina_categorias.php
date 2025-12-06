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

// Se não houver filtros, buscar 4 aleatórios. Caso contrário, buscar por filtros.
if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
    $eventos = $eventoServicos->buscarEventosAleatorios(4);
} else {
    $eventos = $eventoServicos->buscarEventosPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
    // limitar a 4 resultados para preservar o layout 2x2
    if (count($eventos) > 4) $eventos = array_slice($eventos, 0, 4);
}

$contador = 0;

$estilosMusicaisServicos = new EstilosMusicaisServicos();

// Ler filtros da query string (se houver) - reusar as mesmas chaves
$estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidadeFiltro = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estiloFiltro = isset($_GET['estilo']) && $_GET['estilo'] !== '' ? trim($_GET['estilo']) : null;

// Se não houver filtro, buscar 6 estilos aleatórios (3x3). Caso contrário, buscar por filtros.
// Paginação simples para estilos
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;

if (!$estadoFiltro && !$cidadeFiltro && !$estiloFiltro) {
    $seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : mt_rand();
    $offset = ($page - 1) * $perPage;
    $pdo = $estilosMusicaisServicos->conexao;
    $sql = "SELECT id, nome, imagem FROM estilo_musical ORDER BY RAND(:seed) LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':seed', $seed, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $estilos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM estilo_musical');
    $countStmt->execute();
    $totalEstilos = intval($countStmt->fetchColumn() ?: 0);
} else {
    $allEstilos = $estilosMusicaisServicos->buscarEstilosPorFiltros($estadoFiltro, $cidadeFiltro, $estiloFiltro) ?: [];
    $totalEstilos = count($allEstilos);
    $offset = ($page - 1) * $perPage;
    $estilos = array_slice($allEstilos, $offset, $perPage);
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
        ESCOLHA UMA <span style="color: #04A777;">CATEGORIA MUSICAL!</span>
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
                // Buscar todos os estilos musicais (nome)
                $stmt = $estilosMusicaisServicos->conexao->prepare("SELECT DISTINCT nome FROM estilo_musical ORDER BY nome ASC");
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
    if (empty($estilos)) {
        // Quando não houver estilos, ocultamos a visualização (nenhuma saída)
    } else {
        $rows = array_chunk($estilos, 3);
                foreach ($rows as $row) {
            echo '<div class="linha_cards estilos">';
            foreach ($row as $estilo) {
                $id = intval($estilo['id']);
                $img = htmlspecialchars($estilo['imagem'] ?? '');
                $nome = htmlspecialchars($estilo['nome']);
                // Compatibilidade: passar tanto o id quanto o nome do estilo
                $qsName = urlencode($estilo['nome']);
                echo '<a href="encontre_artistas.php?estilo_id=' . $id . '&estilo=' . $qsName . '" class="caixa_banda">';
                echo '<img src="img/estilos_musicais/' . $img . '" alt="' . $nome . '" />';
                echo '<div class="texto_banda_overlay">';
                echo '<h3 class="titulo_banda">' . $nome . '</h3>';
                echo '</div></a>';
            }
            echo '</div>';
        }
    }

    // Link "Mostrar mais" se existirem mais estilos além dos exibidos
    if ($offset + count($estilos) < $totalEstilos) {
        $nextPage = $page + 1;
        $qs = $_GET;
        $qs['page'] = $nextPage;
        $href = 'pagina_categorias.php?' . htmlspecialchars(http_build_query($qs));
        echo '<div class="linha_cards"><a class="mostrar-mais" href="' . $href . '" data-source="estilos" data-page="' . $nextPage . '" data-seed="' . ($seed ?? '') . '">Mostrar mais</a></div>';
    }
    ?>

</section>

<script src="js/encontre-artistas-menu.js"></script>
<script src="js/select-dependent.js"></script>
<script src="js/load-more.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>