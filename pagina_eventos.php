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

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // função para abrir/fechar selects e reset
            function updateSelects() {
                document.querySelectorAll('.custom-select').forEach(function(cs) {
                    const header = cs.querySelector('.select-header');
                    const list = cs.querySelector('.select-list');
                    const resetBtn = cs.querySelector('.reset-select');
                    let open = false;

                    header.addEventListener('click', function() {
                        list.style.display = open ? 'none' : 'block';
                        open = !open;
                    });
                    document.addEventListener('click', function(e) {
                        if (!cs.contains(e.target)) {
                            list.style.display = 'none';
                            open = false;
                        }
                    });

                    list.querySelectorAll('li').forEach(function(li) {
                        li.addEventListener('click', function() {
                            cs.querySelector('.selected-option').textContent = li.dataset.value;

                            const estado = document.querySelector('.custom-select[data-field="estado"] .selected-option').textContent;
                            const cidade = document.querySelector('.custom-select[data-field="cidade"] .selected-option').textContent;
                            const estilo = document.querySelector('.custom-select[data-field="estilo"] .selected-option').textContent;
                            const params = new URLSearchParams();
                            if (estado && estado !== 'ESTADO') params.set('estado', estado);
                            if (cidade && cidade !== 'CIDADE') params.set('cidade', cidade);
                            if (estilo && estilo !== 'ESTILO MUSICAL') params.set('estilo', estilo);
                            params.set('page', '1');
                            window.location.href = window.location.pathname + '?' + params.toString();
                        });
                    });

                    // Reset button
                    resetBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const field = cs.dataset.field;
                        const estadoSpan = document.querySelector('.custom-select[data-field="estado"] .selected-option');
                        const cidadeSpan = document.querySelector('.custom-select[data-field="cidade"] .selected-option');
                        const estiloSpan = document.querySelector('.custom-select[data-field="estilo"] .selected-option');

                        if (field === 'estado') {
                            estadoSpan.textContent = 'ESTADO';
                            cidadeSpan.textContent = 'CIDADE'; // limpa cidade junto
                        } else if (field === 'cidade') cidadeSpan.textContent = 'CIDADE';
                        else if (field === 'estilo') estiloSpan.textContent = 'ESTILO MUSICAL';

                        const params = new URLSearchParams();
                        const estadoVal = estadoSpan.textContent;
                        if (estadoVal && estadoVal !== 'ESTADO') params.set('estado', estadoVal);
                        const cidadeVal = cidadeSpan.textContent;
                        if (cidadeVal && cidadeVal !== 'CIDADE') params.set('cidade', cidadeVal);
                        const estiloVal = estiloSpan.textContent;
                        if (estiloVal && estiloVal !== 'ESTILO MUSICAL') params.set('estilo', estiloVal);
                        params.set('page', '1');
                        window.location.href = window.location.pathname + '?' + params.toString();
                    });
                });
            }

            updateSelects();

            // Mostrar mais
            document.querySelectorAll('a.mostrar-mais').forEach(function(a) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(a.href);
                    url.searchParams.set('ajax', '1');
                    a.classList.add('loading');
                    a.textContent = 'Carregando...';
                    fetch(url.toString(), {
                        credentials: 'same-origin'
                    }).then(r => r.json()).then(json => {
                        const container = document.querySelector('.linha_cards.linha-eventos');
                        container.insertAdjacentHTML('beforeend', json.html);
                        if (json.hasMore) {
                            a.dataset.page = json.nextPage;
                            a.textContent = 'Mostrar mais';
                            a.classList.remove('loading');
                        } else a.remove();
                    }).catch(() => {
                        a.textContent = 'Erro';
                        a.classList.remove('loading');
                    });
                });
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
  const selects = document.querySelectorAll('.custom-select');

  selects.forEach(select => {
    const header = select.querySelector('.select-header');
    const list = select.querySelector('.select-list');
    const resetBtn = select.querySelector('.reset-select');

    header.tabIndex = 0;

    function closeAllExcept(except) {
      selects.forEach(s => {
        if (s !== except) s.classList.remove('open');
      });
    }

    function openSelect() {
      closeAllExcept(select);
      select.classList.toggle('open');
    }

    header.addEventListener('click', openSelect);

    // resetar filtros
    resetBtn.addEventListener('click', e => {
      e.stopPropagation();
      const field = select.dataset.field;

      // reset texto
      if (field === 'estado') {
        select.querySelector('.selected-option').textContent = 'ESTADO';
        // reset cidade também
        const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"]');
        if (cidadeSelect) cidadeSelect.querySelector('.selected-option').textContent = 'CIDADE';
      } else if (field === 'cidade') {
        select.querySelector('.selected-option').textContent = 'CIDADE';
      } else if (field === 'estilo') {
        select.querySelector('.selected-option').textContent = 'ESTILO MUSICAL';
      }

      // recarregar página sem filtros
      const url = new URL(window.location.href);
      url.searchParams.delete('estado');
      url.searchParams.delete('cidade');
      url.searchParams.delete('estilo');
      url.searchParams.set('page', '1');
      window.location.href = url.toString();
    });

    // clicar fora fecha select
    document.addEventListener('click', e => {
      if (!select.contains(e.target)) select.classList.remove('open');
    });

    // seleção de item
    list.querySelectorAll('li').forEach(li => {
      li.addEventListener('click', () => {
        const field = select.dataset.field;
        select.querySelector('.selected-option').textContent = li.textContent.trim();

        // reset cidade se estado mudou
        if (field === 'estado') {
          const cidadeSelect = document.querySelector('.custom-select[data-field="cidade"]');
          if (cidadeSelect) cidadeSelect.querySelector('.selected-option').textContent = 'CIDADE';
        }

        // reload com filtros
        const estado = document.querySelector('.custom-select[data-field="estado"] .selected-option').textContent;
        const cidade = document.querySelector('.custom-select[data-field="cidade"] .selected-option').textContent;
        const estilo = document.querySelector('.custom-select[data-field="estilo"] .selected-option').textContent;

        const url = new URL(window.location.href);
        url.searchParams.set('page', '1');

        if (estado.toLowerCase() !== 'estado') url.searchParams.set('estado', estado);
        else url.searchParams.delete('estado');

        if (cidade.toLowerCase() !== 'cidade') url.searchParams.set('cidade', cidade);
        else url.searchParams.delete('cidade');

        if (estilo.toLowerCase() !== 'estilo musical') url.searchParams.set('estilo', estilo);
        else url.searchParams.delete('estilo');

        window.location.href = url.toString();
      });
    });
  });
});

    </script>

    <?php require_once "includes/rodape.php"; ?>
</body>

</html>