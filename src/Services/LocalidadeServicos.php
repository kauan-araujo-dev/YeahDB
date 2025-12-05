<?php

class LocalidadeServicos
{
    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarEstados(): ?array
    {
        $sql = "SELECT DISTINCT estado FROM (
            SELECT estado FROM eventos
            UNION ALL
            SELECT estado FROM artistas
            UNION ALL
            SELECT estado FROM usuarios
        ) AS t WHERE estado IS NOT NULL AND estado <> '' ORDER BY estado";

        $consulta = $this->conexao->prepare($sql);
        $consulta->execute();

        $result = $consulta->fetchAll(PDO::FETCH_COLUMN);
        return $result ?: null;
    }

    /**
     * Retorna um array associativo: estado => [cidades...]
     */
    public function buscarCidadesAgrupadas(): array
    {
        $sql = "SELECT DISTINCT estado, cidade FROM (
            SELECT estado, cidade FROM eventos
            UNION ALL
            SELECT estado, cidade FROM artistas
            UNION ALL
            SELECT estado, cidade FROM usuarios
        ) AS t WHERE cidade IS NOT NULL AND cidade <> '' AND estado IS NOT NULL AND estado <> '' ORDER BY estado, cidade";

        $consulta = $this->conexao->prepare($sql);
        $consulta->execute();

        $rows = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $r) {
            $est = $r['estado'];
            $cid = $r['cidade'];
            if (!isset($map[$est])) $map[$est] = [];
            if (!in_array($cid, $map[$est])) $map[$est][] = $cid;
        }

        return $map;
    }
}
