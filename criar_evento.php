<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Eventos.php";
require_once "src/Models/IntegranteEvento.php";
require_once "src/Models/FotoEvento.php";
require_once "src/Services/EventoServicos.php";
require_once "src/Services/EstilosMusicaisServicos.php";
require_once "src/Services/AutenticarServico.php";
session_start();
$eventoServico = new EventoServicos();

$estilos_musicais = (new EstilosMusicaisServicos())->buscarEstilos();

$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =====================================================
    // Reorganizar os arquivos enviados
    // =====================================================
    Utils::dump($_FILES);
    $arquivosEvento = Utils::organizarArquivos($_FILES['fotos_evento']);
    $arquivosIntegrantes = Utils::organizarArquivos($_FILES['participante_foto']);
    $estilos_musicais_form = $_POST['estilos'];

    $participantes_estilo_musical = $_POST['participantes_estilo_musical'];
    $participante_nome = $_POST['participante_nome'];

    $fotos_eventos_validas = true;
    $fotos_participantes_validas = true;
    $participante_estilo_musicals_validas = true;
    $nomes_validos = true;
    $estilos_validos = true;

    // =====================================================
    // VALIDAR FOTOS DOS ARTISTAS
    // =====================================================

    if (empty($estilos_musicais_form) || count($estilos_musicais_form) === 0) {
        $estilos_validos = false;
    }
    foreach ($arquivosEvento as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_eventos_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR FOTOS DOS INTEGRANTES
    // =====================================================
    foreach ($arquivosIntegrantes as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_participantes_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR INSTRUMENTOS DOS INTEGRANTES
    // =====================================================
    foreach ($participantes_estilo_musical as $participante_estilo_musical) {
        if (empty(trim($participante_estilo_musical))) {
            $participante_estilo_musicals_validas = false;
            break;
        }
    }


    // =====================================================
    // VALIDAR NOMES DOS INTEGRANTES
    // =====================================================
    foreach ($participante_nome as $nome) {
        if (empty(trim($nome))) {
            $nomes_validos = false;
            break;
        }
    }


    if (
        !empty($_POST['nome']) &&
        !empty($_POST['estado']) &&
        !empty($_POST['cidade']) &&
        !empty($_POST['endereco']) &&
        !empty($_POST['dia']) &&
        !empty($_POST['horario']) &&
        !empty($_POST['instagram']) &&
        !empty($_POST['contato']) &&
        !empty($_POST['descricao']) &&
        !empty($_POST['estilos']) &&            
        $fotos_eventos_validas                  
    ) {

        echo "chegou aqui";

       
        $nome       = Utils::sanitizar($_POST['nome']);
        $estado     = Utils::sanitizar($_POST['estado']);
        $cidade     = Utils::sanitizar($_POST['cidade']);
        $endereco   = Utils::sanitizar($_POST['endereco']);
        $dia        = Utils::sanitizar($_POST['dia']);
        $horario    = Utils::sanitizar($_POST['horario']);
        $instagram  = Utils::sanitizar($_POST['instagram']);
        $contato    = Utils::sanitizar($_POST['contato']);
        $link_compra = Utils::sanitizar($_POST['link_compra'] ?? '');
        $descricao  = Utils::sanitizar($_POST['descricao']);

        
        $estilos = $_POST['estilos'];

        
        $evento = new Eventos(
            null,
            $nome,
            $descricao,
            $estado,
            $cidade,
            $endereco,
            $dia,
            $horario,
            $instagram,
            $contato,
            $link_compra,
            $_SESSION['id']
        );


        $id_inserido = $eventoServico->inserirEvento($evento);

        if (!$id_inserido) {
            $erro = "Não inserido";
        } else {
            "id: insere $id_inserido";
        }

        foreach ($estilos_musicais_form as $estilo) {
            $estiloInserir = Utils::sanitizar($estilo, 'inteiro');

            $eventoServico->inserirEstilo($id_inserido, $estilo);
        }

        $eventoServico->criarPastasUpload($id_inserido);

        for ($i = 0; $i < count($arquivosEvento); $i++) {
            $fotoEvento = new FotoEvento(null, $arquivosEvento[$i]["name"], $id_inserido);
            $eventoServico->inserirFotoEvento($fotoEvento);
            Utils::salvarArquivo($arquivosEvento[$i], "img/eventos/$id_inserido/fotos_eventos");
        }

        for ($i = 0; $i < count($participantes_estilo_musical); $i++) {
            $participanteEvento = new IntegranteEvento(null, $participante_nome[$i], $participantes_estilo_musical[$i], $arquivosIntegrantes[$i]["name"], $id_inserido);
            $eventoServico->inserirIntegrante($participanteEvento);
            Utils::salvarArquivo($arquivosIntegrantes[$i], "img/eventos/$id_inserido/fotos_participantes");
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
    <title>Perfil Evento</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/criar_evento.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <section id="sessao-cadastro">
        <h2>CRIE SEU <span>Evento</span></h2>
        <?php if ($erro) {
            echo "<p>$erro</p>";
        }  ?>
        <form id="form_evento" method="post" enctype="multipart/form-data">
            <div id="container-inputs">

                <div>
                    <label for="nome">Nome do evento:</label>
                    <input type="text" id="nome" name="nome"
                        value="<?= $_POST['nome'] ?? ''; ?>" />
                </div>


                <div>
                    <label for="estado">Estado:</label>
                    <input type="text" id="estado" name="estado" maxlength="2"
                        value="<?= $_POST['estado'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="cidade">Cidade:</label>
                    <input type="text" id="cidade" name="cidade"
                        value="<?= $_POST['cidade'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco"
                        value="<?= $_POST['endereco'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="dia">Dia do evento:</label>
                    <input type="date" id="dia" name="dia"
                        value="<?= $_POST['dia'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="horario">Horário:</label>
                    <input type="time" id="horario" name="horario"
                        value="<?= $_POST['horario'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="instagram">Instagram:</label>
                    <input type="text" id="instagram" name="instagram"
                        value="<?= $_POST['instagram'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="contato">Contato:</label>
                    <input type="text" id="contato" name="contato"
                        value="<?= $_POST['contato'] ?? ''; ?>" />
                </div>

                <div>
                    <label for="link_compra">Link de compra (opcional):</label>
                    <input type="text" id="link_compra" name="link_compra"
                        value="<?= $_POST['link_compra'] ?? ''; ?>" />
                </div>
                <div>
                    <label>Estilos musicais:</label>

                    <div class="checkbox-group" id="estilos_musicais">
                        <?php foreach ($estilos_musicais as $estilo_musical) { ?>
                            <label><input type="checkbox" name="estilos[]" value="<?= $estilo_musical['id'] ?>"
                                    <?= (!empty($_POST['estilos']) && in_array($estilo_musical['id'], $_POST['estilos'])) ? 'checked' : '' ?>>
                                <?= $estilo_musical['nome'] ?>
                            </label>
                        <?php } ?>


                    </div>
                </div>
                <div>
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao"><?= $_POST['descricao'] ?? '' ?></textarea>
                </div>

                <!-- 🔥 MANTIDO: participantes -->
                <div id="container-participantes">
                    <label>PARTICIPANTES: </label>
                    <div id="participantes-container"></div>

                    <button type="button" id="btn-add">ADICIONAR INTEGRANTE +</button>
                </div>

                <!-- 🔥 MANTIDO: fotos -->
                <button id="uploadButton" onclick="fileInput.click()" type="button" class="upBtn">Adicionar foto</button>
                <input type="file" id="fileInput" accept="image/*" multiple style="display: none;" name="fotos_evento[]">
                <div id="previewContainer"></div>

            </div>

            <input type="submit" value="Continuar">
        </form>

    </section>
    <script src="js/perfil_evento.js"></script>
    </body>

</html>