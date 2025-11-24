<?php


class FotoArtista
{
    public ?int $id;
    public string $url;
    public int $id_artista;

    public function __construct(
        ?int $id,
        string $url,
        int $id_artista
    ) {
        $this->setId($id);
        $this->setUrl($url);
        $this->setIdArtista($id_artista);
    }

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    // URL
    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    // ID do artista (FK)
    public function getIdArtista(): int
    {
        return $this->id_artista;
    }

    public function setIdArtista(int $id_artista): void
    {
        $this->id_artista = $id_artista;
    }
}

?>
