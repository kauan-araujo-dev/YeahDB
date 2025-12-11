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
    public function buscarEventosId(int $id): ?array
    {
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia, eventos.descricao, eventos.link_compra, eventos.endereco, eventos.horario, eventos.contato, (
        SELECT GROUP_CONCAT(foto_evento.url_imagem SEPARATOR ',')
        FROM foto_evento
        WHERE foto_evento.id_evento = eventos.id
        ORDER BY foto_evento.id ASC
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
        WHERE id = :id ORDER BY eventos.id DESC";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();



        return $consulta->fetch() ?: null;
    }
    public function buscarEventosComLimite(int $limite): ?array
    {
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia,
            (
                SELECT GROUP_CONCAT(foto_evento.url_imagem SEPARATOR '||')
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
            ) AS estilos_musicais
        FROM eventos
        ORDER BY eventos.id DESC LIMIT :limite";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarEventosPorFiltros(?string $estado, ?string $cidade, ?string $estilo): ?array
    {
        $params = [];
        $where = [];

        if ($estado) {
            $where[] = 'eventos.estado = :estado';
            $params[':estado'] = $estado;
        }
        if ($cidade) {
            $where[] = 'eventos.cidade = :cidade';
            $params[':cidade'] = $cidade;
        }
        $joinEstilo = '';
        if ($estilo) {
            $joinEstilo = "JOIN evento_estilo ee ON ee.id_evento = eventos.id\nJOIN estilo_musical em ON em.id = ee.id_estilo";
            $where[] = 'em.nome = :estilo';
            $params[':estilo'] = $estilo;
        }

        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia, (\n            SELECT foto_evento.url_imagem\n            FROM foto_evento\n            WHERE foto_evento.id_evento = eventos.id\n            ORDER BY foto_evento.id ASC\n            LIMIT 1\n        ) AS url_imagem, (\n            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')\n            FROM evento_estilo\n            JOIN estilo_musical ON estilo_musical.id = evento_estilo.id_estilo\n            WHERE evento_estilo.id_evento = eventos.id\n            ORDER BY estilo_musical.id ASC\n            LIMIT 1\n        ) AS estilos_musicais\n        FROM eventos\n" . $joinEstilo . "\n";

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY eventos.id DESC';

        $consulta = $this->conexao->prepare($sql);
        foreach ($params as $k => $v) {
            $consulta->bindValue($k, $v);
        }
        $consulta->execute();

        return $consulta->fetchAll() ?: null;
    }

    public function buscarEventosAleatorios(int $limite = 4): ?array
    {
        $sql = "SELECT eventos.id, eventos.nome, eventos.cidade, eventos.estado, eventos.dia, (\n            SELECT foto_evento.url_imagem\n            FROM foto_evento\n            WHERE foto_evento.id_evento = eventos.id\n            ORDER BY foto_evento.id ASC\n            LIMIT 1\n        ) AS url_imagem, (\n            SELECT GROUP_CONCAT(estilo_musical.nome SEPARATOR ',')\n            FROM evento_estilo\n            JOIN estilo_musical ON estilo_musical.id = evento_estilo.id_estilo\n            WHERE evento_estilo.id_evento = eventos.id\n            ORDER BY estilo_musical.id ASC\n            LIMIT 1\n        ) AS estilos_musicais\n        FROM eventos\n        ORDER BY RAND() LIMIT :limite";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll() ?: null;
    }

    public function inserirEvento(Eventos $evento): int
    {
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
    public function inserirEstilo($id_evento, $id_estilo)
    {
        $sql = "INSERT INTO evento_estilo(id_evento, id_estilo) VALUES(:id_evento, :id_estilo)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':id_evento', $id_evento);
        $consulta->bindValue(':id_estilo', $id_estilo);

        $consulta->execute();
    }

    public function inserirFotoEvento(FotoEvento $foto): void
    {
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

    public function excluirEvento($id, $idUsuario): int
    {
        // Deletar registros filhos antes de excluir o evento para não violar FKs.
        // Usa transação para garantir atomicidade.
        try {
            $this->conexao->beginTransaction();

            // Tabelas que referenciam eventos.id: artista_evento, evento_estilo, foto_evento, integrante_evento
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

            // Agora exclui o próprio evento (verificando propriedade)
            $sql = "DELETE FROM eventos WHERE id = :id AND id_usuario = :id_usuario";
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $consulta->execute();

            $rows = $consulta->rowCount();

            $this->conexao->commit();

            if ($rows > 0) {
                // Remover arquivos relacionados ao evento
                $root = dirname(__DIR__, 2);
                $dirEvento = $root . "/img/eventos/" . intval($id);

                if (is_dir($dirEvento)) {
                    $this->removerDiretorioRecursivo($dirEvento);
                }
            }

            return $rows;
        } catch (\Throwable $e) {
            try {
                $this->conexao->rollBack();
            } catch (\Throwable $ignore) {
            }

            // Re-throw para o controlador/log, ou retornar 0 caso prefira silenciar
            throw $e;
        }
    }

    private function removerDiretorioRecursivo(string $dir): void
    {
        // Segurança: garante que estamos apagando dentro da pasta img/eventos
        $root = dirname(__DIR__, 2);
        $expectedBase = realpath($root . "/img/eventos");
        $target = realpath($dir);

        if ($target === false || strpos($target, $expectedBase) !== 0) {
            return; // proteção contra deletar caminhos fora do esperado
        }

        $it = new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        // remove a pasta raiz do evento
        if (is_dir($target)) {
            rmdir($target);
        }
    }

    public function obterEventoPorId(int $id): ?array
    {
        $sql = "SELECT * FROM eventos WHERE id = :id LIMIT 1";
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    public function buscarParticipantes(int $id)
    {
        $sql = "SELECT integrante_evento.id, integrante_evento.nome, integrante_evento.estilo_musical, integrante_evento.url_imagem
                FROM integrante_evento
                WHERE integrante_evento.id_evento = :id";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }


    public function buscarArtistaEvento($id)
    {
        $sql = "SELECT DISTINCT artistas.id, artistas.nome, artistas.cidade, artistas.estado,  (
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
            ORDER BY estilo_musical.id ASC
            LIMIT 1
    ) AS estilos_musicais
FROM eventos JOIN artista_evento ON artista_evento.id_evento = eventos.id JOIN artistas ON artista_evento.id_artista = artistas.id WHERE eventos.id = :id
";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id);
        $consulta->execute();
        return $consulta->fetchAll() ?: null;
    }
    public function inserirArtistaEvento($id_artista, $id_evento): void {
        $sql = "INSERT INTO artista_evento (id_artista, id_evento)
                VALUES (:id_artista, :id_evento)";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id_artista", $id_artista);
        $consulta->bindValue(":id_evento", $id_evento);
        $consulta->execute();

    }

    public function buscarIntegrantes(int $id)
    {
        $sql = "SELECT integrante_evento.id, integrante_evento.nome, integrante_evento.estilo_musical, integrante_evento.url_imagem
                FROM integrante_evento
                WHERE integrante_evento.id_evento = :id";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }
}
