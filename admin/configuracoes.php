<?php
// admin/configuracoes.php - VERSÃO MELHORADA COM FUNCIONALIDADES COMPLETAS + DESIGN MODERNO

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Incluir configuração com caminho correto
include '../includes/config.php';
include 'includes/auth.php';

// Inicializar variáveis
$mensagem = '';
$sucesso = '';
$erro = '';

// Criar tabela de configurações principais se não existir
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

// VERIFICAR E CRIAR/MODIFICAR TABELA config_agendamento
try {
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'config_agendamento'");
    $tabela_existe = $stmt->rowCount() > 0;
    
    if($tabela_existe) {
        // Verificar se as colunas necessárias existem
        $stmt = $pdo->query("DESCRIBE config_agendamento");
        $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Adicionar coluna 'tipo' se não existir
        if(!in_array('tipo', $colunas)) {
            $pdo->exec("ALTER TABLE config_agendamento ADD COLUMN tipo VARCHAR(20) DEFAULT 'text'");
        }
        
        // Adicionar coluna 'categoria' se não existir
        if(!in_array('categoria', $colunas)) {
            $pdo->exec("ALTER TABLE config_agendamento ADD COLUMN categoria VARCHAR(50)");
        }
        
        // Adicionar coluna 'descricao' se não existir
        if(!in_array('descricao', $colunas)) {
            $pdo->exec("ALTER TABLE config_agendamento ADD COLUMN descricao TEXT");
        }
    } else {
        // Criar tabela completa se não existir
        $pdo->exec("
            CREATE TABLE config_agendamento (
                id INT PRIMARY KEY AUTO_INCREMENT,
                chave VARCHAR(100) UNIQUE NOT NULL,
                valor TEXT,
                tipo VARCHAR(20) DEFAULT 'text',
                descricao TEXT,
                categoria VARCHAR(50),
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }
} catch(PDOException $e) {
    $mensagem = "Aviso: " . $e->getMessage();
}

// Inserir configurações padrão principais
$configuracoes_padrao = [
    ['site_nome', 'ClimaTech', 'text', 'Nome do site'],
    ['site_descricao', 'Especialistas em Ar Condicionado', 'text', 'Descrição do site'],
    ['site_telefone', '(11) 9999-9999', 'text', 'Telefone de contato'],
    ['site_email', 'contato@climatech.com.br', 'email', 'E-mail de contato'],
    ['site_endereco', 'Rua Exemplo, 123 - São Paulo, SP', 'text', 'Endereço'],
    ['site_whatsapp', '5511999999999', 'text', 'Número do WhatsApp (com DDD e sem caracteres especiais)'],
    ['horario_funcionamento', 'Segunda a Sexta: 8h às 18h | Sábado: 8h às 12h', 'text', 'Horário de funcionamento'],
     ['valor_instalacao_base', '299.00', 'number', 'Valor base para instalação'],
    ['taxa_cartao_avista', '5', 'number', 'Taxa para pagamento com cartão à vista (%)'],
    ['taxa_cartao_parcelado', '20', 'number', 'Taxa para pagamento com cartão parcelado (%)'],
    ['maquininha_cartao', 'ton', 'text', 'Máquina de cartão utilizada'],
    ['chave_pix', 'neltelneto1@gmail.com', 'text', 'Chave PIX para pagamentos'],
    ['dias_agendamento', '30', 'number', 'Dias futuros disponíveis para agendamento'],
    ['manutencao', '0', 'boolean', 'Modo manutenção (1 = ativo, 0 = inativo)']
];

foreach($configuracoes_padrao as $config) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO configuracoes (chave, valor, tipo, descricao) VALUES (?, ?, ?, ?)");
    $stmt->execute($config);
}

// Inicializar arrays para evitar erros
$configs_por_categoria = [
    'Geral' => [],
    'Contato' => [],
    'Financeiro' => [],
    'Agendamento' => [],
    'Sistema' => []
];

// Buscar configurações atuais principais
try {
    $stmt = $pdo->prepare("SELECT * FROM configuracoes ORDER BY chave");
    $stmt->execute();
    $configuracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar configurações principais por categoria
    foreach($configuracoes as $config) {
    if(in_array($config['chave'], ['site_nome', 'site_descricao', 'horario_funcionamento', 'manutencao'])) {
        $configs_por_categoria['Geral'][] = $config;
    } elseif(in_array($config['chave'], ['site_telefone', 'site_email', 'site_endereco', 'site_whatsapp'])) {
        $configs_por_categoria['Contato'][] = $config;
    } elseif(in_array($config['chave'], ['valor_instalacao_base', 'taxa_cartao_avista', 'taxa_cartao_parcelado', 'maquininha_cartao', 'chave_pix'])) {
        $configs_por_categoria['Financeiro'][] = $config;
    } elseif(in_array($config['chave'], ['dias_agendamento'])) {
        $configs_por_categoria['Agendamento'][] = $config;
    } else {
        $configs_por_categoria['Sistema'][] = $config;
    }
}
} catch(PDOException $e) {
    $mensagem .= " Erro ao carregar configurações principais: " . $e->getMessage();
}

// Buscar configurações de agendamento existentes
$config_agendamento = [];
try {
    $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($resultados as $row) {
        $config_agendamento[$row['chave']] = $row['valor'];
    }
} catch(PDOException $e) {
    $mensagem .= " Aviso: Erro ao carregar configurações de agendamento: " . $e->getMessage();
}

// Processar atualização de configurações principais
if($_POST && isset($_POST['config'])) {
    try {
        foreach($_POST['config'] as $chave => $valor) {
            $stmt = $pdo->prepare("UPDATE configuracoes SET valor = ? WHERE chave = ?");
            $stmt->execute([$valor, $chave]);
        }
        $sucesso = "Configurações atualizadas com sucesso!";
        
        // Recarregar configurações
        $stmt = $pdo->prepare("SELECT * FROM configuracoes ORDER BY chave");
        $stmt->execute();
        $configuracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Reorganizar configurações
        $configs_por_categoria = [
            'Geral' => [],
            'Contato' => [],
            'Financeiro' => [],
            'Agendamento' => [],
            'Sistema' => []
        ];
        
        foreach($configuracoes as $config) {
            if(in_array($config['chave'], ['site_nome', 'site_descricao', 'horario_funcionamento', 'manutencao'])) {
                $configs_por_categoria['Geral'][] = $config;
            } elseif(in_array($config['chave'], ['site_telefone', 'site_email', 'site_endereco', 'site_whatsapp'])) {
                $configs_por_categoria['Contato'][] = $config;
            } elseif(in_array($config['chave'], ['valor_instalacao_base', 'taxa_cartao'])) {
                $configs_por_categoria['Financeiro'][] = $config;
            } elseif(in_array($config['chave'], ['dias_agendamento'])) {
                $configs_por_categoria['Agendamento'][] = $config;
            } else {
                $configs_por_categoria['Sistema'][] = $config;
            }
        }
        
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar configurações: " . $e->getMessage();
    }
}

// Processar atualização de configurações de agendamento
if($_POST && isset($_POST['config_agendamento'])) {
    try {
        foreach($_POST['config_agendamento'] as $chave => $valor) {
            // Verificar se a configuração já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM config_agendamento WHERE chave = ?");
            $stmt->execute([$chave]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if($existe > 0) {
                // Atualizar configuração existente
                $stmt = $pdo->prepare("UPDATE config_agendamento SET valor = ? WHERE chave = ?");
                $stmt->execute([$valor, $chave]);
            } else {
                // Inserir nova configuração
                $stmt = $pdo->prepare("INSERT INTO config_agendamento (chave, valor) VALUES (?, ?)");
                $stmt->execute([$chave, $valor]);
            }
        }
        
        if(empty($sucesso)) {
            $sucesso = "Configurações de agendamento atualizadas com sucesso!";
        } else {
            $sucesso .= " Configurações de agendamento atualizadas com sucesso!";
        }
        
        // Recarregar as configurações atualizadas
        $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config_agendamento = [];
        foreach($resultados as $row) {
            $config_agendamento[$row['chave']] = $row['valor'];
        }
        
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar configurações de agendamento: " . $e->getMessage();
    }
}

// Buscar serviços para estatísticas
$servicos = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM servicos ORDER BY nome");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Tabela pode não existir ainda
}

// Buscar agendamentos recentes
$agendamentos = [];
try {
    $stmt = $pdo->prepare("SELECT a.*, c.nome as cliente_nome, c.telefone, s.nome as servico_nome 
                          FROM agendamentos a 
                          LEFT JOIN clientes c ON a.cliente_id = c.id 
                          LEFT JOIN servicos s ON a.servico_id = s.id 
                          ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC 
                          LIMIT 10");
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Tabela pode não existir ainda
}

// Incluir o header
include 'includes/header-admin.php';
?>

<!-- Conteúdo específico da página -->
<div class="page-header">
    <div class="page-title">
        <i class="fas fa-cogs"></i>
        Configurações do Sistema
    </div>
    <div class="alert alert-warning alert-modern">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Área restrita - Alterações aqui afetam todo o site
    </div>
</div>

<!-- Alertas -->
<?php if ($sucesso): ?>
    <div class="alert alert-success alert-modern">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $sucesso; ?>
    </div>
<?php endif; ?>

<?php if ($erro): ?>
    <div class="alert alert-danger alert-modern">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $erro; ?>
    </div>
<?php endif; ?>

<?php if($mensagem): ?>
    <div class="alert alert-info alert-modern"><?php echo $mensagem; ?></div>
<?php endif; ?>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-calendar-check"></i>
            <div class="number"><?php echo count($agendamentos); ?></div>
            <div>Agendamentos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-tools"></i>
            <div class="number"><?php echo count($servicos); ?></div>
            <div>Serviços</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-cog"></i>
            <div class="number"><?php echo count($configuracoes); ?></div>
            <div>Configurações</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-clock"></i>
            <div class="number"><?php echo $config_agendamento['limite_agendamentos_dia'] ?? '8'; ?></div>
            <div>Limite/Dia</div>
        </div>
    </div>
</div>

<!-- Sistema de Abas -->
<div class="modern-card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="configTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">
                    <i class="fas fa-globe me-2"></i>Configurações Gerais
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="agendamento-tab" data-bs-toggle="tab" data-bs-target="#agendamento" type="button" role="tab">
                    <i class="fas fa-calendar-alt me-2"></i>Agendamento
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema" type="button" role="tab">
                    <i class="fas fa-cog me-2"></i>Sistema
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="configTabsContent">
            
            <!-- ABA: CONFIGURAÇÕES GERAIS -->
            <div class="tab-pane fade show active" id="geral" role="tabpanel">
                <form method="POST" class="config-form">
                    <div class="row">
                        <!-- Configurações Gerais -->
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Site
                                </h5>
                                
                                <?php foreach($configs_por_categoria['Geral'] as $config): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $config['descricao']; ?></label>
                                    
                                    <?php if($config['tipo'] == 'text' || $config['tipo'] == 'email'): ?>
                                    <input type="<?php echo $config['tipo']; ?>" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    
                                    <?php elseif($config['tipo'] == 'number'): ?>
                                    <input type="number" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>" 
                                           step="0.01">
                                    
                                    <?php elseif($config['tipo'] == 'boolean'): ?>
                                    <select name="config[<?php echo $config['chave']; ?>]" 
                                            class="form-control form-control-modern">
                                        <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Inativo</option>
                                        <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativo</option>
                                    </select>
                                    
                                    <?php elseif($config['tipo'] == 'textarea'): ?>
                                    <textarea name="config[<?php echo $config['chave']; ?>]" 
                                              class="form-control form-control-modern" 
                                              rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                                    <?php else: ?>
                                    <input type="text" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    <?php endif; ?>
                                    
                                    <small class="form-text text-muted">Chave: <code><?php echo $config['chave']; ?></code></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Configurações de Contato -->
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-address-book me-2"></i>Informações de Contato
                                </h5>
                                
                                <?php foreach($configs_por_categoria['Contato'] as $config): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $config['descricao']; ?></label>
                                    
                                    <?php if($config['tipo'] == 'text' || $config['tipo'] == 'email'): ?>
                                    <input type="<?php echo $config['tipo']; ?>" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    
                                    <?php elseif($config['tipo'] == 'number'): ?>
                                    <input type="number" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>" 
                                           step="0.01">
                                    
                                    <?php elseif($config['tipo'] == 'boolean'): ?>
                                    <select name="config[<?php echo $config['chave']; ?>]" 
                                            class="form-control form-control-modern">
                                        <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Inativo</option>
                                        <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativo</option>
                                    </select>
                                    
                                    <?php elseif($config['tipo'] == 'textarea'): ?>
                                    <textarea name="config[<?php echo $config['chave']; ?>]" 
                                              class="form-control form-control-modern" 
                                              rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                                    <?php else: ?>
                                    <input type="text" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    <?php endif; ?>
                                    
                                    <small class="form-text text-muted">Chave: <code><?php echo $config['chave']; ?></code></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Configurações Financeiras -->
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-dollar-sign me-2"></i>Configurações Financeiras
                                </h5>
                                
                                <?php foreach($configs_por_categoria['Financeiro'] as $config): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $config['descricao']; ?></label>
                                    
                                    <?php if($config['tipo'] == 'text' || $config['tipo'] == 'email'): ?>
                                    <input type="<?php echo $config['tipo']; ?>" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    
                                    <?php elseif($config['tipo'] == 'number'): ?>
                                    <input type="number" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>" 
                                           step="0.01">
                                    
                                    <?php elseif($config['tipo'] == 'boolean'): ?>
                                    <select name="config[<?php echo $config['chave']; ?>]" 
                                            class="form-control form-control-modern">
                                        <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Inativo</option>
                                        <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativo</option>
                                    </select>
                                    
                                    <?php elseif($config['tipo'] == 'textarea'): ?>
                                    <textarea name="config[<?php echo $config['chave']; ?>]" 
                                              class="form-control form-control-modern" 
                                              rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                                    <?php else: ?>
                                    <input type="text" 
                                           name="config[<?php echo $config['chave']; ?>]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo htmlspecialchars($config['valor']); ?>">
                                    <?php endif; ?>
                                    
                                    <small class="form-text text-muted">Chave: <code><?php echo $config['chave']; ?></code></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Salvar Configurações Gerais
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- ABA: CONFIGURAÇÕES DE AGENDAMENTO -->
            <div class="tab-pane fade" id="agendamento" role="tabpanel">
                <form method="POST" class="config-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-clock me-2"></i>Horários de Atendimento
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Horário Início (Comercial)</label>
                                        <input type="time" 
                                               name="config_agendamento[horario_inicio]" 
                                               class="form-control form-control-modern" 
                                               value="<?php echo $config_agendamento['horario_inicio'] ?? '08:00'; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Horário Fim (Comercial)</label>
                                        <input type="time" 
                                               name="config_agendamento[horario_fim]" 
                                               class="form-control form-control-modern" 
                                               value="<?php echo $config_agendamento['horario_fim'] ?? '18:00'; ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Horário Especial Início</label>
                                        <input type="time" 
                                               name="config_agendamento[horario_especial_inicio]" 
                                               class="form-control form-control-modern" 
                                               value="<?php echo $config_agendamento['horario_especial_inicio'] ?? '18:00'; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Horário Especial Fim</label>
                                        <input type="time" 
                                               name="config_agendamento[horario_especial_fim]" 
                                               class="form-control form-control-modern" 
                                               value="<?php echo $config_agendamento['horario_especial_fim'] ?? '20:00'; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-chart-line me-2"></i>Limites e Restrições
                                </h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Limite de Agendamentos por Dia</label>
                                    <input type="number" 
                                           name="config_agendamento[limite_agendamentos_dia]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo $config_agendamento['limite_agendamentos_dia'] ?? '8'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Dias Futuros para Agendamento</label>
                                    <input type="number" 
                                           name="config[dias_agendamento]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo $configs_por_categoria['Agendamento'][0]['valor'] ?? '30'; ?>">
                                    <small class="form-text text-muted">Número de dias no futuro disponíveis para agendamento</small>
                                </div>
                                
                                <div class="mb-3 form-check form-check-modern">
                                    <input type="checkbox" class="form-check-input" name="config_agendamento[bloquear_finais_semana]" value="1" 
                                           <?php echo ($config_agendamento['bloquear_finais_semana'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Bloquear agendamentos nos finais de semana</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fas fa-dollar-sign me-2"></i>Valores de Acréscimo
                                </h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Valor Hora Especial (R$)</label>
                                    <input type="number" 
                                           name="config_agendamento[valor_hora_especial]" 
                                           class="form-control form-control-modern" 
                                           step="0.01" 
                                           value="<?php echo $config_agendamento['valor_hora_especial'] ?? '50.00'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Valor Final de Semana (R$)</label>
                                    <input type="number" 
                                           name="config_agendamento[valor_final_semana]" 
                                           class="form-control form-control-modern" 
                                           step="0.01" 
                                           value="<?php echo $config_agendamento['valor_final_semana'] ?? '150.00'; ?>">
                                </div>
                            </div>
                            
                            <div class="config-section">
                                <h5 class="section-title">
                                    <i class="fab fa-whatsapp me-2"></i>Integração WhatsApp
                                </h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">WhatsApp da Empresa</label>
                                    <input type="text" 
                                           name="config_agendamento[whatsapp_empresa]" 
                                           class="form-control form-control-modern" 
                                           value="<?php echo $config_agendamento['whatsapp_empresa'] ?? '5511999999999'; ?>" 
                                           placeholder="5511999999999">
                                    <small class="form-text text-muted">Formato: 55 + DDD + Número (sem espaços ou caracteres especiais)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Mensagem do WhatsApp</label>
                                    <textarea name="config_agendamento[mensagem_whatsapp]" 
                                              class="form-control form-control-modern" 
                                              rows="6"><?php echo $config_agendamento['mensagem_whatsapp'] ?? '📅 *Novo Agendamento ClimaTech*

👤 *Cliente:* {nome}
📞 *Telefone:* {telefone}
📧 *E-mail:* {email}
📍 *Endereço:* {endereco}

🔧 *Serviço:* {servico}
📅 *Data:* {data}
⏰ *Horário:* {hora}

💬 *Observações:*
{observacoes}

⚡ *Agendado via Site*'; ?></textarea>
                                    <small class="form-text text-muted">
                                        Variáveis disponíveis: {nome}, {telefone}, {email}, {endereco}, {servico}, {data}, {hora}, {observacoes}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Salvar Configurações de Agendamento
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- ABA: SISTEMA -->
            <div class="tab-pane fade" id="sistema" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Agendamentos Recentes -->
                        <div class="modern-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-history me-2"></i>Agendamentos Recentes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($agendamentos)): ?>
                                    <p class="text-muted">Nenhum agendamento encontrado.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-modern">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th>Data</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($agendamentos as $agendamento): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($agendamento['cliente_nome'] ?? 'N/A'); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo $agendamento['telefone'] ?? ''; ?></small>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?><br>
                                                        <small class="text-muted"><?php echo $agendamento['hora_agendamento']; ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-modern badge-<?php 
                                                            switch($agendamento['status'] ?? 'pendente') {
                                                                case 'confirmado': echo 'success'; break;
                                                                case 'cancelado': echo 'danger'; break;
                                                                default: echo 'warning';
                                                            }
                                                        ?>">
                                                            <?php echo $agendamento['status'] ?? 'pendente'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="agendamentos.php" class="btn btn-outline-primary btn-modern">
                                        <i class="fas fa-list me-2"></i>Ver Todos
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Informações do Sistema -->
                        <div class="modern-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Sistema
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="system-info">
                                    <div class="info-item">
                                        <span class="info-label">PHP Version:</span>
                                        <span class="info-value"><?php echo phpversion(); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Database:</span>
                                        <span class="info-value">MySQL</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Configurações:</span>
                                        <span class="info-value"><?php echo count($configuracoes); ?> registros</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Serviços:</span>
                                        <span class="info-value"><?php echo count($servicos); ?> cadastrados</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h6 class="section-title">Ações do Sistema</h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-warning btn-modern">
                                            <i class="fas fa-sync me-2"></i>Limpar Cache
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-modern">
                                            <i class="fas fa-database me-2"></i>Backup do Banco
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-modern">
                                            <i class="fas fa-trash me-2"></i>Limpar Logs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos -->
<style>
.alert-modern {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border: none;
    margin-bottom: 2rem;
}

.modern-card .card-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 16px 16px 0 0 !important;
    padding: 1.5rem;
}

.modern-card .card-body {
    padding: 2rem;
}

.card-header-tabs {
    border-bottom: none;
}

.card-header-tabs .nav-link {
    border: none;
    border-radius: 10px;
    padding: 12px 20px;
    margin-right: 10px;
    color: var(--gray);
    font-weight: 500;
    transition: all 0.3s ease;
}

.card-header-tabs .nav-link.active {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}

.card-header-tabs .nav-link:hover:not(.active) {
    background: #f8fafc;
    color: var(--primary-color);
}

.form-control-modern {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    transform: translateY(-2px);
}

.form-check-modern .form-check-input {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.form-check-modern .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.section-title {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f1f5f9;
}

.config-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.stats-card {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 1.5rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
}

.stats-card i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-card .number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border: none;
    border-radius: 10px;
    padding: 12px 25px;
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    color: white;
}

.btn-outline-primary {
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    background: transparent;
    border-radius: 10px;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.table-modern {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.table-modern th {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 500;
}

.table-modern td {
    padding: 12px 15px;
    border-color: #f1f5f9;
}

.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.badge-success {
    background: #10b981;
}

.badge-warning {
    background: #f59e0b;
}

.badge-danger {
    background: #ef4444;
}

.system-info .info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}

.system-info .info-label {
    font-weight: 500;
    color: var(--dark-color);
}

.system-info .info-value {
    color: var(--gray);
}

/* Responsivo */
@media (max-width: 768px) {
    .modern-card .card-body {
        padding: 1rem;
    }
    
    .config-section {
        padding: 1rem;
    }
    
    .card-header-tabs .nav-link {
        padding: 10px 15px;
        font-size: 0.9rem;
    }
}
</style>

<?php
// Incluir o footer do admin
include 'includes/footer-admin.php';
?>