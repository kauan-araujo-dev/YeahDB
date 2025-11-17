<?php 
class Utils{
    public static function sanitizar(mixed $valor, string $tipo = "texto"):mixed{

        switch($tipo){
            case 'inteiro':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'real':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case 'email':
                return filter_var($valor, FILTER_SANITIZE_EMAIL);
                break;
            default:
                return filter_var($valor, FILTER_SANITIZE_SPECIAL_CHARS);
        }

    }

    public static function codificarSenha(string $senha){
        return password_hash($senha, PASSWORD_DEFAULT);
    }

    public static function redirecionarPara(string $destino) :void{
        header("location: $destino");
        exit;

    }

    public static function dump(mixed $valor) :void{
        echo "<pre>";
        var_dump($valor);
        echo "</pre>";

    }
}

?>