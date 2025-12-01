<?php
class FotoEvento {
    
    public ?int $id;
    public string $url;
    public int $id_evento;

    public function __construct(
        ?int $id,
        string $url,
        int $id_evento
    ) {
        $this->setId($id);
        $this->setUrl($url);
        $this->setIdEvento($id_evento);
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
    public function getIdEvento(): int
    {
        return $this->id_evento;
    }

    public function setIdEvento(int $id_evento): void
    {
        $this->id_evento = $id_evento;
    }
}


