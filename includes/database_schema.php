<?php
/**
 * database_schema.php
 * 
 * Script para criação e atualização do schema do banco de dados
 * Cria todas as tabelas necessárias para o sistema integrado
 * 
 * Executa verificações e cria tabelas apenas se não existirem
 */

require_once __DIR__ . '/../confg.php';

/**
 * Cria todas as tabelas necessárias no banco de dados
 * @param PDO $pdo Conexão PDO com o banco de dados
 * @return bool True se sucesso, false caso contrário
 */
function criarTabelas($pdo) {
    try {
        // ==========================================
        // TABELA: administradores
        // Armazena usuários administradores do sistema
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS administradores (
                id INT PRIMARY KEY AUTO_INCREMENT,
                usuario VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nome de usuário para login',
                senha VARCHAR(255) NOT NULL COMMENT 'Senha hash (bcrypt)',
                nome VARCHAR(100) NOT NULL COMMENT 'Nome completo do administrador',
                email VARCHAR(100) NOT NULL COMMENT 'Email do administrador',
                telefone VARCHAR(20) COMMENT 'Telefone de contato',
                nivel_acesso ENUM('admin', 'gerente', 'tecnico', 'atendente') DEFAULT 'atendente' COMMENT 'Nível de acesso do usuário',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Define se o usuário está ativo',
                ultimo_acesso DATETIME COMMENT 'Data e hora do último login',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação do registro',
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de administradores do sistema'
        ");

        // ==========================================
        // TABELA: clientes
        // Armazena dados dos clientes
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS clientes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL COMMENT 'Nome completo do cliente',
                email VARCHAR(100) COMMENT 'Email do cliente',
                telefone VARCHAR(20) COMMENT 'Telefone principal',
                whatsapp VARCHAR(20) COMMENT 'Número WhatsApp',
                cpf_cnpj VARCHAR(18) COMMENT 'CPF ou CNPJ do cliente',
                rg VARCHAR(20) COMMENT 'RG do cliente',
                tipo_pessoa ENUM('fisica', 'juridica') DEFAULT 'fisica' COMMENT 'Tipo de pessoa',
                
                -- Endereço
                cep VARCHAR(9) COMMENT 'CEP',
                endereco VARCHAR(200) COMMENT 'Logradouro',
                numero VARCHAR(10) COMMENT 'Número',
                complemento VARCHAR(100) COMMENT 'Complemento',
                bairro VARCHAR(100) COMMENT 'Bairro',
                cidade VARCHAR(100) COMMENT 'Cidade',
                estado VARCHAR(2) COMMENT 'Estado (UF)',
                
                -- Dados adicionais
                anotacoes TEXT COMMENT 'Anotações sobre o cliente',
                observacoes TEXT COMMENT 'Observações gerais',
                
                -- Login do cliente
                senha VARCHAR(255) COMMENT 'Senha hash para área do cliente',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Cliente ativo',
                
                -- Datas
                data_nascimento DATE COMMENT 'Data de nascimento',
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do cadastro',
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
                
                INDEX idx_nome (nome),
                INDEX idx_email (email),
                INDEX idx_telefone (telefone),
                INDEX idx_cpf_cnpj (cpf_cnpj)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de clientes'
        ");

        // ==========================================
        // TABELA: documentos_clientes
        // Armazena documentos dos clientes
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS documentos_clientes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                tipo_documento VARCHAR(50) NOT NULL COMMENT 'Tipo do documento (RG, CPF, Comprovante, etc)',
                nome_arquivo VARCHAR(255) NOT NULL COMMENT 'Nome do arquivo',
                caminho_arquivo VARCHAR(500) NOT NULL COMMENT 'Caminho do arquivo no servidor',
                tamanho_arquivo INT COMMENT 'Tamanho do arquivo em bytes',
                tipo_mime VARCHAR(100) COMMENT 'Tipo MIME do arquivo',
                descricao TEXT COMMENT 'Descrição do documento',
                data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do upload',
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                INDEX idx_cliente (cliente_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Documentos dos clientes'
        ");

        // ==========================================
        // TABELA: categorias_produtos
        // Categorias de produtos
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categorias_produtos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL COMMENT 'Nome da categoria',
                descricao TEXT COMMENT 'Descrição da categoria',
                slug VARCHAR(100) UNIQUE COMMENT 'Slug para URL',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Categoria ativa',
                ordem INT DEFAULT 0 COMMENT 'Ordem de exibição',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                INDEX idx_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias de produtos'
        ");

        // ==========================================
        // TABELA: produtos
        // Produtos disponíveis para venda
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS produtos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                categoria_id INT COMMENT 'ID da categoria',
                codigo_produto VARCHAR(50) UNIQUE COMMENT 'Código/SKU do produto',
                nome VARCHAR(200) NOT NULL COMMENT 'Nome do produto',
                descricao TEXT COMMENT 'Descrição detalhada',
                descricao_curta VARCHAR(500) COMMENT 'Descrição resumida',
                
                -- Especificações técnicas
                marca VARCHAR(100) COMMENT 'Marca do produto',
                modelo VARCHAR(100) COMMENT 'Modelo',
                btus INT COMMENT 'BTUs (para ar condicionado)',
                voltagem VARCHAR(20) COMMENT 'Voltagem (110V, 220V, etc)',
                tipo VARCHAR(50) COMMENT 'Tipo do produto',
                
                -- Financeiro
                preco_custo DECIMAL(10,2) COMMENT 'Preço de custo',
                preco_venda DECIMAL(10,2) NOT NULL COMMENT 'Preço de venda',
                preco_promocional DECIMAL(10,2) COMMENT 'Preço promocional',
                margem_lucro DECIMAL(5,2) COMMENT 'Margem de lucro (%)',
                
                -- Estoque
                estoque_atual INT DEFAULT 0 COMMENT 'Quantidade em estoque',
                estoque_minimo INT DEFAULT 0 COMMENT 'Estoque mínimo',
                estoque_maximo INT DEFAULT 0 COMMENT 'Estoque máximo',
                
                -- Imagens
                imagem_principal VARCHAR(500) COMMENT 'Caminho da imagem principal',
                imagens_adicionais TEXT COMMENT 'JSON com caminhos de imagens adicionais',
                
                -- Controle
                destaque BOOLEAN DEFAULT FALSE COMMENT 'Produto em destaque',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Produto ativo',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                FOREIGN KEY (categoria_id) REFERENCES categorias_produtos(id) ON DELETE SET NULL,
                INDEX idx_codigo (codigo_produto),
                INDEX idx_nome (nome),
                INDEX idx_categoria (categoria_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos para venda'
        ");

        // ==========================================
        // TABELA: servicos
        // Serviços oferecidos pela empresa
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS servicos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(200) NOT NULL COMMENT 'Nome do serviço',
                descricao TEXT COMMENT 'Descrição detalhada',
                descricao_curta VARCHAR(500) COMMENT 'Descrição resumida',
                categoria ENUM('instalacao', 'manutencao', 'limpeza', 'reparo', 'remocao', 'outros') DEFAULT 'outros' COMMENT 'Categoria do serviço',
                
                -- Financeiro
                preco_base DECIMAL(10,2) NOT NULL COMMENT 'Preço base do serviço',
                tempo_estimado INT DEFAULT 60 COMMENT 'Tempo estimado em minutos',
                
                -- Controle
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Serviço ativo',
                destaque BOOLEAN DEFAULT FALSE COMMENT 'Serviço em destaque',
                ordem INT DEFAULT 0 COMMENT 'Ordem de exibição',
                
                -- Imagem
                imagem VARCHAR(500) COMMENT 'Caminho da imagem',
                
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                INDEX idx_categoria (categoria)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Serviços oferecidos'
        ");

        // ==========================================
        // TABELA: materiais
        // Materiais utilizados nos serviços
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS materiais (
                id INT PRIMARY KEY AUTO_INCREMENT,
                codigo VARCHAR(50) UNIQUE COMMENT 'Código do material',
                nome VARCHAR(200) NOT NULL COMMENT 'Nome do material',
                descricao TEXT COMMENT 'Descrição',
                categoria VARCHAR(100) COMMENT 'Categoria do material',
                
                -- Financeiro
                preco_custo DECIMAL(10,2) COMMENT 'Preço de custo',
                preco_venda DECIMAL(10,2) COMMENT 'Preço de venda',
                
                -- Estoque
                unidade_medida VARCHAR(20) DEFAULT 'unidade' COMMENT 'Unidade de medida',
                estoque_atual DECIMAL(10,2) DEFAULT 0 COMMENT 'Estoque atual',
                estoque_minimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Estoque mínimo',
                
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Material ativo',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                INDEX idx_codigo (codigo),
                INDEX idx_categoria (categoria)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Materiais utilizados nos serviços'
        ");

        // ==========================================
        // TABELA: servicos_materiais
        // Relacionamento entre serviços e materiais necessários
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS servicos_materiais (
                id INT PRIMARY KEY AUTO_INCREMENT,
                servico_id INT NOT NULL COMMENT 'ID do serviço',
                material_id INT NOT NULL COMMENT 'ID do material',
                quantidade DECIMAL(10,2) NOT NULL DEFAULT 1 COMMENT 'Quantidade necessária',
                obrigatorio BOOLEAN DEFAULT TRUE COMMENT 'Material obrigatório',
                
                FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
                FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
                UNIQUE KEY unique_servico_material (servico_id, material_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Materiais necessários por serviço'
        ");

        // ==========================================
        // TABELA: orcamentos
        // Orçamentos enviados aos clientes
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orcamentos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                numero_orcamento VARCHAR(50) UNIQUE COMMENT 'Número do orçamento',
                
                -- Valores
                valor_servicos DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor total dos serviços',
                valor_produtos DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor total dos produtos',
                valor_desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor de desconto',
                valor_acrescimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor de acréscimo',
                valor_total DECIMAL(10,2) NOT NULL COMMENT 'Valor total do orçamento',
                
                -- Detalhes
                descricao TEXT COMMENT 'Descrição do orçamento',
                observacoes TEXT COMMENT 'Observações',
                observacoes_admin TEXT COMMENT 'Observações internas (admin)',
                
                -- Equipamento (se aplicável)
                equipamento_marca VARCHAR(100) COMMENT 'Marca do equipamento',
                equipamento_btus INT COMMENT 'BTUs do equipamento',
                equipamento_tipo VARCHAR(100) COMMENT 'Tipo de equipamento',
                
                -- Status e controle
                status ENUM('pendente', 'enviado', 'aprovado', 'recusado', 'expirado', 'convertido') DEFAULT 'pendente' COMMENT 'Status do orçamento',
                validade_dias INT DEFAULT 15 COMMENT 'Validade em dias',
                data_validade DATE COMMENT 'Data de validade',
                
                -- PDF
                caminho_pdf VARCHAR(500) COMMENT 'Caminho do PDF gerado',
                
                -- WhatsApp
                enviado_whatsapp BOOLEAN DEFAULT FALSE COMMENT 'Enviado via WhatsApp',
                data_envio_whatsapp DATETIME COMMENT 'Data do envio via WhatsApp',
                
                -- Datas
                data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data da solicitação',
                data_aprovacao DATETIME COMMENT 'Data da aprovação',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                INDEX idx_cliente (cliente_id),
                INDEX idx_status (status),
                INDEX idx_numero (numero_orcamento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Orçamentos'
        ");

        // ==========================================
        // TABELA: orcamentos_itens
        // Itens (produtos/serviços) dos orçamentos
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orcamentos_itens (
                id INT PRIMARY KEY AUTO_INCREMENT,
                orcamento_id INT NOT NULL COMMENT 'ID do orçamento',
                tipo ENUM('produto', 'servico') NOT NULL COMMENT 'Tipo do item',
                produto_id INT COMMENT 'ID do produto (se tipo=produto)',
                servico_id INT COMMENT 'ID do serviço (se tipo=servico)',
                descricao VARCHAR(500) NOT NULL COMMENT 'Descrição do item',
                quantidade DECIMAL(10,2) NOT NULL DEFAULT 1 COMMENT 'Quantidade',
                valor_unitario DECIMAL(10,2) NOT NULL COMMENT 'Valor unitário',
                valor_total DECIMAL(10,2) NOT NULL COMMENT 'Valor total (quantidade * valor_unitario)',
                desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Desconto',
                
                FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
                FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL,
                FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE SET NULL,
                INDEX idx_orcamento (orcamento_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens dos orçamentos'
        ");

        // ==========================================
        // TABELA: pedidos
        // Pedidos de venda (produtos e serviços)
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pedidos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                orcamento_id INT COMMENT 'ID do orçamento relacionado',
                numero_pedido VARCHAR(50) UNIQUE COMMENT 'Número do pedido',
                
                -- Valores
                valor_produtos DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor total dos produtos',
                valor_servicos DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor total dos serviços',
                valor_desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Desconto',
                valor_acrescimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Acréscimo',
                valor_total DECIMAL(10,2) NOT NULL COMMENT 'Valor total',
                
                -- Status e datas
                status ENUM('pendente', 'confirmado', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'pendente',
                data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_conclusao DATETIME COMMENT 'Data de conclusão',
                
                -- Observações
                observacoes TEXT COMMENT 'Observações do pedido',
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_numero (numero_pedido),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos de venda'
        ");

        // ==========================================
        // TABELA: pedidos_itens
        // Itens dos pedidos
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pedidos_itens (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedido_id INT NOT NULL COMMENT 'ID do pedido',
                tipo ENUM('produto', 'servico') NOT NULL COMMENT 'Tipo do item',
                produto_id INT COMMENT 'ID do produto',
                servico_id INT COMMENT 'ID do serviço',
                descricao VARCHAR(500) NOT NULL COMMENT 'Descrição',
                quantidade DECIMAL(10,2) NOT NULL DEFAULT 1,
                valor_unitario DECIMAL(10,2) NOT NULL,
                valor_total DECIMAL(10,2) NOT NULL,
                
                FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
                FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL,
                FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE SET NULL,
                INDEX idx_pedido (pedido_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens dos pedidos'
        ");

        // ==========================================
        // TABELA: vendas
        // Registro de vendas realizadas
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vendas (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                pedido_id INT COMMENT 'ID do pedido relacionado',
                numero_venda VARCHAR(50) UNIQUE COMMENT 'Número da venda',
                
                -- Valores
                valor_total DECIMAL(10,2) NOT NULL COMMENT 'Valor total da venda',
                valor_pago DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor já pago',
                valor_pendente DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor pendente',
                
                -- Forma de pagamento
                forma_pagamento ENUM('dinheiro', 'pix', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outros') COMMENT 'Forma de pagamento',
                parcelas INT DEFAULT 1 COMMENT 'Número de parcelas',
                
                -- Status
                status_pagamento ENUM('pendente', 'parcial', 'pago', 'cancelado') DEFAULT 'pendente',
                
                -- Datas
                data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_pagamento_completo DATETIME COMMENT 'Data do pagamento completo',
                
                -- Observações
                observacoes TEXT COMMENT 'Observações',
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_numero (numero_venda),
                INDEX idx_status (status_pagamento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vendas realizadas'
        ");

        // ==========================================
        // TABELA: agendamentos
        // Agendamentos de serviços
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS agendamentos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                servico_id INT NOT NULL COMMENT 'ID do serviço',
                orcamento_id INT COMMENT 'ID do orçamento relacionado',
                
                -- Data e hora
                data_agendamento DATE NOT NULL COMMENT 'Data do agendamento',
                hora_agendamento TIME NOT NULL COMMENT 'Hora do agendamento',
                data_fim DATE COMMENT 'Data de término',
                hora_fim TIME COMMENT 'Hora de término',
                duracao_estimada_min INT DEFAULT 120 COMMENT 'Duração estimada em minutos',
                
                -- Endereço (pode ser diferente do cadastro do cliente)
                endereco TEXT COMMENT 'Endereço completo do serviço',
                
                -- Observações
                observacoes TEXT COMMENT 'Observações do agendamento',
                acrescimo_especial DECIMAL(10,2) DEFAULT 0 COMMENT 'Acréscimo especial',
                
                -- Status
                status ENUM('agendado', 'confirmado', 'em_andamento', 'realizado', 'cancelado', 'finalizado') DEFAULT 'agendado',
                
                -- Controle
                origem VARCHAR(50) DEFAULT 'site' COMMENT 'Origem do agendamento (site, telefone, whatsapp, etc)',
                origem_id INT COMMENT 'ID da origem (ex: ID do orçamento)',
                
                -- Notificações
                notificado_whatsapp BOOLEAN DEFAULT FALSE COMMENT 'Cliente notificado via WhatsApp',
                data_notificacao DATETIME COMMENT 'Data da notificação',
                
                -- Datas
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
                FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_data (data_agendamento),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Agendamentos de serviços'
        ");

        // ==========================================
        // TABELA: cobrancas
        // Gestão de cobranças e recebimentos
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cobrancas (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                venda_id INT COMMENT 'ID da venda relacionada',
                pedido_id INT COMMENT 'ID do pedido relacionado',
                
                -- Dados da cobrança
                descricao VARCHAR(500) NOT NULL COMMENT 'Descrição da cobrança',
                valor DECIMAL(10,2) NOT NULL COMMENT 'Valor da cobrança',
                numero_parcela INT DEFAULT 1 COMMENT 'Número da parcela',
                total_parcelas INT DEFAULT 1 COMMENT 'Total de parcelas',
                
                -- Datas
                data_vencimento DATE NOT NULL COMMENT 'Data de vencimento',
                data_pagamento DATE COMMENT 'Data do pagamento',
                
                -- Status
                status ENUM('pendente', 'vencido', 'pago', 'cancelado') DEFAULT 'pendente',
                
                -- Forma de pagamento
                forma_pagamento VARCHAR(50) COMMENT 'Forma de pagamento utilizada',
                
                -- Observações
                observacoes TEXT COMMENT 'Observações',
                
                -- Notificações
                notificado BOOLEAN DEFAULT FALSE COMMENT 'Cliente foi notificado',
                data_notificacao DATETIME COMMENT 'Data da notificação',
                
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE SET NULL,
                FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_vencimento (data_vencimento),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cobranças e recebimentos'
        ");

        // ==========================================
        // TABELA: garantias
        // Garantias de produtos e serviços
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS garantias (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                venda_id INT COMMENT 'ID da venda relacionada',
                pedido_id INT COMMENT 'ID do pedido relacionado',
                
                -- Dados da garantia
                numero_garantia VARCHAR(50) UNIQUE COMMENT 'Número da garantia',
                tipo ENUM('produto', 'servico', 'instalacao') NOT NULL COMMENT 'Tipo de garantia',
                descricao TEXT NOT NULL COMMENT 'Descrição do que está coberto',
                
                -- Prazo
                data_inicio DATE NOT NULL COMMENT 'Data de início da garantia',
                data_fim DATE NOT NULL COMMENT 'Data de término da garantia',
                prazo_meses INT NOT NULL COMMENT 'Prazo em meses',
                
                -- Termos legais
                termos_garantia TEXT COMMENT 'Termos completos da garantia',
                codigo_defesa_consumidor TEXT COMMENT 'Referência ao Código de Defesa do Consumidor',
                
                -- Status
                status ENUM('ativa', 'utilizada', 'expirada', 'cancelada') DEFAULT 'ativa',
                
                -- PDF
                caminho_pdf VARCHAR(500) COMMENT 'Caminho do PDF da garantia',
                
                -- Controle
                ativo BOOLEAN DEFAULT TRUE,
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE SET NULL,
                FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_numero (numero_garantia),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Garantias'
        ");

        // ==========================================
        // TABELA: preventivas_pmp
        // Plano de Manutenção Preventiva
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS preventivas_pmp (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                titulo VARCHAR(200) NOT NULL COMMENT 'Título do plano',
                descricao TEXT COMMENT 'Descrição do plano',
                
                -- Frequência
                frequencia ENUM('mensal', 'bimestral', 'trimestral', 'semestral', 'anual') NOT NULL COMMENT 'Frequência da manutenção',
                dia_preferencial INT COMMENT 'Dia preferencial do mês (1-31)',
                
                -- Datas
                data_inicio DATE NOT NULL COMMENT 'Data de início do plano',
                data_proxima_manutencao DATE COMMENT 'Data da próxima manutenção',
                data_ultima_manutencao DATE COMMENT 'Data da última manutenção realizada',
                
                -- Checklist (JSON)
                checklist_padrao TEXT COMMENT 'Checklist padrão em JSON',
                checklist_ia TEXT COMMENT 'Checklist gerado/melhorado por IA em JSON',
                
                -- Status
                status ENUM('ativo', 'pausado', 'cancelado', 'concluido') DEFAULT 'ativo',
                
                -- Observações
                observacoes TEXT COMMENT 'Observações',
                
                -- Notificações
                notificar_cliente BOOLEAN DEFAULT TRUE COMMENT 'Enviar notificações ao cliente',
                dias_antecedencia_notificacao INT DEFAULT 7 COMMENT 'Dias de antecedência para notificar',
                
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                INDEX idx_cliente (cliente_id),
                INDEX idx_proxima (data_proxima_manutencao),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plano de Manutenção Preventiva'
        ");

        // ==========================================
        // TABELA: preventivas_execucoes
        // Execuções das manutenções preventivas
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS preventivas_execucoes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                preventiva_id INT NOT NULL COMMENT 'ID do plano de manutenção',
                agendamento_id INT COMMENT 'ID do agendamento relacionado',
                
                -- Data da execução
                data_prevista DATE NOT NULL COMMENT 'Data prevista',
                data_realizada DATE COMMENT 'Data em que foi realizada',
                
                -- Checklist executado
                checklist_executado TEXT COMMENT 'Checklist executado em JSON',
                
                -- Status
                status ENUM('pendente', 'agendada', 'realizada', 'cancelada') DEFAULT 'pendente',
                
                -- Observações técnicas
                observacoes_tecnico TEXT COMMENT 'Observações do técnico',
                problemas_encontrados TEXT COMMENT 'Problemas encontrados',
                acoes_realizadas TEXT COMMENT 'Ações realizadas',
                
                -- Assinatura do cliente
                assinatura_cliente VARCHAR(500) COMMENT 'Caminho da imagem da assinatura',
                
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                FOREIGN KEY (preventiva_id) REFERENCES preventivas_pmp(id) ON DELETE CASCADE,
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
                INDEX idx_preventiva (preventiva_id),
                INDEX idx_data_prevista (data_prevista)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Execuções de manutenções preventivas'
        ");

        // ==========================================
        // TABELA: relatorios_tecnicos
        // Relatórios técnicos dos serviços
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS relatorios_tecnicos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                agendamento_id INT COMMENT 'ID do agendamento relacionado',
                cliente_id INT NOT NULL COMMENT 'ID do cliente',
                tecnico_id INT COMMENT 'ID do técnico (admin) que realizou',
                
                -- Dados do relatório
                titulo VARCHAR(200) NOT NULL COMMENT 'Título do relatório',
                descricao_problema TEXT COMMENT 'Descrição do problema',
                diagnostico TEXT COMMENT 'Diagnóstico técnico',
                solucao_aplicada TEXT COMMENT 'Solução aplicada',
                
                -- Melhorias por IA
                descricao_original TEXT COMMENT 'Descrição original antes da IA',
                descricao_melhorada TEXT COMMENT 'Descrição melhorada pela IA',
                usado_ia BOOLEAN DEFAULT FALSE COMMENT 'Se foi usado IA para melhorar',
                
                -- Materiais e peças
                materiais_utilizados TEXT COMMENT 'Materiais utilizados (JSON)',
                pecas_trocadas TEXT COMMENT 'Peças trocadas (JSON)',
                
                -- Fotos
                fotos TEXT COMMENT 'Caminhos das fotos em JSON',
                
                -- Assinatura
                assinatura_cliente VARCHAR(500) COMMENT 'Caminho da assinatura do cliente',
                assinatura_tecnico VARCHAR(500) COMMENT 'Caminho da assinatura do técnico',
                
                -- PDF
                caminho_pdf VARCHAR(500) COMMENT 'Caminho do PDF gerado',
                
                data_servico DATE NOT NULL COMMENT 'Data do serviço',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (tecnico_id) REFERENCES administradores(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_agendamento (agendamento_id),
                INDEX idx_data (data_servico)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relatórios técnicos'
        ");

        // ==========================================
        // TABELA: configuracoes
        // Configurações do sistema
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS configuracoes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                chave VARCHAR(100) UNIQUE NOT NULL COMMENT 'Chave da configuração',
                valor TEXT COMMENT 'Valor da configuração',
                tipo VARCHAR(20) DEFAULT 'text' COMMENT 'Tipo do valor (text, number, boolean, json)',
                categoria VARCHAR(50) COMMENT 'Categoria da configuração',
                descricao TEXT COMMENT 'Descrição da configuração',
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurações do sistema'
        ");

        // ==========================================
        // TABELA: logs_sistema
        // Logs de acesso e ações do sistema
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS logs_sistema (
                id INT PRIMARY KEY AUTO_INCREMENT,
                usuario_id INT COMMENT 'ID do usuário (admin ou cliente)',
                tipo_usuario ENUM('admin', 'cliente', 'anonimo') DEFAULT 'anonimo' COMMENT 'Tipo de usuário',
                
                -- Dados do log
                tipo_acao VARCHAR(50) NOT NULL COMMENT 'Tipo de ação (login, logout, create, update, delete, view)',
                modulo VARCHAR(50) COMMENT 'Módulo do sistema (clientes, produtos, etc)',
                descricao TEXT COMMENT 'Descrição da ação',
                
                -- Dados técnicos
                ip VARCHAR(45) COMMENT 'IP do usuário',
                user_agent TEXT COMMENT 'User agent do navegador',
                metodo_http VARCHAR(10) COMMENT 'Método HTTP (GET, POST, etc)',
                url TEXT COMMENT 'URL acessada',
                
                -- Dados adicionais
                dados_adicionais TEXT COMMENT 'Dados adicionais em JSON',
                
                -- Nível de severidade
                nivel ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info' COMMENT 'Nível de severidade',
                
                data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do log',
                
                INDEX idx_usuario (usuario_id, tipo_usuario),
                INDEX idx_tipo_acao (tipo_acao),
                INDEX idx_modulo (modulo),
                INDEX idx_data (data_hora),
                INDEX idx_nivel (nivel)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs do sistema'
        ");

        // ==========================================
        // TABELA: movimentacoes_estoque
        // Movimentações de estoque de produtos e materiais
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
                id INT PRIMARY KEY AUTO_INCREMENT,
                tipo_item ENUM('produto', 'material') NOT NULL COMMENT 'Tipo do item',
                item_id INT NOT NULL COMMENT 'ID do produto ou material',
                tipo_movimentacao ENUM('entrada', 'saida', 'ajuste', 'devolucao') NOT NULL COMMENT 'Tipo de movimentação',
                quantidade DECIMAL(10,2) NOT NULL COMMENT 'Quantidade movimentada',
                estoque_anterior DECIMAL(10,2) COMMENT 'Estoque anterior',
                estoque_atual DECIMAL(10,2) COMMENT 'Estoque atual',
                
                -- Motivo
                motivo VARCHAR(200) COMMENT 'Motivo da movimentação',
                observacoes TEXT COMMENT 'Observações',
                
                -- Relacionamentos
                venda_id INT COMMENT 'ID da venda (se aplicável)',
                pedido_id INT COMMENT 'ID do pedido (se aplicável)',
                
                -- Usuário responsável
                usuario_id INT COMMENT 'ID do usuário que fez a movimentação',
                
                data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                FOREIGN KEY (usuario_id) REFERENCES administradores(id) ON DELETE SET NULL,
                FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE SET NULL,
                FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
                INDEX idx_item (tipo_item, item_id),
                INDEX idx_data (data_movimentacao)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque'
        ");

        // ==========================================
        // TABELA: notificacoes_whatsapp
        // Log de notificações enviadas via WhatsApp
        // ==========================================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS notificacoes_whatsapp (
                id INT PRIMARY KEY AUTO_INCREMENT,
                cliente_id INT COMMENT 'ID do cliente destinatário',
                telefone VARCHAR(20) NOT NULL COMMENT 'Telefone destinatário',
                
                -- Dados da mensagem
                tipo_notificacao VARCHAR(50) COMMENT 'Tipo (agendamento, orcamento, cobranca, etc)',
                mensagem TEXT NOT NULL COMMENT 'Texto da mensagem',
                
                -- Anexos
                anexos TEXT COMMENT 'Caminhos de anexos em JSON',
                
                -- Status
                status ENUM('pendente', 'enviado', 'erro', 'entregue', 'lido') DEFAULT 'pendente',
                mensagem_erro TEXT COMMENT 'Mensagem de erro (se houver)',
                
                -- IDs relacionados
                orcamento_id INT COMMENT 'ID do orçamento relacionado',
                agendamento_id INT COMMENT 'ID do agendamento relacionado',
                cobranca_id INT COMMENT 'ID da cobrança relacionada',
                
                -- Datas
                data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_entrega DATETIME COMMENT 'Data de entrega',
                data_leitura DATETIME COMMENT 'Data de leitura',
                
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
                FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE SET NULL,
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
                FOREIGN KEY (cobranca_id) REFERENCES cobrancas(id) ON DELETE SET NULL,
                INDEX idx_cliente (cliente_id),
                INDEX idx_status (status),
                INDEX idx_tipo (tipo_notificacao)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notificações WhatsApp'
        ");

        return true;
    } catch(PDOException $e) {
        error_log("Erro ao criar tabelas: " . $e->getMessage());
        return false;
    }
}

/**
 * Insere dados iniciais nas tabelas
 * @param PDO $pdo Conexão PDO
 */
function inserirDadosIniciais($pdo) {
    try {
        // Admin padrão (senha: admin123)
        $senha_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->exec("
            INSERT IGNORE INTO administradores (usuario, senha, nome, email, nivel_acesso) 
            VALUES ('admin', '$senha_hash', 'Administrador', 'admin@nmrefrigeracao.com.br', 'admin')
        ");

        // Configurações iniciais
        $configs = [
            // Dados da empresa
            ['empresa_nome', 'N&M Refrigeração', 'text', 'empresa', 'Nome da empresa'],
            ['empresa_razao_social', 'N&M Refrigeração LTDA', 'text', 'empresa', 'Razão social'],
            ['empresa_cnpj', '00.000.000/0000-00', 'text', 'empresa', 'CNPJ da empresa'],
            ['empresa_telefone', '(17) 99999-9999', 'text', 'empresa', 'Telefone principal'],
            ['empresa_email', 'contato@nmrefrigeracao.com.br', 'text', 'empresa', 'Email de contato'],
            ['empresa_endereco', 'São José do Rio Preto, SP', 'text', 'empresa', 'Endereço completo'],
            ['empresa_logo', '', 'text', 'empresa', 'Caminho do logo'],
            
            // WhatsApp
            ['whatsapp_numero', '5517999999999', 'text', 'integracao', 'Número WhatsApp'],
            ['whatsapp_api_key', '', 'text', 'integracao', 'API Key WhatsApp'],
            ['whatsapp_ativo', 'false', 'boolean', 'integracao', 'WhatsApp ativo'],
            
            // PIX
            ['pix_chave', '', 'text', 'financeiro', 'Chave PIX'],
            ['pix_tipo', 'telefone', 'text', 'financeiro', 'Tipo de chave PIX'],
            
            // Taxas
            ['taxa_cartao_credito', '2.99', 'number', 'financeiro', 'Taxa cartão de crédito (%)'],
            ['taxa_cartao_debito', '1.99', 'number', 'financeiro', 'Taxa cartão de débito (%)'],
            
            // Sistema
            ['sistema_dias_agendamento', '30', 'number', 'sistema', 'Dias futuros para agendamento'],
            ['sistema_horario_inicio', '08:00', 'text', 'sistema', 'Horário de início'],
            ['sistema_horario_fim', '18:00', 'text', 'sistema', 'Horário de término'],
            ['sistema_intervalo_agendamento', '30', 'number', 'sistema', 'Intervalo entre agendamentos (min)'],
            
            // IA
            ['ia_api_key', '', 'text', 'integracao', 'API Key OpenAI'],
            ['ia_ativo', 'false', 'boolean', 'integracao', 'IA ativa'],
            ['ia_modelo', 'gpt-3.5-turbo', 'text', 'integracao', 'Modelo de IA']
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO configuracoes (chave, valor, tipo, categoria, descricao) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach($configs as $config) {
            $stmt->execute($config);
        }

        // Categorias de produtos iniciais
        $categorias = [
            ['Ar Condicionado Split', 'Aparelhos de ar condicionado tipo split', 'ar-condicionado-split', 1],
            ['Ar Condicionado Janela', 'Aparelhos de ar condicionado tipo janela', 'ar-condicionado-janela', 2],
            ['Peças e Acessórios', 'Peças de reposição e acessórios', 'pecas-acessorios', 3],
            ['Ferramentas', 'Ferramentas para instalação e manutenção', 'ferramentas', 4]
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO categorias_produtos (nome, descricao, slug, ordem) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach($categorias as $cat) {
            $stmt->execute($cat);
        }

        // Serviços iniciais
        $servicos = [
            ['Instalação de Ar Condicionado 9000 BTUs', 'Instalação completa incluindo suporte, tubulação até 3m e mão de obra', 'Instalação profissional de ar condicionado 9000 BTUs', 'instalacao', 350.00, 120, 1],
            ['Instalação de Ar Condicionado 12000 BTUs', 'Instalação completa incluindo suporte, tubulação até 3m e mão de obra', 'Instalação profissional de ar condicionado 12000 BTUs', 'instalacao', 380.00, 120, 2],
            ['Instalação de Ar Condicionado 18000 BTUs', 'Instalação completa incluindo suporte, tubulação até 3m e mão de obra', 'Instalação profissional de ar condicionado 18000 BTUs', 'instalacao', 420.00, 150, 3],
            ['Manutenção Preventiva Completa', 'Limpeza completa, verificação de gás, teste de funcionamento e checklist técnico', 'Manutenção preventiva para garantir o bom funcionamento', 'manutencao', 150.00, 90, 4],
            ['Limpeza Técnica', 'Limpeza interna e externa com produtos específicos', 'Limpeza profissional do ar condicionado', 'limpeza', 100.00, 60, 5],
            ['Diagnóstico e Reparo', 'Diagnóstico do problema e reparo técnico (peças à parte)', 'Diagnóstico técnico e reparo de defeitos', 'reparo', 80.00, 90, 6],
            ['Recarga de Gás', 'Recarga de gás refrigerante com verificação de vazamentos', 'Recarga completa de gás refrigerante', 'manutencao', 180.00, 60, 7],
            ['Remoção de Equipamento', 'Remoção segura do aparelho de ar condicionado', 'Remoção profissional do equipamento', 'remocao', 100.00, 45, 8]
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO servicos (nome, descricao, descricao_curta, categoria, preco_base, tempo_estimado, ordem) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach($servicos as $serv) {
            $stmt->execute($serv);
        }

        registrarLog('info', 'Dados iniciais inseridos no banco de dados');
        
    } catch(PDOException $e) {
        error_log("Erro ao inserir dados iniciais: " . $e->getMessage());
    }
}

// Executar criação de tabelas e inserção de dados iniciais
if (criarTabelas($pdo)) {
    inserirDadosIniciais($pdo);
    echo "Database schema criado com sucesso!";
} else {
    echo "Erro ao criar database schema.";
}
?>
