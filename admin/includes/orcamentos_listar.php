<?php
// includes/orcamentos_listar.php
$status_filter = $_GET['status'] ?? '';
$pagina = $_GET['pagina'] ?? 1;
$por_pagina = 15;
$offset = ($pagina - 1) * $por_pagina;

$mes_selecionado = $_GET['mes'] ?? date('m');
$ano_selecionado = $_GET['ano'] ?? date('Y');

// Construir query com filtros
$sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, s.nome as servico_nome 
        FROM orcamentos o 
        LEFT JOIN clientes c ON o.cliente_id = c.id 
        LEFT JOIN servicos s ON o.servico_id = s.id 
        WHERE 1=1";
$params = [];

if($status_filter) {
    $sql .= " AND o.status = ?";
    $params[] = $status_filter;
}

// Filtro por mês e ano
$sql .= " AND MONTH(o.data_solicitacao) = ? AND YEAR(o.data_solicitacao) = ?";
$params[] = $mes_selecionado;
$params[] = $ano_selecionado;

$sql .= " ORDER BY o.data_solicitacao DESC LIMIT $por_pagina OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total para paginação
$sql_count = "SELECT COUNT(*) as total FROM orcamentos o WHERE 1=1";
if($status_filter) {
    $sql_count .= " AND o.status = ?";
}
$sql_count .= " AND MONTH(o.data_solicitacao) = ? AND YEAR(o.data_solicitacao) = ?";

$stmt = $pdo->prepare($sql_count);
$stmt->execute($status_filter ? [$status_filter, $mes_selecionado, $ano_selecionado] : [$mes_selecionado, $ano_selecionado]);
$total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_orcamentos / $por_pagina);

// Estatísticas por status
$stmt = $pdo->prepare("SELECT status, COUNT(*) as total FROM orcamentos WHERE MONTH(data_solicitacao) = ? AND YEAR(data_solicitacao) = ? GROUP BY status");
$stmt->execute([$mes_selecionado, $ano_selecionado]);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ ESTATÍSTICAS FINANCEIRAS POR STATUS
$financeiro_stats = [];
$status_list = ['pendente', 'gerado', 'enviado', 'aprovado', 'concluido', 'recusado'];

foreach($status_list as $status) {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_orcamentos,
            COALESCE(SUM(valor_total), 0) as valor_bruto_total,
            COALESCE(SUM(
                CASE 
                    WHEN servico_id IS NOT NULL THEN 
                        (SELECT preco_base FROM servicos WHERE id = o.servico_id)
                    ELSE 0 
                END
            ), 0) as valor_mao_obra,
            COALESCE(SUM(
                (SELECT COALESCE(SUM(m.preco_unitario * om.quantidade), 0) 
                 FROM orcamento_materiais om 
                 JOIN materiais m ON om.material_id = m.id 
                 WHERE om.orcamento_id = o.id)
            ), 0) as valor_materiais
        FROM orcamentos o 
        WHERE status = ? 
        AND MONTH(data_solicitacao) = ? 
        AND YEAR(data_solicitacao) = ?
    ");
    $stmt->execute([$status, $mes_selecionado, $ano_selecionado]);
    $financeiro_stats[$status] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calcular custos e lucro
    if($financeiro_stats[$status]) {
        $valor_bruto = floatval($financeiro_stats[$status]['valor_bruto_total']);
        $valor_custos = floatval($financeiro_stats[$status]['valor_materiais']) + (floatval($financeiro_stats[$status]['valor_mao_obra']) * 0.3);
        $valor_lucro = $valor_bruto - $valor_custos;
        
        $financeiro_stats[$status]['valor_custos'] = $valor_custos;
        $financeiro_stats[$status]['valor_lucro'] = $valor_lucro;
    }
}
?>

<div class="page-header">
    <h2><i class="fas fa-file-invoice-dollar"></i> Gerenciar Orçamentos</h2>
    <div class="header-actions">
        <span class="total-info"><i class="fas fa-chart-bar"></i> Total: <?php echo $total_orcamentos; ?> orçamentos</span>
    </div>
</div>

<?php if($sucesso): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($erro): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ✅ FILTROS DE DATA -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Período</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="acao" value="listar">
            
            <div class="col-md-3">
                <label for="mes" class="form-label"><strong>Mês:</strong></label>
                <select id="mes" name="mes" class="form-select" onchange="this.form.submit()">
                    <?php
                    $meses = [
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                    ];
                    foreach($meses as $num => $nome): ?>
                        <option value="<?php echo $num; ?>" <?php echo $num == $mes_selecionado ? 'selected' : ''; ?>>
                            <?php echo $nome; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="ano" class="form-label"><strong>Ano:</strong></label>
                <select id="ano" name="ano" class="form-select" onchange="this.form.submit()">
                    <?php
                    $ano_atual = date('Y');
                    for($i = $ano_atual; $i >= $ano_atual - 5; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $ano_selecionado ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <?php if($status_filter): ?>
                <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
            <?php endif; ?>
            
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Aplicar Filtros</button>
                <a href="orcamentos.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Limpar Filtros</a>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <?php 
    $status_info = [
        'pendente' => ['label' => 'Pendentes', 'icon' => 'fas fa-clock', 'color' => 'warning'],
        'gerado' => ['label' => 'Gerados', 'icon' => 'fas fa-file-invoice', 'color' => 'info'],
        'enviado' => ['label' => 'Enviados', 'icon' => 'fas fa-paper-plane', 'color' => 'primary'],
        'aprovado' => ['label' => 'Aprovados', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
        'concluido' => ['label' => 'Concluídos', 'icon' => 'fas fa-flag-checkered', 'color' => 'success'],
        'recusado' => ['label' => 'Recusados', 'icon' => 'fas fa-times-circle', 'color' => 'danger']
    ];
    
    foreach($status_info as $status_key => $info):
        $total = 0;
        foreach($stats as $stat) {
            if($stat['status'] == $status_key) {
                $total = $stat['total'];
                break;
            }
        }
    ?>
    <div class="col-md-2 col-6 mb-3">
        <div class="card stat-card border-<?php echo $info['color']; ?> shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stat-icon text-<?php echo $info['color']; ?> mb-2">
                    <i class="<?php echo $info['icon']; ?> fa-2x"></i>
                </div>
                <h3 class="stat-number"><?php echo $total; ?></h3>
                <p class="stat-label text-muted mb-0"><?php echo $info['label']; ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ✅ ESTATÍSTICAS FINANCEIRAS DETALHADAS -->
<div class="card mt-4 shadow-sm">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas Financeiras - <?php echo $meses[intval($mes_selecionado)] . ' de ' . $ano_selecionado; ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach($status_info as $status_key => $info): 
                $stats_financeiro = $financeiro_stats[$status_key] ?? [];
                $valor_bruto = $stats_financeiro['valor_bruto_total'] ?? 0;
                $valor_custos = $stats_financeiro['valor_custos'] ?? 0;
                $valor_lucro = $stats_financeiro['valor_lucro'] ?? 0;
            ?>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="finance-stat-card card h-100">
                    <div class="card-header bg-<?php echo $info['color']; ?> bg-opacity-10 border-<?php echo $info['color']; ?>">
                        <h6 class="mb-0"><i class="<?php echo $info['icon']; ?> me-2"></i><?php echo $info['label']; ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="finance-stat-item d-flex justify-content-between align-items-center mb-2">
                            <span class="finance-stat-label">Valor Bruto:</span>
                            <span class="finance-stat-value text-success fw-bold"><?php echo formatarMoeda($valor_bruto); ?></span>
                        </div>
                        <div class="finance-stat-item d-flex justify-content-between align-items-center mb-2">
                            <span class="finance-stat-label">Custos:</span>
                            <span class="finance-stat-value text-danger"><?php echo formatarMoeda($valor_custos); ?></span>
                        </div>
                        <div class="finance-stat-item d-flex justify-content-between align-items-center mb-0">
                            <span class="finance-stat-label">Lucro:</span>
                            <span class="finance-stat-value fw-bold <?php echo $valor_lucro >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo formatarMoeda($valor_lucro); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- ✅ RESUMO GERAL -->
        <div class="finance-summary mt-4 p-4 bg-light rounded">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="text-muted">💰 Total Bruto</h6>
                        <h4 class="text-success fw-bold">
                            <?php
                            $total_geral_bruto = 0;
                            foreach($financeiro_stats as $stats) {
                                $total_geral_bruto += floatval($stats['valor_bruto_total'] ?? 0);
                            }
                            echo formatarMoeda($total_geral_bruto);
                            ?>
                        </h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="text-muted">📉 Total Custos</h6>
                        <h4 class="text-danger">
                            <?php
                            $total_geral_custos = 0;
                            foreach($financeiro_stats as $stats) {
                                $total_geral_custos += floatval($stats['valor_custos'] ?? 0);
                            }
                            echo formatarMoeda($total_geral_custos);
                            ?>
                        </h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="text-muted">📈 Total Lucro</h6>
                        <h4 class="fw-bold <?php echo ($total_geral_bruto - $total_geral_custos) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo formatarMoeda($total_geral_bruto - $total_geral_custos); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Orçamentos</h5>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2">
                    <select id="statusFilter" onchange="filtrarOrcamentos()" class="form-select" style="max-width: 200px;">
                        <option value="">Todos os status</option>
                        <?php foreach($status_info as $status_key => $info): ?>
                        <option value="<?php echo $status_key; ?>" <?php echo $status_filter == $status_key ? 'selected' : ''; ?>>
                            <?php echo $info['label']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="text" id="searchOrcamentos" placeholder="Buscar orçamentos..." class="form-control" style="max-width: 300px;">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="tabelaOrcamentos">
                <thead class="table-light">
                    <tr>
                        <th width="80">ID</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Equipamento</th>
                        <th width="120">Data</th>
                        <th width="140">Valor</th>
                        <th width="120">Status</th>
                        <th width="200" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orcamentos)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-invoice-dollar fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Nenhum orçamento encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($orcamentos as $orcamento): ?>
                        <tr>
                            <td>
                                <strong class="text-primary">#<?php echo $orcamento['id']; ?></strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong><?php echo $orcamento['cliente_nome']; ?></strong>
                                        <?php if($orcamento['cliente_telefone']): ?>
                                        <br><small class="text-muted"><?php echo $orcamento['cliente_telefone']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?php echo $orcamento['servico_nome']; ?></span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo $orcamento['equipamento_marca'] ?: '-'; ?>
                                    <?php echo $orcamento['equipamento_btus'] ? ' - ' . $orcamento['equipamento_btus'] . ' BTUs' : ''; ?>
                                </small>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?></small>
                            </td>
                            <td>
                                <?php if($orcamento['valor_total'] && $orcamento['valor_total'] > 0): ?>
                                <strong class="text-success"><?php echo formatarMoeda($orcamento['valor_total']); ?></strong>
                                <?php else: ?>
                                <span class="text-muted">Não definido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge status-<?php echo $orcamento['status']; ?>">
                                    <?php echo ucfirst($orcamento['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="?acao=editar&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?acao=visualizar&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?acao=gerar_orcamento&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-success" title="Gerar Orçamento">
                                        <i class="fas fa-calculator"></i>
                                    </a>
                                    <a href="?acao=enviar_whatsapp&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-success" title="Enviar WhatsApp" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="gerar_pdf_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-danger" title="Gerar PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="?acao=excluir&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir o orçamento #<?php echo $orcamento['id']; ?> do cliente <?php echo addslashes($orcamento['cliente_nome']); ?>?')">
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
    </div>
    
    <!-- Paginação -->
    <?php if($total_paginas > 1): ?>
    <div class="card-footer bg-white">
        <nav aria-label="Paginação">
            <ul class="pagination justify-content-center mb-0">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?>&mes=<?php echo $mes_selecionado; ?>&ano=<?php echo $ano_selecionado; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<style>
.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.finance-stat-card {
    border-left: 4px solid;
    transition: transform 0.2s;
}

.finance-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge.status-pendente { background: #ffc107; color: #000; }
.badge.status-gerado { background: #0dcaf0; color: #000; }
.badge.status-enviado { background: #6f42c1; color: #fff; }
.badge.status-aprovado { background: #198754; color: #fff; }
.badge.status-concluido { background: #20c997; color: #fff; }
.badge.status-recusado { background: #dc3545; color: #fff; }

.finance-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.finance-summary h4 {
    margin-bottom: 0;
}

.form-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.form-inline .form-group {
    margin-bottom: 0;
}

.mr-3 {
    margin-right: 1rem;
}

.ml-2 {
    margin-left: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.action-buttons .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    margin: 0.1rem;
}
</style>

<script>
function filtrarOrcamentos() {
    const status = document.getElementById('statusFilter').value;
    const mes = <?php echo $mes_selecionado; ?>;
    const ano = <?php echo $ano_selecionado; ?>;
    const url = new URL(window.location.href);
    
    if(status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    url.searchParams.set('mes', mes);
    url.searchParams.set('ano', ano);
    url.searchParams.delete('pagina');
    
    window.location.href = url.toString();
}

// Busca em tempo real
document.getElementById('searchOrcamentos').addEventListener('input', function(e) {
    const termo = e.target.value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaOrcamentos tbody tr');
    
    linhas.forEach(linha => {
        const texto = linha.textContent.toLowerCase();
        linha.style.display = texto.includes(termo) ? '' : 'none';
    });
});
</script>