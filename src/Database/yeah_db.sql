-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 11/12/2025 às 03:02
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
  `whatsapp` varchar(50) NOT NULL,
  `instagram` varchar(100) NOT NULL,
  `contato` varchar(100) NOT NULL,
  `codigo_artista` varchar(10) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `artistas`
--

INSERT INTO `artistas` (`id`, `nome`, `descricao`, `estado`, `cidade`, `cache_artista`, `whatsapp`, `instagram`, `contato`, `codigo_artista`, `id_usuario`) VALUES
(1, 'Iron Howl', 'Iron Howl é uma banda de Heavy Metal formada em Belo Horizonte, unindo riffs agressivos, vocais potentes e uma presença de palco intensa. O grupo se inspira na velha guarda do metal, mas incorpora elementos modernos de produção, entregando shows explosivos e cheios de energia. Suas letras abordam temas épicos, fantasia sombria e lutas internas, criando um estilo marcante e identitário.', 'RJ', 'Volta Redonda', 12000, 'https://wa.me/31992448866', 'https://www.instagram.com/ironhowl.official', 'contato@ironhowl.com', 'hc8NA3Ulhj', 2),
(2, 'Golden Groove Experience', 'Golden Groove Experience é uma banda vibrante inspirada na era clássica da disco music dos anos 70. Com figurinos brilhosos, luzes coloridas, arranjos cheios de groove e vocais poderosos, o grupo recria fielmente a atmosfera das grandes pistas de dança da época. Inspirada em nomes como Earth, Wind &#38; Fire, Bee Gees e Chic, a banda entrega shows alegres, dançantes e cheios de energia — transformando qualquer evento em uma festa retrô inesquecível.', 'SP', 'São Paulo', 8500, 'https://wa.me/11988552297', 'https://www.instagram.com/goldengroovexp', 'contato@goldengroovexp.com', 'zmoHyLJGfp', 1),
(3, 'Rima Urbana Crew', 'Rima Urbana Crew é um grupo de Rap do Rio de Janeiro que mistura rimas conscientes, batidas pesadas e elementos de trap moderno. Suas letras abordam a vida na periferia, superação e identidade cultural, trazendo autenticidade e intensidade às apresentações. Com forte presença de palco e performances energéticas, o trio se destaca pela química entre os integrantes e pela sonoridade marcante.', 'RJ', 'Rio de Janeiro', 6500, 'https://wa.me/21987331204', 'https://www.instagram.com/rimaurbana.crew', 'contato@rimaurbanacrew.com', 'QVOO0kuUYQ', 4),
(4, 'Aurora Urbana', 'Aurora Urbana é uma banda de Indie Rock com influências modernas de synth pop e rock alternativo. Formada em São Paulo em 2019, o grupo combina melodias atmosféricas, vocais expressivos e uma identidade visual profunda que remete ao cenário urbano contemporâneo. Suas apresentações ao vivo são conhecidas por alta energia e forte envolvimento com o público.', 'SP', 'São Paulo', 7500, 'https://wa.me/11983441922', 'https://www.instagram.com/auroraurbana.oficial', 'contato@auroraurbana.com', 'u7jc42jMWY', 5),
(5, 'Thiago Solano', 'Thiago Solano é um violonista solo conhecido por sua técnica refinada, performances emocionantes e arranjos acústicos envolventes. Traz influências da Música Popular Brasileira, folk internacional e técnicas modernas de fingerstyle. Com um repertório versátil — que vai de clássicos nacionais a interpretações criativas de hits contemporâneos — Thiago transforma qualquer evento em uma experiência intimista e sofisticada.', 'SE', 'Aracajú', 2800, 'https://wa.me/41998775521', 'https://www.instagram.com/thiagosolano.music', 'contato@thiagosolano.com', 'kEPu3WyMP2', 6);

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
(1, 2),
(2, 4),
(3, 7),
(4, 1),
(4, 2),
(4, 6),
(5, 1),
(5, 6);

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
(4, 1),
(4, 4);

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
(6, 'MPB', 'mpb.jpg'),
(7, 'RAP', 'rap.jpg'),
(8, 'Samba', 'samba.jpg'),
(9, 'Clássica', 'classica.jpg');

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
(1, 'Eco-Sons Festival 2006', 'Uma celebração da música e da sustentabilidade. O festival mais verde do país, com palcos movidos a energia solar e workshops de conscientização ambiental.', 'SP', 'São Paulo', 'Parque Ibirapuera', '2026-02-20', '15:00:00', '@ecosons_fest', 'faleconosco@ecosons.art.br', 'www.ticketagora.com.br/ecosonsibirapuera', 1),
(2, 'Metálica Ruína', 'O evento mais pesado do ano. Uma noite de puro caos e decibéis, reunindo lendas do metal e as novas promessas em um cenário pós-industrial.', 'RJ', 'Volta Redonda', 'Antigo Complexo Industrial de Volta Redonda', '2026-08-15', '23:00:00', '@metalicaruinaoficial', 'bandas@metalicaruina.com', 'www.headbangeringressos.com/metalicaruina', 2),
(3, 'Samba na Laje', 'O autêntico clima de festa de comunidade. Samba, feijoada e a vista mais deslumbrante da cidade maravilhosa. Venha celebrar a cultura brasileira no alto da colina.', 'RJ', 'Rio de Janeiro', 'Comunidade Santa Marta', '2026-01-05', '21:00:00', '@sambanalaje_rj', 'reservas@sambanalaje.com.br', 'www.sympla.com.br/sambanalajestamarta', 5),
(4, 'Pop Galaxy Tour', 'O maior espetáculo pop da América Latina. Superprodução, coreografias e os artistas mais vendidos do momento em uma noite de hits e luzes.', 'MG', 'Ouro Preto', 'Teatro Municipal de Ouro Preto', '2026-02-15', '23:00:00', '@classicosdeinvernooficial', 'ouropreto.arte@festival.org', 'www.teatromunicipalop.com/ingressos', 6),
(5, 'Clássicos de Inverno', 'Uma noite de gala dedicada às grandes obras da música erudita. Uma experiência refinada na arquitetura histórica de Minas Gerais, celebrando os mestres.', 'MG', 'Ouro Preto', 'Teatro Municipal de Ouro Preto', '2026-03-09', '22:00:00', '@classicosdeinvernooficial', 'ouropreto.arte@festival.org', 'www.teatromunicipalop.com/ingressos', 6),
(6, 'Sunset Vibes Festival', 'Sunset Vibes Festival chega à Praia do Madeiro, uma das mais paradisíacas do Nordeste, para 10 horas ininterruptas de música eletrônica com vista para o mar.&#13;&#10;Line-up secreto sendo revelado aos poucos, com DJs nacionais e internacionais que vão fazer você dançar da golden hour até debaixo das estrelas.&#13;&#10;Open bar premium, área VIP com vista privilegiada, after oficial na praia e muita energia positiva.&#13;&#10;Prepare-se para o evento que vai marcar o verão 2026!&#13;&#10;#SunsetVibes2026 #Pipa #ElectronicMusic #FestivalNaPraia', 'RS', 'Pelotas', 'Praia do Madeiro', '2026-07-18', '23:00:00', '@sunsetvibesfestival', '(84) 99999-8888', 'https://www.sympla.com.br/sunsetvibes2026', 7);

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
(1, 6),
(2, 2),
(3, 8),
(4, 1),
(4, 4),
(5, 9),
(6, 1),
(6, 4),
(6, 5);

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
(1, 'IronHowl5.png', 1),
(2, 'IronHowl4.png', 1),
(3, 'IronHowl3.png', 1),
(4, 'IronHowl2.png', 1),
(5, 'IronHowl1.png', 1),
(6, 'GoldenGrooveExperience1.png', 2),
(7, 'RimaUrbanaCrew1.png', 3),
(8, 'RimaUrbanaCrew2.png', 3),
(9, 'RimaUrbanaCrew3.png', 3),
(10, 'RimaUrbanaCrew4.png', 3),
(11, 'Aurora Urbana1.png', 4),
(12, 'Thiago Solano4.png', 5),
(13, 'Thiago Solano3.png', 5),
(14, 'Thiago Solano2.png', 5),
(15, 'Thiago Solano1.png', 5);

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
(1, 'Eco Sons Festival2.png', 1),
(2, 'Metálica Ruína1.png', 2),
(3, 'Metálica Ruína3.png', 2),
(4, 'Metálica Ruína4.png', 2),
(5, 'Metálica Ruína5.png', 2),
(6, 'Samba na Laje1.png', 3),
(7, 'Samba na Laje2.png', 3),
(8, 'Samba na Laje3.png', 3),
(9, 'Samba na Laje4.png', 3),
(10, 'Samba na Laje5.png', 3),
(11, 'Pop Galaxy Tour1.png', 4),
(12, 'Pop Galaxy Tour2.png', 4),
(13, 'Pop Galaxy Tour3.png', 4),
(15, 'Clássicos de Inverno1.png', 5),
(16, 'Clássicos de Inverno2.png', 5),
(17, 'Clássicos de Inverno3.png', 5),
(18, 'Clássicos de Inverno4.png', 5),
(19, 'Clássicos de Inverno5.png', 5),
(20, 'SunsetVibes20262.png', 6);

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
(1, 'Viktor Ramírez', 'Vocal / Guitarra Base', 'IronHowl1.png', 1),
(2, 'Caio Ferraz', 'Baixo / Backing Vocal', 'IronHowl3.png', 1),
(3, 'Bruno \"HellHammer\" Carvalho', 'Bateria', 'IronHowl2.png', 1),
(4, 'Marvin “SoulFire” Ribeiro', 'Vocal Principal', 'GoldenGrooveExperience1.png', 2),
(5, 'Elisa “FunkShine” Prado', 'Baixo / Vocais de Apoio', 'GoldenGrooveExperience3.png', 2),
(6, 'Renato “GrooveLine” Torres', 'Guitarra Rítmica / Talkbox', 'GoldenGrooveExperience4.png', 2),
(7, 'Diana “StarVibe” Monteiro', 'Teclados / Synths', 'GoldenGrooveExperience5.png', 2),
(8, 'André “FlashBeat” Costa', 'Bateria / Percussões', 'GoldenGrooveExperience4.png', 2),
(9, 'Kael “RK” Fonseca', 'Voz / Compositor', 'RimaUrbanaCrew2.png', 3),
(10, 'Davi “Beatz” Amaral', 'Beatmaker / DJ', 'RimaUrbanaCrew3.png', 3),
(11, 'Luan “FlowZ” Martins', 'Voz / Freestyle', 'RimaUrbanaCrew4.png', 3),
(12, 'Lucas Andrade', 'Vocal e Guitarra', 'Aurora Urbana2.png', 4),
(13, 'Sabrina Mello', 'Teclado e Synths', 'Aurora Urbana4.png', 4),
(14, 'Diego Torres', 'Baixo', 'Aurora Urbana3.png', 4),
(15, 'Luwilson Silva', 'Bateria', 'Aurora Urbana5.png', 4),
(16, 'Thiago Solano', 'Violão e Voz', 'Thiago Solano5.png', 5);

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
(1, 'Jonny Mathias', 'Pop', 'Eco Sons Festival1.png', 1),
(2, 'Viktor Ramírez', 'Vocal / Guitarra Base', 'IronHowl1.png', 2),
(3, 'Bruno \"HellHammer\" Carvalho', 'Bateria', 'IronHowl2.png', 2),
(4, 'Caio Ferraz', 'Baixo / Backing Vocal', 'IronHowl3.png', 2),
(5, 'GES Escola de Samba', 'Samba', 'Samba na Laje4.png', 3),
(6, 'Carol Biffe', 'Trance', 'Pop Galaxy Tour4.png', 4),
(7, 'Tripla Lipa', 'Contralto', 'Clássicos de Inverno5.png', 5),
(8, 'Djs Djiliro e Liro', 'Funk', 'hiphop.jpg', 6);

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
(1, 'Rodrigo Alexandre Vehman', '1977-08-14', '05711-00', 'SP', 'São Paulo', 'Rua 1', '2', 'rodvehman@hotmail.com', '$2y$10$N6Kf0r./cpDzXGRsve1/zumV0p6gJSPwAx1onhzfGJWs1WxCFyZu6'),
(2, 'João Silva', '1985-03-15', '01001-00', 'SP', 'São Paulo', 'Av. Paulista', '1500', 'joao.silva@teste.com', '$2y$10$JRebmzy9cL6uFS4W21zUr.lIO0ueVJD41SCnkPyIXMMbhVD1K5.Sa'),
(3, 'Maria Santos', '1992-10-22', '22010-01', 'RJ', 'Rio de Janeiro', 'Rua Barata Ribeiro', '45', 'maria.santos@exemplo.org', '$2y$10$llDCssDg6BwmiAU1M3H.DuIL9lLxrt.j.B0.ciSO/.MVW6ZrlYW6y'),
(4, 'Pedro Oliveira', '1970-07-01', '90010-10', 'RS', 'Porto Alegre', 'Praça da Matriz', '120', 'pedro.oliveira@ficticio.net', '$2y$10$mN3teRkXEz82u7lFhRvXL.MXpUbX9k76uT68XFtHlkgmj4buhyyUa'),
(5, 'Ana Costa', '2000-01-30', '30130-90', 'MG', 'Belo Horizonte', 'Av. Afonso Pena', '250', 'ana.costa@modelo.com', '$2y$10$luzIQNYYSo1do2yrb.2uLexrxb3DUr5SKmRH.yN/0qHBLDyvcyKnC'),
(6, 'Carlos Pereira', '1965-12-10', '69005-01', 'RJ', 'Rio de Janeiro', 'Alameda Cosme Ferreira', '987', 'carlos.pereira@dados.br', '$2y$10$VT2hK64A1GxKHxo1/iw4weE/419bO1mxCEPVOldSE0KBtQhb9DKFK'),
(7, 'Josefa Fagundes', '1950-05-15', '10291048', 'MG', 'Belo Horizonte', 'Rua 1', '3', 'josefagundes@email.com', '$2y$10$sDjoWVZA3Rjkj4zYKlTzxOGDsuWuYSMKG9801kNEDECCRPVA.tIaC'),
(8, 'Kauan', '2007-01-04', '03923055', 'SP', 'São Paulo', 'Penhores', '123', 'rogerio@gmail.com', '$2y$10$n3/21vM3TOM6y2fiyupew.LTKFeQ3YPTyDkiV8/hYjryc1UIysBJi');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `artistas`
--
ALTER TABLE `artistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `estilo_musical`
--
ALTER TABLE `estilo_musical`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `foto_artista`
--
ALTER TABLE `foto_artista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `foto_evento`
--
ALTER TABLE `foto_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `integrante_artista`
--
ALTER TABLE `integrante_artista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `integrante_evento`
--
ALTER TABLE `integrante_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `artista_evento_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);

--
-- Restrições para tabelas `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `evento_estilo`
--
ALTER TABLE `evento_estilo`
  ADD CONSTRAINT `evento_estilo_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`),
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
  ADD CONSTRAINT `foto_evento_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);

--
-- Restrições para tabelas `integrante_artista`
--
ALTER TABLE `integrante_artista`
  ADD CONSTRAINT `integrante_artista_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id`);

--
-- Restrições para tabelas `integrante_evento`
--
ALTER TABLE `integrante_evento`
  ADD CONSTRAINT `integrante_evento_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
