<?php 
require_once "src/Database/Conecta.php";
require_once "src/Helpers/Utils.php";
require_once "src/Models/Usuario.php";
require_once "src/Services/UsuarioServicos.php";
require_once "src/Services/AutenticarServico.php";

AutenticarServico::estaLogado();

$usuarioServicos = new UsuarioServicos();
$erro = null;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!empty($_POST['email']) && !empty($_POST['senha'])){
        $emailValido = $usuarioServicos->buscarPorEmail($_POST['email']);
        if($emailValido){
            echo "email válido";
            echo $emailValido['senha'];
            if(Utils::verificarSenha($emailValido['senha'], $_POST['senha'])){
                echo "senha válida";
                AutenticarServico::criarLogin($emailValido['id'], $emailValido['nome'], $emailValido['email']);         
            }else{
                $erro = "Dados inválidos";
            }
        }else{
            $erro = "Dados inválidos";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/login.css">
    <?php require_once "includes/cabecalho.php"; ?>
    <?php if ($erro) {
      echo "<p>$erro</p>";
    }  ?>
    <section id="sessao-login">
        <h2>Faça seu <span>Login</span></h2>
        <form action="" method="post">
            <div id="container-inputs">
                <div>
                <label for="email">e-mail:</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="senha">senha:</label>
                <input type="password" name="senha" id="senha">
            </div>
            </div>
            <div id="link-cadastro">
                <span>Não tem uma conta? </span><a href="cadastrar.php">Faça seu cadastro</a>
            </div>

            <input type="submit" value="entrar">
        </form>
    </section>

    <?php require_once("includes/rodape.php"); ?>
</body>
</html>