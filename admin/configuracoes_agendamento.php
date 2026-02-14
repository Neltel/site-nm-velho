<?php
// admin/configuracoes_agendamento.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$mensagem = '';

// Criar tabela de configurações de agendamento se não existir
$pdo->exec("
    CREATE TABLE IF NOT EXISTS config_agendamento (
        id INT PRIMARY KEY AUTO_INCREMENT,
        chave VARCHAR(100) UNIQUE NOT NULL,
        valor TEXT,
        descricao TEXT,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Configurações padrão
$configs_padrao = [
    ['horario_inicio', '08:00', 'Horário de início do atendimento'],
    ['horario_fim', '18:00', 'Horário de fim do atendimento'],
    ['intervalo_agendamento', '60', 'Intervalo entre agendamentos (minutos)'],
    ['tempo_deslocamento', '30', 'Tempo médio de deslocamento entre serviços (minutos)'],
    ['max_agendamentos_dia', '4', 'Máximo de agendamentos por dia'],
    ['dias_antecipacao', '30', 'Dias de antecedência para agendamento'],
    ['valor_horario_normal', '0', 'Acréscimo para horário normal (%)'],
    ['valor_fora_horario', '30', 'Acréscimo para fora do horário comercial (%)'],
    ['valor_final_semana', '50', 'Acréscimo para finais de semana (%)'],
    ['valor_feriado', '100', 'Acréscimo para feriados (%)'],
    ['bloquear_feriados', '1', 'Bloquear agendamentos em feriados (1=sim, 0=não)'],
    ['horario_almoco_inicio', '12:00', 'Início do horário de almoço'],
    ['horario_almoco_fim', '13:00', 'Fim do horário de almoço']
];

foreach($configs_padrao as $config) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO config_agendamento (chave, valor, descricao) VALUES (?, ?, ?)");
    $stmt->execute($config);
}

// Processar atualização
if($_POST) {
    try {
        foreach($_POST['config'] as $chave => $valor) {
            $stmt = $pdo->prepare("UPDATE config_agendamento SET valor = ? WHERE chave = ?");
            $stmt->execute([$valor, $chave]);
        }
        $mensagem = "Configurações de agendamento atualizadas com sucesso!";
    } catch(PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Buscar configurações atuais
$stmt = $pdo->prepare("SELECT * FROM config_agendamento ORDER BY chave");
$stmt->execute();
$configuracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar por categorias
$configs_organizadas = [
    'Horários' => [],
    'Limites' => [],
    'Valores' => [],
    'Regras' => []
];

foreach($configuracoes as $config) {
    if(in_array($config['chave'], ['horario_inicio', 'horario_fim', 'horario_almoco_inicio', 'horario_almoco_fim', 'intervalo_agendamento'])) {
        $configs_organizadas['Horários'][] = $config;
    } elseif(in_array($config['chave'], ['max_agendamentos_dia', 'dias_antecipacao', 'tempo_deslocamento'])) {
        $configs_organizadas['Limites'][] = $config;
    } elseif(strpos($config['chave'], 'valor_') === 0) {
        $configs_organizadas['Valores'][] = $config;
    } else {
        $configs_organizadas['Regras'][] = $config;
    }
}
?>

<div class="page-header">
    <h2>⚙️ Configurações de Agendamento</h2>
    <p>Configure as regras e parâmetros do sistema de agendamento</p>
</div>

<?php if($mensagem): ?>
<div class="alert alert-info"><?php echo $mensagem; ?></div>
<?php endif; ?>

<form method="POST" class="config-form">
    <?php foreach($configs_organizadas as $categoria => $configs): ?>
    <div class="config-category">
        <h3><?php echo $categoria; ?></h3>
        <div class="config-grid">
            <?php foreach($configs as $config): ?>
            <div class="config-item">
                <label for="config_<?php echo $config['chave']; ?>">
                    <?php echo $config['descricao']; ?>
                </label>
                
                <?php if(in_array($config['chave'], ['horario_inicio', 'horario_fim', 'horario_almoco_inicio', 'horario_almoco_fim'])): ?>
                <input type="time" 
                       id="config_<?php echo $config['chave']; ?>" 
                       name="config[<?php echo $config['chave']; ?>]" 
                       class="form-control" 
                       value="<?php echo $config['valor']; ?>" 
                       required>
                
                <?php elseif(strpos($config['chave'], 'valor_') === 0): ?>
                <div class="input-group">
                    <input type="number" 
                           id="config_<?php echo $config['chave']; ?>" 
                           name="config[<?php echo $config['chave']; ?>]" 
                           class="form-control" 
                           value="<?php echo $config['valor']; ?>" 
                           min="0" 
                           max="200" 
                           step="1">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <small class="form-text">Acréscimo sobre o valor base</small>
                
                <?php elseif($config['chave'] == 'bloquear_feriados'): ?>
                <select id="config_<?php echo $config['chave']; ?>" 
                        name="config[<?php echo $config['chave']; ?>]" 
                        class="form-control">
                    <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Não</option>
                </select>
                
                <?php else: ?>
                <input type="number" 
                       id="config_<?php echo $config['chave']; ?>" 
                       name="config[<?php echo $config['chave']; ?>]" 
                       class="form-control" 
                       value="<?php echo $config['valor']; ?>" 
                       min="1" 
                       required>
                
                <?php if($config['chave'] == 'intervalo_agendamento'): ?>
                <small class="form-text">minutos</small>
                <?php elseif($config['chave'] == 'tempo_deslocamento'): ?>
                <small class="form-text">minutos entre serviços</small>
                <?php elseif($config['chave'] == 'dias_antecipacao'): ?>
                <small class="form-text">dias no futuro</small>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
        <button type="reset" class="btn btn-secondary">Restaurar Valores</button>
    </div>
</form>

<!-- Tabela de Feriados -->
<div class="config-category">
    <h3>📅 Feriados Configurados</h3>
    
    <?php
    // Criar tabela de feriados se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS feriados (
            id INT PRIMARY KEY AUTO_INCREMENT,
            data_feriado DATE NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            recorrente BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Inserir feriados padrão do Brasil
    $ano_atual = date('Y');
    $feriados_padrao = [
        ["{$ano_atual}-01-01", "Ano Novo", 1],
        ["{$ano_atual}-04-21", "Tiradentes", 1],
        ["{$ano_atual}-05-01", "Dia do Trabalho", 1],
        ["{$ano_atual}-09-07", "Independência do Brasil", 1],
        ["{$ano_atual}-10-12", "Nossa Senhora Aparecida", 1],
        ["{$ano_atual}-11-02", "Finados", 1],
        ["{$ano_atual}-11-15", "Proclamação da República", 1],
        ["{$ano_atual}-12-25", "Natal", 1]
    ];
    
    foreach($feriados_padrao as $feriado) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO feriados (data_feriado, descricao, recorrente) VALUES (?, ?, ?)");
        $stmt->execute($feriado);
    }
    
    // Processar novo feriado
    if(isset($_POST['adicionar_feriado'])) {
        $data_feriado = $_POST['data_feriado'];
        $descricao = sanitize($_POST['descricao_feriado']);
        $recorrente = isset($_POST['recorrente']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO feriados (data_feriado, descricao, recorrente) VALUES (?, ?, ?)");
            $stmt->execute([$data_feriado, $descricao, $recorrente]);
            $mensagem = "Feriado adicionado com sucesso!";
        } catch(PDOException $e) {
            $mensagem = "Erro ao adicionar feriado: " . $e->getMessage();
        }
    }
    
    // Excluir feriado
    if(isset($_GET['excluir_feriado'])) {
        $feriado_id = $_GET['excluir_feriado'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM feriados WHERE id = ?");
            $stmt->execute([$feriado_id]);
            $mensagem = "Feriado excluído com sucesso!";
        } catch(PDOException $e) {
            $mensagem = "Erro ao excluir feriado: " . $e->getMessage();
        }
    }
    
    // Buscar feriados
    $stmt = $pdo->prepare("SELECT * FROM feriados ORDER BY data_feriado");
    $stmt->execute();
    $feriados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="card">
        <div class="card-header">
            <h4>Adicionar Novo Feriado</h4>
        </div>
        <div class="card-body">
            <form method="POST" class="form-inline">
                <div class="form-group mr-2">
                    <label for="data_feriado" class="mr-2">Data:</label>
                    <input type="date" id="data_feriado" name="data_feriado" class="form-control" required>
                </div>
                <div class="form-group mr-2">
                    <label for="descricao_feriado" class="mr-2">Descrição:</label>
                    <input type="text" id="descricao_feriado" name="descricao_feriado" class="form-control" required>
                </div>
                <div class="form-group mr-2">
                    <label class="checkbox-label">
                        <input type="checkbox" name="recorrente" checked>
                        <span class="checkmark"></span>
                        Feriado Recorrente
                    </label>
                </div>
                <button type="submit" name="adicionar_feriado" class="btn btn-primary">Adicionar Feriado</button>
            </form>
        </div>
    </div>
    
    <div class="table-responsive mt-3">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Recorrente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($feriados as $feriado): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($feriado['data_feriado'])); ?></td>
                    <td><?php echo $feriado['descricao']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $feriado['recorrente'] ? 'ativo' : 'inativo'; ?>">
                            <?php echo $feriado['recorrente'] ? 'Sim' : 'Não'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="?excluir_feriado=<?php echo $feriado['id']; ?>" class="btn-sm btn-danger" 
                           onclick="return confirm('Tem certeza que deseja excluir este feriado?')">
                            Excluir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.form-inline {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.form-inline .form-group {
    margin-bottom: 10px;
}

.mr-2 {
    margin-right: 10px;
}
</style>

<?php include 'includes/footer-admin.php'; ?>