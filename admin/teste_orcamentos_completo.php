<?php
// admin/teste_orcamentos_completo.php
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

// Definir funções se não existirem
if (!function_exists('formatarMoeda')) {
    function formatarMoeda($valor) {
        if(empty($valor)) $valor = 0;
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}

if (!function_exists('moedaParaFloat')) {
    function moedaParaFloat($valor) {
        if(empty($valor)) return 0;
        $valor = str_replace(['R$', '.', ','], ['', '', '.'], $valor);
        return floatval($valor);
    }
}

if (!function_exists('gerarLinkWhatsApp')) {
    function gerarLinkWhatsApp($telefone, $mensagem) {
        $numero = preg_replace('/[^0-9]/', '', $telefone);
        $mensagem_encoded = urlencode($mensagem);
        return "https://wa.me/{$numero}?text={$mensagem_encoded}";
    }
}

// Função para testar conexão com banco
function testarConexaoBanco($pdo) {
    echo "<h4>🔍 Testando Conexão com Banco de Dados...</h4>";
    try {
        $stmt = $pdo->query("SELECT 1 as teste");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['teste'] == 1) {
            echo "<div class='alert alert-success'>✅ Conexão com banco de dados OK</div>";
            return true;
        }
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro na conexão: " . $e->getMessage() . "</div>";
        return false;
    }
}

// Função para testar tabelas
function testarTabelas($pdo) {
    echo "<h4>📊 Testando Tabelas...</h4>";
    $tabelas = [
        'orcamentos' => 'SELECT COUNT(*) as total FROM orcamentos',
        'clientes' => 'SELECT COUNT(*) as total FROM clientes', 
        'servicos' => 'SELECT COUNT(*) as total FROM servicos',
        'materiais' => 'SELECT COUNT(*) as total FROM materiais',
        'orcamento_materiais' => 'SELECT COUNT(*) as total FROM orcamento_materiais',
        'orcamento_servicos' => 'SELECT COUNT(*) as total FROM orcamento_servicos',
        'agendamentos' => 'SELECT COUNT(*) as total FROM agendamentos'
    ];
    
    $todos_ok = true;
    
    foreach($tabelas as $tabela => $sql) {
        try {
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='alert alert-success'>✅ Tabela $tabela: " . $result['total'] . " registros</div>";
        } catch(PDOException $e) {
            echo "<div class='alert alert-danger'>❌ Tabela $tabela: " . $e->getMessage() . "</div>";
            $todos_ok = false;
        }
    }
    return $todos_ok;
}

// Função para testar funções
function testarFuncoes() {
    echo "<h4>⚙️ Testando Funções...</h4>";
    
    $funcoes_ok = true;
    
    // Testar formatarMoeda
    if(!function_exists('formatarMoeda')) {
        echo "<div class='alert alert-danger'>❌ Função formatarMoeda não existe</div>";
        $funcoes_ok = false;
    } else {
        $teste = formatarMoeda(100.50);
        if($teste == 'R$ 100,50') {
            echo "<div class='alert alert-success'>✅ Função formatarMoeda OK: $teste</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Função formatarMoeda ERRO: Esperado 'R$ 100,50', Recebido '$teste'</div>";
            $funcoes_ok = false;
        }
    }
    
    // Testar moedaParaFloat
    if(!function_exists('moedaParaFloat')) {
        echo "<div class='alert alert-danger'>❌ Função moedaParaFloat não existe</div>";
        $funcoes_ok = false;
    } else {
        $teste = moedaParaFloat('R$ 100,50');
        if($teste == 100.50) {
            echo "<div class='alert alert-success'>✅ Função moedaParaFloat OK: $teste</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Função moedaParaFloat ERRO: Esperado '100.50', Recebido '$teste'</div>";
            $funcoes_ok = false;
        }
    }
    
    // Testar gerarLinkWhatsApp
    if(!function_exists('gerarLinkWhatsApp')) {
        echo "<div class='alert alert-danger'>❌ Função gerarLinkWhatsApp não existe</div>";
        $funcoes_ok = false;
    } else {
        $teste = gerarLinkWhatsApp('(17) 99624-0725', 'Teste');
        if(strpos($teste, 'https://wa.me/5517996240725') !== false) {
            echo "<div class='alert alert-success'>✅ Função gerarLinkWhatsApp OK</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Função gerarLinkWhatsApp ERRO</div>";
            $funcoes_ok = false;
        }
    }
    
    return $funcoes_ok;
}

// Função para testar orçamentos
function testarOrcamentos($pdo) {
    echo "<h4>💰 Testando Orçamentos...</h4>";
    
    try {
        // Buscar último orçamento
        $stmt = $pdo->query("SELECT o.*, c.nome as cliente_nome, s.nome as servico_nome 
                            FROM orcamentos o 
                            LEFT JOIN clientes c ON o.cliente_id = c.id 
                            LEFT JOIN servicos s ON o.servico_id = s.id 
                            ORDER BY o.id DESC LIMIT 1");
        $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($orcamento) {
            echo "<div class='alert alert-success'>✅ Último orçamento: #{$orcamento['id']} - {$orcamento['cliente_nome']} - {$orcamento['servico_nome']}</div>";
            
            // Testar materiais do orçamento
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamento_materiais WHERE orcamento_id = ?");
            $stmt->execute([$orcamento['id']]);
            $materiais = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='alert alert-info'>📦 Materiais no orçamento: " . $materiais['total'] . "</div>";
            
            // Testar serviços adicionais
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamento_servicos WHERE orcamento_id = ?");
            $stmt->execute([$orcamento['id']]);
            $servicos = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='alert alert-info'>🔧 Serviços adicionais: " . $servicos['total'] . "</div>";
            
            return $orcamento['id'];
        } else {
            echo "<div class='alert alert-warning'>⚠️ Nenhum orçamento encontrado</div>";
            return 0;
        }
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro ao buscar orçamentos: " . $e->getMessage() . "</div>";
        return 0;
    }
}

// Função para testar cálculos
function testarCalculos($pdo, $orcamento_id) {
    echo "<h4>🧮 Testando Cálculos...</h4>";
    
    if($orcamento_id == 0) {
        echo "<div class='alert alert-warning'>⚠️ Pulando testes de cálculo - nenhum orçamento</div>";
        return;
    }
    
    try {
        // Buscar materiais do orçamento
        $stmt = $pdo->prepare("SELECT om.*, m.nome, m.preco_unitario 
                              FROM orcamento_materiais om 
                              JOIN materiais m ON om.material_id = m.id 
                              WHERE om.orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        $materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_materiais = 0;
        foreach($materiais as $material) {
            $subtotal = $material['preco_unitario'] * $material['quantidade'];
            $total_materiais += $subtotal;
            echo "<div class='alert alert-info'>📦 {$material['nome']}: {$material['quantidade']} x " . formatarMoeda($material['preco_unitario']) . " = " . formatarMoeda($subtotal) . "</div>";
        }
        
        echo "<div class='alert alert-success'>💰 Total Materiais: " . formatarMoeda($total_materiais) . "</div>";
        
        // Buscar serviços adicionais
        $stmt = $pdo->prepare("SELECT os.*, s.nome, s.preco_base 
                              FROM orcamento_servicos os 
                              JOIN servicos s ON os.servico_id = s.id 
                              WHERE os.orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_servicos = 0;
        foreach($servicos as $servico) {
            $quantidade = $servico['quantidade'] ?? 1;
            $subtotal = $servico['preco_base'] * $quantidade;
            $total_servicos += $subtotal;
            echo "<div class='alert alert-info'>🔧 {$servico['nome']}: {$quantidade} x " . formatarMoeda($servico['preco_base']) . " = " . formatarMoeda($subtotal) . "</div>";
        }
        
        echo "<div class='alert alert-success'>🔧 Total Serviços Adicionais: " . formatarMoeda($total_servicos) . "</div>";
        
        // Buscar mão de obra
        $stmt = $pdo->prepare("SELECT s.preco_base 
                              FROM orcamentos o 
                              JOIN servicos s ON o.servico_id = s.id 
                              WHERE o.id = ?");
        $stmt->execute([$orcamento_id]);
        $servico_principal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $mao_obra = $servico_principal ? $servico_principal['preco_base'] : 0;
        echo "<div class='alert alert-success'>👷 Mão de Obra: " . formatarMoeda($mao_obra) . "</div>";
        
        $total_geral = $total_materiais + $total_servicos + $mao_obra;
        echo "<div class='alert alert-success'>🎯 Total Geral Calculado: " . formatarMoeda($total_geral) . "</div>";
        
        // Verificar total salvo no banco
        $stmt = $pdo->prepare("SELECT valor_total FROM orcamentos WHERE id = ?");
        $stmt->execute([$orcamento_id]);
        $orcamento_db = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($orcamento_db['valor_total']) {
            echo "<div class='alert alert-info'>💾 Total Salvo no Banco: " . formatarMoeda($orcamento_db['valor_total']) . "</div>";
            
            if(abs($orcamento_db['valor_total'] - $total_geral) < 0.01) {
                echo "<div class='alert alert-success'>✅ Cálculos batem com o banco!</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Cálculos NÃO batem! Diferença: " . formatarMoeda($orcamento_db['valor_total'] - $total_geral) . "</div>";
            }
        }
        
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro nos cálculos: " . $e->getMessage() . "</div>";
    }
}

// Função para testar ações
function testarAcoes($pdo, $orcamento_id) {
    echo "<h4>🎯 Testando Ações e Botões...</h4>";
    
    if($orcamento_id == 0) {
        echo "<div class='alert alert-warning'>⚠️ Pulando testes de ações - nenhum orçamento</div>";
        return;
    }
    
    $base_url = "orcamentos.php";
    
    echo "<div class='alert alert-info'>🔗 URLs de teste para orçamento #$orcamento_id:</div>";
    
    $acoes = [
        'listar' => ['Listar Orçamentos', 'btn-info'],
        'editar' => ['Editar Orçamento', 'btn-primary'], 
        'visualizar' => ['Visualizar Orçamento', 'btn-info'],
        'gerar_orcamento' => ['Gerar Orçamento', 'btn-success'],
        'enviar_whatsapp' => ['Enviar WhatsApp', 'btn-success'],
        'gerar_pdf' => ['Gerar PDF', 'btn-danger'],
        'excluir' => ['Excluir Orçamento', 'btn-danger']
    ];
    
    foreach($acoes as $acao => [$descricao, $classe]) {
        $url = "$base_url?acao=$acao&id=$orcamento_id";
        
        if($acao == 'listar') {
            $url = "$base_url?acao=$acao";
        }
        
        if($acao == 'excluir') {
            echo "<a href='$url' class='btn $classe btn-sm m-1' onclick='return confirm(\"Tem certeza que deseja excluir este orçamento?\")'>$descricao</a>";
        } else {
            echo "<a href='$url' class='btn $classe btn-sm m-1' target='_blank'>$descricao</a>";
        }
    }
    
    echo "<hr>";
    echo "<h5>🧪 Teste de Agendamento</h5>";
    
    // Testar agendamento
    try {
        $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($agendamento) {
            echo "<div class='alert alert-success'>✅ Agendamento encontrado para este orçamento</div>";
            echo "<div class='alert alert-info'>📅 Data: " . date('d/m/Y', strtotime($agendamento['data_agendamento'])) . " às {$agendamento['hora_agendamento']}</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Nenhum agendamento encontrado para este orçamento</div>";
        }
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro ao buscar agendamento: " . $e->getMessage() . "</div>";
    }
}

// Função para testar WhatsApp
function testarWhatsApp($pdo, $orcamento_id) {
    echo "<h4>📱 Testando WhatsApp...</h4>";
    
    if($orcamento_id == 0) {
        echo "<div class='alert alert-warning'>⚠️ Pulando teste WhatsApp - nenhum orçamento</div>";
        return;
    }
    
    try {
        // Buscar dados do orçamento
        $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, s.nome as servico_nome 
                              FROM orcamentos o 
                              LEFT JOIN clientes c ON o.cliente_id = c.id 
                              LEFT JOIN servicos s ON o.servico_id = s.id 
                              WHERE o.id = ?");
        $stmt->execute([$orcamento_id]);
        $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($orcamento) {
            echo "<div class='alert alert-success'>✅ Dados do orçamento carregados: {$orcamento['cliente_nome']}</div>";
            echo "<div class='alert alert-info'>📞 Telefone: {$orcamento['cliente_telefone']}</div>";
            
            // Testar função gerarLinkWhatsApp
            $mensagem_teste = "Teste de mensagem WhatsApp - N&M Refrigeração - Orçamento #{$orcamento_id}";
            $link_whatsapp = gerarLinkWhatsApp($orcamento['cliente_telefone'], $mensagem_teste);
            
            echo "<div class='alert alert-info'>🔗 Link WhatsApp Gerado:</div>";
            echo "<div class='alert alert-light'><small>" . htmlspecialchars($link_whatsapp) . "</small></div>";
            
            echo "<a href='$link_whatsapp' class='btn btn-success' target='_blank'>📱 Testar Envio WhatsApp</a>";
            
        } else {
            echo "<div class='alert alert-danger'>❌ Orçamento não encontrado para teste WhatsApp</div>";
        }
        
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro no teste WhatsApp: " . $e->getMessage() . "</div>";
    }
}

// Função para testar PDF
function testarPDF($orcamento_id) {
    echo "<h4>📄 Testando PDF...</h4>";
    
    if($orcamento_id == 0) {
        echo "<div class='alert alert-warning'>⚠️ Pulando teste PDF - nenhum orçamento</div>";
        return;
    }
    
    $pdf_url = "gerar_pdf_orcamento.php?id=$orcamento_id";
    
    if(file_exists('gerar_pdf_orcamento.php')) {
        echo "<div class='alert alert-success'>✅ Arquivo gerar_pdf_orcamento.php encontrado</div>";
        echo "<a href='$pdf_url' class='btn btn-danger' target='_blank'>📄 Gerar PDF do Orçamento</a>";
    } else {
        echo "<div class='alert alert-danger'>❌ Arquivo gerar_pdf_orcamento.php NÃO encontrado</div>";
    }
}

// Função para testar calendário
function testarCalendario($pdo) {
    echo "<h4>📅 Testando Calendário e Agendamentos...</h4>";
    
    try {
        // Buscar agendamentos futuros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento >= CURDATE() AND status != 'cancelado'");
        $agendamentos_futuros = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<div class='alert alert-info'>📅 Agendamentos futuros: " . $agendamentos_futuros['total'] . "</div>";
        
        // Buscar últimos agendamentos
        $stmt = $pdo->query("SELECT a.*, c.nome as cliente_nome 
                            FROM agendamentos a 
                            LEFT JOIN clientes c ON a.cliente_id = c.id 
                            WHERE a.data_agendamento >= CURDATE() 
                            ORDER BY a.data_agendamento ASC 
                            LIMIT 5");
        $proximos_agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($proximos_agendamentos) {
            echo "<div class='alert alert-success'>✅ Próximos agendamentos:</div>";
            foreach($proximos_agendamentos as $agendamento) {
                echo "<div class='alert alert-light'>📅 " . date('d/m/Y', strtotime($agendamento['data_agendamento'])) . 
                     " {$agendamento['hora_agendamento']} - {$agendamento['cliente_nome']}</div>";
            }
        }
        
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro no teste do calendário: " . $e->getMessage() . "</div>";
    }
}

// Função para testar JavaScript
function testarJavaScript() {
    echo "<h4>📜 Testando JavaScript e Cálculos em Tempo Real...</h4>";
    
    echo "
    <div class='card'>
        <div class='card-header'>
            <h5>🧪 Simulador de Cálculos JavaScript</h5>
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-4'>
                    <label>Material 1 - Preço:</label>
                    <input type='number' id='preco1' class='form-control' value='50.00' step='0.01'>
                </div>
                <div class='col-md-4'>
                    <label>Quantidade:</label>
                    <input type='number' id='quantidade1' class='form-control' value='2' step='0.5'>
                </div>
                <div class='col-md-4'>
                    <label>Subtotal:</label>
                    <div id='subtotal1' class='form-control-plaintext'>R$ 100,00</div>
                </div>
            </div>
            
            <div class='row mt-3'>
                <div class='col-md-4'>
                    <label>Material 2 - Preço:</label>
                    <input type='number' id='preco2' class='form-control' value='25.00' step='0.01'>
                </div>
                <div class='col-md-4'>
                    <label>Quantidade:</label>
                    <input type='number' id='quantidade2' class='form-control' value='4' step='0.5'>
                </div>
                <div class='col-md-4'>
                    <label>Subtotal:</label>
                    <div id='subtotal2' class='form-control-plaintext'>R$ 100,00</div>
                </div>
            </div>
            
            <div class='row mt-3'>
                <div class='col-md-6'>
                    <label>Mão de Obra:</label>
                    <input type='number' id='mao_obra' class='form-control' value='120.00' step='0.01'>
                </div>
                <div class='col-md-6'>
                    <label><strong>Total Geral:</strong></label>
                    <div id='total_geral' class='form-control-plaintext h4 text-success'>R$ 320,00</div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function calcularTotais() {
        // Materiais
        const preco1 = parseFloat(document.getElementById('preco1').value) || 0;
        const quantidade1 = parseFloat(document.getElementById('quantidade1').value) || 0;
        const subtotal1 = preco1 * quantidade1;
        
        const preco2 = parseFloat(document.getElementById('preco2').value) || 0;
        const quantidade2 = parseFloat(document.getElementById('quantidade2').value) || 0;
        const subtotal2 = preco2 * quantidade2;
        
        const maoObra = parseFloat(document.getElementById('mao_obra').value) || 0;
        const totalGeral = subtotal1 + subtotal2 + maoObra;
        
        // Atualizar displays
        document.getElementById('subtotal1').textContent = formatarMoedaJS(subtotal1);
        document.getElementById('subtotal2').textContent = formatarMoedaJS(subtotal2);
        document.getElementById('total_geral').textContent = formatarMoedaJS(totalGeral);
    }
    
    function formatarMoedaJS(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\\d(?=(\\d{3})+,)/g, '$&.');
    }
    
    // Event listeners
    document.getElementById('preco1').addEventListener('input', calcularTotais);
    document.getElementById('quantidade1').addEventListener('input', calcularTotais);
    document.getElementById('preco2').addEventListener('input', calcularTotais);
    document.getElementById('quantidade2').addEventListener('input', calcularTotais);
    document.getElementById('mao_obra').addEventListener('input', calcularTotais);
    
    // Calcular inicial
    calcularTotais();
    </script>
    ";
}

// Função para testar tudo
function executarTestesCompletos($pdo) {
    echo "<div class='alert alert-info'><strong>🚀 INICIANDO TESTES COMPLETOS...</strong></div>";
    
    // Testar conexão
    if(!testarConexaoBanco($pdo)) {
        echo "<div class='alert alert-danger'><strong>❌ TESTES INTERROMPIDOS - Problema na conexão com banco</strong></div>";
        return;
    }
    
    // Testar tabelas  
    if(!testarTabelas($pdo)) {
        echo "<div class='alert alert-warning'><strong>⚠️ ALGUMAS TABELAS COM PROBLEMAS</strong></div>";
    }
    
    // Testar funções
    if(!testarFuncoes()) {
        echo "<div class='alert alert-warning'><strong>⚠️ ALGUMAS FUNÇÕES COM PROBLEMAS</strong></div>";
    }
    
    // Buscar orçamento para testes
    $orcamento_id = testarOrcamentos($pdo);
    
    // Executar testes que dependem do orçamento
    if($orcamento_id > 0) {
        testarCalculos($pdo, $orcamento_id);
        testarAcoes($pdo, $orcamento_id);
        testarWhatsApp($pdo, $orcamento_id);
        testarPDF($orcamento_id);
    }
    
    // Testar calendário (não depende de orçamento específico)
    testarCalendario($pdo);
    
    // Testar JavaScript
    testarJavaScript();
    
    echo "<div class='alert alert-success mt-4'><strong>🎉 TODOS OS TESTES CONCLUÍDOS!</strong></div>";
}
?>

<div class="page-header">
    <h2><i class="fas fa-vial"></i> Teste COMPLETO - Sistema de Orçamentos</h2>
    <p>Este arquivo testa TODAS as funcionalidades do sistema de orçamentos</p>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-play-circle"></i> Executar Testes Completos</h5>
    </div>
    <div class="card-body">
        <?php executarTestesCompletos($pdo); ?>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-bug"></i> Diagnóstico de Problemas</h5>
    </div>
    <div class="card-body">
        <h6>🔍 Problemas Identificados e Soluções:</h6>
        
        <div class="alert alert-warning">
            <strong>❌ Funções faltando:</strong>
            <ul>
                <li>As funções são definidas automaticamente neste teste</li>
                <li>No arquivo principal, verifique se estão definidas</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ Botões não funcionam:</strong>
            <ul>
                <li>Clique nos botões acima para testar cada um</li>
                <li>Verifique se aparece erro ou página em branco</li>
                <li>Confirme que o arquivo orcamentos.php existe</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ Cálculos errados:</strong>
            <ul>
                <li>Compare os cálculos do PHP vs JavaScript acima</li>
                <li>Verifique os preços no banco de dados</li>
                <li>Teste as funções de formatação de moeda</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ Agendamento não funciona:</strong>
            <ul>
                <li>Verifique a tabela agendamentos no teste acima</li>
                <li>Confirme os campos data_fim e hora_fim</li>
                <li>Teste a função salvarAgendamentoOrcamento</li>
            </ul>
        </div>
    </div>
</div>

<style>
.alert {
    margin-bottom: 10px;
}
.btn {
    margin: 2px;
}
.card {
    margin-bottom: 20px;
}
</style>

<?php include 'includes/footer-admin.php'; ?>