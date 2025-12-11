<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Usuario.php";
require_once "src/Services/UsuarioServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::exigirLogin();

$usuarioServicos = new UsuarioServicos();
$msg = null;

// Buscar dados atuais do usuário logado (para edição)
$dadosUsuario = $usuarioServicos->buscarPorId($_SESSION['id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validar campos obrigatórios
    $campos = ["nome", "data_nascimento", "cep", "estado", "cidade", "rua", "numero", "email"];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $msg = "Preencha todos os campos.";
            break;
        }
    }

    // Validar email
    if (!$msg && $_POST['email'] !== $_POST['confirmar-email']) {
        $msg = "Os emails não coincidem.";
    }

    // Validar senha (somente se o usuário quiser alterar)

    // Quando não houver erro:
    if (!$msg) {

        $usuario = new Usuario(
            Utils::sanitizar($_POST['nome']),
            Utils::sanitizar($_POST['data_nascimento']),
            Utils::sanitizar($_POST['cep']),
            Utils::sanitizar($_POST['estado']),
            Utils::sanitizar($_POST['cidade']),
            Utils::sanitizar($_POST['rua']),
            Utils::sanitizar($_POST['numero']),
            Utils::sanitizar($_POST['email'], "email"),
            $dadosUsuario['senha'],
            $_SESSION['id']
        );

        $usuarioServicos->atualizarUsuario($usuario);

        Utils::redirecionarPara("minha_conta.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro</title>
  <link rel="stylesheet" href="css/forms.css" />
  <link rel="stylesheet" href="css/editar_conta.css" />
  <?php require_once "includes/cabecalho.php" ?>
  <section>
  <h2>Editar <span>Cadastro</span></h2>

  <?php if ($msg): ?>
    <p><?= $msg ?></p>
  <?php endif; ?>

  <form method="post" id="form-editar">

    <div id="container-inputs">

      <div>
        <label for="nome">nome:</label>
        <input type="text" id="nome" name="nome"
          value="<?= $dadosUsuario['nome'] ?>" />
      </div>

      <div>
        <label for="data_nascimento">data de nascimento:</label>
        <input type="date" id="data_nascimento" name="data_nascimento"
          value="<?= $dadosUsuario['data_nascimento'] ?>" />
      </div>

      <div>
        <label for="cep">cep:</label>
        <input type="text" id="cep" name="cep" maxlength="8"
          value="<?= $dadosUsuario['cep'] ?>" />
      </div>

      <div>
        <label for="estado">estado:</label>
        <input type="text" id="estado" name="estado" maxlength="2"
          value="<?= $dadosUsuario['estado'] ?>" />
      </div>

      <div>
        <label for="cidade">cidade:</label>
        <input type="text" id="cidade" name="cidade"
          value="<?= $dadosUsuario['cidade'] ?>" />
      </div>

      <div>
        <label for="rua">rua:</label>
        <input type="text" id="rua" name="rua"
          value="<?= $dadosUsuario['rua'] ?>" />
      </div>

      <div>
        <label for="numero">número:</label>
        <input type="text" id="numero" name="numero"
          value="<?= $dadosUsuario['numero'] ?>" />
      </div>

      <div>
        <label for="email">e-mail:</label>
        <input type="email" id="email" name="email"
          value="<?= $dadosUsuario['email'] ?>" />
      </div>

      <div>
        <label for="confirmar-email">confirmar e-mail:</label>
        <input type="email" id="confirmar-email" name="confirmar-email"
          value="<?= $dadosUsuario['email'] ?>" />
      </div>
      <input type="submit" value="Salvar alterações">

    </div>


  </form>

</section>
    <?php require_once "includes/rodape.php" ?>
  <script src="js/script.js"></script>

  </body>

</html>