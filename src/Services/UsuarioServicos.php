<?php

class UsuarioServicos
{

    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function inserirUsuario(Usuario $usuario):void
    {
        $sql = "INSERT INTO usuarios (
            nome,
            data_nascimento,
            cep,
            estado,
            cidade,
            rua,
            numero,
            email,
            senha
        ) VALUES (
            :nome,
            :data_nascimento,
            :cep,
            :estado,
            :cidade,
            :rua,
            :numero,
            :email,
            :senha
        )";

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':nome', $usuario->getNome());
        $consulta->bindValue(':data_nascimento', $usuario->getDataNascimento());
        $consulta->bindValue(':cep', $usuario->getCep());
        $consulta->bindValue(':estado', $usuario->getEstado());
        $consulta->bindValue(':cidade', $usuario->getCidade());
        $consulta->bindValue(':rua', $usuario->getRua());
        $consulta->bindValue(':numero', $usuario->getNumero());
        $consulta->bindValue(':email', $usuario->getEmail());
        $consulta->bindValue(':senha', $usuario->getSenha());

        $consulta->execute();
    }

    public function buscarPorEmail($email):?array{
        $sql = "SELECT * FROM usuarios WHERE email = :email";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":email", $email);

        $consulta->execute();

        return $consulta->fetch() ?: null;

    }

    
}
