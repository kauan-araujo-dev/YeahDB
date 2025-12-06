<?php
require_once dirname(__DIR__) . '/src/Database/Conecta.php';
require_once dirname(__DIR__) . '/src/Helpers/Utils.php';
require_once dirname(__DIR__) . '/src/Services/EventoServicos.php';
require_once dirname(__DIR__) . '/src/Services/ArtistaServicos.php';
require_once dirname(__DIR__) . '/src/Services/EstilosMusicaisServicos.php';

header('Content-Type: application/json; charset=utf-8');

$source = $_GET['source'] ?? ($_POST['source'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['perPage']) ? intval($_GET['perPage']) : 4;

$estado = isset($_GET['estado']) ? trim($_GET['estado']) : null;
$cidade = isset($_GET['cidade']) ? trim($_GET['cidade']) : null;
$estilo = isset($_GET['estilo']) ? trim($_GET['estilo']) : null;
$estilo_id = isset($_GET['estilo_id']) ? intval($_GET['estilo_id']) : null;

$html = '';
$hasMore = false;
$nextPage = $page + 1;

$eventoServ = new EventoServicos();
$artistaServ = new ArtistaServicos();
$estiloServ = new EstilosMusicaisServicos();

try {
    if ($source === 'eventos') {
        if (!$estado && !$cidade && !$estilo) {
            $all = $eventoServ->buscarEventosAleatorios(100) ?: [];
        } else {
            $all = $eventoServ->buscarEventosPorFiltros($estado, $cidade, $estilo) ?: [];
        }

        $total = count($all);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($all, $offset, $perPage);

        foreach ($slice as $evento) {
            $evento['estilos_musicais'] = explode(",", $evento['estilos_musicais']);
            $img = htmlspecialchars($evento['url_imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($evento['nome'], ENT_QUOTES, 'UTF-8');
            $id = intval($evento['id']);
            $html .= '<a href="evento.php?evento=' . $id . '" class="caixa_banda">';
            $html .= '<img src="img/eventos/' . $id . '/fotos_eventos/' . $img . '" alt="' . $nome . '" />';
            $html .= '<div class="texto_banda_overlay">';
            $html .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $html .= '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(', ', $evento['estilos_musicais'])) . '</h4>';
            $html .= '<h4 class="data_evento">' . Utils::formatarData($evento['dia'], true) . '</h4>';
            $html .= '</div></a>';
        }

        $hasMore = ($offset + count($slice)) < $total;

    } elseif ($source === 'artistas') {
        // se foi passado estilo_id, prioriza
        if ($estilo_id) {
            $all = $artistaServ->buscarArtistasPorEstiloId($estilo_id) ?: [];
        } else {
            if (!$estado && !$cidade && !$estilo) {
                $all = $artistaServ->buscarArtistasAleatorios(100) ?: [];
            } else {
                $all = $artistaServ->buscarArtistasPorFiltros($estado, $cidade, $estilo) ?: [];
            }
        }

        $total = count($all);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($all, $offset, $perPage);

        foreach ($slice as $artista) {
            $id = intval($artista['id']);
            $img = htmlspecialchars($artista['url_imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($artista['nome'], ENT_QUOTES, 'UTF-8');
            $html .= '<a href="artista.php?artista=' . $id . '" class="caixa_banda">';
            $html .= '<img src="img/artistas/' . $id . '/fotos_artistas/' . $img . '" alt="' . $nome . '" />';
            $html .= '<div class="texto_banda_overlay">';
            $html .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $html .= '<h4 class="estilo_musical_banda">' . htmlspecialchars($artista['estilos_musicais']) . '</h4>';
            $html .= '</div></a>';
        }

        $hasMore = ($offset + count($slice)) < $total;

    } elseif ($source === 'estilos') {
        if (!$estado && !$cidade && !$estilo) {
            $all = $estiloServ->buscarEstilosAleatorios(100) ?: [];
        } else {
            $all = $estiloServ->buscarEstilosPorFiltros($estado, $cidade, $estilo) ?: [];
        }

        $total = count($all);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($all, $offset, $perPage);

        foreach ($slice as $est) {
            $id = intval($est['id']);
            $img = htmlspecialchars($est['imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($est['nome'], ENT_QUOTES, 'UTF-8');
            $html .= '<a href="encontre_artistas.php?estilo_id=' . $id . '&estilo=' . urlencode($est['nome']) . '" class="caixa_banda">';
            $html .= '<img src="img/estilos_musicais/' . $img . '" alt="' . $nome . '" />';
            $html .= '<div class="texto_banda_overlay">';
            $html .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $html .= '</div></a>';
        }

        $hasMore = ($offset + count($slice)) < $total;

    } else {
        throw new Exception('Fonte inválida');
    }

    echo json_encode(['html' => $html, 'hasMore' => $hasMore, 'nextPage' => $nextPage]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
