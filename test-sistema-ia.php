<?php
// test-sistema-ia.php
// ARQUIVO DE TESTES COMPLETO PARA SISTEMA IA DE AGENDAMENTO
// Para executar: acesse http://seu-site.com/test-sistema-ia.php

// Configurações básicas
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

// Simular sessão para testes
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir o arquivo principal
require_once 'sistema-ia.php';

// Classe de testes
class TestSistemaIA {
    private $testResults = [];
    private $currentTest = '';
    
    public function __construct() {
        echo "<!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Testes do Sistema IA - N&M Refrigeração</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                h1 { color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 10px; }
                h2 { color: #333; margin-top: 30px; }
                .test-section { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #0066cc; }
                .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
                .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
                .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
                .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
                .test-item { margin: 5px 0; padding: 8px; background: white; border-radius: 3px; border: 1px solid #ddd; }
                .function-call { font-family: monospace; background: #f1f1f1; padding: 2px 5px; border-radius: 3px; }
                .expected { color: #28a745; font-weight: bold; }
                .actual { color: #dc3545; font-weight: bold; }
                .buttons { margin: 20px 0; }
                .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
                .btn-run-all { background: #0066cc; color: white; }
                .btn-run-section { background: #17a2b8; color: white; }
                .btn-reset { background: #6c757d; color: white; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                th { background: #f8f9fa; }
                pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow: auto; }
                .debug-info { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🔧 TESTES DO SISTEMA IA - N&M REFRIGERAÇÃO</h1>
                <div class='buttons'>
                    <button class='btn btn-run-all' onclick='runAllTests()'>▶️ Executar Todos os Testes</button>
                    <button class='btn btn-reset' onclick='resetTests()'>🔄 Limpar Testes</button>
                    <a href='sistema-ia.php' class='btn' style='background: #28a745; color: white; text-decoration: none;'>🔙 Voltar ao Sistema IA</a>
                </div>";
    }
    
    public function __destruct() {
        echo "</div>
            <script>
                function runAllTests() {
                    document.querySelectorAll('[onclick^=\"runTest\"]').forEach(btn => {
                        setTimeout(() => btn.click(), 100);
                    });
                }
                function resetTests() {
                    location.reload();
                }
            </script>
            </body>
            </html>";
    }
    
    private function startTest($name) {
        $this->currentTest = $name;
        echo "<div class='test-section' id='test-$name'>
                <h2>🧪 $name</h2>";
    }
    
    private function endTest() {
        echo "</div>";
        $this->currentTest = '';
    }
    
    private function logResult($type, $message, $details = '') {
        $class = '';
        switch ($type) {
            case 'success': $class = 'success'; break;
            case 'error': $class = 'error'; break;
            case 'warning': $class = 'warning'; break;
            case 'info': $class = 'info'; break;
        }
        
        echo "<div class='test-result $class'>
                <strong>$message</strong>";
        if ($details) {
            echo "<div style='margin-top: 5px;'>$details</div>";
        }
        echo "</div>";
        
        $this->testResults[] = [
            'test' => $this->currentTest,
            'type' => $type,
            'message' => $message,
            'details' => $details
        ];
    }
    
    private function assertEqual($actual, $expected, $message) {
        if ($actual == $expected) {
            $this->logResult('success', "✅ $message", 
                "Esperado: <span class='expected'>$expected</span><br>Obtido: <span class='expected'>$actual</span>");
            return true;
        } else {
            $this->logResult('error', "❌ $message", 
                "Esperado: <span class='expected'>$expected</span><br>Obtido: <span class='actual'>$actual</span>");
            return false;
        }
    }
    
    private function assertTrue($condition, $message) {
        return $this->assertEqual($condition, true, $message);
    }
    
    private function assertFalse($condition, $message) {
        return $this->assertEqual($condition, false, $message);
    }
    
    private function assertNotNull($value, $message) {
        if ($value !== null) {
            $this->logResult('success', "✅ $message", "Valor não é nulo");
            return true;
        } else {
            $this->logResult('error', "❌ $message", "Valor é nulo");
            return false;
        }
    }
    
    // ============================================================================
    // TESTES DAS FUNÇÕES BÁSICAS
    // ============================================================================
    
    public function testFuncoesBasicas() {
        $this->startTest('Funções Básicas');
        
        // Testar função isFeriado
        $this->assertTrue(isFeriado('2024-01-01'), '01/01/2024 deve ser feriado');
        $this->assertFalse(isFeriado('2024-01-02'), '02/01/2024 não deve ser feriado');
        
        // Testar função formatarTempoMinutos
        $this->assertEqual(formatarTempoMinutos(30), "30 min", "30 minutos deve formatar como '30 min'");
        $this->assertEqual(formatarTempoMinutos(60), "1 hora", "60 minutos deve formatar como '1 hora'");
        $this->assertEqual(formatarTempoMinutos(90), "1h30min", "90 minutos deve formatar como '1h30min'");
        $this->assertEqual(formatarTempoMinutos(120), "2 horas", "120 minutos deve formatar como '2 horas'");
        
        // Testar função calcularDiasNecessarios
        $this->assertEqual(calcularDiasNecessarios(300), 1, "300 minutos deve precisar de 1 dia");
        $this->assertEqual(calcularDiasNecessarios(700), 2, "700 minutos deve precisar de 2 dias");
        $this->assertEqual(calcularDiasNecessarios(1400), 3, "1400 minutos deve precisar de 3 dias");
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE CÁLCULO DE TEMPO
    // ============================================================================
    
    public function testCalculoTempo() {
        $this->startTest('Cálculo de Tempo dos Serviços');
        
        // Criar array de serviços de teste
        $servicos_teste = [
            [
                'duracao_min_min' => 30,
                'duracao_max_min' => 60,
                'duracao_padrao_min' => 45,
                'quantidade' => 2
            ],
            [
                'duracao_min_min' => 60,
                'duracao_max_min' => 120,
                'duracao_padrao_min' => 90,
                'quantidade' => 1
            ]
        ];
        
        // Testar calcularTempoTotalServicos
        $tempo_total = calcularTempoTotalServicos($servicos_teste);
        
        $this->assertEqual($tempo_total['minimo_minutos'], 120, 
            "Tempo mínimo: (30×2) + (60×1) = 120 minutos");
        $this->assertEqual($tempo_total['maximo_minutos'], 240, 
            "Tempo máximo: (60×2) + (120×1) = 240 minutos");
        $this->assertEqual($tempo_total['padrao_minutos'], 180, 
            "Tempo padrão: (45×2) + (90×1) = 180 minutos");
        
        echo "<div class='test-item'>";
        echo "<strong>Serviços de Teste:</strong><br>";
        echo "<pre>" . print_r($servicos_teste, true) . "</pre>";
        echo "<strong>Resultado do cálculo:</strong><br>";
        echo "<pre>" . print_r($tempo_total, true) . "</pre>";
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE ACRÉSCIMOS
    // ============================================================================
    
    public function testAcrescimos() {
        $this->startTest('Cálculo de Acréscimos');
        
        // Testar final de semana
        $acrescimos_fds = calcularAcrescimosCorretamente('2024-02-17', '10:00', 1000.00); // Sábado
        $this->assertTrue($acrescimos_fds['total'] > 0, "Sábado deve ter acréscimo de 10%");
        $this->assertEqual($acrescimos_fds['total'], 100.00, "Acréscimo de 10% sobre R$1000 = R$100");
        
        // Testar feriado
        $acrescimos_feriado = calcularAcrescimosCorretamente('2024-01-01', '10:00', 1000.00); // Ano Novo
        $this->assertTrue($acrescimos_feriado['total'] > 0, "Feriado deve ter acréscimo de 10%");
        
        // Testar horário noturno
        $acrescimos_noturno = calcularAcrescimosCorretamente('2024-02-16', '18:00', 1000.00); // Sexta-feira 18h
        $this->assertTrue($acrescimos_noturno['total'] > 0, "Horário após 17h deve ter acréscimo de 5%");
        $this->assertEqual($acrescimos_noturno['total'], 50.00, "Acréscimo de 5% sobre R$1000 = R$50");
        
        // Testar dia útil normal
        $acrescimos_normal = calcularAcrescimosCorretamente('2024-02-16', '10:00', 1000.00); // Sexta-feira 10h
        $this->assertEqual($acrescimos_normal['total'], 0, "Dia útil normal não deve ter acréscimo");
        
        echo "<div class='test-item'>";
        echo "<strong>Exemplo de acréscimos para Sábado 10h (R$1000):</strong><br>";
        echo "<pre>" . print_r($acrescimos_fds, true) . "</pre>";
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE VALOR FINAL
    // ============================================================================
    
    public function testValorFinal() {
        $this->startTest('Cálculo de Valor Final');
        
        // Teste com desconto apenas
        $acrescimos_teste = ['total' => 0, 'detalhes' => []];
        $valor1 = calcularValorFinalComAjustes(1000.00, $acrescimos_teste, 5);
        
        $this->assertEqual($valor1['valor_final'], 950.00, 
            "R$1000 com 5% desconto = R$950");
        $this->assertTrue($valor1['tem_ajustes'], "Deve ter ajustes (desconto)");
        
        // Teste com acréscimo apenas (final de semana)
        $acrescimos_fds = [
            'total' => 100.00,
            'detalhes' => [[
                'tipo' => 'fds_feriado',
                'descricao' => 'Fim de semana/Feriado',
                'percentual' => 10,
                'valor' => 100.00
            ]]
        ];
        $valor2 = calcularValorFinalComAjustes(1000.00, $acrescimos_fds, 0);
        
        $this->assertEqual($valor2['valor_final'], 1100.00, 
            "R$1000 + R$100 acréscimo = R$1100");
        
        // Teste com desconto e acréscimo
        $valor3 = calcularValorFinalComAjustes(1000.00, $acrescimos_fds, 5);
        $this->assertEqual($valor3['valor_final'], 1050.00, 
            "R$1000 + R$100 - R$50 = R$1050");
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE JANELAS DE HORÁRIO (PROBLEMA PRINCIPAL)
    // ============================================================================
    
    public function testJanelasHorario() {
        $this->startTest('Janelas de Horário - VERIFICAÇÃO DE PROBLEMAS');
        
        // Primeiro, testar a função básica calcularJanelasDisponiveis
        echo "<div class='test-item'>";
        echo "<strong>Testando função calcularJanelasDisponiveis:</strong><br>";
        
        // Mock do banco de dados para testes
        global $pdo;
        $pdo_mock = null;
        
        try {
            // Teste 1: Dia sem agendamentos
            $data_teste1 = '2024-03-01'; // Sexta-feira
            $tempo_necessario = 240; // 4 horas
            $tempo_maximo = 300; // 5 horas
            
            $janelas1 = calcularJanelasDisponiveis($data_teste1, $tempo_necessario, $tempo_maximo);
            
            echo "<div class='debug-info'>";
            echo "Data: $data_teste1<br>";
            echo "Tempo necessário: $tempo_necessario min<br>";
            echo "Tempo máximo: $tempo_maximo min<br>";
            echo "Número de janelas encontradas: " . count($janelas1) . "<br>";
            if (!empty($janelas1)) {
                echo "Primeira janela: " . $janelas1[0]['inicio'] . " até " . $janelas1[0]['termino'] . "<br>";
            }
            echo "</div>";
            
            $this->assertTrue(is_array($janelas1), "Deve retornar array");
            $this->assertTrue(count($janelas1) > 0, "Dia sem agendamentos deve ter janelas disponíveis");
            
            // Verificar estrutura das janelas
            if (!empty($janelas1)) {
                $janela = $janelas1[0];
                $this->assertTrue(isset($janela['inicio']), "Janela deve ter hora de início");
                $this->assertTrue(isset($janela['termino']), "Janela deve ter hora de término");
                $this->assertTrue(isset($janela['duracao_min']), "Janela deve ter duração");
                
                // Verificar se o horário está dentro do expediente
                $hora_inicio = intval(substr($janela['inicio'], 0, 2));
                $this->assertTrue($hora_inicio >= 8 && $hora_inicio <= 19, 
                    "Horário de início deve estar entre 8:00 e 19:00");
            }
            
        } catch (Exception $e) {
            $this->logResult('error', "Erro ao testar janelas: " . $e->getMessage());
        }
        
        echo "</div>";
        
        // Teste 2: Verificar disponibilidade com tempo
        echo "<div class='test-item'>";
        echo "<strong>Testando verificarDisponibilidadeComTempo:</strong><br>";
        
        try {
            $disponibilidade = verificarDisponibilidadeComTempo(
                $pdo_mock,
                '2024-03-01',
                '09:00',
                240
            );
            
            echo "<pre>" . print_r($disponibilidade, true) . "</pre>";
            
            $this->assertTrue(isset($disponibilidade['disponivel']), 
                "Deve retornar status de disponibilidade");
                
        } catch (Exception $e) {
            $this->logResult('error', "Erro na verificação de disponibilidade: " . $e->getMessage());
        }
        
        echo "</div>";
        
        // Teste 3: Verificar getTodosAgendamentosDoDia
        echo "<div class='test-item'>";
        echo "<strong>Testando getTodosAgendamentosDoDia:</strong><br>";
        
        try {
            $agendamentos = getTodosAgendamentosDoDia($pdo_mock, '2024-03-01');
            echo "Agendamentos encontrados: " . count($agendamentos) . "<br>";
            echo "<pre>" . print_r($agendamentos, true) . "</pre>";
            
            $this->assertTrue(is_array($agendamentos), "Deve retornar array");
            
        } catch (Exception $e) {
            $this->logResult('error', "Erro ao buscar agendamentos: " . $e->getMessage());
        }
        
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE DATAS DISPONÍVEIS
    // ============================================================================
    
    public function testDatasDisponiveis() {
        $this->startTest('Datas Disponíveis - SIMULAÇÃO DE ERROS');
        
        // Configurar sessão de teste
        $_SESSION['ia_conversa'] = [
            'tempo_estimado' => [
                'minimo' => 120,
                'maximo' => 240,
                'padrao' => 180,
                'minimo_horas' => '2 horas',
                'maximo_horas' => '4 horas',
                'padrao_horas' => '3 horas'
            ],
            'total_servicos_valor' => 1500.00,
            'dias_necessarios' => 1,
            'agendamento_multi_dia' => false
        ];
        
        echo "<div class='test-item'>";
        echo "<strong>Configuração da sessão para testes:</strong><br>";
        echo "<pre>" . print_r($_SESSION['ia_conversa'], true) . "</pre>";
        echo "</div>";
        
        // Testar gerarDatasSimples
        echo "<div class='test-item'>";
        echo "<strong>Testando gerarDatasSimples() [Esta função pode conter erros]:</strong><br>";
        
        try {
            // Capturar saída da função
            ob_start();
            gerarDatasSimples();
            $output = ob_get_clean();
            
            echo "<div class='debug-info'>";
            echo "Função executada. Verificando última pergunta na sessão...<br>";
            
            if (isset($_SESSION['ia_conversa']['ultima_pergunta'])) {
                $ultima_pergunta = $_SESSION['ia_conversa']['ultima_pergunta'];
                echo "<strong>Tipo:</strong> " . $ultima_pergunta['tipo'] . "<br>";
                echo "<strong>Tem opções:</strong> " . (isset($ultima_pergunta['opcoes']) ? 'Sim' : 'Não') . "<br>";
                
                if (isset($ultima_pergunta['opcoes'])) {
                    echo "<strong>Número de opções:</strong> " . count($ultima_pergunta['opcoes']) . "<br>";
                    
                    // Verificar cada opção
                    foreach ($ultima_pergunta['opcoes'] as $i => $opcao) {
                        echo "Opção $i: " . substr($opcao['texto'], 0, 50) . "...<br>";
                    }
                }
            } else {
                echo "❌ ERRO: Não definiu última pergunta!<br>";
            }
            echo "</div>";
            
            $this->assertTrue(isset($_SESSION['ia_conversa']['ultima_pergunta']),
                "Deve definir última pergunta após gerarDatasSimples");
                
            if (isset($_SESSION['ia_conversa']['ultima_pergunta']['opcoes'])) {
                $this->assertTrue(count($_SESSION['ia_conversa']['ultima_pergunta']['opcoes']) > 0,
                    "Deve gerar opções de datas disponíveis");
            }
            
        } catch (Exception $e) {
            $this->logResult('error', "Erro em gerarDatasSimples: " . $e->getMessage());
            echo "<pre>Stack trace: " . $e->getTraceAsString() . "</pre>";
        }
        
        echo "</div>";
        
        // Testar gerarOpcoesMultiDia
        echo "<div class='test-item'>";
        echo "<strong>Testando gerarOpcoesMultiDia() [Esta pode ter erros graves]:</strong><br>";
        
        try {
            $tempo_estimado = [
                'maximo_minutos' => 1400 // Precisa de 3 dias
            ];
            
            $opcoes_multi = gerarOpcoesMultiDia($tempo_estimado, 1500.00);
            
            echo "<div class='debug-info'>";
            echo "Opções multi-dia geradas: " . count($opcoes_multi) . "<br>";
            echo "<pre>" . print_r($opcoes_multi, true) . "</pre>";
            echo "</div>";
            
            $this->assertTrue(is_array($opcoes_multi), "Deve retornar array");
            
            // Mesmo que não encontre períodos, deve retornar array vazio, não erro
            if (empty($opcoes_multi)) {
                $this->logResult('warning', "Nenhum período multi-dia disponível nos próximos 60 dias");
            }
            
        } catch (Exception $e) {
            $this->logResult('error', "❌ ERRO CRÍTICO em gerarOpcoesMultiDia: " . $e->getMessage());
            echo "<pre>Stack trace: " . $e->getTraceAsString() . "</pre>";
            
            // Este é provavelmente o problema principal!
            echo "<div class='test-result error'>";
            echo "<strong>🚨 POSSÍVEL CAUSA DO PROBLEMA:</strong><br>";
            echo "A função gerarOpcoesMultiDia está lançando exceção!<br>";
            echo "Verifique: conexão com banco, consultas SQL, e tratamento de erros.<br>";
            echo "Solução: Adicionar try-catch na função e retornar array vazio em caso de erro.";
            echo "</div>";
        }
        
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE PROCESSAMENTO DE RESPOSTAS
    // ============================================================================
    
    public function testProcessamentoRespostas() {
        $this->startTest('Processamento de Respostas - Simulação');
        
        // Configurar sessão limpa
        $_SESSION['ia_conversa'] = [
            'etapa' => 1,
            'dados' => [],
            'servicos_selecionados' => [],
            'servicos_disponiveis' => [
                [
                    'id' => 1,
                    'nome' => 'Instalação de ar condicionado',
                    'preco_base' => 350.00,
                    'duracao_padrao_min' => 240,
                    'duracao_min_min' => 180,
                    'duracao_max_min' => 480
                ]
            ],
            'ultima_pergunta' => []
        ];
        
        // Testar processamento de nome
        echo "<div class='test-item'>";
        echo "<strong>Testando processarRespostaSimples('nome', 'João Silva'):</strong><br>";
        
        processarRespostaSimples('nome', 'João Silva');
        
        echo "Dados após nome: <pre>" . print_r($_SESSION['ia_conversa']['dados'], true) . "</pre>";
        echo "Última pergunta: <pre>" . print_r($_SESSION['ia_conversa']['ultima_pergunta'], true) . "</pre>";
        
        $this->assertEqual($_SESSION['ia_conversa']['dados']['nome'], 'João Silva', 
            "Nome deve ser salvo corretamente");
        $this->assertEqual($_SESSION['ia_conversa']['ultima_pergunta']['acao'], 'whatsapp',
            "Próxima etapa deve ser whatsapp");
        
        echo "</div>";
        
        // Testar processamento de whatsapp
        echo "<div class='test-item'>";
        echo "<strong>Testando processarRespostaSimples('whatsapp', '17996240725'):</strong><br>";
        
        processarRespostaSimples('whatsapp', '17996240725');
        
        echo "Dados após whatsapp: <pre>" . print_r($_SESSION['ia_conversa']['dados'], true) . "</pre>";
        echo "Telefone formatado: " . ($_SESSION['ia_conversa']['dados']['telefone_formatado'] ?? 'NÃO FORMATADO') . "<br>";
        
        $this->assertTrue(isset($_SESSION['ia_conversa']['dados']['telefone']), 
            "Telefone deve ser salvo");
        $this->assertTrue(isset($_SESSION['ia_conversa']['ultima_pergunta']['opcoes']), 
            "Deve mostrar opções de serviços");
        
        echo "</div>";
        
        // Testar seleção de serviço
        echo "<div class='test-item'>";
        echo "<strong>Testando seleção de serviço:</strong><br>";
        
        processarRespostaSimples('selecionar_servico', 'Instalação de ar condicionado');
        
        echo "Serviço temporário: <pre>" . print_r($_SESSION['ia_conversa']['servico_temp'] ?? 'NÃO DEFINIDO', true) . "</pre>";
        echo "Última pergunta: " . ($_SESSION['ia_conversa']['ultima_pergunta']['acao'] ?? 'NÃO DEFINIDA') . "<br>";
        
        $this->assertTrue(isset($_SESSION['ia_conversa']['servico_temp']), 
            "Deve armazenar serviço temporário");
        $this->assertEqual($_SESSION['ia_conversa']['ultima_pergunta']['acao'], 'quantidade_equipamentos',
            "Próxima etapa deve ser quantidade de equipamentos");
        
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE BACKUP E LOGS
    // ============================================================================
    
    public function testBackupLogs() {
        $this->startTest('Backup e Logs');
        
        // Testar função registrarLog (simulação)
        echo "<div class='test-item'>";
        echo "<strong>Testando função registrarLog (simulação):</strong><br>";
        
        try {
            // Esta função tenta inserir no banco, então vamos apenas verificar se existe
            $functionExists = function_exists('registrarLog');
            $this->assertTrue($functionExists, "Função registrarLog deve existir");
            
            if ($functionExists) {
                echo "✅ Função registrarLog existe<br>";
                echo "📝 Assinatura esperada: registrarLog(tipo, mensagem, dados, ip)<br>";
            }
        } catch (Exception $e) {
            $this->logResult('warning', "Possível problema com registrarLog: " . $e->getMessage());
        }
        
        echo "</div>";
        
        // Testar função formatarJanelaHorario
        echo "<div class='test-item'>";
        echo "<strong>Testando formatarJanelaHorario:</strong><br>";
        
        $janela_teste = [
            'inicio' => '08:00',
            'termino' => '16:00',
            'duracao_min' => 480
        ];
        
        $tempo_padrao_horas = '8 horas';
        $formatted = formatarJanelaHorario($janela_teste, $tempo_padrao_horas);
        
        echo "Janela: <pre>" . print_r($janela_teste, true) . "</pre>";
        echo "Formatada: $formatted<br>";
        
        $this->assertTrue(strpos($formatted, '08:00') !== false, 
            "Deve conter hora de início");
        $this->assertTrue(strpos($formatted, '16:00') !== false, 
            "Deve conter hora de término");
            
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE SIMULAÇÃO DE ERROS COMUNS
    // ============================================================================
    
    public function testErrosComuns() {
        $this->startTest('Simulação de Erros Comuns');
        
        echo "<div class='test-item'>";
        echo "<h3>🚨 ERROS MAIS COMUNS NO SISTEMA IA:</h3>";
        
        // Erro 1: Banco de dados offline
        echo "<div class='test-result warning'>";
        echo "<strong>ERRO 1: Conexão com banco de dados</strong><br>";
        echo "Se o banco estiver offline, as funções gerarOpcoesMultiDia e gerarDatasSimples falharão.<br>";
        echo "<strong>Solução:</strong> Adicionar try-catch em todas as funções que acessam o banco.";
        echo "</div>";
        
        // Erro 2: Sessão corrompida
        echo "<div class='test-result warning'>";
        echo "<strong>ERRO 2: Sessão corrompida ou perdida</strong><br>";
        echo "Se a sessão PHP expirar, \$_SESSION['ia_conversa'] será perdida.<br>";
        echo "<strong>Solução:</strong> Verificar se a sessão existe antes de usá-la, recomeçar se necessário.";
        echo "</div>";
        
        // Erro 3: Datas com conflito
        echo "<div class='test-result warning'>";
        echo "<strong>ERRO 3: Conflitos de horário não detectados</strong><br>";
        echo "A função verificarDisponibilidadeComTempo pode não estar detectando todos os conflitos.<br>";
        echo "<strong>Solução:</strong> Melhorar a lógica de verificação de sobreposição.";
        echo "</div>";
        
        // Erro 4: Timeout de execução
        echo "<div class='test-result warning'>";
        echo "<strong>ERRO 4: Timeout em cálculos complexos</strong><br>";
        echo "Funções como gerarOpcoesMultiDia podem demorar muito e causar timeout.<br>";
        echo "<strong>Solução:</strong> Limitar o número de dias verificados e usar cache.";
        echo "</div>";
        
        // Erro 5: Formato de datas/horas
        echo "<div class='test-result warning'>";
        echo "<strong>ERRO 5: Problemas com formato de datas/horas</strong><br>";
        echo "Incompatibilidade entre formatos (Y-m-d vs d/m/Y) pode causar erros.<br>";
        echo "<strong>Solução:</strong> Padronizar todos os formatos e validar entradas.";
        echo "</div>";
        
        echo "</div>";
        
        // Testar cenário de erro específico
        echo "<div class='test-item'>";
        echo "<strong>Testando cenário de data inválida:</strong><br>";
        
        try {
            // Tentar usar data malformada
            $data_invalida = '2024/02/30'; // Data inexistente
            $data_obj = DateTime::createFromFormat('Y-m-d', $data_invalida);
            
            if ($data_obj === false) {
                echo "✅ Data '{$data_invalida}' corretamente identificada como inválida<br>";
            } else {
                echo "⚠️ Data '{$data_invalida}' aceita mas pode ser problema<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Erro ao processar data: " . $e->getMessage() . "<br>";
        }
        
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // TESTES DE PERFORMANCE
    // ============================================================================
    
    public function testPerformance() {
        $this->startTest('Testes de Performance');
        
        echo "<div class='test-item'>";
        echo "<strong>Medindo tempo de execução das funções críticas:</strong><br><br>";
        
        // Teste 1: calcularJanelasDisponiveis
        $start = microtime(true);
        for ($i = 0; $i < 10; $i++) {
            calcularJanelasDisponiveis('2024-03-01', 240, 300);
        }
        $time1 = microtime(true) - $start;
        echo "calcularJanelasDisponiveis (10x): {$time1}s<br>";
        
        // Teste 2: calcularTempoTotalServicos
        $servicos = array_fill(0, 10, [
            'duracao_min_min' => 30,
            'duracao_max_min' => 60,
            'duracao_padrao_min' => 45,
            'quantidade' => 2
        ]);
        
        $start = microtime(true);
        calcularTempoTotalServicos($servicos);
        $time2 = microtime(true) - $start;
        echo "calcularTempoTotalServicos (10 serviços): {$time2}s<br>";
        
        // Teste 3: gerarDatasSimples (simulação)
        $start = microtime(true);
        // Não executar a função real para não afetar a sessão
        $time3 = microtime(true) - $start;
        echo "gerarDatasSimples (simulação): {$time3}s<br>";
        
        echo "<br><strong>Recomendações:</strong><br>";
        if ($time1 > 1.0) echo "⚠️ calcularJanelasDisponiveis está lenta - otimizar consultas ao banco<br>";
        if ($time2 > 0.5) echo "⚠️ calcularTempoTotalServicos está lenta - verificar loops<br>";
        
        echo "</div>";
        
        $this->endTest();
    }
    
    // ============================================================================
    // EXECUTAR TODOS OS TESTES
    // ============================================================================
    
    public function runAllTests() {
        echo "<div class='buttons'>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Funcoes Basicas').scrollIntoView();runTest('basic')\">▶️ Testar Funções Básicas</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Calculo de Tempo').scrollIntoView();runTest('tempo')\">▶️ Testar Cálculo de Tempo</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Acrescimos').scrollIntoView();runTest('acrescimos')\">▶️ Testar Acréscimos</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Valor Final').scrollIntoView();runTest('valor')\">▶️ Testar Valor Final</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Janelas de Horario').scrollIntoView();runTest('janelas')\">▶️ Testar Janelas (PROBLEMA)</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Datas Disponiveis').scrollIntoView();runTest('datas')\">▶️ Testar Datas (PROBLEMA)</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Processamento de Respostas').scrollIntoView();runTest('processamento')\">▶️ Testar Processamento</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Backup e Logs').scrollIntoView();runTest('backup')\">▶️ Testar Backup/Logs</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Simulacao de Erros Comuns').scrollIntoView();runTest('erros')\">▶️ Testar Erros</button>";
        echo "<button class='btn btn-run-section' onclick=\"document.getElementById('test-Testes de Performance').scrollIntoView();runTest('performance')\">▶️ Testar Performance</button>";
        echo "</div>";
        
        $this->testFuncoesBasicas();
        $this->testCalculoTempo();
        $this->testAcrescimos();
        $this->testValorFinal();
        $this->testJanelasHorario();
        $this->testDatasDisponiveis();
        $this->testProcessamentoRespostas();
        $this->testBackupLogs();
        $this->testErrosComuns();
        $this->testPerformance();
        
        // Resumo final
        echo "<div class='test-section'>";
        echo "<h2>📊 RESUMO DOS TESTES</h2>";
        
        $total = count($this->testResults);
        $success = count(array_filter($this->testResults, fn($r) => $r['type'] == 'success'));
        $errors = count(array_filter($this->testResults, fn($r) => $r['type'] == 'error'));
        $warnings = count(array_filter($this->testResults, fn($r) => $r['type'] == 'warning'));
        
        echo "<table>";
        echo "<tr><th>Total de Testes</th><td>$total</td></tr>";
        echo "<tr><th>✅ Sucessos</th><td>$success</td></tr>";
        echo "<tr><th>❌ Erros</th><td>$errors</td></tr>";
        echo "<tr><th>⚠️ Alertas</th><td>$warnings</td></tr>";
        echo "</table>";
        
        if ($errors > 0) {
            echo "<div class='test-result error'>";
            echo "<h3>🚨 PROBLEMAS ENCONTRADOS:</h3>";
            foreach ($this->testResults as $result) {
                if ($result['type'] == 'error') {
                    echo "<strong>{$result['test']}:</strong> {$result['message']}<br>";
                    if ($result['details']) {
                        echo "<div style='margin-left: 20px; font-size: 0.9em;'>{$result['details']}</div>";
                    }
                }
            }
            echo "</div>";
        }
        
        if ($warnings > 0) {
            echo "<div class='test-result warning'>";
            echo "<h3>⚠️ ALERTAS IMPORTANTES:</h3>";
            foreach ($this->testResults as $result) {
                if ($result['type'] == 'warning') {
                    echo "<strong>{$result['test']}:</strong> {$result['message']}<br>";
                }
            }
            echo "</div>";
        }
        
        echo "<div class='test-result info'>";
        echo "<h3>🔧 RECOMENDAÇÕES PARA CORREÇÃO:</h3>";
        echo "1. <strong>Problema principal:</strong> Função gerarOpcoesMultiDia() pode estar lançando exceções<br>";
        echo "2. <strong>Solução:</strong> Adicionar try-catch robusto e retornar array vazio em caso de erro<br>";
        echo "3. <strong>Outros pontos:</strong> Verificar conexão com banco em todas as funções que usam \$pdo<br>";
        echo "4. <strong>Validação:</strong> Validar formatos de data/hora em todas as entradas<br>";
        echo "5. <strong>Performance:</strong> Otimizar consultas ao banco que podem estar lentas<br>";
        echo "</div>";
        
        echo "</div>";
    }
}

// ============================================================================
// EXECUTAR OS TESTES
// ============================================================================

$tester = new TestSistemaIA();
$tester->runAllTests();

?>