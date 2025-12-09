<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/ArtistaServicos.php";

$servico = new ArtistaServicos();

// ---------------------------
// FILTROS
// ---------------------------
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
$cidade = isset($_GET['cidade']) && $_GET['cidade'] !== '' ? trim($_GET['cidade']) : null;
$estilo  = isset($_GET['estilo'])  && $_GET['estilo']  !== '' ? trim($_GET['estilo']) : null;

// ---------------------------
// PAGINAÇÃO
// ---------------------------
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$porPagina = 6;

// ---------------------------
// BUSCA PRINCIPAL
// ---------------------------
$artistas = $servico->buscarArtistasFiltrados($estado, $cidade, $estilo, $page, $porPagina);
$total = $servico->contarArtistasFiltrados($estado, $cidade, $estilo);

$temMais = ($page * $porPagina) < $total;

// ---------------------------
// LISTAS PARA OS SELECTS
// ---------------------------
$estados = $servico->buscarEstadosDisponiveis();
$cidades = $servico->buscarCidadesDisponiveis();
$estilos = $servico->buscarEstilosDisponiveis();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahDB - Encontre Artistas</title>

    <link rel="stylesheet" href="css/pagina_artistas_menu.css">
    <?php require_once "includes/cabecalho.php"; ?>
</head>

<body>

<section id="secao_bandas">

    <h2 class="titulo_secao">
        ENCONTRE UM <span style="color:#FB8B24;">ARTISTA!</span>
    </h2>

    <!-- FILTROS -->
    <nav class="nav-selects">

        <!-- ESTADO -->
        <div class="custom-select" data-field="estado">
            <div class="select-header">
                <span class="selected-option"><?= $estado ?: "ESTADO" ?></span>
                <button type="button" class="reset-select">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php if (!empty($estados)) : ?>
                    <?php foreach ($estados as $uf) : ?>
                        <li><?= htmlspecialchars($uf) ?></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>Nenhum estado encontrado</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- CIDADE -->
        <div class="custom-select" data-field="cidade">
            <div class="select-header">
                <span class="selected-option"><?= $cidade ?: "CIDADE" ?></span>
                <button type="button" class="reset-select">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php if (!empty($cidades)) : ?>
                    <?php foreach ($cidades as $cid) : ?>
                        <li><?= htmlspecialchars($cid) ?></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>Nenhuma cidade encontrada</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- ESTILO MUSICAL -->
        <div class="custom-select" data-field="estilo">
            <div class="select-header">
                <span class="selected-option"><?= $estilo ?: "ESTILO MUSICAL" ?></span>
                <button type="button" class="reset-select">✕</button>
                <i class="arrow"></i>
            </div>

            <ul class="select-list">
                <?php if (!empty($estilos)) : ?>
                    <?php foreach ($estilos as $nome) : ?>
                        <li><?= htmlspecialchars($nome) ?></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>Nenhum estilo encontrado</li>
                <?php endif; ?>
            </ul>
        </div>

    </nav>

    <!-- LISTAGEM DE ARTISTAS -->
    <div class="linha_cards">

        <?php if (empty($artistas)) : ?>

            <p style="width:100%; text-align:center; margin-top:20px;">
                Nenhum artista encontrado para os filtros selecionados.
            </p>

        <?php else : ?>

            <?php foreach ($artistas as $artista) : ?>

                <?php
                $id = intval($artista['id']);
                $nome = htmlspecialchars($artista['nome']);

                // pega somente a primeira imagem
                $imagens = explode("||", $artista['imagens'] ?? '');
                $imgPrincipal = $imagens[0] ?? "sem-imagem.png";

                $caminhoImagem = "img/artistas/{$id}/fotos_artistas/{$imgPrincipal}";
                if (!file_exists($caminhoImagem)) {
                    $caminhoImagem = "img/sem-imagem.png";
                }
                ?>

                <a href="artista.php?artista=<?= $id ?>" class="caixa_banda">

                    <img src="<?= $caminhoImagem ?>" alt="<?= $nome ?>">

                    <div class="texto_banda_overlay">
                        <h3 class="titulo_banda"><?= $nome ?></h3>
                        <h4 class="estilo_musical_banda">
                            <?= htmlspecialchars($artista['estilos_musicais']) ?>
                        </h4>
                    </div>

                </a>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <!-- BOTÃO "MOSTRAR MAIS" -->
    <?php if ($temMais) : ?>
        <?php
        $qs = $_GET;
        $qs['page'] = $page + 1;
        $proximoLink = 'encontre_artistas.php?' . http_build_query($qs);
        ?>
        <div class="linha_cards">
            <a href="<?= $proximoLink ?>" class="mostrar-mais">MOSTRAR MAIS</a>
        </div>
    <?php endif; ?>

</section>

<script src="js/encontre-artistas-menu.js"></script>
<script src="js/select-dependent.js"></script>
<script src="js/gallery.js"></script>
<script src="js/load-more.js"></script>

<?php require_once "includes/rodape.php"; ?>

</body>
</html>
