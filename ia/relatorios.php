<?php
// ia/relatorios.php
session_start();
include '../includes/config.php';

// Verificar se está logado
if(!isset($_SESSION['admin_logado']) && !isset($_SESSION['tecnico_logado'])) {
    header('Location: ../admin/login.php');
    exit;
}

// Gerar PDF
if(isset($_GET['gerar_pdf']) && isset($_SESSION['diagnostico'])) {
    require_once('../includes/tcpdf/tcpdf.php');
    
    $diagnostico = $_SESSION['diagnostico'];
    $resultado = $diagnostico['resultado_ia'];
    $checklist = $diagnostico['checklist'];
    
    // Criar novo PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Informações do documento
    $pdf->SetCreator('ClimaTech - Sistema de Diagnóstico');
    $pdf->SetAuthor('Técnico ClimaTech');
    $pdf->SetTitle('Relatório de Diagnóstico - ' . $diagnostico['marca']);
    $pdf->SetSubject('Diagnóstico Técnico');
    
    // Margens
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Quebra de página automática
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Adicionar página
    $pdf->AddPage();
    
    // Logo e cabeçalho
    $html = '
    <style>
        .header { 
            border-bottom: 2px solid #0066cc; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .title { 
            color: #0066cc; 
            font-size: 20px; 
            font-weight: bold; 
        }
        .subtitle { 
            color: #666; 
            font-size: 14px; 
        }
        .section { 
            margin: 15px 0; 
            padding: 10px; 
            background: #f8f9fa; 
            border-left: 4px solid #0066cc; 
        }
        .problema { 
            background: #fff3cd; 
            padding: 8px; 
            margin: 5px 0; 
            border-radius: 3px; 
        }
        .solucao { 
            background: #d1edff; 
            padding: 8px; 
            margin: 5px 0; 
            border-radius: 3px; 
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
        }
        .table td, .table th { 
            border: 1px solid #ddd; 
            padding: 8px; 
        }
        .table th { 
            background: #0066cc; 
            color: white; 
        }
    </style>
    
    <div class="header">
        <h1 class="title">🌡️ ClimaTech - Relatório de Diagnóstico</h1>
        <p class="subtitle">Sistema Inteligente de Análise Técnica</p>
        <p>Data: ' . date('d/m/Y H:i') . '</p>
    </div>
    ';
    
    // Informações do Equipamento
    $html .= '
    <div class="section">
        <h2>📋 Informações do Equipamento</h2>
        <table class="table">
            <tr>
                <td><strong>Marca:</strong></td>
                <td>' . $diagnostico['marca'] . '</td>
                <td><strong>BTUs:</strong></td>
                <td>' . $diagnostico['btus'] . '</td>
            </tr>
            <tr>
                <td><strong>Modelo:</strong></td>
                <td>' . ($diagnostico['modelo'] ?: 'Não informado') . '</td>
                <td><strong>Tipo:</strong></td>
                <td>' . $diagnostico['tipo_equipamento'] . '</td>
            </tr>
            <tr>
                <td><strong>Sintoma:</strong></td>
                <td colspan="3">' . $diagnostico['sintoma'] . '</td>
            </tr>
            ' . ($diagnostico['codigo_erro'] ? '
            <tr>
                <td><strong>Código de Erro:</strong></td>
                <td colspan="3">' . $diagnostico['codigo_erro'] . '</td>
            </tr>' : '') . '
        </table>
    </div>
    ';
    
    // Diagnóstico Final
    $gravidade_cor = [
        'alta' => '#dc3545',
        'media' => '#ffc107', 
        'baixa' => '#28a745'
    ];
    
    $html .= '
    <div class="section" style="border-left-color: ' . $gravidade_cor[$resultado['gravidade']] . '">
        <h2>🎯 Diagnóstico Final</h2>
        <p><strong>Resultado:</strong> ' . $resultado['diagnostico_final'] . '</p>
        <p><strong>Gravidade:</strong> <span style="color: ' . $gravidade_cor[$resultado['gravidade']] . '">' . 
        ucfirst($resultado['gravidade']) . '</span></p>
    </div>
    ';
    
    // Problemas Identificados
    if(count($resultado['problemas']) > 0) {
        $html .= '
        <div class="section">
            <h2>⚠️ Problemas Identificados</h2>
        ';
        
        foreach($resultado['problemas'] as $problema) {
            $html .= '<div class="problema">' . $problema . '</div>';
        }
        
        $html .= '</div>';
    }
    
    // Soluções Recomendadas
    if(count($resultado['solucoes']) > 0) {
        $html .= '
        <div class="section">
            <h2>🔧 Soluções Recomendadas</h2>
        ';
        
        foreach($resultado['solucoes'] as $solucao) {
            $html .= '<div class="solucao">' . $solucao . '</div>';
        }
        
        $html .= '</div>';
    }
    
    // Dados Técnicos Coletados
    $html .= '
    <div class="section">
        <h2>📊 Dados Técnicos Coletados</h2>
        <table class="table">
            <tr>
                <th>Parâmetro</th>
                <th>Valor</th>
                <th>Observação</th>
            </tr>
    ';
    
    // Temperaturas
    if(isset($checklist['temp_entrada']) && isset($checklist['temp_saida'])) {
        $delta_t = $checklist['temp_entrada'] - $checklist['temp_saida'];
        $status_delta_t = $delta_t >= 8 && $delta_t <= 15 ? '✅ Ideal' : '⚠️ Fora do ideal';
        
        $html .= '
            <tr>
                <td>ΔT (Diferença de Temperatura)</td>
                <td>' . $delta_t . '°C</td>
                <td>' . $status_delta_t . '</td>
            </tr>
        ';
    }
    
    // Tensão
    if(isset($checklist['tensao_alimentacao'])) {
        $status_tensao = $checklist['tensao_alimentacao'] >= 200 && $checklist['tensao_alimentacao'] <= 240 ? '✅ OK' : '⚠️ Verificar';
        
        $html .= '
            <tr>
                <td>Tensão de Alimentação</td>
                <td>' . $checklist['tensao_alimentacao'] . 'V</td>
                <td>' . $status_tensao . '</td>
            </tr>
        ';
    }
    
    // Corrente
    if(isset($checklist['corrente_compressor'])) {
        $html .= '
            <tr>
                <td>Corrente do Compressor</td>
                <td>' . $checklist['corrente_compressor'] . 'A</td>
                <td>-</td>
            </tr>
        ';
    }
    
    // Isolamento
    if(isset($checklist['teste_isolamento'])) {
        $status_isolamento = $checklist['teste_isolamento'] >= 2 ? '✅ Adequado' : '⚠️ Insuficiente';
        
        $html .= '
            <tr>
                <td>Isolamento Elétrico</td>
                <td>' . $checklist['teste_isolamento'] . 'MΩ</td>
                <td>' . $status_isolamento . '</td>
            </tr>
        ';
    }
    
    $html .= '
        </table>
    </div>
    ';
    
    // Recomendações
    if(count($resultado['recomendacoes']) > 0) {
        $html .= '
        <div class="section">
            <h2>💡 Recomendações</h2>
            <ul>
        ';
        
        foreach($resultado['recomendacoes'] as $recomendacao) {
            $html .= '<li>' . $recomendacao . '</li>';
        }
        
        $html .= '
            </ul>
        </div>
        ';
    }
    
    // Observações Finais
    $html .= '
    <div class="section">
        <h2>📝 Observações Finais</h2>
        <p>Este relatório foi gerado automaticamente pelo Sistema de Diagnóstico por IA da ClimaTech.</p>
        <p>Recomendamos a execução dos serviços por técnicos qualificados e a utilização de peças originais.</p>
        <p><strong>Data do Diagnóstico:</strong> ' . $diagnostico['data_inicio'] . '</p>
    </div>
    
    <div style="text-align: center; margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd;">
        <p><strong>ClimaTech - Especialistas em Climatização</strong></p>
        <p>📞 (11) 9999-9999 | ✉️ contato@climatech.com.br</p>
        <p>www.climatech.com.br</p>
    </div>
    ';
    
    // Gerar PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Saída do PDF
    $pdf->Output('diagnostico_' . $diagnostico['marca'] . '_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}

// Se não for para gerar PDF, mostrar página normal
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - ClimaTech</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <?php include '../admin/includes/header-admin.php'; ?>
    
    <div class="admin-main">
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../admin/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="diagnostico.php">🔍 Diagnóstico IA</a></li>
                    <li><a href="checklist.php">📋 Checklist</a></li>
                    <li><a href="relatorios.php" class="active">📄 Relatórios</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-content">
            <div class="page-header">
                <h2>📄 Relatórios de Diagnóstico</h2>
                <p>Gerar e gerenciar relatórios técnicos</p>
            </div>
            
            <?php if(isset($_SESSION['diagnostico'])): ?>
            <div class="card">
                <h3>Último Diagnóstico</h3>
                <p>Equipamento: <strong><?php echo $_SESSION['diagnostico']['marca']; ?> <?php echo $_SESSION['diagnostico']['btus']; ?> BTUs</strong></p>
                <p>Data: <?php echo $_SESSION['diagnostico']['data_inicio']; ?></p>
                <p>Resultado: <?php echo $_SESSION['diagnostico']['resultado_ia']['diagnostico_final']; ?></p>
                
                <div class="form-actions">
                    <a href="relatorios.php?gerar_pdf=1" class="btn btn-primary" target="_blank">
                        📄 Gerar Relatório PDF
                    </a>
                    <a href="diagnostico.php?pagina=resultado" class="btn btn-secondary">
                        🔍 Ver Diagnóstico
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="alert alert-info">
                    <h4>Nenhum diagnóstico recente</h4>
                    <p>Para gerar um relatório, primeiro realize um diagnóstico no sistema.</p>
                </div>
                
                <div class="form-actions">
                    <a href="diagnostico.php" class="btn btn-primary">
                        🆕 Iniciar Novo Diagnóstico
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <h3>Relatórios Salvos</h3>
                <p>Em desenvolvimento - Em breve você poderá acessar o histórico completo de relatórios.</p>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Equipamento</th>
                                <th>Diagnóstico</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="no-data">
                                    Sistema em desenvolvimento - Os relatórios serão salvos automaticamente em breve.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <?php include '../admin/includes/footer-admin.php'; ?>
</body>
</html>