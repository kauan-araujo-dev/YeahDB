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

$seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? intval($_GET['seed']) : null;

$eventoServ = new EventoServicos();
$artistaServ = new ArtistaServicos();
$estiloServ = new EstilosMusicaisServicos();

try {
    if ($source === 'eventos') {
        $offset = ($page - 1) * $perPage;
        $pdo = Conecta::getConexao();
        if ($seed !== null && !$estado && !$cidade && !$estilo) {
            // consulta determinística por seed
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
            $slice = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM eventos');
            $countStmt->execute();
            $total = intval($countStmt->fetchColumn() ?: 0);
        } else {
            if (!$estado && !$cidade && !$estilo) {
                $all = $eventoServ->buscarEventosAleatorios(100) ?: [];
            } else {
                $all = $eventoServ->buscarEventosPorFiltros($estado, $cidade, $estilo) ?: [];
            }

            $total = count($all);
            $offset = ($page - 1) * $perPage;
            $slice = array_slice($all, $offset, $perPage);
        }

        $anchors = [];
        foreach ($slice as $evento) {
            $evento['estilos_musicais'] = explode(",", $evento['estilos_musicais']);
            $img = htmlspecialchars($evento['url_imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($evento['nome'], ENT_QUOTES, 'UTF-8');
            $id = intval($evento['id']);
            $anchor = '';
            $anchor .= '<a href="evento.php?evento=' . $id . '" class="caixa_banda">';
            $anchor .= '<img src="img/eventos/' . $id . '/fotos_eventos/' . $img . '" alt="' . $nome . '" />';
            $anchor .= '<div class="texto_banda_overlay">';
            $anchor .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $anchor .= '<h4 class="estilo_musical_banda">' . htmlspecialchars(implode(', ', $evento['estilos_musicais'])) . '</h4>';
            $anchor .= '<h4 class="data_evento">' . Utils::formatarData($evento['dia'], true) . '</h4>';
            $anchor .= '</div></a>';
            $anchors[] = $anchor;
        }

        // manter o mesmo layout por linhas usado em pagina_eventos.php (2 por linha)
        $rowSize = 2;
        $chunks = array_chunk($anchors, $rowSize);
        foreach ($chunks as $chunk) {
            $html .= '<div class="linha_cards">' . implode('', $chunk) . '</div>';
        }

        $hasMore = ($offset + count($slice)) < $total;

    } elseif ($source === 'artistas') {
        $offset = ($page - 1) * $perPage;
        $pdo = Conecta::getConexao();
        // se foi passado estilo_id, prioriza (busca por id retorna determinístico)
        if ($estilo_id !== null) {
            $all = $artistaServ->buscarArtistasPorEstiloId($estilo_id) ?: [];
            $total = count($all);
            $slice = array_slice($all, $offset, $perPage);
        } elseif ($seed !== null && !$estado && !$cidade && !$estilo) {
            // consulta determinística por seed
            $sql = "SELECT artistas.id, artistas.nome, artistas.cidade, artistas.estado,
                    (
                        SELECT foto_artista.url_imagem
                        FROM foto_artista
                        WHERE foto_artista.id_artista = artistas.id
                        ORDER BY foto_artista.id ASC
                        LIMIT 1
                    ) AS url_imagem,
                    (
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
            $slice = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM artistas');
            $countStmt->execute();
            $total = intval($countStmt->fetchColumn() ?: 0);
        } else {
            if (!$estado && !$cidade && !$estilo) {
                $all = $artistaServ->buscarArtistasAleatorios(100) ?: [];
            } else {
                $all = $artistaServ->buscarArtistasPorFiltros($estado, $cidade, $estilo) ?: [];
            }

            $total = count($all);
            $slice = array_slice($all, $offset, $perPage);
        }

        $anchors = [];
        foreach ($slice as $artista) {
            $id = intval($artista['id']);
            $img = htmlspecialchars($artista['url_imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($artista['nome'], ENT_QUOTES, 'UTF-8');
            $anchor = '';
            $anchor .= '<a href="artista.php?artista=' . $id . '" class="caixa_banda">';
            $anchor .= '<img src="img/artistas/' . $id . '/fotos_artistas/' . $img . '" alt="' . $nome . '" />';
            $anchor .= '<div class="texto_banda_overlay">';
            $anchor .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $anchor .= '<h4 class="estilo_musical_banda">' . htmlspecialchars($artista['estilos_musicais']) . '</h4>';
            $anchor .= '</div></a>';
            $anchors[] = $anchor;
        }

        // manter o layout padrão de artistas (3 por linha)
        $rowSize = 3;
        $chunks = array_chunk($anchors, $rowSize);
        foreach ($chunks as $chunk) {
            $html .= '<div class="linha_cards">' . implode('', $chunk) . '</div>';
        }

        $hasMore = ($offset + count($slice)) < $total;

    } elseif ($source === 'estilos') {
        $offset = ($page - 1) * $perPage;
        $pdo = Conecta::getConexao();
        if ($seed !== null && !$estado && !$cidade && !$estilo) {
            $sql = "SELECT id, nome, imagem FROM estilo_musical ORDER BY RAND(:seed) LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':seed', $seed, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $slice = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM estilo_musical');
            $countStmt->execute();
            $total = intval($countStmt->fetchColumn() ?: 0);
        } else {
            if (!$estado && !$cidade && !$estilo) {
                $all = $estiloServ->buscarEstilosAleatorios(100) ?: [];
            } else {
                $all = $estiloServ->buscarEstilosPorFiltros($estado, $cidade, $estilo) ?: [];
            }

            $total = count($all);
            $slice = array_slice($all, $offset, $perPage);
        }

        $anchors = [];
        foreach ($slice as $est) {
            $id = intval($est['id']);
            $img = htmlspecialchars($est['imagem'] ?? '', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($est['nome'], ENT_QUOTES, 'UTF-8');
            $anchor = '';
            $anchor .= '<a href="encontre_artistas.php?estilo_id=' . $id . '&estilo=' . urlencode($est['nome']) . '" class="caixa_banda">';
            $anchor .= '<img src="img/estilos_musicais/' . $img . '" alt="' . $nome . '" />';
            $anchor .= '<div class="texto_banda_overlay">';
            $anchor .= '<h3 class="titulo_banda">' . $nome . '</h3>';
            $anchor .= '</div></a>';
            $anchors[] = $anchor;
        }

        // estilos usam layout 3 por linha e classe adicional 'estilos' no container
        $rowSize = 3;
        $chunks = array_chunk($anchors, $rowSize);
        foreach ($chunks as $chunk) {
            $html .= '<div class="linha_cards estilos">' . implode('', $chunk) . '</div>';
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
