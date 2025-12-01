<?php

class ArtistaServicos
{

    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarArtistas(): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, (
        SELECT foto_artista.url_imagem
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
        LIMIT 1
        ) AS url_imagem,
        (
        SELECT estilo_musical.nome
        FROM artista_estilo
        JOIN estilo_musical 
            ON estilo_musical.id = artista_estilo.id_estilo
        WHERE artista_estilo.id_artista = artistas.id
        ORDER BY estilo_musical.id ASC
        LIMIT 1
        ) AS estilo
        FROM artistas
        ORDER BY artistas.id ASC";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll() ?: null;
    }
    public function buscarArtistaId(int $id)
    {
        $sql = "SELECT DISTINCT artistas.id, artistas.nome, artistas.cidade, artistas.estado, artistas.whatsapp, artistas.contato, artistas.instagram, artistas.whatsapp, artistas.cache_artista, artistas.descricao, (
        SELECT GROUP_CONCAT(foto_artista.url_imagem SEPARATOR ',')
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
    ) AS url_imagem,
    (
        SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
        FROM artista_estilo
        JOIN estilo_musical 
            ON estilo_musical.id = artista_estilo.id_estilo
        WHERE artista_estilo.id_artista = artistas.id
    ) AS estilos_musicais
    FROM artistas JOIN artista_estilo ON artista_estilo.id_artista = artistas.id WHERE id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);
        $consulta->execute();


        return $consulta->fetch() ?: null;
    }
    public function buscarArtistasUsuario(int $id): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, (
        SELECT foto_artista.url_imagem
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
        LIMIT 1
        ) AS url_imagem,
        (
        SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
        FROM artista_estilo
        JOIN estilo_musical 
            ON estilo_musical.id = artista_estilo.id_estilo
        WHERE artista_estilo.id_artista = artistas.id
    ) AS estilos_musicais
        FROM artistas JOIN usuarios ON artistas.id_usuario = usuarios.id
        WHERE usuarios.id = :id ORDER BY artistas.id DESC";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();



        return $consulta->fetchAll() ?: null;
    }

    public function buscarArtistasComLimite(int $limite): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, (
        SELECT foto_artista.url_imagem
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
        LIMIT 1
        ) AS url_imagem,
        (
        SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
        FROM artista_estilo
        JOIN estilo_musical 
            ON estilo_musical.id = artista_estilo.id_estilo
        WHERE artista_estilo.id_artista = artistas.id
    ) AS estilos_musicais
        FROM artistas
        ORDER BY artistas.id DESC LIMIT $limite ";

        $consulta = $this->conexao->prepare($sql);


        $consulta->execute();


        return $consulta->fetchAll() ?: null;
    }
    public function buscarIntegrantes(int $id)
    {
        $sql = "SELECT 	integrante_artista.id, integrante_artista.nome, integrante_artista.instrumento, integrante_artista.url_imagem FROM integrante_artista JOIN artistas ON integrante_artista.id = artistas.id WHERE artistas.id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);
        $consulta->execute();


        return $consulta->fetchAll() ?: null;
    }

    public function inserirArtista(Artista $dadosArtista): int
    {
        $sql = "INSERT INTO artistas 
            (nome, descricao, estado, cidade, cache_artista, whatsapp, instagram, contato, id_usuario)
            VALUES 
            (:nome, :descricao, :estado, :cidade, :cache_artista, :whatsapp, :instagram, :contato, :id_usuario)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':nome', $dadosArtista->getNome());
        $consulta->bindValue(':descricao', $dadosArtista->getDescricao());
        $consulta->bindValue(':estado', $dadosArtista->getEstado());
        $consulta->bindValue(':cidade', $dadosArtista->getCidade());
        $consulta->bindValue(':cache_artista', $dadosArtista->getCacheArtista());
        $consulta->bindValue(':whatsapp', $dadosArtista->getWhatsapp());
        $consulta->bindValue(':instagram', $dadosArtista->getInstagram());
        $consulta->bindValue(':contato', $dadosArtista->getContato());
        $consulta->bindValue(':id_usuario', $dadosArtista->getIdUsuario(), PDO::PARAM_INT);

        $consulta->execute();

        // retorna o ID gerado
        return intval($this->conexao->lastInsertId());
    }

    public function inserirEstilo($id_artista, $id_estilo){
        $sql = "INSERT INTO artista_estilo(id_artista, id_estilo) VALUES(:id_artista, :id_estilo)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':id_artista', $id_artista);
        $consulta->bindValue(':id_estilo', $id_estilo);

        $consulta->execute();
    }

    public function inserirIntegrante(IntegranteArtista $integrante): void
    {
        $sql = "INSERT INTO integrante_artista 
            (nome, instrumento, url_imagem, id_artista)
            VALUES 
            (:nome, :instrumento, :url_imagem, :id_artista)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':nome', $integrante->getNome());
        $consulta->bindValue(':instrumento', $integrante->getInstrumento());
        $consulta->bindValue(':url_imagem', $integrante->getUrlImagem());
        $consulta->bindValue(':id_artista', $integrante->getIdArtista());

        $consulta->execute();
    }

    public function inserirFotoArtista(FotoArtista $foto): void
    {
        $sql = "INSERT INTO foto_artista (url_imagem, id_artista)
                VALUES (:url_imagem, :id_artista)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":url_imagem", $foto->getUrl());
        $consulta->bindValue(":id_artista", $foto->getIdArtista());

        $consulta->execute();

        // retorna o ID recém inserido
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
            "$basePath/fotos_artistas",
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
}
