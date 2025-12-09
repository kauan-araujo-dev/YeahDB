<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Services/UsuarioServicos.php";
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
    $arquivosParticipantes = !empty($_FILES['participante_foto']) ? Utils::organizarArquivos($_FILES['participante_foto']) :  null;

    $participantes_estilo_musical = $_POST['participantes_estilo_musical'] ?? null;
    $participante_nome = $_POST['participante_nome'] ?? null;
    $participante_codigo = $_POST['participante_codigo'];
    $codigo_participante = false;
    $id_participante = null;




    $estilos_musicais_form = $_POST['estilos'];
    $participante_validos = true;
    $nomes_validos = true;
    $estilos_validos = true;

    // =====================================================
    // VALIDAR FOTOS DOS ARTISTAS
    // =====================================================
    for ($i = 0; $i < count($participante_codigo); $i++) {
        if (empty($participante_codigo[$i])) {
            echo "n foi preenchido";
            if (empty(trim($participantes_estilo_musical[$i]))) {
                $participante_validos = false;
                break;
            }
            if (!Utils::validarArquivo($arquivosParticipantes[$i])) {
                $participante_validos = false;
                break;
            }
            if (empty(trim($participante_nome[$i]))) {
                $participante_validos = false;
                break;
            }
        } else {
            $codigo = Utils::sanitizar($participante_codigo[$i]);
            var_dump($codigo);
            $id_participante = (new UsuarioServicos())->buscarPorArtistaCodigo($codigo);

            if (empty($id_participante)) {
                $participante_validos = false;
            } else {
                $id_participante = $id_participante['id'];
            }
        }
    }
    if (empty($estilos_musicais_form) || count($estilos_musicais_form) === 0) {
        $estilos_validos = false;
    }
    foreach ($arquivosEvento as $arquivo) {
        if (!Utils::validarArquivo($arquivo)) {
            $fotos_eventos_validas = false;
            break;
        }
    }
    if (empty($_POST['nome'])) {
        echo "O campo nome não foi preenchido completamente.";
    }

    if (empty($_POST['estado'])) {
        echo "O campo estado não foi preenchido completamente.";
    }

    if (empty($_POST['cidade'])) {
        echo "O campo cidade não foi preenchido completamente.";
    }

    if (empty($_POST['endereco'])) {
        echo "O campo endereço não foi preenchido completamente.";
    }

    if (empty($_POST['dia'])) {
        echo "O campo dia não foi preenchido completamente.";
    }

    if (empty($_POST['horario'])) {
        echo "O campo horário não foi preenchido completamente.";
    }

    if (empty($_POST['instagram'])) {
        echo "O campo instagram não foi preenchido completamente.";
    }

    if (empty($_POST['contato'])) {
        echo "O campo contato não foi preenchido completamente.";
    }

    if (empty($_POST['descricao'])) {
        echo "O campo descrição não foi preenchido completamente.";
    }

    if (empty($_POST['estilos'])) {
        echo "O campo estilos não foi preenchido completamente.";
    }

    if (!$participante_validos) {
        echo "Os dados dos participantes não foram preenchidos completamente.";
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
        $participante_validos
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
        $eventoServico->criarPastasUpload($id_inserido);
        for ($i = 0; $i < count($arquivosEvento); $i++) {
            $fotoEvento = new FotoEvento(null, $arquivosEvento[$i]["name"], $id_inserido);
            $eventoServico->inserirFotoEvento($fotoEvento);
            Utils::salvarArquivo($arquivosEvento[$i], "img/eventos/$id_inserido/fotos_eventos");
        }
        foreach ($estilos_musicais_form as $estilo) {
                $estiloInserir = Utils::sanitizar($estilo, 'inteiro');

                $eventoServico->inserirEstilo($id_inserido, $estilo);
            }

           

        if (!$id_inserido) {
            $erro = "Não inserido";
        } else {
            "id: insere $id_inserido";
        }

        if (!empty($id_participante)) {
            $eventoServico->inserirArtistaEvento($id_participante, $id_inserido);
        } else {
            


            for ($i = 0; $i < count($participantes_estilo_musical); $i++) {
                $participanteEvento = new IntegranteEvento(null, $participante_nome[$i], $participantes_estilo_musical[$i], $arquivosParticipantes[$i]["name"], $id_inserido);
                $eventoServico->inserirIntegrante($participanteEvento);
                
                Utils::salvarArquivo($arquivosParticipantes[$i], "img/eventos/$id_inserido/fotos_participantes");
            }
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
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ============================
            // ARRAYS DE VALORES ALEATÓRIOS
            // ============================
            const nomes = [
                "Festa Urbana", "Noite do Som", "Festival Neon",
                "Vibe Sunset", "Balada Mix", "Sons da Cidade"
            ];

            const cidades = [
                "São Paulo", "Rio de Janeiro", "Curitiba",
                "Belo Horizonte", "Salvador", "Porto Alegre"
            ];

            const estados = ["SP", "RJ", "PR", "MG", "BA", "RS"];

            const enderecos = [
                "Rua das Flores, 102", "Avenida Central, 500",
                "Praça da Música, 88", "Rua Verde, 230",
                "Avenida Horizonte, 900"
            ];

            const instagrams = [
                "@festaurbana", "@noitedosom", "@vibesunset",
                "@baladamix", "@sons_da_cidade"
            ];

            const contatos = [
                "11988887777", "21977776666", "41999995555",
                "31944443333"
            ];

            const descricoes = [
                "Evento imperdível com artistas renomados!",
                "Uma noite inesquecível com muito som!",
                "Festival ao ar livre com várias atrações!",
                "Balada exclusiva com lineup especial!",
                "Venha curtir o melhor da música!"
            ];

            const links_compra = [
                "https://ingressos.com/evento123",
                "https://tickets.com/festival",
                "https://comprar.com/balada",
                ""
            ];

            // gera número aleatório
            const rand = arr => arr[Math.floor(Math.random() * arr.length)];

            // ============================
            // PREENCHENDO O FORMULÁRIO
            // ============================

            document.getElementById("nome").value = rand(nomes);
            document.getElementById("estado").value = rand(estados);
            document.getElementById("cidade").value = rand(cidades);
            document.getElementById("endereco").value = rand(enderecos);

            // data futura
            const hoje = new Date();
            hoje.setDate(hoje.getDate() + Math.floor(Math.random() * 30));
            document.getElementById("dia").value = hoje.toISOString().split("T")[0];

            // horário aleatório
            const hora = String(Math.floor(Math.random() * 23)).padStart(2, "0");
            const minuto = String(Math.floor(Math.random() * 59)).padStart(2, "0");
            document.getElementById("horario").value = `${hora}:${minuto}`;

            document.getElementById("instagram").value = rand(instagrams);
            document.getElementById("contato").value = rand(contatos);
            document.getElementById("link_compra").value = rand(links_compra);
            document.getElementById("descricao").value = rand(descricoes);

            // ============================
            // SELECIONAR ESTILOS AUTOMATICAMENTE
            // ============================
            const checkboxes = document.querySelectorAll("#estilos_musicais input[type='checkbox']");
            checkboxes.forEach(chk => {
                // probabilisticamente marca ≈ metade
                chk.checked = Math.random() > 0.5;
            });

            // PARTICIPANTES E FOTOS — você preenche manualmente :)
        });
    </script>
    </body>

</html>