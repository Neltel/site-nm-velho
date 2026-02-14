<?php
// admin/orcamentos_unificados.php - Painel unificado de orçamentos e agendamentos
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = '';
$erro = '';

// Ações
if($acao == 'excluir' && $id > 0) {
    try {
        $pdo->beginTransaction();
        
        // Excluir serviços múltiplos
        $stmt = $pdo->prepare("DELETE FROM orcamento_servicos_multiplos WHERE orcamento_id = ?");
        $stmt->execute([$id]);
        
        // Excluir agendamento vinculado
        $stmt = $pdo->prepare("SELECT agendamento_id FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($orcamento && $orcamento['agendamento_id']) {
            $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
            $stmt->execute([$orcamento['agendamento_id']]);
        }
        
        // Excluir orçamento
        $stmt = $pdo->prepare("DELETE FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        $sucesso = "Orçamento e agendamento excluídos com sucesso!";
        $acao = 'listar';
    } catch(PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao excluir: " . $e->getMessage();
    }
}

// Listar orçamentos unificados
if($acao == 'listar') {
    $status_filter = $_GET['status'] ?? '';
    $data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
    $data_fim = $_GET['data_fim'] ?? date('Y-m-t');
    $pagina = $_GET['pagina'] ?? 1;
    $por_pagina = 15;
    $offset = ($pagina - 1) * $por_pagina;
    
    // Query com filtros
    $sql = "SELECT o.*, 
                   c.nome as cliente_nome, 
                   c.telefone as cliente_telefone,
                   a.status as agendamento_status,
                   a.data_agendamento,
                   a.hora_agendamento,
                   a.hora_fim
            FROM orcamentos o 
            LEFT JOIN clientes c ON o.cliente_id = c.id 
            LEFT JOIN agendamentos a ON o.agendamento_id = a.id 
            WHERE o.is_agendamento_unificado = 1";
    
    $params = [];
    
    if($status_filter) {
        $sql .= " AND o.status = ?";
        $params[] = $status_filter;
    }
    
    if($data_inicio && $data_fim) {
        $sql .= " AND o.data_inicio_estimada BETWEEN ? AND ?";
        $params[] = $data_inicio;
        $params[] = $data_fim;
    }
    
    $sql .= " ORDER BY o.data_inicio_estimada DESC LIMIT $por_pagina OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total para paginação
    $sql_count = "SELECT COUNT(*) as total FROM orcamentos o WHERE o.is_agendamento_unificado = 1";
    if($status_filter) {
        $sql_count .= " AND o.status = ?";
    }
    if($data_inicio && $data_fim) {
        $sql_count .= " AND o.data_inicio_estimada BETWEEN ? AND ?";
    }
    
    $stmt = $pdo->prepare($sql_count);
    $stmt->execute($params);
    $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_orcamentos / $por_pagina);
    
    // Estatísticas
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
            SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
            SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos,
            SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
            COALESCE(SUM(valor_total), 0) as valor_total
        FROM orcamentos 
        WHERE is_agendamento_unificado = 1
        AND data_inicio_estimada BETWEEN ? AND ?
    ");
    $stmt->execute([$data_inicio, $data_fim]);
    $estatisticas = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-calendar-check me-2"></i>
            Orçamentos e Agendamentos Unificados
        </div>
        <div class="alert alert-info alert-modern">
            <i class="fas fa-info-circle me-2"></i>
            Gerencie orçamentos com agendamentos integrados em um único lugar
        </div>
    </div>
    
    <!-- Alertas -->
    <?php if($sucesso): ?>
    <div class="alert alert-success alert-modern">
        <i class="fas fa-check-circle me-2"></i> <?php echo $sucesso; ?>
    </div>
    <?php endif; ?>
    
    <?php if($erro): ?>
    <div class="alert alert-danger alert-modern">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $erro; ?>
    </div>
    <?php endif; ?>
    
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <div class="number"><?php echo $estatisticas['total'] ?? 0; ?></div>
                <div>Total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-warning">
                <i class="fas fa-clock"></i>
                <div class="number"><?php echo $estatisticas['pendentes'] ?? 0; ?></div>
                <div>Pendentes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-success">
                <i class="fas fa-check-circle"></i>
                <div class="number"><?php echo $estatisticas['aprovados'] ?? 0; ?></div>
                <div>Aprovados</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-dollar-sign"></i>
                <div class="number">R$ <?php echo number_format($estatisticas['valor_total'] ?? 0, 2, ',', '.'); ?></div>
                <div>Valor Total</div>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-filter me-2"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="acao" value="listar">
                
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control form-control-modern">
                        <option value="">Todos</option>
                        <option value="pendente" <?php echo $status_filter == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="aprovado" <?php echo $status_filter == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="concluido" <?php echo $status_filter == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                        <option value="cancelado" <?php echo $status_filter == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control form-control-modern" 
                           value="<?php echo $data_inicio; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control form-control-modern" 
                           value="<?php echo $data_fim; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                        <i class="fas fa-filter me-2"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Calendário de Visão Geral -->
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Calendário de Agendamentos</h5>
        </div>
        <div class="card-body">
            <div id="calendarioAgendamentos"></div>
        </div>
    </div>
    
    <!-- Lista -->
    <div class="modern-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i> Lista de Orçamentos
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <span class="total-info">Mostrando <?php echo count($orcamentos); ?> de <?php echo $total_orcamentos; ?></span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Serviços</th>
                            <th>Data/Hora</th>
                            <th>Duração</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orcamentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum orçamento com agendamento encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($orcamentos as $orcamento): 
                                // Buscar serviços deste orçamento
                                $stmt = $pdo->prepare("
                                    SELECT sm.*, s.nome as servico_nome 
                                    FROM orcamento_servicos_multiplos sm
                                    JOIN servicos s ON sm.servico_id = s.id
                                    WHERE sm.orcamento_id = ?
                                ");
                                $stmt->execute([$orcamento['id']]);
                                $servicos_orcamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                $nomes_servicos = array_column($servicos_orcamento, 'servico_nome');
                                $total_servicos = array_sum(array_column($servicos_orcamento, 'quantidade'));
                            ?>
                            <tr>
                                <td><strong class="text-primary">#<?php echo $orcamento['id']; ?></strong></td>
                                <td>
                                    <div>
                                        <strong><?php echo $orcamento['cliente_nome']; ?></strong>
                                        <br><small class="text-muted"><?php echo $orcamento['cliente_telefone']; ?></small>
                                    </div>
                                </td>
                                <td>
                                    <small><?php echo implode(', ', $nomes_servicos); ?></small>
                                    <br><span class="badge bg-secondary"><?php echo $total_servicos; ?> serviços</span>
                                </td>
                                <td>
                                    <?php if($orcamento['data_agendamento']): ?>
                                        <div>
                                            <strong><?php echo date('d/m/Y', strtotime($orcamento['data_agendamento'])); ?></strong>
                                            <br>
                                            <span class="time-badge"><?php echo $orcamento['hora_agendamento']; ?> - <?php echo $orcamento['hora_fim']; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Não agendado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($orcamento['duracao_total_estimada_min']): ?>
                                        <?php 
                                        $horas = floor($orcamento['duracao_total_estimada_min'] / 60);
                                        $minutos = $orcamento['duracao_total_estimada_min'] % 60;
                                        echo $horas > 0 ? $horas . 'h' : '';
                                        echo $minutos > 0 ? $minutos . 'min' : '';
                                        ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-success"><?php echo formatarMoeda($orcamento['valor_total']); ?></strong>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                                        <?php echo ucfirst($orcamento['status']); ?>
                                    </span>
                                    <?php if($orcamento['agendamento_status']): ?>
                                    <br>
                                    <small class="text-muted">Agendamento: <?php echo $orcamento['agendamento_status']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?acao=visualizar&id=<?php echo $orcamento['id']; ?>" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?acao=editar&id=<?php echo $orcamento['id']; ?>" class="btn btn-sm btn-outline-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=enviar_whatsapp&id=<?php echo $orcamento['id']; ?>" class="btn btn-sm btn-outline-success" title="WhatsApp" target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <a href="?acao=excluir&id=<?php echo $orcamento['id']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Excluir orçamento #<?php echo $orcamento['id']; ?>?')">
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
            
            <?php if($total_paginas > 1): ?>
            <div class="pagination-modern mt-4">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>" 
                   class="pagination-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    .time-badge {
        background: var(--primary-color);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    #calendarioAgendamentos {
        min-height: 400px;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar calendário
        inicializarCalendario();
    });
    
    function inicializarCalendario() {
        const calendario = document.getElementById('calendarioAgendamentos');
        
        // Em produção, usar uma biblioteca como FullCalendar
        // Aqui um exemplo simplificado
        fetch('api/agendamentos_calendario.php')
            .then(response => response.json())
            .then(agendamentos => {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                // Lógica do calendário aqui
                // Por simplicidade, mostramos uma mensagem
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">Calendário com todos os agendamentos do mês</small>
                    </div>
                `;
                
                calendario.innerHTML = html;
            })
            .catch(error => {
                calendario.innerHTML = '<div class="alert alert-info">Calendário disponível com biblioteca FullCalendar</div>';
            });
    }
    </script>
    
    <?php
    include 'includes/footer-admin.php';
} elseif($acao == 'visualizar' && $id > 0) {
    // Visualização detalhada
    $stmt = $pdo->prepare("
        SELECT o.*, 
               c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone,
               a.*
        FROM orcamentos o
        LEFT JOIN clientes c ON o.cliente_id = c.id
        LEFT JOIN agendamentos a ON o.agendamento_id = a.id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$orcamento) {
        echo "<div class='alert alert-danger'>Orçamento não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    // Buscar serviços múltiplos
    $stmt = $pdo->prepare("
        SELECT sm.*, s.nome as servico_nome, s.preco_base, s.duracao_media_min, s.duracao_media_max
        FROM orcamento_servicos_multiplos sm
        JOIN servicos s ON sm.servico_id = s.id
        WHERE sm.orcamento_id = ?
    ");
    $stmt->execute([$id]);
    $servicos_orcamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totais
    $total_servicos = 0;
    $total_duracao = 0;
    $total_valor = 0;
    
    foreach($servicos_orcamento as $servico) {
        $total_servicos += $servico['quantidade'];
        $total_duracao += $servico['duracao_media_min'] * $servico['quantidade'];
        $total_valor += $servico['preco_unitario'] * $servico['quantidade'];
    }
    ?>
    
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-eye me-2"></i>
            Detalhes do Orçamento #<?php echo $orcamento['id']; ?>
        </div>
        <div class="header-actions">
            <a href="?acao=editar&id=<?php echo $orcamento['id']; ?>" class="btn btn-primary-modern">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
            <a href="orcamentos_unificados.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Informações do Cliente -->
        <div class="col-md-4">
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-user me-2"></i> Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Nome:</strong><br>
                        <?php echo $orcamento['cliente_nome']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Telefone:</strong><br>
                        <?php echo $orcamento['cliente_telefone']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>E-mail:</strong><br>
                        <?php echo $orcamento['cliente_email']; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Agendamento -->
        <div class="col-md-4">
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Agendamento</h5>
                </div>
                <div class="card-body">
                    <?php if($orcamento['data_agendamento']): ?>
                    <div class="mb-3">
                        <strong>Data:</strong><br>
                        <?php echo date('d/m/Y', strtotime($orcamento['data_agendamento'])); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Horário:</strong><br>
                        <?php echo $orcamento['hora_agendamento']; ?> - <?php echo $orcamento['hora_fim']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                            <?php echo ucfirst($orcamento['status']); ?>
                        </span>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p>Não agendado</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Resumo Financeiro -->
        <div class="col-md-4">
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-dollar-sign me-2"></i> Financeiro</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Valor Total:</strong><br>
                        <span class="fs-4 text-success"><?php echo formatarMoeda($orcamento['valor_total']); ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Duração Total:</strong><br>
                        <?php 
                        $horas = floor($total_duracao / 60);
                        $minutos = $total_duracao % 60;
                        echo $horas > 0 ? $horas . 'h' : '';
                        echo $minutos > 0 ? $minutos . 'min' : '';
                        ?>
                    </div>
                    <div class="mb-3">
                        <strong>Quantidade de Serviços:</strong><br>
                        <?php echo $total_servicos; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Serviços -->
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-tools me-2"></i> Serviços Contratados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Quantidade</th>
                            <th>Duração Unitária</th>
                            <th>Valor Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($servicos_orcamento as $servico): 
                            $subtotal = $servico['preco_unitario'] * $servico['quantidade'];
                            $duracao_total = $servico['duracao_media_min'] * $servico['quantidade'];
                            $horas = floor($servico['duracao_media_min'] / 60);
                            $minutos = $servico['duracao_media_min'] % 60;
                        ?>
                        <tr>
                            <td><?php echo $servico['servico_nome']; ?></td>
                            <td><?php echo $servico['quantidade']; ?></td>
                            <td><?php echo $horas > 0 ? $horas . 'h' : ''; ?><?php echo $minutos > 0 ? $minutos . 'min' : ''; ?></td>
                            <td><?php echo formatarMoeda($servico['preco_unitario']); ?></td>
                            <td><?php echo formatarMoeda($subtotal); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong><?php echo formatarMoeda($total_valor); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Observações -->
    <?php if($orcamento['descricao'] || $orcamento['observacoes']): ?>
    <div class="modern-card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-sticky-note me-2"></i> Observações</h5>
        </div>
        <div class="card-body">
            <?php if($orcamento['descricao']): ?>
            <div class="mb-4">
                <h6>Observações do Cliente:</h6>
                <div class="p-3 bg-light rounded"><?php echo nl2br($orcamento['descricao']); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($orcamento['observacoes']): ?>
            <div>
                <h6>Observações Internas:</h6>
                <div class="p-3 bg-light rounded"><?php echo nl2br($orcamento['observacoes']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php
    include 'includes/footer-admin.php';
}
?>