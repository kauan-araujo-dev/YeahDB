<?php
class Eventos
{
    public ?int $id;
    public string $nome;
    public string $descricao;
    public string $estado;
    public string $cidade;
    public string $endereco;
    public string $dia;       // DATE -> string
    public string $horario;   // TIME -> string
    public string $instagram;
    public string $contato;
    public int $id_usuario;

    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $estado,
        string $cidade,
        string $endereco,
        string $dia,
        string $horario,
        string $instagram,
        string $contato,
        int $id_usuario,
        array $urlFotos = []
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setDescricao($descricao);
        $this->setEstado($estado);
        $this->setCidade($cidade);
        $this->setEndereco($endereco);
        $this->setDia($dia);
        $this->setHorario($horario);
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

    // Endereço
    public function getEndereco(): string
    {
        return $this->endereco;
    }
    public function setEndereco(string $endereco): void
    {
        $this->endereco = $endereco;
    }

    // Dia
    public function getDia(): string
    {
        return $this->dia;
    }
    public function setDia(string $dia): void
    {
        $this->dia = $dia;
    }

    // Horário
    public function getHorario(): string
    {
        return $this->horario;
    }
    public function setHorario(string $horario): void
    {
        $this->horario = $horario;
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
