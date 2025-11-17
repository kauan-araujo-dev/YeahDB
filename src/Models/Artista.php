<?php

class Artistas
{
    public ?int $id;
    public string $nome;
    public string $descricao;
    public string $estado;
    public string $cidade;
    public float $cache_artista;
    public string $whatsapp;
    public string $instagram;
    public string $contato;
    public int $id_usuario;

    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $estado,
        string $cidade,
        float $cache_artista,
        string $whatsapp,
        string $instagram,
        string $contato,
        int $id_usuario
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setDescricao($descricao);
        $this->setEstado($estado);
        $this->setCidade($cidade);
        $this->setCacheArtista($cache_artista);
        $this->setWhatsapp($whatsapp);
        $this->setInstagram($instagram);
        $this->setContato($contato);
        $this->setIdUsuario($id_usuario);
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

    // Descrição
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    // Estado
    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    // Cidade
    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function setCidade(string $cidade): void
    {
        $this->cidade = $cidade;
    }

    // Cache Artista
    public function getCacheArtista(): float
    {
        return $this->cache_artista;
    }

    public function setCacheArtista(float $cache_artista): void
    {
        $this->cache_artista = $cache_artista;
    }

    // WhatsApp
    public function getWhatsapp(): string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(string $whatsapp): void
    {
        $this->whatsapp = $whatsapp;
    }

    // Instagram
    public function getInstagram(): string
    {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): void
    {
        $this->instagram = $instagram;
    }

    // Contato
    public function getContato(): string
    {
        return $this->contato;
    }

    public function setContato(string $contato): void
    {
        $this->contato = $contato;
    }

    // ID Usuário
    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }
}
