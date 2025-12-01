<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Artista.php";
require_once "src/Models/IntegranteArtista.php";
require_once "src/Models/FotoArtista.php";
require_once "src/Services/ArtistaServicos.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/AutenticarServico.php";
session_start();
$artistaServico = new ArtistaServicos();

$estilos_musicais = (new EstilosMusicaisServicos())->buscarEstilos();

$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =====================================================
    // Reorganizar os arquivos enviados
    // =====================================================
    $arquivosArtista = Utils::organizarArquivos($_FILES['foto_artista']);
    $arquivosIntegrantes = Utils::organizarArquivos($_FILES['integrante_foto']);
    $estilos_musicais_form = $_POST['estilos'];

    $integrante_instrumento = $_POST['integrante_instrumento'];
    $integrante_nome = $_POST['integrante_nome'];

    $fotos_artistas_validas = true;
    $fotos_integrantes_validas = true;
    $instrumentos_validas = true;
    $nomes_validos = true;
    $estilos_validos = true;

    // =====================================================
    // VALIDAR FOTOS DOS ARTISTAS
    // =====================================================

    if (empty($estilos_musicais_form) || count($estilos_musicais_form) === 0) {
        $estilos_validos = false;
    }
    foreach ($arquivosArtista as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_artistas_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR FOTOS DOS INTEGRANTES
    // =====================================================
    foreach ($arquivosIntegrantes as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_integrantes_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR INSTRUMENTOS DOS INTEGRANTES
    // =====================================================
    foreach ($integrante_instrumento as $instrumento) {
        if (empty(trim($instrumento))) {
            $instrumentos_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR NOMES DOS INTEGRANTES
    // =====================================================
    foreach ($integrante_nome as $nome) {
        if (empty(trim($nome))) {
            $nomes_validos = false;
            break;
        }
    }


    if (
        !empty($_POST['nome']) &&
        !empty($_POST['estado']) &&
        !empty($_POST['cidade']) &&
        !empty($_POST['cache_artista']) &&
        !empty($_POST['whatsapp']) &&
        !empty($_POST['instagram']) &&
        !empty($_POST['contato']) &&
        !empty($_POST['descricao']) && $instrumentos_validas && $nomes_validos && $fotos_artistas_validas && $fotos_integrantes_validas && $estilos_validos
    ) {
        echo "chegou aqui";
        $nome       = Utils::sanitizar($_POST['nome']);
        $estado     = Utils::sanitizar($_POST['estado']);
        $cidade     = Utils::sanitizar($_POST['cidade']);
        $cache      = Utils::sanitizar($_POST['cache_artista'], "real");
        $whatsapp   = Utils::sanitizar($_POST['whatsapp']);
        $instagram  = Utils::sanitizar($_POST['instagram']);
        $contato    = Utils::sanitizar($_POST['contato']);
        $descricao  = Utils::sanitizar($_POST['descricao']);

        

        $artista = new Artista(
            null,
            $nome,
            $descricao,
            $estado,
            $cidade,
            $cache,
            $whatsapp,
            $instagram,
            $contato,
            $_SESSION['id']
        );


        $id_inserido = $artistaServico->inserirArtista($artista);

        if (!$id_inserido) {
            $erro = "Não inserido";
        } else {
            "id: insere $id_inserido";
        }

        foreach($estilos_musicais_form as $estilo){
            $estiloInserir = Utils::sanitizar($estilo, 'inteiro');

            $artistaServico->inserirEstilo($id_inserido, $estilo);
        }

        $artistaServico->criarPastasUpload($id_inserido);

        for ($i = 0; $i < count($arquivosArtista); $i++) {
            $fotoArtista = new FotoArtista(null, $arquivosArtista[$i]["name"], $id_inserido);
            $artistaServico->inserirFotoArtista($fotoArtista);
            Utils::salvarArquivo($arquivosArtista[$i], "img/artistas/$id_inserido/fotos_artistas");
        }

        for ($i = 0; $i < count($integrante_instrumento); $i++) {
            $integranteArtista = new IntegranteArtista(null, $integrante_nome[$i], $integrante_instrumento[$i], $arquivosIntegrantes[$i]["name"], $id_inserido);
            $artistaServico->inserirIntegrante($integranteArtista);
            Utils::salvarArquivo($arquivosIntegrantes[$i], "img/artistas/$id_inserido/fotos_integrantes");
        }

        Utils::redirecionarPara("minha_conta.php");
    } else {
        echo "Tem bagulho não preenchido";
        $erro = "Preencha todos os campos";
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Artista</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/perfil_artista.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <section id="sessao-cadastro">
        <h2>Faça seu perfil de <span>Artista</span></h2>
        <?php if ($erro) {
            echo "<p>$erro</p>";
        }  ?>
        <form id="form_artista" method="post" enctype="multipart/form-data">
            <div id="container-inputs">
                <div>
                    <label for="nome">nome:</label>
                    <input type="text" id="nome" name="nome"
                        value="<?= $_POST['nome'] ?? ''; ?>" />
                </div>


                <div>
                    <label for="estado">estado:</label>
                    <input type="text" id="estado" name="estado" maxlength="2"
                        value="<?= $_POST['estado'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="cidade">cidade:</label>
                    <input type="text" id="cidade" name="cidade"
                        value="<?= $_POST['cidade'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="cache_artista">cache do artista (R$):</label>
                    <input type="number" step="0.01" id="cache_artista" name="cache_artista"
                        value="<?= $_POST['cache_artista'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="whatsapp">whatsapp:</label>
                    <input type="text" id="whatsapp" name="whatsapp"
                        value="<?= $_POST['whatsapp'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="instagram">instagram:</label>
                    <input type="text" id="instagram" name="instagram"
                        value="<?= $_POST['instagram'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="contato">contato alternativo:</label>
                    <input type="text" id="contato" name="contato"
                        value="<?= $_POST['contato'] ?? ''; ?>" />
                </div>
                <div>
                    <label>Estilos musicais:</label>

                    <div class="checkbox-group" id="estilos_musicais">
                        <?php foreach($estilos_musicais as $estilo_musical){ ?>
                            <label><input type="checkbox" name="estilos[]" value="<?= $estilo_musical['id'] ?>"
                                <?= (!empty($_POST['estilos']) && in_array($estilo_musical['id'], $_POST['estilos'])) ? 'checked' : '' ?>>
                            <?= $estilo_musical['nome'] ?>
                        </label>
                        <?php }?>
                        

                    </div>
                </div>
                <div>
                    <label for="descricao">descrição:</label>
                    <textarea id="descricao" name="descricao"><?= $_POST['descricao'] ?? '' ?></textarea>
                </div>

                <div id="container-integrantes">
                    <label >Integrantes: </label>
                    <div id="integrantes-container"></div>

                    <button type="button" id="btn-add">ADICIONAR INTEGRANTE +</button>
                </div>

                <button id="uploadButton" onclick="fileInput.click()" type="button" class="upBtn">Adicionar foto</button>
                <input type="file" id="fileInput" accept="image/*" multiple style="display: none;" name="foto_artista[]">
                <div id="previewContainer"></div>
            </div>

            <input type="submit" value="Continuar">
        </form>
    </section>
    <script src="js/perfil_artista.js"></script>
    </body>

</html>