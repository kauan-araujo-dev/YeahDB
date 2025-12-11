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
            SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
        ) AS imagens, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilos_musicais
        FROM artistas
        ORDER BY artistas.id ASC";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }
    public function buscarArtistaId(int $id)
    {
        $sql = "SELECT DISTINCT artistas.id, artistas.nome, artistas.cidade, artistas.estado, artistas.whatsapp, artistas.contato, artistas.instagram, artistas.whatsapp, artistas.cache_artista, artistas.descricao, (
        SELECT GROUP_CONCAT(foto_artista.url_imagem SEPARATOR '||')
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
    ) AS imagens,
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


        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function buscarArtistasUsuario(int $id): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, (
            SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
        ) AS imagens, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
        ) AS estilos_musicais
        FROM artistas JOIN usuarios ON artistas.id_usuario = usuarios.id
        WHERE usuarios.id = :id ORDER BY artistas.id DESC";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();



        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarArtistasComLimite(int $limite): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, (
            SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
        ) AS imagens, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
        ) AS estilos_musicais
        FROM artistas
        ORDER BY artistas.id DESC LIMIT :limite";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

 
    public function buscarArtistasPorEstiloId(int $idEstilo): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, artistas.cidade, artistas.estado, (
            SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
        ) AS imagens, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilos_musicais
        FROM artistas
        JOIN artista_estilo ae ON ae.id_artista = artistas.id
        WHERE ae.id_estilo = :id_estilo
        ORDER BY artistas.id DESC";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_estilo', $idEstilo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarArtistasAleatorios(int $limite = 6): ?array
    {
        $sql = "SELECT artistas.id, artistas.nome, artistas.cidade, artistas.estado, (
            SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
            FROM foto_artista
            WHERE foto_artista.id_artista = artistas.id
            ORDER BY foto_artista.id ASC
        ) AS imagens, (
            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
            FROM artista_estilo
            JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
            WHERE artista_estilo.id_artista = artistas.id
            ORDER BY estilo_musical.id ASC
            LIMIT 1
        ) AS estilos_musicais
        FROM artistas
        ORDER BY RAND() LIMIT :limite";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }
    public function buscarIntegrantes(int $id)
    {
        $sql = "SELECT integrante_artista.id, integrante_artista.nome, integrante_artista.instrumento, integrante_artista.url_imagem
                FROM integrante_artista
                WHERE integrante_artista.id_artista = :id";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function inserirArtista(Artista $dadosArtista): int
    {
        $sql = "INSERT INTO artistas 
            (nome, descricao, estado, cidade, cache_artista, whatsapp, instagram, contato, codigo_artista, id_usuario)
            VALUES 
            (:nome, :descricao, :estado, :cidade, :cache_artista, :whatsapp, :instagram, :contato, :codigo_artista, :id_usuario)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':nome', $dadosArtista->getNome());
        $consulta->bindValue(':descricao', $dadosArtista->getDescricao());
        $consulta->bindValue(':estado', $dadosArtista->getEstado());
        $consulta->bindValue(':cidade', $dadosArtista->getCidade());
        $consulta->bindValue(':cache_artista', $dadosArtista->getCacheArtista());
        $consulta->bindValue(':whatsapp', $dadosArtista->getWhatsapp());
        $consulta->bindValue(':instagram', $dadosArtista->getInstagram());
        $consulta->bindValue(':contato', $dadosArtista->getContato());
        $consulta->bindValue(':codigo_artista', $dadosArtista->getCodigoArtista());
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

  public function buscarArtistasPorFiltros(?string $estado, ?string $cidade, ?string $estilo): ?array
    {
        $params = [];
        $where = [];

        if ($estado) {
            $where[] = 'artistas.estado = :estado';
            $params[':estado'] = $estado;
        }

        if ($cidade) {
            $where[] = 'artistas.cidade = :cidade';
            $params[':cidade'] = $cidade;
        }

        $joinEstilo = '';
        if ($estilo) {
            $joinEstilo = "JOIN artista_estilo ae ON ae.id_artista = artistas.id
                            JOIN estilo_musical em ON em.id = ae.id_estilo";
            $where[] = 'em.nome = :estilo';
            $params[':estilo'] = $estilo;
        }

        $sql = "SELECT artistas.id, artistas.nome, artistas.cidade, artistas.estado,
                    (SELECT GROUP_CONCAT(url_imagem SEPARATOR '||')
                     FROM foto_artista 
                     WHERE foto_artista.id_artista = artistas.id) AS imagens,
                    (SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')
                     FROM artista_estilo
                     JOIN estilo_musical ON estilo_musical.id = artista_estilo.id_estilo
                     WHERE artista_estilo.id_artista = artistas.id
                     ORDER BY estilo_musical.id ASC
                     LIMIT 1) AS estilos_musicais
                FROM artistas
                $joinEstilo";

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY artistas.id DESC';

        $consulta = $this->conexao->prepare($sql);
        foreach ($params as $k => $v) {
            $consulta->bindValue($k, $v);
        }
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    // Buscar todos os estados disponíveis para filtro
    public function buscarEstadosDisponiveis(): array
    {
        $sql = "SELECT DISTINCT estado 
                FROM artistas 
                WHERE estado IS NOT NULL AND estado != '' 
                ORDER BY estado ASC";

        $stmt = $this->conexao->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    // Buscar todas as cidades disponíveis para filtro
    public function buscarCidadesDisponiveis(): array
    {
        $sql = "SELECT DISTINCT cidade 
                FROM artistas 
                WHERE cidade IS NOT NULL AND cidade != '' 
                ORDER BY cidade ASC";

        $stmt = $this->conexao->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    // Buscar todos os estilos musicais disponíveis para filtro
    public function buscarEstilosDisponiveis(): array
    {
        $sql = "SELECT DISTINCT em.nome
                FROM artista_estilo ae
                JOIN estilo_musical em ON em.id = ae.id_estilo
                ORDER BY em.nome ASC";

        $stmt = $this->conexao->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    // Buscar cidades disponíveis dentro de um estado específico
    public function buscarCidadesPorEstado(string $estado): array
    {
        $sql = "SELECT DISTINCT cidade FROM artistas WHERE estado = :estado ORDER BY cidade ASC";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
    public function buscarEventosArtista($id){
    $sql = "SELECT DISTINCT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,  (
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
FROM eventos JOIN artista_evento ON artista_evento.id_evento = eventos.id JOIN artistas ON artista_evento.id_artista = artistas.id WHERE artistas.id = :id
";

$consulta = $this->conexao->prepare($sql);
    $consulta->bindValue(":id", $id);
    $consulta->execute();
    return $consulta->fetchAll() ?: null;
}
}
