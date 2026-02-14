<?php
// admin/gerar_pdf_orcamento.php - VERSÃO COM DESIGN ATUALIZADO
include 'includes/auth.php';
include '../includes/config.php';

if(!isset($_GET['id']) || empty($_GET['id'])) die("ID inválido.");
$id = intval($_GET['id']);

try {
    // 1. Orçamento e Cliente
    $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, s.nome as servico_nome, s.preco_base as servico_preco FROM orcamentos o LEFT JOIN clientes c ON o.cliente_id = c.id LEFT JOIN servicos s ON o.servico_id = s.id WHERE o.id = ?");
    $stmt->execute([$id]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$orcamento) die("Orçamento não encontrado!");
    
    // 2. Materiais
    $stmt = $pdo->prepare("SELECT om.*, m.nome, m.unidade_medida, m.preco_unitario FROM orcamento_materiais om JOIN materiais m ON om.material_id = m.id WHERE om.orcamento_id = ?");
    $stmt->execute([$id]);
    $materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Serviços Adicionais  
    $stmt = $pdo->prepare("SELECT os.*, s.nome, s.preco_base FROM orcamento_servicos os JOIN servicos s ON os.servico_id = s.id WHERE os.orcamento_id = ?");
    $stmt->execute([$id]);
    $servicos_extras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. PRODUTOS PARA VENDA
    $stmt = $pdo->prepare("SELECT op.*, p.nome, p.preco, p.marca, p.btus FROM orcamento_produtos op JOIN produtos p ON op.produto_id = p.id WHERE op.orcamento_id = ?");
    $stmt->execute([$id]);
    $produtos_venda = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Configs
    $configs_site = $pdo->query("SELECT chave, valor FROM config_site")->fetchAll(PDO::FETCH_KEY_PAIR);
    $configs_taxas = $pdo->query("SELECT chave, valor FROM configuracoes WHERE chave IN ('taxa_cartao_avista', 'taxa_cartao_parcelado', 'chave_pix', 'horario_funcionamento')")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $site_nome = $configs_site['site_nome'] ?? 'N&M Refrigeração';
    $site_telefone = $configs_site['site_telefone'] ?? '';
    $cidades = $configs_site['cidades_atendidas'] ?? 'São José do Rio Preto';
    $cidade_principal = explode("\n", $cidades)[0];
    
    // Valores
    $valor_total = floatval($orcamento['valor_total']);
    $taxa_avista = floatval($configs_taxas['taxa_cartao_avista'] ?? 0);
    $taxa_parcelado = floatval($configs_taxas['taxa_cartao_parcelado'] ?? 0);
    $valor_avista = $valor_total * (1 + ($taxa_avista / 100));
    $valor_parcelado = $valor_total * (1 + ($taxa_parcelado / 100));

    // Logo
    $logo_path = '../assets/images/' . ($configs_site['site_logo'] ?? 'logo.png');
    $logo_base64 = null;
    if(file_exists($logo_path)) {
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $data = file_get_contents($logo_path);
        $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    
} catch(PDOException $e) { 
    die("Erro DB: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orçamento #<?php echo $id; ?></title>
    <style>
        /* DESIGN MELHORADO - MANTENDO TODAS AS FUNCIONALIDADES */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            padding: 25px; 
            color: #2d3748; 
            font-size: 13px;
            background: #f8fafc;
        }
        
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        
        /* Cabeçalho melhorado */
        .header { 
            text-align: center; 
            border-bottom: 3px solid #2563eb; 
            padding-bottom: 20px; 
            margin-bottom: 25px;
            position: relative;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
        }
        
        .logo { 
            max-height: 70px; 
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .header h1 { 
            margin: 5px 0; 
            font-size: 24px; 
            color: #1e3a8a;
            font-weight: 700;
        }
        
        .header p { 
            margin: 5px 0; 
            color: #4b5563;
            font-size: 14px;
        }
        
        /* Grid melhorado */
        .grid-2col { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
            margin-bottom: 20px; 
        }
        
        /* Cards melhorados */
        .card { 
            background: white; 
            padding: 18px;
            border-radius: 8px; 
            border-left: 4px solid #2563eb;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card h3 { 
            margin: 0 0 12px 0; 
            font-size: 15px; 
            color: #2563eb; 
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 6px;
            font-weight: 600;
        }
        
        .info-item { 
            display: flex; 
            justify-content: space-between; 
            margin: 6px 0; 
            padding: 4px 0;
        }
        
        .label { 
            font-weight: 600; 
            color: #4a5568; 
        }
        
        /* Tabelas melhoradas */
        .table-compact { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
            font-size: 11px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .table-compact thead {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        }
        
        .table-compact th { 
            color: white; 
            padding: 8px 6px; 
            text-align: left; 
            font-weight: 600;
            font-size: 11px;
        }
        
        .table-compact tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .table-compact tbody tr:hover {
            background-color: #eff6ff;
        }
        
        .table-compact td { 
            padding: 6px; 
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Valores melhorados */
        .valores-compact { 
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
            padding: 20px; 
            border: 2px solid #38bdf8;
            border-radius: 8px; 
            text-align: center; 
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .valores-compact > div:first-child {
            font-size: 16px;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 10px;
        }
        
        .valor-total { 
            font-size: 28px; 
            font-weight: bold; 
            color: #2563eb;
        }
        
        /* Pagamento melhorado */
        .pagamento-compact { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
            padding: 20px; 
            border-radius: 8px; 
            margin-top: 20px;
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px;
            border: 2px solid #fbbf24;
        }
        
        .pag-box { 
            background: white; 
            padding: 15px;
            border-radius: 6px; 
            text-align: center; 
            border-left: 4px solid #10b981;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }
        
        .pag-box.card { 
            border-left-color: #f59e0b; 
        }
        
        .pag-box strong {
            display: block;
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .pag-box span {
            display: block;
            color: #10b981;
            font-weight: bold; 
            font-size: 18px;
            margin: 8px 0;
        }
        
        .pag-box.card span {
            color: #f59e0b;
        }
        
        /* Footer melhorado */
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 11px; 
            color: #6b7280; 
            border-top: 2px solid #e5e7eb; 
            padding-top: 15px;
            padding-bottom: 5px;
        }
        
        .footer p {
            margin: 4px 0;
        }
        
        /* Botão imprimir melhorado */
        .no-print { 
            position: fixed; 
            top: 20px; 
            right: 20px; 
            padding: 10px 20px; 
            background: #2563eb; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .no-print:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
        }
        
        /* Observações melhoradas */
        .observacoes-inline {
            margin-top: 10px;
            font-size: 11px;
            background: #fef3c7;
            padding: 8px 10px;
            border-radius: 4px;
            border-left: 3px solid #f59e0b;
        }
        
        .observacoes-inline strong {
            color: #92400e;
        }
        
        /* Badges para melhor visualização */
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
        }
        
        /* Para impressão */
        @media print { 
            .no-print { 
                display: none; 
            }
            
            body { 
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: none;
                border-radius: 0;
                padding: 20px;
            }
            
            .card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }
        }
        
        /* Responsividade */
        @media (max-width: 850px) {
            .grid-2col {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .pagamento-compact {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Imprimir Orçamento</button>
    
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <?php if($logo_base64): ?>
                <img src="<?php echo $logo_base64; ?>" class="logo" alt="Logo">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($site_nome); ?></h1>
            <p>WhatsApp: <?php echo htmlspecialchars($site_telefone); ?> | <?php echo htmlspecialchars($cidade_principal); ?></p>
        </div>

        <!-- Grid 2 colunas -->
        <div class="grid-2col">
            <div class="card">
                <h3>📋 DADOS DO ORÇAMENTO</h3>
                <div class="info-item"><span class="label">Nº:</span><span>#<?php echo $id; ?></span></div>
                <div class="info-item"><span class="label">Data:</span><span><?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?></span></div>
                <div class="info-item"><span class="label">Validade:</span><span>15 dias</span></div>
            </div>
            
            <div class="card">
                <h3>👤 CLIENTE</h3>
                <div class="info-item"><span class="label">Nome:</span><span><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></span></div>
                <div class="info-item"><span class="label">Tel:</span><span><?php echo htmlspecialchars($orcamento['cliente_telefone']); ?></span></div>
            </div>
        </div>

        <!-- Serviço -->
        <div class="card">
            <h3>🔧 SERVIÇO</h3>
            <div class="info-item">
                <span class="label">Tipo:</span>
                <span><?php echo htmlspecialchars($orcamento['servico_nome']); ?></span>
            </div>
            <?php if(!empty($orcamento['equipamento_marca'])): ?>
            <div class="info-item">
                <span class="label">Equip.:</span>
                <span>
                    <?php echo htmlspecialchars($orcamento['equipamento_marca']); ?> 
                    <?php if(!empty($orcamento['equipamento_btus'])): ?>
                        <?php echo htmlspecialchars($orcamento['equipamento_btus']); ?> BTUs
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if(!empty($orcamento['descricao'])): ?>
            <div class="observacoes-inline">
                <strong>Obs:</strong> <?php echo nl2br(htmlspecialchars($orcamento['descricao'])); ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- PRODUTOS -->
        <?php if(count($produtos_venda) > 0): ?>
        <div class="card">
            <h3>🛒 PRODUTOS PARA VENDA</h3>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th width="60">Qtd</th>
                        <th width="80">Preço Unit.</th>
                        <th width="80">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_produtos = 0;
                    foreach($produtos_venda as $p): 
                        $quantidade = isset($p['quantidade']) ? floatval($p['quantidade']) : 1;
                        $preco_unitario = isset($p['preco']) ? floatval($p['preco']) : 0;
                        $subtotal = $preco_unitario * $quantidade;
                        $total_produtos += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div><strong><?php echo htmlspecialchars($p['nome']); ?></strong></div>
                            <div style="font-size:10px;">
                                <?php if(!empty($p['marca'])): ?>
                                    <span class="badge-info"><?php echo htmlspecialchars($p['marca']); ?></span>
                                <?php endif; ?>
                                <?php if(!empty($p['btus'])): ?>
                                    <span class="badge-info"><?php echo htmlspecialchars($p['btus']); ?> BTUs</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center"><?php echo $quantidade; ?> un</td>
                        <td class="text-end">R$ <?php echo number_format($preco_unitario, 2, ',', '.'); ?></td>
                        <td class="text-end">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="border-top: 1px solid #ddd;">
                        <td colspan="3" class="text-end"><strong>Total Produtos:</strong></td>
                        <td class="text-end"><strong>R$ <?php echo number_format($total_produtos, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- MATERIAIS -->
        <?php if(count($materiais) > 0): ?>
        <div class="card">
            <h3>📦 MATERIAIS INCLUSOS</h3>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="50">Qtd</th>
                        <th width="80">Unidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_materiais = 0;
                    foreach($materiais as $m): 
                        $total_item = isset($m['preco_unitario']) ? floatval($m['preco_unitario']) * floatval($m['quantidade']) : 0;
                        $total_materiais += $total_item;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['nome']); ?></td>
                        <td><?php echo $m['quantidade']; ?></td>
                        <td><?php echo htmlspecialchars($m['unidade_medida']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- SERVIÇOS ADICIONAIS -->
        <?php if(count($servicos_extras) > 0): ?>
        <div class="card">
            <h3>🛠️ SERVIÇOS ADICIONAIS</h3>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th width="50">Qtd</th>
                        <th width="80">Valor Unit.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_servicos = 0;
                    foreach($servicos_extras as $s): 
                        $quantidade = isset($s['quantidade']) ? intval($s['quantidade']) : 1;
                        $preco_unitario = isset($s['preco_base']) ? floatval($s['preco_base']) : 0;
                        $total_servico = $preco_unitario * $quantidade;
                        $total_servicos += $total_servico;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['nome']); ?></td>
                        <td><?php echo $quantidade; ?></td>
                        <td>R$ <?php echo number_format($preco_unitario, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- VALORES -->
        <div class="valores-compact">
            <div>VALOR TOTAL</div>
            <div class="valor-total">R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></div>
        </div>

        <!-- PAGAMENTO -->
        <div class="pagamento-compact">
            <div class="pag-box">
                <strong>À VISTA (PIX/DINHEIRO)</strong><br>
                <span>R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></span><br>
                <small>PIX: <?php echo htmlspecialchars($configs_taxas['chave_pix'] ?? ''); ?></small>
            </div>
            <div class="pag-box card">
                <strong>CARTÃO (ATÉ 12x)</strong><br>
                <span>R$ <?php echo number_format($valor_parcelado, 2, ',', '.'); ?></span><br>
                <small>(Inclui taxas da máquina)</small>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Garantia de 90 dias sobre a mão de obra. | <?php echo htmlspecialchars($configs_taxas['horario_funcionamento'] ?? ''); ?></p>
            <p>Emitido em <?php echo date('d/m/Y H:i'); ?></p>
        </div>
    </div>
    
    <script>
        if(window.location.search.indexOf('preview') === -1) {
            setTimeout(() => window.print(), 800);
        }
    </script>
</body>
</html>