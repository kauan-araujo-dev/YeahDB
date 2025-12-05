<?php
class EstilosMusicaisServicos
{
    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarEstilos(): ?array
    {
        $sql = "SELECT id, nome, imagem FROM estilo_musical;";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll() ?: null;
    }
    public function buscarEstilosComLimite(): ?array
    {
        $sql = "SELECT id, nome, imagem FROM estilo_musical LIMIT 3;";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll() ?: null;
    }

    public function buscarEstilosPorFiltros(?string $estado, ?string $cidade, ?string $estilo): ?array
    {
        $params = [];
        $where = [];
        $join = "JOIN artista_estilo ae ON ae.id_estilo = estilo_musical.id\nJOIN artistas a ON a.id = ae.id_artista";

        $sql = "SELECT DISTINCT estilo_musical.id, estilo_musical.nome, estilo_musical.imagem\nFROM estilo_musical\n" . $join . "\nWHERE 1=1";

        if ($estado) {
            $sql .= " AND a.estado = :estado";
            $params[':estado'] = $estado;
        }
        if ($cidade) {
            $sql .= " AND a.cidade = :cidade";
            $params[':cidade'] = $cidade;
        }
        if ($estilo) {
            $sql .= " AND estilo_musical.nome = :estilo";
            $params[':estilo'] = $estilo;
        }

        $sql .= " ORDER BY estilo_musical.nome ASC";

        $consulta = $this->conexao->prepare($sql);
        foreach ($params as $k => $v) {
            $consulta->bindValue($k, $v);
        }
        $consulta->execute();

        return $consulta->fetchAll() ?: null;
    }

    public function buscarEstilosAleatorios(int $limite = 6): ?array
    {
        $sql = "SELECT id, nome, imagem FROM estilo_musical ORDER BY RAND() LIMIT :limite";
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll() ?: null;
    }
}
