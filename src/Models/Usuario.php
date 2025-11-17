<?php

class Usuario
{
    public string $nome;
    public string $data_nascimento;
    public string $cep;
    public string $estado;
    public string $cidade;
    public string $rua;
    public string $numero;
    public string $email;
    public string $senha;
    public ?int $id;

    public function __construct(
        string $nome,
        string $data_nascimento,
        string $cep,
        string $estado,
        string $cidade,
        string $rua,
        string $numero,
        string $email,
        string $senha,
        ?int $id
    ) {
        $this->setNome($nome);
        $this->setDataNascimento($data_nascimento);
        $this->setCep($cep);
        $this->setEstado($estado);
        $this->setCidade($cidade);
        $this->setRua($rua);
        $this->setNumero($numero);
        $this->setEmail($email);
        $this->setSenha($senha);
        $this->setId($id);
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
    
    // Data Nascimento
    public function getDataNascimento(): string
    {
        return $this->data_nascimento;
    }
    
    public function setDataNascimento(string $data_nascimento): void
    {
        $this->data_nascimento = $data_nascimento;
    }
    
    // CEP
    public function getCep(): string
    {
        return $this->cep;
    }
    
    public function setCep(string $cep): void
    {
        $this->cep = $cep;
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
    
    // Rua
    public function getRua(): string
    {
        return $this->rua;
    }
    
    public function setRua(string $rua): void
    {
        $this->rua = $rua;
    }
    
    // Número
    public function getNumero(): string
    {
        return $this->numero;
    }
    
    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }
    
    // Email
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    // Senha
    public function getSenha(): string
    {
        return $this->senha;
    }
    
    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
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
}
