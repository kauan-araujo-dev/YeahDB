<?php

class EventoServicos
{
    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    /* ============================================================
       BUSCAR EVENTO POR ID (Necessário na edição)
    ============================================================ */
    public function buscarEventoPorId(int $id): ?array
    {
        $sql = "SELECT * FROM eventos WHERE id = :id LIMIT 1";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /* ============================================================
       BUSCAR TODAS AS IMAGENS DO EVENTO
    ============================================================ */
    public function buscarImagensEvento(int $idEvento): array
    {
        $sql = "SELECT id, url_imagem 
                FROM foto_evento 
                WHERE id_evento = :id
                ORDER BY id ASC";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $idEvento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ============================================================
       BUSCAR ESTILOS MUSICAIS DO EVENTO
    ============================================================ */
    public function buscarEstilosEvento(int $idEvento): array
    {
        $sql = "SELECT em.id, em.nome
                FROM evento_estilo ee
                JOIN estilo_musical em ON em.id = ee.id_estilo
                WHERE ee.id_evento = :id
                ORDER BY em.nome ASC";

        $c = $this->conexao->prepare($sql);
        $c->bindValue(":id", $idEvento, PDO::PARAM_INT);
        $c->execute();

        return $c->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ============================================================
       BUSCAR INTEGRANTES DO EVENTO
    ============================================================ */
    public function buscarIntegrantesEvento(int $idEvento): array
    {
        $sql = "SELECT id, nome, estilo_musical, url_imagem 
                FROM integrante_evento
                WHERE id_evento = :id
                ORDER BY id ASC";

        $c = $this->conexao->prepare($sql);
        $c->bindValue(":id", $idEvento, PDO::PARAM_INT);
        $c->execute();

        return $c->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ============================================================
       BUSCAR EVENTOS DO USUÁRIO (Já existia)
    ============================================================ */
    public function buscarEventosUsuario(int $id): ?array
    {
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,
            (
                SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
                FROM foto_evento
                WHERE foto_evento.id_evento = eventos.id
                ORDER BY foto_evento.id ASC
            ) AS imagens,
            (
                SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
                FROM evento_estilo
                JOIN estilo_musical 
                    ON estilo_musical.id = evento_estilo.id_estilo
                WHERE evento_estilo.id_evento = eventos.id
                ORDER BY estilo_musical.id ASC
                LIMIT 1
            ) AS estilos_musicais
        FROM eventos 
        JOIN usuarios ON eventos.id_usuario = usuarios.id
        WHERE usuarios.id = :id 
        ORDER BY eventos.id DESC";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /* ============================================================
       OUTRAS FUNÇÕES (permanecem iguais)
    ============================================================ */

    public function inserirEvento(Eventos $evento): int {
        $sql = "INSERT INTO eventos
            (nome, descricao, estado, cidade, endereco, dia, horario, instagram, contato, link_compra, id_usuario)
            VALUES
            (:nome, :descricao, :estado, :cidade, :endereco, :dia, :horario, :instagram, :contato, :link_compra, :id_usuario)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":nome", $evento->getNome());
        $consulta->bindValue(":descricao", $evento->getDescricao());
        $consulta->bindValue(":estado", $evento->getEstado());
        $consulta->bindValue(":cidade", $evento->getCidade());
        $consulta->bindValue(":endereco", $evento->getEndereco());
        $consulta->bindValue(":dia", $evento->getDia());
        $consulta->bindValue(":horario", $evento->getHorario());
        $consulta->bindValue(":instagram", $evento->getInstagram());
        $consulta->bindValue(":contato", $evento->getContato());
        $consulta->bindValue(":link_compra", $evento->getLinkCompra());
        $consulta->bindValue(":id_usuario", $evento->getIdUsuario(), PDO::PARAM_INT);

        $consulta->execute();

        return intval($this->conexao->lastInsertId());
    }

    public function inserirFotoEvento(FotoEvento $foto): void {
        $sql = "INSERT INTO foto_evento (url_imagem, id_evento)
                VALUES (:url, :id_evento)";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":url", $foto->getUrl());
        $consulta->bindValue(":id_evento", $foto->getIdEvento());
        $consulta->execute();
    }

    public function inserirEstilo($id_evento, $id_estilo){
        $sql = "INSERT INTO evento_estilo(id_evento, id_estilo) VALUES(:id_evento, :id_estilo)";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_evento', $id_evento);
        $consulta->bindValue(':id_estilo', $id_estilo);
        $consulta->execute();
    }

    public function inserirIntegrante(IntegranteEvento $integrante): void
    {
        $sql = "INSERT INTO integrante_evento 
            (nome, estilo_musical, url_imagem, id_evento)
            VALUES 
            (:nome, :estilo_musical, :url_imagem, :id_evento)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':nome', $integrante->getNome());
        $consulta->bindValue(':estilo_musical', $integrante->getEstiloMusical());
        $consulta->bindValue(':url_imagem', $integrante->getUrlImagem());
        $consulta->bindValue(':id_evento', $integrante->getIdEvento());

        $consulta->execute();
    }

    /* ============================================================
       EXCLUIR EVENTO (permanece igual)
    ============================================================ */
    public function excluirEvento($id, $idUsuario): int {
        try {
            $this->conexao->beginTransaction();

            $sqls = [
                "DELETE FROM artista_evento WHERE id_evento = :id",
                "DELETE FROM evento_estilo WHERE id_evento = :id",
                "DELETE FROM foto_evento WHERE id_evento = :id",
                "DELETE FROM integrante_evento WHERE id_evento = :id",
            ];

            foreach ($sqls as $s) {
                $stmt = $this->conexao->prepare($s);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $sql = "DELETE FROM eventos WHERE id = :id AND id_usuario = :id_usuario";
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $consulta->execute();

            $rows = $consulta->rowCount();

            $this->conexao->commit();

            return $rows;
        } catch (\Throwable $e) {
            $this->conexao->rollBack();
            throw $e;
        }
    }
}
