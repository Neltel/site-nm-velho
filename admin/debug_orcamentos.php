<?php
// admin/debug_orcamentos.php
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

// Função para debug
function debug($data, $title = 'DEBUG') {
    echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h5 style='color: #dc3545;'>🔍 $title</h5>";
    echo "<pre style='background: white; padding: 10px; border-radius: 3px;'>";
    print_r($data);
    echo "</pre>";
    echo "</div>";
}

// Testar conexão com banco
debug($pdo, 'CONEXÃO PDO');

// Testar tabelas
try {
    $tables = ['orcamentos', 'clientes', 'servicos', 'materiais', 'orcamento_materiais', 'orcamento_servicos', 'agendamentos'];
    foreach($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $table");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        debug($result, "TABELA: $table");
    }
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO TABELAS");
}

// Testar funções
debug(function_exists('formatarMoeda'), 'Função formatarMoeda existe?');
debug(function_exists('moedaParaFloat'), 'Função moedaParaFloat existe?');

// Testar valores de exemplo
$valor_teste = "R$ 1.234,56";
debug($valor_teste, 'Valor teste para conversão');
if(function_exists('moedaParaFloat')) {
    debug(moedaParaFloat($valor_teste), 'Valor convertido para float');
}

// Testar orçamentos
try {
    $stmt = $pdo->query("SELECT o.*, c.nome as cliente_nome, s.nome as servico_nome, s.preco_base 
                         FROM orcamentos o 
                         LEFT JOIN clientes c ON o.cliente_id = c.id 
                         LEFT JOIN servicos s ON o.servico_id = s.id 
                         LIMIT 3");
    $orcamentos_teste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug($orcamentos_teste, 'PRIMEIROS 3 ORÇAMENTOS');
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO ORÇAMENTOS");
}

// Testar materiais
try {
    $stmt = $pdo->query("SELECT * FROM materiais WHERE ativo = 1 LIMIT 3");
    $materiais_teste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug($materiais_teste, 'PRIMEIROS 3 MATERIAIS');
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO MATERIAIS");
}

// Testar serviços
try {
    $stmt = $pdo->query("SELECT * FROM servicos WHERE ativo = 1 LIMIT 3");
    $servicos_teste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug($servicos_teste, 'PRIMEIROS 3 SERVIÇOS');
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO SERVIÇOS");
}

// Testar cálculo de materiais do orçamento
try {
    $orcamento_id_teste = 1; // Altere para um ID que existe
    $stmt = $pdo->prepare("SELECT om.*, m.preco_unitario, m.unidade_medida 
                          FROM orcamento_materiais om 
                          JOIN materiais m ON om.material_id = m.id 
                          WHERE om.orcamento_id = ?");
    $stmt->execute([$orcamento_id_teste]);
    $materiais_orcamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug($materiais_orcamento, "MATERIAIS DO ORÇAMENTO ID: $orcamento_id_teste");
    
    // Calcular total
    $total_materiais = 0;
    foreach($materiais_orcamento as $material) {
        $total_materiais += $material['preco_unitario'] * $material['quantidade'];
    }
    debug($total_materiais, "TOTAL MATERIAIS CALCULADO");
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO CÁLCULO MATERIAIS");
}

// Testar URLs dos botões
$orcamento_exemplo = ['id' => 1, 'cliente_nome' => 'Cliente Teste'];
$botoes = [
    'Editar' => "?acao=editar&id={$orcamento_exemplo['id']}",
    'Visualizar' => "?acao=visualizar&id={$orcamento_exemplo['id']}",
    'Gerar Orçamento' => "?acao=gerar_orcamento&id={$orcamento_exemplo['id']}",
    'Enviar WhatsApp' => "?acao=enviar_whatsapp&id={$orcamento_exemplo['id']}",
    'PDF' => "gerar_pdf_orcamento.php?id={$orcamento_exemplo['id']}",
    'Excluir' => "?acao=excluir&id={$orcamento_exemplo['id']}"
];
debug($botoes, 'URLs DOS BOTÕES DE EXEMPLO');

// Testar funções específicas
function testarFuncoes($pdo) {
    $resultados = [];
    
    // Testar formatarMoeda
    if(function_exists('formatarMoeda')) {
        $resultados['formatarMoeda(1234.56)'] = formatarMoeda(1234.56);
        $resultados['formatarMoeda(0)'] = formatarMoeda(0);
        $resultados['formatarMoeda(null)'] = formatarMoeda(null);
    }
    
    // Testar moedaParaFloat
    if(function_exists('moedaParaFloat')) {
        $resultados['moedaParaFloat("R$ 1.234,56")'] = moedaParaFloat("R$ 1.234,56");
        $resultados['moedaParaFloat("R$ 0,00")'] = moedaParaFloat("R$ 0,00");
    }
    
    return $resultados;
}

debug(testarFuncoes($pdo), 'TESTE DAS FUNÇÕES');

// Verificar se há orçamentos para testar
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orcamentos");
    $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC);
    debug($total_orcamentos, 'TOTAL DE ORÇAMENTOS NO SISTEMA');
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO CONTAGEM ORÇAMENTOS");
}

// Testar estrutura das tabelas
try {
    $tables = ['orcamentos', 'clientes', 'servicos', 'materiais'];
    foreach($tables as $table) {
        $stmt = $pdo->query("DESCRIBE $table");
        $estrutura = $stmt->fetchAll(PDO::FETCH_ASSOC);
        debug($estrutura, "ESTRUTURA DA TABELA: $table");
    }
} catch(Exception $e) {
    debug($e->getMessage(), "ERRO ESTRUTURA TABELAS");
}

echo "<div class='alert alert-info mt-4'>";
echo "<h4>📋 INSTRUÇÕES PARA TESTAR</h4>";
echo "<ol>";
echo "<li>Verifique se todas as tabelas existem e têm dados</li>";
echo "<li>Confirme se as funções formatarMoeda e moedaParaFloat estão funcionando</li>";
echo "<li>Teste os cálculos de materiais e serviços</li>";
echo "<li>Verifique as URLs dos botões</li>";
echo "<li>Teste um orçamento específico clicando nos botões</li>";
echo "</ol>";
echo "</div>";

include 'includes/footer-admin.php';
?>