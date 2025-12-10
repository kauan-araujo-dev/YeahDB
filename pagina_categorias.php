<?php
require_once "src/Database/Conecta.php";
require_once "src/Services/EstilosMusicaisServicos.php";

$estilosMusicaisServicos = new EstilosMusicaisServicos();

// Buscar todos os estilos musicais
$estilos = $estilosMusicaisServicos->buscarEstilosComLimite() ?: [];

// Determina qual aba está ativa via query string
$abaAtiva = isset($_GET['tipo']) && in_array($_GET['tipo'], ['artistas','eventos']) ? $_GET['tipo'] : 'artistas';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YeahDB - Categorias Musicais</title>
    <link rel="stylesheet" href="css/pagina_categorias.css">
    <style>
        .abas { display:flex; margin-bottom:20px; }
        .aba { flex:1; text-align:center; padding:10px; cursor:pointer; border-bottom: 2px solid transparent; font-weight:bold; }
        .aba.ativa { border-bottom: 2px solid #04A777; color:#04A777; }
        .linha_cards { display:flex; flex-wrap:wrap; gap:20px; }
        .caixa_banda { display:block; width:30%; }
        .caixa_banda img { width:100%; border-radius:8px; }
    </style>
    <?php require_once "includes/cabecalho.php"; ?>
</head>
<body>

<section id="secao_bandas">
    <h2 id="titulo_evento">
        ESCOLHA UMA <span style="color: #04A777;">CATEGORIA MUSICAL!</span>
    </h2>

    <!-- Abas -->
    <div class="abas">
        <div class="aba <?= $abaAtiva === 'artistas' ? 'ativa' : '' ?>" onclick="mudarAba('artistas')">
            ARTISTAS
        </div>
        <div class="aba <?= $abaAtiva === 'eventos' ? 'ativa' : '' ?>" onclick="mudarAba('eventos')">
            EVENTOS
        </div>
    </div>

    <!-- Categorias -->
    <?php if (!empty($estilos)) : ?>
        <?php $rows = array_chunk($estilos, 3); ?>
        <?php foreach ($rows as $row) : ?>
            <div class="linha_cards estilos">
                <?php foreach ($row as $estilo) : 
                    $id = intval($estilo['id']);
                    $img = htmlspecialchars($estilo['imagem'] ?? 'sem-imagem.png');
                    $nome = htmlspecialchars($estilo['nome']);
                ?>
                    <a href="<?= $abaAtiva === 'eventos' ? 'pagina_eventos.php' : 'pagina_artistas.php' ?>?estilo=<?= urlencode($nome) ?>" class="caixa_banda" data-id="<?= $id ?>">
                        <img src="img/estilos_musicais/<?= $img ?>" alt="<?= $nome ?>" />
                        <p style="text-align:center;margin-top:5px;"><?= $nome ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma categoria musical encontrada.</p>
    <?php endif; ?>
</section>

<script>
function mudarAba(tipo) {
    const url = new URL(window.location.href);
    url.searchParams.set('tipo', tipo);
    window.location.href = url.toString();
}
</script>

<?php require_once "includes/rodape.php"; ?>
</body>
</html>
