<?php
// Uso: php clean_db_except_estilo.php --confirm
// Este script executa a limpeza das tabelas (truncate) EXCETO a tabela `estilo_musical`.
// Ele deve ser executado via CLI e exige o parâmetro --confirm para efetuar alterações.

require_once __DIR__ . '/../src/Database/Conecta.php';

$confirm = in_array('--confirm', $argv ?? []);

if (php_sapi_name() !== 'cli') {
    echo "Este script deve ser executado via linha de comando (CLI).\n";
    exit(1);
}

if (!$confirm) {
    echo "MODO DE SIMULAÇÃO (dry-run). Nenhuma alteração será feita.\n";
    echo "Execute com --confirm para aplicar a limpeza.\n";
    echo "Tabelas que seriam truncadas:\n";
    $tables = [
        'artista_evento', 'artista_estilo', 'evento_estilo', 'foto_evento', 'foto_artista',
        'integrante_evento', 'integrante_artista', 'eventos', 'artistas', 'usuarios'
    ];
    foreach ($tables as $t) echo " - $t\n";
    exit(0);
}

$pdo = Conecta::getConexao();
try {
    echo "Iniciando limpeza do banco (excluindo tabela estilo_musical)...\n";
    $pdo->beginTransaction();

    // Desabilitar checagem de FK temporariamente
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $tables = [
        'artista_evento', 'artista_estilo', 'evento_estilo', 'foto_evento', 'foto_artista',
        'integrante_evento', 'integrante_artista', 'eventos', 'artistas', 'usuarios'
    ];

    foreach ($tables as $t) {
        echo "Truncando $t... ";
        $pdo->exec("TRUNCATE TABLE `$t`");
        echo "OK\n";
    }

    // Reabilitar FK checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    $pdo->commit();
    echo "Limpeza concluída com sucesso.\n";

    // Opcional: remover imagens relacionadas (descomente se quiser habilitar)
    // $root = dirname(__DIR__);
    // $dirs = [ $root . '/img/artistas', $root . '/img/eventos' ];
    // foreach ($dirs as $d) {
    //     if (is_dir($d)) {
    //         $it = new RecursiveDirectoryIterator($d, RecursiveDirectoryIterator::SKIP_DOTS);
    //         $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    //         foreach ($files as $file) {
    //             if ($file->isDir()) rmdir($file->getRealPath()); else unlink($file->getRealPath());
    //         }
    //     }
    // }

} catch (Throwable $e) {
    try { $pdo->rollBack(); } catch (Throwable $ignore) {}
    echo "Erro ao limpar o banco: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
