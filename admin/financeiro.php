<?php
// admin/financeiro.php
session_start();
include '../includes/config.php';
include 'includes/header-admin.php';


// Verificar se o admin está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] != true) {
    header('Location: login.php');
    exit;
}

// Definir período padrão (últimos 30 dias)
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d', strtotime('-30 days'));
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');

// Converter datas para formato do banco
$data_inicio_db = $data_inicio . ' 00:00:00';
$data_fim_db = $data_fim . ' 23:59:59';

// ========== CÁLCULO DE RECEITAS ==========
// Receitas de serviços
$sql_receitas_servicos = "
    SELECT 
        SUM(o.valor_total) as total_servicos,
        COUNT(*) as qtd_servicos
    FROM orcamentos o
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    AND o.valor_total > 0
";
$stmt = $pdo->prepare($sql_receitas_servicos);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$receitas_servicos = $stmt->fetch(PDO::FETCH_ASSOC);

// Receitas de produtos vendidos
$sql_receitas_produtos = "
    SELECT 
        SUM(op.quantidade * p.preco) as total_produtos,
        COUNT(DISTINCT op.orcamento_id) as qtd_vendas,
        SUM(op.quantidade) as qtd_itens
    FROM orcamento_produtos op
    INNER JOIN produtos p ON op.produto_id = p.id
    INNER JOIN orcamentos o ON op.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
";
$stmt = $pdo->prepare($sql_receitas_produtos);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$receitas_produtos = $stmt->fetch(PDO::FETCH_ASSOC);

// Receitas por tipo de serviço
$sql_receitas_por_servico = "
    SELECT 
        s.nome,
        COUNT(os.servico_id) as quantidade,
        SUM(CASE 
            WHEN os.quantidade > 1 THEN os.quantidade 
            ELSE 1 
        END) as total_unidades,
        ROUND(AVG(o.valor_total), 2) as valor_medio,
        SUM(o.valor_total) as total_receita
    FROM orcamento_servicos os
    INNER JOIN servicos s ON os.servico_id = s.id
    INNER JOIN orcamentos o ON os.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    GROUP BY os.servico_id
    ORDER BY total_receita DESC
";
$stmt = $pdo->prepare($sql_receitas_por_servico);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$receitas_por_servico = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========== CÁLCULO DE DESPESAS ==========
// Despesas com materiais (baseado nos materiais usados nos orçamentos concluídos)
$sql_despesas_materiais = "
    SELECT 
        SUM(om.quantidade * m.preco_unitario) as total_materiais,
        COUNT(DISTINCT om.material_id) as tipos_materiais,
        SUM(om.quantidade) as qtd_total_materiais
    FROM orcamento_materiais om
    INNER JOIN materiais m ON om.material_id = m.id
    INNER JOIN orcamentos o ON om.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
";
$stmt = $pdo->prepare($sql_despesas_materiais);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$despesas_materiais = $stmt->fetch(PDO::FETCH_ASSOC);

// Lista detalhada de materiais usados
$sql_materiais_detalhados = "
    SELECT 
        m.nome,
        m.categoria,
        m.unidade_medida,
        SUM(om.quantidade) as quantidade_total,
        m.preco_unitario,
        SUM(om.quantidade * m.preco_unitario) as total
    FROM orcamento_materiais om
    INNER JOIN materiais m ON om.material_id = m.id
    INNER JOIN orcamentos o ON om.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    GROUP BY om.material_id
    ORDER BY total DESC
";
$stmt = $pdo->prepare($sql_materiais_detalhados);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$materiais_detalhados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========== CÁLCULO DE LUCRO ==========
$total_receitas = ($receitas_servicos['total_servicos'] ?? 0) + ($receitas_produtos['total_produtos'] ?? 0);
$total_despesas = $despesas_materiais['total_materiais'] ?? 0;
$lucro_liquido = $total_receitas - $total_despesas;
$margem_lucro = $total_receitas > 0 ? ($lucro_liquido / $total_receitas) * 100 : 0;

// ========== ESTATÍSTICAS POR PERÍODO ==========
// Receita diária nos últimos 7 dias
$sql_receita_diaria = "
    SELECT 
        DATE(o.data_solicitacao) as data,
        COUNT(*) as quantidade,
        SUM(o.valor_total) as total_diario
    FROM orcamentos o
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(o.data_solicitacao)
    ORDER BY data ASC
";
$stmt = $pdo->prepare($sql_receita_diaria);
$stmt->execute();
$receita_diaria = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========== RESUMO GERAL ==========
// Total de todos os tempos
$sql_total_geral = "
    SELECT 
        SUM(CASE WHEN status IN ('concluido', 'aprovado') THEN valor_total ELSE 0 END) as receita_total,
        COUNT(CASE WHEN status IN ('concluido', 'aprovado') THEN 1 END) as servicos_concluidos,
        COUNT(*) as total_orcamentos
    FROM orcamentos
";
$stmt = $pdo->prepare($sql_total_geral);
$stmt->execute();
$total_geral = $stmt->fetch(PDO::FETCH_ASSOC);

// Top 5 clientes que mais geraram receita
$sql_top_clientes = "
    SELECT 
        c.nome,
        c.telefone,
        COUNT(o.id) as total_servicos,
        SUM(o.valor_total) as total_gasto
    FROM orcamentos o
    INNER JOIN clientes c ON o.cliente_id = c.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    GROUP BY o.cliente_id
    ORDER BY total_gasto DESC
    LIMIT 5
";
$stmt = $pdo->prepare($sql_top_clientes);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$top_clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Serviços mais lucrativos
$sql_servicos_lucrativos = "
    SELECT 
        s.nome,
        s.preco_base,
        COUNT(os.servico_id) as vezes_executado,
        SUM(o.valor_total) as total_gerado
    FROM orcamento_servicos os
    INNER JOIN servicos s ON os.servico_id = s.id
    INNER JOIN orcamentos o ON os.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    GROUP BY os.servico_id
    ORDER BY total_gerado DESC
    LIMIT 10
";
$stmt = $pdo->prepare($sql_servicos_lucrativos);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$servicos_lucrativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========== ESTOQUE E INVESTIMENTO ==========
// Valor total do estoque
$sql_valor_estoque = "
    SELECT 
        SUM(estoque * preco_unitario) as valor_total_estoque,
        COUNT(*) as itens_estoque,
        SUM(CASE WHEN estoque <= estoque_minimo THEN 1 ELSE 0 END) as itens_baixos
    FROM materiais
    WHERE ativo = 1
";
$stmt = $pdo->prepare($sql_valor_estoque);
$stmt->execute();
$estoque_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Materiais mais usados
$sql_materiais_mais_usados = "
    SELECT 
        m.nome,
        m.categoria,
        SUM(om.quantidade) as quantidade_usada,
        SUM(om.quantidade * m.preco_unitario) as valor_usado
    FROM orcamento_materiais om
    INNER JOIN materiais m ON om.material_id = m.id
    INNER JOIN orcamentos o ON om.orcamento_id = o.id
    WHERE o.status IN ('concluido', 'aprovado')
    AND o.data_solicitacao BETWEEN ? AND ?
    GROUP BY om.material_id
    ORDER BY quantidade_usada DESC
    LIMIT 10
";
$stmt = $pdo->prepare($sql_materiais_mais_usados);
$stmt->execute([$data_inicio_db, $data_fim_db]);
$materiais_mais_usados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard {
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receita { color: #28a745; }
        .despesa { color: #dc3545; }
        .lucro { color: #17a2b8; }
        .margem { color: #ffc107; }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        .admin-header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav ul {
            display: flex;
            list-style: none;
            flex-wrap: wrap;
        }
        .admin-nav ul li {
            margin-left: 15px;
        }
        .admin-nav ul li a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .admin-nav ul li a:hover {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .filter-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .filter-form .form-group {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .filter-form input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }
        .filter-form button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-form button:hover {
            background: var(--primary-dark);
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .section h3 {
            margin-bottom: 20px;
            color: var(--dark);
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 10px;
        }
        
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .info-box h4 {
            margin-bottom: 15px;
            color: var(--dark);
        }
    </style>
</head>
<body>
    

    <div class="container dashboard">
        <h2>Financeiro</h2>
        <p>Análise financeira completa do período selecionado</p>
        
        <!-- Filtro por período -->
        <div class="filter-form">
            <form method="GET" action="">
                <div class="form-group">
                    <div>
                        <label for="data_inicio">Data Início:</label>
                        <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>" required>
                    </div>
                    <div>
                        <label for="data_fim">Data Fim:</label>
                        <input type="date" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>" required>
                    </div>
                    <div>
                        <button type="submit">Filtrar</button>
                        <a href="financeiro.php" style="margin-left: 10px; padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Limpar</a>
                    </div>
                </div>
            </form>
            <p style="margin-top: 10px; color: #666; font-size: 0.9rem;">
                Período: <?php echo date('d/m/Y', strtotime($data_inicio)); ?> à <?php echo date('d/m/Y', strtotime($data_fim)); ?>
            </p>
        </div>

        <!-- Resumo Financeiro -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number receita">R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?></div>
                <div class="stat-label">Receita Total</div>
                <small>
                    Serviços: R$ <?php echo number_format($receitas_servicos['total_servicos'] ?? 0, 2, ',', '.'); ?><br>
                    Produtos: R$ <?php echo number_format($receitas_produtos['total_produtos'] ?? 0, 2, ',', '.'); ?>
                </small>
            </div>
            
            <div class="stat-card">
                <div class="stat-number despesa">R$ <?php echo number_format($total_despesas, 2, ',', '.'); ?></div>
                <div class="stat-label">Despesas com Materiais</div>
                <small>
                    <?php echo $despesas_materiais['tipos_materiais'] ?? 0; ?> tipos de materiais<br>
                    <?php echo $despesas_materiais['qtd_total_materiais'] ?? 0; ?> unidades
                </small>
            </div>
            
            <div class="stat-card">
                <div class="stat-number lucro">R$ <?php echo number_format($lucro_liquido, 2, ',', '.'); ?></div>
                <div class="stat-label">Lucro Líquido</div>
                <small>
                    Margem: <?php echo number_format($margem_lucro, 1, ',', '.'); ?>%<br>
                    <?php echo ($lucro_liquido >= 0) ? '✅ Positivo' : '❌ Negativo'; ?>
                </small>
            </div>
            
            <div class="stat-card">
                <div class="stat-number margem"><?php echo number_format($margem_lucro, 1, ',', '.'); ?>%</div>
                <div class="stat-label">Margem de Lucro</div>
                <small>
                    Baseado no período<br>
                    <?php echo ($margem_lucro >= 20) ? '🎉 Excelente' : (($margem_lucro >= 10) ? '👍 Boa' : '⚠️ Atenção'); ?>
                </small>
            </div>
        </div>

        <!-- Gráfico de Receitas por Serviço -->
        <div class="section">
            <h3>📊 Receitas por Tipo de Serviço</h3>
            <div class="chart-container">
                <canvas id="chartReceitasServicos"></canvas>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Quantidade</th>
                        <th>Valor Médio</th>
                        <th>Total Receita</th>
                        <th>% do Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receitas_por_servico as $servico): ?>
                        <?php 
                        $percentual = $total_receitas > 0 ? ($servico['total_receita'] / $total_receitas) * 100 : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                            <td><?php echo $servico['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($servico['valor_medio'], 2, ',', '.'); ?></td>
                            <td><strong>R$ <?php echo number_format($servico['total_receita'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 100px; background: #e9ecef; height: 8px; border-radius: 4px;">
                                        <div style="width: <?php echo $percentual; ?>%; background: var(--primary); height: 100%; border-radius: 4px;"></div>
                                    </div>
                                    <?php echo number_format($percentual, 1); ?>%
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Despesas Detalhadas com Materiais -->
        <div class="section">
            <h3>📦 Despesas com Materiais</h3>
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Categoria</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Total Gasto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materiais_detalhados as $material): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($material['nome']); ?></td>
                            <td><span class="badge badge-info"><?php echo $material['categoria']; ?></span></td>
                            <td><?php echo number_format($material['quantidade_total'], 2, ',', '.'); ?> <?php echo $material['unidade_medida']; ?></td>
                            <td>R$ <?php echo number_format($material['preco_unitario'], 2, ',', '.'); ?></td>
                            <td><strong class="despesa">R$ <?php echo number_format($material['total'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Clientes e Serviços -->
        <div class="summary-grid">
            <div class="info-box">
                <h4>🏆 Top 5 Clientes</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviços</th>
                            <th>Total Gasto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_clientes as $cliente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo $cliente['total_servicos']; ?></td>
                                <td><strong class="receita">R$ <?php echo number_format($cliente['total_gasto'], 2, ',', '.'); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="info-box">
                <h4>🚀 Serviços Mais Lucrativos</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Execuções</th>
                            <th>Total Gerado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicos_lucrativos as $servico): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                                <td><?php echo $servico['vezes_executado']; ?></td>
                                <td><strong class="receita">R$ <?php echo number_format($servico['total_gerado'], 2, ',', '.'); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Informações de Estoque -->
        <div class="section">
            <h3>📦 Situação do Estoque</h3>
            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4 style="margin-top: 0; color: var(--dark);">Valor Total em Estoque</h4>
                    <div style="font-size: 1.8rem; font-weight: bold; color: #17a2b8;">
                        R$ <?php echo number_format($estoque_info['valor_total_estoque'] ?? 0, 2, ',', '.'); ?>
                    </div>
                    <small><?php echo $estoque_info['itens_estoque'] ?? 0; ?> itens cadastrados</small>
                </div>
                
                <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4 style="margin-top: 0; color: var(--dark);">Materiais Críticos</h4>
                    <div style="font-size: 1.8rem; font-weight: bold; color: <?php echo ($estoque_info['itens_baixos'] ?? 0) > 0 ? '#dc3545' : '#28a745'; ?>;">
                        <?php echo $estoque_info['itens_baixos'] ?? 0; ?> itens
                    </div>
                    <small>com estoque abaixo do mínimo</small>
                </div>
                
                <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4 style="margin-top: 0; color: var(--dark);">Materiais Mais Usados</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php 
                        $count = 0;
                        foreach ($materiais_mais_usados as $material):
                            if ($count++ < 3):
                        ?>
                            <li><?php echo htmlspecialchars($material['nome']); ?> (<?php echo $material['quantidade_usada']; ?> un)</li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Resumo Geral -->
        <div class="section">
            <h3>📈 Resumo Geral (Todos os Tempos)</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);">
                        R$ <?php echo number_format($total_geral['receita_total'] ?? 0, 2, ',', '.'); ?>
                    </div>
                    <div style="color: #666;">Receita Total</div>
                </div>
                
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                        <?php echo $total_geral['servicos_concluidos'] ?? 0; ?>
                    </div>
                    <div style="color: #666;">Serviços Concluídos</div>
                </div>
                
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #17a2b8;">
                        <?php echo $total_geral['total_orcamentos'] ?? 0; ?>
                    </div>
                    <div style="color: #666;">Total de Orçamentos</div>
                </div>
                
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #ffc107;">
                        <?php echo $total_geral['total_orcamentos'] > 0 ? number_format(($total_geral['servicos_concluidos'] / $total_geral['total_orcamentos']) * 100, 1) : 0; ?>%
                    </div>
                    <div style="color: #666;">Taxa de Conversão</div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Gráfico de Receitas por Serviço
    const ctxReceitas = document.getElementById('chartReceitasServicos').getContext('2d');
    
    // Preparar dados para o gráfico
    const servicoLabels = <?php echo json_encode(array_column($receitas_por_servico, 'nome')); ?>;
    const servicoValores = <?php echo json_encode(array_column($receitas_por_servico, 'total_receita')); ?>;
    
    new Chart(ctxReceitas, {
        type: 'doughnut',
        data: {
            labels: servicoLabels,
            datasets: [{
                label: 'Receitas por Serviço',
                data: servicoValores,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
                    '#6A4C93', '#F15BB5'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Distribuição de Receitas por Serviço'
                }
            }
        }
    });
    
    // Função para exportar relatório
    function exportarRelatorio() {
        const data = {
            periodo: {
                inicio: '<?php echo $data_inicio; ?>',
                fim: '<?php echo $data_fim; ?>'
            },
            resumo: {
                receitaTotal: <?php echo $total_receitas; ?>,
                despesas: <?php echo $total_despesas; ?>,
                lucro: <?php echo $lucro_liquido; ?>,
                margem: <?php echo $margem_lucro; ?>
            },
            servicos: <?php echo json_encode($receitas_por_servico); ?>,
            materiais: <?php echo json_encode($materiais_detalhados); ?>,
            topClientes: <?php echo json_encode($top_clientes); ?>
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `relatorio_financeiro_<?php echo $data_inicio; ?>_<?php echo $data_fim; ?>.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    
    // Botão de exportação
    const exportBtn = document.createElement('button');
    exportBtn.innerHTML = '📥 Exportar Relatório';
    exportBtn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.2); z-index: 1000;';
    exportBtn.onclick = exportarRelatorio;
    document.body.appendChild(exportBtn);
    </script>
</body>
</html>