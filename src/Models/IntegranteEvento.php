<?php
class IntegranteEvento {
    public ?int $id;
    public string $nome;
    public string $estilo_musical;
    public ?string $url_imagem;
    public int $id_evento;

    public function __construct(
        ?int $id,
        string $nome,
        string $estilo_musical,
        ?string $url_imagem,
        int $id_evento
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setEstiloMusical($estilo_musical);
        $this->setUrlImagem($url_imagem);
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

    // Nome
    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    // EstiloMusical
    public function getEstiloMusical(): string
    {
        return $this->estilo_musical;
    }

    public function setEstiloMusical(string $estilo_musical): void
    {
        $this->estilo_musical = $estilo_musical;
    }

    // URL Imagem
    public function getUrlImagem(): ?string
    {
        return $this->url_imagem;
    }

    public function setUrlImagem(?string $url_imagem): void
    {
        $this->url_imagem = $url_imagem;
    }

    // ID Artista (Chave estrangeira)
    public function getIdEvento(): int
    {
        return $this->id_evento;
    }

    public function setIdEvento(int $id_evento): void
    {
        $this->id_evento = $id_evento;
    }
}
?>