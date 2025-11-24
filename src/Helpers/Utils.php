<?php
class Utils
{
    public static function sanitizar(mixed $valor, string $tipo = "texto"): mixed
    {

        switch ($tipo) {
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

    public static function codificarSenha(string $senha)
    {
        return password_hash($senha, PASSWORD_DEFAULT);
    }

    public static function redirecionarPara(string $destino): void
    {
        header("location: $destino");
        exit;
    }

    public static function dump(mixed $valor): void
    {
        echo "<pre>";
        var_dump($valor);
        echo "</pre>";
    }

    public static function verificarSenha(string $senhaBanco, string $senhaFormulario): bool
    {
        return password_verify($senhaFormulario, $senhaBanco);
    }

    public static function organizarArquivos(array $arquivos): array
    {
        $arr = [];

        $total = count($arquivos['name']);

        for ($i = 0; $i < $total; $i++) {
            $arr[] = [
                'name'     => $arquivos['name'][$i],
                'type'     => $arquivos['type'][$i],
                'tmp_name' => $arquivos['tmp_name'][$i],
                'error'    => $arquivos['error'][$i],
                'size'     => $arquivos['size'][$i]
            ];
        }

        return $arr;
    }
    public static function validarArquivo(?array $arquivo): bool
    {
        if (
            !$arquivo ||
            !isset($arquivo["tmp_name"]) ||
            !is_uploaded_file($arquivo["tmp_name"])
        ) {
            return false;
        }

        $formatosPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
        $tamanhoMaximo = 15 * 1024 * 1024; // 2MB

        $formatoDoArquivoEnviado = mime_content_type($arquivo["tmp_name"]);

        if (!in_array($formatoDoArquivoEnviado, $formatosPermitidos)) {
            return false;
        }

        if ($arquivo["size"] > $tamanhoMaximo) {
            return false;
        }

        return true;
    }
    public static function salvarArquivo(array $arquivo, string $pastaDestino): string
    {
        // Garante que a pasta existe
        if (!file_exists($pastaDestino)) {
            mkdir($pastaDestino, 0777, true);
        }

        // Nome final
        $destinoFinal = rtrim($pastaDestino, "/") . "/" . basename($arquivo["name"]);

        if (!move_uploaded_file($arquivo["tmp_name"], $destinoFinal)) {
            throw new Exception("Erro ao mover o arquivo. Código de erro: " . $arquivo["error"]);
        }

        return $destinoFinal; // retorna caminho salvo
    }
}
