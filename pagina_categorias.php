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
                echo '<a href="artista.php?artista=' . intval($evento['id']) . '" class="caixa_banda">';
                $img = htmlspecialchars($evento['url_imagem'] ?? '');
                $nome = htmlspecialchars($evento['nome']);
                echo '<img src="img/eventos/' . intval($evento['id']) . '/fotos_eventos/' . $img . '" alt="' . $nome . '" />';
                echo '<div class="texto_banda_overlay">';
                echo '<h3 class="titulo_banda">' . $nome . '</h3>';
                echo '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(', ', $evento['estilos_musicais'])) . '</h4>';
                echo '<h4 class="data_evento">' . Utils::formatarData($evento['dia'], true) . '</h4>';
                echo '</div></a>';
            }
            echo '</div>';
        }
    }
    ?>

</section>

<script src="js/encontre-artistas-menu.js"></script>
<script src="js/select-dependent.js"></script>

<body>
    <?php require_once "includes/rodape.php" ?>
</body>

</html>