<?php
// admin/dashboard.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

// Estatísticas para o dashboard
try {
    // Total de orçamentos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos");
    $stmt->execute();
    $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de agendamentos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos");
    $stmt->execute();
    $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de clientes
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes");
    $stmt->execute();
    $total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de produtos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1");
    $stmt->execute();
    $total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Orçamentos do mês
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos WHERE MONTH(data_solicitacao) = MONTH(CURDATE()) AND YEAR(data_solicitacao) = YEAR(CURDATE())");
    $stmt->execute();
    $orcamentos_mes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Agendamentos de hoje
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE() AND status IN ('agendado', 'confirmado')");
    $stmt->execute();
    $agendamentos_hoje = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Orçamentos recentes
    $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.telefone, s.nome as servico_nome 
                          FROM orcamentos o 
                          LEFT JOIN clientes c ON o.cliente_id = c.id 
                          LEFT JOIN servicos s ON o.servico_id = s.id 
                          ORDER BY o.data_solicitacao DESC 
                          LIMIT 8");
    $stmt->execute();
    $orcamentos_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agendamentos recentes
    $stmt = $pdo->prepare("SELECT a.*, c.nome as cliente_nome, c.telefone, s.nome as servico_nome 
                          FROM agendamentos a 
                          LEFT JOIN clientes c ON a.cliente_id = c.id 
                          LEFT JOIN servicos s ON a.servico_id = s.id 
                          ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC 
                          LIMIT 8");
    $stmt->execute();
    $agendamentos_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agendamentos de hoje para a timeline
    $stmt = $pdo->prepare("SELECT a.*, c.nome as cliente_nome, s.nome as servico_nome 
                          FROM agendamentos a 
                          LEFT JOIN clientes c ON a.cliente_id = c.id 
                          LEFT JOIN servicos s ON a.servico_id = s.id 
                          WHERE a.data_agendamento = CURDATE() 
                          AND a.status IN ('agendado', 'confirmado')
                          ORDER BY a.hora_agendamento ASC 
                          LIMIT 6");
    $stmt->execute();
    $agendamentos_hoje_lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Erro ao carregar estatísticas: " . $e->getMessage());
}
?>

<div class="page-header">
    <div class="page-title">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </div>
    <div class="alert alert-info alert-modern">
        <i class="fas fa-info-circle me-2"></i>
        Bem-vindo ao painel administrativo do ClimaTech - <?php echo date('d/m/Y'); ?>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-file-invoice-dollar"></i>
            <div class="number"><?php echo $total_orcamentos; ?></div>
            <div>Total Orçamentos</div>
            <small class="stats-subtitle"><?php echo $orcamentos_mes; ?> este mês</small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-calendar-check"></i>
            <div class="number"><?php echo $total_agendamentos; ?></div>
            <div>Total Agendamentos</div>
            <small class="stats-subtitle"><?php echo $agendamentos_hoje; ?> hoje</small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-users"></i>
            <div class="number"><?php echo $total_clientes; ?></div>
            <div>Clientes</div>
            <small class="stats-subtitle">Cadastrados no sistema</small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-snowflake"></i>
            <div class="number"><?php echo $total_produtos; ?></div>
            <div>Produtos</div>
            <small class="stats-subtitle">Disponíveis</small>
        </div>
    </div>
</div>

<div class="row">
    <!-- Coluna Esquerda -->
    <div class="col-md-8">
        <!-- Orçamentos Recentes -->
        <div class="modern-card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Orçamentos Recentes
                </h5>
                <a href="orcamentos.php" class="btn btn-outline-primary btn-modern btn-sm">
                    <i class="fas fa-list me-1"></i>Ver Todos
                </a>
            </div>
            <div class="card-body">
                <?php if(count($orcamentos_recentes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th width="80">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orcamentos_recentes as $orcamento): 
                                $data_obj = new DateTime($orcamento['data_solicitacao']);
                                $hoje = new DateTime();
                            ?>
                            <tr class="<?php echo $orcamento['data_solicitacao'] == $hoje->format('Y-m-d') ? 'today-row' : ''; ?>">
                                <td>
                                    <strong class="text-primary">#<?php echo $orcamento['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="client-info">
                                        <strong><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></strong>
                                        <?php if($orcamento['telefone']): ?>
                                        <br><small class="text-muted"><?php echo $orcamento['telefone']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="service-badge"><?php echo $orcamento['servico_nome']; ?></span>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?>
                                    <?php if($orcamento['data_solicitacao'] == $hoje->format('Y-m-d')): ?>
                                    <span class="badge badge-modern badge-success ms-1">Hoje</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        R$ <?php echo number_format($orcamento['valor_total'] ?? 0, 2, ',', '.'); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                                        <?php echo ucfirst($orcamento['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="orcamentos.php?acao=editar&id=<?php echo $orcamento['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum orçamento encontrado</p>
                    <a href="orcamentos.php?acao=novo" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-plus me-2"></i>Criar Primeiro Orçamento
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Agendamentos Recentes -->
        <div class="modern-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-calendar-alt me-2"></i>Agendamentos Recentes
                </h5>
                <a href="agendamentos.php" class="btn btn-outline-primary btn-modern btn-sm">
                    <i class="fas fa-list me-1"></i>Ver Todos
                </a>
            </div>
            <div class="card-body">
                <?php if(count($agendamentos_recentes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Data/Hora</th>
                                <th>Status</th>
                                <th width="80">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($agendamentos_recentes as $agendamento): 
                                $data_obj = new DateTime($agendamento['data_agendamento']);
                                $hoje = new DateTime();
                                $amanha = new DateTime('+1 day');
                            ?>
                            <tr class="<?php echo $agendamento['data_agendamento'] == $hoje->format('Y-m-d') ? 'today-row' : ''; ?>">
                                <td>
                                    <strong class="text-primary">#<?php echo $agendamento['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="client-info">
                                        <strong><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></strong>
                                        <?php if($agendamento['telefone']): ?>
                                        <br><small class="text-muted"><?php echo $agendamento['telefone']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="service-badge"><?php echo $agendamento['servico_nome']; ?></span>
                                </td>
                                <td>
                                    <div class="datetime-info">
                                        <?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?>
                                        <br>
                                        <small class="time-badge"><?php echo $agendamento['hora_agendamento']; ?></small>
                                        <?php if($agendamento['data_agendamento'] == $hoje->format('Y-m-d')): ?>
                                        <span class="badge badge-modern badge-success ms-1">Hoje</span>
                                        <?php elseif($agendamento['data_agendamento'] == $amanha->format('Y-m-d')): ?>
                                        <span class="badge badge-modern badge-warning ms-1">Amanhã</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                        <?php echo ucfirst($agendamento['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="agendamentos.php?acao=editar&id=<?php echo $agendamento['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum agendamento encontrado</p>
                    <a href="agendamentos.php?acao=novo" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-plus me-2"></i>Criar Primeiro Agendamento
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna Direita -->
    <div class="col-md-4">
        <!-- Agenda de Hoje -->
        <div class="modern-card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-clock me-2"></i>Agenda de Hoje
                </h5>
                <span class="badge badge-modern badge-primary"><?php echo count($agendamentos_hoje_lista); ?> agendamentos</span>
            </div>
            <div class="card-body">
                <?php if(count($agendamentos_hoje_lista) > 0): ?>
                <div class="timeline">
                    <?php foreach($agendamentos_hoje_lista as $index => $agendamento): ?>
                    <div class="timeline-item <?php echo $index === 0 ? 'current' : ''; ?>">
                        <div class="timeline-time">
                            <?php echo $agendamento['hora_agendamento']; ?>
                        </div>
                        <div class="timeline-content">
                            <strong><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></strong>
                            <p class="mb-1"><?php echo $agendamento['servico_nome']; ?></p>
                            <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                <?php echo ucfirst($agendamento['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-3">
                    <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Nenhum agendamento para hoje</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="modern-card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="orcamentos.php?acao=novo" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-plus me-2"></i>Novo Orçamento
                    </a>
                    <a href="agendamentos.php?acao=novo" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-calendar-plus me-2"></i>Novo Agendamento
                    </a>
                    <a href="clientes.php?acao=novo" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-user-plus me-2"></i>Novo Cliente
                    </a>
                    <a href="servicos.php?acao=novo" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-tools me-2"></i>Novo Serviço
                    </a>
                </div>
            </div>
        </div>

        <!-- Status do Sistema -->
        <div class="modern-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2"></i>Status do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="system-status">
                    <div class="status-item">
                        <i class="fas fa-database text-success"></i>
                        <span>Banco de Dados</span>
                        <span class="status-indicator online"></span>
                    </div>
                    <div class="status-item">
                        <i class="fas fa-server text-info"></i>
                        <span>Servidor Web</span>
                        <span class="status-indicator online"></span>
                    </div>
                    <div class="status-item">
                        <i class="fas fa-envelope text-warning"></i>
                        <span>E-mail</span>
                        <span class="status-indicator online"></span>
                    </div>
                    <div class="status-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Segurança</span>
                        <span class="status-indicator online"></span>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Última atualização: <?php echo date('H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos para dashboard -->
<style>
.stats-card {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 1.5rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
}

.stats-card:hover::before {
    transform: rotate(45deg) translate(50%, 50%);
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
    margin-bottom: 0.25rem;
    line-height: 1;
}

.stats-card div:last-of-type {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.stats-subtitle {
    font-size: 0.8rem;
    opacity: 0.8;
    display: block;
}

.timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
}

.timeline-item.current {
    background: rgba(37, 99, 235, 0.05);
    border-left-color: #10b981;
}

.timeline-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.timeline-time {
    background: var(--primary-color);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    min-width: 70px;
    text-align: center;
}

.timeline-content {
    flex: 1;
}

.timeline-content strong {
    display: block;
    margin-bottom: 0.25rem;
    color: var(--dark-color);
}

.timeline-content p {
    font-size: 0.875rem;
    color: var(--gray);
    margin-bottom: 0.5rem;
}

.system-status {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
}

.status-item i {
    width: 20px;
    text-align: center;
}

.status-item span:first-of-type {
    flex: 1;
    font-weight: 500;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #10b981;
}

.status-indicator.offline {
    background: #ef4444;
}

.service-badge {
    background: #e2e8f0;
    color: #4a5568;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.time-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-block;
}

.status-pendente { background: #fbbf24; color: #78350f; }
.status-aprovado { background: #10b981; color: white; }
-status-recusado { background: #ef4444; color: white; }
.status-agendado { background: #3b82f6; color: white; }
.status-confirmado { background: #10b981; color: white; }
-status-realizado { background: #8b5cf6; color: white; }
-status-cancelado { background: #6b7280; color: white; }

.today-row {
    background: rgba(37, 99, 235, 0.05) !important;
    border-left: 4px solid var(--primary-color);
}

.datetime-info {
    line-height: 1.4;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.action-buttons .btn {
    padding: 6px 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}

/* Responsivo */
@media (max-width: 768px) {
    .stats-card .number {
        font-size: 1.5rem;
    }
    
    .timeline-item {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .timeline-time {
        align-self: flex-start;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer-admin.php'; ?>