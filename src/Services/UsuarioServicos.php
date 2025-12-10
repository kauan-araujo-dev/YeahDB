<?php

class UsuarioServicos
{

    public ?PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conecta::getConexao();
    }

    public function inserirUsuario(Usuario $usuario):int
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

        return intval($this->conexao->lastInsertId());
    }

    public function buscarPorArtistaCodigo($codigo):?array{
        $sql = "SELECT artistas.id FROM artistas WHERE artistas.codigo_artista = :codigo";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":codigo", $codigo);

        $consulta->execute();

        return $consulta->fetch() ?: null;

    }

    public function buscarPorid($id):?array{
        $sql = "SELECT * FROM usuarios WHERE id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();

        return $consulta->fetch() ?: null;

    }

    public function atualizarUsuario(Usuario $usuario): void
{
    $sql = "UPDATE usuarios SET
                nome = :nome,
                data_nascimento = :data_nascimento,
                cep = :cep,
                estado = :estado,
                cidade = :cidade,
                rua = :rua,
                numero = :numero,
                email = :email
            WHERE id = :id";

    $consulta = $this->conexao->prepare($sql);

    $consulta->bindValue(':nome', $usuario->getNome());
    $consulta->bindValue(':data_nascimento', $usuario->getDataNascimento());
    $consulta->bindValue(':cep', $usuario->getCep());
    $consulta->bindValue(':estado', $usuario->getEstado());
    $consulta->bindValue(':cidade', $usuario->getCidade());
    $consulta->bindValue(':rua', $usuario->getRua());
    $consulta->bindValue(':numero', $usuario->getNumero());
    $consulta->bindValue(':email', $usuario->getEmail());
    $consulta->bindValue(':id', $usuario->getId());

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
