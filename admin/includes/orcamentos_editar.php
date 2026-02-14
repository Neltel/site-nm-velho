<?php
// includes/orcamentos_editar.php
$orcamento = [];
$stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone, 
                              s.nome as servico_nome, s.preco_base as servico_preco, s.categoria as servico_categoria
                      FROM orcamentos o 
                      LEFT JOIN clientes c ON o.cliente_id = c.id 
                      LEFT JOIN servicos s ON o.servico_id = s.id 
                      WHERE o.id = ?");
$stmt->execute([$id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$orcamento) {
    echo "<div class='alert alert-danger'>Orçamento não encontrado!</div>";
    exit;
}

// Buscar dados relacionados
$materiais_orcamento = buscarMateriaisOrcamento($pdo, $id);
$total_materiais = calcularTotalMateriais($materiais_orcamento);
$servicos_adicionais = buscarServicosAdicionais($pdo, $id);
$total_servicos_adicionais = calcularTotalServicosAdicionais($servicos_adicionais);

// Buscar agendamento existente
$agendamento_existente = [];
$stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE orcamento_id = ?");
$stmt->execute([$id]);
$agendamento_existente = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar todos os materiais e serviços disponíveis
$stmt = $pdo->prepare("SELECT * FROM materiais WHERE ativo = 1 ORDER BY categoria, nome");
$stmt->execute();
$todos_materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
$stmt->execute();
$todos_servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar agendamentos existentes para o calendário
$agendamentos_periodo = [];
if($agendamento_existente && $agendamento_existente['data_agendamento']) {
    $data_inicio_cal = date('Y-m-d', strtotime('-7 days', strtotime($agendamento_existente['data_agendamento'])));
    $data_fim_cal = date('Y-m-d', strtotime('+30 days', strtotime($agendamento_existente['data_agendamento'])));
    $agendamentos_periodo = buscarAgendamentosPeriodo($pdo, $data_inicio_cal, $data_fim_cal);
}
?>

<div class="page-header">
    <h2>
        <i class="fas fa-edit"></i> Editar Orçamento - #<?php echo $orcamento['id']; ?>
    </h2>
    <div class="header-actions">
        <a href="?acao=gerar_orcamento&id=<?php echo $orcamento['id']; ?>" class="btn btn-success">
            <i class="fas fa-calculator"></i> Gerar Orçamento
        </a>
        <a href="?acao=enviar_whatsapp&id=<?php echo $orcamento['id']; ?>" class="btn btn-success" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <a href="gerar_pdf_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="orcamentos.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
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

<div class="row">
    <!-- Informações Principais -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Informações do Cliente</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-4"><strong>Nome:</strong></div>
                    <div class="col-8"><?php echo $orcamento['cliente_nome']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Telefone:</strong></div>
                    <div class="col-8"><?php echo $orcamento['cliente_telefone']; ?></div>
                </div>
                <div class="row mb-0">
                    <div class="col-4"><strong>E-mail:</strong></div>
                    <div class="col-8"><?php echo $orcamento['cliente_email']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tools"></i> Detalhes do Serviço</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-4"><strong>Serviço:</strong></div>
                    <div class="col-8"><?php echo $orcamento['servico_nome']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Equipamento:</strong></div>
                    <div class="col-8">
                        <?php echo $orcamento['equipamento_marca'] ?: '-'; ?>
                        <?php echo $orcamento['equipamento_btus'] ? ' - ' . $orcamento['equipamento_btus'] . ' BTUs' : ''; ?>
                        <?php echo $orcamento['equipamento_tipo'] ? ' (' . $orcamento['equipamento_tipo'] . ')' : ''; ?>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-4"><strong>Data:</strong></div>
                    <div class="col-8"><?php echo date('d/m/Y H:i', strtotime($orcamento['data_solicitacao'])); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($orcamento['descricao']): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Observações do Cliente</h5>
    </div>
    <div class="card-body">
        <?php echo nl2br($orcamento['descricao']); ?>
    </div>
</div>
<?php endif; ?>

<form method="POST" class="needs-validation" novalidate>
    <!-- ✅ SEÇÃO DE AGENDAMENTO -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Agendamento do Serviço</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="agendamento_data_inicio" class="form-label">Data Início *</label>
                            <input type="date" id="agendamento_data_inicio" name="agendamento_data_inicio" 
                                   class="form-control" 
                                   value="<?php echo $agendamento_existente['data_agendamento'] ?? ''; ?>"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="agendamento_hora_inicio" class="form-label">Hora Início *</label>
                            <select id="agendamento_hora_inicio" name="agendamento_hora_inicio" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php
                                $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                foreach($horarios as $horario):
                                    $selected = ($agendamento_existente['hora_agendamento'] ?? '') == $horario ? 'selected' : '';
                                ?>
                                <option value="<?php echo $horario; ?>" <?php echo $selected; ?>>
                                    <?php echo $horario; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="agendamento_data_fim" class="form-label">Data Fim (Opcional)</label>
                            <input type="date" id="agendamento_data_fim" name="agendamento_data_fim" 
                                   class="form-control" 
                                   value="<?php echo $agendamento_existente['data_fim'] ?? ''; ?>"
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="agendamento_hora_fim" class="form-label">Hora Fim (Opcional)</label>
                            <select id="agendamento_hora_fim" name="agendamento_hora_fim" class="form-select">
                                <option value="">Selecione</option>
                                <?php
                                $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                foreach($horarios as $horario):
                                    $selected = ($agendamento_existente['hora_fim'] ?? '') == $horario ? 'selected' : '';
                                ?>
                                <option value="<?php echo $horario; ?>" <?php echo $selected; ?>>
                                    <?php echo $horario; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        💡 Defina data e hora de início e fim para bloquear este período no calendário de agendamentos.
                    </small>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-calendar-check"></i> Calendário</h6>
                            <div id="calendarioAgendamentos" class="small">
                                <p class="mb-2"><strong>Agendamentos no período:</strong></p>
                                <div id="listaAgendamentos">
                                    <?php if(empty($agendamentos_periodo)): ?>
                                        <p class="text-muted mb-0">Nenhum agendamento no período selecionado.</p>
                                    <?php else: ?>
                                        <?php foreach($agendamentos_periodo as $ag): ?>
                                            <div class="agendamento-item border-bottom py-1">
                                                <small>
                                                    <strong><?php echo date('d/m', strtotime($ag['data_agendamento'])); ?></strong> 
                                                    <?php echo $ag['hora_agendamento']; ?> - 
                                                    <?php echo $ag['cliente_nome']; ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ SEÇÃO DE SERVIÇOS ADICIONAIS -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Serviços Adicionais</h5>
        </div>
        <div class="card-body">
            <?php if(!empty($todos_servicos)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="30" class="text-center">
                                    <input type="checkbox" id="selectAllServicos" class="form-check-input">
                                </th>
                                <th>Serviço</th>
                                <th width="100" class="text-center">Quantidade</th>
                                <th width="120" class="text-end">Valor Unit.</th>
                                <th width="120" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($todos_servicos as $servico): 
                                // Verificar se serviço já está no orçamento
                                $servico_no_orcamento = null;
                                foreach($servicos_adicionais as $serv_orc) {
                                    if($serv_orc['servico_id'] == $servico['id']) {
                                        $servico_no_orcamento = $serv_orc;
                                        break;
                                    }
                                }
                                
                                $usar = $servico_no_orcamento ? true : false;
                                $quantidade = $servico_no_orcamento ? $servico_no_orcamento['quantidade'] : 1;
                                $subtotal = $servico['preco_base'] * $quantidade;
                            ?>
                            <tr class="servico-adicional-row">
                                <td class="text-center">
                                    <input type="checkbox" name="servicos_adicionais[<?php echo $servico['id']; ?>][usar]" 
                                           value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                           class="form-check-input servico-checkbox">
                                </td>
                                <td>
                                    <strong><?php echo $servico['nome']; ?></strong>
                                    <?php if($servico['descricao']): ?>
                                    <br><small class="text-muted"><?php echo substr($servico['descricao'], 0, 80); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <input type="number" name="servicos_adicionais[<?php echo $servico['id']; ?>][quantidade]" 
                                           value="<?php echo $quantidade; ?>" 
                                           min="1" 
                                           step="1" 
                                           class="form-control form-control-sm quantidade-servico"
                                           data-preco="<?php echo $servico['preco_base']; ?>">
                                </td>
                                <td class="text-end valor-servico"><?php echo formatarMoeda($servico['preco_base']); ?></td>
                                <td class="text-end subtotal-servico"><?php echo formatarMoeda($subtotal); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-end"><strong>Total Serviços Adicionais:</strong></td>
                                <td class="text-end" id="totalServicosAdicionais"><strong><?php echo formatarMoeda($total_servicos_adicionais); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mb-0">
                    <i class="fas fa-info-circle"></i> Nenhum serviço adicional cadastrado no sistema.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ✅ SEÇÃO DE MATERIAIS -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-boxes"></i> Materiais do Orçamento</h5>
        </div>
        <div class="card-body">
            <?php if(!empty($todos_materiais)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="30" class="text-center">
                                    <input type="checkbox" id="selectAllMateriais" class="form-check-input">
                                </th>
                                <th>Material</th>
                                <th width="120">Categoria</th>
                                <th width="120" class="text-center">Quantidade</th>
                                <th width="120" class="text-end">Preço Unit.</th>
                                <th width="120" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($todos_materiais as $material): 
                                // Verificar se material já está no orçamento
                                $material_no_orcamento = null;
                                foreach($materiais_orcamento as $mat_orc) {
                                    if($mat_orc['material_id'] == $material['id']) {
                                        $material_no_orcamento = $mat_orc;
                                        break;
                                    }
                                }
                                
                                $quantidade = $material_no_orcamento ? $material_no_orcamento['quantidade'] : ($material['quantidade_padrao'] ?? 1);
                                $usar = $material_no_orcamento ? true : false;
                                $subtotal = $material['preco_unitario'] * $quantidade;
                            ?>
                            <tr class="material-row">
                                <td class="text-center">
                                    <input type="checkbox" name="materiais[<?php echo $material['id']; ?>][usar]" 
                                           value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                           class="form-check-input material-checkbox">
                                </td>
                                <td>
                                    <strong><?php echo $material['nome']; ?></strong>
                                    <?php if($material['descricao']): ?>
                                    <br><small class="text-muted"><?php echo substr($material['descricao'], 0, 80); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $material['categoria']; ?></span>
                                </td>
                                <td class="text-center">
                                    <input type="number" name="materiais[<?php echo $material['id']; ?>][quantidade]" 
                                           value="<?php echo $quantidade; ?>" 
                                           min="0" 
                                           step="0.5" 
                                           class="form-control form-control-sm quantidade-input" 
                                           data-preco="<?php echo $material['preco_unitario']; ?>">
                                    <small class="text-muted"><?php echo $material['unidade_medida']; ?></small>
                                </td>
                                <td class="text-end preco-unitario"><?php echo formatarMoeda($material['preco_unitario']); ?></td>
                                <td class="text-end subtotal"><?php echo formatarMoeda($subtotal); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-end"><strong>Total Materiais:</strong></td>
                                <td colspan="2" class="text-end" id="totalMateriais"><strong><?php echo formatarMoeda($total_materiais); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Mão de Obra:</strong></td>
                                <td colspan="2" class="text-end" id="maoObra"><?php echo formatarMoeda($orcamento['servico_preco']); ?></td>
                            </tr>
                            <tr id="rowServicosAdicionais" style="display: <?php echo $total_servicos_adicionais > 0 ? 'table-row' : 'none'; ?>;">
                                <td colspan="4" class="text-end"><strong>Serviços Adicionais:</strong></td>
                                <td colspan="2" class="text-end" id="totalServicosAdicionaisTabela"><?php echo formatarMoeda($total_servicos_adicionais); ?></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="4" class="text-end"><strong>VALOR TOTAL:</strong></td>
                                <td colspan="2" class="text-end" id="valorTotalFinal"><strong><?php echo formatarMoeda($total_materiais + $orcamento['servico_preco'] + $total_servicos_adicionais); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mb-0">
                    <i class="fas fa-info-circle"></i> Nenhum material cadastrado no sistema.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit"></i> Configurações do Orçamento</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="pendente" <?php echo $orcamento['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="gerado" <?php echo $orcamento['status'] == 'gerado' ? 'selected' : ''; ?>>Gerado</option>
                        <option value="enviado" <?php echo $orcamento['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                        <option value="aprovado" <?php echo $orcamento['status'] == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="concluido" <?php echo $orcamento['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                        <option value="recusado" <?php echo $orcamento['status'] == 'recusado' ? 'selected' : ''; ?>>Recusado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="valor_total" class="form-label">Valor Total *</label>
                    <input type="text" id="valor_total" name="valor_total" class="form-control money" 
                           value="<?php echo $orcamento['valor_total'] ? formatarMoeda($orcamento['valor_total']) : formatarMoeda($orcamento['servico_preco'] + $total_materiais + $total_servicos_adicionais); ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <label for="observacoes_admin" class="form-label">Observações Internas</label>
                <textarea id="observacoes_admin" name="observacoes_admin" class="form-control" rows="4"><?php echo $orcamento['observacoes_admin']; ?></textarea>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Orçamento
                </button>
                <a href="orcamentos.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</form>

<script>
// Cálculo em tempo real dos valores
document.addEventListener('DOMContentLoaded', function() {
    const maoObra = <?php echo $orcamento['servico_preco']; ?>;
    
    function calcularTotais() {
        let totalMateriais = 0;
        let totalServicosAdicionais = 0;
        
        // Calcular totais dos materiais selecionados
        document.querySelectorAll('.material-row').forEach(row => {
            const checkbox = row.querySelector('.material-checkbox');
            const quantidadeInput = row.querySelector('.quantidade-input');
            const preco = parseFloat(quantidadeInput.dataset.preco);
            
            if(checkbox.checked) {
                const quantidade = parseFloat(quantidadeInput.value) || 0;
                const subtotal = preco * quantidade;
                totalMateriais += subtotal;
                
                // Atualizar subtotal na linha
                row.querySelector('.subtotal').textContent = formatarMoedaJS(subtotal);
            } else {
                row.querySelector('.subtotal').textContent = 'R$ 0,00';
            }
        });
        
        // Calcular totais dos serviços adicionais selecionados
        document.querySelectorAll('.servico-adicional-row').forEach(row => {
            const checkbox = row.querySelector('.servico-checkbox');
            const quantidadeInput = row.querySelector('.quantidade-servico');
            const preco = parseFloat(quantidadeInput.dataset.preco);
            
            if(checkbox.checked) {
                const quantidade = parseFloat(quantidadeInput.value) || 1;
                const subtotal = preco * quantidade;
                totalServicosAdicionais += subtotal;
                
                // Atualizar subtotal na linha
                row.querySelector('.subtotal-servico').textContent = formatarMoedaJS(subtotal);
            } else {
                row.querySelector('.subtotal-servico').textContent = 'R$ 0,00';
            }
        });
        
        // Atualizar totais na tabela
        document.getElementById('totalMateriais').innerHTML = '<strong>' + formatarMoedaJS(totalMateriais) + '</strong>';
        document.getElementById('totalServicosAdicionais').innerHTML = '<strong>' + formatarMoedaJS(totalServicosAdicionais) + '</strong>';
        document.getElementById('totalServicosAdicionaisTabela').textContent = formatarMoedaJS(totalServicosAdicionais);
        
        // Mostrar/ocultar linha de serviços adicionais
        const rowServicos = document.getElementById('rowServicosAdicionais');
        if(totalServicosAdicionais > 0) {
            rowServicos.style.display = 'table-row';
        } else {
            rowServicos.style.display = 'none';
        }
        
        // Atualizar valor total
        const valorTotal = maoObra + totalMateriais + totalServicosAdicionais;
        document.getElementById('valorTotalFinal').innerHTML = '<strong>' + formatarMoedaJS(valorTotal) + '</strong>';
        document.getElementById('valor_total').value = formatarMoedaJS(valorTotal);
    }
    
    function formatarMoedaJS(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+,)/g, '$&.');
    }
    
    // Event listeners para materiais
    document.querySelectorAll('.material-checkbox, .quantidade-input').forEach(element => {
        element.addEventListener('change', calcularTotais);
    });
    
    document.querySelectorAll('.quantidade-input').forEach(input => {
        input.addEventListener('input', calcularTotais);
    });
    
    // Event listeners para serviços adicionais
    document.querySelectorAll('.servico-checkbox, .quantidade-servico').forEach(element => {
        element.addEventListener('change', calcularTotais);
    });
    
    document.querySelectorAll('.quantidade-servico').forEach(input => {
        input.addEventListener('input', calcularTotais);
    });
    
    // Select All para serviços
    document.getElementById('selectAllServicos').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.servico-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        calcularTotais();
    });
    
    // Select All para materiais
    document.getElementById('selectAllMateriais').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.material-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        calcularTotais();
    });
    
    // ✅ CARREGAR AGENDAMENTOS QUANDO ALTERAR DATAS
    function carregarAgendamentosPeriodo() {
        const dataInicio = document.getElementById('agendamento_data_inicio').value;
        const dataFim = document.getElementById('agendamento_data_fim').value || dataInicio;
        
        if(!dataInicio) {
            document.getElementById('listaAgendamentos').innerHTML = 
                '<p class="text-muted mb-0">Selecione uma data para ver os agendamentos.</p>';
            return;
        }
        
        // Fazer requisição para buscar agendamentos do período
        fetch(`api/agendamentos_periodo.php?data_inicio=${dataInicio}&data_fim=${dataFim}`)
            .then(response => response.json())
            .then(agendamentos => {
                const lista = document.getElementById('listaAgendamentos');
                
                if(agendamentos.length > 0) {
                    let html = '';
                    agendamentos.forEach(ag => {
                        html += `
                            <div class="agendamento-item border-bottom py-1">
                                <small>
                                    <strong>${ag.data_agendamento}</strong> 
                                    ${ag.hora_agendamento} - 
                                    ${ag.nome_cliente}
                                </small>
                            </div>
                        `;
                    });
                    lista.innerHTML = html;
                } else {
                    lista.innerHTML = '<p class="text-success mb-0"><i class="fas fa-check-circle"></i> Período disponível para agendamento.</p>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('listaAgendamentos').innerHTML = 
                    '<p class="text-muted mb-0">Erro ao carregar agendamentos.</p>';
            });
    }
    
    // Event listeners para datas do agendamento
    document.getElementById('agendamento_data_inicio').addEventListener('change', carregarAgendamentosPeriodo);
    document.getElementById('agendamento_data_fim').addEventListener('change', carregarAgendamentosPeriodo);
    document.getElementById('agendamento_hora_inicio').addEventListener('change', carregarAgendamentosPeriodo);
    document.getElementById('agendamento_hora_fim').addEventListener('change', carregarAgendamentosPeriodo);
    
    // Calcular totais iniciais
    calcularTotais();
});

// Máscara para campo de moeda
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('.money');
    moneyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = 'R$ ' + value;
        });
    });
});
</script>

<style>
.agendamento-item {
    font-size: 0.8rem;
}

.material-row:hover, .servico-adicional-row:hover {
    background-color: #f8f9fa;
}

.table-active {
    background-color: #e9ecef !important;
}

.badge {
    font-size: 0.75em;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.375rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}
</style>