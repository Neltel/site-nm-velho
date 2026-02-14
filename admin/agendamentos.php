<?php
// admin/agendamentos.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = '';
$erro = '';

// Processar atualização de agendamento
if($_POST && $acao == 'editar' && $id > 0) {
    $data_agendamento = sanitize($_POST['data_agendamento']);
    $hora_agendamento = sanitize($_POST['hora_agendamento']);
    $status = sanitize($_POST['status']);
    $observacoes = sanitize($_POST['observacoes']);

    try {
        // Verificar conflito de horário (exceto para o próprio agendamento)
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos 
                              WHERE data_agendamento = ? AND hora_agendamento = ? 
                              AND status IN ('agendado', 'confirmado') AND id != ?");
        $stmt->execute([$data_agendamento, $hora_agendamento, $id]);
        $conflitos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if($conflitos > 0) {
            $erro = "Já existe um agendamento para esta data e horário!";
        } else {
            $stmt = $pdo->prepare("UPDATE agendamentos SET data_agendamento = ?, hora_agendamento = ?, status = ?, observacoes = ? WHERE id = ?");
            $stmt->execute([$data_agendamento, $hora_agendamento, $status, $observacoes, $id]);
            $sucesso = "Agendamento atualizado com sucesso!";
        }
    } catch(PDOException $e) {
        $erro = "Erro: " . $e->getMessage();
    }
}

// Excluir agendamento
if($acao == 'excluir' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
        $stmt->execute([$id]);
        $sucesso = "Agendamento excluído com sucesso!";
        $acao = 'listar';
    } catch(PDOException $e) {
        $erro = "Erro ao excluir: " . $e->getMessage();
    }
}

// Buscar estatísticas para os cards
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos");
    $stmt->execute();
    $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE() AND status IN ('agendado', 'confirmado')");
    $stmt->execute();
    $agendamentos_hoje = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE status = 'confirmado'");
    $stmt->execute();
    $confirmados = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE status = 'pendente'");
    $stmt->execute();
    $pendentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch(PDOException $e) {
    // Tabela pode não existir ainda
}

// Listar agendamentos
if($acao == 'listar') {
    $status_filter = $_GET['status'] ?? '';
    $data_filter = $_GET['data'] ?? '';
    $pagina = $_GET['pagina'] ?? 1;
    $por_pagina = 15;
    $offset = ($pagina - 1) * $por_pagina;
    
    // Construir query com filtros
    $sql = "SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone, s.nome as servico_nome 
            FROM agendamentos a 
            LEFT JOIN clientes c ON a.cliente_id = c.id 
            LEFT JOIN servicos s ON a.servico_id = s.id 
            WHERE 1=1";
    $params = [];
    
    if($status_filter) {
        $sql .= " AND a.status = ?";
        $params[] = $status_filter;
    }
    
    if($data_filter) {
        $sql .= " AND a.data_agendamento = ?";
        $params[] = $data_filter;
    }
    
    $sql .= " ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC LIMIT $por_pagina OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total para paginação
    $sql_count = "SELECT COUNT(*) as total FROM agendamentos a WHERE 1=1";
    if($status_filter) {
        $sql_count .= " AND a.status = ?";
    }
    if($data_filter) {
        $sql_count .= " AND a.data_agendamento = ?";
    }
    
    $stmt = $pdo->prepare($sql_count);
    $stmt->execute($params);
    $total_filtrado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_filtrado / $por_pagina);
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-calendar-alt me-2"></i>
            Gerenciar Agendamentos
        </div>
        <div class="alert alert-warning alert-modern">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Gerencie todos os agendamentos do sistema
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

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-calendar-check"></i>
                <div class="number"><?php echo $total_agendamentos ?? 0; ?></div>
                <div>Total Agendamentos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-sun"></i>
                <div class="number"><?php echo $agendamentos_hoje ?? 0; ?></div>
                <div>Agendamentos Hoje</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-check-circle"></i>
                <div class="number"><?php echo $confirmados ?? 0; ?></div>
                <div>Confirmados</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-clock"></i>
                <div class="number"><?php echo $pendentes ?? 0; ?></div>
                <div>Pendentes</div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Agendamentos
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <span class="total-info">
                        Mostrando <?php echo count($agendamentos); ?> de <?php echo $total_filtrado; ?> agendamentos
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <div class="filters-section mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="statusFilter" class="form-control form-control-modern">
                            <option value="">Todos os status</option>
                            <option value="agendado" <?php echo $status_filter == 'agendado' ? 'selected' : ''; ?>>Agendados</option>
                            <option value="confirmado" <?php echo $status_filter == 'confirmado' ? 'selected' : ''; ?>>Confirmados</option>
                            <option value="realizado" <?php echo $status_filter == 'realizado' ? 'selected' : ''; ?>>Realizados</option>
                            <option value="cancelado" <?php echo $status_filter == 'cancelado' ? 'selected' : ''; ?>>Cancelados</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Data</label>
                        <input type="date" id="dataFilter" class="form-control form-control-modern" value="<?php echo $data_filter; ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" id="searchAgendamentos" placeholder="Buscar por cliente, serviço..." class="form-control form-control-modern">
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" onclick="filtrarAgendamentos()" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                    <button type="button" onclick="limparFiltros()" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-modern" id="tabelaAgendamentos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Status</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agendamentos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum agendamento encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($agendamentos as $agendamento): 
                            $data_obj = new DateTime($agendamento['data_agendamento']);
                            $hoje = new DateTime();
                            $amanha = new DateTime('+1 day');
                            ?>
                            <tr class="<?php echo $agendamento['data_agendamento'] == $hoje->format('Y-m-d') ? 'today-row' : ''; ?>">
                                <td>
                                    <strong class="text-primary">#<?php echo $agendamento['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="client-info">
                                            <strong><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></strong>
                                            <?php if($agendamento['cliente_telefone']): ?>
                                            <br><small class="text-muted"><?php echo $agendamento['cliente_telefone']; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="service-badge"><?php echo $agendamento['servico_nome']; ?></span>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?>
                                        <?php if($agendamento['data_agendamento'] == $hoje->format('Y-m-d')): ?>
                                        <span class="badge badge-modern badge-success ms-2">Hoje</span>
                                        <?php elseif($agendamento['data_agendamento'] == $amanha->format('Y-m-d')): ?>
                                        <span class="badge badge-modern badge-warning ms-2">Amanhã</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="time-badge"><?php echo $agendamento['hora_agendamento']; ?></span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                        <?php echo ucfirst($agendamento['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?acao=editar&id=<?php echo $agendamento['id']; ?>" class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=visualizar&id=<?php echo $agendamento['id']; ?>" class="btn btn-sm btn-outline-info btn-modern" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?acao=excluir&id=<?php echo $agendamento['id']; ?>" class="btn btn-sm btn-outline-danger btn-modern" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if($total_paginas > 1): ?>
            <div class="pagination-modern mt-4">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $data_filter ? '&data=' . $data_filter : ''; ?>" 
                   class="pagination-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function filtrarAgendamentos() {
        const status = document.getElementById('statusFilter').value;
        const data = document.getElementById('dataFilter').value;
        const url = new URL(window.location.href);
        
        if(status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        if(data) {
            url.searchParams.set('data', data);
        } else {
            url.searchParams.delete('data');
        }
        
        url.searchParams.delete('pagina');
        window.location.href = url.toString();
    }
    
    function limparFiltros() {
        window.location.href = 'agendamentos.php';
    }
    
    // Busca em tempo real
    document.getElementById('searchAgendamentos').addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaAgendamentos tbody tr');
        
        linhas.forEach(linha => {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
    
    // Definir data de hoje como padrão no filtro de data se não houver data selecionada
    document.addEventListener('DOMContentLoaded', function() {
        const dataFilter = document.getElementById('dataFilter');
        if(!dataFilter.value) {
            const hoje = new Date().toISOString().split('T')[0];
            dataFilter.value = hoje;
        }
    });
    </script>

    <?php
} elseif(($acao == 'editar' || $acao == 'visualizar') && $id > 0) {
    
    $agendamento = [];
    $stmt = $pdo->prepare("SELECT a.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone, 
                                  s.nome as servico_nome
                          FROM agendamentos a 
                          LEFT JOIN clientes c ON a.cliente_id = c.id 
                          LEFT JOIN servicos s ON a.servico_id = s.id 
                          WHERE a.id = ?");
    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$agendamento) {
        echo "<div class='alert alert-danger alert-modern'>Agendamento não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-<?php echo $acao == 'editar' ? 'edit' : 'eye'; ?> me-2"></i>
            <?php echo $acao == 'editar' ? 'Editar Agendamento' : 'Detalhes do Agendamento'; ?>
        </div>
        <div class="header-actions">
            <?php if($acao == 'visualizar'): ?>
            <a href="?acao=editar&id=<?php echo $agendamento['id']; ?>" class="btn btn-primary-modern btn-modern">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <?php endif; ?>
            <a href="agendamentos.php" class="btn btn-outline-primary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
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

    <div class="modern-card">
        <div class="card-body">
            <div class="row">
                <!-- Informações do Cliente -->
                <div class="col-md-6">
                    <div class="config-section">
                        <h5 class="section-title">
                            <i class="fas fa-user me-2"></i>Informações do Cliente
                        </h5>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Nome:</label>
                                <span class="info-value"><?php echo $agendamento['cliente_nome']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Telefone:</label>
                                <span class="info-value"><?php echo $agendamento['cliente_telefone']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>E-mail:</label>
                                <span class="info-value"><?php echo $agendamento['cliente_email']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalhes do Agendamento -->
                <div class="col-md-6">
                    <div class="config-section">
                        <h5 class="section-title">
                            <i class="fas fa-calendar-alt me-2"></i>Detalhes do Agendamento
                        </h5>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Serviço:</label>
                                <span class="info-value"><?php echo $agendamento['servico_nome']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Data:</label>
                                <span class="info-value"><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Hora:</label>
                                <span class="info-value"><?php echo $agendamento['hora_agendamento']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Status:</label>
                                <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                    <?php echo ucfirst($agendamento['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($agendamento['observacoes']): ?>
            <div class="config-section mt-4">
                <h5 class="section-title">
                    <i class="fas fa-sticky-note me-2"></i>Observações
                </h5>
                <div class="observacao-box">
                    <?php echo nl2br(htmlspecialchars($agendamento['observacoes'])); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if($acao == 'editar'): ?>
            <div class="config-section mt-4">
                <h5 class="section-title">
                    <i class="fas fa-edit me-2"></i>Editar Agendamento
                </h5>
                
                <form method="POST" class="config-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Data *</label>
                                <input type="date" name="data_agendamento" class="form-control form-control-modern" 
                                       value="<?php echo $agendamento['data_agendamento']; ?>" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora *</label>
                                <select name="hora_agendamento" class="form-control form-control-modern" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                    foreach($horarios as $horario):
                                    ?>
                                    <option value="<?php echo $horario; ?>" <?php echo $agendamento['hora_agendamento'] == $horario ? 'selected' : ''; ?>>
                                        <?php echo $horario; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control form-control-modern" required>
                            <option value="agendado" <?php echo $agendamento['status'] == 'agendado' ? 'selected' : ''; ?>>Agendado</option>
                            <option value="confirmado" <?php echo $agendamento['status'] == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="realizado" <?php echo $agendamento['status'] == 'realizado' ? 'selected' : ''; ?>>Realizado</option>
                            <option value="cancelado" <?php echo $agendamento['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control form-control-modern" rows="4"><?php echo htmlspecialchars($agendamento['observacoes']); ?></textarea>
                    </div>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Atualizar Agendamento
                        </button>
                        <a href="agendamentos.php" class="btn btn-outline-primary btn-modern">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
}

// Estilos específicos para agendamentos
?>
<style>
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

.filters-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
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
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.875rem;
}

.status-agendado { background: #fbbf24; color: #78350f; }
.status-confirmado { background: #10b981; color: white; }
.status-realizado { background: #3b82f6; color: white; }
.status-cancelado { background: #ef4444; color: white; }

.today-row {
    background: rgba(37, 99, 235, 0.05) !important;
    border-left: 4px solid var(--primary-color);
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-item label {
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
}

.info-value {
    color: var(--gray);
    text-align: right;
}

.observacao-box {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.5rem;
    line-height: 1.6;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.action-buttons .btn {
    padding: 6px 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}

.pagination-modern {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.pagination-item {
    padding: 8px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: var(--gray);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-item:hover,
.pagination-item.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

/* Responsivo */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .info-value {
        text-align: left;
    }
}
</style>

<?php
include 'includes/footer-admin.php';
?>