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

--
-- Despejando dados para a tabela `artistas`
--

INSERT INTO `artistas` (`id`, `nome`, `descricao`, `estado`, `cidade`, `cache_artista`, `whatsapp`, `instagram`, `contato`, `id_usuario`) VALUES
(1, 'Banda Solaris', 'Banda de pop rock com 5 integrantes', 'SP', 'São Paulo', 5000, '11999990000', '@bandasolaris', 'contato@solaris.com', 1),
(2, 'DJ Luna', 'DJ especializada em música eletrônica', 'RJ', 'Rio de Janeiro', 3000, '21988887777', '@djluna', 'contato@luna.com', 2),
(3, 'Trio Sertanejo', 'Grupo de sertanejo universitário', 'MG', 'Belo Horizonte', 4000, '31977776666', '@triosertanejo', 'contato@sertanejo.com', 3),
(4, 'Coral Harmonia', 'Grupo vocal especializado em casamentos', 'BA', 'Salvador', 2500, '71966665555', '@coralharmonia', 'contato@harmonia.com', 4),
(5, 'MC Ray', 'Cantor de funk carioca', 'RJ', 'Rio de Janeiro', 3500, '21955554444', '@mcrayoficial', 'contato@mcray.com', 5),
(11, 'Lana Souza', '32434243324423', 'SP', 'São Paulo', 3244340, '34243342423', '342432324', '324324324', 12),
(12, 'Jabure', 'ehwefyhigbaw3eduwedbyuwhjdlvedwjtguvTGUvsadfgtsgdehwefyhigbaw3eduwedbyuwhjdlvedwjtguvTGUvsadfgtsgdehwefyhigbaw3eduwedbyuwhjdlvedwjtguvTGUvsadfgtsgdehwefyhigbaw3eduwedbyuwhjdlvedwjtguvTGUvsadfgtsgd', 'SP', 'SDDDDD', 23324200, '32443324', 'tyrgh456grfgd', '34234232432', 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `artista_estilo`
--

CREATE TABLE `artista_estilo` (
  `id_artista` int(11) NOT NULL,
  `id_estilo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `artista_estilo`
--

INSERT INTO `artista_estilo` (`id_artista`, `id_estilo`) VALUES
(1, 1),
(1, 2),
(2, 4),
(3, 3),
(4, 6),
(5, 5),
(11, 1),
(11, 5),
(12, 1),
(12, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `artista_evento`
--

CREATE TABLE `artista_evento` (
  `id_artista` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `artista_evento`
--

INSERT INTO `artista_evento` (`id_artista`, `id_evento`) VALUES
(1, 2),
(2, 4),
(3, 3),
(4, 5),
(5, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estilo_musical`
--

CREATE TABLE `estilo_musical` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `imagem` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estilo_musical`
--

INSERT INTO `estilo_musical` (`id`, `nome`, `imagem`) VALUES
(1, 'Pop', 'pop.jpg'),
(2, 'Rock', 'rock.jpg'),
(3, 'Sertanejo', 'sertanejo.jpg'),
(4, 'Eletrônica', 'eletronica.jpg'),
(5, 'Funk', 'funk.jpg'),
(6, 'MPB', 'mpb.jpg');

-- --------------------------------------------------------

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
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `nome`, `descricao`, `estado`, `cidade`, `endereco`, `dia`, `horario`, `instagram`, `contato`, `link_compra`, `id_usuario`) VALUES
(1, 'Festival de Verão', 'Grande festival de música para o público jovem', 'BA', 'Salvador', 'Praia do Farol', '2025-12-10', '18:00:00', '@festivalverao', 'eventos@verao.com', 'https://www.sympla.com.br/festival-de-verao', 1),
(2, 'Rock Night', 'Evento dedicado ao rock nacional', 'SP', 'São Paulo', 'Espaço das Artes', '2025-11-20', '20:00:00', '@rocknight', 'contato@rocknight.com', 'https://www.ingresso.com/evento/rock-night', 2),
(3, 'Sertanejo Fest', 'Festa com as maiores duplas sertanejas', 'MG', 'Belo Horizonte', 'Parque Municipal', '2025-12-05', '19:30:00', '@sertanejofest', 'contato@sertanejo.com', 'https://www.eventbrite.com/e/sertanejo-fest', 3),
(4, 'Eletro Sunset', 'Balada eletrônica open air', 'RJ', 'Rio de Janeiro', 'Arena Rio', '2025-11-25', '22:00:00', '@eletrosunset', 'contato@eletro.com', 'https://www.bilheto.com.br/eletro-sunset', 4),
(5, 'Natal Solidário', 'Evento beneficente com várias apresentações musicais', 'SC', 'Florianópolis', 'Centro Cultural', '2025-12-22', '17:00:00', '@natalsolidario', 'contato@natal.com', 'https://www.sympla.com.br/natal-solidario', 5),
(6, 'dfsfsdfsd', 'sadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasdsadsdasadsadasdsdasdasdsdadasasdasdsdadasd', 'ds', 'dsffsd', 'fsdfdsdsf', '2012-12-04', '04:04:00', 'dssadasd', 'sadsdaads', 'asddasasd', 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_estilo`
--

CREATE TABLE `evento_estilo` (
  `id_evento` int(11) NOT NULL,
  `id_estilo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `evento_estilo`
--

INSERT INTO `evento_estilo` (`id_evento`, `id_estilo`) VALUES
(1, 1),
(1, 5),
(2, 2),
(3, 3),
(4, 4),
(5, 6),
(6, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `foto_artista`
--

CREATE TABLE `foto_artista` (
  `id` int(11) NOT NULL,
  `url_imagem` varchar(255) NOT NULL,
  `id_artista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `foto_artista`
--

INSERT INTO `foto_artista` (`id`, `url_imagem`, `id_artista`) VALUES
(1, 'https://img.site.com/artistas/solaris1.jpg', 1),
(2, 'https://img.site.com/artistas/luna1.jpg', 2),
(3, 'https://img.site.com/artistas/sertanejo1.jpg', 3),
(4, 'https://img.site.com/artistas/harmonia1.jpg', 4),
(5, 'https://img.site.com/artistas/mcray1.jpg', 5),
(14, 'uma cantora de pop s.png', 11),
(15, 'um grupo de hip hop .png', 11),
(16, 'uma dupla sertaneja .png', 11),
(17, 'uma banda de rock to.png', 11),
(18, 'ff54c022fc5c3952c5979d38885388b2.jpg', 12),
(19, 'images (1).jpg', 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `foto_evento`
--

CREATE TABLE `foto_evento` (
  `id` int(11) NOT NULL,
  `url_imagem` varchar(255) NOT NULL,
  `id_evento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `foto_evento`
--

INSERT INTO `foto_evento` (`id`, `url_imagem`, `id_evento`) VALUES
(1, 'https://img.site.com/eventos/festivalverao.jpg', 1),
(2, 'https://img.site.com/eventos/rocknight.jpg', 2),
(3, 'https://img.site.com/eventos/sertanejofest.jpg', 3),
(4, 'https://img.site.com/eventos/eletrosunset.jpg', 4),
(5, 'https://img.site.com/eventos/natalsolidario.jpg', 5),
(6, 'coloque um degradê p.png', 6),
(7, 'um DJ se apresentand.png', 6),
(8, 'uma cantora de pop s - Copia.png', 6),
(9, 'uma cantora de pop s.png', 6),
(10, 'um grupo de hip hop .png', 6);

-- --------------------------------------------------------

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
-- Despejando dados para a tabela `integrante_artista`
--

INSERT INTO `integrante_artista` (`id`, `nome`, `instrumento`, `url_imagem`, `id_artista`) VALUES
(1, 'Carlos Mendes', 'Guitarra', 'https://example.com/imagens/integrante1.jpg', 1),
(2, 'Felipe Rocha', 'Baixo', 'https://example.com/imagens/felipe-rocha.jpg', 1),
(3, 'Rafaela Torres', 'Voz', 'https://example.com/imagens/rafaela-torres.jpg', 1),
(4, 'Luana Dias', 'CDJ', 'https://example.com/imagens/luana-dias.jpg', 2),
(5, 'João Pedro', 'Voz', 'https://example.com/imagens/joao-pedro.jpg', 3),
(6, 'Bruno Castro', 'Violão', 'https://example.com/imagens/bruno-castro.jpg', 3),
(7, 'Clara Melo', 'Soprano', 'https://example.com/imagens/clara-melo.jpg', 4),
(8, 'Marcos Silva', 'Tenor', 'https://example.com/imagens/marcos-silva.jpg', 4),
(9, 'Ray Oliveira', 'Voz', 'https://example.com/imagens/ray-oliveira.jpg', 5),
(11, '3234432', '324432342', 'um grupo de hip hop .png', 11),
(12, 'saddsdsa', 'dasdasdsa', 'um cantor sertanejo .png', 12);

-- --------------------------------------------------------

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
-- Despejando dados para a tabela `integrante_evento`
--

INSERT INTO `integrante_evento` (`id`, `nome`, `estilo_musical`, `url_imagem`, `id_evento`) VALUES
(1, 'safsasdsad', 'sdasdaasd', 'ff54c022fc5c3952c5979d38885388b2.jpg', 6);

-- --------------------------------------------------------

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
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `data_nascimento`, `cep`, `estado`, `cidade`, `rua`, `numero`, `email`, `senha`) VALUES
(1, 'João Silva', '1990-05-12', '01001000', 'SP', 'São Paulo', 'Rua das Flores', '123', 'joao@gmail.com', 'senha123'),
(2, 'Maria Souza', '1985-09-20', '20040030', 'RJ', 'Rio de Janeiro', 'Av. Atlântica', '456', 'maria@gmail.com', 'senha456'),
(3, 'Pedro Oliveira', '1993-07-02', '30140071', 'MG', 'Belo Horizonte', 'Rua Goiás', '789', 'pedro@gmail.com', 'senha789'),
(4, 'Ana Lima', '1998-02-11', '40010000', 'BA', 'Salvador', 'Rua do Carmo', '321', 'ana@gmail.com', 'senha321'),
(5, 'Lucas Pereira', '1995-11-15', '88010010', 'SC', 'Florianópolis', 'Rua das Palmeiras', '654', 'lucas@gmail.com', 'senha654'),
(12, 'Kauan', '3434-02-04', '3223432', 'SP', 'São Paulo', 'Rua Marrela', '123', 'rogerio@gmail.com', '$2y$10$8gQjcVDCk7ZQ9/.DBr6kK.ATqpJDXOHxYJRhQzFkiNd/OKTxCCx2K');

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
