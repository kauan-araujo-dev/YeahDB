<?php
class EstilosMusicaisServicos
{
    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarArtistas(): ?array
    {
        $sql = "SELECT id, nome, imagem FROM estilo_musical LIMIT 3;";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll() ?: null;
    }
}
