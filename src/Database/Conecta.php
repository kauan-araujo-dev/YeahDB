<?php

class Conecta
{
    private static ?PDO $conexao = null;

    public static function getConexao(): PDO
    {
        if (self::$conexao === null) {
            $host = 'localhost';
            $banco = 'yeah_db';
            $usuario = 'root';
            $senha = '';
            $porta = 3306;

            $dsn = "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4";

            try {
                self::$conexao = new PDO(
                    $dsn,
                    $usuario,
                    $senha,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Erro ao conectar ao banco: ' . $e->getMessage());
            }
        }

        return self::$conexao;
    }
}
