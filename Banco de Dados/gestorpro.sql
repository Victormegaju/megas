-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 13/12/2024 às 23:45
-- Versão do servidor: 5.7.43-log
-- Versão do PHP: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestorpro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carteira`
--

CREATE TABLE `carteira` (
  `Id` int(11) NOT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `valor` varchar(250) DEFAULT NULL,
  `entrada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `vjurus` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(60) DEFAULT NULL,
  `tipo` varchar(2) DEFAULT '1',
  `status` varchar(2) DEFAULT '1',
  `nome` varchar(120) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `nascimento` varchar(15) DEFAULT NULL,
  `cpf` varchar(25) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `cep` varchar(25) DEFAULT NULL,
  `endereco` varchar(120) DEFAULT NULL,
  `numero` varchar(9) DEFAULT NULL,
  `bairro` varchar(60) DEFAULT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `uf` varchar(5) DEFAULT NULL,
  `tokenmp` varchar(255) DEFAULT NULL,
  `tokenasaas` varchar(255) DEFAULT NULL,
  `nomecom` varchar(160) NOT NULL,
  `cnpj` varchar(30) NOT NULL,
  `enderecom` varchar(160) NOT NULL,
  `contato` varchar(15) NOT NULL,
  `msg` varchar(2) DEFAULT '1',
  `msgqr` varchar(2) DEFAULT '1',
  `msgpix` varchar(2) DEFAULT '1',
  `tokenapi` varchar(60) DEFAULT NULL,
  `pagamentos` varchar(2) DEFAULT '1',
  `assinatura` varchar(10) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `juros_diarios` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `carteira`
--

INSERT INTO `carteira` (`Id`, `idm`, `valor`, `entrada`, `vjurus`, `login`, `senha`, `tipo`, `status`, `nome`, `celular`, `nascimento`, `cpf`, `email`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `uf`, `tokenmp`, `tokenasaas`, `nomecom`, `cnpj`, `enderecom`, `contato`, `msg`, `msgqr`, `msgpix`, `tokenapi`, `pagamentos`, `assinatura`, `background`, `juros_diarios`) VALUES
(1, '1', '', '2024-12-13 21:06:28', NULL, '230223', '48396ec03279bae53f0a6b9ef9c4c2e0e4f850ef', '1', '1', 'ADMINISTRADOR MASTER', '41988150812', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SeuToken', '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAyODcxMjY6OiRhYWNoXzMyZjYxNzdmLWY3MTMtNDI3MC05MjBkLTlkZDQ1ODNlNzE5Ng==', 'Gestor PRO', '45988080667', 'RUA XXX', '41988150812', '1', '1', '1', '2f76061fe5e91b5b61ade6382daff607', '1', '15/02/2024', 'https://wallpaper.forfun.com/fetch/ae/ae2cbe4c74a79df332455ab8fe742e7c.jpeg', 0.50);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) DEFAULT NULL,
  `nome` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `idu`, `nome`) VALUES
(1, '1', 'CLIENTE SAAS');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `Id` int(11) NOT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT '0.00',
  `entrada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `vjurus` varchar(5) DEFAULT '100',
  `login` varchar(15) DEFAULT NULL,
  `senha` varchar(60) DEFAULT NULL,
  `tipo` varchar(2) DEFAULT '1',
  `status` varchar(2) DEFAULT '1',
  `nome` varchar(120) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `nascimento` varchar(15) DEFAULT NULL,
  `cpf` varchar(25) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `cep` varchar(25) DEFAULT NULL,
  `endereco` varchar(120) DEFAULT NULL,
  `numero` varchar(9) DEFAULT NULL,
  `bairro` varchar(60) DEFAULT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `uf` varchar(5) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `emissao` varchar(30) DEFAULT NULL,
  `uf2` varchar(6) DEFAULT NULL,
  `mae` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conexoes`
--

CREATE TABLE `conexoes` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `qrcode` text,
  `conn` int(11) DEFAULT '0',
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `tokenid` varchar(60) DEFAULT NULL,
  `apikey` varchar(60) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro1`
--

CREATE TABLE `financeiro1` (
  `Id` int(11) NOT NULL,
  `idc` varchar(5) DEFAULT 'n',
  `idm` varchar(9) DEFAULT NULL,
  `idcob` varchar(5) DEFAULT 'n',
  `valorsolicitado` decimal(10,2) DEFAULT '0.00',
  `taxaj` varchar(5) DEFAULT 'n',
  `valorjurus` decimal(10,2) DEFAULT '0.00',
  `valorfinal` decimal(10,2) DEFAULT NULL,
  `formapagamento` varchar(3) DEFAULT 'n',
  `parcelas` varchar(3) DEFAULT 'n',
  `primeiraparcela` varchar(20) DEFAULT 'n',
  `chave` varchar(60) DEFAULT 'n',
  `status` varchar(2) DEFAULT '1',
  `vparcela` decimal(10,2) DEFAULT '0.00',
  `pagoem` varchar(255) DEFAULT 'n',
  `entrada` varchar(15) DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro2`
--

CREATE TABLE `financeiro2` (
  `Id` int(11) NOT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `chave` varchar(60) DEFAULT 'n',
  `parcela` decimal(10,2) DEFAULT '0.00',
  `datapagamento` varchar(20) DEFAULT 'n',
  `pagoem` varchar(20) DEFAULT 'n',
  `status` varchar(2) DEFAULT '1',
  `tempo` varchar(2) DEFAULT '2',
  `temp5` varchar(2) DEFAULT '2',
  `temp3` varchar(2) DEFAULT '2',
  `temp0` varchar(2) DEFAULT '2',
  `obsv` text,
  `juros_calculados` int(11) DEFAULT '0',
  `taxa_juros_diaria` decimal(10,2) DEFAULT '0.00',
  `dias_vencidos` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro3`
--

CREATE TABLE `financeiro3` (
  `id` int(11) NOT NULL,
  `idm` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dataentrada` datetime DEFAULT CURRENT_TIMESTAMP,
  `valor` decimal(10,2) DEFAULT NULL,
  `datavencimento` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datapagamento` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descricao` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacao` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `data` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `idu` varchar(5) DEFAULT NULL,
  `msg` text,
  `tipo` varchar(2) DEFAULT NULL,
  `status` varchar(2) DEFAULT '1',
  `hora` varchar(5) DEFAULT '16:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `idu`, `msg`, `tipo`, `status`, `hora`) VALUES
(1, '1', '#EMPRESA#\nCNPJ: #CNPJ#\nEndereço: #ENDERECO#\nContato: #CONTATO#\n\n==================================\n\nOlá *#NOME#*,\n\nPassando para informar que em *5* dias vence sua mensalidade no valor de:\n*R$: #VALOR#*\n\nPara realizar o pagamento agora basta clicar no link abaixo:\nhttps://financeiro.gestorproplw.com#LINK#\n\nSe preferir estou te enviando nossa chave pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.', '1', '1', '15:02'),
(2, '1', 'Olá #NOME#,\n\nPassando para informar que em *3* dias vence sua mensalidade no valor de: #VALOR#.\n\nCopie a chave abaixo e no seu aplicativo na opção PIX procura a opção Pagar com chave Copia e Cola.\n\n#COPIAECOLA#\n\nVocê também poderá pagar clicando no link abaixo:\n\nhttps://financeiro.gestorproplw.com#LINK#\n\n*Caso já tenha efetuado o pagamento favor desconsiderar esta cobrança.*\n\nEsta é uma mensagem automática e não precisa ser respondida.', '2', '1', '15:45'),
(3, '1', 'Olá #NOME#,\r\n\r\nPassamos para informar que hoje vence sua mensalidade no valor de R$: #VALOR#.\r\n\r\nPara realizar o pagamento basta clicar no link abaixo:\r\n\r\nhttps://painel.gestorproplw.com#LINK#\r\n\r\nCaso já tenha efetuado o pagamento por favor desconsiderar.\r\n\r\nEsta é uma mensagem automática e não precisa ser respondida.', '3', '1', '10:45'),
(4, '1', '#EMPRESA#\nCNPJ: #CNPJ#\nEndereço: #ENDERECO#\nContato: #CONTATO#\n\n=============================\n\nOlá *#NOME#*,\n\nPassando para informar que sua mensalidade no valor de:\n*R$: #VALOR#* encontra-se vencida desde o dia #VENCIMENTO#.\n\nPara realizar o pagamento agora basta clicar no link abaixo:\n\nhttps://financeiro.gestorproplw.com#LINK#\n\nSe preferir estou te enviando nossa chave pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.', '4', '1', '17:42'),
(5, '1', '#EMPRESA#\r\nCNPJ: #CNPJ#\r\nEndereço: #ENDERECO#\r\nContato: #CONTATO#\r\n\r\n==================================\r\n*RECIBO DE PAGAMENTO*\r\n==================================\r\nCliente: *#NOME#*\r\nData de Vencimento: #VENCIMENTO#\r\nData de Pagamento: #DATAPAGAMENTO#\r\nValor: R$: #VALOR#\r\n==================================\r\n\r\nEsta é uma mensagem automática e não precisa ser respondida.', '5', '1', '02:33'),
(6, '1', '#EMPRESA#\nCNPJ: #CNPJ#\nEndereço: #ENDERECO#\nContato: #CONTATO#\n\n=============================\n\nOlá *#NOME#*,\n\nPassando para informar que sua mensalidade no valor de:\n*R$: #VALOR#* já está disponível para pagamento.\n\nPara realizar o pagamento agora basta clicar no link abaixo:\n\nhttps://financeiro.gestorproplw.com#LINK#\n\nSe preferir estou te enviando nossa chave pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.', '6', '1', '12:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mercadopago`
--

CREATE TABLE `mercadopago` (
  `id` int(11) NOT NULL,
  `idc` varchar(20) DEFAULT NULL,
  `status` varchar(60) DEFAULT NULL,
  `instancia` varchar(60) DEFAULT NULL,
  `data` datetime NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `idp` varchar(60) NOT NULL,
  `qrcode` text NOT NULL,
  `linhad` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `message_queue`
--

CREATE TABLE `message_queue` (
  `id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `media` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentofun`
--

CREATE TABLE `pagamentofun` (
  `id` int(11) NOT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `data` varchar(20) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `videos`
--

INSERT INTO `videos` (`id`, `link`, `title`) VALUES
(1, 'OS5oTcJZ4K4', 'Guia de Uso');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carteira`
--
ALTER TABLE `carteira`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `conexoes`
--
ALTER TABLE `conexoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `financeiro1`
--
ALTER TABLE `financeiro1`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `financeiro2`
--
ALTER TABLE `financeiro2`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `financeiro3`
--
ALTER TABLE `financeiro3`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mercadopago`
--
ALTER TABLE `mercadopago`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `message_queue`
--
ALTER TABLE `message_queue`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentofun`
--
ALTER TABLE `pagamentofun`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carteira`
--
ALTER TABLE `carteira`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `conexoes`
--
ALTER TABLE `conexoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `financeiro1`
--
ALTER TABLE `financeiro1`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `financeiro2`
--
ALTER TABLE `financeiro2`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `financeiro3`
--
ALTER TABLE `financeiro3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `mercadopago`
--
ALTER TABLE `mercadopago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `message_queue`
--
ALTER TABLE `message_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `pagamentofun`
--
ALTER TABLE `pagamentofun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;