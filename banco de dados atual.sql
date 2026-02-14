-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 14/02/2026 às 06:25
-- Versão do servidor: 8.0.44
-- Versão do PHP: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `nmrefrig_climatech`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int NOT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `senha`, `nome`, `email`, `ativo`, `data_criacao`) VALUES
(6, 'admin', '$2y$10$J5aNy2Z/sUYIWY0qzTcOHetT6P7QzRPo65/pJSNhSLqUK/wtT/ORG', 'Administrador', 'admin@climatech.com.br', 1, '2025-11-17 19:16:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `data_agendamento` date NOT NULL,
  `hora_agendamento` time NOT NULL,
  `endereco` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tempo_estimado_minutos` int DEFAULT '60',
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `acrescimo_especial` decimal(10,2) DEFAULT '0.00',
  `status` enum('agendado','confirmado','realizado','cancelado','finalizado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'agendado',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `origem` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'site',
  `origem_id` int DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `orcamento_id` int DEFAULT NULL,
  `duracao_estimada` decimal(4,2) DEFAULT '1.50',
  `duracao_estimada_min` int DEFAULT '120' COMMENT 'Duração estimada em minutos do serviço'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `cliente_id`, `servico_id`, `data_agendamento`, `hora_agendamento`, `endereco`, `tempo_estimado_minutos`, `observacoes`, `acrescimo_especial`, `status`, `data_criacao`, `origem`, `origem_id`, `data_fim`, `hora_fim`, `orcamento_id`, `duracao_estimada`, `duracao_estimada_min`) VALUES
(127, 74, 8, '2025-12-19', '15:00:00', NULL, 60, 'Agendamento via orçamento #120 - Joselaine', 0.00, 'realizado', '2025-12-18 10:35:19', 'orcamento', NULL, '2025-12-18', '17:00:00', 120, 1.50, 120),
(128, 75, 7, '2025-12-20', '09:00:00', 'Rua Sebastião de Souza Guimarães 200 , 14 - Cambuí, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 20/12/2025\r\nHorário: 09:00\r\nCliente: Daniel da Riopetrana\r\nWhatsApp: (17) 9 8138-9114\r\n\r\n=== AJUSTES APLICADOS ===\r\n• Fim de semana/Feriado: +10% (R$ +40,00)\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +40,00\r\nValor final: R$ 440,00', 40.00, 'realizado', '2025-12-18 10:39:24', 'sistema_ia', 121, NULL, NULL, NULL, 1.50, 120),
(129, 76, 7, '2025-12-20', '09:00:00', 'Rua Fritz Jacobs, 1055 - Boa vista, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 19/12/2025\r\nHorário: 14:00\r\nCliente: Gustavo Rio petrana\r\nWhatsApp: (17) 9 9625-4293\r\n\r\n=== AJUSTES APLICADOS ===\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +0,00\r\nValor final: R$ 400,00', 0.00, 'realizado', '2025-12-18 10:43:01', 'sistema_ia', 122, NULL, NULL, NULL, 1.50, 120),
(130, 77, 5, '2026-01-08', '08:00:00', 'Rua, 123 - Bairro , Cidade', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Instalação de ar condicionado (x1) - R$ 350,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 24000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 21/12/2025\r\nHorário: 08:00\r\nCliente: N&amp;M Refrigeração\r\nWhatsApp: (17) 9 9624-0727\r\n\r\n=== AJUSTES APLICADOS ===\r\n• Fim de semana/Feriado: +10% (R$ +55,00)\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +55,00\r\nValor final: R$ 605,00', 55.00, 'agendado', '2025-12-18 14:15:53', 'sistema_ia', 123, NULL, NULL, NULL, 1.50, 120),
(131, 78, 8, '2025-12-27', '08:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 7000 BTUs\n\n=== AGENDAMENTO ===\nData: 27/12/2025\nHorário: 08:00\nCliente: Mercado jj\nWhatsApp: (34) 9 9916-2350\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +10,00)\n\n=== VALORES ===\nTotal ajustes: R$ +10,00\nValor final: R$ 110,00\n', 10.00, 'agendado', '2025-12-18 15:23:49', 'sistema_ia', 124, NULL, NULL, NULL, 1.50, 120),
(132, 72, 8, '2025-12-28', '08:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto', 60, 'Ficar de segurança no jj', 10.00, 'agendado', '2025-12-18 15:25:10', 'sistema_ia', 125, NULL, NULL, NULL, 1.50, 120),
(133, 79, 8, '2025-12-22', '08:00:00', 'Rua Paulo Setúbal , 337 - Santa cruz, São José do Rio Preto-SP ', 60, '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x2) - R$ 200,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 9000 BTUs\n  - Equipamento 2: 78000 BTUs\n• Limpeza com remoção do equipamento (x1) - R$ 550,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 7000 BTUs\n\n=== AGENDAMENTO ===\nData: 22/12/2025\nHorário: 08:00\nCliente: Paulo santana\nWhatsApp: (17) 9 9177-6565\n\n=== AJUSTES APLICADOS ===\n\n=== VALORES ===\nTotal ajustes: R$ +0,00\nValor final: R$ 750,00\n', 0.00, 'agendado', '2025-12-19 00:01:43', 'sistema_ia', 126, NULL, NULL, NULL, 1.50, 120),
(134, 80, 5, '2025-12-23', '08:00:00', 'Rua José favaro, 102 - JD tropical, Bady bassitt', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Instalação de ar condicionado (x2) - R$ 700,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 9000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 23/12/2025\r\nHorário: 12:00\r\nCliente: William Antônio de oliveira\r\nWhatsApp: (17) 9 9705-1616\r\n\r\n=== AJUSTES APLICADOS ===\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +0,00\r\nValor final: R$ 700,00', 0.00, 'realizado', '2025-12-22 11:45:23', 'sistema_ia', 127, NULL, NULL, NULL, 1.50, 120),
(138, 77, 5, '2026-01-07', '08:00:00', 'eewewe, wewe - wewewew, ewew', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Instalação de ar condicionado (x1) - R$ 350,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 25/12/2025\r\nHorário: 19:00\r\nCliente: N&amp;M Refrigeração\r\nWhatsApp: (17) 9 9624-0727\r\n\r\n\r\n=== VALORES ===\r\nTotal serviços: R$ 350,00\r\nValor final: R$ 350,00', 0.00, 'agendado', '2025-12-22 23:45:01', 'sistema_ia', 131, NULL, NULL, NULL, 1.50, 120),
(139, 84, 9, '2026-01-02', '08:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Remoção de Equipamento (x1) - R$ 300,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 30/12/2025\r\nHorário: 08:00\r\nCliente: Carlos Simonett\r\nWhatsApp: (17) 9 9625-6434\r\n\r\n\r\n=== VALORES ===\r\nTotal serviços: R$ 300,00\r\nValor final: R$ 300,00', 0.00, 'agendado', '2025-12-29 11:51:43', 'sistema_ia', 132, NULL, NULL, NULL, 1.50, 120),
(140, 84, 5, '2026-01-09', '08:00:00', 'Rua Virgílio Dias de castro, 118 - São delcleciano, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x4) - R$ 1.400,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 9000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 18000 BTUs\n  - Equipamento 4: 32000 BTUs\n\n=== AGENDAMENTO ===\nData: 09/01/2026\nHorário: 08:00\nCliente: Carlos Simonett\nWhatsApp: (17) 9 9625-6434\n\n\n=== VALORES ===\nTotal serviços: R$ 1.400,00\nValor final: R$ 1.400,00\n', 0.00, 'agendado', '2026-01-05 21:16:24', 'sistema_ia', 133, NULL, NULL, NULL, 1.50, 120),
(141, 85, 9, '2026-01-16', '08:00:00', 'Sítio 3 irmãos , Na - Sitio, Santa albertina ', 60, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x2) - R$ 600,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n• Instalação de ar condicionado (x4) - R$ 1.400,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 12000 BTUs\n  - Equipamento 4: 32000 BTUs\n\n=== AGENDAMENTO ===\nData: 16/01/2026\nHorário: 08:00\nCliente: Izabel Tanaka\nWhatsApp: (17) 9 9707-9792\n\n\n=== VALORES ===\nTotal serviços: R$ 2.400,00\nValor final: R$ 2.400,00\n', 0.00, 'agendado', '2026-01-06 20:43:34', 'sistema_ia', 134, NULL, NULL, NULL, 1.50, 120),
(142, 86, 9, '2026-01-10', '08:00:00', 'Rua Carlos Aumari Santana Branco, 965 - Jardim Califórnia, Bady bassitt', 60, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x2) - R$ 600,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 24000 BTUs\n  - Equipamento 2: 24000 BTUs\n• Instalação de ar condicionado (x3) - R$ 1.050,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 24000 BTUs\n  - Equipamento 2: 24000 BTUs\n  - Equipamento 3: 24000 BTUs\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 24000 BTUs\n  - Equipamento 2: 24000 BTUs\n\n=== AGENDAMENTO ===\nData: 10/01/2026\nHorário: 08:00\nCliente: Ruam lapa\nWhatsApp: (17) 9 9197-5047\n\n\n=== VALORES ===\nTotal serviços: R$ 2.050,00\nValor final: R$ 2.050,00\n', 0.00, 'agendado', '2026-01-07 19:07:48', 'sistema_ia', 135, NULL, NULL, NULL, 1.50, 120),
(143, 87, 7, '2026-01-11', '08:00:00', 'Gaivota 2, 1511 - São delcleciano, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x5) - R$ 1.000,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 12000 BTUs\n  - Equipamento 4: 12000 BTUs\n  - Equipamento 5: 12000 BTUs\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 11/01/2026\nHorário: 08:00\nCliente: Lobato\nWhatsApp: (17) 9 8134-0827\n\n\n=== VALORES ===\nTotal serviços: R$ 1.100,00\nValor final: R$ 1.100,00\n', 0.00, 'agendado', '2026-01-08 12:01:22', 'sistema_ia', 136, NULL, NULL, NULL, 1.50, 120),
(144, 88, 7, '2026-01-12', '08:00:00', 'Georgina, 106 - ., São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x3) - R$ 600,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 12/01/2026\nHorário: 08:00\nCliente: Márcio valsechi\nWhatsApp: (17) 9 9739-0085\n\n\n=== VALORES ===\nTotal serviços: R$ 600,00\nValor final: R$ 600,00\n', 0.00, 'agendado', '2026-01-10 16:08:57', 'sistema_ia', 137, NULL, NULL, NULL, 1.50, 120),
(145, 89, 7, '2026-01-13', '08:00:00', 'Drama 6, Quadra O - Lote 13, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 13/01/2026\nHorário: 08:00\nCliente: Milena Pires Assunção\nWhatsApp: (17) 9 8133-0612\n\n\n=== VALORES ===\nTotal serviços: R$ 200,00\nValor final: R$ 200,00\n', 0.00, 'agendado', '2026-01-11 16:35:42', 'sistema_ia', 138, NULL, NULL, NULL, 1.50, 120),
(146, 90, 9, '2026-01-13', '13:00:00', 'Rua formosa, Sn - Entrada da cidade , Ipigua ', 60, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x1) - R$ 300,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n• Instalação de ar condicionado (x2) - R$ 700,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 30000 BTUs\n\n=== AGENDAMENTO ===\nData: 13/01/2026\nHorário: 13:00\nCliente: Wagner\nWhatsApp: (17) 9 8126-4481\n\n\n=== VALORES ===\nTotal serviços: R$ 1.000,00\nValor final: R$ 1.000,00\n', 0.00, 'agendado', '2026-01-11 16:42:06', 'sistema_ia', 139, NULL, NULL, NULL, 1.50, 120),
(147, 91, 7, '2026-01-14', '09:00:00', 'Pq industrial , Uugug -  Hv, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 14/01/2026\nHorário: 09:00\nCliente: Nilza pirassollo\nWhatsApp: (17) 9 8808-9090\n\n\n=== VALORES ===\nTotal serviços: R$ 200,00\nValor final: R$ 200,00\n', 0.00, 'agendado', '2026-01-11 16:46:14', 'sistema_ia', 140, NULL, NULL, NULL, 1.50, 120),
(148, 92, 9, '2026-01-14', '11:00:00', 'Gggy, 44 - Santa Catarina , Eng. Schmidt ', 60, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x1) - R$ 300,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 14/01/2026\nHorário: 11:00\nCliente: José santista\nWhatsApp: (17) 9 8102-0574\n\n\n=== VALORES ===\nTotal serviços: R$ 650,00\nValor final: R$ 650,00\n', 0.00, 'agendado', '2026-01-11 16:50:50', 'sistema_ia', 141, NULL, NULL, NULL, 1.50, 120),
(149, 90, 8, '2026-01-15', '09:00:00', 'Grt4, Dgr - Fef, Ipigua ', 60, '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 15/01/2026\nHorário: 09:00\nCliente: Wagner\nWhatsApp: (17) 9 8126-4481\n\n\n=== VALORES ===\nTotal serviços: R$ 100,00\nValor final: R$ 100,00\n', 0.00, 'agendado', '2026-01-11 17:11:34', 'sistema_ia', 142, NULL, NULL, NULL, 1.50, 120),
(150, 96, 8, '2026-01-16', '09:00:00', NULL, 60, 'Agendamento via orcamento #147 - Ana Paula vaccari', 0.00, 'agendado', '2026-01-12 07:53:18', 'orcamento', NULL, '2026-01-16', '17:00:00', 147, 1.50, 120),
(151, 98, 5, '2026-01-17', '10:00:00', 'R. Francisco Paes, 72 - Jardim Santa Rosa I, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\r\n• Instalação de ar condicionado (x1) - R$ 350,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 9000 BTUs\r\n• Remoção de Equipamento (x1) - R$ 300,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 9000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 17/01/2026\r\nHorário: 10:00\r\nCliente: Pedrenrique Guimarães\r\nWhatsApp: (17) 9 8157-7396\r\n\r\n\r\n=== VALORES ===\r\nTotal serviços: R$ 650,00\r\nValor final: R$ 650,00', 0.00, 'cancelado', '2026-01-12 19:52:41', 'sistema_ia', 149, NULL, NULL, NULL, 1.50, 120),
(152, 99, 6, '2026-01-17', '08:00:00', NULL, 60, 'Agendamento via orcamento #150 - Letícia', 0.00, 'agendado', '2026-01-12 22:34:05', 'orcamento', NULL, '2026-01-17', '17:00:00', 150, 1.50, 120),
(153, 100, 1, '2026-01-19', '08:00:00', NULL, 60, 'Agendamento via orcamento #151 - Márcio', 0.00, 'agendado', '2026-01-12 22:36:28', 'orcamento', NULL, '2026-01-25', '17:00:00', 151, 1.50, 120),
(154, 101, 1, '2026-01-20', '08:00:00', NULL, 60, 'Agendamento via orcamento #152 - Acampamento', 0.00, 'agendado', '2026-01-12 22:38:08', 'orcamento', NULL, '2026-01-25', '17:00:00', 152, 1.50, 120),
(155, 102, 5, '2026-01-21', '08:00:00', 'Rrr, 138 - Vila Anchieta, Eed', 60, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 21/01/2026\nHorário: 08:00\nCliente: Rrr\nWhatsApp: (33) 3 3333-3333\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 0.00, 'agendado', '2026-01-12 22:52:36', 'sistema_ia', 153, NULL, NULL, NULL, 1.50, 120),
(156, 103, 5, '2026-01-22', '08:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, Ee', 60, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 22/01/2026\nHorário: 08:00\nCliente: Tgrg4\nWhatsApp: (99) 9 9999-9999\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 0.00, 'agendado', '2026-01-12 22:53:45', 'sistema_ia', 154, NULL, NULL, NULL, 1.50, 120),
(157, 101, 1, '2026-01-18', '08:00:00', NULL, 60, 'Agendamento via orcamento #155 - Kdjee', 0.00, 'agendado', '2026-01-12 23:12:02', 'orcamento', NULL, '2026-01-18', '17:00:00', 155, 1.50, 120),
(158, 101, 1, '2026-01-23', '08:00:00', NULL, 60, 'Agendamento via orcamento #156 - Acampamento', 0.00, 'agendado', '2026-01-12 23:13:16', 'orcamento', NULL, '2026-01-23', '17:00:00', 156, 1.50, 120),
(159, 101, 1, '2026-01-24', '08:00:00', NULL, 60, 'Agendamento via orcamento #157 - Acampamento', 0.00, 'agendado', '2026-01-12 23:14:10', 'orcamento', NULL, '2026-01-24', '17:00:00', 157, 1.50, 120),
(160, 101, 1, '2026-01-25', '08:00:00', NULL, 60, 'Agendamento via orcamento #158 - Acampamento', 0.00, 'agendado', '2026-01-12 23:18:22', 'orcamento', NULL, '2026-01-25', '17:00:00', 158, 1.50, 120),
(161, 101, 1, '2026-01-26', '08:00:00', NULL, 60, 'Agendamento via orcamento #159 - Acam', 0.00, 'agendado', '2026-01-12 23:19:21', 'orcamento', NULL, '2026-01-26', '17:00:00', 159, 1.50, 120),
(162, 104, 1, '2026-01-27', '08:00:00', NULL, 60, 'Agendamento via orcamento #160 - Gustavo lapa', 0.00, 'agendado', '2026-01-13 15:07:29', 'orcamento', NULL, '2026-01-27', '17:00:00', 160, 1.50, 120),
(163, 105, 1, '2026-01-30', '08:00:00', NULL, 60, 'Agendamento via orcamento #163 - Nilza', 0.00, 'agendado', '2026-01-15 11:55:30', 'orcamento', NULL, '2026-01-30', '17:00:00', 163, 1.50, 120),
(164, 109, 5, '2026-01-28', '08:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto', 60, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 28/01/2026\nHorário: 08:00\nCliente: N&M Refrigeração\nWhatsApp: (88) 8 8888-8888\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 0.00, 'agendado', '2026-01-18 17:45:14', 'sistema_ia', 166, NULL, NULL, NULL, 1.50, 120);

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos_orcamentos`
--

CREATE TABLE `agendamentos_orcamentos` (
  `id` int NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` text,
  `data_agendamento` date DEFAULT NULL,
  `hora_agendamento` varchar(10) DEFAULT NULL,
  `observacoes` text,
  `status` enum('pendente','agendado','confirmado','concluido','cancelado') DEFAULT 'pendente',
  `origem` enum('admin','site','whatsapp') DEFAULT 'admin',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento_servicos`
--

CREATE TABLE `agendamento_servicos` (
  `id` int NOT NULL,
  `agendamento_id` int NOT NULL,
  `servico_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bairro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `data_cadastro`) VALUES
(72, 'Mercado jj', NULL, '34999162340', 'Rua Itanhaém', '138', 'Vila Anchieta', 'São José do Rio Preto', '2025-12-17 23:45:35'),
(74, 'Joselaine', '', '17988093650', '', '', '', '', '2025-12-18 10:33:23'),
(75, 'Daniel da Riopetrana', '', '17981389114', '', '', '', '', '2025-12-18 10:39:24'),
(76, 'Gustavo Rio petrana', '', '17996254293', '', '', '', '', '2025-12-18 10:43:01'),
(77, 'N&amp;amp;M Refrigeração', '', '17996240727', '', '', '', '', '2025-12-18 14:15:53'),
(78, 'Mercado jj', NULL, '34999162350', 'Rua Itanhaém', '138', 'Vila Anchieta', 'São José do Rio Preto', '2025-12-18 15:23:49'),
(79, 'Paulo santana', NULL, '17991776565', 'Rua Paulo Setúbal ', '337', 'Santa cruz', 'São José do Rio Preto-SP ', '2025-12-19 00:01:43'),
(80, 'William Antônio de oliveira', '', '17997051616', '', '', '', '', '2025-12-22 11:45:23'),
(84, 'Carlos Simonett', '', '17996256434', '', '', '', '', '2025-12-29 11:51:43'),
(85, 'Izabel Tanaka', '', '17997079792', '', '', '', '', '2026-01-06 20:43:34'),
(86, 'Ruam lapa', '', '17991975047', '', '', '', '', '2026-01-07 19:07:48'),
(87, 'Lobato', '', '17981340827', '', '', '', '', '2026-01-08 12:01:22'),
(88, 'Márcio valsechi', '', '17997390085', '', '', '', '', '2026-01-10 16:08:57'),
(89, 'Milena Pires Assunção', '', '17981330612', '', '', '', '', '2026-01-11 16:35:42'),
(90, 'Wagner', '', '17981264481', '', '', '', '', '2026-01-11 16:42:06'),
(91, 'Nilza pirassollo', NULL, '17988089090', 'Pq industrial ', 'Uugug', ' Hv', 'São José do Rio Preto', '2026-01-11 16:46:14'),
(92, 'José santista', NULL, '17981020574', 'Gggy', '44', 'Santa Catarina ', 'Eng. Schmidt ', '2026-01-11 16:50:50'),
(93, 'Neltel neto', 'neltelneto1@gmail.com', '(34) 9 9916-2340', '', '', '', '', '2026-01-11 18:52:49'),
(95, 'rwrwrwer', '', '(23) 2323-2323', '', '', '', '', '2026-01-11 18:55:58'),
(96, 'Ana Paula vaccari', '', '(17) 9 9136-4040', '', '', '', '', '2026-01-12 07:50:18'),
(97, 'Vinicius', '', '(17) 9 9122-5656', '', '', '', '', '2026-01-12 14:32:07'),
(98, 'Pedrenrique Guimarães', NULL, '17981577396', 'R. Francisco Paes', '72', 'Jardim Santa Rosa I', 'São José do Rio Preto', '2026-01-12 19:52:41'),
(99, 'Letícia', '', '(17) 9 9665-1616', '', '', '', '', '2026-01-12 22:33:28'),
(100, 'Márcio', '', '(17) 9 9739-0085', '', '', '', '', '2026-01-12 22:35:32'),
(101, 'Café cimo', '', '34999162340', '', '', '', '', '2026-01-12 22:37:34'),
(102, 'Rrr', NULL, '33333333333', 'Rrr', '138', 'Vila Anchieta', 'Eed', '2026-01-12 22:52:36'),
(103, 'Tgrg4', NULL, '99999999999', 'Rua Itanhaém', '138', 'Vila Anchieta', 'Ee', '2026-01-12 22:53:45'),
(104, 'Gustavo lapa', '', '(17) 9 9208-7920', '', '', '', '', '2026-01-13 15:07:05'),
(105, 'Nilza', '', '(11) 1 1111-1111', '', '', '', '', '2026-01-14 02:03:07'),
(106, 'Beto', '', '17 99772-3812', '', '', '', '', '2026-01-14 12:31:22'),
(107, 'Wagner', '', '(17) 9 8126-4481', '', '', '', '', '2026-01-15 12:38:11'),
(108, 'Café cimo', '', '(17) 9 8174-6562', '', '', '', '', '2026-01-15 19:55:04'),
(109, 'N&M Refrigeração', NULL, '88888888888', 'Rua Itanhaém', '138', 'Vila Anchieta', 'São José do Rio Preto', '2026-01-18 17:45:14'),
(110, 'Milena Pires Assunção', '', '(17) 9 8133-0612', '', '', '', '', '2026-01-30 02:14:23'),
(111, 'Lacticínios são José', '', '17 99259-1328', '', '', '', '', '2026-02-09 13:43:31'),
(112, 'Silvio', '', '(17) 9 9151-1198', '', '', '', '', '2026-02-12 15:33:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int NOT NULL,
  `chave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'text',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feed_instagram_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Token de Acesso da API do Instagram',
  `feed_instagram_ativo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 se o feed do Instagram estiver ativo',
  `feed_instagram_limite` int NOT NULL DEFAULT '6' COMMENT 'Número máximo de posts a exibir',
  `feed_tiktok_ativo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 se o feed do TikTok estiver ativo (API)',
  `feed_tiktok_user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nome de usuário do TikTok',
  `feed_facebook_ativo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 se o feed do Facebook estiver ativo (API)',
  `feed_facebook_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Token de Acesso da API do Facebook'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `descricao`, `data_atualizacao`, `feed_instagram_token`, `feed_instagram_ativo`, `feed_instagram_limite`, `feed_tiktok_ativo`, `feed_tiktok_user`, `feed_facebook_ativo`, `feed_facebook_token`) VALUES
(1, 'site_nome', 'N&M Refrigeração', 'text', 'Nome do site', '2025-11-19 19:31:58', NULL, 0, 6, 0, NULL, 0, NULL),
(2, 'site_descricao', 'Especialistas em conforto térmico', 'text', 'Descrição do site', '2025-11-14 20:27:27', NULL, 0, 6, 0, NULL, 0, NULL),
(3, 'site_telefone', '(17) 9 9624-0725', 'text', 'Telefone de contato', '2025-11-14 20:00:01', NULL, 0, 6, 0, NULL, 0, NULL),
(4, 'site_email', '', 'email', 'E-mail de contato', '2025-11-19 19:31:58', NULL, 0, 6, 0, NULL, 0, NULL),
(5, 'site_endereco', 'São José do rio preto', 'text', 'Endereço', '2025-11-14 20:00:01', NULL, 0, 6, 0, NULL, 0, NULL),
(6, 'site_whatsapp', '5517996240725', 'text', 'Número do WhatsApp (com DDD e sem caracteres especiais)', '2025-11-14 20:00:01', NULL, 0, 6, 0, NULL, 0, NULL),
(7, 'horario_funcionamento', 'de segunda a sexta da 08:00 as 17:00', 'text', 'Horário de funcionamento', '2025-11-22 13:54:34', NULL, 0, 6, 0, NULL, 0, NULL),
(8, 'valor_instalacao_base', '0', 'number', 'Valor base para instalação', '2025-11-20 23:16:46', NULL, 0, 6, 0, NULL, 0, NULL),
(9, 'taxa_cartao', '1', 'number', 'Taxa para pagamento com cartão (%)', '2025-11-22 13:33:45', NULL, 0, 6, 0, NULL, 0, NULL),
(10, 'dias_agendamento', '30', 'number', 'Dias futuros disponíveis para agendamento', '2025-12-16 11:43:43', NULL, 0, 6, 0, NULL, 0, NULL),
(11, 'manutencao', '1', 'boolean', 'Modo manutenção (1 = ativo, 0 = inativo)', '2025-11-22 16:32:02', NULL, 0, 6, 0, NULL, 0, NULL),
(592, 'taxa_cartao_avista', '5', 'number', 'Taxa para pagamento com cartão à vista (%)', '2025-11-22 13:54:34', NULL, 0, 6, 0, NULL, 0, NULL),
(593, 'taxa_cartao_parcelado', '21', 'number', 'Taxa para pagamento com cartão parcelado (%)', '2025-11-22 13:54:34', NULL, 0, 6, 0, NULL, 0, NULL),
(594, 'maquininha_cartao', 'ton', 'text', 'Máquina de cartão utilizada', '2025-11-17 22:45:23', NULL, 0, 6, 0, NULL, 0, NULL),
(595, 'chave_pix', ' 17992059663', 'text', 'Chave PIX para pagamentos', '2026-01-31 20:07:18', NULL, 0, 6, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_agendamento`
--

CREATE TABLE `config_agendamento` (
  `id` int NOT NULL,
  `chave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'text',
  `categoria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `config_agendamento`
--

INSERT INTO `config_agendamento` (`id`, `chave`, `valor`, `descricao`, `data_atualizacao`, `tipo`, `categoria`) VALUES
(1, 'horario_inicio', '08:00', 'Horário de início do atendimento', '2025-11-20 01:25:36', 'time', 'Horários'),
(2, 'horario_fim', '18:00', 'Horário de fim do atendimento', '2025-11-20 01:25:36', 'time', 'Horários'),
(3, 'intervalo_agendamento', '60', 'Intervalo entre agendamentos (minutos)', '2025-11-14 20:24:34', 'number', 'Horários'),
(4, 'dias_antecedencia', '1', 'Dias mínimos de antecedência', '2025-11-14 20:32:18', 'number', 'Restrições'),
(5, 'bloquear_finais_semana', '1', 'Bloquear agendamentos nos finais de semana', '2025-11-20 01:24:24', 'boolean', 'Restrições'),
(6, 'max_agendamentos_dia', '5', 'Máximo de agendamentos por dia', '2025-11-14 20:32:18', 'number', 'Restrições'),
(7, 'valor_hora_normal', '0', 'Acréscimo para horário normal', '2025-11-14 19:24:05', 'text', NULL),
(8, 'valor_hora_especial', '50.00', 'Acréscimo para horário especial (fora do comercial)', '2025-11-25 11:58:34', 'text', NULL),
(9, 'valor_final_semana', '100', 'Acréscimo para finais de semana', '2025-11-25 11:58:34', 'text', NULL),
(10, 'horario_especial_inicio', '18:00', 'Início do horário especial', '2025-11-20 01:25:36', 'text', NULL),
(11, 'horario_especial_fim', '23:00', 'Fim do horário especial', '2025-12-16 11:43:43', 'text', NULL),
(12, 'horario_almoco_inicio', '12:00', 'Início do horário de almoço', '2025-11-14 20:24:34', 'time', 'Horários'),
(13, 'horario_almoco_fim', '13:00', 'Fim do horário de almoço', '2025-11-14 20:24:34', 'time', 'Horários'),
(14, 'limite_agendamentos_dia', '1', 'Dias máximos para agendamento futuro', '2026-01-11 17:11:52', 'number', 'Restrições'),
(15, 'valor_visita_tecnica', '80.00', 'Valor da visita técnica', '2025-11-14 20:24:34', 'number', 'Valores'),
(16, 'valor_hora_tecnica', '60.00', 'Valor por hora de serviço', '2025-11-14 20:24:34', 'number', 'Valores'),
(17, 'taxa_urgencia', '50.00', 'Taxa para serviços de urgência', '2025-11-14 20:32:18', 'number', 'Valores'),
(18, 'dias_max_agendamento', '30', 'Dias máximos para agendamento futuro', '2025-11-14 22:00:56', 'number', 'Restrições'),
(19, 'whatsapp_empresa', '5517996240725', 'Whatsapp da empresa', '2025-11-14 22:28:55', 'number', 'Restrições'),
(20, 'mensagem_whatsapp', '*Novo Agendamento N&M Refrigeração*\r\n\r\nOlá me chamo {nome}, estou entrando em contato para confirmar um agendamento:\r\n\r\nServiço: {servico}\r\n\r\nData: {data}\r\nHora: {hora}\r\n\r\nEndereço: {endereco}\r\n\r\nObservações: {observacoes} ', NULL, '2025-11-14 23:25:22', 'text', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_disponibilidade`
--

CREATE TABLE `config_disponibilidade` (
  `id` int NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text,
  `tipo` varchar(20) DEFAULT 'text',
  `descricao` text,
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `config_disponibilidade`
--

INSERT INTO `config_disponibilidade` (`id`, `chave`, `valor`, `tipo`, `descricao`, `data_atualizacao`) VALUES
(1, 'horario_inicio', '08:00', 'text', 'Horário de início do atendimento', '2025-12-11 01:47:50'),
(2, 'horario_fim', '18:00', 'text', 'Horário de fim do atendimento', '2025-12-11 01:47:50'),
(3, 'limite_agendamentos_dia', '8', 'text', 'Máximo de agendamentos por dia', '2025-12-11 01:47:50'),
(4, 'bloquear_finais_semana', '0', 'text', 'Bloquear agendamentos em finais de semana (0=não, 1=sim)', '2025-12-11 01:47:50'),
(5, 'horario_especial_inicio', '', 'text', 'Início do horário especial', '2025-12-11 01:47:50'),
(6, 'horario_especial_fim', '', 'text', 'Fim do horário especial', '2025-12-11 01:47:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_sistema`
--

CREATE TABLE `config_sistema` (
  `id` int NOT NULL,
  `chave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_sistema_ia`
--

CREATE TABLE `config_sistema_ia` (
  `id` int NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text,
  `descricao` text,
  `tipo` varchar(20) DEFAULT 'text',
  `categoria` varchar(50) DEFAULT NULL,
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `config_sistema_ia`
--

INSERT INTO `config_sistema_ia` (`id`, `chave`, `valor`, `descricao`, `tipo`, `categoria`, `data_atualizacao`) VALUES
(1, 'limite_agendamentos_dia', '8', 'Máximo de agendamentos por dia', 'number', 'agendamento', '2025-12-17 15:15:04'),
(2, 'horario_inicio', '08:00', 'Horário de início do atendimento', 'time', 'horarios', '2025-12-17 15:15:04'),
(3, 'horario_fim', '19:00', 'Horário de fim do atendimento', 'time', 'horarios', '2025-12-17 15:15:04'),
(4, 'margem_seguranca_min', '30', 'Margem de segurança entre serviços (minutos)', 'number', 'agendamento', '2025-12-17 15:15:04'),
(5, 'email_notificacao', 'contato@nmrefrigeracao.com.br', 'Email para notificações', 'email', 'notificacoes', '2025-12-17 15:15:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_site`
--

CREATE TABLE `config_site` (
  `id` int NOT NULL,
  `chave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'text',
  `categoria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ordem` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `config_site`
--

INSERT INTO `config_site` (`id`, `chave`, `valor`, `tipo`, `categoria`, `descricao`, `data_atualizacao`, `ordem`) VALUES
(1, 'site_nome', 'N&M Refrigeração', 'text', 'empresa', 'Nome da empresa', '2025-11-20 01:00:18', 1),
(2, 'site_slogan', 'Somos proficionais e especialistas em conforto térmico.', 'text', 'empresa', 'Slogan da empresa', '2025-11-19 17:14:23', 2),
(3, 'site_telefone', '(17) 9 9624-0725', 'text', 'contato', 'Telefone principal', '2025-11-19 17:14:23', 1),
(4, 'site_telefone2', '', 'text', 'contato', 'Telefone secundário', '2025-11-19 17:14:23', 2),
(5, 'site_email', '', 'email', 'contato', 'E-mail de contato', '2025-11-19 17:14:23', 3),
(6, 'site_endereco', 'São José do rio preto', 'text', 'empresa', 'Endereço', '2025-11-19 17:14:23', 5),
(7, 'cor_primaria', '#000000', 'color', 'cores', 'Cor primária (azul principal)', '2025-11-22 20:37:06', 1),
(8, 'cor_secundaria', '#bf150d', 'color', 'cores', 'Cor secundária (azul escuro)', '2025-11-20 01:12:35', 2),
(9, 'cor_accent', '#0080ff', 'color', 'cores', 'Cor de destaque (laranja)', '2025-11-19 17:14:23', 3),
(10, 'cor_texto', '#000084', 'color', 'cores', 'Cor do texto', '2025-11-19 17:14:23', 4),
(11, 'cor_fundo', '#ffffff', 'color', 'cores', 'Cor de fundo', '2025-11-19 17:14:23', 5),
(12, 'meta_descricao', 'N&M Refrigeração: Técnicos especializados em ar condicionado em São José do Rio Preto e região. Manutenção, conserto e instalação de todas as marcas. Orçamento rápido!\r\n\r\nN&M Refrigeração a especialistas em conforto térmico!\r\n\r\ntécnico de ar condicionado,\r\nassistência técnica ar condicionado,\r\nmanutenção ar condicionado,\r\nconserto ar condicionado,\r\ninstalação ar condicionado,\r\nlimpeza ar condicionado,\r\nreparo ar condicionado,\r\nhigienização ar condicionado,\r\nserviço ar condicionado,\r\norçamento ar condicionado,\r\ntécnico ar condicionado Rio Preto,\r\nassistência técnica ar condicionado São José do Rio Preto,\r\nmanutenção ar condicionado SP,\r\nconserto ar condicionado centro,\r\ninstalação split,\r\nlimpeza split,\r\nreparo split,\r\ncarga de gás,\r\ntroca de gás,\r\ntécnico urgente,\r\nassistência 24 horas,\r\nconserto urgente,\r\nmanutenção residencial,\r\nmanutenção comercial,\r\nmanutenção industrial,\r\ntécnico especializado,\r\ntécnico autorizado,\r\ntécnico confiável,\r\nmelhor técnico,\r\npreço de manutenção,\r\nvalor instalação,\r\norçamento limpeza,\r\norçamento conserto,\r\nar condicionado não gela,\r\nar condicionado vazando água,\r\nar condicionado fazendo barulho,\r\nar condicionado não liga,\r\nar condicionado liga e desliga,\r\nar condicionado cheiro ruim,\r\nar condicionado goteira,\r\nar condicionado baixa pressão,\r\nar condicionado congelando,\r\nproblema no split,\r\nproblema no evaporadora,\r\nproblema na condensadora,\r\ntécnico split,\r\nassistência split,\r\nconserto split inverter,\r\nmanutenção ar condicionado janela,\r\nconserto ar condicionado portátil,\r\nlimpeza de dutos,\r\nmanutenção de chiller,\r\ntécnico ar condicionado automotivo,\r\nassistência ar carro,\r\ninstalação ar condicionado apartamento,\r\ninstalação split hidráulica,\r\ninstalação split elétrica,\r\nvenda e instalação,\r\ncomprar e instalar,\r\nloja com instalação,\r\nar condicionado com instalação grátis,\r\nkit ar condicionado com instalação,\r\nassistência técnica LG,\r\ntécnico Samsung,\r\nmanutenção Springer,\r\nconserto Carrier,\r\ntécnico Mitsubishi,\r\nassistência Elgin,\r\nconserto Gree,\r\nmanutenção Midea,\r\ntécnico Philco,\r\nconserto Consul,\r\nassistência Fujitsu,\r\nmanutenção Hitachi,\r\ntécnico de ar condicionado perto de mim,\r\nassistência técnica perto de mim,\r\ntécnico zona norte,\r\ntécnico zona sul,\r\ntécnico zona leste,\r\ntécnico centro,\r\ntécnico bom sucesso,\r\ntécnico jardim bela vista,\r\ntécnico jardim parque da colina,\r\ntécnico aldeia,\r\ntécnico dona carmen,\r\ntécnico santa cruz,\r\ntécnico vila bom Jesus,\r\ntécnico vila maceno,\r\ntécnico vila ercilia,\r\ntécnico jardim do sol,\r\ntécnico jardim santa rosa,\r\ntécnico jardim redentor,\r\ntécnico jardim das orquídeas,\r\ntécnico parque industrial,\r\ntécnico distrito industrial,\r\ntécnico anchieta,\r\ntécnico talarico,\r\ntécnico vetorazzo,\r\ntécnico vila elvira,\r\ntécnico vila azul,\r\ntécnico são deocleciano,\r\ntécnico jd sumaré,\r\ntécnico jd santa rita,\r\ntécnico jd santo antonio,\r\ntécnico jd alvorada,\r\ntécnico em balsamo,\r\ntécnico em ipiguá,\r\ntécnico em mirassol,\r\ntécnico em mirassolândia,\r\ntécnico em cedral,\r\ntécnico em potirendaba,\r\ntécnico em josé bonifácio,\r\ntécnico em mendonça,\r\ntécnico em neves paulista,\r\ntécnico em tanabi,\r\ntécnico em nhandeara,\r\ntécnico em monte aprazível,\r\ntécnico em união paulista,\r\ntécnico em nova granada,\r\ntécnico em nova aliança,\r\ntécnico em bady bassitt,\r\ntécnico em gnramata,\r\ntécnico em olímpia,\r\ntécnico em catanduva,\r\ntécnico em votorantim,\r\ntécnico em barretos,\r\ntécnico em birigui,\r\ntécnico em araçatuba,\r\ntécnico em lins,\r\ntécnico em marília,\r\ntécnico em presidente prudente,\r\ntécnico em são carlos,\r\ntécnico em ribeirão preto,\r\ntécnico em araraquara,\r\ntécnico em americana,\r\ntécnico em campinas,\r\ntécnico em são paulo capital,\r\nar condicionado piscina,\r\nar condicionado data center,\r\nar condicionado sala servidor,\r\nar condicionado escritório,\r\nar condicionado consultório,\r\nar condicionado loja,\r\nar condicionado restaurante,\r\nar condicionado supermercado,\r\nar condicionado indústria,\r\nar condicionado galpão,\r\nar condicionado igreja,\r\nar condicionado escola,\r\nar condicionado hospital,\r\nar condicionado clínica,\r\nar condicionado apartamento,\r\nar condicionado casa,\r\nar condicionado sobrado,\r\nar condicionado kitnet,\r\nar condicionado quitinete,\r\nar condicionado sala comercial,\r\nar condicionado andar terreo,\r\nar condicionado cobertura,\r\nar condicionado varanda,\r\nar condicionado área gourmet,\r\nar condicionado churrasqueira,\r\nar condicionado lavanderia,\r\nar condicionado garagem,\r\nar condicionado suíte,\r\nar condicionado quarto,\r\nar condicionado sala estar,\r\nar condicionado home office,\r\nar condicionado loja shopping,\r\nar condicionado centro comercial,\r\nar condicionado corredor comercial,\r\nar condicionado showroom,\r\nar condicionado vitrine,\r\nar condicionado fachada,\r\nar condicionado teto,\r\nar condicionado piso,\r\nar condicionado parede,\r\nar condicionado janela,\r\nar condicionado porta,\r\nar condicionado veneziana,\r\nar condicionado persiana,\r\nar condicionado cortina,\r\nar condicionado blackout,\r\nar condicionado película,\r\nar condicionado insulfilm,\r\nar condicionado sombreamento,\r\nar condicionado ventilação,\r\nar condicionado exaustor,\r\nar condicionado ventilador,\r\nar condicionado climatização,\r\nar condicionado aquecimento,\r\nar condicionado resfriamento,\r\nar condicionado umidificação,\r\nar condicionado desumidificação,\r\nar condicionado purificação,\r\nar condicionado ionização,\r\nar condicionado ozônio,\r\nar condicionado ultravioleta,\r\nar condicionado HEPA,\r\nar condicionado carvão ativado,\r\nar condicionado filtro,\r\nar condicionado antialérgico,\r\nar condicionado asma,\r\nar condicionado rinite,\r\nar condicionado alérgico,\r\nar condicionado bebê,\r\nar condicionado criança,\r\nar condicionado idoso,\r\nar condicionado pet,\r\nar condicionado cachorro,\r\nar condicionado gato,\r\nar condicionado animal estimação,\r\nar condicionado silencioso,\r\nar condicionado econômico,\r\nar condicionado baixo consumo,\r\nar condicionado eficiente,\r\nar condicionado inverter,\r\nar condicionado dual inverter,\r\nar condicionado turbo,\r\nar condicionado jet cool,\r\nar condicionado sleep,\r\nar condicionado modo noturno,\r\nar condicionado timer,\r\nar condicionado wifi,\r\nar condicionado smart,\r\nar condicionado controle app,\r\nar condicionado Alexa,\r\nar condicionado Google Assistente,\r\nar condicionado sensor presença,\r\nar condicionado sensor movimento,\r\nar condicionado auto limpeza,\r\nar condicionado plasma,\r\nar condicionado ion,\r\nar condicionado ar puro,\r\nar condicionado multi split,\r\nar condicionado sistema VRF,\r\nar condicionado dutado,\r\nar condicionado piso teto,\r\nar condicionado cassette,\r\nar condicionado self contained,\r\nar condicionado precisão,\r\nar condicionado janela dupla,\r\nar condicionado portátil silencioso,\r\nar condicionado evaporativo,\r\nar condicionado solar,\r\nar condicionado gás ecológico,\r\nar condicionado R410A,\r\nar condicionado R22,\r\nar condicionado R32,\r\nar condicionado R454B,\r\nar condicionado sustentável,\r\nar condicionado green,\r\nar condicionado eco,\r\nar condicionado selo procel,\r\nar condicionado classe A,\r\nar condicionado estrela inmetro,\r\nar condicionado baixo ruído,\r\nar condicionado decibel,\r\nar condicionado dB,\r\nar condicionado BTU,\r\nar condicionado 7000 BTU,\r\nar condicionado 9000 BTU,\r\nar condicionado 12000 BTU,\r\nar condicionado 18000 BTU,\r\nar condicionado 24000 BTU,\r\nar condicionado 30000 BTU,\r\nar condicionado 36000 BTU,\r\nar condicionado 48000 BTU,\r\nar condicionado 60000 BTU,\r\nar condicionado 72000 BTU,\r\nar condicionado 90000 BTU,\r\nar condicionado 120000 BTU,\r\nar condicionado 180000 BTU,\r\nar condicionado 240000 BTU,\r\nar condicionado 360000 BTU,\r\nar condicionado 480000 BTU,\r\nar condicionado 600000 BTU,\r\nar condicionado cálculo BTU,\r\nar condicionado dimensionamento,\r\nar condicionado carga térmica,\r\nar condicionado projeto,\r\nar condicionado laudo,\r\nar condicionado ART,\r\nar condicionado CREA,\r\nar condicionado engenheiro,\r\nar condicionado arquiteto,\r\nar condicionado mão obra,\r\nar condicionado material,\r\nar condicionado tubulação,\r\nar condicionado cobre,\r\nar condicionado isolamento,\r\nar condicionado mangueira,\r\nar condicionado dreno,\r\nar condicionado bandeja,\r\nar condicionado suporte,\r\nar condicionado calha,\r\nar condicionado ralo,\r\nar condicionado bomba condensado,\r\nar condicionado ventilador convecção,\r\nar condicionado motor,\r\nar condicionado compressor,\r\nar condicionado capacitor,\r\nar condicionado contator,\r\nar condicionado relé,\r\nar condicionado termostato,\r\nar condicionado sensor temperatura,\r\nar condicionado sensor umidade,\r\nar condicionado placa,\r\nar condicionado display,\r\nar condicionado led,\r\nar condicionado botão,\r\nar condicionado controle remoto,\r\nar condicionado bateria controle,\r\nar condicionado suporte controle,\r\nar condicionado capa protetora,\r\nar condicionado capa inverno,\r\nar condicionado limpa filtro,\r\nar condicionado aromatizador,\r\nar condicionado vela perfumada,\r\nar condicionado tela proteção,\r\nar condicionado grade,\r\nar condicionado parafuso,\r\nar condicionado bucha,\r\nar condicionado plugue,\r\nar condicionado tomada,\r\nar condicionado disjuntor,\r\nar condicionado fusível,\r\nar condicionado estabilizador,\r\nar condicionado nobreak,\r\nar condicionado filtro linha,\r\nar condicionado DR,\r\nar condicionado aterramento,\r\nar condicionado fio,\r\nar condicionado cabo,\r\nar condicionado eletroduto,\r\nar condicionado conduit,\r\nar condicionado caixa terminação,\r\nar condicionado quadro força,\r\nar condicionado religação,\r\nar condicionado religar,\r\nar condicionado religação luz,\r\nar condicionado religação água,\r\nar condicionado religação gás,\r\nar condicionado corte,\r\nar condicionado falta pagamento,\r\nar condicionado inadimplente,\r\nar condicionado negociação,\r\nar condicionado desconto,\r\nar condicionado promoção,\r\nar condicionado oferta,\r\nar condicionado liquidação,\r\nar condicionado black friday,\r\nar condicionado cyber monday,\r\nar condicionado natal,\r\nar condicionado ano novo,\r\nar condicionado di', 'textarea', 'seo', 'Meta descrição padrão', '2025-11-22 20:37:06', 1),
(13, 'palavras_chave', 'ar condicionado São José do Rio Preto, instalação ar condicionado, manutenção ar condicionado,técnico de ar condicionado, assistência técnica ar condicionado, manutenção ar condicionado, conserto ar condicionado, instalação ar condicionado, limpeza ar condicionado, reparo ar condicionado, higienização ar condicionado, serviço ar condicionado, orçamento ar condicionado, técnico ar condicionado Rio Preto, assistência técnica ar condicionado São José do Rio Preto, manutenção ar condicionado SP, conserto ar condicionado centro, instalação split, limpeza split, reparo split, carga de gás, troca de gás, técnico urgente, assistência 24 horas, conserto urgente, manutenção residencial, manutenção comercial, manutenção industrial, técnico especializado, técnico autorizado, técnico confiável, melhor técnico, preço de manutenção, valor instalação, orçamento limpeza, orçamento conserto, ar condicionado não gela, ar condicionado vazando água, ar condicionado fazendo barulho, ar condicionado não liga, ar condicionado liga e desliga, ar condicionado cheiro ruim, ar condicionado goteira, ar condicionado baixa pressão, ar condicionado congelando, problema no split, problema no evaporadora, problema na condensadora, técnico split, assistência split, conserto split inverter, manutenção ar condicionado janela, conserto ar condicionado portátil, limpeza de dutos, manutenção de chiller, técnico ar condicionado automotivo, assistência ar carro, instalação ar condicionado apartamento, instalação split hidráulica, instalação split elétrica, venda e instalação, comprar e instalar, loja com instalação, ar condicionado com instalação grátis, kit ar condicionado com instalação, assistência técnica LG, técnico Samsung, manutenção Springer, conserto Carrier, técnico Mitsubishi, assistência Elgin, conserto Gree, manutenção Midea, técnico Philco, conserto Consul, assistência Fujitsu, manutenção Hitachi, técnico de ar condicionado perto de mim, assistência técnica perto de mim, técnico zona norte, técnico zona sul, técnico zona leste, técnico centro, técnico bom sucesso, técnico jardim bela vista, técnico jardim parque da colina, técnico aldeia, técnico dona carmen, técnico santa cruz, técnico vila bom Jesus, técnico vila maceno, técnico vila ercilia, técnico jardim do sol, técnico jardim santa rosa, técnico jardim redentor, técnico jardim das orquídeas, técnico parque industrial, técnico distrito industrial, técnico anchieta, técnico talarico, técnico vetorazzo, técnico vila elvira, técnico vila azul, técnico são deocleciano, técnico jd sumaré, técnico jd santa rita, técnico jd santo antonio, técnico jd alvorada, técnico em balsamo, técnico em ipiguá, técnico em mirassol, técnico em mirassolândia, técnico em cedral, técnico em potirendaba, técnico em josé bonifácio, técnico em mendonça, técnico em neves paulista, técnico em tanabi, técnico em nhandeara, técnico em monte aprazível, técnico em união paulista, técnico em nova granada, técnico em nova aliança, técnico em bady bassitt, técnico em gnramata, técnico em olímpia, técnico em catanduva, técnico em votorantim, técnico em barretos, técnico em birigui, técnico em araçatuba, técnico em lins, técnico em marília, técnico em presidente prudente, técnico em são carlos, técnico em ribeirão preto, técnico em araraquara, técnico em americana, técnico em campinas, técnico em são paulo capital, ar condicionado piscina, ar condicionado data center, ar condicionado sala servidor, ar condicionado escritório, ar condicionado consultório, ar condicionado loja, ar condicionado restaurante, ar condicionado supermercado, ar condicionado indústria, ar condicionado galpão, ar condicionado igreja, ar condicionado escola, ar condicionado hospital, ar condicionado clínica, ar condicionado apartamento, ar condicionado casa, ar condicionado sobrado, ar condicionado kitnet, ar condicionado quitinete, ar condicionado sala comercial, ar condicionado andar terreo, ar condicionado cobertura, ar condicionado varanda, ar condicionado área gourmet, ar condicionado churrasqueira, ar condicionado lavanderia, ar condicionado garagem, ar condicionado suíte, ar condicionado quarto, ar condicionado sala estar, ar condicionado home office, ar condicionado loja shopping, ar condicionado centro comercial, ar condicionado corredor comercial, ar condicionado showroom, ar condicionado vitrine, ar condicionado fachada, ar condicionado teto, ar condicionado piso, ar condicionado parede, ar condicionado janela, ar condicionado porta, ar condicionado veneziana, ar condicionado persiana, ar condicionado cortina, ar condicionado blackout, ar condicionado película, ar condicionado insulfilm, ar condicionado sombreamento, ar condicionado ventilação, ar condicionado exaustor, ar condicionado ventilador, ar condicionado climatização, ar condicionado aquecimento, ar condicionado resfriamento, ar condicionado umidificação, ar condicionado desumidificação, ar condicionado purificação, ar condicionado ionização, ar condicionado ozônio, ar condicionado ultravioleta, ar condicionado HEPA, ar condicionado carvão ativado, ar condicionado filtro, ar condicionado antialérgico, ar condicionado asma, ar condicionado rinite, ar condicionado alérgico, ar condicionado bebê, ar condicionado criança, ar condicionado idoso, ar condicionado pet, ar condicionado cachorro, ar condicionado gato, ar condicionado animal estimação, ar condicionado silencioso, ar condicionado econômico, ar condicionado baixo consumo, ar condicionado eficiente, ar condicionado inverter, ar condicionado dual inverter, ar condicionado turbo, ar condicionado jet cool, ar condicionado sleep, ar condicionado modo noturno, ar condicionado timer, ar condicionado wifi, ar condicionado smart, ar condicionado controle app, ar condicionado Alexa, ar condicionado Google Assistente, ar condicionado sensor presença, ar condicionado sensor movimento, ar condicionado auto limpeza, ar condicionado plasma, ar condicionado ion, ar condicionado ar puro, ar condicionado multi split, ar condicionado sistema VRF, ar condicionado dutado, ar condicionado piso teto, ar condicionado cassette, ar condicionado self contained, ar condicionado precisão, ar condicionado janela dupla, ar condicionado portátil silencioso, ar condicionado evaporativo, ar condicionado solar, ar condicionado gás ecológico, ar condicionado R410A, ar condicionado R22, ar condicionado R32, ar condicionado R454B, ar condicionado sustentável, ar condicionado green, ar condicionado eco, ar condicionado selo procel, ar condicionado classe A, ar condicionado estrela inmetro, ar condicionado baixo ruído, ar condicionado decibel, ar condicionado dB, ar condicionado BTU, ar condicionado 7000 BTU, ar condicionado 9000 BTU, ar condicionado 12000 BTU, ar condicionado 18000 BTU, ar condicionado 24000 BTU, ar condicionado 30000 BTU, ar condicionado 36000 BTU, ar condicionado 48000 BTU, ar condicionado 60000 BTU, ar condicionado 72000 BTU, ar condicionado 90000 BTU, ar condicionado 120000 BTU, ar condicionado 180000 BTU, ar condicionado 240000 BTU, ar condicionado 360000 BTU, ar condicionado 480000 BTU, ar condicionado 600000 BTU, ar condicionado cálculo BTU, ar condicionado dimensionamento, ar condicionado carga térmica, ar condicionado projeto, ar condicionado laudo, ar condicionado ART, ar condicionado CREA, ar condicionado engenheiro, ar condicionado arquiteto, ar condicionado mão obra, ar condicionado material, ar condicionado tubulação, ar condicionado cobre, ar condicionado isolamento, ar condicionado mangueira, ar condicionado dreno, ar condicionado bandeja, ar condicionado suporte, ar condicionado calha, ar condicionado ralo, ar condicionado bomba condensado, ar condicionado ventilador convecção, ar condicionado motor, ar condicionado compressor, ar condicionado capacitor, ar condicionado contator, ar condicionado relé, ar condicionado termostato, ar condicionado sensor temperatura, ar condicionado sensor umidade, ar condicionado placa, ar condicionado display, ar condicionado led, ar condicionado botão, ar condicionado controle remoto, ar condicionado bateria controle, ar condicionado suporte controle, ar condicionado capa protetora, ar condicionado capa inverno, ar condicionado limpa filtro, ar condicionado aromatizador, ar condicionado vela perfumada, ar condicionado tela proteção, ar condicionado grade, ar condicionado parafuso, ar condicionado bucha, ar condicionado plugue, ar condicionado tomada, ar condicionado disjuntor, ar condicionado fusível, ar condicionado estabilizador, ar condicionado nobreak, ar condicionado filtro linha, ar condicionado DR, ar condicionado aterramento, ar condicionado fio, ar condicionado cabo, ar condicionado eletroduto, ar condicionado conduit, ar condicionado caixa terminação, ar condicionado quadro força, ar condicionado religação, ar condicionado religar, ar condicionado religação luz, ar condicionado religação água, ar condicionado religação gás, ar condicionado corte, ar condicionado falta pagamento, ar condicionado inadimplente, ar condicionado negociação, ar condicionado desconto, ar condicionado promoção, ar condicionado oferta, ar condicionado liquidação, ar condicionado black friday, ar condicionado cyber monday, ar condicionado natal, ar condicionado ano novo, ar condicionado dia pais, ar condicionado dia mães, ar condicionado dia namorados, ar condicionado carnaval, ar condicionado férias, ar condicionado verão, ar condicionado inverno,', 'text', 'seo', 'Palavras-chave', '2025-11-22 20:37:06', 2),
(14, 'facebook_url', 'https://www.facebook.com/NMRefrigeracao', 'url', 'social', 'Facebook', '2025-11-19 17:14:23', 1),
(15, 'instagram_url', 'https://instagram.com/nmrefrigeracao_riopreto', 'url', 'social', 'Instagram', '2025-11-19 17:14:23', 2),
(16, 'whatsapp_numero', '5517996240725', 'text', 'contato', 'Número do WhatsApp (com DDD)', '2025-11-19 17:14:23', 4),
(17, 'whatsapp_ativo', '0', 'boolean', 'funcionalidades', 'Ativar WhatsApp automático', '2025-11-19 17:54:58', 1),
(18, 'agendamento_online', '0', 'boolean', 'funcionalidades', 'Ativar agendamento online', '2025-11-19 17:54:58', 2),
(19, 'manutencao', '0', 'boolean', 'sistema', 'Modo manutenção', '2025-11-22 19:56:43', 1),
(39, 'site_logo', 'logo.png', 'text', 'empresa', 'Logo do site', '2025-11-19 17:14:23', 3),
(135, 'cidades_atendidas', 'São José do Rio Preto\r\nBady Bassitt\r\nMirassol\r\nCedral\r\nIpiguá\r\nGuapiaçu\r\nPotirendaba\r\nEngenheiro Schmitt\r\nMirassolândia\r\nBálsamo\r\nNova Granada\r\nOnda Verde\r\nJaci\r\nNova Aliança\r\nTalhado\r\nUchoa\r\nMonte Aprazível\r\nNeves Paulista', 'textarea', 'cidades', 'Lista de cidades atendidas', '2025-11-22 21:50:03', 1),
(679, 'site_favicon', '', 'image', 'empresa', 'Favicon do site', '2025-11-19 17:07:06', 4),
(684, 'site_whatsapp', '5517996240725', 'tel', 'contato', 'Número do WhatsApp (com DDD e 55)', '2025-11-19 17:07:06', 4),
(685, 'site_horario_funcionamento', 'Segunda a Sexta: 8h às 18h\\nSábado: 8h às 12h', 'textarea', 'contato', 'Horário de funcionamento', '2025-11-19 17:07:06', 5),
(691, 'cor_header', '#ffffff', 'color', 'cores', 'Cor do cabeçalho', '2025-11-19 17:07:06', 6),
(692, 'cor_footer', '#1f2937', 'color', 'cores', 'Cor do rodapé', '2025-11-19 17:07:06', 7),
(693, 'meta_titulo', 'N&M Refrigeração - Especialistas em Ar Condicionado', 'text', 'seo', 'Título padrão do site', '2025-11-19 17:07:06', 1),
(696, 'google_analytics', '<!-- Google tag (gtag.js) -->\r\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-9T1BBK7D6R\"></script>\r\n<script>\r\n  window.dataLayer = window.dataLayer || [];\r\n  function gtag(){dataLayer.push(arguments);}\r\n  gtag(\'js\', new Date());\r\n\r\n  gtag(\'config\', \'G-9T1BBK7D6R\');\r\n</script>', 'textarea', 'seo', 'Código do Google Analytics', '2025-11-22 22:06:39', 4),
(697, 'google_maps_api', 'AIzaSyDDR14cqOVuuf0MoOO29YGrNmpeponSeGA', 'text', 'seo', 'Chave API Google Maps', '2025-11-22 22:28:27', 5),
(700, 'linkedin_url', '', 'url', 'social', 'URL do LinkedIn', '2025-11-19 17:07:06', 3),
(701, 'youtube_url', '', 'url', 'social', 'URL do YouTube', '2025-11-19 17:07:06', 4),
(704, 'formulario_contato', '0', 'boolean', 'funcionalidades', 'Ativar formulário de contato', '2025-11-19 17:54:58', 3),
(705, 'depoimentos_ativo', '0', 'boolean', 'funcionalidades', 'Exibir seção de depoimentos', '2025-11-19 17:54:58', 4),
(706, 'blog_ativo', '0', 'boolean', 'funcionalidades', 'Ativar blog do site', '2025-11-19 17:54:58', 5),
(708, 'manutencao_mensagem', 'Site em manutenção. Volte em breve!', 'textarea', 'sistema', 'Mensagem de manutenção', '2025-11-19 17:07:06', 2),
(709, 'debug_mode', '0', 'boolean', 'sistema', 'Modo desenvolvimento', '2025-11-19 17:07:06', 3),
(711, 'raio_atendimento', '50', 'number', 'cidades', 'Raio de atendimento (km)', '2025-11-19 17:07:06', 2),
(712, 'especialidades', 'Instalação de Ar Condicionado\r\nManutenção Preventiva\r\nLimpeza Técnica\r\nConserto e Reparo\r\nVenda de Equipamentos', 'textarea', 'negocio', 'Especialidades e serviços', '2025-11-19 17:25:05', 1),
(713, 'anos_experiencia', '3', 'number', 'negocio', 'Anos de experiência', '2025-12-16 11:42:21', 2),
(714, 'clientes_atendidos', '2545', 'number', 'negocio', 'Clientes atendidos', '2025-12-16 11:42:21', 3),
(790, 'whatsapp_url', 'https://api.whatsapp.com/send/?phone=5517996240725&text=Estou+vindo+do+site+,+pode+me+ajudar?', 'url', 'social', 'URL do WhatsApp', '2025-11-22 20:37:06', 3),
(791, 'tiktok_url', 'https://www.tiktok.com/@nm.refrigerao?_r=1&_t=ZS-91cJq4JUm8N', 'url', 'social', 'URL do TikTok', '2025-11-22 20:37:06', 6),
(792, 'twitter_url', '', 'url', 'social', 'URL do Twitter/X', '2025-11-19 17:40:00', 7),
(793, 'pinterest_url', '', 'url', 'social', 'URL do Pinterest', '2025-11-19 17:40:00', 8),
(794, 'telegram_url', '', 'url', 'social', 'URL do Telegram', '2025-11-19 17:40:00', 9),
(795, 'getninja_url', '', 'url', 'social', 'URL do GetNinja', '2025-12-07 19:06:21', 10),
(796, 'olx_url', '', 'url', 'social', 'URL do OLX', '2025-11-19 17:40:00', 11),
(797, 'mercado_livre_url', '', 'url', 'social', 'URL do Mercado Livre', '2025-11-19 17:40:00', 12),
(798, 'google_meu_negocio_url', '', 'url', 'social', 'URL do Google Meu Negócio', '2025-11-19 17:40:00', 13),
(799, 'tripadvisor_url', '', 'url', 'social', 'URL do TripAdvisor', '2025-11-19 17:40:00', 14),
(800, 'airbnb_url', '', 'url', 'social', 'URL do Airbnb', '2025-11-19 17:40:00', 15),
(801, 'booking_url', '', 'url', 'social', 'URL do Booking', '2025-11-19 17:40:00', 16),
(802, 'spotify_url', '', 'url', 'social', 'URL do Spotify', '2025-11-19 17:40:00', 17),
(803, 'soundcloud_url', '', 'url', 'social', 'URL do SoundCloud', '2025-11-19 17:40:00', 18),
(804, 'twitch_url', '', 'url', 'social', 'URL do Twitch', '2025-11-19 17:40:00', 19),
(805, 'discord_url', '', 'url', 'social', 'URL do Discord', '2025-11-19 17:40:00', 20),
(806, 'github_url', '', 'url', 'social', 'URL do GitHub', '2025-11-19 17:40:00', 21),
(807, 'behance_url', '', 'url', 'social', 'URL do Behance', '2025-11-19 17:40:00', 22),
(808, 'dribbble_url', '', 'url', 'social', 'URL do Dribbble', '2025-11-19 17:40:00', 23),
(809, 'medium_url', '', 'url', 'social', 'URL do Medium', '2025-11-19 17:40:00', 24),
(810, 'reddit_url', '', 'url', 'social', 'URL do Reddit', '2025-11-19 17:40:00', 25),
(811, 'quora_url', '', 'url', 'social', 'URL do Quora', '2025-11-19 17:40:00', 26),
(812, 'vimeo_url', '', 'url', 'social', 'URL do Vimeo', '2025-11-19 17:40:00', 27),
(813, 'flickr_url', '', 'url', 'social', 'URL do Flickr', '2025-11-19 17:40:00', 28),
(814, 'snapchat_url', '', 'url', 'social', 'URL do Snapchat', '2025-11-19 17:40:00', 29),
(815, 'wechat_url', '', 'url', 'social', 'URL do WeChat', '2025-11-19 17:40:00', 30),
(816, 'line_url', '', 'url', 'social', 'URL do LINE', '2025-11-19 17:40:00', 31),
(817, 'vk_url', '', 'url', 'social', 'URL do VK', '2025-11-19 17:40:00', 32),
(818, 'tumblr_url', '', 'url', 'social', 'URL do Tumblr', '2025-11-19 17:40:00', 33),
(819, 'blogger_url', '', 'url', 'social', 'URL do Blogger', '2025-11-19 17:40:00', 34),
(820, 'wordpress_url', '', 'url', 'social', 'URL do WordPress', '2025-11-19 17:40:00', 35),
(849, 'feed_instagram_ativo', '0', 'boolean', 'social', 'Ativar feed do Instagram no site', '2025-11-23 18:57:52', 50),
(850, 'feed_instagram_token', 'ARpHgSjiVIc-FvTfV2tH0F1', 'text', 'social', 'Token de Acesso (Long-Lived) do Instagram', '2025-11-23 19:27:10', 51),
(851, 'feed_instagram_limite', '11', 'number', 'social', 'Número de posts a exibir do Instagram', '2025-11-23 19:27:10', 52),
(852, 'feed_tiktok_ativo', '0', 'boolean', 'social', 'Ativar feed do TikTok no site', '2025-11-23 18:57:52', 53),
(853, 'feed_tiktok_user', '', 'text', 'social', 'Nome de usuário do TikTok (@username)', '2025-11-23 18:57:52', 54),
(854, 'feed_facebook_ativo', '0', 'boolean', 'social', 'Ativar feed do Facebook no site', '2025-11-23 18:57:52', 55),
(855, 'feed_facebook_token', '', 'text', 'social', 'Token de Acesso do Facebook (API Graph)', '2025-11-23 18:57:52', 56);

-- --------------------------------------------------------

--
-- Estrutura para tabela `email_config`
--

CREATE TABLE `email_config` (
  `id` int NOT NULL,
  `smtp_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` int DEFAULT '587',
  `smtp_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `email_config`
--

INSERT INTO `email_config` (`id`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `from_email`, `from_name`, `updated_at`) VALUES
(1, 'smtp.hostinger.com', 587, 'contato@nmrefrigeracao.com.br', '', 'contato@nmrefrigeracao.com.br', 'N&M Refrigeração', '2025-12-09 21:55:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id` int NOT NULL,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dados` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sessao_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagina` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `logs_sistema`
--

INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES
(4558, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"179.247.234.138\", \"sessao_id\": \"87g4vppeiqggn0v2716najdepg\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '179.247.234.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '87g4vppeiqggn0v2716najdepg', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 04:47:25'),
(4559, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '179.247.234.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '87g4vppeiqggn0v2716najdepg', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 04:47:25'),
(4560, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"87g4vppeiqggn0v2716najdepg\", \"resposta\": \"sim\"}', '179.247.234.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '87g4vppeiqggn0v2716najdepg', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 04:47:25'),
(4561, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": null, \"hora\": null, \"cliente\": null, \"total_servicos\": 0}', '179.247.234.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '87g4vppeiqggn0v2716najdepg', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 04:47:25'),
(4562, 'agendamento_erro', 'Erro ao salvar agendamento: SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null', '{\"erro\": \"SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null\", \"trace\": \"#0 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(1389): PDOStatement->execute()\\n#1 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(831): salvarAgendamento()\\n#2 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(424): processarRespostaSimples()\\n#3 {main}\", \"cliente\": null, \"telefone\": null}', '179.247.234.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '87g4vppeiqggn0v2716najdepg', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 04:47:25'),
(4563, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.210\", \"sessao_id\": \"tq3osvrrn0tlsfusp8t9afkces\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.210', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'tq3osvrrn0tlsfusp8t9afkces', '/sistema-ia.php', '', '2026-01-12 07:54:47'),
(4564, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.210', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'tq3osvrrn0tlsfusp8t9afkces', '/sistema-ia.php', '', '2026-01-12 07:54:47'),
(4565, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.208\", \"sessao_id\": \"qoirnurfp2e4bv62adjvr6g51s\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'qoirnurfp2e4bv62adjvr6g51s', '/sistema-ia.php', '', '2026-01-12 11:26:24'),
(4566, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'qoirnurfp2e4bv62adjvr6g51s', '/sistema-ia.php', '', '2026-01-12 11:26:24'),
(4567, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.3\", \"sessao_id\": \"0uav3erjucqhvghtc44jmirnnn\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.3', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '0uav3erjucqhvghtc44jmirnnn', '/sistema-ia.php', '', '2026-01-12 12:36:36'),
(4568, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.3', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '0uav3erjucqhvghtc44jmirnnn', '/sistema-ia.php', '', '2026-01-12 12:36:36'),
(4569, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"152.244.36.56\", \"sessao_id\": \"mlhnb5ji3u24ro9tqlofpch688\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', '', '2026-01-12 16:50:27'),
(4570, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', '', '2026-01-12 16:50:27'),
(4571, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:36'),
(4572, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:36'),
(4573, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"Pedrenrique Guimarães \"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:46'),
(4574, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"Pedrenrique Guimarães\", \"etapa\": \"nome\", \"primeiro_nome\": \"Pedrenrique\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:46'),
(4575, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"(17) 9 8157-7396\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:58'),
(4576, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"17981577396\", \"cliente_existente\": true, \"telefone_formatado\": \"(17) 9 8157-7396\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:50:58'),
(4577, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"Instalação de ar condicionado\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:10'),
(4578, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:10'),
(4579, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:18'),
(4580, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"1\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:18'),
(4581, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:18'),
(4582, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7195}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:23'),
(4583, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"sim\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:23'),
(4584, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7191}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:27'),
(4585, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"Remoção de Equipamento\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:27'),
(4586, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"300.00\", \"servico_id\": 9, \"servico_nome\": \"Remoção de Equipamento\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:27'),
(4587, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"72.14.199.238\", \"sessao_id\": \"pmirt0j74ae851mmgrnad100gv\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.7499.169 Mobile Safari/537.36\"}', '72.14.199.238', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.7499.169 Mobile Safari/537.36', 'pmirt0j74ae851mmgrnad100gv', '/sistema-ia.php', '', '2026-01-12 16:51:27'),
(4588, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '72.14.199.238', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.7499.169 Mobile Safari/537.36', 'pmirt0j74ae851mmgrnad100gv', '/sistema-ia.php', '', '2026-01-12 16:51:27'),
(4589, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7184}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:34'),
(4590, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"1\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:34'),
(4591, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Remoção de Equipamento\", \"subtotal\": 300, \"quantidade\": 1, \"total_valor\": 650, \"total_equipamentos\": 2}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:34'),
(4592, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7179}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:39'),
(4593, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"nao\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:39'),
(4594, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7170}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:48'),
(4595, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"9000\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:48'),
(4596, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"9000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:48'),
(4597, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7160}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:58'),
(4598, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"9000\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:58'),
(4599, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"9000\", \"servico_id\": 9, \"servico_nome\": \"Remoção de Equipamento\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:51:58'),
(4600, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7150}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:08'),
(4601, 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:08'),
(4602, 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"R. Francisco Paes, 72 - Jardim Santa Rosa I, São José do Rio Preto\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:08'),
(4603, 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 10, \"total_verificadas\": 14}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:08'),
(4604, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7136}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:22'),
(4605, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"2026-01-17\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:22'),
(4606, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-17\", \"data_formatada\": \"17/01/2026\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:22'),
(4607, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-17\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:22'),
(4608, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7129}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:29'),
(4609, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"10:00\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:29'),
(4610, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-17\", \"horario\": \"10:00\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:29'),
(4611, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": \"Pedrenrique Guimarães\", \"valor_total\": 650, \"tem_desconto\": false, \"total_servicos\": 2}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:29'),
(4612, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.3\", \"sessao_id\": \"oom78nngf8i02u4b9vm0bf9vhh\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.3', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'oom78nngf8i02u4b9vm0bf9vhh', '/sistema-ia.php', '', '2026-01-12 16:52:37'),
(4613, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.3', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'oom78nngf8i02u4b9vm0bf9vhh', '/sistema-ia.php', '', '2026-01-12 16:52:37'),
(4614, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7117}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4615, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"mlhnb5ji3u24ro9tqlofpch688\", \"resposta\": \"sim\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4616, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": \"2026-01-17\", \"hora\": \"10:00\", \"cliente\": \"Pedrenrique Guimarães\", \"total_servicos\": 2}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4617, 'cliente_novo', 'Novo cliente criado', '{\"nome\": \"Pedrenrique Guimarães\", \"telefone\": \"17981577396\", \"cliente_id\": \"98\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4618, 'orcamento_criado', 'Orçamento criado', '{\"cliente_id\": \"98\", \"valor_total\": 650, \"orcamento_id\": \"149\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4619, 'agendamento_criado', 'Agendamento criado com sucesso', '{\"data\": \"2026-01-17\", \"hora\": \"10:00\", \"cliente_id\": \"98\", \"valor_total\": 650, \"orcamento_id\": \"149\", \"agendamento_id\": \"151\"}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4620, 'agendamento_concluido', 'Agendamento concluído com sucesso ', '{\"data\": \"2026-01-17\", \"hora\": \"10:00\", \"cliente\": \"Pedrenrique Guimarães\", \"telefone\": \"17981577396\", \"valor_total\": 650, \"agendamento_id\": \"151\", \"tempo_conversa\": 134}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 16:52:41'),
(4621, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 7098}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/index.php', '2026-01-12 16:53:00'),
(4622, 'rate_limit', 'Tentativa bloqueada - IP: 152.244.36.56', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 18:51:18\", \"tempo_restante\": 4169}', '152.244.36.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'mlhnb5ji3u24ro9tqlofpch688', '/sistema-ia.php', 'https://nmrefrigeracao.business/index.php', '2026-01-12 17:41:49'),
(4623, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.13\", \"sessao_id\": \"h7uf4htqq86nitm75iujfgc13a\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.13', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'h7uf4htqq86nitm75iujfgc13a', '/sistema-ia.php', '', '2026-01-12 17:53:07'),
(4624, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.13', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'h7uf4htqq86nitm75iujfgc13a', '/sistema-ia.php', '', '2026-01-12 17:53:07'),
(4625, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.7\", \"sessao_id\": \"45ig15dp0inu100fltnalvinkv\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.7', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '45ig15dp0inu100fltnalvinkv', '/sistema-ia.php', '', '2026-01-12 19:20:45'),
(4626, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.7', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '45ig15dp0inu100fltnalvinkv', '/sistema-ia.php', '', '2026-01-12 19:20:45'),
(4627, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.26.153.83\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:15'),
(4628, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:15'),
(4629, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"sim\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:15'),
(4630, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": null, \"hora\": null, \"cliente\": null, \"total_servicos\": 0}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:15'),
(4631, 'agendamento_erro', 'Erro ao salvar agendamento: SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null', '{\"erro\": \"SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null\", \"trace\": \"#0 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(1389): PDOStatement->execute()\\n#1 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(831): salvarAgendamento()\\n#2 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(424): processarRespostaSimples()\\n#3 {main}\", \"cliente\": null, \"telefone\": null}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:15'),
(4632, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"191.26.153.83\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:18'),
(4633, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.26.153.83\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:19'),
(4634, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:19'),
(4635, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:22'),
(4636, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:22'),
(4637, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Fre\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:26'),
(4638, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"Fre\", \"etapa\": \"nome\", \"primeiro_nome\": \"Fre\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:27'),
(4639, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"(44) 4 4444-4444\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:35'),
(4640, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"44444444444\", \"cliente_existente\": true, \"telefone_formatado\": \"(44) 4 4444-4444\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:35'),
(4641, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Instalação de ar condicionado\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:38'),
(4642, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:38'),
(4643, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:38:43\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:43'),
(4644, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"12000\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:43'),
(4645, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 4200000, \"quantidade\": 12000, \"total_valor\": 4200000, \"total_equipamentos\": 12000}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:43'),
(4646, 'rate_limit', 'Tentativa bloqueada - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:38:43\", \"tempo_restante\": 7196}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:47'),
(4647, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"nao\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:47'),
(4648, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"191.26.153.83\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:57'),
(4649, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.26.153.83\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:57'),
(4650, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:38:57'),
(4651, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:02'),
(4652, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:02'),
(4653, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Rrr\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:13'),
(4654, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"Rrr\", \"etapa\": \"nome\", \"primeiro_nome\": \"Rrr\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:13'),
(4655, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"(33) 3 3333-3333\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:22'),
(4656, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"33333333333\", \"cliente_existente\": true, \"telefone_formatado\": \"(33) 3 3333-3333\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:22'),
(4657, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Instalação de ar condicionado\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:25'),
(4658, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:25'),
(4659, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:39:29\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:29'),
(4660, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"1\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:29'),
(4661, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:29'),
(4662, 'rate_limit', 'Tentativa bloqueada - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:39:29\", \"tempo_restante\": 7195}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:34'),
(4663, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"nao\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:34'),
(4664, 'rate_limit', 'Tentativa bloqueada - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:39:29\", \"tempo_restante\": 7189}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:40'),
(4665, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"12000\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:40'),
(4666, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"12000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:40'),
(4667, 'rate_limit', 'Tentativa bloqueada - IP: 191.26.153.83', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:39:29\", \"tempo_restante\": 7178}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:51'),
(4668, 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:51'),
(4669, 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"Rrr, 138 - Vila Anchieta, Eed\"}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:51'),
(4670, 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 7, \"total_verificadas\": 14}', '191.26.153.83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:39:51'),
(4671, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"2026-01-21\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:28'),
(4672, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-21\", \"data_formatada\": \"21/01/2026\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:28'),
(4673, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-21\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:28'),
(4674, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:32'),
(4675, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-21\", \"horario\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:32');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES
(4676, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": \"Rrr\", \"valor_total\": 350, \"tem_desconto\": false, \"total_servicos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:32'),
(4677, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"sim\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4678, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": \"2026-01-21\", \"hora\": \"08:00\", \"cliente\": \"Rrr\", \"total_servicos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4679, 'cliente_novo', 'Novo cliente criado', '{\"nome\": \"Rrr\", \"telefone\": \"33333333333\", \"cliente_id\": \"102\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4680, 'orcamento_criado', 'Orçamento criado', '{\"cliente_id\": \"102\", \"valor_total\": 350, \"orcamento_id\": \"153\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4681, 'agendamento_criado', 'Agendamento criado com sucesso', '{\"data\": \"2026-01-21\", \"hora\": \"08:00\", \"cliente_id\": \"102\", \"valor_total\": 350, \"orcamento_id\": \"153\", \"agendamento_id\": \"155\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4682, 'agendamento_concluido', 'Agendamento concluído com sucesso ', '{\"data\": \"2026-01-21\", \"hora\": \"08:00\", \"cliente\": \"Rrr\", \"telefone\": \"33333333333\", \"valor_total\": 350, \"agendamento_id\": \"155\", \"tempo_conversa\": 819}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:36'),
(4683, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"200.232.137.153\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:38'),
(4684, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:39'),
(4685, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:39'),
(4686, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:42'),
(4687, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:42'),
(4688, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Tgrg4\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:47'),
(4689, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"Tgrg4\", \"etapa\": \"nome\", \"primeiro_nome\": \"Tgrg4\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:47'),
(4690, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"(99) 9 9999-9999\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:58'),
(4691, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"99999999999\", \"cliente_existente\": true, \"telefone_formatado\": \"(99) 9 9999-9999\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:52:58'),
(4692, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Instalação de ar condicionado\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:01'),
(4693, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:01'),
(4694, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:06'),
(4695, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"1\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:06'),
(4696, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:06'),
(4697, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7196}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:10'),
(4698, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"nao\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:10'),
(4699, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7186}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:20'),
(4700, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"12000\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:20'),
(4701, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"12000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:20'),
(4702, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7174}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:32'),
(4703, 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:32'),
(4704, 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"Rua Itanhaém, 138 - Vila Anchieta, Ee\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:32'),
(4705, 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 6, \"total_verificadas\": 14}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:32'),
(4706, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7170}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:36'),
(4707, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"2026-01-22\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:36'),
(4708, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-22\", \"data_formatada\": \"22/01/2026\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:36'),
(4709, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-22\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:36'),
(4710, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7165}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:41'),
(4711, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:41'),
(4712, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-22\", \"horario\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:41'),
(4713, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": \"Tgrg4\", \"valor_total\": 350, \"tem_desconto\": false, \"total_servicos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:41'),
(4714, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:53:06\", \"tempo_restante\": 7161}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4715, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"sim\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4716, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": \"2026-01-22\", \"hora\": \"08:00\", \"cliente\": \"Tgrg4\", \"total_servicos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4717, 'cliente_novo', 'Novo cliente criado', '{\"nome\": \"Tgrg4\", \"telefone\": \"99999999999\", \"cliente_id\": \"103\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4718, 'orcamento_criado', 'Orçamento criado', '{\"cliente_id\": \"103\", \"valor_total\": 350, \"orcamento_id\": \"154\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4719, 'agendamento_criado', 'Agendamento criado com sucesso', '{\"data\": \"2026-01-22\", \"hora\": \"08:00\", \"cliente_id\": \"103\", \"valor_total\": 350, \"orcamento_id\": \"154\", \"agendamento_id\": \"156\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4720, 'agendamento_concluido', 'Agendamento concluído com sucesso ', '{\"data\": \"2026-01-22\", \"hora\": \"08:00\", \"cliente\": \"Tgrg4\", \"telefone\": \"99999999999\", \"valor_total\": 350, \"agendamento_id\": \"156\", \"tempo_conversa\": 66}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:45'),
(4721, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"200.232.137.153\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:49'),
(4722, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:49'),
(4723, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:49'),
(4724, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:55'),
(4725, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:53:55'),
(4726, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Ddd\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:00'),
(4727, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"Ddd\", \"etapa\": \"nome\", \"primeiro_nome\": \"Ddd\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:00'),
(4728, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"(22) 2 2222-2222\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:06'),
(4729, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"22222222222\", \"cliente_existente\": true, \"telefone_formatado\": \"(22) 2 2222-2222\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:06'),
(4730, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"Instalação de ar condicionado\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:09'),
(4731, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:09'),
(4732, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:16'),
(4733, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"1\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:16'),
(4734, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:16'),
(4735, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\", \"tempo_restante\": 7194}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:22'),
(4736, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"nao\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:22'),
(4737, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\", \"tempo_restante\": 7187}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:29'),
(4738, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"12000\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:29'),
(4739, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"12000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:29'),
(4740, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\", \"tempo_restante\": 7177}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:39'),
(4741, 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:39'),
(4742, 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:39'),
(4743, 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 5, \"total_verificadas\": 14}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:39'),
(4744, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\", \"tempo_restante\": 7167}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:49'),
(4745, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"2026-01-18\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:49'),
(4746, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-18\", \"data_formatada\": \"18/01/2026\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:49'),
(4747, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-18\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:49'),
(4748, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.137.153', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-12 21:54:16\", \"tempo_restante\": 7164}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:52'),
(4749, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:52'),
(4750, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-18\", \"horario\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:52'),
(4751, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": \"Ddd\", \"valor_total\": 350, \"tem_desconto\": false, \"total_servicos\": 1}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:52'),
(4752, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"200.232.137.153\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:52'),
(4753, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:53'),
(4754, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:54:53'),
(4755, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"2026-01-18\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:01'),
(4756, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-18\", \"data_formatada\": \"18/01/2026\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:01'),
(4757, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-18\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:01'),
(4758, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:04'),
(4759, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-18\", \"horario\": \"08:00\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:04'),
(4760, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": null, \"valor_total\": 0, \"tem_desconto\": false, \"total_servicos\": 0}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:04'),
(4761, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"resposta\": \"sim\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:13'),
(4762, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": \"2026-01-18\", \"hora\": \"08:00\", \"cliente\": null, \"total_servicos\": 0}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:13'),
(4763, 'agendamento_erro', 'Erro ao salvar agendamento: SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null', '{\"erro\": \"SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null\", \"trace\": \"#0 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(1389): PDOStatement->execute()\\n#1 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(831): salvarAgendamento()\\n#2 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(424): processarRespostaSimples()\\n#3 {main}\", \"cliente\": null, \"telefone\": null}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:13'),
(4764, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"200.232.137.153\", \"sessao_nova\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"sessao_antiga\": \"l1nj15sfimovj2ig7fmjdr5gt3\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:17'),
(4765, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"l1nj15sfimovj2ig7fmjdr5gt3\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:17'),
(4766, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'l1nj15sfimovj2ig7fmjdr5gt3', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-12 19:55:17'),
(4767, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.227.182\", \"sessao_id\": \"i0u9da79dmsivaqevgdkb2s1ng\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.227.182', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'i0u9da79dmsivaqevgdkb2s1ng', '/sistema-ia.php', '', '2026-01-13 00:50:27'),
(4768, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.227.182', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'i0u9da79dmsivaqevgdkb2s1ng', '/sistema-ia.php', '', '2026-01-13 00:50:27'),
(4769, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"74.7.227.182\", \"sessao_nova\": \"i0u9da79dmsivaqevgdkb2s1ng\", \"sessao_antiga\": \"i0u9da79dmsivaqevgdkb2s1ng\"}', '74.7.227.182', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'i0u9da79dmsivaqevgdkb2s1ng', '/sistema-ia.php', '', '2026-01-13 00:50:48'),
(4770, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.227.182\", \"sessao_id\": \"i0u9da79dmsivaqevgdkb2s1ng\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.227.182', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'i0u9da79dmsivaqevgdkb2s1ng', '/sistema-ia.php', '', '2026-01-13 00:50:48'),
(4771, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.227.182', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'i0u9da79dmsivaqevgdkb2s1ng', '/sistema-ia.php', '', '2026-01-13 00:50:48'),
(4772, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"177.57.25.198\", \"sessao_id\": \"npgk66pivb2nqqrl6gkls4651s\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '177.57.25.198', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'npgk66pivb2nqqrl6gkls4651s', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-13 11:38:01'),
(4773, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '177.57.25.198', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'npgk66pivb2nqqrl6gkls4651s', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-13 11:38:01'),
(4774, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"40.77.167.76\", \"sessao_id\": \"mrqija5vd5d24qorvvlsh60rkl\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36\"}', '40.77.167.76', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'mrqija5vd5d24qorvvlsh60rkl', '/sistema-ia.php', '', '2026-01-15 16:08:32'),
(4775, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '40.77.167.76', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'mrqija5vd5d24qorvvlsh60rkl', '/sistema-ia.php', '', '2026-01-15 16:08:32'),
(4776, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"179.247.226.176\", \"sessao_id\": \"lv9g3664935q42s9o259go1lf0\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '179.247.226.176', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'lv9g3664935q42s9o259go1lf0', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 14:45:04'),
(4777, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '179.247.226.176', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'lv9g3664935q42s9o259go1lf0', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 14:45:04'),
(4778, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"ffinfl64bhr254mo1pfvi1n2je\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'ffinfl64bhr254mo1pfvi1n2je', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 17:00:56'),
(4779, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'ffinfl64bhr254mo1pfvi1n2je', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 17:00:56'),
(4780, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"2ltg13l83hed1gmibrlt9nr80h\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '2ltg13l83hed1gmibrlt9nr80h', '/sistema-ia.php', '', '2026-01-16 19:02:29'),
(4781, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '2ltg13l83hed1gmibrlt9nr80h', '/sistema-ia.php', '', '2026-01-16 19:02:29'),
(4782, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"iualmjo74ktu658dkcq2kaicfk\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'iualmjo74ktu658dkcq2kaicfk', '/sistema-ia.php', '', '2026-01-16 19:03:03'),
(4783, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'iualmjo74ktu658dkcq2kaicfk', '/sistema-ia.php', '', '2026-01-16 19:03:03'),
(4784, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"5q9emhtqms7uibjsjrpqlqlf0a\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '5q9emhtqms7uibjsjrpqlqlf0a', '/sistema-ia.php', '', '2026-01-16 19:03:20'),
(4785, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '5q9emhtqms7uibjsjrpqlqlf0a', '/sistema-ia.php', '', '2026-01-16 19:03:20'),
(4786, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"pchnbj305qht946m38qvt4l409\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'pchnbj305qht946m38qvt4l409', '/sistema-ia.php', '', '2026-01-16 19:03:52'),
(4787, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'pchnbj305qht946m38qvt4l409', '/sistema-ia.php', '', '2026-01-16 19:03:52'),
(4788, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"iu9u3o5mr6jho9vg5gspips2o7\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'iu9u3o5mr6jho9vg5gspips2o7', '/sistema-ia.php', '', '2026-01-16 19:07:26'),
(4789, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'iu9u3o5mr6jho9vg5gspips2o7', '/sistema-ia.php', '', '2026-01-16 19:07:26'),
(4790, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"q9s7215d79gaediqbe7osgo5gs\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'q9s7215d79gaediqbe7osgo5gs', '/sistema-ia.php', '', '2026-01-16 19:07:27'),
(4791, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'q9s7215d79gaediqbe7osgo5gs', '/sistema-ia.php', '', '2026-01-16 19:07:27'),
(4792, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"216.73.216.50\", \"sessao_nova\": \"1477c1p9marv0oqdkn2rc16h7j\", \"sessao_antiga\": \"7pgr9outb8rf8to3d1lhl7sqj0\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '1477c1p9marv0oqdkn2rc16h7j', '/sistema-ia.php', '', '2026-01-16 20:32:58'),
(4793, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.50\", \"sessao_id\": \"1477c1p9marv0oqdkn2rc16h7j\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '1477c1p9marv0oqdkn2rc16h7j', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-16 20:32:58'),
(4794, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.50', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '1477c1p9marv0oqdkn2rc16h7j', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-16 20:32:58');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES
(4795, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.137.153\", \"sessao_id\": \"m05ijr33vf5c7luqbaa6kfcrlb\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'm05ijr33vf5c7luqbaa6kfcrlb', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 20:33:54'),
(4796, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.137.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'm05ijr33vf5c7luqbaa6kfcrlb', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 20:33:54'),
(4797, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"179.247.233.166\", \"sessao_id\": \"rsvg069eekca1o4muqn43pcg86\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '179.247.233.166', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'rsvg069eekca1o4muqn43pcg86', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 23:27:39'),
(4798, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '179.247.233.166', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'rsvg069eekca1o4muqn43pcg86', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-16 23:27:39'),
(4799, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"57.141.6.30\", \"sessao_id\": \"3ctm7pqdq1qij48lkou9t6un5h\", \"user_agent\": \"meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)\"}', '57.141.6.30', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', '3ctm7pqdq1qij48lkou9t6un5h', '/sistema-ia.php', '', '2026-01-17 12:53:09'),
(4800, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '57.141.6.30', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', '3ctm7pqdq1qij48lkou9t6un5h', '/sistema-ia.php', '', '2026-01-17 12:53:09'),
(4801, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.36\", \"sessao_id\": \"423uj5vtin4g8j72p5eoina95f\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36\"}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', '423uj5vtin4g8j72p5eoina95f', '/sistema-ia.php', '', '2026-01-18 06:21:13'),
(4802, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', '423uj5vtin4g8j72p5eoina95f', '/sistema-ia.php', '', '2026-01-18 06:21:13'),
(4803, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.189.32\", \"sessao_id\": \"e55p907lq1ukeotna1eloskuvk\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.189.32', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'e55p907lq1ukeotna1eloskuvk', '/sistema-ia.php', '', '2026-01-18 14:38:02'),
(4804, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.189.32', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'e55p907lq1ukeotna1eloskuvk', '/sistema-ia.php', '', '2026-01-18 14:38:02'),
(4805, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.220.149.23\", \"sessao_id\": \"9d0t621hrqi8noe0mtcg44bb8t\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\"}', '66.220.149.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', '9d0t621hrqi8noe0mtcg44bb8t', '/sistema-ia.php', 'https://www.facebook.com/', '2026-01-18 14:38:04'),
(4806, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.220.149.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', '9d0t621hrqi8noe0mtcg44bb8t', '/sistema-ia.php', 'https://www.facebook.com/', '2026-01-18 14:38:04'),
(4807, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:43:55'),
(4808, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:43:55'),
(4809, 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:03'),
(4810, 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:03'),
(4811, 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"N&M Refrigeração \"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:15'),
(4812, 'conversa_etapa', 'Nome recebido', '{\"nome\": \"N&M Refrigeração\", \"etapa\": \"nome\", \"primeiro_nome\": \"N&M\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:15'),
(4813, 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"(88) 8 8888-8888\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:25'),
(4814, 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"88888888888\", \"cliente_existente\": true, \"telefone_formatado\": \"(88) 8 8888-8888\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:25'),
(4815, 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"Instalação de ar condicionado\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:29'),
(4816, 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:29'),
(4817, 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:34'),
(4818, 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"1\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:34'),
(4819, 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:34'),
(4820, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7197}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:37'),
(4821, 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"nao\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:37'),
(4822, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7187}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:47'),
(4823, 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"12000\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:47'),
(4824, 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"12000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:44:47'),
(4825, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7174}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:00'),
(4826, 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:00'),
(4827, 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:00'),
(4828, 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 4, \"total_verificadas\": 14}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:00'),
(4829, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7166}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:08'),
(4830, 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"2026-01-28\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:08'),
(4831, 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2026-01-28\", \"data_formatada\": \"28/01/2026\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:08'),
(4832, 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2026-01-28\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:08'),
(4833, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7163}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:11'),
(4834, 'processamento_tentativa', 'Processando ação: selecionar_horario', '{\"acao\": \"selecionar_horario\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"08:00\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:11'),
(4835, 'horario_selecionado', 'Horário selecionado para agendamento', '{\"data\": \"2026-01-28\", \"horario\": \"08:00\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:11'),
(4836, 'resumo_mostrado', 'Resumo do agendamento mostrado ao usuário', '{\"cliente\": \"N&M Refrigeração\", \"valor_total\": 350, \"tem_desconto\": false, \"total_servicos\": 1}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:11'),
(4837, 'rate_limit', 'Tentativa bloqueada - IP: 200.232.138.112', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2026-01-18 16:44:34\", \"tempo_restante\": 7160}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4838, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"2agl2uu1nh6ca4595vuocsj9ic\", \"resposta\": \"sim\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4839, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": \"2026-01-28\", \"hora\": \"08:00\", \"cliente\": \"N&M Refrigeração\", \"total_servicos\": 1}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4840, 'cliente_novo', 'Novo cliente criado', '{\"nome\": \"N&M Refrigeração\", \"telefone\": \"88888888888\", \"cliente_id\": \"109\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4841, 'orcamento_criado', 'Orçamento criado', '{\"cliente_id\": \"109\", \"valor_total\": 350, \"orcamento_id\": \"166\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4842, 'agendamento_criado', 'Agendamento criado com sucesso', '{\"data\": \"2026-01-28\", \"hora\": \"08:00\", \"cliente_id\": \"109\", \"valor_total\": 350, \"orcamento_id\": \"166\", \"agendamento_id\": \"164\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4843, 'agendamento_concluido', 'Agendamento concluído com sucesso ', '{\"data\": \"2026-01-28\", \"hora\": \"08:00\", \"cliente\": \"N&M Refrigeração\", \"telefone\": \"88888888888\", \"valor_total\": 350, \"agendamento_id\": \"164\", \"tempo_conversa\": 79}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2agl2uu1nh6ca4595vuocsj9ic', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-18 14:45:14'),
(4844, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.207\", \"sessao_id\": \"ncl27csubnvuehlsvqpq6ip85q\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'ncl27csubnvuehlsvqpq6ip85q', '/sistema-ia.php', '', '2026-01-18 20:04:02'),
(4845, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'ncl27csubnvuehlsvqpq6ip85q', '/sistema-ia.php', '', '2026-01-18 20:04:02'),
(4846, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.208\", \"sessao_id\": \"m424qibh6hucqh6qto8hropohv\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'm424qibh6hucqh6qto8hropohv', '/sistema-ia.php', '', '2026-01-18 20:19:48'),
(4847, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'm424qibh6hucqh6qto8hropohv', '/sistema-ia.php', '', '2026-01-18 20:19:48'),
(4848, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.208\", \"sessao_id\": \"f01hr8cbpur1gcre09h7n6dkdt\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'f01hr8cbpur1gcre09h7n6dkdt', '/sistema-ia.php', '', '2026-01-18 20:39:11'),
(4849, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.208', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'f01hr8cbpur1gcre09h7n6dkdt', '/sistema-ia.php', '', '2026-01-18 20:39:11'),
(4850, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.12\", \"sessao_id\": \"0j3770s1olh0nltcl6r0oj0ibe\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '0j3770s1olh0nltcl6r0oj0ibe', '/sistema-ia.php', '', '2026-01-19 00:59:06'),
(4851, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '0j3770s1olh0nltcl6r0oj0ibe', '/sistema-ia.php', '', '2026-01-19 00:59:06'),
(4852, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.207\", \"sessao_id\": \"i91n0tdhh64oa68bq3k1p4j6ll\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'i91n0tdhh64oa68bq3k1p4j6ll', '/sistema-ia.php', '', '2026-01-19 04:25:42'),
(4853, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'i91n0tdhh64oa68bq3k1p4j6ll', '/sistema-ia.php', '', '2026-01-19 04:25:42'),
(4854, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"sl3l92hfmr8c5admtsg1d422ka\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', 'sl3l92hfmr8c5admtsg1d422ka', '/sistema-ia.php', '', '2026-01-19 06:20:09'),
(4855, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', 'sl3l92hfmr8c5admtsg1d422ka', '/sistema-ia.php', '', '2026-01-19 06:20:09'),
(4856, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.210\", \"sessao_id\": \"5m6j7tgejfvgkf96252n3b72u5\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.210', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '5m6j7tgejfvgkf96252n3b72u5', '/sistema-ia.php', '', '2026-01-19 06:38:32'),
(4857, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.210', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '5m6j7tgejfvgkf96252n3b72u5', '/sistema-ia.php', '', '2026-01-19 06:38:32'),
(4858, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"64.233.172.230\", \"sessao_id\": \"m1vnsvthhmiog44nk54ulhujiu\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36\"}', '64.233.172.230', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', 'm1vnsvthhmiog44nk54ulhujiu', '/sistema-ia.php', '', '2026-01-20 06:20:31'),
(4859, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '64.233.172.230', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.169 Safari/537.36', 'm1vnsvthhmiog44nk54ulhujiu', '/sistema-ia.php', '', '2026-01-20 06:20:31'),
(4860, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.242.39\", \"sessao_id\": \"f4m9du24daqgpkat597u6dcmnj\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.242.39', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'f4m9du24daqgpkat597u6dcmnj', '/sistema-ia.php', 'https://nmrefrigeracao.business/', '2026-01-20 09:29:35'),
(4861, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.242.39', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'f4m9du24daqgpkat597u6dcmnj', '/sistema-ia.php', 'https://nmrefrigeracao.business/', '2026-01-20 09:29:35'),
(4862, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"74.7.242.39\", \"sessao_nova\": \"f4m9du24daqgpkat597u6dcmnj\", \"sessao_antiga\": \"f4m9du24daqgpkat597u6dcmnj\"}', '74.7.242.39', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'f4m9du24daqgpkat597u6dcmnj', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php?servico=8', '2026-01-20 09:29:50'),
(4863, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.242.39\", \"sessao_id\": \"f4m9du24daqgpkat597u6dcmnj\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.242.39', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'f4m9du24daqgpkat597u6dcmnj', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-20 09:29:50'),
(4864, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.242.39', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'f4m9du24daqgpkat597u6dcmnj', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-20 09:29:50'),
(4865, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.83.8\", \"sessao_id\": \"tkoajkj440e2pd071bf0pu1kfa\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.83.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'tkoajkj440e2pd071bf0pu1kfa', '/sistema-ia.php', '', '2026-01-21 06:19:49'),
(4866, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.83.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'tkoajkj440e2pd071bf0pu1kfa', '/sistema-ia.php', '', '2026-01-21 06:19:49'),
(4867, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"rrqt2j8lgv0slqiliuv9np6tta\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'rrqt2j8lgv0slqiliuv9np6tta', '/sistema-ia.php', '', '2026-01-21 19:37:01'),
(4868, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'rrqt2j8lgv0slqiliuv9np6tta', '/sistema-ia.php', '', '2026-01-21 19:37:01'),
(4869, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"195.178.110.192\", \"sessao_nova\": \"v6bj0qngt52lnactjn69rlchir\", \"sessao_antiga\": \"sifk3fp83m20cne76fed9kqs3g\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'v6bj0qngt52lnactjn69rlchir', '/sistema-ia.php', '', '2026-01-21 19:37:02'),
(4870, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"md5cvdqdmf9c9ecu7ad67c1h8s\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'md5cvdqdmf9c9ecu7ad67c1h8s', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4871, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'md5cvdqdmf9c9ecu7ad67c1h8s', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4872, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"4j46ah6997muu211m7h3qh1497\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '4j46ah6997muu211m7h3qh1497', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4873, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"piuq6s7le3p4g3ng0ec7bp4hkd\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'piuq6s7le3p4g3ng0ec7bp4hkd', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4874, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"ivkeojvlik710sjsj3ppsdqaus\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'ivkeojvlik710sjsj3ppsdqaus', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4875, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'piuq6s7le3p4g3ng0ec7bp4hkd', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4876, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '4j46ah6997muu211m7h3qh1497', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4877, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'ivkeojvlik710sjsj3ppsdqaus', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4878, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.192\", \"sessao_id\": \"j018mphvbln36l0q8216920p5d\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'j018mphvbln36l0q8216920p5d', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4879, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.192', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'j018mphvbln36l0q8216920p5d', '/sistema-ia.php', '', '2026-01-21 19:37:03'),
(4880, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.199\", \"sessao_id\": \"livb0r6p0de5npe3p21vv1ufe1\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.199', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'livb0r6p0de5npe3p21vv1ufe1', '/sistema-ia.php', '', '2026-01-21 23:20:38'),
(4881, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.199', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'livb0r6p0de5npe3p21vv1ufe1', '/sistema-ia.php', '', '2026-01-21 23:20:38'),
(4882, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.64\", \"sessao_id\": \"qfe2c70l2e8cfpd32duql6ge7m\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'qfe2c70l2e8cfpd32duql6ge7m', '/sistema-ia.php', '', '2026-01-22 01:22:25'),
(4883, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'qfe2c70l2e8cfpd32duql6ge7m', '/sistema-ia.php', '', '2026-01-22 01:22:25'),
(4884, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.215\", \"sessao_id\": \"fbd1db33s6k5agm32gk7vol6vo\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'fbd1db33s6k5agm32gk7vol6vo', '/sistema-ia.php', '', '2026-01-22 01:23:18'),
(4885, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'fbd1db33s6k5agm32gk7vol6vo', '/sistema-ia.php', '', '2026-01-22 01:23:18'),
(4886, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.65\", \"sessao_id\": \"bqa113no5ejecn2t7jl0aecdnf\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.65', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'bqa113no5ejecn2t7jl0aecdnf', '/sistema-ia.php', '', '2026-01-22 01:23:42'),
(4887, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.65', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'bqa113no5ejecn2t7jl0aecdnf', '/sistema-ia.php', '', '2026-01-22 01:23:42'),
(4888, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.215\", \"sessao_id\": \"pf3u462d5q5ak4nq5g15kub45k\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'pf3u462d5q5ak4nq5g15kub45k', '/sistema-ia.php', '', '2026-01-22 01:24:04'),
(4889, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'pf3u462d5q5ak4nq5g15kub45k', '/sistema-ia.php', '', '2026-01-22 01:24:04'),
(4890, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.62\", \"sessao_id\": \"kdapk8hi9bkra84tko9eei6uq5\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.62', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'kdapk8hi9bkra84tko9eei6uq5', '/sistema-ia.php', '', '2026-01-22 01:24:31'),
(4891, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.62', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'kdapk8hi9bkra84tko9eei6uq5', '/sistema-ia.php', '', '2026-01-22 01:24:31'),
(4892, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.245\", \"sessao_id\": \"7gfokvff3hq16i1rcjmj5vpr0s\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.245', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', '7gfokvff3hq16i1rcjmj5vpr0s', '/sistema-ia.php', '', '2026-01-22 01:24:54'),
(4893, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.245', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', '7gfokvff3hq16i1rcjmj5vpr0s', '/sistema-ia.php', '', '2026-01-22 01:24:54'),
(4894, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.195\", \"sessao_id\": \"8662eustmj72776p3hf65js0an\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.195', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '8662eustmj72776p3hf65js0an', '/sistema-ia.php', '', '2026-01-22 02:33:12'),
(4895, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.195', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '8662eustmj72776p3hf65js0an', '/sistema-ia.php', '', '2026-01-22 02:33:12'),
(4896, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.249.6\", \"sessao_id\": \"6jrq79edclic80rcb5bht5sl3b\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.249.6', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '6jrq79edclic80rcb5bht5sl3b', '/sistema-ia.php', '', '2026-01-22 03:16:42'),
(4897, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.249.6', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '6jrq79edclic80rcb5bht5sl3b', '/sistema-ia.php', '', '2026-01-22 03:16:42'),
(4898, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.249.1\", \"sessao_id\": \"h1skmd9f0jh4jdjq5l6cjnr8u0\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.249.1', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'h1skmd9f0jh4jdjq5l6cjnr8u0', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4899, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.249.1', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'h1skmd9f0jh4jdjq5l6cjnr8u0', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4900, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"173.252.83.116\", \"sessao_id\": \"as9ii0s2apm8qg9784ah30u9k5\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '173.252.83.116', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'as9ii0s2apm8qg9784ah30u9k5', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4901, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '173.252.83.116', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'as9ii0s2apm8qg9784ah30u9k5', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4902, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.249.7\", \"sessao_id\": \"iofsgpo559kbvk608skc5nhjah\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.249.7', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'iofsgpo559kbvk608skc5nhjah', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4903, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.249.7', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'iofsgpo559kbvk608skc5nhjah', '/sistema-ia.php', '', '2026-01-22 03:16:44'),
(4904, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.12\", \"sessao_id\": \"af6s3kp0h3ol6m8225m2n2n92g\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'af6s3kp0h3ol6m8225m2n2n92g', '/sistema-ia.php', '', '2026-01-22 03:22:57'),
(4905, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'af6s3kp0h3ol6m8225m2n2n92g', '/sistema-ia.php', '', '2026-01-22 03:22:57'),
(4906, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.12\", \"sessao_id\": \"mosod495obb3ql0kfo8olvjcp4\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'mosod495obb3ql0kfo8olvjcp4', '/sistema-ia.php', '', '2026-01-22 03:46:00'),
(4907, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.12', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'mosod495obb3ql0kfo8olvjcp4', '/sistema-ia.php', '', '2026-01-22 03:46:00'),
(4908, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.207\", \"sessao_id\": \"g96vaggu9ltq90dbnjth9lopki\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'g96vaggu9ltq90dbnjth9lopki', '/sistema-ia.php', '', '2026-01-22 04:03:21'),
(4909, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.207', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'g96vaggu9ltq90dbnjth9lopki', '/sistema-ia.php', '', '2026-01-22 04:03:21'),
(4910, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"s3o19a529d0h1be7fq5e2mqck2\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 's3o19a529d0h1be7fq5e2mqck2', '/sistema-ia.php', '', '2026-01-22 06:17:01'),
(4911, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 's3o19a529d0h1be7fq5e2mqck2', '/sistema-ia.php', '', '2026-01-22 06:17:01'),
(4912, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.19\", \"sessao_id\": \"o3f6kj8ui2amhj92a65ns96a5c\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.19', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'o3f6kj8ui2amhj92a65ns96a5c', '/sistema-ia.php', '', '2026-01-22 08:10:46'),
(4913, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.19', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'o3f6kj8ui2amhj92a65ns96a5c', '/sistema-ia.php', '', '2026-01-22 08:10:46'),
(4914, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"64.233.172.229\", \"sessao_id\": \"qsbens52g0m7490i3vfivte8dg\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '64.233.172.229', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'qsbens52g0m7490i3vfivte8dg', '/sistema-ia.php', '', '2026-01-23 06:17:37'),
(4915, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '64.233.172.229', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'qsbens52g0m7490i3vfivte8dg', '/sistema-ia.php', '', '2026-01-23 06:17:37'),
(4916, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.59.100.90\", \"sessao_id\": \"f3iaen6ipggefhvs07c39hjiej\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36\"}', '191.59.100.90', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'f3iaen6ipggefhvs07c39hjiej', '/sistema-ia.php', '', '2026-01-23 12:12:54'),
(4917, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.59.100.90', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'f3iaen6ipggefhvs07c39hjiej', '/sistema-ia.php', '', '2026-01-23 12:12:54'),
(4918, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.102.8.200\", \"sessao_id\": \"1peu30f53gpn2im24ofdmukogc\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.102.8.200', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '1peu30f53gpn2im24ofdmukogc', '/sistema-ia.php', '', '2026-01-24 06:12:50'),
(4919, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.102.8.200', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '1peu30f53gpn2im24ofdmukogc', '/sistema-ia.php', '', '2026-01-24 06:12:50'),
(4920, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"57.141.6.8\", \"sessao_id\": \"89jon9prlhide8g7jtfkkg71sr\", \"user_agent\": \"meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)\"}', '57.141.6.8', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', '89jon9prlhide8g7jtfkkg71sr', '/sistema-ia.php', '', '2026-01-24 13:46:47'),
(4921, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '57.141.6.8', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', '89jon9prlhide8g7jtfkkg71sr', '/sistema-ia.php', '', '2026-01-24 13:46:47'),
(4922, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.102.8.198\", \"sessao_id\": \"4eu4eta8lsuf3fqskmgjem65ji\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.102.8.198', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '4eu4eta8lsuf3fqskmgjem65ji', '/sistema-ia.php', '', '2026-01-25 06:20:46'),
(4923, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.102.8.198', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '4eu4eta8lsuf3fqskmgjem65ji', '/sistema-ia.php', '', '2026-01-25 06:20:46'),
(4924, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"rg67q09d4t63tnrdtr4fb2eb3i\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'rg67q09d4t63tnrdtr4fb2eb3i', '/sistema-ia.php', '', '2026-01-26 06:16:48'),
(4925, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'rg67q09d4t63tnrdtr4fb2eb3i', '/sistema-ia.php', '', '2026-01-26 06:16:48');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES
(4926, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.36\", \"sessao_id\": \"9n73plimtv5jntr8k6ki0lhols\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '9n73plimtv5jntr8k6ki0lhols', '/sistema-ia.php', '', '2026-01-27 06:12:29'),
(4927, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '9n73plimtv5jntr8k6ki0lhols', '/sistema-ia.php', '', '2026-01-27 06:12:29'),
(4928, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"173.252.83.4\", \"sessao_id\": \"bo1vuic3vu25ci7eir8n45b6uj\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '173.252.83.4', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'bo1vuic3vu25ci7eir8n45b6uj', '/sistema-ia.php', '', '2026-01-27 08:12:52'),
(4929, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '173.252.83.4', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'bo1vuic3vu25ci7eir8n45b6uj', '/sistema-ia.php', '', '2026-01-27 08:12:52'),
(4930, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.184.9\", \"sessao_id\": \"pv4rpf35f02au4utd8jjk827ft\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.184.9', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'pv4rpf35f02au4utd8jjk827ft', '/sistema-ia.php', '', '2026-01-27 08:12:52'),
(4931, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.184.9', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'pv4rpf35f02au4utd8jjk827ft', '/sistema-ia.php', '', '2026-01-27 08:12:52'),
(4932, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"173.252.127.1\", \"sessao_id\": \"rsrigii2e51haubhmfeprdldv1\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36\"}', '173.252.127.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'rsrigii2e51haubhmfeprdldv1', '/sistema-ia.php', 'http://m.facebook.com', '2026-01-27 08:13:07'),
(4933, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '173.252.127.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'rsrigii2e51haubhmfeprdldv1', '/sistema-ia.php', 'http://m.facebook.com', '2026-01-27 08:13:07'),
(4934, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.220.149.37\", \"sessao_id\": \"mhh2q0hlui3gkts7o0ktntfqai\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36\"}', '66.220.149.37', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'mhh2q0hlui3gkts7o0ktntfqai', '/sistema-ia.php', 'http://m.facebook.com', '2026-01-27 08:17:16'),
(4935, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.220.149.37', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'mhh2q0hlui3gkts7o0ktntfqai', '/sistema-ia.php', 'http://m.facebook.com', '2026-01-27 08:17:16'),
(4936, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"217.113.194.70\", \"sessao_nova\": \"tc89ptuc2a4p8fonlvena1nkpd\", \"sessao_antiga\": \"nuukt9g0vpmtmefjstpjm1siam\"}', '217.113.194.70', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'tc89ptuc2a4p8fonlvena1nkpd', '/sistema-ia.php', '', '2026-01-27 09:18:15'),
(4937, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"mrnpn03e4hnlporhet32trgnau\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'mrnpn03e4hnlporhet32trgnau', '/sistema-ia.php', '', '2026-01-27 21:12:09'),
(4938, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'mrnpn03e4hnlporhet32trgnau', '/sistema-ia.php', '', '2026-01-27 21:12:09'),
(4939, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"20lsrdgfi6fdomtddttqiujq88\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '20lsrdgfi6fdomtddttqiujq88', '/sistema-ia.php', '', '2026-01-27 21:12:14'),
(4940, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '20lsrdgfi6fdomtddttqiujq88', '/sistema-ia.php', '', '2026-01-27 21:12:14'),
(4941, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"thoiaorp0v2cem2f7lu8emv8q3\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'thoiaorp0v2cem2f7lu8emv8q3', '/sistema-ia.php', '', '2026-01-27 21:13:52'),
(4942, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'thoiaorp0v2cem2f7lu8emv8q3', '/sistema-ia.php', '', '2026-01-27 21:13:52'),
(4943, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"38grpp75gh9adrcorlc8re05mn\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '38grpp75gh9adrcorlc8re05mn', '/sistema-ia.php', '', '2026-01-27 21:13:52'),
(4944, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '38grpp75gh9adrcorlc8re05mn', '/sistema-ia.php', '', '2026-01-27 21:13:52'),
(4945, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"595a6f3rd1b7cq9tg1gllcnbg6\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '595a6f3rd1b7cq9tg1gllcnbg6', '/sistema-ia.php', '', '2026-01-27 21:16:06'),
(4946, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', '595a6f3rd1b7cq9tg1gllcnbg6', '/sistema-ia.php', '', '2026-01-27 21:16:06'),
(4947, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"b54utl8h9nqmlhkc2lkeka5fp4\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'b54utl8h9nqmlhkc2lkeka5fp4', '/sistema-ia.php', '', '2026-01-27 22:31:24'),
(4948, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'b54utl8h9nqmlhkc2lkeka5fp4', '/sistema-ia.php', '', '2026-01-27 22:31:24'),
(4949, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"216.73.216.172\", \"sessao_nova\": \"m223bchaotcrp4mgdo075ta1q3\", \"sessao_antiga\": \"csicrdr2el33kmh4nmtug55l17\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'm223bchaotcrp4mgdo075ta1q3', '/sistema-ia.php', '', '2026-01-27 22:34:33'),
(4950, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"216.73.216.172\", \"sessao_id\": \"m223bchaotcrp4mgdo075ta1q3\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)\"}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'm223bchaotcrp4mgdo075ta1q3', '/sistema-ia.php', 'https://www.nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-27 22:34:33'),
(4951, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '216.73.216.172', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'm223bchaotcrp4mgdo075ta1q3', '/sistema-ia.php', 'https://www.nmrefrigeracao.business/sistema-ia.php?limpar=1', '2026-01-27 22:34:33'),
(4952, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.36\", \"sessao_id\": \"g56n2e1fovhnc9avlov0nnlu6h\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'g56n2e1fovhnc9avlov0nnlu6h', '/sistema-ia.php', '', '2026-01-28 06:11:24'),
(4953, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'g56n2e1fovhnc9avlov0nnlu6h', '/sistema-ia.php', '', '2026-01-28 06:11:24'),
(4954, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"30i3fmvv401k3vvsohf1m5q483\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '30i3fmvv401k3vvsohf1m5q483', '/sistema-ia.php', '', '2026-01-28 10:49:56'),
(4955, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '30i3fmvv401k3vvsohf1m5q483', '/sistema-ia.php', '', '2026-01-28 10:49:56'),
(4956, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"h8gtsnur6el0bpk5ur6ai7fg5n\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'h8gtsnur6el0bpk5ur6ai7fg5n', '/sistema-ia.php', '', '2026-01-28 10:50:02'),
(4957, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'h8gtsnur6el0bpk5ur6ai7fg5n', '/sistema-ia.php', '', '2026-01-28 10:50:02'),
(4958, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"b8usin00fr36ol3lt755bjd9kt\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'b8usin00fr36ol3lt755bjd9kt', '/sistema-ia.php', '', '2026-01-28 10:50:06'),
(4959, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'b8usin00fr36ol3lt755bjd9kt', '/sistema-ia.php', '', '2026-01-28 10:50:06'),
(4960, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"h2p3l2jefti5p09kkt0m12j461\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'h2p3l2jefti5p09kkt0m12j461', '/sistema-ia.php', '', '2026-01-28 10:50:08'),
(4961, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'h2p3l2jefti5p09kkt0m12j461', '/sistema-ia.php', '', '2026-01-28 10:50:08'),
(4962, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"hu9dr3h6nhksri8cqvg2co3661\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'hu9dr3h6nhksri8cqvg2co3661', '/sistema-ia.php', '', '2026-01-28 10:50:10'),
(4963, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'hu9dr3h6nhksri8cqvg2co3661', '/sistema-ia.php', '', '2026-01-28 10:50:10'),
(4964, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"dpbqm967tos85sqjc8va2bs12d\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'dpbqm967tos85sqjc8va2bs12d', '/sistema-ia.php', '', '2026-01-28 10:50:13'),
(4965, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'dpbqm967tos85sqjc8va2bs12d', '/sistema-ia.php', '', '2026-01-28 10:50:13'),
(4966, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"echtt9lcedohn1nk32m1uo8lvo\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'echtt9lcedohn1nk32m1uo8lvo', '/sistema-ia.php', '', '2026-01-28 10:50:15'),
(4967, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'echtt9lcedohn1nk32m1uo8lvo', '/sistema-ia.php', '', '2026-01-28 10:50:15'),
(4968, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"idsuj4ska3ugdd8u5t3vc3h9ic\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'idsuj4ska3ugdd8u5t3vc3h9ic', '/sistema-ia.php', '', '2026-01-28 10:50:17'),
(4969, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'idsuj4ska3ugdd8u5t3vc3h9ic', '/sistema-ia.php', '', '2026-01-28 10:50:17'),
(4970, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"r59dpg6dt9eekb948rsuqindmk\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'r59dpg6dt9eekb948rsuqindmk', '/sistema-ia.php', '', '2026-01-28 10:50:19'),
(4971, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'r59dpg6dt9eekb948rsuqindmk', '/sistema-ia.php', '', '2026-01-28 10:50:19'),
(4972, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"s9t5drft0n34abalmlce6olaik\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 's9t5drft0n34abalmlce6olaik', '/sistema-ia.php', '', '2026-01-28 10:50:22'),
(4973, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 's9t5drft0n34abalmlce6olaik', '/sistema-ia.php', '', '2026-01-28 10:50:22'),
(4974, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"cg45afvuhels3je57mt3opde8l\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'cg45afvuhels3je57mt3opde8l', '/sistema-ia.php', '', '2026-01-28 10:50:23'),
(4975, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'cg45afvuhels3je57mt3opde8l', '/sistema-ia.php', '', '2026-01-28 10:50:23'),
(4976, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"1237l0tefpgn3819dd7266bua0\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '1237l0tefpgn3819dd7266bua0', '/sistema-ia.php', '', '2026-01-28 10:50:27'),
(4977, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '1237l0tefpgn3819dd7266bua0', '/sistema-ia.php', '', '2026-01-28 10:50:27'),
(4978, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"49dqqkrasrdp6df9i8g4j477al\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '49dqqkrasrdp6df9i8g4j477al', '/sistema-ia.php', '', '2026-01-28 10:50:30'),
(4979, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '49dqqkrasrdp6df9i8g4j477al', '/sistema-ia.php', '', '2026-01-28 10:50:30'),
(4980, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"23.171.176.36\", \"sessao_id\": \"uem2bf5e37tj0unbs5o6u4olmp\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'uem2bf5e37tj0unbs5o6u4olmp', '/sistema-ia.php', '', '2026-01-28 10:50:33'),
(4981, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '23.171.176.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'uem2bf5e37tj0unbs5o6u4olmp', '/sistema-ia.php', '', '2026-01-28 10:50:33'),
(4982, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"c56g5nli2c1adtvtp639fbrpom\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'c56g5nli2c1adtvtp639fbrpom', '/sistema-ia.php', '', '2026-01-29 06:08:02'),
(4983, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'c56g5nli2c1adtvtp639fbrpom', '/sistema-ia.php', '', '2026-01-29 06:08:02'),
(4984, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.59.102.56\", \"sessao_id\": \"7e3cvqt5s2gsip9c93h953sf5m\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '191.59.102.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '7e3cvqt5s2gsip9c93h953sf5m', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 12:18:33'),
(4985, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.59.102.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '7e3cvqt5s2gsip9c93h953sf5m', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 12:18:33'),
(4986, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"7e3cvqt5s2gsip9c93h953sf5m\", \"resposta\": \"sim\"}', '191.59.102.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '7e3cvqt5s2gsip9c93h953sf5m', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 12:18:33'),
(4987, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": null, \"hora\": null, \"cliente\": null, \"total_servicos\": 0}', '191.59.102.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '7e3cvqt5s2gsip9c93h953sf5m', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 12:18:33'),
(4988, 'agendamento_erro', 'Erro ao salvar agendamento: SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null', '{\"erro\": \"SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null\", \"trace\": \"#0 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(1389): PDOStatement->execute()\\n#1 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(831): salvarAgendamento()\\n#2 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(424): processarRespostaSimples()\\n#3 {main}\", \"cliente\": null, \"telefone\": null}', '191.59.102.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '7e3cvqt5s2gsip9c93h953sf5m', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 12:18:33'),
(4989, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.164.220.109\", \"sessao_id\": \"h53u3uujthqjl94o3v2n9jnjil\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Safari/605.1.15\"}', '69.164.220.109', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Safari/605.1.15', 'h53u3uujthqjl94o3v2n9jnjil', '/sistema-ia.php', '', '2026-01-29 22:21:40'),
(4990, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.164.220.109', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Safari/605.1.15', 'h53u3uujthqjl94o3v2n9jnjil', '/sistema-ia.php', '', '2026-01-29 22:21:40'),
(4991, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"bqhui47cpopn0fb68phpe1f8ug\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:18'),
(4992, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:18'),
(4993, 'processamento_tentativa', 'Processando ação: confirmar_resumo', '{\"acao\": \"confirmar_resumo\", \"etapa\": 1, \"sessao\": \"bqhui47cpopn0fb68phpe1f8ug\", \"resposta\": \"sim\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:18'),
(4994, 'salvamento_inicio', 'Iniciando salvamento do agendamento', '{\"data\": null, \"hora\": null, \"cliente\": null, \"total_servicos\": 0}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:18'),
(4995, 'agendamento_erro', 'Erro ao salvar agendamento: SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null', '{\"erro\": \"SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'nome\' cannot be null\", \"trace\": \"#0 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(1389): PDOStatement->execute()\\n#1 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(831): salvarAgendamento()\\n#2 /home/nmrefrig/domains/nmrefrigeracao.business/private_html/sistema-ia.php(424): processarRespostaSimples()\\n#3 {main}\", \"cliente\": null, \"telefone\": null}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:18'),
(4996, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"200.232.138.112\", \"sessao_nova\": \"bqhui47cpopn0fb68phpe1f8ug\", \"sessao_antiga\": \"bqhui47cpopn0fb68phpe1f8ug\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:22'),
(4997, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"bqhui47cpopn0fb68phpe1f8ug\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:22'),
(4998, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'bqhui47cpopn0fb68phpe1f8ug', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 22:36:22'),
(4999, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"vsff6v5nn0hrt4jalleuefnd49\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\"}', '200.232.138.112', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'vsff6v5nn0hrt4jalleuefnd49', '/sistema-ia.php', '', '2026-01-29 22:36:30'),
(5000, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'vsff6v5nn0hrt4jalleuefnd49', '/sistema-ia.php', '', '2026-01-29 22:36:30'),
(5001, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"40vbases91s1nidhsp2mml84f0\", \"user_agent\": \"WhatsApp/2.23.20.0\"}', '200.232.138.112', 'WhatsApp/2.23.20.0', '40vbases91s1nidhsp2mml84f0', '/sistema-ia.php', '', '2026-01-29 22:36:34'),
(5002, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'WhatsApp/2.23.20.0', '40vbases91s1nidhsp2mml84f0', '/sistema-ia.php', '', '2026-01-29 22:36:34'),
(5003, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"138.204.39.102\", \"sessao_id\": \"gnl5hnkspasecm36p7sflbg12b\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36\"}', '138.204.39.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'gnl5hnkspasecm36p7sflbg12b', '/sistema-ia.php', '', '2026-01-29 22:38:32'),
(5004, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '138.204.39.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'gnl5hnkspasecm36p7sflbg12b', '/sistema-ia.php', '', '2026-01-29 22:38:32'),
(5005, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"200.232.138.112\", \"sessao_id\": \"73ecch9kt7f8rdl2fs18ubraum\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '73ecch9kt7f8rdl2fs18ubraum', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 23:12:10'),
(5006, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '200.232.138.112', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '73ecch9kt7f8rdl2fs18ubraum', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-29 23:12:10'),
(5007, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"jelur8l9leekrdb1r3gpcnfgel\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jelur8l9leekrdb1r3gpcnfgel', '/sistema-ia.php', '', '2026-01-30 05:56:59'),
(5008, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jelur8l9leekrdb1r3gpcnfgel', '/sistema-ia.php', '', '2026-01-30 05:56:59'),
(5009, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.202.229.96\", \"sessao_id\": \"hie3muol8em5qc179tlql4albs\", \"user_agent\": \"Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 414.0.0.23.79 (iPhone16,1; iOS 26_2; pt_BR; pt; scale=3.00; 1179x2556; IABMV/1; 868652560) Safari/604.1\"}', '191.202.229.96', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 414.0.0.23.79 (iPhone16,1; iOS 26_2; pt_BR; pt; scale=3.00; 1179x2556; IABMV/1; 868652560) Safari/604.1', 'hie3muol8em5qc179tlql4albs', '/sistema-ia.php', 'https://l.instagram.com/', '2026-01-30 12:19:34'),
(5010, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.202.229.96', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 414.0.0.23.79 (iPhone16,1; iOS 26_2; pt_BR; pt; scale=3.00; 1179x2556; IABMV/1; 868652560) Safari/604.1', 'hie3muol8em5qc179tlql4albs', '/sistema-ia.php', 'https://l.instagram.com/', '2026-01-30 12:19:34'),
(5011, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.26.154.65\", \"sessao_id\": \"4hprtkp145hgkeni3k4s7um5qe\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '191.26.154.65', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '4hprtkp145hgkeni3k4s7um5qe', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-30 15:43:50'),
(5012, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.26.154.65', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '4hprtkp145hgkeni3k4s7um5qe', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2026-01-30 15:43:50'),
(5013, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.204\", \"sessao_id\": \"48un7h5dbsapuleu000lrnufvf\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.204', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '48un7h5dbsapuleu000lrnufvf', '/sistema-ia.php', '', '2026-01-30 21:05:03'),
(5014, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.204', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '48un7h5dbsapuleu000lrnufvf', '/sistema-ia.php', '', '2026-01-30 21:05:03'),
(5015, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.197\", \"sessao_id\": \"hi8b4t7gfkh80205r9gshnrk9e\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.197', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'hi8b4t7gfkh80205r9gshnrk9e', '/sistema-ia.php', '', '2026-01-31 03:07:40'),
(5016, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.197', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'hi8b4t7gfkh80205r9gshnrk9e', '/sistema-ia.php', '', '2026-01-31 03:07:40'),
(5017, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"fvlknbp59qkvb99gkimf9orpmp\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'fvlknbp59qkvb99gkimf9orpmp', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5018, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'fvlknbp59qkvb99gkimf9orpmp', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5019, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"qvsg7pujpoiuqd8205i6rm7o4l\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'qvsg7pujpoiuqd8205i6rm7o4l', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5020, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"qiqauak5k3s6f0vab6uges4ftu\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'qiqauak5k3s6f0vab6uges4ftu', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5021, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'qvsg7pujpoiuqd8205i6rm7o4l', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5022, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'qiqauak5k3s6f0vab6uges4ftu', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5023, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"8ga99te2v38qrtad2agfl2p1me\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '8ga99te2v38qrtad2agfl2p1me', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5024, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"6bqrhmuva8oo2at9gho6ua2nvp\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '6bqrhmuva8oo2at9gho6ua2nvp', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5025, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"195.178.110.108\", \"sessao_id\": \"rvdj7o8bbq1efj4gfjfdidulve\", \"user_agent\": \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\"}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'rvdj7o8bbq1efj4gfjfdidulve', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5026, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '6bqrhmuva8oo2at9gho6ua2nvp', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5027, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', '8ga99te2v38qrtad2agfl2p1me', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5028, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '195.178.110.108', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'rvdj7o8bbq1efj4gfjfdidulve', '/sistema-ia.php', '', '2026-01-31 04:34:07'),
(5029, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"ajfehcjhhd4jeeoej54fd9mrjo\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'ajfehcjhhd4jeeoej54fd9mrjo', '/sistema-ia.php', '', '2026-01-31 05:21:29'),
(5030, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'ajfehcjhhd4jeeoej54fd9mrjo', '/sistema-ia.php', '', '2026-01-31 05:21:29'),
(5031, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.1\", \"sessao_id\": \"a33gqtrdh9ghuj0qcjmaaiogp9\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.1', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'a33gqtrdh9ghuj0qcjmaaiogp9', '/sistema-ia.php', '', '2026-01-31 06:25:08'),
(5032, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.1', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'a33gqtrdh9ghuj0qcjmaaiogp9', '/sistema-ia.php', '', '2026-01-31 06:25:08'),
(5033, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.13\", \"sessao_id\": \"sie6dfqtgptvrujaojtd40brjb\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.13', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'sie6dfqtgptvrujaojtd40brjb', '/sistema-ia.php', '', '2026-01-31 08:23:22'),
(5034, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.13', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'sie6dfqtgptvrujaojtd40brjb', '/sistema-ia.php', '', '2026-01-31 08:23:22'),
(5035, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.249.1\", \"sessao_id\": \"3na1e3duapcs0pufhb60f3m25b\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.249.1', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '3na1e3duapcs0pufhb60f3m25b', '/sistema-ia.php', '', '2026-01-31 11:54:53'),
(5036, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.249.1', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '3na1e3duapcs0pufhb60f3m25b', '/sistema-ia.php', '', '2026-01-31 11:54:53'),
(5037, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.189.39\", \"sessao_id\": \"rc6b2o96do5l1imn0b7tvamiv6\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.189.39', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'rc6b2o96do5l1imn0b7tvamiv6', '/sistema-ia.php', '', '2026-01-31 11:54:53'),
(5038, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.189.39', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'rc6b2o96do5l1imn0b7tvamiv6', '/sistema-ia.php', '', '2026-01-31 11:54:53'),
(5039, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"31.13.127.116\", \"sessao_id\": \"9b7mlq7m6p706va9mvm2heg4uc\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '31.13.127.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '9b7mlq7m6p706va9mvm2heg4uc', '/sistema-ia.php', 'https://www.facebook.com/', '2026-01-31 11:56:22'),
(5040, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '31.13.127.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '9b7mlq7m6p706va9mvm2heg4uc', '/sistema-ia.php', 'https://www.facebook.com/', '2026-01-31 11:56:22'),
(5041, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"57.141.6.35\", \"sessao_id\": \"aal59aglhdhe0cuf5jkn2tas0o\", \"user_agent\": \"meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)\"}', '57.141.6.35', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'aal59aglhdhe0cuf5jkn2tas0o', '/sistema-ia.php', '', '2026-01-31 13:22:35'),
(5042, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '57.141.6.35', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'aal59aglhdhe0cuf5jkn2tas0o', '/sistema-ia.php', '', '2026-01-31 13:22:35'),
(5043, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.200\", \"sessao_id\": \"irpen0ng7vjliaus0ehoqbp4a3\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.200', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'irpen0ng7vjliaus0ehoqbp4a3', '/sistema-ia.php', '', '2026-01-31 17:06:03'),
(5044, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.200', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'irpen0ng7vjliaus0ehoqbp4a3', '/sistema-ia.php', '', '2026-01-31 17:06:03'),
(5045, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.197\", \"sessao_id\": \"tnqsujfjh71dt8scg1s378hr5r\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.197', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'tnqsujfjh71dt8scg1s378hr5r', '/sistema-ia.php', '', '2026-01-31 18:36:26'),
(5046, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.197', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'tnqsujfjh71dt8scg1s378hr5r', '/sistema-ia.php', '', '2026-01-31 18:36:26'),
(5047, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.36\", \"sessao_id\": \"uabpa099no07kotnpu7hshfdqm\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'uabpa099no07kotnpu7hshfdqm', '/sistema-ia.php', '', '2026-02-01 05:22:11'),
(5048, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.36', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'uabpa099no07kotnpu7hshfdqm', '/sistema-ia.php', '', '2026-02-01 05:22:11'),
(5049, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.64\", \"sessao_id\": \"l1ti1ela8l66n1bngqamrlir0h\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'l1ti1ela8l66n1bngqamrlir0h', '/sistema-ia.php', '', '2026-02-01 18:24:31'),
(5050, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'l1ti1ela8l66n1bngqamrlir0h', '/sistema-ia.php', '', '2026-02-01 18:24:31'),
(5051, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.71\", \"sessao_id\": \"t03ikb5sncso282fmfibln0pii\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.71', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 't03ikb5sncso282fmfibln0pii', '/sistema-ia.php', '', '2026-02-01 18:25:19'),
(5052, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.71', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 't03ikb5sncso282fmfibln0pii', '/sistema-ia.php', '', '2026-02-01 18:25:19'),
(5053, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"154.54.249.199\", \"sessao_id\": \"csgbcksklqm2h6gf9c1r9gcepm\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '154.54.249.199', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'csgbcksklqm2h6gf9c1r9gcepm', '/sistema-ia.php', '', '2026-02-01 18:25:44'),
(5054, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '154.54.249.199', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'csgbcksklqm2h6gf9c1r9gcepm', '/sistema-ia.php', '', '2026-02-01 18:25:44'),
(5055, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"154.54.249.199\", \"sessao_id\": \"ptdk2dc515qis520aei85vqlve\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '154.54.249.199', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'ptdk2dc515qis520aei85vqlve', '/sistema-ia.php', '', '2026-02-01 18:26:14');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES
(5056, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '154.54.249.199', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'ptdk2dc515qis520aei85vqlve', '/sistema-ia.php', '', '2026-02-01 18:26:14'),
(5057, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.69\", \"sessao_id\": \"1ljgufortfjrjuu6ec3qbsaum8\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.69', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', '1ljgufortfjrjuu6ec3qbsaum8', '/sistema-ia.php', '', '2026-02-01 18:26:45'),
(5058, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.69', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', '1ljgufortfjrjuu6ec3qbsaum8', '/sistema-ia.php', '', '2026-02-01 18:26:45'),
(5059, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.215\", \"sessao_id\": \"omi6qdmr3alcje82talpmcftjs\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'omi6qdmr3alcje82talpmcftjs', '/sistema-ia.php', '', '2026-02-01 18:27:08'),
(5060, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.215', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'omi6qdmr3alcje82talpmcftjs', '/sistema-ia.php', '', '2026-02-01 18:27:08'),
(5061, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"k1b1rfj741cu24eu3rfn9jikn4\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'k1b1rfj741cu24eu3rfn9jikn4', '/sistema-ia.php', '', '2026-02-02 05:17:16'),
(5062, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'k1b1rfj741cu24eu3rfn9jikn4', '/sistema-ia.php', '', '2026-02-02 05:17:16'),
(5063, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"207.46.13.130\", \"sessao_id\": \"3fper9l5n24p6t8eldcqh2fo1r\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36\"}', '207.46.13.130', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', '3fper9l5n24p6t8eldcqh2fo1r', '/sistema-ia.php', '', '2026-02-03 00:09:45'),
(5064, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '207.46.13.130', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', '3fper9l5n24p6t8eldcqh2fo1r', '/sistema-ia.php', '', '2026-02-03 00:09:45'),
(5065, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.125.215.9\", \"sessao_id\": \"20b3v148l0kkr0hqqe6dbm1v04\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '74.125.215.9', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '20b3v148l0kkr0hqqe6dbm1v04', '/sistema-ia.php', '', '2026-02-03 05:21:49'),
(5066, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.125.215.9', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '20b3v148l0kkr0hqqe6dbm1v04', '/sistema-ia.php', '', '2026-02-03 05:21:49'),
(5067, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.201\", \"sessao_id\": \"ie60qc0ua7v0ca8udvbtmjeoce\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.201', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'ie60qc0ua7v0ca8udvbtmjeoce', '/sistema-ia.php', '', '2026-02-03 08:03:30'),
(5068, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.201', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'ie60qc0ua7v0ca8udvbtmjeoce', '/sistema-ia.php', '', '2026-02-03 08:03:30'),
(5069, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.16\", \"sessao_id\": \"lh0k74burf3fff5vgfh91g9u2k\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.16', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'lh0k74burf3fff5vgfh91g9u2k', '/sistema-ia.php', '', '2026-02-03 08:25:51'),
(5070, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.16', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'lh0k74burf3fff5vgfh91g9u2k', '/sistema-ia.php', '', '2026-02-03 08:25:51'),
(5071, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.11\", \"sessao_id\": \"2ujluoftgpb7nvp4844f0smuvs\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.11', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '2ujluoftgpb7nvp4844f0smuvs', '/sistema-ia.php', '', '2026-02-03 09:24:14'),
(5072, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.11', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '2ujluoftgpb7nvp4844f0smuvs', '/sistema-ia.php', '', '2026-02-03 09:24:14'),
(5073, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"85.208.96.193\", \"sessao_id\": \"b0hv3qsrhprd2u8olpeet5ei6i\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '85.208.96.193', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'b0hv3qsrhprd2u8olpeet5ei6i', '/sistema-ia.php', '', '2026-02-03 10:27:10'),
(5074, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '85.208.96.193', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'b0hv3qsrhprd2u8olpeet5ei6i', '/sistema-ia.php', '', '2026-02-03 10:27:10'),
(5075, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.4\", \"sessao_id\": \"9mp82jq4bu135svt53p6ll3bec\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.4', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '9mp82jq4bu135svt53p6ll3bec', '/sistema-ia.php', '', '2026-02-03 12:04:53'),
(5076, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.4', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', '9mp82jq4bu135svt53p6ll3bec', '/sistema-ia.php', '', '2026-02-03 12:04:53'),
(5077, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"185.191.171.14\", \"sessao_id\": \"gbrou1slbmdm1hhhn3gkmgduvb\", \"user_agent\": \"Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)\"}', '185.191.171.14', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'gbrou1slbmdm1hhhn3gkmgduvb', '/sistema-ia.php', '', '2026-02-03 12:57:31'),
(5078, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '185.191.171.14', 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)', 'gbrou1slbmdm1hhhn3gkmgduvb', '/sistema-ia.php', '', '2026-02-03 12:57:31'),
(5079, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"173.252.127.22\", \"sessao_id\": \"mhgcsi5javuo3h6h2uii2cfr4d\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '173.252.127.22', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'mhgcsi5javuo3h6h2uii2cfr4d', '/sistema-ia.php', '', '2026-02-03 13:51:41'),
(5080, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '173.252.127.22', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'mhgcsi5javuo3h6h2uii2cfr4d', '/sistema-ia.php', '', '2026-02-03 13:51:41'),
(5081, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.189.115\", \"sessao_id\": \"nebcd89u1gpp3qlssdjlvcr03c\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.189.115', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'nebcd89u1gpp3qlssdjlvcr03c', '/sistema-ia.php', '', '2026-02-03 13:51:41'),
(5082, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.189.115', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'nebcd89u1gpp3qlssdjlvcr03c', '/sistema-ia.php', '', '2026-02-03 13:51:41'),
(5083, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"31.13.127.29\", \"sessao_id\": \"8dvsasv75b8rdki50cq21bo6o9\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', '31.13.127.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '8dvsasv75b8rdki50cq21bo6o9', '/sistema-ia.php', 'https://www.facebook.com/', '2026-02-03 13:51:51'),
(5084, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '31.13.127.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '8dvsasv75b8rdki50cq21bo6o9', '/sistema-ia.php', 'https://www.facebook.com/', '2026-02-03 13:51:51'),
(5085, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.125.215.8\", \"sessao_id\": \"3t3j4tusak1dqtgar93iaepdtr\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '3t3j4tusak1dqtgar93iaepdtr', '/sistema-ia.php', '', '2026-02-04 05:16:34'),
(5086, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '3t3j4tusak1dqtgar93iaepdtr', '/sistema-ia.php', '', '2026-02-04 05:16:34'),
(5087, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.125.215.7\", \"sessao_id\": \"a1ihg7os6kut7rqrb27jlba2og\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '74.125.215.7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'a1ihg7os6kut7rqrb27jlba2og', '/sistema-ia.php', '', '2026-02-05 05:19:53'),
(5088, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.125.215.7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'a1ihg7os6kut7rqrb27jlba2og', '/sistema-ia.php', '', '2026-02-05 05:19:53'),
(5089, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"e9oghk47afbpg3q35qro50jpv5\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'e9oghk47afbpg3q35qro50jpv5', '/sistema-ia.php', '', '2026-02-06 05:13:04'),
(5090, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'e9oghk47afbpg3q35qro50jpv5', '/sistema-ia.php', '', '2026-02-06 05:13:04'),
(5091, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"191.241.138.175\", \"sessao_id\": \"gnhpg5roidjtle9sn8cde0ncia\", \"user_agent\": \"Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36\"}', '191.241.138.175', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'gnhpg5roidjtle9sn8cde0ncia', '/sistema-ia.php', '', '2026-02-06 08:54:40'),
(5092, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '191.241.138.175', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'gnhpg5roidjtle9sn8cde0ncia', '/sistema-ia.php', '', '2026-02-06 08:54:40'),
(5093, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"8mbu285j2h4emepkbmpaavpatm\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '8mbu285j2h4emepkbmpaavpatm', '/sistema-ia.php', '', '2026-02-07 05:24:59'),
(5094, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', '8mbu285j2h4emepkbmpaavpatm', '/sistema-ia.php', '', '2026-02-07 05:24:59'),
(5095, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"skobrb07nole5b5bj4b1v6p9cs\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'skobrb07nole5b5bj4b1v6p9cs', '/sistema-ia.php', '', '2026-02-08 05:13:06'),
(5096, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'skobrb07nole5b5bj4b1v6p9cs', '/sistema-ia.php', '', '2026-02-08 05:13:06'),
(5097, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"57.141.6.32\", \"sessao_id\": \"nh5mo8adam74l6929tvs366oim\", \"user_agent\": \"meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)\"}', '57.141.6.32', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'nh5mo8adam74l6929tvs366oim', '/sistema-ia.php', '', '2026-02-08 18:18:58'),
(5098, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '57.141.6.32', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'nh5mo8adam74l6929tvs366oim', '/sistema-ia.php', '', '2026-02-08 18:18:58'),
(5099, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"57.141.6.1\", \"sessao_id\": \"j2bq9gdb7diuv7915ogevuak7o\", \"user_agent\": \"meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)\"}', '57.141.6.1', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'j2bq9gdb7diuv7915ogevuak7o', '/sistema-ia.php', '', '2026-02-08 20:38:27'),
(5100, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '57.141.6.1', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)', 'j2bq9gdb7diuv7915ogevuak7o', '/sistema-ia.php', '', '2026-02-08 20:38:27'),
(5101, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.241.45\", \"sessao_id\": \"6a8qbqnr1p2mf3peo6b19c4h0s\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.241.45', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', '6a8qbqnr1p2mf3peo6b19c4h0s', '/sistema-ia.php', 'https://nmrefrigeracao.business/', '2026-02-09 00:08:17'),
(5102, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.241.45', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', '6a8qbqnr1p2mf3peo6b19c4h0s', '/sistema-ia.php', 'https://nmrefrigeracao.business/', '2026-02-09 00:08:17'),
(5103, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.7.241.13\", \"sessao_id\": \"rj74iu0tohcocevru20v0jt7ja\", \"user_agent\": \"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)\"}', '74.7.241.13', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'rj74iu0tohcocevru20v0jt7ja', '/sistema-ia.php', 'https://www.nmrefrigeracao.business/', '2026-02-09 02:25:30'),
(5104, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.7.241.13', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'rj74iu0tohcocevru20v0jt7ja', '/sistema-ia.php', 'https://www.nmrefrigeracao.business/', '2026-02-09 02:25:30'),
(5105, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.35\", \"sessao_id\": \"jq8bqv1mt21bb0499iqasllcqo\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jq8bqv1mt21bb0499iqasllcqo', '/sistema-ia.php', '', '2026-02-09 05:07:34'),
(5106, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jq8bqv1mt21bb0499iqasllcqo', '/sistema-ia.php', '', '2026-02-09 05:07:34'),
(5107, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"173.252.107.5\", \"sessao_id\": \"0v40fs5t8jcofcta77n725mgqo\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '173.252.107.5', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '0v40fs5t8jcofcta77n725mgqo', '/sistema-ia.php', '', '2026-02-09 14:57:18'),
(5108, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '173.252.107.5', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '0v40fs5t8jcofcta77n725mgqo', '/sistema-ia.php', '', '2026-02-09 14:57:18'),
(5109, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.189.24\", \"sessao_id\": \"il1aungvqbc8oan4e0cau20t6c\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.189.24', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'il1aungvqbc8oan4e0cau20t6c', '/sistema-ia.php', '', '2026-02-09 14:57:18'),
(5110, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.189.24', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'il1aungvqbc8oan4e0cau20t6c', '/sistema-ia.php', '', '2026-02-09 14:57:18'),
(5111, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"jne55vqchv00n192c9lj3av08m\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jne55vqchv00n192c9lj3av08m', '/sistema-ia.php', '', '2026-02-10 05:02:59'),
(5112, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'jne55vqchv00n192c9lj3av08m', '/sistema-ia.php', '', '2026-02-10 05:02:59'),
(5113, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"66.249.93.34\", \"sessao_id\": \"lh1buoe5d1418c13lmv60ci657\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'lh1buoe5d1418c13lmv60ci657', '/sistema-ia.php', '', '2026-02-12 05:03:58'),
(5114, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '66.249.93.34', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'lh1buoe5d1418c13lmv60ci657', '/sistema-ia.php', '', '2026-02-12 05:03:58'),
(5115, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.234.28\", \"sessao_id\": \"cne5pi2hv148jv4gcanb0g4npj\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.234.28', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'cne5pi2hv148jv4gcanb0g4npj', '/sistema-ia.php', '', '2026-02-12 10:31:56'),
(5116, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.234.28', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'cne5pi2hv148jv4gcanb0g4npj', '/sistema-ia.php', '', '2026-02-12 10:31:56'),
(5117, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.63.189.113\", \"sessao_id\": \"a4hoodfdmf7i90jebt8h8e0gnu\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.63.189.113', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'a4hoodfdmf7i90jebt8h8e0gnu', '/sistema-ia.php', '', '2026-02-12 10:31:56'),
(5118, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.63.189.113', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'a4hoodfdmf7i90jebt8h8e0gnu', '/sistema-ia.php', '', '2026-02-12 10:31:56'),
(5119, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"31.13.127.5\", \"sessao_id\": \"vhn96bp4oj8ltukj05h70tkc9m\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36\"}', '31.13.127.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'vhn96bp4oj8ltukj05h70tkc9m', '/sistema-ia.php', 'https://www.facebook.com/', '2026-02-12 11:53:47'),
(5120, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '31.13.127.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'vhn96bp4oj8ltukj05h70tkc9m', '/sistema-ia.php', 'https://www.facebook.com/', '2026-02-12 11:53:47'),
(5121, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"69.171.231.116\", \"sessao_id\": \"6pf3858nhukkgehrdkfgmpc39p\", \"user_agent\": \"facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)\"}', '69.171.231.116', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '6pf3858nhukkgehrdkfgmpc39p', '/sistema-ia.php', '', '2026-02-12 11:54:24'),
(5122, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '69.171.231.116', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '6pf3858nhukkgehrdkfgmpc39p', '/sistema-ia.php', '', '2026-02-12 11:54:24'),
(5123, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"217.113.194.64\", \"sessao_id\": \"r7qucumivucjeq8ooo73134qob\", \"user_agent\": \"Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)\"}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'r7qucumivucjeq8ooo73134qob', '/sistema-ia.php', '', '2026-02-12 14:09:11'),
(5124, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '217.113.194.64', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'r7qucumivucjeq8ooo73134qob', '/sistema-ia.php', '', '2026-02-12 14:09:11'),
(5125, 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"217.113.194.216\", \"sessao_nova\": \"vbcjoqbpu2dedp6h5j6fe12if9\", \"sessao_antiga\": \"htntkq2su1enssa5fsnoqshn23\"}', '217.113.194.216', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'vbcjoqbpu2dedp6h5j6fe12if9', '/sistema-ia.php', '', '2026-02-12 14:09:37'),
(5126, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.125.215.8\", \"sessao_id\": \"e4b7rk7182t423e2d3u5ug3i4p\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'e4b7rk7182t423e2d3u5ug3i4p', '/sistema-ia.php', '', '2026-02-13 04:58:29'),
(5127, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'e4b7rk7182t423e2d3u5ug3i4p', '/sistema-ia.php', '', '2026-02-13 04:58:29'),
(5128, 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"74.125.215.8\", \"sessao_id\": \"c5cfuslhbno5jnp813ukgm6dt3\", \"user_agent\": \"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36\"}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'c5cfuslhbno5jnp813ukgm6dt3', '/sistema-ia.php', '', '2026-02-14 04:57:33'),
(5129, 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '74.125.215.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google-BusinessLinkVerification) Chrome/143.0.7499.192 Safari/537.36', 'c5cfuslhbno5jnp813ukgm6dt3', '/sistema-ia.php', '', '2026-02-14 04:57:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `categoria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `preco_unitario` decimal(10,2) DEFAULT NULL,
  `unidade_medida` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'unidade',
  `estoque` int DEFAULT '0',
  `estoque_minimo` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `materiais`
--

INSERT INTO `materiais` (`id`, `nome`, `descricao`, `categoria`, `preco_unitario`, `unidade_medida`, `estoque`, `estoque_minimo`, `ativo`, `data_criacao`) VALUES
(1, 'Suporte condensadora 12 mil btus tambor', 'O Suporte Condensadora &quot;Tambor&quot; para ar condicionado de 12.000 BTUs é uma peça metálica robusta, projetada para fixar a unidade externa (condensadora) na parede com segurança. Seu formato é especificamente desenhado para acomodar condensadoras cilíndricas ou &quot;tambor&quot;, garantindo o espaçamento ideal da parede para ventilação e manutenção. É geralmente fabricado em aço carbono com pintura eletrostática, conferindo alta resistência contra intempéries e corrosão.', 'Outros', 50.00, 'unidade', 2, 1, 1, '2025-11-15 10:59:44'),
(2, 'Tubo de 1/4', 'Conduz o gás refrigerante entre as unidades. Cobre de alta qualidade evita vazamentos e perda de eficiência.\r\n\r\nEsta tubulação é utilizada para as unidades de 9 a 18 mil BTUs.\r\n\r\nÉ considerado como Linha Fina (ou Tubo de Líquido): Transporta o fluido refrigerante em estado líquido, sob alta pressão e temperatura ambiente ou ligeiramente acima, da unidade externa (condensadora) para a unidade interna (evaporadora).', 'Tubulação', 20.00, 'metro', 10, 4, 0, '2025-11-17 20:31:54'),
(3, 'Tubo de Cobre 3/8', 'Tubo de cobre para linha de gás. Usado em unidades de 9.000 a 12.000 BTUs. Conduz o refrigerante em estado gasoso entre as unidades. A qualidade do cobre é crucial para evitar vazamentos e manter a eficiência do sistema.', 'Tubulação', 35.00, 'metro', 9, 5, 1, '2025-11-17 20:35:33'),
(4, 'Tubo de Cobre 1/2', 'Tubo de cobre para linha de gás de maior capacidade. Usado em unidades de 18.000 BTUs. Projetado para suportar o fluxo e a pressão de sistemas de média capacidade.', 'Tubulação', 50.00, 'metro', 0, 3, 1, '2025-11-17 20:35:33'),
(5, 'Tubo de Cobre 5/8', 'Tubo de cobre de diâmetro maior para a linha de gás. Utilizado em unidades de 24.000 BTUs. Essencial para atender à vazão necessária de sistemas de alta capacidade.', 'Tubulação', 70.00, 'metro', 10, 3, 1, '2025-11-17 20:35:33'),
(6, 'Tubo de Cobre 3/4', 'Tubo de cobre de grande diâmetro para a linha de gás. Empregado em unidades de 30.000 BTUs ou mais. Garante o transporte adequado do refrigerante em sistemas de alta potência.', 'Tubulação', 90.00, 'metro', 0, 2, 1, '2025-11-17 20:35:33'),
(7, 'Isolamento Térmico para 1/4', 'Manga de polietileno (Polipex/Thermoflex) para isolamento da linha de líquido (tubo de 1/4). Evita a formação de suor e a troca de calor indesejada com o ambiente, mantendo a eficiência energética.', 'Isolamento', 5.00, 'metro', 4, 10, 1, '2025-11-17 20:35:33'),
(8, 'Isolamento Térmico para 3/8', 'Isolamento térmico para tubo de 3/8. Previne a condensação e a perda de energia na linha de gás de unidades menores.', 'Isolamento', 7.00, 'metro', 5, 8, 1, '2025-11-17 20:35:33'),
(9, 'Isolamento Térmico para 1/2', 'Isolamento térmico para tubo de 1/2. Fornece proteção contra corrosão e controle térmico para linhas de maior diâmetro.', 'Isolamento', 9.00, 'metro', 1, 6, 1, '2025-11-17 20:35:33'),
(10, 'Isolamento Térmico para 5/8', 'Isolamento térmico para tubo de 5/8. Essencial para a linha de gás de aparelhos de 24.000 BTUs.', 'Isolamento', 12.00, 'metro', 2, 5, 1, '2025-11-17 20:35:33'),
(11, 'Isolamento Térmico para 3/4', 'Isolamento térmico para tubo de 3/4. Usado em sistemas de alta capacidade (30.000+ BTUs) para garantir o isolamento adequado.', 'Isolamento', 15.00, 'metro', 0, 4, 1, '2025-11-17 20:35:33'),
(12, 'Fita PVC Autoadesiva', 'Fita plástica para vedação e proteção do isolamento térmico após a instalação. Impermeabiliza o conjunto, impedindo a entrada de umidade e sujeira que podem comprometer o isolamento.', 'Isolamento', 8.00, 'unidade', 0, 5, 1, '2025-11-17 20:35:33'),
(13, 'Suporte Condensadora 18-24 mil btus', 'Suporte em aço reforçado para unidades condensadoras de 18.000 a 24.000 BTUs. Projetado para suportar o peso e a vibração do equipamento, garantindo fixação segura na parede ou em lajes.', 'Suporte', 80.00, 'unidade', 0, 2, 1, '2025-11-17 20:35:33'),
(14, 'Suporte Condensadora 30 mil btus', 'Suporte extra-reforçado para unidades condensadoras de 30.000 BTUs ou mais. Possui estrutura mais robusta para suportar o peso maior desses equipamentos.', 'Suporte', 120.00, 'unidade', 0, 1, 1, '2025-11-17 20:35:33'),
(17, 'Cabo PP 2x2,5mm', 'Cabo de energia bipolar 2x2,5mm para alimentação elétrica da unidade condensadora. Atende às exigências de segurança e capacidade de corrente para aparelhos de ar condicionado.', 'Elétrica', 10.00, 'metro', 0, 15, 1, '2025-11-17 20:35:33'),
(18, 'Disjuntor Bipolar 25A', 'Disjuntor bipolar para proteção do circuito elétrico dedicado ao ar condicionado. Interrompe o fluxo de energia em caso de sobrecarga ou curto-circuito, protegendo a fiação e o equipamento.', 'Disjuntores', 60.00, 'unidade', 0, 1, 1, '2025-11-17 20:35:33'),
(19, 'Eletroduto Corrugado 20mm', 'Eletroduto flexível para a passagem e proteção dos cabos elétricos e de comunicação contra umidade, danos físicos e interferências.', 'Elétrica', 4.00, 'metro', 0, 10, 0, '2025-11-17 20:35:33'),
(20, 'Parafusos de Fixação', 'Kit com parafusos e buchas de alta resistência para fixação do suporte da condensadora em paredes de alvenaria ou concreto.', 'Fixação', 15.00, 'kit', 0, 3, 1, '2025-11-17 20:35:33'),
(21, 'Fita de Liga', 'Fita de liga ou fita de união para conectar e vedar a tubulação de cobre. Usada com solda específica para garantir uma conexão hermética e livre de vazamentos.', 'Tubulação', 25.00, 'rolo', 0, 2, 0, '2025-11-17 20:35:33'),
(22, 'Solda para Cobre', 'Solda especial (prata ou fósforo) para união de tubos de cobre na instalação do sistema de refrigeração, garantindo uma vedação perfeita e durável.', 'Tubulação', 40.00, 'unidade', 0, 1, 1, '2025-11-17 20:35:33'),
(23, 'Flux para Solda', 'Pasta fluxante utilizada para facilitar o processo de soldagem do cobre, assegurando uma solda limpa e de alta qualidade.', 'Tubulação', 20.00, 'pote', 0, 1, 0, '2025-11-17 20:35:33'),
(24, 'Gás Refrigerante R-22', 'Carga de gás refrigerante R-22 para sistemas mais antigos. Usado para completar ou recarregar o ciclo de refrigeração.', '', 170.00, 'unidade', 7, 0, 1, '2025-11-17 20:35:33'),
(25, 'Gás Refrigerante R-410A', 'Carga de gás refrigerante R-410A, utilizado na maioria dos aparelhos modernos. Possui maior pressão de trabalho e é mais ecológico.', '', 200.00, 'unidade', 11, 0, 1, '2025-11-17 20:35:33'),
(26, 'Caixa de Passagem(Caixa de espera)', 'Caixa plástica embutida na parede para abrigar e organizar as conexões elétricas e da tubulação, proporcionando segurança e um acabamento mais limpo.', 'Tubulação', 25.00, 'unidade', 0, 3, 1, '2025-11-17 20:35:33'),
(27, 'Tubo de 1/4', 'Conduz o gás refrigerante entre as unidades. Cobre de alta qualidade evita vazamentos e perda de eficiência.\r\n\r\nEsta tubulação é utilizada para as unidades de 9 a 18 mil BTUs.\r\n\r\nÉ considerado como Linha Fina (ou Tubo de Líquido): Transporta o fluido refrigerante em estado líquido, sob alta pressão e temperatura ambiente ou ligeiramente acima, da unidade externa (condensadora) para a unidade interna (evaporadora).', 'Tubulação', 20.00, 'metro', 18, 4, 1, '2025-11-17 20:35:47'),
(28, 'Fita pvc branca 10 metros', '', 'Isolamento', 7.00, 'unidade', 10, 10, 1, '2025-11-20 11:10:15'),
(29, 'Disjuntor Bipolar 16A', 'Disjuntor bipolar para proteção do circuito elétrico dedicado ao ar condicionado. Interrompe o fluxo de energia em caso de sobrecarga ou curto-circuito, protegendo a fiação e o equipamento.', 'Disjuntores', 30.00, 'unidade', 0, 3, 1, '2025-11-20 13:20:26'),
(30, 'Disjuntor Bipolar 20A', 'Disjuntor bipolar para proteção do circuito elétrico dedicado ao ar condicionado. Interrompe o fluxo de energia em caso de sobrecarga ou curto-circuito, protegendo a fiação e o equipamento.', 'Disjuntores', 45.00, 'unidade', 0, 3, 1, '2025-11-20 13:21:28'),
(32, 'Valor por km rodado', '', 'Outros', 2.00, 'metro', 0, 0, 1, '2025-12-29 11:54:02'),
(33, 'Bomba de água froid', 'Bomba de dreno', 'Outros', 335.00, 'unidade', 0, 0, 1, '2026-01-02 13:09:54'),
(34, 'Cabo PP 4x1,5', '', 'Fiação', 9.20, 'metro', 4, 5, 1, '2026-01-07 19:14:17'),
(35, 'Porca 5/8', '', 'Tubulação', 30.00, 'unidade', 2, 0, 1, '2026-01-12 14:34:35'),
(36, 'Serviços de solda', '', 'Tubulação', 60.00, 'unidade', 0, 0, 1, '2026-01-12 16:06:03'),
(37, 'Troca de capacitor', '', 'Acessórios', 150.00, 'unidade', 0, 0, 1, '2026-01-13 16:09:24'),
(38, 'Suporte universal evaporadora', '', 'Acessórios', 60.00, 'unidade', 0, 0, 1, '2026-01-13 20:11:06'),
(39, 'Limpeza no local piso-teto', '', 'Outros', 300.00, 'unidade', 0, 0, 1, '2026-01-14 12:36:00'),
(40, 'Limpeza condensadora no local', '', 'Outros', 100.00, 'unidade', 0, 0, 1, '2026-01-14 12:36:44'),
(41, 'Limpeza evaporadora split', '', 'Outros', 170.00, 'unidade', 0, 0, 1, '2026-01-14 12:38:03'),
(42, 'Relé condensadora springer', '', 'Disjuntores', 88.00, 'unidade', 2, 0, 1, '2026-01-15 12:29:21'),
(43, 'Contrastepara vazamentos bisnaga', '', 'Ferramentas', 10.00, 'unidade', 5, 0, 1, '2026-01-15 12:30:39'),
(44, 'Relé de placa', '', 'Disjuntores', 35.00, 'unidade', 2, 0, 1, '2026-01-15 12:31:51'),
(45, 'Controle ar condicionado elgin', '', 'Outros', 85.00, 'unidade', 0, 0, 1, '2026-01-15 12:32:28'),
(46, 'Tapa fugas k11', '', 'Acessórios', 100.00, 'unidade', 0, 0, 1, '2026-01-15 12:33:34'),
(47, 'Limpeza ar condicionado k7', '', 'Outros', 300.00, 'unidade', 0, 0, 1, '2026-01-15 12:35:07'),
(48, 'Infraestrutura', '', 'Outros', 120.00, 'unidade', 0, 0, 1, '2026-01-29 19:36:32'),
(49, 'Cabo PP 5x2,5mm', '', 'Fiação', 15.00, 'metro', 0, 0, 1, '2026-01-29 19:47:43'),
(50, 'Recarga de gás R32', 'Carga de gás refrigerante R-32 para sistemas mais antigos. Usado para completar ou recarregar o ciclo de refrigeração.', '', 200.00, 'unidade', 0, 0, 1, '2026-02-12 15:35:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

CREATE TABLE `orcamentos` (
  `id` int NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `servico_id` int DEFAULT NULL,
  `equipamento_marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `equipamento_btus` int DEFAULT NULL,
  `equipamento_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `status` enum('pendente','gerado','enviado','aprovado','recusado','concluido') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pendente',
  `observacoes_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_solicitacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_servico` enum('instalacao','manutencao','limpeza','reparo','remocao','multiplos') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'multiplos',
  `valor_mao_obra` decimal(10,2) DEFAULT '0.00',
  `valor_materiais` decimal(10,2) DEFAULT '0.00',
  `valor_servicos_adicionais` decimal(10,2) DEFAULT '0.00',
  `duracao_total_estimada_min` int DEFAULT NULL,
  `data_inicio_estimada` datetime DEFAULT NULL,
  `data_fim_estimada` datetime DEFAULT NULL,
  `servicos_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orcamentos`
--

INSERT INTO `orcamentos` (`id`, `cliente_id`, `servico_id`, `equipamento_marca`, `equipamento_btus`, `equipamento_tipo`, `descricao`, `valor_total`, `status`, `observacoes_admin`, `data_solicitacao`, `tipo_servico`, `valor_mao_obra`, `valor_materiais`, `valor_servicos_adicionais`, `duracao_total_estimada_min`, `data_inicio_estimada`, `data_fim_estimada`, `servicos_ids`) VALUES
(120, 74, NULL, NULL, NULL, NULL, 'Equipamento com mal cheiro, primeiro será a inspeção do equipamento para entender de onde vem o mal cheiro.', 50.00, 'concluido', '', '2025-12-18 10:33:23', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(121, 75, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 20/12/2025\r\nHorário: 09:00\r\nCliente: Daniel da Riopetrana\r\nWhatsApp: (17) 9 8138-9114\r\n\r\n=== AJUSTES APLICADOS ===\r\n• Fim de semana/Feriado: +10% (R$ +40,00)\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +40,00\r\nValor final: R$ 440,00', 440.00, 'concluido', '', '2025-12-18 10:39:24', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(122, 76, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x2) - R$ 400,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 19/12/2025\r\nHorário: 14:00\r\nCliente: Gustavo Rio petrana\r\nWhatsApp: (17) 9 9625-4293\r\n\r\n=== AJUSTES APLICADOS ===\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +0,00\r\nValor final: R$ 400,00', 400.00, 'concluido', '', '2025-12-18 10:43:01', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(123, 77, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 24000 BTUs\n\n=== AGENDAMENTO ===\nData: 21/12/2025\nHorário: 08:00\nCliente: N&M Refrigeração\nWhatsApp: (17) 9 9624-0727\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +55,00)\n\n=== VALORES ===\nTotal ajustes: R$ +55,00\nValor final: R$ 605,00\n', 605.00, 'pendente', NULL, '2025-12-18 14:15:53', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(124, 78, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 7000 BTUs\n\n=== AGENDAMENTO ===\nData: 27/12/2025\nHorário: 08:00\nCliente: Mercado jj\nWhatsApp: (34) 9 9916-2350\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +10,00)\n\n=== VALORES ===\nTotal ajustes: R$ +10,00\nValor final: R$ 110,00\n', 110.00, 'pendente', NULL, '2025-12-18 15:23:49', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(125, 72, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 7000 BTUs\n\n=== AGENDAMENTO ===\nData: 28/12/2025\nHorário: 08:00\nCliente: Mercado jj\nWhatsApp: (34) 9 9916-2340\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +10,00)\n\n=== VALORES ===\nTotal ajustes: R$ +10,00\nValor final: R$ 110,00\n', 110.00, 'pendente', NULL, '2025-12-18 15:25:10', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(127, 80, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\r\n• Instalação de ar condicionado (x2) - R$ 700,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 9000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 23/12/2025\r\nHorário: 12:00\r\nCliente: William Antônio de oliveira\r\nWhatsApp: (17) 9 9705-1616\r\n\r\n=== AJUSTES APLICADOS ===\r\n\r\n=== VALORES ===\r\nTotal ajustes: R$ +0,00\r\nValor final: R$ 700,00', 700.00, 'concluido', '', '2025-12-22 11:45:23', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(131, 77, NULL, NULL, NULL, NULL, 'Um novo de 32 mil\r\nUm novo de 12 mil\r\n\r\nRetirar 2 \r\nReinstalação 2\r\nLimpar 2', 350.00, 'gerado', '', '2025-12-22 23:45:01', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(132, 84, NULL, NULL, NULL, NULL, '', 1000.00, 'enviado', 'Desconto aplicado: R$ 690,00', '2025-12-29 11:51:43', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(133, 84, NULL, NULL, NULL, NULL, '', 1700.00, 'concluido', 'Acréscimo aplicado: R$ 150,00\r\nDesconto aplicado: R$ 400,00\r\nDesconto aplicado: R$ 1.700,00', '2026-01-05 21:16:24', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(134, 85, NULL, NULL, NULL, NULL, '', 11227.00, 'enviado', '', '2026-01-06 20:43:34', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(135, 86, NULL, NULL, NULL, NULL, '', 2753.20, 'concluido', '', '2026-01-07 19:07:48', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(136, 87, NULL, NULL, NULL, NULL, '', 1400.00, 'enviado', 'Desconto aplicado: R$ 105,00', '2026-01-08 12:01:22', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(137, 88, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x3) - R$ 600,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n  - Equipamento 2: 12000 BTUs\r\n  - Equipamento 3: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 12/01/2026\r\nHorário: 08:00\r\nCliente: Márcio valsechi\r\nWhatsApp: (17) 9 9739-0085\r\n\r\n\r\n=== VALORES ===\r\nTotal serviços: R$ 600,00\r\nValor final: R$ 600,00', 500.00, 'concluido', 'Desconto aplicado: R$ 100,00', '2026-01-10 16:08:57', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(138, 89, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\r\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\r\n  Detalhes dos equipamentos:\r\n  - Equipamento 1: 12000 BTUs\r\n\r\n=== AGENDAMENTO ===\r\nData: 13/01/2026\r\nHorário: 08:00\r\nCliente: Milena Pires Assunção\r\nWhatsApp: (17) 9 8133-0612\r\n\r\n\r\n=== VALORES ===\r\nTotal serviços: R$ 200,00\r\nValor final: R$ 200,00', 270.00, 'enviado', '', '2026-01-11 16:35:42', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(139, 90, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x1) - R$ 300,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n• Instalação de ar condicionado (x2) - R$ 700,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 30000 BTUs\n\n=== AGENDAMENTO ===\nData: 13/01/2026\nHorário: 13:00\nCliente: Wagner\nWhatsApp: (17) 9 8126-4481\n\n\n=== VALORES ===\nTotal serviços: R$ 1.000,00\nValor final: R$ 1.000,00\n', 1000.00, 'pendente', NULL, '2026-01-11 16:42:06', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(140, 91, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x1) - R$ 200,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 14/01/2026\nHorário: 09:00\nCliente: Nilza pirassollo\nWhatsApp: (17) 9 8808-9090\n\n\n=== VALORES ===\nTotal serviços: R$ 200,00\nValor final: R$ 200,00\n', 200.00, 'pendente', NULL, '2026-01-11 16:46:14', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(141, 92, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Remoção de Equipamento (x1) - R$ 300,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 14/01/2026\nHorário: 11:00\nCliente: José santista\nWhatsApp: (17) 9 8102-0574\n\n\n=== VALORES ===\nTotal serviços: R$ 650,00\nValor final: R$ 650,00\n', 650.00, 'pendente', NULL, '2026-01-11 16:50:50', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(142, 90, NULL, NULL, NULL, NULL, '', 946.80, 'enviado', '', '2026-01-11 17:11:34', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(146, 96, NULL, 'Midea convencional', 30000, 'Split', 'Cliente questiona que após desligar o equipamento, um tempo depois parece ter um motor ligado dentro da evaporadora', NULL, 'pendente', NULL, '2026-01-12 07:50:18', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(147, 96, NULL, 'Midea convencional', 30000, 'Split', 'Cliente questiona que após desligar o equipamento, um tempo depois parece ter um motor ligado dentro da evaporadora\r\n\r\nOBS: equipamento em garantia, identificar o problema e acionar a Climaster.\r\n\r\n\r\nExiste também um cassete para fazer manutenção, esta derramando água dentro, precisa dar uma olhada.', 300.00, 'enviado', 'Desconto aplicado: R$ 100,00', '2026-01-12 07:50:19', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(148, 97, NULL, 'Carrier', 42000, 'Piso teto', 'Instalação', 1370.00, 'gerado', 'Acrescimo aplicado: R$ 500,00 - Motivo: Instalação ar condicionado piso teto', '2026-01-12 14:32:07', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(149, 98, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 9000 BTUs\n• Remoção de Equipamento (x1) - R$ 300,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 9000 BTUs\n\n=== AGENDAMENTO ===\nData: 17/01/2026\nHorário: 10:00\nCliente: Pedrenrique Guimarães\nWhatsApp: (17) 9 8157-7396\n\n\n=== VALORES ===\nTotal serviços: R$ 650,00\nValor final: R$ 650,00\n', 650.00, 'pendente', NULL, '2026-01-12 19:52:41', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(150, 99, NULL, 'Elgin', 12000, 'Split', 'Manutenção', 340.00, 'enviado', '', '2026-01-12 22:33:28', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(151, 100, NULL, 'Elgin', 12000, 'Spli', 'Desligando', 325.00, 'enviado', '', '2026-01-12 22:35:32', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(152, 101, NULL, 'Idie', 9000, 'Sjsj', 'Keieie', 0.00, 'pendente', '', '2026-01-12 22:37:34', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(153, 102, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 21/01/2026\nHorário: 08:00\nCliente: Rrr\nWhatsApp: (33) 3 3333-3333\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 350.00, 'pendente', NULL, '2026-01-12 22:52:36', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(154, 103, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 22/01/2026\nHorário: 08:00\nCliente: Tgrg4\nWhatsApp: (99) 9 9999-9999\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 350.00, 'pendente', NULL, '2026-01-12 22:53:45', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(155, 101, NULL, 'Elgin', 12000, 'Split', 'Iiee', 0.00, 'pendente', '', '2026-01-12 23:11:13', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(156, 101, NULL, 'Lg', 12, 'Split', 'Ksksw', 0.00, 'pendente', '', '2026-01-12 23:12:46', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(157, 101, NULL, 'Elgin', 12000, 'Split', 'Kekew', 0.00, 'pendente', '', '2026-01-12 23:13:48', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(158, 101, NULL, 'Lg', 12000, 'Split', 'Kekwkw', 0.00, 'pendente', '', '2026-01-12 23:14:48', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(159, 101, NULL, 'Lg', 12000, 'Split', 'Isis', 0.00, 'pendente', '', '2026-01-12 23:19:02', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(160, 104, NULL, 'Lg', 12000, 'Split', 'Limpeza 2 ar na casa dele uma manutenção', 0.00, 'pendente', '', '2026-01-13 15:07:05', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(161, 105, NULL, 'Ee', 12000, 'Split', 'Dgfg', 0.00, 'gerado', '', '2026-01-14 02:03:07', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(162, 106, NULL, 'Lg', 12, 'Split', 'Ar Festa 60mil \r\nAr Provador fora 12mil \r\nAr provador dentro 12mil \r\nAr Nadir 18mil \r\nAr DL 24mil \r\nAr sala noiva 19mil \r\nAr recepção noiva 12mil \r\nAr sala vestidos novos 12mil \r\nAr segunda sala noivas 24mil\r\n\r\nFazer limpeza de todos os equipamentos.\r\nAr condicionado de 24 mil btus que está em baixo da escada precisa mudar a evaporadora do lugar.\r\n8 metros de tubulação \r\n12 metros de PP 4x2,5', 2830.00, 'enviado', '', '2026-01-14 12:31:22', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(163, 105, NULL, 'Elgin', 12000, 'Limpeza', 'Limpeza de brinde do bem estar dia 30', 0.00, 'pendente', '', '2026-01-15 11:55:01', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(164, 107, NULL, 'Elgin', 52000, 'K7', 'Limpezas e manutenções em todos os equipamentos', 700.00, 'enviado', 'Desconto aplicado: R$ 70,00', '2026-01-15 12:38:11', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(165, 108, NULL, 'Lg', 12000, 'Split', 'Desinstalação e reinstalação', 3150.00, 'enviado', 'Não vou cobrar as desinstalaçoes dos equipamentos.\r\n\r\nApenas as instalações \r\n\r\nNeste orçamento está sendo colocado \r\n\r\n5 das máquinas já instalados na frente \r\n\r\n2 novas de 32 mil btus \r\n2 novas de 24 mil btus', '2026-01-15 19:55:04', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(166, 109, NULL, NULL, NULL, NULL, '=== SERVIÇOS SOLICITADOS ===\n• Instalação de ar condicionado (x1) - R$ 350,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 28/01/2026\nHorário: 08:00\nCliente: N&M Refrigeração\nWhatsApp: (88) 8 8888-8888\n\n\n=== VALORES ===\nTotal serviços: R$ 350,00\nValor final: R$ 350,00\n', 350.00, 'pendente', NULL, '2026-01-18 17:45:14', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(167, 101, NULL, 'Lg', 12000, 'Split', 'Passar infraestrutura', 0.00, 'gerado', 'Desconto aplicado: R$ 200,00', '2026-01-29 15:20:11', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(168, 110, NULL, 'Midea', 60000, 'Piso teto', 'Recarga de gás r410a piso teto\r\nRecarga de gás r22 split 30 mil btus\r\n2 limpezas split\r\n1 limpeza piso teto', 2030.00, 'enviado', '', '2026-01-30 02:14:23', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(169, 111, NULL, 'Lg', 12000, 'Spli', 'R 32 LG\r\n19000mil btus\r\n750gr\r\nBaixa de gás \r\n110 psi\r\n(2°lado direito ponta)\r\n\r\nLg dual inverter\r\n9000\r\nR 410\r\n520gr\r\n100psi\r\n(1° lado direito ponta)\r\n\r\nElgim\r\n30mil btus\r\nR410\r\n1,440kg\r\n115 psi\r\n\r\nMidea \r\nR410\r\n570gr\r\n12000 mil btus\r\n115psi\r\n\r\nLg dual inverter\r\n12mil btus\r\nR410\r\n520gr\r\n135psi\r\n(Em cima do midea)\r\n\r\nLg dual inverter \r\n19mil btus\r\nR410\r\n65psi\r\n1,165gr\r\n(2° da segunda fileira)\r\n\r\nLg dual inverter \r\n19mil btus\r\nR410\r\n1,150gr\r\n115psi\r\n(1°da segunda fileira)\r\n\r\nLg dual inverter \r\n19mil btus\r\nR410\r\n1,150\r\n120psi\r\n(1° lado esquerdo)\r\n\r\n\r\nTodos os equipamentos trabalham com o gás R410a, apenas um equipamento trabalha com R32.\r\n\r\nAs pressões de acordo com as normas do fabricante são de 135 a 150 psi.\r\n\r\nPrimeira inspeção indico a limpeza de todos os equipamentos evaporadoras (máquinas internas), e também uma reposição de gás em todos os equipamentos.\r\n\r\nTodos os equipamentos com baixa de pressão indico, manutenções preventivas, tanto limpezas e reposição de gás.\r\n\r\nEm alguns equipamentos como da cozinha uma limpeza mensal, os demais equipamentos uma limpeza trimestral.\r\n\r\nA limpeza dos filtros de todos os equipamentos splits devem ser realizadas semanalmente.\r\n\r\nNeste relatório tenho apenas as informações dos ar splits, pois os ar piso-teto eles tem uma potência maior, o indicado para manutenção Preventiva destes equipamentos deve ser semestral.', 625.00, 'enviado', '', '2026-02-09 13:43:31', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL),
(170, 112, NULL, 'Midea', 9000, 'Limpeza', 'Foram dois equipamentos verificados \r\n\r\n1 midea 30 mil btus gás R32\r\nLimpeza da evaporadora e reposição de gás com tapa fugas e contraste.\r\n\r\n1 midea 9 mil btus gás R410a \r\nApenas reposição de gás com tapa fugas e contraste.', 790.00, 'enviado', '', '2026-02-12 15:33:55', 'multiplos', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_materiais`
--

CREATE TABLE `orcamento_materiais` (
  `id` int NOT NULL,
  `orcamento_id` int NOT NULL,
  `material_id` int NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orcamento_materiais`
--

INSERT INTO `orcamento_materiais` (`id`, `orcamento_id`, `material_id`, `quantidade`, `data_criacao`) VALUES
(330, 132, 28, 1.00, '2026-01-02 14:19:38'),
(331, 132, 9, 2.00, '2026-01-02 14:19:38'),
(332, 132, 7, 2.00, '2026-01-02 14:19:38'),
(333, 132, 33, 1.00, '2026-01-02 14:19:38'),
(334, 132, 32, 75.00, '2026-01-02 14:19:38'),
(335, 132, 27, 4.00, '2026-01-02 14:19:38'),
(336, 132, 4, 4.00, '2026-01-02 14:19:38'),
(358, 134, 30, 2.00, '2026-01-06 20:50:13'),
(359, 134, 17, 6.00, '2026-01-06 20:50:13'),
(360, 134, 20, 1.00, '2026-01-06 20:50:13'),
(361, 134, 28, 6.00, '2026-01-06 20:50:13'),
(362, 134, 9, 1.00, '2026-01-06 20:50:13'),
(363, 134, 7, 5.00, '2026-01-06 20:50:13'),
(364, 134, 8, 3.00, '2026-01-06 20:50:13'),
(365, 134, 1, 1.00, '2026-01-06 20:50:13'),
(366, 134, 14, 1.00, '2026-01-06 20:50:13'),
(367, 134, 27, 10.00, '2026-01-06 20:50:13'),
(368, 134, 4, 2.00, '2026-01-06 20:50:13'),
(369, 134, 3, 7.00, '2026-01-06 20:50:13'),
(420, 135, 34, 6.00, '2026-01-09 22:15:25'),
(421, 135, 20, 6.00, '2026-01-09 22:15:25'),
(422, 135, 28, 6.00, '2026-01-09 22:15:25'),
(423, 135, 7, 8.00, '2026-01-09 22:15:25'),
(424, 135, 10, 8.00, '2026-01-09 22:15:25'),
(425, 135, 14, 2.00, '2026-01-09 22:15:25'),
(426, 135, 27, 6.00, '2026-01-09 22:15:25'),
(427, 135, 5, 6.00, '2026-01-09 22:15:25'),
(488, 138, 24, 1.00, '2026-01-13 14:39:25'),
(493, 136, 24, 2.00, '2026-01-13 16:10:50'),
(494, 136, 37, 1.00, '2026-01-13 16:10:50'),
(498, 142, 38, 1.00, '2026-01-13 20:11:54'),
(499, 142, 34, 4.00, '2026-01-13 20:11:54'),
(512, 148, 24, 3.00, '2026-01-14 01:59:55'),
(513, 148, 35, 2.00, '2026-01-14 01:59:55'),
(514, 148, 22, 4.00, '2026-01-14 01:59:55'),
(515, 148, 5, 2.00, '2026-01-14 01:59:55'),
(516, 161, 34, 12.00, '2026-01-14 02:04:50'),
(517, 161, 27, 11.00, '2026-01-14 02:04:50'),
(518, 161, 4, 11.00, '2026-01-14 02:04:50'),
(548, 162, 40, 10.00, '2026-01-14 12:49:47'),
(549, 162, 41, 9.00, '2026-01-14 12:49:47'),
(550, 162, 39, 1.00, '2026-01-14 12:49:47'),
(555, 164, 24, 1.00, '2026-01-15 19:23:51'),
(558, 147, 25, 1.00, '2026-01-16 18:25:26'),
(559, 147, 46, 1.00, '2026-01-16 18:25:26'),
(561, 150, 41, 2.00, '2026-01-18 13:28:54'),
(565, 151, 25, 1.00, '2026-01-19 19:25:45'),
(566, 151, 37, 1.00, '2026-01-19 19:25:45'),
(567, 151, 36, 1.00, '2026-01-19 19:25:45'),
(573, 167, 49, 60.00, '2026-01-29 19:54:47'),
(574, 167, 9, 5.00, '2026-01-29 19:54:47'),
(575, 167, 7, 30.00, '2026-01-29 19:54:47'),
(576, 167, 8, 12.00, '2026-01-29 19:54:47'),
(577, 167, 10, 10.00, '2026-01-29 19:54:47'),
(578, 167, 27, 60.00, '2026-01-29 19:54:47'),
(579, 167, 4, 10.00, '2026-01-29 19:54:47'),
(580, 167, 3, 20.00, '2026-01-29 19:54:47'),
(581, 167, 5, 20.00, '2026-01-29 19:54:47'),
(626, 168, 24, 2.00, '2026-01-31 20:07:35'),
(627, 168, 25, 3.00, '2026-01-31 20:07:35'),
(628, 168, 46, 3.00, '2026-01-31 20:07:35'),
(629, 168, 37, 1.00, '2026-01-31 20:07:35'),
(630, 168, 41, 2.00, '2026-01-31 20:07:35'),
(631, 168, 39, 1.00, '2026-01-31 20:07:35'),
(650, 169, 25, 1.00, '2026-02-09 13:57:00'),
(651, 169, 45, 1.00, '2026-02-09 13:57:00'),
(652, 169, 41, 2.00, '2026-02-09 13:57:00'),
(667, 170, 25, 1.00, '2026-02-12 15:43:42'),
(668, 170, 50, 1.00, '2026-02-12 15:43:42'),
(669, 170, 46, 2.00, '2026-02-12 15:43:42'),
(670, 170, 43, 2.00, '2026-02-12 15:43:42'),
(671, 170, 41, 1.00, '2026-02-12 15:43:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_produtos`
--

CREATE TABLE `orcamento_produtos` (
  `id` int NOT NULL,
  `orcamento_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` int NOT NULL DEFAULT '1',
  `desconto` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `orcamento_produtos`
--

INSERT INTO `orcamento_produtos` (`id`, `orcamento_id`, `produto_id`, `quantidade`, `desconto`) VALUES
(28, 131, 11, 1, 0.00),
(31, 134, 11, 1, 0.00),
(32, 134, 14, 1, 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_servicos`
--

CREATE TABLE `orcamento_servicos` (
  `id` int NOT NULL,
  `orcamento_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `quantidade` decimal(10,2) DEFAULT '1.00',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orcamento_servicos`
--

INSERT INTO `orcamento_servicos` (`id`, `orcamento_id`, `servico_id`, `quantidade`, `data_criacao`) VALUES
(171, 120, 8, 1.00, '2025-12-19 10:01:55'),
(190, 132, 5, 1.00, '2026-01-02 14:19:38'),
(191, 132, 7, 1.00, '2026-01-02 14:19:38'),
(192, 132, 9, 1.00, '2026-01-02 14:19:38'),
(206, 134, 5, 4.00, '2026-01-06 20:50:13'),
(207, 134, 7, 2.00, '2026-01-06 20:50:13'),
(208, 134, 9, 2.00, '2026-01-06 20:50:13'),
(227, 135, 5, 3.00, '2026-01-09 22:15:25'),
(228, 135, 9, 2.00, '2026-01-09 22:15:25'),
(241, 137, 7, 3.00, '2026-01-11 20:07:23'),
(244, 133, 5, 2.00, '2026-01-11 20:08:41'),
(245, 133, 7, 2.00, '2026-01-11 20:08:41'),
(246, 133, 9, 2.00, '2026-01-11 20:08:41'),
(252, 138, 8, 1.00, '2026-01-13 14:39:25'),
(257, 136, 7, 5.00, '2026-01-13 16:10:50'),
(258, 136, 8, 1.00, '2026-01-13 16:10:50'),
(265, 142, 5, 1.00, '2026-01-13 20:11:54'),
(266, 142, 7, 1.00, '2026-01-13 20:11:54'),
(267, 142, 9, 1.00, '2026-01-13 20:11:54'),
(268, 161, 5, 1.00, '2026-01-14 02:04:50'),
(269, 161, 7, 10.00, '2026-01-14 02:04:50'),
(270, 161, 9, 1.00, '2026-01-14 02:04:50'),
(275, 164, 7, 3.00, '2026-01-15 19:23:51'),
(281, 165, 5, 9.00, '2026-01-15 20:05:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_servicos_backup`
--

CREATE TABLE `orcamento_servicos_backup` (
  `id` int NOT NULL,
  `orcamento_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `data_agendamento` date DEFAULT NULL,
  `hora_agendamento` time DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `quantidade` decimal(10,2) DEFAULT '1.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `orcamento_servicos_backup`
--

INSERT INTO `orcamento_servicos_backup` (`id`, `orcamento_id`, `servico_id`, `data_agendamento`, `hora_agendamento`, `data_criacao`, `quantidade`) VALUES
(5, 10, 7, NULL, NULL, '2025-11-20 19:31:16', 4.00),
(6, 10, 9, NULL, NULL, '2025-11-20 19:31:16', 1.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_servicos_multiplos`
--

CREATE TABLE `orcamento_servicos_multiplos` (
  `id` int NOT NULL,
  `orcamento_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `quantidade` int DEFAULT '1',
  `preco_unitario` decimal(10,2) DEFAULT NULL,
  `observacoes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `preco` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `btus` int DEFAULT NULL,
  `estoque` int DEFAULT '0',
  `imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `categoria`, `marca`, `btus`, `estoque`, `imagem`, `ativo`, `data_criacao`) VALUES
(9, 'SPLIT HW 12.000 BTUS GREE 220V FRIO G TOP AUTO GAS R32', 'valor a vista: R$  2.100,00 \r\nvalor a prazo: R$  2.230,00', 2230.00, 'Split', 'gree', 12000, 0, NULL, 1, '2025-12-10 01:52:18'),
(10, 'SPLIT HW 18.000 BTUS GREE 220V FRIO G TOP AUTO GAS R32', 'valor a vista: R$  3.330,00\r\nValor a prazo: R$  3.530,00', 3530.00, 'Split', 'gree', 18000, 0, NULL, 1, '2025-12-10 01:53:31'),
(11, 'Ar condicionado tcl 32 mil btus inverter', 'Parcelado em 10 vezes sem juros', 5400.00, 'Split', 'TCL', 30000, 0, NULL, 1, '2026-01-05 21:04:28'),
(12, 'Ar condicionado tcl 24 mil btus inverter', 'Em 10 vezes sem juros', 4100.00, 'Split', 'TCL', 24000, 0, NULL, 1, '2026-01-05 21:05:42'),
(13, 'Multi-split midea 42 mil btus condensadora', 'Com dois splits \r\n1 de 24 mil btus \r\n1 de 18 mil btus', 15140.00, 'Split', 'Midea', NULL, 0, NULL, 1, '2026-01-05 21:08:31'),
(14, 'TCL Inverter 12 mil btus', '', 2450.00, 'Split', 'TCL', 12000, 0, NULL, 1, '2026-01-06 20:37:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `preco_base` decimal(10,2) DEFAULT NULL,
  `categoria` enum('instalacao','manutencao','limpeza','reparo','remocao') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duracao_media_min` int DEFAULT '120',
  `duracao_media_max` int DEFAULT '240',
  `tempo_setup_min` int DEFAULT '30',
  `complexidade` enum('baixa','media','alta') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'media',
  `duracao_horas` decimal(4,2) DEFAULT '1.00',
  `tipo_equipamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao_tecnica` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `duracao_padrao_min` int DEFAULT '120',
  `duracao_min_min` int DEFAULT '60',
  `duracao_max_min` int DEFAULT '240',
  `intervalo_entre_servicos_min` int DEFAULT '30',
  `max_por_dia` int DEFAULT '3',
  `precisa_materiais` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`, `descricao`, `preco_base`, `categoria`, `ativo`, `data_criacao`, `duracao_media_min`, `duracao_media_max`, `tempo_setup_min`, `complexidade`, `duracao_horas`, `tipo_equipamento`, `descricao_tecnica`, `duracao_padrao_min`, `duracao_min_min`, `duracao_max_min`, `intervalo_entre_servicos_min`, `max_por_dia`, `precisa_materiais`) VALUES
(1, 'Instalação Completa 9000 BTUs', 'Instalação profissional com todos os materiais inclusos para ar condicionado de 9000 BTUs\r\n\r\nsuporte condensadora\r\ntubos de cobre\r\nesponjosos\r\nfita pvc\r\nfita prateada\r\nbuchas e parafusos \r\ncabo pp de 5 vias 2,5mm\r\ncabo pp de 2 vias (4 mm para ar condicionado até 18 mil btus) acima deve ser de 6mm\r\nmangueira cristal para dreno se nescessario\r\ndisjuntor de 16 a 20 a', 650.00, 'instalacao', 0, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(2, 'Instalação Com material 12000 BTUs', 'Instalação profissional com todos os materiais inclusos para ar condicionado de 12000 BTUs\r\n\r\nsuporte condensadora\r\ntubos de cobre\r\nesponjosos\r\nfita pvc\r\nfita prateada\r\nbuchas e parafusos \r\ncabo pp de 5 vias 2,5mm\r\ncabo pp de 2 vias (4 mm para ar condicionado até 18 mil btus) acima deve ser de 6mm\r\nmangueira cristal para dreno se nescessario\r\ndisjuntor de 16 a 20 a', 700.00, 'instalacao', 0, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(3, 'Instalação Completa 18000 BTUs', 'Instalação profissional com todos os materiais inclusos para ar condicionado de 18000 BTUs\r\n\r\nsuporte condensadora\r\ntubos de cobre\r\nesponjosos\r\nfita pvc\r\nfita prateada\r\nbuchas e parafusos \r\ncabo pp de 5 vias 2,5mm\r\ncabo pp de 2 vias (4 mm para ar condicionado até 18 mil btus) acima deve ser de 6mm\r\nmangueira cristal para dreno se nescessario\r\ndisjuntor de 16 a 20 a', 900.00, 'instalacao', 0, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(4, 'Instalação Completa 24 a 32 mil BTUs', 'Instalação profissional com todos os materiais inclusos para ar condicionado de 24000 BTUs a 30 mil BTUs \r\n\r\nsuporte condensadora\r\ntubos de cobre\r\nesponjosos\r\nfita pvc\r\nfita prateada\r\nbuchas e parafusos \r\ncabo pp de 5 vias 2,5mm\r\ncabo pp de 2 vias (4 mm para ar condicionado até 18 mil btus) acima deve ser de 6mm\r\nmangueira cristal para dreno se nescessario\r\ndisjuntor de 16 a 20 a', 500.00, 'instalacao', 0, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(5, 'Instalação de ar condicionado', 'Instalação profissional sem incluir materiais - cliente fornece todos os materiais\r\n\r\nsuporte condensadora\r\ntubos de cobre\r\nesponjosos\r\nfita pvc\r\nfita prateada\r\nbuchas e parafusos \r\ncabo pp de 5 vias 2,5mm\r\ncabo pp de 2 vias (4 mm para ar condicionado até 18 mil btus) acima deve ser de 6mm\r\nmangueira cristal para dreno se nescessario\r\ndisjuntor de 16 a 20 a', 350.00, 'instalacao', 1, '2025-11-14 19:07:53', 240, 480, 30, 'alta', 4.00, '', NULL, 240, 120, 480, 60, 2, 1),
(6, 'Limpeza com remoção do equipamento', 'Limpeza completa, verificação de componentes e calibração do sistema', 550.00, 'manutencao', 1, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(7, 'Limpeza Completa (no local com bolsa coletora)', 'Limpeza interna e externa completa com higienização', 200.00, 'limpeza', 1, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(8, 'Manutenção corretiva, Diagnóstico e Reparo', 'Diagnóstico completo e reparo do equipamento com garantia\r\n\r\nPeças de reposição será cobrado separadamente.', 100.00, 'reparo', 1, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1),
(9, 'Remoção de Equipamento', 'Remoção segura do equipamento com preservação do gás refrigerante', 300.00, 'remocao', 1, '2025-11-14 19:07:53', 120, 240, 30, 'media', 1.00, NULL, NULL, 120, 60, 240, 30, 3, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico_materiais`
--

CREATE TABLE `servico_materiais` (
  `id` int NOT NULL,
  `servico_id` int NOT NULL,
  `material_id` int NOT NULL,
  `quantidade_minima` decimal(10,2) DEFAULT '1.00',
  `quantidade_padrao` decimal(10,2) DEFAULT '1.00',
  `obrigatorio` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `servico_materiais`
--

INSERT INTO `servico_materiais` (`id`, `servico_id`, `material_id`, `quantidade_minima`, `quantidade_padrao`, `obrigatorio`) VALUES
(1, 1, 1, 1.00, 1.00, 1),
(2, 1, 2, 3.00, 4.00, 1),
(3, 1, 3, 3.00, 4.00, 1),
(4, 1, 7, 3.00, 4.00, 1),
(5, 1, 8, 3.00, 4.00, 1),
(6, 1, 12, 1.00, 1.00, 1),
(7, 1, 17, 5.00, 7.00, 1),
(8, 1, 18, 1.00, 1.00, 1),
(9, 1, 19, 5.00, 7.00, 1),
(10, 1, 20, 1.00, 1.00, 1),
(11, 1, 21, 1.00, 1.00, 1),
(12, 1, 22, 1.00, 1.00, 1),
(13, 1, 23, 1.00, 1.00, 1),
(14, 2, 1, 1.00, 1.00, 1),
(15, 2, 2, 3.00, 4.00, 1),
(16, 2, 3, 3.00, 4.00, 1),
(17, 2, 7, 3.00, 4.00, 1),
(18, 2, 8, 3.00, 4.00, 1),
(19, 2, 12, 1.00, 1.00, 1),
(20, 2, 17, 5.00, 7.00, 1),
(21, 2, 18, 1.00, 1.00, 1),
(22, 2, 19, 5.00, 7.00, 1),
(23, 2, 20, 1.00, 1.00, 1),
(24, 2, 21, 1.00, 1.00, 1),
(25, 2, 22, 1.00, 1.00, 1),
(26, 2, 23, 1.00, 1.00, 1),
(27, 3, 13, 1.00, 1.00, 1),
(28, 3, 2, 3.00, 4.00, 1),
(29, 3, 4, 3.00, 4.00, 1),
(30, 3, 7, 3.00, 4.00, 1),
(31, 3, 9, 3.00, 4.00, 1),
(32, 3, 12, 1.00, 1.00, 1),
(33, 3, 17, 5.00, 7.00, 1),
(34, 3, 18, 1.00, 1.00, 1),
(35, 3, 19, 5.00, 7.00, 1),
(36, 3, 20, 1.00, 1.00, 1),
(37, 3, 21, 1.00, 1.00, 1),
(38, 3, 22, 1.00, 1.00, 1),
(39, 3, 23, 1.00, 1.00, 1),
(40, 4, 13, 1.00, 1.00, 1),
(41, 4, 2, 3.00, 4.00, 1),
(42, 4, 5, 3.00, 4.00, 1),
(43, 4, 7, 3.00, 4.00, 1),
(44, 4, 10, 3.00, 4.00, 1),
(45, 4, 12, 1.00, 1.00, 1),
(46, 4, 17, 5.00, 7.00, 1),
(47, 4, 18, 1.00, 1.00, 1),
(48, 4, 19, 5.00, 7.00, 1),
(49, 4, 20, 1.00, 1.00, 1),
(50, 4, 21, 1.00, 1.00, 1),
(51, 4, 22, 1.00, 1.00, 1),
(52, 4, 23, 1.00, 1.00, 1),
(53, 1, 1, 1.00, 1.00, 1),
(54, 1, 2, 3.00, 4.00, 1),
(55, 1, 3, 3.00, 4.00, 1),
(56, 1, 7, 3.00, 4.00, 1),
(57, 1, 8, 3.00, 4.00, 1),
(58, 1, 12, 1.00, 1.00, 1),
(59, 1, 17, 5.00, 7.00, 1),
(60, 1, 18, 1.00, 1.00, 1),
(61, 1, 19, 5.00, 7.00, 1),
(62, 1, 20, 1.00, 1.00, 1),
(63, 1, 21, 1.00, 1.00, 1),
(64, 1, 22, 1.00, 1.00, 1),
(65, 1, 23, 1.00, 1.00, 1),
(66, 2, 1, 1.00, 1.00, 1),
(67, 2, 2, 3.00, 4.00, 1),
(68, 2, 3, 3.00, 4.00, 1),
(69, 2, 7, 3.00, 4.00, 1),
(70, 2, 8, 3.00, 4.00, 1),
(71, 2, 12, 1.00, 1.00, 1),
(72, 2, 17, 5.00, 7.00, 1),
(73, 2, 18, 1.00, 1.00, 1),
(74, 2, 19, 5.00, 7.00, 1),
(75, 2, 20, 1.00, 1.00, 1),
(76, 2, 21, 1.00, 1.00, 1),
(77, 2, 22, 1.00, 1.00, 1),
(78, 2, 23, 1.00, 1.00, 1),
(79, 3, 13, 1.00, 1.00, 1),
(80, 3, 2, 3.00, 4.00, 1),
(81, 3, 4, 3.00, 4.00, 1),
(82, 3, 7, 3.00, 4.00, 1),
(83, 3, 9, 3.00, 4.00, 1),
(84, 3, 12, 1.00, 1.00, 1),
(85, 3, 17, 5.00, 7.00, 1),
(86, 3, 18, 1.00, 1.00, 1),
(87, 3, 19, 5.00, 7.00, 1),
(88, 3, 20, 1.00, 1.00, 1),
(89, 3, 21, 1.00, 1.00, 1),
(90, 3, 22, 1.00, 1.00, 1),
(91, 3, 23, 1.00, 1.00, 1),
(92, 4, 13, 1.00, 1.00, 1),
(93, 4, 2, 3.00, 4.00, 1),
(94, 4, 5, 3.00, 4.00, 1),
(95, 4, 7, 3.00, 4.00, 1),
(96, 4, 10, 3.00, 4.00, 1),
(97, 4, 12, 1.00, 1.00, 1),
(98, 4, 17, 5.00, 7.00, 1),
(99, 4, 18, 1.00, 1.00, 1),
(100, 4, 19, 5.00, 7.00, 1),
(101, 4, 20, 1.00, 1.00, 1),
(102, 4, 21, 1.00, 1.00, 1),
(103, 4, 22, 1.00, 1.00, 1),
(104, 4, 23, 1.00, 1.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_equipamento`
--

CREATE TABLE `tipos_equipamento` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text,
  `ativo` tinyint(1) DEFAULT '1',
  `ordem` int DEFAULT '0',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `tipos_equipamento`
--

INSERT INTO `tipos_equipamento` (`id`, `nome`, `descricao`, `ativo`, `ordem`, `data_criacao`, `categoria`) VALUES
(1, 'Split (parede)', 'Ar condicionado split instalado na parede', 1, 1, '2025-12-11 01:40:48', NULL),
(2, 'Split piso-teto', 'Ar condicionado split instalado no piso ou teto', 1, 2, '2025-12-11 01:40:48', NULL),
(3, 'Split cassete', 'Ar condicionado split tipo cassete embutido no forro', 1, 3, '2025-12-11 01:40:48', NULL),
(4, 'Portátil', 'Ar condicionado portátil móvel', 1, 4, '2025-12-11 01:40:48', NULL),
(5, 'Janela', 'Ar condicionado de janela (tradicional)', 1, 5, '2025-12-11 01:40:48', NULL),
(6, 'Outro', 'Outro tipo de equipamento não listado', 1, 99, '2025-12-11 01:40:48', NULL),
(7, 'Split dutado', NULL, 1, 4, '2025-12-11 01:47:50', 'split'),
(8, 'Multisplit', NULL, 0, 7, '2025-12-11 01:47:50', 'split'),
(9, 'VRF', NULL, 0, 8, '2025-12-11 01:47:50', 'split'),
(10, 'Piso-teto', NULL, 1, 9, '2025-12-11 01:47:50', 'outros'),
(11, 'Cassete 1 via', NULL, 1, 10, '2025-12-11 01:47:50', 'split'),
(12, 'Cassete 4 vias', NULL, 1, 11, '2025-12-11 01:47:50', 'split');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` enum('admin','gerente','tecnico','atendente') DEFAULT 'tecnico',
  `receber_notificacoes` tinyint(1) DEFAULT '1',
  `telefone` varchar(20) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `receber_notificacoes`, `telefone`, `ativo`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'Administrador', 'contato@nmrefrigeracao.com.br', '$2y$10$YourHashedPasswordHere', 'admin', 1, NULL, 1, '2025-12-17 15:14:20', '2025-12-17 15:14:20');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `agendamentos_orcamentos`
--
ALTER TABLE `agendamentos_orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `agendamento_servicos`
--
ALTER TABLE `agendamento_servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_agendamento`
--
ALTER TABLE `config_agendamento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_disponibilidade`
--
ALTER TABLE `config_disponibilidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_sistema`
--
ALTER TABLE `config_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_sistema_ia`
--
ALTER TABLE `config_sistema_ia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `config_site`
--
ALTER TABLE `config_site`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_criacao`),
  ADD KEY `idx_ip` (`ip_address`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `orcamento_materiais`
--
ALTER TABLE `orcamento_materiais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `orcamento_materiais_ibfk_1` (`orcamento_id`);

--
-- Índices de tabela `orcamento_produtos`
--
ALTER TABLE `orcamento_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `orcamento_servicos`
--
ALTER TABLE `orcamento_servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico_id` (`servico_id`),
  ADD KEY `orcamento_servicos_ibfk_1` (`orcamento_id`);

--
-- Índices de tabela `orcamento_servicos_backup`
--
ALTER TABLE `orcamento_servicos_backup`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orcamento_servicos_multiplos`
--
ALTER TABLE `orcamento_servicos_multiplos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `servico_materiais`
--
ALTER TABLE `servico_materiais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico_id` (`servico_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Índices de tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

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
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT de tabela `agendamentos_orcamentos`
--
ALTER TABLE `agendamentos_orcamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `agendamento_servicos`
--
ALTER TABLE `agendamento_servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1547;

--
-- AUTO_INCREMENT de tabela `config_agendamento`
--
ALTER TABLE `config_agendamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `config_disponibilidade`
--
ALTER TABLE `config_disponibilidade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `config_sistema`
--
ALTER TABLE `config_sistema`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `config_sistema_ia`
--
ALTER TABLE `config_sistema_ia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `config_site`
--
ALTER TABLE `config_site`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=856;

--
-- AUTO_INCREMENT de tabela `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5130;

--
-- AUTO_INCREMENT de tabela `materiais`
--
ALTER TABLE `materiais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT de tabela `orcamento_materiais`
--
ALTER TABLE `orcamento_materiais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=672;

--
-- AUTO_INCREMENT de tabela `orcamento_produtos`
--
ALTER TABLE `orcamento_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `orcamento_servicos`
--
ALTER TABLE `orcamento_servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT de tabela `orcamento_servicos_backup`
--
ALTER TABLE `orcamento_servicos_backup`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `orcamento_servicos_multiplos`
--
ALTER TABLE `orcamento_servicos_multiplos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `servico_materiais`
--
ALTER TABLE `servico_materiais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `agendamentos_orcamentos`
--
ALTER TABLE `agendamentos_orcamentos`
  ADD CONSTRAINT `agendamentos_orcamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `agendamento_servicos`
--
ALTER TABLE `agendamento_servicos`
  ADD CONSTRAINT `agendamento_servicos_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos_orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamento_servicos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD CONSTRAINT `orcamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `orcamentos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `orcamento_materiais`
--
ALTER TABLE `orcamento_materiais`
  ADD CONSTRAINT `orcamento_materiais_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamento_materiais_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiais` (`id`);

--
-- Restrições para tabelas `orcamento_produtos`
--
ALTER TABLE `orcamento_produtos`
  ADD CONSTRAINT `orcamento_produtos_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamento_produtos_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamento_servicos`
--
ALTER TABLE `orcamento_servicos`
  ADD CONSTRAINT `orcamento_servicos_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamento_servicos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `orcamento_servicos_multiplos`
--
ALTER TABLE `orcamento_servicos_multiplos`
  ADD CONSTRAINT `orcamento_servicos_multiplos_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamento_servicos_multiplos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `servico_materiais`
--
ALTER TABLE `servico_materiais`
  ADD CONSTRAINT `servico_materiais_ibfk_1` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`),
  ADD CONSTRAINT `servico_materiais_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiais` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
