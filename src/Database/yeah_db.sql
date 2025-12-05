-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01/12/2025 às 15:52
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `yeah_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `artistas`
--

CREATE TABLE `artistas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `estado` char(2) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `cache_artista` float NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `instagram` varchar(100) NOT NULL,
  `contato` varchar(100) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `artista_estilo`
--

CREATE TABLE `artista_estilo` (
  `id_artista` int(11) NOT NULL,
  `id_estilo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

CREATE TABLE `artista_evento` (
  `id_artista` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `estilo_musical`
--

CREATE TABLE `estilo_musical` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `imagem` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `estado` char(2) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `dia` date NOT NULL,
  `horario` time NOT NULL,
  `instagram` varchar(100) NOT NULL,
  `contato` varchar(100) NOT NULL,
  `link_compra` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `evento_estilo`
--

CREATE TABLE `evento_estilo` (
  `id_evento` int(11) NOT NULL,
  `id_estilo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `foto_artista`
--

CREATE TABLE `foto_artista` (
  `id` int(11) NOT NULL,
  `url_imagem` varchar(255) NOT NULL,
  `id_artista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `foto_evento`
--

CREATE TABLE `foto_evento` (
  `id` int(11) NOT NULL,
  `url_imagem` varchar(255) NOT NULL,
  `id_evento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `integrante_artista`
--

CREATE TABLE `integrante_artista` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `instrumento` varchar(100) NOT NULL,
  `url_imagem` varchar(255) DEFAULT NULL,
  `id_artista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `integrante_evento`
--

CREATE TABLE `integrante_evento` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `estilo_musical` varchar(100) NOT NULL,
  `url_imagem` varchar(255) DEFAULT NULL,
  `id_evento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `cep` varchar(8) NOT NULL,
  `estado` char(2) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `artistas`
--
ALTER TABLE `artistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `artista_estilo`
--
ALTER TABLE `artista_estilo`
  ADD PRIMARY KEY (`id_artista`,`id_estilo`),
  ADD KEY `id_estilo` (`id_estilo`);

--
-- Índices de tabela `artista_evento`
--
ALTER TABLE `artista_evento`
  ADD PRIMARY KEY (`id_artista`,`id_evento`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Índices de tabela `estilo_musical`
--
ALTER TABLE `estilo_musical`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `evento_estilo`
--
ALTER TABLE `evento_estilo`
  ADD PRIMARY KEY (`id_evento`,`id_estilo`),
  ADD KEY `id_estilo` (`id_estilo`);

--
-- Índices de tabela `foto_artista`
--
ALTER TABLE `foto_artista`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Índices de tabela `foto_evento`
--
ALTER TABLE `foto_evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Índices de tabela `integrante_artista`
--
ALTER TABLE `integrante_artista`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Índices de tabela `integrante_evento`
--
ALTER TABLE `integrante_evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `artistas`
--
ALTER TABLE `artistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `estilo_musical`
--
ALTER TABLE `estilo_musical`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `foto_artista`
--
ALTER TABLE `foto_artista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `foto_evento`
--
ALTER TABLE `foto_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `integrante_artista`
--
ALTER TABLE `integrante_artista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `integrante_evento`
--
ALTER TABLE `integrante_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `artistas`
--
ALTER TABLE `artistas`
  ADD CONSTRAINT `artistas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `artista_estilo`
--
ALTER TABLE `artista_estilo`
  ADD CONSTRAINT `artista_estilo_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id`),
  ADD CONSTRAINT `artista_estilo_ibfk_2` FOREIGN KEY (`id_estilo`) REFERENCES `estilo_musical` (`id`);

--
-- Restrições para tabelas `artista_evento`
--
ALTER TABLE `artista_evento`
  ADD CONSTRAINT `artista_evento_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id`),
  ADD CONSTRAINT `artista_evento_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `evento_estilo`
--
ALTER TABLE `evento_estilo`
  ADD CONSTRAINT `evento_estilo_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_estilo_ibfk_2` FOREIGN KEY (`id_estilo`) REFERENCES `estilo_musical` (`id`);

--
-- Restrições para tabelas `foto_artista`
--
ALTER TABLE `foto_artista`
  ADD CONSTRAINT `foto_artista_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id`);

--
-- Restrições para tabelas `foto_evento`
--
ALTER TABLE `foto_evento`
  ADD CONSTRAINT `foto_evento_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `integrante_artista`
--
ALTER TABLE `integrante_artista`
  ADD CONSTRAINT `integrante_artista_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id`);

--
-- Restrições para tabelas `integrante_evento`
--
ALTER TABLE `integrante_evento`
  ADD CONSTRAINT `integrante_evento_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
