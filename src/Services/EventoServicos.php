<?php

class EventoServicos
{

    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarEventos(): ?array
    {
        $sql = "SELECT foto_evento.url_imagem
        FROM foto_evento
        WHERE foto_evento.id_evento = eventos.id
        ORDER BY foto_evento.id ASC
        LIMIT 1
        ) AS url_imagem,
        (
            SELECT estilo_musical.nome
            FROM evento_estilo
            JOIN estilo_musical 
                ON estilo_musical.id = evento_estilo.id_estilo
            WHERE evento_estilo.id_evento = eventos.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilo
        FROM eventos
        ORDER BY eventos.id ASC";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll() ?: null;
    }
    public function buscarEventosUsuario(int $id): ?array
    {
       $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,  (
        SELECT foto_evento.url_imagem
        FROM foto_evento
        WHERE foto_evento.id_evento = eventos.id
        ORDER BY foto_evento.id ASC
        LIMIT 1
        ) AS url_imagem,
        (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM evento_estilo
            JOIN estilo_musical 
                ON estilo_musical.id = evento_estilo.id_estilo
            WHERE evento_estilo.id_evento = eventos.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilos_musicais
        FROM eventos JOIN usuarios ON eventos.id_usuario = usuarios.id
        WHERE usuarios.id = :id ORDER BY eventos.id DESC";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();



        return $consulta->fetchAll() ?: null;
    }
    public function buscarEventosComLimite(int $limite): ?array
    {
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,  (
        SELECT foto_evento.url_imagem
        FROM foto_evento
        WHERE foto_evento.id_evento = eventos.id
        ORDER BY foto_evento.id ASC
        LIMIT 1
        ) AS url_imagem,
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
        ORDER BY eventos.id DESC LIMIT $limite";

        $consulta = $this->conexao->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll() ?: null;
    }

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
    public function inserirEstilo($id_evento, $id_estilo){
        $sql = "INSERT INTO evento_estilo(id_evento, id_estilo) VALUES(:id_evento, :id_estilo)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':id_evento', $id_evento);
        $consulta->bindValue(':id_estilo', $id_estilo);

        $consulta->execute();
    }

    public function inserirFotoEvento(FotoEvento $foto): void {
        $sql = "INSERT INTO foto_evento (url_imagem, id_evento)
                VALUES (:url, :id_evento)";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":url", $foto->getUrl());
        $consulta->bindValue(":id_evento", $foto->getIdEvento());
        $consulta->execute();
    }

     public static function criarPastasUpload(int $id): bool
    {
        // sobe 2 níveis e chega na raiz do projeto
        $root = dirname(__DIR__, 2);

        // Caminho base dentro da pasta raiz -> img/artistas/id
        $basePath = $root . "/img/artistas/$id";

        // Subpastas obrigatórias
        $pastas = [
            $basePath,
            "$basePath/fotos_eventos",
            "$basePath/fotos_integrantes"
        ];

        foreach ($pastas as $pasta) {
            if (!file_exists($pasta)) {
                if (!mkdir($pasta, 0777, true)) {
                    return false; // Erro ao criar pasta
                }
            }
        }

        return true;
    }

    public static function excluirEvento(){
        
    }
}
