-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/06/2026 às 20:38
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
-- Banco de dados: `emprestimo_informatica`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `data_prevista_devolucao` date NOT NULL,
  `data_devolucao` datetime DEFAULT NULL,
  `status` enum('pendente','aprovado','recusado','ativo','devolvido','atrasado') DEFAULT 'pendente',
  `aprovado_por` int(11) DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`id`, `usuario_id`, `material_id`, `data_pedido`, `data_prevista_devolucao`, `data_devolucao`, `status`, `aprovado_por`, `data_aprovacao`, `observacoes`) VALUES
(1, 4, 1, '2025-12-10 09:43:31', '2025-12-17', '2025-12-10 22:47:39', 'devolvido', 1, '2025-12-10 22:47:07', 'Preciso para trabalho de grupo'),
(2, 5, 13, '2025-12-10 09:43:31', '2025-12-13', NULL, 'pendente', NULL, NULL, 'Projeto de história'),
(3, 6, 3, '2025-12-10 09:43:31', '2025-12-15', '2026-06-13 19:03:30', 'devolvido', 1, '2025-12-12 19:27:33', 'Aulas de matemática'),
(4, 4, 10, '2025-12-10 22:30:37', '2028-06-12', NULL, 'recusado', NULL, NULL, 'Precisso do Portatil para estes 3 anos de escolaridade'),
(5, 4, 14, '2025-12-12 19:29:43', '2027-06-11', NULL, 'pendente', NULL, NULL, 'Para acabar o curso de informática'),
(6, 4, 1, '2025-12-17 11:03:18', '2025-12-17', NULL, 'pendente', NULL, NULL, 'Trabalho de Matemática'),
(7, 4, 13, '2026-06-13 19:05:41', '2026-06-12', '2026-06-13 19:14:13', 'devolvido', 1, '2026-06-13 19:06:40', 'teste'),
(8, 4, 11, '2026-06-13 19:19:23', '2026-03-12', '2026-06-13 19:21:21', 'devolvido', 1, '2026-06-13 19:20:09', 'teste de atraso');

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('Portátil','Tablet') NOT NULL,
  `numero_serie` varchar(100) DEFAULT NULL COMMENT 'Número de série do equipamento',
  `status` enum('disponivel','emprestado','manutencao') DEFAULT 'disponivel',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `materiais`
--

INSERT INTO `materiais` (`id`, `nome`, `tipo`, `numero_serie`, `status`, `data_cadastro`) VALUES
(1, 'Portátil 1', 'Portátil', 'SN-PORT-2024-001', 'disponivel', '2025-12-10 09:43:30'),
(2, 'Portátil 2', 'Portátil', 'SN-PORT-2024-002', 'disponivel', '2025-12-10 09:43:30'),
(3, 'Portátil 3', 'Portátil', 'SN-PORT-2024-003', 'disponivel', '2025-12-10 09:43:30'),
(4, 'Portátil 4', 'Portátil', 'SN-PORT-2024-004', 'disponivel', '2025-12-10 09:43:30'),
(5, 'Portátil 5', 'Portátil', 'SN-PORT-2024-005', 'disponivel', '2025-12-10 09:43:30'),
(6, 'Portátil 6', 'Portátil', 'SN-PORT-2024-006', 'disponivel', '2025-12-10 09:43:30'),
(7, 'Portátil 7', 'Portátil', 'SN-PORT-2024-007', 'disponivel', '2025-12-10 09:43:30'),
(8, 'Portátil 8', 'Portátil', 'SN-PORT-2024-008', 'disponivel', '2025-12-10 09:43:30'),
(9, 'Portátil 9', 'Portátil', 'SN-PORT-2024-009', 'disponivel', '2025-12-10 09:43:30'),
(10, 'Portátil 10', 'Portátil', 'SN-PORT-2024-010', 'disponivel', '2025-12-10 09:43:30'),
(11, 'Portátil 11', 'Portátil', 'SN-PORT-2024-011', 'disponivel', '2025-12-10 09:43:30'),
(12, 'Portátil 12', 'Portátil', 'SN-PORT-2024-012', 'disponivel', '2025-12-10 09:43:30'),
(13, 'Tablet 1', 'Tablet', 'SN-TAB-2024-001', 'disponivel', '2025-12-10 09:43:31'),
(14, 'Tablet 2', 'Tablet', 'SN-TAB-2024-002', 'disponivel', '2025-12-10 09:43:31'),
(15, 'Tablet 3', 'Tablet', 'SN-TAB-2024-003', 'disponivel', '2025-12-10 09:43:31'),
(16, 'Tablet 4', 'Tablet', 'SN-TAB-2024-004', 'disponivel', '2025-12-10 09:43:31'),
(17, 'Tablet 5', 'Tablet', 'SN-TAB-2024-005', 'disponivel', '2025-12-10 09:43:31'),
(18, 'Tablet 6', 'Tablet', 'SN-TAB-2024-006', 'disponivel', '2025-12-10 09:43:31'),
(19, 'Tablet 7', 'Tablet', 'SN-TAB-2024-007', 'disponivel', '2025-12-10 09:43:31'),
(20, 'Tablet 8', 'Tablet', 'SN-TAB-2024-008', 'disponivel', '2025-12-10 09:43:31'),
(21, 'Tablet 9', 'Tablet', 'SN-TAB-2024-009', 'disponivel', '2025-12-10 09:43:31'),
(22, 'Tablet 10', 'Tablet', 'SN-TAB-2024-010', 'disponivel', '2025-12-10 09:43:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('aluno','professor','funcionario') NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `ano` int(11) DEFAULT NULL COMMENT 'Ano de escolaridade (ex: 10, 11, 12)',
  `turma` varchar(10) DEFAULT NULL COMMENT 'Turma (ex: A, B, C)',
  `numero_processo` varchar(50) DEFAULT NULL COMMENT 'Número de processo do aluno',
  `nif` varchar(9) DEFAULT NULL COMMENT 'NIF do aluno',
  `tel_encarregado` varchar(20) DEFAULT NULL COMMENT 'Telefone do encarregado de educação',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `tipo`, `is_admin`, `ano`, `turma`, `numero_processo`, `nif`, `tel_encarregado`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@canelas.pt', '0192023a7bbd73250516f069df18b500', '999999999', 'funcionario', 1, NULL, NULL, NULL, NULL, NULL, '2025-12-10 09:43:30'),
(2, 'Maria Santos', 'maria.santos@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '923456789', 'professor', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-10 09:43:30'),
(3, 'Ana Ferreira', 'ana.ferreira@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '945678901', 'professor', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-10 09:43:30'),
(4, 'João Silva', 'joao.silva@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '912345678', 'aluno', 0, 10, 'A', 'P2024001', '123456789', '967891234', '2025-12-10 09:43:30'),
(5, 'Pedro Costa', 'pedro.costa@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '934567890', 'aluno', 0, 11, 'B', 'P2024002', '234567890', '978902345', '2025-12-10 09:43:30'),
(6, 'Sofia Oliveira', 'sofia.oliveira@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '956789012', 'aluno', 0, 12, 'A', 'P2024003', '345678901', '989013456', '2025-12-10 09:43:30'),
(7, 'Miguel Alves', 'miguel.alves@canelas.pt', 'e10adc3949ba59abbe56e057f20f883e', '967890123', 'aluno', 0, 10, 'C', 'P2024004', '456789012', '990124567', '2025-12-10 09:43:30'),
(8, 'Joana', 'a1010@agrcanelas.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-13 17:15:55');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `aprovado_por` (`aprovado_por`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`);

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
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `materiais`
--
ALTER TABLE `materiais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `emprestimos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `emprestimos_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiais` (`id`),
  ADD CONSTRAINT `emprestimos_ibfk_3` FOREIGN KEY (`aprovado_por`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
