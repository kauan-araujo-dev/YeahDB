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
            echo "<p class='erro'>$erro</p>";
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
    <select id="estado" name="estado">
        <option value="">Selecione</option>
    </select>
</div>

<div>
    <label for="cidade">Cidade:</label>
    <select id="cidade" name="cidade">
        <option value="">---</option>
    </select>
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
<button id="uploadButton" onclick="fileInput.click()" type="button" class="upBtn">Adicionar foto do evento(obrigatório)</button>
                <input type="file" id="fileInput" accept="image/*" multiple style="display: none;" name="fotos_evento[]">
                <div id="previewContainer"></div>
                <!-- 🔥 MANTIDO: participantes -->
                <div id="container-participantes">
                    <label>PARTICIPANTES: </label>
                    <div id="participantes-container"></div>

                    <button type="button" id="btn-add">ADICIONAR PARTICIPANTE +</button>
                </div>

                <!-- 🔥 MANTIDO: fotos -->
                

            </div>

            <input type="submit" value="Continuar">
        </form>

    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="js/perfil_evento.js"></script>

    </body>

    <script>
// Carregar estados ao abrir a página
document.addEventListener("DOMContentLoaded", async () => {
    const estadoSelect = document.getElementById("estado");
    const cidadeSelect = document.getElementById("cidade");

    // Buscar estados na API do IBGE
    const res = await fetch("https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome");
    const estados = await res.json();

    estados.forEach(est => {
        const option = document.createElement("option");
        option.value = est.sigla;
        option.textContent = est.nome;
        option.dataset.id = est.id; // Guardar ID do estado (IBGE)
        estadoSelect.appendChild(option);
    });

    // Se já tiver valor salvo (em caso de erro e repost), selecionar
    <?php if (!empty($_POST['estado'])) : ?>
        estadoSelect.value = "<?= $_POST['estado'] ?>";
    <?php endif; ?>

    // Trigger inicial (carregar cidades se o estado já existir no POST)
    if (estadoSelect.value !== "") {
        carregarCidades(estadoSelect, cidadeSelect);
    }
});

// Quando mudar o estado, carregar cidades
document.getElementById("estado").addEventListener("change", (e) => {
    const estadoSelect = e.target;
    const cidadeSelect = document.getElementById("cidade");

    carregarCidades(estadoSelect, cidadeSelect);
});

// Função para carregar cidades pela API
async function carregarCidades(estadoSelect, cidadeSelect) {

    cidadeSelect.innerHTML = "<option>Carregando...</option>";

    const selectedOption = estadoSelect.selectedOptions[0];
    const idEstado = selectedOption.dataset.id;

    if (!idEstado) {
        cidadeSelect.innerHTML = "<option>Selecione o estado primeiro</option>";
        return;
    }

    const res = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${idEstado}/municipios`);
    const cidades = await res.json();

    cidadeSelect.innerHTML = "<option value=''>Selecione</option>";

    cidades.forEach(cidade => {
        const option = document.createElement("option");
        option.value = cidade.nome;
        option.textContent = cidade.nome;
        cidadeSelect.appendChild(option);
    });

    // Restaurar cidade selecionada se existir no POST
    <?php if (!empty($_POST['cidade'])) : ?>
        cidadeSelect.value = "<?= $_POST['cidade'] ?>";
    <?php endif; ?>
}
</script>


</html>