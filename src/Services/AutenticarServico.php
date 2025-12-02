<?php

class AutenticarServico
{

    public static function iniciarSecao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function exigirLogin(): void
    {
        self::iniciarSecao();

        if (!isset($_SESSION["id"])) {
            session_destroy();
            Utils::redirecionarPara("login.php");
        }
    }

    public static function criarLogin(int $id, string $nome, string $email):void
    {
        self::iniciarSecao();
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        Utils::redirecionarPara("minha_conta.php");
    }

    public static function estaLogado():void{
        self::iniciarSecao();
        if(isset($_SESSION["id"])){
            Utils::redirecionarPara("minha_conta.php");
        }
    }

    
}
