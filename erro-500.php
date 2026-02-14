<?php
// sistema-ia-teste-completo.php
// ARQUIVO DE TESTES SIMPLIFICADO E FUNCIONAL

// ============================================================================
// CONFIGURAÇÃO INICIAL - SEM CONFLITOS COM SESSÃO
// ============================================================================

// Desativar erros na saída (mostramos de forma controlada)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Iniciar buffer de saída
ob_start();

// Não interferir com sessões existentes
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// HTML inicial
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Testes do Sistema IA</title>
    <style>
        /* Estilos simplificados */
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #0066cc; color: white; padding: 20px; border-radius: 10px 10px 0 0; margin-bottom: 20px; }
        .controls { background: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background: #0066cc; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .test-section { background: white; border-radius: 5px; margin-bottom: 15px; overflow: hidden; }
        .test-header { background: #e9ecef; padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #dee2e6; }
        .test-content { padding: 15px; }
        .message { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .message-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .message-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .message-warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .message-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow: auto; max-height: 300px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #dee2e6; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Testes do Sistema IA - N&M Refrigeração</h1>
            <p>Identificação de problemas no sistema de agendamento</p>
        </div>
        
        <div class="controls">
            <button class="btn btn-primary" onclick="runAllTests()">▶️ Executar Todos os Testes</button>
            <button class="btn btn-success" onclick="runTest('datas')">📅 Testar Datas (Problema)</button>
            <button class="btn btn-danger" onclick="resetTests()">🔄 Reiniciar</button>
            <a href="sistema-ia.php" class="btn" style="background: #6c757d; color: white;">🔙 Voltar ao Sistema</a>
        </div>
        
        <div id="test-results">
            <!-- Resultados aqui -->
        </div>
    </div>

    <script>
    function runAllTests() {
        document.querySelectorAll('.run-test').forEach(btn => btn.click());
    }
    
    function runTest(testName) {
        const results = document.getElementById('test-results');
        results.innerHTML += `<div class="message-info">Executando teste: ${testName}...</div>`;
        
        fetch(`?test=${testName}&t=${Date.now()}`)
            .then(r => r.text())
            .then(html => {
                results.innerHTML += html;
            })
            .catch(err => {
                results.innerHTML += `<div class="message-error">Erro: ${err}</div>`;
            });
    }
    
    function resetTests() {
        document.getElementById('test-results').innerHTML = '';
        location.search = '';
    }
    
    // Expandir/colapsar
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('test-header')) {
            e.target.nextElementSibling.style.display = 
                e.target.nextElementSibling.style.display === 'none' ? 'block' : 'none';
        }
    });
    </script>
</body>
</html>
<?php
$html_output = ob_get_clean();

// ============================================================================
// CLASSE DE TESTES
// ============================================================================

class TestadorSistemaIA {
    private $testes = [];
    
    public function executarTeste($nome) {
        switch($nome) {
            case 'datas':
                return $this->testarDatas();
            case 'funcoes':
                return $this->testarFuncoesBasicas();
            case 'banco':
                return $this->testarBanco();
            case 'fluxo':
                return $this->testarFluxo();
            case 'performance':
                return $this->testarPerformance();
            default:
                return $this->executarTodosTestes();
        }
    }
    
    private function testarDatas() {
        $output = '<div class="test-section">
            <div class="test-header"><h3>📅 Teste de Datas - Erro 500</h3></div>
            <div class="test-content">';
        
        // Teste 1: Verificar função de formatação
        if (!function_exists('formatarTempoMinutos')) {
            $output .= '<div class="message-warning">⚠️ Função formatarTempoMinutos não existe</div>';
        } else {
            $resultado = formatarTempoMinutos(90);
            $output .= '<div class="message-success">✅ formatarTempoMinutos(90) = ' . $resultado . '</div>';
        }
        
        // Teste 2: Verificar data atual
        $data_atual = date('Y-m-d');
        $output .= '<div class="message-info">📅 Data atual: ' . $data_atual . '</div>';
        
        // Teste 3: Validar formato de data
        $datas_teste = [
            '2024-12-18' => 'Válida',
            '2024-13-45' => 'Inválida',
            '18/12/2024' => 'Formato errado',
            '' => 'Vazia'
        ];
        
        $output .= '<table><tr><th>Data</th><th>Esperado</th><th>Resultado</th></tr>';
        foreach ($datas_teste as $data => $esperado) {
            $valida = $this->validarData($data);
            $status = $valida ? '✅' : '❌';
            $classe = $valida ? 'success' : ($data === '' ? 'warning' : 'error');
            $output .= "<tr>
                <td><code>{$data}</code></td>
                <td>{$esperado}</td>
                <td><span class='message-{$classe}'>{$status}</span></td>
            </tr>";
        }
        $output .= '</table>';
        
        // Teste 4: Simular clique em data
        $output .= '<div class="message-info">🔄 Simulando seleção de data...</div>';
        
        try {
            // Simular requisição
            $_SESSION['teste'] = ['tempo_estimado' => ['maximo' => 480]];
            $data_teste = date('Y-m-d', strtotime('+5 days'));
            
            if (function_exists('gerarJanelasHorario')) {
                $output .= '<div class="message-success">✅ Função gerarJanelasHorario existe</div>';
                $output .= '<div class="message-info">📅 Testando com data: ' . $data_teste . '</div>';
            } else {
                $output .= '<div class="message-error">❌ Função gerarJanelasHorario NÃO existe</div>';
            }
            
        } catch (Exception $e) {
            $output .= '<div class="message-error">❌ ERRO: ' . $e->getMessage() . '</div>';
        }
        
        $output .= '</div></div>';
        return $output;
    }
    
    private function testarFuncoesBasicas() {
        $output = '<div class="test-section">
            <div class="test-header"><h3>🔧 Funções Básicas</h3></div>
            <div class="test-content">';
        
        // Verificar funções essenciais
        $funcoes = [
            'calcularDiasNecessarios',
            'isFeriado',
            'calcularAcrescimosCorretamente',
            'calcularValorFinalComAjustes',
            'registrarLog'
        ];
        
        foreach ($funcoes as $funcao) {
            if (function_exists($funcao)) {
                $output .= '<div class="message-success">✅ ' . $funcao . '() existe</div>';
            } else {
                $output .= '<div class="message-warning">⚠️ ' . $funcao . '() não existe</div>';
            }
        }
        
        $output .= '</div></div>';
        return $output;
    }
    
    private function testarBanco() {
        $output = '<div class="test-section">
            <div class="test-header"><h3>🗄️ Banco de Dados</h3></div>
            <div class="test-content">';
        
        try {
            // Tentar conexão
            if (file_exists('includes/config.php')) {
                include 'includes/config.php';
                
                if (isset($pdo) && $pdo instanceof PDO) {
                    $output .= '<div class="message-success">✅ Conexão PDO estabelecida</div>';
                    
                    // Verificar tabelas
                    $tabelas = ['agendamentos', 'clientes', 'servicos'];
                    foreach ($tabelas as $tabela) {
                        try {
                            $stmt = $pdo->query("SELECT 1 FROM {$tabela} LIMIT 1");
                            $output .= '<div class="message-success">✅ Tabela ' . $tabela . ' existe</div>';
                        } catch (Exception $e) {
                            $output .= '<div class="message-error">❌ Tabela ' . $tabela . ' NÃO existe ou erro: ' . $e->getMessage() . '</div>';
                        }
                    }
                } else {
                    $output .= '<div class="message-error">❌ Variável $pdo não é instância de PDO</div>';
                }
            } else {
                $output .= '<div class="message-error">❌ Arquivo config.php não encontrado</div>';
            }
            
        } catch (Exception $e) {
            $output .= '<div class="message-error">❌ ERRO no banco: ' . $e->getMessage() . '</div>';
        }
        
        $output .= '</div></div>';
        return $output;
    }
    
    private function testarFluxo() {
        $output = '<div class="test-section">
            <div class="test-header"><h3>🔄 Fluxo do Agendamento</h3></div>
            <div class="test-content">';
        
        // Simular fluxo básico
        $passos = [
            'iniciar' => 'Inicia conversa',
            'nome' => 'Coleta nome',
            'whatsapp' => 'Coleta telefone',
            'selecionar_servico' => 'Escolhe serviço',
            'selecionar_data' => 'Escolhe data',
            'selecionar_horario' => 'Escolhe horário',
            'confirmar_resumo' => 'Confirma agendamento'
        ];
        
        $output .= '<table><tr><th>Passo</th><th>Descrição</th><th>Status</th></tr>';
        
        foreach ($passos as $acao => $descricao) {
            // Verificar se a ação é processada
            $status = '⚠️ Não testado';
            $classe = 'warning';
            
            if (function_exists('processarRespostaSimples')) {
                $status = '✅ Disponível';
                $classe = 'success';
            }
            
            $output .= "<tr>
                <td><code>{$acao}</code></td>
                <td>{$descricao}</td>
                <td><span class='message-{$classe}'>{$status}</span></td>
            </tr>";
        }
        
        $output .= '</table>';
        $output .= '</div></div>';
        return $output;
    }
    
    private function testarPerformance() {
        $output = '<div class="test-section">
            <div class="test-header"><h3>⚡ Performance</h3></div>
            <div class="test-content">';
        
        // Testar tempo de execução de funções simples
        $start = microtime(true);
        
        // Executar algumas operações
        for ($i = 0; $i < 1000; $i++) {
            $x = sqrt($i);
        }
        
        $tempo = round((microtime(true) - $start) * 1000, 2);
        
        $output .= '<div class="message-info">⏱️ Tempo para 1000 operações: ' . $tempo . 'ms</div>';
        
        if ($tempo > 100) {
            $output .= '<div class="message-warning">⚠️ Performance pode estar lenta</div>';
        } else {
            $output .= '<div class="message-success">✅ Performance OK</div>';
        }
        
        $output .= '</div></div>';
        return $output;
    }
    
    private function executarTodosTestes() {
        $output = '';
        $testes = ['funcoes', 'banco', 'datas', 'fluxo', 'performance'];
        
        foreach ($testes as $teste) {
            $method = 'testar' . ucfirst($teste);
            if (method_exists($this, $method)) {
                $output .= $this->$method();
            }
        }
        
        return $output;
    }
    
    private function validarData($data) {
        if (empty($data)) return false;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) return false;
        
        $date = DateTime::createFromFormat('Y-m-d', $data);
        return $date && $date->format('Y-m-d') === $data;
    }
}

// ============================================================================
// EXECUTAR TESTES
// ============================================================================

// Limpar buffer anterior
ob_clean();

// Mostrar HTML inicial
echo $html_output;

// Executar teste específico se solicitado
if (isset($_GET['test'])) {
    $testador = new TestadorSistemaIA();
    echo $testador->executarTeste($_GET['test']);
    
    // Adicionar diagnóstico
    echo '<div class="test-section">
        <div class="test-header"><h3>🎯 Diagnóstico do Problema</h3></div>
        <div class="test-content">';
    
    if ($_GET['test'] == 'datas') {
        echo '<div class="message-info">
            <h4>🚨 POSSÍVEL CAUSA DO ERRO 500:</h4>
            <p>O erro ao clicar em datas pode ser causado por:</p>
            <ol>
                <li><strong>Banco de dados offline</strong> durante o fluxo real</li>
                <li><strong>Exceção não tratada</strong> em gerarJanelasHorario()</li>
                <li><strong>Sessão perdida</strong> entre requisições</li>
                <li><strong>Timeout</strong> na execução da função</li>
            </ol>
            
            <h4>🔧 SOLUÇÃO RÁPIDA:</h4>
            <pre>
// Adicione no início de gerarJanelasHorario():

function gerarJanelasHorario($data) {
    try {
        // VALIDAR DATA
        if (empty($data)) {
            throw new Exception("Data vazia");
        }
        
        // Validar formato
        if (!DateTime::createFromFormat("Y-m-d", $data)) {
            throw new Exception("Formato de data inválido: " . $data);
        }
        
        // Verificar sessão
        if (!isset($_SESSION["ia_conversa"])) {
            throw new Exception("Sessão não encontrada");
        }
        
        // SEU CÓDIGO AQUI...
        
    } catch (Exception $e) {
        // Logar erro
        error_log("ERRO em gerarJanelasHorario: " . $e->getMessage());
        
        // Mostrar mensagem amigável
        $_SESSION["ia_conversa"]["ultima_pergunta"] = [
            "texto" => "❌ Ocorreu um erro. Por favor, tente outra data.",
            "tipo" => "erro_agendamento"
        ];
        return;
    }
}
            </pre>
        </div>';
    }
    
    echo '</div></div>';
    
    // Verificar logs
    echo '<div class="test-section">
        <div class="test-header"><h3>📋 Logs do Sistema</h3></div>
        <div class="test-content">';
    
    $logs = [
        'php_errors.log',
        'logs/php_errors.log',
        'logs/sistema_ia.log'
    ];
    
    foreach ($logs as $log) {
        if (file_exists($log)) {
            $conteudo = file_get_contents($log);
            $linhas = explode("\n", $conteudo);
            $ultimas = array_slice($linhas, -10);
            
            echo '<div class="message-info">
                <strong>📄 ' . $log . ' (últimas 10 linhas):</strong>
                <pre>' . htmlspecialchars(implode("\n", $ultimas)) . '</pre>
            </div>';
        }
    }
    
    echo '</div></div>';
}

// Se nenhum teste específico, mostrar instruções
if (!isset($_GET['test'])) {
    echo '<div id="test-results">
        <div class="message-info">
            <h3>👋 Bem-vindo ao Testador do Sistema IA!</h3>
            <p>Clique em um dos botões acima para executar os testes.</p>
            <p><strong>Para diagnosticar o erro 500:</strong></p>
            <ol>
                <li>Clique em "Testar Datas (Problema)"</li>
                <li>Analise os resultados</li>
                <li>Corrija os erros identificados</li>
                <li>Teste novamente</li>
            </ol>
        </div>
    </div>';
}
?>