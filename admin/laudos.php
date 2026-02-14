<?php
// admin/gerar_laudo.php - LAUDO DE MANUTENÇÃO E PMOC
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: orcamentos.php?erro=id_invalido');
    exit;
}
$id = intval($_GET['id']);

try {
    // Buscar dados do orçamento/agendamento
    $stmt = $pdo->prepare("
        SELECT 
            o.*, 
            c.nome as cliente_nome, 
            c.telefone as cliente_telefone,
            c.email as cliente_email,
            c.rua as cliente_rua,
            c.numero as cliente_numero,
            c.bairro as cliente_bairro,
            c.cidade as cliente_cidade,
            s.nome as servico_nome,
            s.descricao as servico_descricao,
            a.data_agendamento,
            a.hora_agendamento,
            a.observacoes as obs_agendamento
        FROM orcamentos o 
        LEFT JOIN clientes c ON o.cliente_id = c.id 
        LEFT JOIN servicos s ON o.servico_id = s.id
        LEFT JOIN agendamentos a ON o.id = a.orcamento_id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$dados) {
        echo "<div class='alert alert-danger'>Registro não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    // Buscar materiais utilizados
    $stmt = $pdo->prepare("
        SELECT om.*, m.nome, m.categoria, m.unidade_medida 
        FROM orcamento_materiais om 
        JOIN materiais m ON om.material_id = m.id 
        WHERE om.orcamento_id = ?
    ");
    $stmt->execute([$id]);
    $materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar serviços realizados
    $stmt = $pdo->prepare("
        SELECT os.*, s.nome, s.categoria 
        FROM orcamento_servicos os 
        JOIN servicos s ON os.servico_id = s.id 
        WHERE os.orcamento_id = ?
    ");
    $stmt->execute([$id]);
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Configurações do sistema
    $configs = $pdo->query("SELECT chave, valor FROM config_site")->fetchAll(PDO::FETCH_KEY_PAIR);
    $site_nome = $configs['site_nome'] ?? 'N&M Refrigeração';
    $site_telefone = $configs['site_telefone'] ?? '';
    $site_email = $configs['site_email'] ?? '';
    $tecnico_responsavel = "Técnico N&M Refrigeração";
    
} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Erro ao carregar dados: " . $e->getMessage() . "</div>");
}

// Determinar tipo de laudo baseado no serviço
$tipo_laudo = 'manutencao';
$servico_nome_lower = strtolower($dados['servico_nome'] ?? '');
if(strpos($servico_nome_lower, 'pmoc') !== false || 
   strpos($servico_nome_lower, 'limpeza') !== false ||
   strpos($servico_nome_lower, 'preventiva') !== false) {
    $tipo_laudo = 'pmoc';
}

$titulo_laudo = ($tipo_laudo == 'pmoc') ? 'LAUDO TÉCNICO PMOC' : 'LAUDO TÉCNICO DE MANUTENÇÃO';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h2 class="mb-3 mb-md-0"><i class="fas fa-file-medical-alt"></i> <?php echo $titulo_laudo; ?></h2>
    <div class="header-actions">
        <div class="btn-group flex-wrap">
            <button onclick="imprimirLaudo()" class="btn btn-primary btn-sm">
                <i class="fas fa-print"></i> Imprimir Laudo
            </button>
            <button onclick="downloadPDF()" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </button>
            <a href="orcamentos.php?acao=editar&id=<?php echo $id; ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div id="conteudo-laudo" class="laudo-container">
    <!-- Cabeçalho do Laudo -->
    <div class="laudo-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="../assets/images/<?php echo $configs['site_logo'] ?? 'logo.png'; ?>" 
                     alt="Logo" class="laudo-logo" style="max-height: 80px;">
            </div>
            <div class="col-md-6 text-center">
                <h1 class="laudo-titulo"><?php echo $titulo_laudo; ?></h1>
                <p class="laudo-subtitulo">Sistema de Refrigeração e Climatização</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="laudo-codigo">
                    <strong>Nº: LA-<?php echo str_pad($id, 6, '0', STR_PAD_LEFT); ?></strong><br>
                    <small>Data: <?php echo date('d/m/Y'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <hr class="laudo-divider">

    <!-- Dados do Cliente e Equipamento -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-circle"></i> IDENTIFICAÇÃO DO CLIENTE E EQUIPAMENTO</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-user"></i> DADOS DO CLIENTE</h6>
                    <table class="table table-sm table-borderless">
                        <tr><td width="120"><strong>Nome:</strong></td><td><?php echo htmlspecialchars($dados['cliente_nome']); ?></td></tr>
                        <tr><td><strong>Telefone:</strong></td><td><?php echo htmlspecialchars($dados['cliente_telefone']); ?></td></tr>
                        <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($dados['cliente_email']); ?></td></tr>
                        <tr><td><strong>Endereço:</strong></td>
                            <td><?php echo htmlspecialchars($dados['cliente_rua']) . ', ' . 
                                       htmlspecialchars($dados['cliente_numero']) . ' - ' . 
                                       htmlspecialchars($dados['cliente_bairro']) . ', ' . 
                                       htmlspecialchars($dados['cliente_cidade']); ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-snowflake"></i> DADOS DO EQUIPAMENTO</h6>
                    <table class="table table-sm table-borderless">
                        <tr><td width="140"><strong>Marca/Modelo:</strong></td><td><?php echo htmlspecialchars($dados['equipamento_marca'] ?? 'Não informado'); ?></td></tr>
                        <tr><td><strong>Capacidade (BTUs):</strong></td><td><?php echo htmlspecialchars($dados['equipamento_btus'] ?? 'Não informado'); ?> BTUs</td></tr>
                        <tr><td><strong>Tipo:</strong></td><td><?php echo htmlspecialchars($dados['equipamento_tipo'] ?? 'Split Parede'); ?></td></tr>
                        <tr><td><strong>Nº Série:</strong></td><td><input type="text" class="form-control form-control-sm d-inline w-50" placeholder="Informe o nº de série" id="numero_serie"></td></tr>
                        <tr><td><strong>Local Instalação:</strong></td><td><input type="text" class="form-control form-control-sm d-inline w-75" placeholder="Ex: Sala, Quarto, Escritório" id="local_instalacao"></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados do Serviço -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-tools"></i> DADOS DO SERVIÇO REALIZADO</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Data do Serviço:</strong><br>
                    <input type="date" class="form-control" id="data_servico" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <strong>Horário:</strong><br>
                    <input type="time" class="form-control" id="hora_servico" value="<?php echo date('H:i'); ?>">
                </div>
                <div class="col-md-4">
                    <strong>Técnico Responsável:</strong><br>
                    <input type="text" class="form-control" id="tecnico_responsavel" value="<?php echo $tecnico_responsavel; ?>">
                </div>
            </div>
            
            <div class="mt-3">
                <strong>Tipo de Serviço:</strong><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_servico" id="preventiva" value="preventiva" <?php echo $tipo_laudo == 'pmoc' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="preventiva">Manutenção Preventiva</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_servico" id="corretiva" value="corretiva" <?php echo $tipo_laudo == 'manutencao' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="corretiva">Manutenção Corretiva</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_servico" id="pmoc" value="pmoc" <?php echo $tipo_laudo == 'pmoc' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="pmoc">PMOC</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_servico" id="instalacao" value="instalacao">
                    <label class="form-check-label" for="instalacao">Instalação</label>
                </div>
            </div>
        </div>
    </div>

    <?php if($tipo_laudo == 'pmoc'): ?>
    <!-- SEÇÃO ESPECÍFICA PARA PMOC -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> CHECKLIST PMOC - PROCEDIMENTOS REALIZADOS</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-check-circle text-success"></i> UNIDADE INTERNA (EVAPORADORA)</h6>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc1" checked>
                        <label class="form-check-label" for="pmoc1">Limpeza completa do filtro de ar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc2" checked>
                        <label class="form-check-label" for="pmoc2">Limpeza da bandeja de condensado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc3" checked>
                        <label class="form-check-label" for="pmoc3">Limpeza das aletas do evaporador</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc4" checked>
                        <label class="form-check-label" for="pmoc4">Verificação do dreno e escoamento</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc5" checked>
                        <label class="form-check-label" for="pmoc5">Limpeza da turbina/ventilador</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6><i class="fas fa-check-circle text-success"></i> UNIDADE EXTERNA (CONDENSADORA)</h6>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc6" checked>
                        <label class="form-check-label" for="pmoc6">Limpeza completa do condensador</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc7" checked>
                        <label class="form-check-label" for="pmoc7">Verificação das aletas do condensador</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc8" checked>
                        <label class="form-check-label" for="pmoc8">Limpeza da turbina/ventilador</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc9" checked>
                        <label class="form-check-label" for="pmoc9">Verificação da base de fixação</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input pmoc-check" type="checkbox" id="pmoc10" checked>
                        <label class="form-check-label" for="pmoc10">Inspeção das conexões elétricas</label>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6><i class="fas fa-thermometer-half text-warning"></i> MEDIÇÕES E TESTES</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Temperatura Ambiente:</label>
                            <input type="text" class="form-control" id="temp_ambiente" value="24°C" placeholder="°C">
                        </div>
                        <div class="col-md-3">
                            <label>Temperatura de Saída:</label>
                            <input type="text" class="form-control" id="temp_saida" value="16°C" placeholder="°C">
                        </div>
                        <div class="col-md-3">
                            <label>Pressão de Baixa:</label>
                            <input type="text" class="form-control" id="pressao_baixa" value="65 PSI" placeholder="PSI">
                        </div>
                        <div class="col-md-3">
                            <label>Pressão de Alta:</label>
                            <input type="text" class="form-control" id="pressao_alta" value="250 PSI" placeholder="PSI">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- SEÇÃO PARA MANUTENÇÃO CORRETIVA -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-search"></i> DIAGNÓSTICO E PROCEDIMENTOS REALIZADOS</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label><strong>Problema Relatado pelo Cliente:</strong></label>
                <textarea class="form-control" id="problema_cliente" rows="3" placeholder="Descreva o problema relatado pelo cliente..."><?php echo htmlspecialchars($dados['descricao'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label><strong>Diagnóstico Técnico:</strong></label>
                <textarea class="form-control" id="diagnostico_tecnico" rows="3" placeholder="Descreva o diagnóstico encontrado..."></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-cogs"></i> PROCEDIMENTOS REALIZADOS</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proc1">
                        <label class="form-check-label" for="proc1">Verificação de pressão do gás</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proc2">
                        <label class="form-check-label" for="proc2">Teste de vazamento</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proc3">
                        <label class="form-check-label" for="proc3">Substituição de componentes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proc4">
                        <label class="form-check-label" for="proc4">Carga/Recarga de gás refrigerante</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proc5">
                        <label class="form-check-label" for="proc5">Limpeza do sistema</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6><i class="fas fa-exclamation-triangle text-danger"></i> PEÇAS SUBSTITUÍDAS</h6>
                    <?php if(count($materiais) > 0): ?>
                        <ul class="list-group">
                            <?php foreach($materiais as $material): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($material['nome']); ?> 
                                <span class="badge bg-secondary float-end"><?php echo $material['quantidade']; ?> <?php echo $material['unidade_medida']; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Nenhuma peça substituída</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Materiais e Serviços Utilizados -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> MATERIAIS E SERVIÇOS UTILIZADOS</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-box-open"></i> MATERIAIS UTILIZADOS</h6>
                    <?php if(count($materiais) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Qtd</th>
                                        <th>Unidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($materiais as $material): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($material['nome']); ?></td>
                                        <td><?php echo $material['quantidade']; ?></td>
                                        <td><?php echo htmlspecialchars($material['unidade_medida']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Nenhum material utilizado</p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <h6><i class="fas fa-concierge-bell"></i> SERVIÇOS REALIZADOS</h6>
                    <?php if(count($servicos) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Qtd</th>
                                        <th>Categoria</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($servicos as $servico): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                                        <td><?php echo $servico['quantidade'] ?? 1; ?></td>
                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($servico['categoria']); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Nenhum serviço registrado</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações e Recomendações -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-sticky-note"></i> OBSERVAÇÕES E RECOMENDAÇÕES</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label><strong>Observações Técnicas:</strong></label>
                <textarea class="form-control" id="observacoes_tecnicas" rows="3" placeholder="Descreva observações importantes sobre o serviço realizado..."></textarea>
            </div>
            
            <div class="mb-3">
                <label><strong>Recomendações ao Cliente:</strong></label>
                <textarea class="form-control" id="recomendacoes_cliente" rows="3" placeholder="Informe recomendações importantes para o cliente...">
• Manter os filtros limpos a cada 15 dias
• Não bloquear a saída de ar do equipamento
• Realizar manutenção preventiva a cada 6 meses
• Verificar regularmente o dreno de condensado
• Em caso de qualquer anormalidade, desligar o equipamento e entrar em contato
                </textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <label><strong>Próxima Manutenção Preventiva:</strong></label>
                    <input type="date" class="form-control" id="proxima_manutencao" value="<?php echo date('Y-m-d', strtotime('+6 months')); ?>">
                </div>
                <div class="col-md-6">
                    <label><strong>Garantia do Serviço:</strong></label>
                    <select class="form-control" id="garantia_servico">
                        <option value="30">30 dias</option>
                        <option value="60">60 dias</option>
                        <option value="90" selected>90 dias</option>
                        <option value="180">180 dias</option>
                        <option value="365">1 ano</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Assinaturas -->
    <div class="card laudo-section mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-signature"></i> ASSINATURAS E RESPONSABILIDADES</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6">
                    <div class="assinatura-box">
                        <h6>CLIENTE</h6>
                        <p class="text-muted mb-1">Declaro ter ciência e concordar com o serviço realizado</p>
                        <div class="linha-assinatura">_________________________________________</div>
                        <p class="mt-1"><strong><?php echo htmlspecialchars($dados['cliente_nome']); ?></strong></p>
                        <p class="text-muted small">CPF: _________________________</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="assinatura-box">
                        <h6>TÉCNICO RESPONSÁVEL</h6>
                        <p class="text-muted mb-1">Declaro ter realizado o serviço conforme normas técnicas</p>
                        <div class="linha-assinatura">_________________________________________</div>
                        <p class="mt-1"><strong id="nome_tecnico_display"><?php echo $tecnico_responsavel; ?></strong></p>
                        <p class="text-muted small">CREA/CRC: ___________________</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <strong><?php echo $site_nome; ?></strong><br>
                    <?php echo $site_telefone; ?> | <?php echo $site_email; ?><br>
                    CNPJ: _________________________ | Inscrição Municipal: _________________________
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos para o laudo -->
<style>
.laudo-container {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    max-width: 1200px;
    margin: 0 auto;
}

.laudo-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #2563eb;
}

.laudo-titulo {
    color: #1e3a8a;
    font-weight: 800;
    font-size: 28px;
    margin-bottom: 5px;
}

.laudo-subtitulo {
    color: #6b7280;
    font-size: 14px;
}

.laudo-codigo {
    background: #f8fafc;
    padding: 10px;
    border-radius: 8px;
    border: 2px solid #2563eb;
}

.laudo-divider {
    border: 0;
    height: 2px;
    background: linear-gradient(90deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
    margin: 30px 0;
}

.laudo-section {
    border: 1px solid #e2e8f0;
    border-left: 4px solid #2563eb;
}

.assinatura-box {
    padding: 20px;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.linha-assinatura {
    margin: 20px 0;
    color: #6b7280;
    font-size: 14px;
    letter-spacing: 2px;
}

@media print {
    .no-print, .page-header, .header-actions {
        display: none !important;
    }
    
    body {
        background: white;
        padding: 0;
        margin: 0;
    }
    
    .laudo-container {
        box-shadow: none;
        padding: 20px;
        margin: 0;
    }
    
    .form-control, .form-check-input {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    
    input[type="text"], 
    input[type="date"], 
    input[type="time"],
    textarea {
        border-bottom: 1px solid #ccc !important;
        background: transparent !important;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>

<script>
// Atualizar nome do técnico no display
document.getElementById('tecnico_responsavel').addEventListener('input', function() {
    document.getElementById('nome_tecnico_display').textContent = this.value;
});

// Função para imprimir o laudo
function imprimirLaudo() {
    window.print();
}

// Função para gerar PDF (simulação - você pode integrar com uma biblioteca PDF)
function downloadPDF() {
    alert('Para gerar PDF, você pode integrar com uma biblioteca como jsPDF ou usar um serviço de conversão HTML para PDF.');
    // Exemplo com jsPDF:
    // const { jsPDF } = window.jspdf;
    // const doc = new jsPDF();
    // doc.html(document.getElementById('conteudo-laudo'), {
    //     callback: function(doc) {
    //         doc.save('laudo-<?php echo $id; ?>.pdf');
    //     },
    //     margin: [10, 10, 10, 10],
    //     autoPaging: 'text',
    //     x: 0,
    //     y: 0,
    //     width: 190,
    //     windowWidth: 800
    // });
}

// Preencher automaticamente alguns campos
document.addEventListener('DOMContentLoaded', function() {
    // Se for PMOC, marcar todos os checkboxes
    if(document.getElementById('pmoc')) {
        document.querySelectorAll('.pmoc-check').forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    
    // Atualizar tipo de serviço quando alterado
    document.querySelectorAll('input[name="tipo_servico"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === 'pmoc' || this.value === 'preventiva') {
                // Habilitar seção PMOC
                document.querySelectorAll('.pmoc-check').forEach(cb => cb.checked = true);
            }
        });
    });
});
</script>

<?php include 'includes/footer-admin.php'; ?>