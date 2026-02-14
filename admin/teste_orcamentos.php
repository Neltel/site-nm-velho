<?php
// admin/teste_orcamentos.php
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

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
    $tabelas = ['orcamentos', 'clientes', 'servicos', 'materiais', 'orcamento_materiais', 'orcamento_servicos', 'agendamentos'];
    $todos_ok = true;
    
    foreach($tabelas as $tabela) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabela");
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
    
    // Testar formatarMoeda
    if(!function_exists('formatarMoeda')) {
        echo "<div class='alert alert-danger'>❌ Função formatarMoeda não existe</div>";
        return false;
    }
    
    $teste = formatarMoeda(100.50);
    if($teste == 'R$ 100,50') {
        echo "<div class='alert alert-success'>✅ Função formatarMoeda OK: $teste</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Função formatarMoeda ERRO: Esperado 'R$ 100,50', Recebido '$teste'</div>";
        return false;
    }
    
    // Testar moedaParaFloat
    if(!function_exists('moedaParaFloat')) {
        echo "<div class='alert alert-danger'>❌ Função moedaParaFloat não existe</div>";
        return false;
    }
    
    $teste = moedaParaFloat('R$ 100,50');
    if($teste == 100.50) {
        echo "<div class='alert alert-success'>✅ Função moedaParaFloat OK: $teste</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Função moedaParaFloat ERRO: Esperado '100.50', Recebido '$teste'</div>";
        return false;
    }
    
    return true;
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
        $stmt = $pdo->prepare("SELECT om.*, m.preco_unitario 
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
        $stmt = $pdo->prepare("SELECT os.*, s.preco_base 
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
    echo "<h4>🎯 Testando Ações...</h4>";
    
    if($orcamento_id == 0) {
        echo "<div class='alert alert-warning'>⚠️ Pulando testes de ações - nenhum orçamento</div>";
        return;
    }
    
    $base_url = "orcamentos.php";
    
    echo "<div class='alert alert-info'>🔗 URLs de teste para orçamento #$orcamento_id:</div>";
    
    $acoes = [
        'listar' => 'Listar Orçamentos',
        'editar' => 'Editar Orçamento', 
        'visualizar' => 'Visualizar Orçamento',
        'gerar_orcamento' => 'Gerar Orçamento',
        'enviar_whatsapp' => 'Enviar WhatsApp',
        'gerar_pdf' => 'Gerar PDF',
        'excluir' => 'Excluir Orçamento'
    ];
    
    foreach($acoes as $acao => $descricao) {
        $url = "$base_url?acao=$acao&id=$orcamento_id";
        $btn_class = $acao == 'excluir' ? 'btn-danger' : 'btn-primary';
        
        if($acao == 'listar') {
            $url = "$base_url?acao=$acao";
        }
        
        echo "<a href='$url' class='btn $btn_class btn-sm m-1' target='_blank'>$descricao</a>";
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
            
            // Testar função gerarLinkWhatsApp
            if(!function_exists('gerarLinkWhatsApp')) {
                echo "<div class='alert alert-danger'>❌ Função gerarLinkWhatsApp não existe</div>";
                return;
            }
            
            $mensagem_teste = "Teste de mensagem WhatsApp - N&M Refrigeração";
            $link_whatsapp = gerarLinkWhatsApp($orcamento['cliente_telefone'], $mensagem_teste);
            
            echo "<div class='alert alert-info'>🔗 Link WhatsApp Gerado:</div>";
            echo "<div class='alert alert-light'><small>$link_whatsapp</small></div>";
            
            echo "<a href='$link_whatsapp' class='btn btn-success' target='_blank'>📱 Testar Envio WhatsApp</a>";
            
        } else {
            echo "<div class='alert alert-danger'>❌ Orçamento não encontrado para teste WhatsApp</div>";
        }
        
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Erro no teste WhatsApp: " . $e->getMessage() . "</div>";
    }
}

// Função para testar inclusões
function testarIncludes() {
    echo "<h4>📁 Testando Includes...</h4>";
    
    $includes = [
        'includes/auth.php',
        'includes/header-admin.php', 
        'includes/footer-admin.php',
        '../includes/config.php'
    ];
    
    foreach($includes as $include) {
        if(file_exists($include)) {
            echo "<div class='alert alert-success'>✅ $include encontrado</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ $include NÃO encontrado</div>";
        }
    }
}

// Função para testar JavaScript
function testarJavaScript() {
    echo "<h4>📜 Testando JavaScript...</h4>";
    
    echo "<div class='alert alert-info'>🧪 Testando cálculos em JavaScript...</div>";
    
    echo "
    <script>
    function testarCalculoJS() {
        // Simular cálculo de materiais
        let totalMateriais = 0;
        const materiais = [
            { preco: 50.00, quantidade: 2 },
            { preco: 25.00, quantidade: 4 },
            { preco: 15.00, quantidade: 1 }
        ];
        
        materiais.forEach(material => {
            totalMateriais += material.preco * material.quantidade;
        });
        
        const maoObra = 120.00;
        const totalGeral = totalMateriais + maoObra;
        
        const resultado = document.getElementById('resultado-js');
        resultado.innerHTML = `
            <div class='alert alert-success'>
                <strong>🧮 Cálculo JavaScript Testado:</strong><br>
                Total Materiais: R$ ${totalMateriais.toFixed(2)}<br>
                Mão de Obra: R$ ${maoObra.toFixed(2)}<br>
                <strong>Total Geral: R$ ${totalGeral.toFixed(2)}</strong>
            </div>
        `;
    }
    
    // Executar teste quando página carregar
    document.addEventListener('DOMContentLoaded', testarCalculoJS);
    </script>
    ";
    
    echo "<div id='resultado-js' class='mt-3'></div>";
}
?>

<div class="page-header">
    <h2><i class="fas fa-vial"></i> Teste Completo - Sistema de Orçamentos</h2>
    <p>Este arquivo testa todas as funcionalidades do sistema de orçamentos</p>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-play-circle"></i> Executar Testes</h5>
    </div>
    <div class="card-body">
        <?php
        // Executar todos os testes
        echo "<div class='alert alert-info'><strong>🚀 Iniciando testes...</strong></div>";
        
        // Testar includes
        testarIncludes();
        
        // Testar conexão
        if(testarConexaoBanco($pdo)) {
            // Testar tabelas
            if(testarTabelas($pdo)) {
                // Testar funções
                if(testarFuncoes()) {
                    // Testar orçamentos
                    $orcamento_id = testarOrcamentos($pdo);
                    
                    // Testar cálculos
                    testarCalculos($pdo, $orcamento_id);
                    
                    // Testar ações
                    testarAcoes($pdo, $orcamento_id);
                    
                    // Testar WhatsApp
                    testarWhatsApp($pdo, $orcamento_id);
                }
            }
        }
        
        // Testar JavaScript
        testarJavaScript();
        
        echo "<div class='alert alert-success mt-4'><strong>🎉 Testes concluídos!</strong> Verifique os resultados acima.</div>";
        ?>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-bug"></i> Diagnóstico de Problemas Comuns</h5>
    </div>
    <div class="card-body">
        <h6>🔍 Problemas Identificados e Soluções:</h6>
        
        <div class="alert alert-warning">
            <strong>❌ Botões não funcionam:</strong>
            <ul>
                <li>Verifique se as URLs estão corretas no teste acima</li>
                <li>Confirme que o parâmetro 'id' está sendo passado</li>
                <li>Verifique permissões de arquivo</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ Cálculos errados:</strong>
            <ul>
                <li>Verifique os preços unitários na tabela materiais</li>
                <li>Confirme as quantidades em orcamento_materiais</li>
                <li>Teste a função formatarMoeda e moedaParaFloat</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ WhatsApp não envia:</strong>
            <ul>
                <li>Verifique o formato do telefone no banco</li>
                <li>Teste a função gerarLinkWhatsApp</li>
                <li>Confirme se o número está completo com DDD</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <strong>❌ PDF não gera:</strong>
            <ul>
                <li>Verifique se o arquivo gerar_pdf_orcamento.php existe</li>
                <li>Confirme permissões de escrita</li>
                <li>Teste bibliotecas PDF (TCPDF, Dompdf, etc)</li>
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
</style>

<?php include 'includes/footer-admin.php'; ?>