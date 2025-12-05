<?php
require_once "src/Services/AutenticarServico.php";
require_once "src/Helpers/Utils.php";

// Inicia sessão se necessário
AutenticarServico::iniciarSecao();

// Limpa todas as variáveis de sessão
$_SESSION = []; 

// Apaga cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroi a sessão
session_destroy();

// Redireciona para página inicial
Utils::redirecionarPara('index.php');
