<?php
// admin/includes/solicitacao_detalhes.php
// Detalhes completos de uma solicitação

// Buscar solicitação principal
$stmt = $pdo->prepare("
    SELECT 
        o.*, 
        c.nome as cliente_nome, 
        c.email as cliente_email, 
        c.telefone as cliente_telefone,
        c.rua, c.numero, c.bairro, c.cidade,
        a.data_agendamento,
        a.hora_agendamento,
        a.observacoes as obs_agendamento,
        a.status as status_agendamento,
        s.nome as servico_nome
    FROM orcamentos o
    LEFT JOIN clientes c ON o.cliente_id = c.id
    LEFT JOIN agendamentos a ON o.id = a.orcamento_id
    LEFT JOIN servicos s ON o.servico_id = s.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    echo "<div class='alert alert-danger'>Solicitação não encontrada!</div>";
    exit;
}

// Buscar serviços detalhados
$stmt = $pdo->prepare("
    SELECT os.*, s.nome as servico_nome 
    FROM orcamento_servicos os
    LEFT JOIN servicos s ON os.servico_id = s.id
    WHERE os.orcamento_id = ?
");
$stmt->execute([$id]);
$servicos_detalhados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar materiais
$stmt = $pdo->prepare("
    SELECT om.*, m.nome, m.preco_unitario, m.unidade_medida
    FROM orcamento_materiais om
    LEFT JOIN materiais m ON om.material_id = m.id
    WHERE om.orcamento_id = ?
");
$stmt->execute([$id]);
$materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h2>
        <i class="fas fa-eye me-2"></i> Solicitação #<?php echo $solicitacao['id']; ?>
    </h2>
    <div class="header-actions">
        <a href="?acao=editar&id=<?php echo $solicitacao['id']; ?>" class="btn btn-primary-modern btn-modern">
            <i class="fas fa-edit me-2"></i> Editar
        </a>
        <a href="solicitacoes.php" class="btn btn-outline-primary btn-modern">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações do Cliente -->
    <div class="col-md-6 mb-4">
        <div class="modern-card h-100">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-user me-2"></i> Cliente</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>Nome:</label>
                    <span class="info-value"><?php echo $solicitacao['cliente_nome']; ?></span>
                </div>
                <div class="info-item">
                    <label>Telefone:</label>
                    <span class="info-value"><?php echo $solicitacao['cliente_telefone']; ?></span>
                </div>
                <div class="info-item">
                    <label>E-mail:</label>
                    <span class="info-value"><?php echo $solicitacao['cliente_email']; ?></span>
                </div>
                <div class="info-item">
                    <label>Endereço:</label>
                    <span class="info-value">
                        <?php echo $solicitacao['rua']; ?>, <?php echo $solicitacao['numero']; ?> - 
                        <?php echo $solicitacao['bairro']; ?>, <?php echo $solicitacao['cidade']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhes da Solicitação -->
    <div class="col-md-6 mb-4">
        <div class="modern-card h-100">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i> Detalhes</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>Status:</label>
                    <span class="status-badge status-<?php echo $solicitacao['status']; ?>">
                        <?php echo ucfirst($solicitacao['status']); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Data Solicitação:</label>
                    <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($solicitacao['data_solicitacao'])); ?></span>
                </div>
                <div class="info-item">
                    <label>Valor:</label>
                    <span class="info-value text-success fw-bold"><?php echo formatarMoeda($solicitacao['valor_total']); ?></span>
                </div>
                <?php if($solicitacao['data_agendamento']): ?>
                <div class="info-item">
                    <label>Agendamento:</label>
                    <span class="info-value">
                        <?php echo date('d/m/Y', strtotime($solicitacao['data_agendamento'])); ?> 
                        às <?php echo $solicitacao['hora_agendamento']; ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Status Agendamento:</label>
                    <span class="status-badge status-<?php echo $solicitacao['status_agendamento']; ?>">
                        <?php echo ucfirst($solicitacao['status_agendamento']); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Serviços Detalhados -->
<div class="modern-card mb-4">
    <div class="card-header">
        <h5 class="card-title"><i class="fas fa-cogs me-2"></i> Serviços Solicitados</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach($servicos_detalhados as $index => $servico): ?>
            <div class="col-md-6 mb-3">
                <div class="servico-card p-3 border rounded">
                    <h6>Item #<?php echo ($index + 1); ?> - <?php echo $servico['servico_nome']; ?></h6>
                    <div class="servico-info small">
                        <div><strong>Marca:</strong> <?php echo $servico['marca']; ?></div>
                        <div><strong>BTUs:</strong> <?php echo $servico['btus']; ?></div>
                        <?php if($servico['modelo']): ?>
                        <div><strong>Modelo:</strong> <?php echo $servico['modelo']; ?></div>
                        <?php endif; ?>
                        <?php if($servico['observacoes_cliente']): ?>
                        <div><strong>Observações:</strong> <?php echo $servico['observacoes_cliente']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Materiais -->
<?php if(!empty($materiais)): ?>
<div class="modern-card mb-4">
    <div class="card-header">
        <h5 class="card-title"><i class="fas fa-boxes me-2"></i> Materiais</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Quantidade</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_materiais = 0;
                    foreach($materiais as $material): 
                        $subtotal = $material['preco_unitario'] * $material['quantidade'];
                        $total_materiais += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo $material['nome']; ?></td>
                        <td><?php echo $material['quantidade']; ?> <?php echo $material['unidade_medida']; ?></td>
                        <td><?php echo formatarMoeda($material['preco_unitario']); ?></td>
                        <td><?php echo formatarMoeda($subtotal); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="3" class="text-end"><strong>Total Materiais:</strong></td>
                        <td><strong><?php echo formatarMoeda($total_materiais); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Observações -->
<div class="modern-card">
    <div class="card-header">
        <h5 class="card-title"><i class="fas fa-sticky-note me-2"></i> Observações</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Observações do Cliente:</h6>
                <div class="observacao-box p-3 bg-light rounded">
                    <?php echo nl2br($solicitacao['observacoes_cliente'] ?: 'Sem observações'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Observações Internas:</h6>
                <div class="observacao-box p-3 bg-light rounded">
                    <?php echo nl2br($solicitacao['observacoes_admin'] ?: 'Sem observações internas'); ?>
                </div>
            </div>
        </div>
        <?php if($solicitacao['obs_agendamento']): ?>
        <div class="mt-3">
            <h6>Observações do Agendamento:</h6>
            <div class="observacao-box p-3 bg-info bg-opacity-10 rounded">
                <?php echo nl2br($solicitacao['obs_agendamento']); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-item label {
    font-weight: 600;
    color: var(--dark-color);
}

.info-value {
    color: var(--gray);
    text-align: right;
}

.servico-card {
    background-color: #f8fafc;
    transition: all 0.3s ease;
}

.servico-card:hover {
    background-color: #e2e8f0;
    transform: translateY(-2px);
}

.observacao-box {
    min-height: 100px;
    line-height: 1.6;
}
</style>