<?php
// includes/database.php

// Criar tabelas necessárias se não existirem
function criarTabelas($pdo) {
    
    // Tabela de administradores
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS administradores (
            id INT PRIMARY KEY AUTO_INCREMENT,
            usuario VARCHAR(50) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de serviços
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS servicos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            preco_base DECIMAL(10,2),
            categoria ENUM('instalacao', 'manutencao', 'limpeza', 'reparo', 'remocao'),
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de produtos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS produtos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            preco DECIMAL(10,2),
            categoria VARCHAR(50),
            marca VARCHAR(50),
            btus INT,
            estoque INT DEFAULT 0,
            imagem VARCHAR(255),
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de clientes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clientes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            telefone VARCHAR(20),
            endereco TEXT,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de orçamentos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orcamentos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            cliente_id INT,
            servico_id INT,
            equipamento_marca VARCHAR(50),
            equipamento_btus INT,
            equipamento_tipo VARCHAR(50),
            descricao TEXT,
            valor_total DECIMAL(10,2),
            status ENUM('pendente', 'aprovado', 'recusado', 'concluido') DEFAULT 'pendente',
            observacoes_admin TEXT,
            data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clientes(id),
            FOREIGN KEY (servico_id) REFERENCES servicos(id)
        )
    ");
    
    // Tabela de agendamentos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agendamentos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            cliente_id INT,
            servico_id INT,
            data_agendamento DATE,
            hora_agendamento TIME,
            observacoes TEXT,
            status ENUM('agendado', 'confirmado', 'realizado', 'cancelado') DEFAULT 'agendado',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clientes(id),
            FOREIGN KEY (servico_id) REFERENCES servicos(id)
        )
    ");
    
    // Tabela de materiais
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS materiais (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(50),
            preco_unitario DECIMAL(10,2),
            unidade_medida VARCHAR(20) DEFAULT 'unidade',
            estoque INT DEFAULT 0,
            estoque_minimo INT DEFAULT 0,
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de configurações
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS configuracoes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            chave VARCHAR(100) UNIQUE NOT NULL,
            valor TEXT,
            tipo VARCHAR(20) DEFAULT 'text',
            descricao TEXT,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Inserir dados iniciais
    inserirDadosIniciais($pdo);
}

function inserirDadosIniciais($pdo) {
    
    // Admin padrão (senha: 123456)
    $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO administradores (usuario, senha, nome, email) VALUES 
                ('admin', '$senha_hash', 'Administrador', 'admin@climatech-sjrp.com.br')");
    
    // Serviços iniciais
    $servicos = [
        ['Instalação de Ar Condicionado 9000 BTUs', 'Instalação completa com material incluído para ar condicionado 9000 BTUs', 299.00, 'instalacao'],
        ['Instalação de Ar Condicionado 12000 BTUs', 'Instalação completa com material incluído para ar condicionado 12000 BTUs', 349.00, 'instalacao'],
        ['Instalação de Ar Condicionado 18000 BTUs', 'Instalação completa com material incluído para ar condicionado 18000 BTUs', 399.00, 'instalacao'],
        ['Manutenção Preventiva', 'Limpeza e verificação completa do equipamento', 129.00, 'manutencao'],
        ['Limpeza Técnica Completa', 'Limpeza interna e externa com produtos específicos', 89.00, 'limpeza'],
        ['Reparo Técnico Especializado', 'Diagnóstico e reparo de problemas técnicos', 79.00, 'reparo'],
        ['Remoção de Equipamento', 'Remoção segura do ar condicionado', 59.00, 'remocao']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO servicos (nome, descricao, preco_base, categoria) VALUES (?, ?, ?, ?)");
    foreach($servicos as $servico) {
        $stmt->execute($servico);
    }
    
    // Produtos iniciais
    $produtos = [
        ['Ar Condicionado Split Samsung 9000 BTUs', 'Ar condicionado split Samsung, 9000 BTUs, inverter, eficiência A', 1299.00, 'Split', 'Samsung', 9000, 5],
        ['Ar Condicionado Split LG 12000 BTUs', 'Ar condicionado split LG, 12000 BTUs, dual inverter, wifi', 1599.00, 'Split', 'LG', 12000, 3],
        ['Ar Condicionado Split Midea 9000 BTUs', 'Ar condicionado split Midea, 9000 BTUs, inverter, turbo cool', 1099.00, 'Split', 'Midea', 9000, 8],
        ['Ar Condicionado Split Gree 18000 BTUs', 'Ar condicionado split Gree, 18000 BTUs, inverter, silencioso', 1899.00, 'Split', 'Gree', 18000, 2]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO produtos (nome, descricao, preco, categoria, marca, btus, estoque) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach($produtos as $produto) {
        $stmt->execute($produto);
    }
    
    // Configurações iniciais
    $configuracoes = [
        ['site_nome', 'ClimaTech - Especialistas em Ar Condicionado', 'text', 'Nome do site'],
        ['site_descricao', 'Especialista em ar condicionado em São José do Rio Preto. Instalação, manutenção, limpeza e venda.', 'text', 'Descrição do site'],
        ['site_telefone', '(17) 99999-9999', 'text', 'Telefone principal'],
        ['site_email', 'contato@climatech-sjrp.com.br', 'email', 'E-mail de contato'],
        ['site_endereco', 'São José do Rio Preto, SP', 'text', 'Endereço'],
        ['site_whatsapp', '5517999999999', 'text', 'WhatsApp para contato'],
        ['horario_funcionamento', 'Segunda a Sexta: 8h às 18h | Sábado: 8h às 12h', 'text', 'Horário de atendimento'],
        ['valor_instalacao_base', '299.00', 'number', 'Valor base para instalação'],
        ['taxa_cartao', '2.99', 'number', 'Taxa para pagamento com cartão'],
        ['dias_agendamento', '30', 'number', 'Dias futuros para agendamento']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO configuracoes (chave, valor, tipo, descricao) VALUES (?, ?, ?, ?)");
    foreach($configuracoes as $config) {
        $stmt->execute($config);
    }
}

// Executar criação de tabelas
try {
    criarTabelas($pdo);
} catch(PDOException $e) {
    error_log("Erro ao criar tabelas: " . $e->getMessage());
}
?>