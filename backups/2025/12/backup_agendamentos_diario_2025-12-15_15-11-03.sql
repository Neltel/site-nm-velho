-- Backup N&M Refrigeração - 2025-12-15_15-11-03
-- Tipo: diario


-- Estrutura da tabela agendamentos
DROP TABLE IF EXISTS `agendamentos`;
CREATE TABLE `agendamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `data_agendamento` date NOT NULL,
  `hora_agendamento` time NOT NULL,
  `endereco` text COLLATE utf8mb4_general_ci,
  `tempo_estimado_minutos` int DEFAULT '60',
  `observacoes` text COLLATE utf8mb4_general_ci,
  `acrescimo_especial` decimal(10,2) DEFAULT '0.00',
  `status` enum('agendado','confirmado','realizado','cancelado','finalizado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'agendado',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `origem` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'site',
  `origem_id` int DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `orcamento_id` int DEFAULT NULL,
  `duracao_estimada` decimal(4,2) DEFAULT '1.50',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `servico_id` (`servico_id`),
  CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados da tabela agendamentos
INSERT INTO `agendamentos` (`id`, `cliente_id`, `servico_id`, `data_agendamento`, `hora_agendamento`, `endereco`, `tempo_estimado_minutos`, `observacoes`, `acrescimo_especial`, `status`, `data_criacao`, `origem`, `origem_id`, `data_fim`, `hora_fim`, `orcamento_id`, `duracao_estimada`) VALUES ('112', '60', '8', '2025-12-13', '17:00:00', 'Rua Itanhaém, 138 - Vila Anchieta, São José do Rio Preto', '60', '=== SERVIÇOS SOLICITADOS ===\n• Manutenção corretiva, Diagnóstico e Reparo (x1) - R$ 100,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n\n=== AGENDAMENTO ===\nData: 13/12/2025\nHorário: 17:00\nCliente: Neltel\nWhatsApp: (34) 9 9916-2340\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +10,00)\n• Horário noturno: +5% (R$ +5,00)\n\n=== VALORES ===\nTotal ajustes: R$ +15,00\nValor final: R$ 115,00\n', '15.00', 'agendado', '2025-12-12 04:59:19', 'sistema_ia', '105', '', '', '', '1.50');
INSERT INTO `agendamentos` (`id`, `cliente_id`, `servico_id`, `data_agendamento`, `hora_agendamento`, `endereco`, `tempo_estimado_minutos`, `observacoes`, `acrescimo_especial`, `status`, `data_criacao`, `origem`, `origem_id`, `data_fim`, `hora_fim`, `orcamento_id`, `duracao_estimada`) VALUES ('113', '61', '7', '2025-12-20', '09:00:00', 'rua Sebastião de Souza Guimarães casa 14, 200 - Cambui, São José do Rio Preto', '60', '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x3) - R$ 600,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 18000 BTUs\n\n=== AGENDAMENTO ===\nData: 20/12/2025\nHorário: 09:00\nCliente: Daniel da riopretania\nWhatsApp: (17) 9 8138-9114\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +60,00)\n\n=== VALORES ===\nTotal ajustes: R$ +60,00\nValor final: R$ 660,00\n', '60.00', 'agendado', '2025-12-12 11:55:39', 'sistema_ia', '106', '', '', '', '1.50');


-- Estrutura da tabela clientes
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rua` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados da tabela clientes
INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `data_cadastro`) VALUES ('19', 'NELTEL SINDEAUX SEVERINO NETO', 'neltelneto1@gmail.com', '(34) 99916-2340', 'R. Itanhaém', '138', 'Vila Anchieta', 'São José do Rio Preto', '2025-11-22 14:07:45');
INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `data_cadastro`) VALUES ('20', 'Leonardo Felix', 'leonardofelix@neltel.com', '(17) 99642-4647', 'Fenhouse', 'Quadra J', 'Lote 3', 'Fenhouse', '2025-11-25 08:51:20');
INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `data_cadastro`) VALUES ('60', 'Neltel', '', '34999162340', 'Rua Itanhaém', '138', 'Vila Anchieta', 'São José do Rio Preto', '2025-12-12 04:59:19');
INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `data_cadastro`) VALUES ('61', 'Daniel da riopretania', '', '17981389114', 'rua Sebastião de Souza Guimarães casa 14', '200', 'Cambui', 'São José do Rio Preto', '2025-12-12 11:55:39');


-- Estrutura da tabela orcamentos
DROP TABLE IF EXISTS `orcamentos`;
CREATE TABLE `orcamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int DEFAULT NULL,
  `servico_id` int DEFAULT NULL,
  `equipamento_marca` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `equipamento_btus` int DEFAULT NULL,
  `equipamento_tipo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `status` enum('pendente','gerado','enviado','aprovado','recusado','concluido') COLLATE utf8mb4_general_ci DEFAULT 'pendente',
  `observacoes_admin` text COLLATE utf8mb4_general_ci,
  `data_solicitacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_servico` enum('instalacao','manutencao','limpeza','reparo','remocao','multiplos') COLLATE utf8mb4_general_ci DEFAULT 'multiplos',
  `valor_mao_obra` decimal(10,2) DEFAULT '0.00',
  `valor_materiais` decimal(10,2) DEFAULT '0.00',
  `valor_servicos_adicionais` decimal(10,2) DEFAULT '0.00',
  `duracao_total_estimada_min` int DEFAULT NULL,
  `data_inicio_estimada` datetime DEFAULT NULL,
  `data_fim_estimada` datetime DEFAULT NULL,
  `servicos_ids` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `servico_id` (`servico_id`),
  CONSTRAINT `orcamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `orcamentos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados da tabela orcamentos
INSERT INTO `orcamentos` (`id`, `cliente_id`, `servico_id`, `equipamento_marca`, `equipamento_btus`, `equipamento_tipo`, `descricao`, `valor_total`, `status`, `observacoes_admin`, `data_solicitacao`, `tipo_servico`, `valor_mao_obra`, `valor_materiais`, `valor_servicos_adicionais`, `duracao_total_estimada_min`, `data_inicio_estimada`, `data_fim_estimada`, `servicos_ids`) VALUES ('34', '19', '', 'Consul', '30000', '', 'ssss', '70.00', 'pendente', '', '2025-11-22 14:07:45', 'multiplos', '0.00', '0.00', '0.00', '', '', '', '');
INSERT INTO `orcamentos` (`id`, `cliente_id`, `servico_id`, `equipamento_marca`, `equipamento_btus`, `equipamento_tipo`, `descricao`, `valor_total`, `status`, `observacoes_admin`, `data_solicitacao`, `tipo_servico`, `valor_mao_obra`, `valor_materiais`, `valor_servicos_adicionais`, `duracao_total_estimada_min`, `data_inicio_estimada`, `data_fim_estimada`, `servicos_ids`) VALUES ('35', '19', '', 'LG', '9000', '', 'Instalação ar condicionado', '1890.00', 'enviado', 'Obs internas', '2025-11-22 20:37:01', 'multiplos', '0.00', '0.00', '0.00', '', '', '', '');
INSERT INTO `orcamentos` (`id`, `cliente_id`, `servico_id`, `equipamento_marca`, `equipamento_btus`, `equipamento_tipo`, `descricao`, `valor_total`, `status`, `observacoes_admin`, `data_solicitacao`, `tipo_servico`, `valor_mao_obra`, `valor_materiais`, `valor_servicos_adicionais`, `duracao_total_estimada_min`, `data_inicio_estimada`, `data_fim_estimada`, `servicos_ids`) VALUES ('36', '20', '', 'TCL', '12000', '', 'Instalação do ar condicionado TCL 12 e 18 mil BTUs', '700.00', 'enviado', '', '2025-11-25 08:51:20', 'multiplos', '0.00', '0.00', '0.00', '', '', '', '');
INSERT INTO `orcamentos` (`id`, `cliente_id`, `servico_id`, `equipamento_marca`, `equipamento_btus`, `equipamento_tipo`, `descricao`, `valor_total`, `status`, `observacoes_admin`, `data_solicitacao`, `tipo_servico`, `valor_mao_obra`, `valor_materiais`, `valor_servicos_adicionais`, `duracao_total_estimada_min`, `data_inicio_estimada`, `data_fim_estimada`, `servicos_ids`) VALUES ('106', '61', '', '', '', '', '=== SERVIÇOS SOLICITADOS ===\n• Limpeza Completa (no local com bolsa coletora) (x3) - R$ 600,00\n  Detalhes dos equipamentos:\n  - Equipamento 1: 12000 BTUs\n  - Equipamento 2: 12000 BTUs\n  - Equipamento 3: 18000 BTUs\n\n=== AGENDAMENTO ===\nData: 20/12/2025\nHorário: 09:00\nCliente: Daniel da riopretania\nWhatsApp: (17) 9 8138-9114\n\n=== AJUSTES APLICADOS ===\n• Fim de semana/Feriado: +10% (R$ +60,00)\n\n=== VALORES ===\nTotal ajustes: R$ +60,00\nValor final: R$ 660,00\n', '1100.00', 'gerado', 'Desconto aplicado: R$ 200,00\r\nDesconto aplicado: R$ 50,00\r\nAcréscimo aplicado: R$ 25,00\r\nDesconto aplicado: R$ 150,00\r\nAcréscimo aplicado: R$ 2,51\nDesconto aplicado: R$ 75,00\nAcréscimo aplicado: R$ 10,00', '2025-12-12 11:55:39', 'multiplos', '0.00', '0.00', '0.00', '', '', '', '');


-- Estrutura da tabela logs_sistema
DROP TABLE IF EXISTS `logs_sistema`;
CREATE TABLE `logs_sistema` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dados` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sessao_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_data` (`data_criacao`),
  KEY `idx_ip` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=1179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados da tabela logs_sistema
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1152', 'sessao', 'Conversa reiniciada pelo usuário', '{\"ip\": \"187.75.21.238\", \"sessao_nova\": \"j4tifmc0qvjd2rk0srgufet651\", \"sessao_antiga\": \"j4tifmc0qvjd2rk0srgufet651\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:08:44');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1153', 'conversa_inicio', 'Nova conversa iniciada', '{\"ip\": \"187.75.21.238\", \"sessao_id\": \"j4tifmc0qvjd2rk0srgufet651\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:08:44');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1154', 'servicos_carregados', 'Serviços carregados do banco', '{\"quantidade\": 5}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:08:44');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1155', 'processamento_tentativa', 'Processando ação: iniciar', '{\"acao\": \"iniciar\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:09:46');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1156', 'conversa_etapa', 'Início do agendamento', '{\"etapa\": \"inicio\", \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:09:46');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1157', 'processamento_tentativa', 'Processando ação: nome', '{\"acao\": \"nome\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"apenas 1 teste\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:09:55');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1158', 'conversa_etapa', 'Nome recebido', '{\"nome\": \"apenas 1 teste\", \"etapa\": \"nome\", \"primeiro_nome\": \"apenas\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:09:55');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1159', 'processamento_tentativa', 'Processando ação: whatsapp', '{\"acao\": \"whatsapp\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"(34) 9 9916-2340\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:03');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1160', 'conversa_etapa', 'Telefone recebido', '{\"etapa\": \"whatsapp\", \"telefone_limpo\": \"34999162340\", \"cliente_existente\": true, \"telefone_formatado\": \"(34) 9 9916-2340\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:03');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1161', 'processamento_tentativa', 'Processando ação: selecionar_servico', '{\"acao\": \"selecionar_servico\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"Instalação de ar condicionado\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:11');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1162', 'servico_selecionado', 'Serviço selecionado', '{\"preco\": \"350.00\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:11');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1163', 'rate_limit_bloqueio', 'Usuário bloqueado - IP: 187.75.21.238', '{\"tipo\": \"nova_conversa\", \"limite\": 5, \"contador\": 6, \"bloqueado_ate\": \"2025-12-15 17:10:19\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:19');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1164', 'processamento_tentativa', 'Processando ação: quantidade_equipamentos', '{\"acao\": \"quantidade_equipamentos\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"1\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:19');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1165', 'quantidade_adicionada', 'Quantidade de equipamentos adicionada', '{\"servico\": \"Instalação de ar condicionado\", \"subtotal\": 350, \"quantidade\": 1, \"total_valor\": 350, \"total_equipamentos\": 1}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:19');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1166', 'rate_limit', 'Tentativa bloqueada - IP: 187.75.21.238', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2025-12-15 17:10:19\", \"tempo_restante\": 7192}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:27');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1167', 'processamento_tentativa', 'Processando ação: mais_servicos', '{\"acao\": \"mais_servicos\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"nao\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:27');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1168', 'rate_limit', 'Tentativa bloqueada - IP: 187.75.21.238', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2025-12-15 17:10:19\", \"tempo_restante\": 7181}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:38');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1169', 'processamento_tentativa', 'Processando ação: btu_equipamento', '{\"acao\": \"btu_equipamento\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"120000\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:38');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1170', 'btu_adicionado', 'BTU adicionado para equipamento', '{\"btu\": \"120000\", \"servico_id\": 5, \"servico_nome\": \"Instalação de ar condicionado\", \"equipamento_num\": 1, \"total_equipamentos_servico\": 1}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:38');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1171', 'rate_limit', 'Tentativa bloqueada - IP: 187.75.21.238', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2025-12-15 17:10:19\", \"tempo_restante\": 7167}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:52');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1172', 'processamento_tentativa', 'Processando ação: endereco', '{\"acao\": \"endereco\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:52');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1173', 'endereco_recebido', 'Endereço completo recebido', '{\"etapa\": \"endereco\", \"endereco\": \"rua, n - bairro, cidade\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:52');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1174', 'datas_geradas', 'Datas disponíveis geradas', '{\"total_disponiveis\": 13, \"total_verificadas\": 14}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:10:52');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1175', 'rate_limit', 'Tentativa bloqueada - IP: 187.75.21.238', '{\"tipo\": \"nova_conversa\", \"contador\": 6, \"bloqueado_ate\": \"2025-12-15 17:10:19\", \"tempo_restante\": 7159}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:11:00');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1176', 'processamento_tentativa', 'Processando ação: selecionar_data', '{\"acao\": \"selecionar_data\", \"etapa\": 1, \"sessao\": \"j4tifmc0qvjd2rk0srgufet651\", \"resposta\": \"2025-12-16\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:11:00');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1177', 'data_selecionada', 'Data selecionada para agendamento', '{\"data\": \"2025-12-16\", \"data_formatada\": \"16/12/2025\"}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:11:00');
INSERT INTO `logs_sistema` (`id`, `tipo`, `mensagem`, `dados`, `ip_address`, `user_agent`, `sessao_id`, `pagina`, `referer`, `data_criacao`) VALUES ('1178', 'horarios_gerados', 'Horários disponíveis gerados', '{\"data\": \"2025-12-16\", \"total_disponiveis\": 12, \"total_verificados\": 12}', '187.75.21.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'j4tifmc0qvjd2rk0srgufet651', '/sistema-ia.php', 'https://nmrefrigeracao.business/sistema-ia.php', '2025-12-15 15:11:00');

