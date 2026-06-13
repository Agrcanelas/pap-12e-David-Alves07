-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/05/2026 às 21:57
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
(3, 6, 3, '2025-12-10 09:43:31', '2025-12-15', NULL, 'atrasado', 1, '2025-12-12 19:27:33', 'Aulas de matemática'),
(4, 4, 10, '2025-12-10 22:30:37', '2028-06-12', NULL, 'recusado', NULL, NULL, 'Precisso do Portatil para estes 3 anos de escolaridade'),
(5, 4, 14, '2025-12-12 19:29:43', '2027-06-11', NULL, 'pendente', NULL, NULL, 'Para acabar o curso de informática'),
(6, 4, 1, '2025-12-17 11:03:18', '2025-12-17', NULL, 'pendente', NULL, NULL, 'Trabalho de Matemática');

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
