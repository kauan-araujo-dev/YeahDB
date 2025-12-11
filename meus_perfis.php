<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$artistaServico = new ArtistaServicos();
$artistas = $artistaServico->buscarArtistasUsuario($_SESSION['id']);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA</title>

    <link rel="stylesheet" href="css/meus_perfis.css">
    <?php require_once "includes/cabecalho.php"; ?>
</head>

<body>

<main class="conteudo-principal">

    <section id="secao_bandas">

        <?php if (!empty($artistas)) { ?>

            <h2 class="titulo_secao" id="titulo_artistas">MEUS <span>PERFIS</span></h2>

            <div class="linha_cards">

                <?php
                foreach ($artistas as $artista):

                    // BUSCAR PRIMEIRA IMAGEM REAL DO ARTISTA
                    $pdo = Conecta::getConexao();
                    $stmt = $pdo->prepare("
                        SELECT url_imagem 
                        FROM foto_artista 
                        WHERE id_artista = :id
                        ORDER BY id ASC 
                        LIMIT 1
                    ");
                    $stmt->execute([":id" => $artista['id']]);
                    $img = $stmt->fetchColumn();

                    // Monta caminho final
                    $caminhoImagem = $img 
                        ? "img/artistas/{$artista['id']}/fotos_artistas/{$img}" 
                        : "img/sem-imagem.png";
                ?>

                <div class="caixa_banda">

                    <img src="<?= $caminhoImagem ?>" 
                         alt="<?= htmlspecialchars($artista['nome']) ?>" />

                    <div class="texto_banda_overlay">

                        <h3 class="titulo_banda"><?= htmlspecialchars($artista['nome']) ?></h3>

                        <div class="botoes_banda">
                            <a href="excluir_artista.php?id=<?= intval($artista['id']) ?>" 
                               class="banda_excluir"
                               onclick="return confirm('Tem certeza que deseja excluir este perfil?')">
                                EXCLUIR
                            </a>
                        </div>
                    </div>

                </div>

                <?php endforeach; ?>

            </div>

        <?php } else { ?>

            <h2 class="titulo_secao" id="titulo_artistas">Nenhum Perfil Encontrado.</h2>

        <?php } ?>

    </section>

</main>

<?php require_once("includes/rodape.php"); ?>

</body>
</html>
