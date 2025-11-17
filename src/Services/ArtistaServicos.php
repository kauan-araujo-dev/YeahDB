<?php 

class ArtistaServicos{

    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function buscarArtistas():?array {
        $sql = "SELECT artistas.id, artistas.nome, (
        SELECT foto_artista.url
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
        LIMIT 1
        ) AS url,
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

    public function buscarArtistasComLimite(int $limite):?array {
        $sql = "SELECT artistas.id, artistas.nome, (
        SELECT foto_artista.url
        FROM foto_artista
        WHERE foto_artista.id_artista = artistas.id
        ORDER BY foto_artista.id ASC
        LIMIT 1
        ) AS url,
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
        ORDER BY artistas.id ASC LIMIT :limite";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":limite", $limite);


        if($limite === 1){
            return $consulta->fetch() ?: null;
        }elseif($limite == 0){
            return null;
        }else{
            return $consulta->fetchAll() ?: null;
        }

        
    }

    public function buscarEventos():?array {
        $sql = "SELECT foto_evento.url
        FROM foto_evento
        WHERE foto_evento.id_evento = eventos.id
        ORDER BY foto_evento.id ASC
        LIMIT 1
        ) AS url,
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
}


?>