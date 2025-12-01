<?php
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Usuario.php";
require_once "src/Services/UsuarioServicos.php";
require_once "src/Services/AutenticarServico.php";


AutenticarServico::estaLogado();

$condMsg;
$msg = null;
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  session_start();
  session_destroy();
  $condMsg = false;
}
session_start();
$usuarioServicos = new UsuarioServicos();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['voltar'])) {
  $_SESSION['pt2'] = null;
  $msg = false;
}



if (!isset($_SESSION['pt2'])) {

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
      !empty($_POST['nome']) &&
      !empty($_POST['data_nascimento']) &&
      !empty($_POST['cep']) &&
      !empty($_POST['estado']) &&
      !empty($_POST['cidade']) &&
      !empty($_POST['rua']) &&
      !empty($_POST['numero'])
    ) {
      $condMsg = false;
      $_SESSION['nome'] = $_POST['nome'];
      $_SESSION['data_nascimento'] = $_POST['data_nascimento'];
      $_SESSION['cep'] = $_POST['cep'];
      $_SESSION['estado'] = $_POST['estado'];
      $_SESSION['cidade'] = $_POST['cidade'];
      $_SESSION['rua'] = $_POST['rua'];
      $_SESSION['numero'] = $_POST['numero'];
      $_SESSION['pt2'] = true;
    } else {

      

      if (!isset($_POST['voltar'])) {
        if (empty($_POST['nome'])) {
        $_SESSION['nome'] = null;
      }
        if (empty($_POST['data_nascimento'])) {
          $_SESSION['data_nascimento'] = null;
        }

        if (empty($_POST['cep'])) {
          $_SESSION['cep'] = null;
        }

        if (empty($_POST['estado'])) {
          $_SESSION['estado'] = null;
        }

        if (empty($_POST['cidade'])) {
          $_SESSION['cidade'] = null;
        }

        if (empty($_POST['rua'])) {
          $_SESSION['rua'] = null;
        }

        if (empty($_POST['numero'])) {
          $_SESSION['numero'] = null;
        }
      }
      if ($msg !== false) {
        $msg = "Preencha todos os campos 1";
      }
    }
  }
}


if (isset($_SESSION['pt2'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
      !empty($_POST['email']) &&
      !empty($_POST['confirmar-email']) &&
      !empty($_POST['senha']) &&
      !empty($_POST['confirmar-senha'])
    ) {

      if ($_POST['email'] === $_POST['confirmar-email']) {


        if ($_POST['senha'] === $_POST['confirmar-senha']) {

          $nome = Utils::sanitizar($_SESSION['nome']);
          $data_nascimento = Utils::sanitizar($_SESSION['data_nascimento']);
          $cep = Utils::sanitizar($_SESSION['cep']);
          $estado = Utils::sanitizar($_SESSION['estado']);
          $cidade = Utils::sanitizar($_SESSION['cidade']);
          $rua = Utils::sanitizar($_SESSION['rua']);
          $numero = Utils::sanitizar($_SESSION['numero']);
          $email = Utils::sanitizar($_POST['email'], 'email');
          $senha = Utils::codificarSenha($_POST['senha']);

          $usuario = new Usuario($nome, $data_nascimento, $cep, $estado, $cidade, $rua, $numero, $email, $senha, null);

          $usuarioServicos->inserirUsuario($usuario);

          session_destroy();
          Utils::redirecionarPara("index.php");
        } else {
          $msg = "As senha não coincidem";
        }
      } else {
        $msg = "Os emails não coincidem";
      }
    } else if ($msg && $condMsg) {
      $msg = "Preencha todos os campos 2";
      $condMsg = true;
    }
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
  <link rel="stylesheet" href="css/cadastrar.css">
  <?php require_once "includes/cabecalho.php" ?>
  <section>
    <h2>Faça seu <span>Cadastro</span></h2>
    <?php if ($msg) {
      echo "<p>$msg</p>";
    }  ?>
    <?php if (!isset($_SESSION['pt2'])) { ?>
      <form method="post" id="form1" method="post">
        <div id="container-inputs">

          <div>
            <label for="nome">nome:</label>
            <input type="text" id="nome" name="nome"
              value="<?php echo $_SESSION['nome'] ?? ''; ?>" />
          </div>

          <div>
            <label for="data_nascimento">data de nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento"
              value="<?php echo $_SESSION['data_nascimento'] ?? ''; ?>" />
          </div>

          <div>
            <label for="cep">cep:</label>
            <input type="text" id="cep" name="cep" maxlength="8"
              value="<?php echo $_SESSION['cep'] ?? ''; ?>" />
          </div>

          <div>
            <label for="estado">estado:</label>
            <input type="text" id="estado" name="estado" maxlength="2"
              value="<?php echo $_SESSION['estado'] ?? ''; ?>" />
          </div>

          <div>
            <label for="cidade">cidade:</label>
            <input type="text" id="cidade" name="cidade"
              value="<?php echo $_SESSION['cidade'] ?? ''; ?>" />
          </div>

          <div>
            <label for="rua">rua:</label>
            <input type="text" id="rua" name="rua"
              value="<?php echo $_SESSION['rua'] ?? ''; ?>" />
          </div>

          <div>
            <label for="numero">número:</label>
            <input type="text" id="numero" name="numero"
              value="<?php echo $_SESSION['numero'] ?? ''; ?>" />
          </div>

        </div>
      </form>
      <footer id="footer-form1">
        <button id="btn-continuar">Continuar</button>

      </footer>
    <?php } else { ?>

      <form method="post" id="form2" method="post">

        <div id="container-inputs">
          <div>
            <label for="email">e-mail:</label>
            <input type="email" id="email" name="email" />
          </div>
          <div>
            <label for="confirmar-email">confirmar e-mail:</label>
            <input type="email" id="confirmar-email" name="confirmar-email" />
          </div>
          <div>
            <label for="senha">senha:</label>
            <input type="password" name="senha" id="senha" />
          </div>
          <div>
            <label for="confirmar-senha">confirmar senha:</label>
            <input type="password" name="confirmar-senha" id="confirmar-senha" />
          </div>
        </div>

      </form>
      <footer>
        <button id="btn-voltar" type="submit" name="voltar" value="voltar">Voltar</button>
        <button id="btn-cadastrar">Cadastrar</button>

      </footer>
    <?php } ?>
  </section>
  <script src="js/script.js"></script>
  </body>

</html>