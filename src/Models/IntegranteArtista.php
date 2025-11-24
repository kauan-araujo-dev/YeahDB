<?php
class IntegranteArtista {
    public ?int $id;
    public string $nome;
    public string $instrumento;
    public ?string $url_imagem;
    public int $id_artista;

    public function __construct(
        ?int $id,
        string $nome,
        string $instrumento,
        ?string $url_imagem,
        int $id_artista
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setInstrumento($instrumento);
        $this->setUrlImagem($url_imagem);
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

    // Nome
    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    // Instrumento
    public function getInstrumento(): string
    {
        return $this->instrumento;
    }

    public function setInstrumento(string $instrumento): void
    {
        $this->instrumento = $instrumento;
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