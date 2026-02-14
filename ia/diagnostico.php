<?php
// ia/diagnostico.php
session_start();
include '../includes/config.php';

// Verificar se está logado (técnico ou admin)
if(!isset($_SESSION['admin_logado']) && !isset($_SESSION['tecnico_logado'])) {
    header('Location: ../admin/login.php');
    exit;
}

$pagina = $_GET['pagina'] ?? 'inicio';
$mensagem = '';

// Processar diagnóstico
if($_POST && isset($_POST['iniciar_diagnostico'])) {
    $marca = sanitize($_POST['marca']);
    $modelo = sanitize($_POST['modelo']);
    $btus = $_POST['btus'];
    $tipo_equipamento = sanitize($_POST['tipo_equipamento']);
    $sintoma = sanitize($_POST['sintoma']);
    $codigo_erro = sanitize($_POST['codigo_erro']);
    
    // Salvar na sessão
    $_SESSION['diagnostico'] = [
        'marca' => $marca,
        'modelo' => $modelo,
        'btus' => $btus,
        'tipo_equipamento' => $tipo_equipamento,
        'sintoma' => $sintoma,
        'codigo_erro' => $codigo_erro,
        'data_inicio' => date('Y-m-d H:i:s')
    ];
    
    header('Location: diagnostico.php?pagina=checklist');
    exit;
}

// Processar checklist
if($_POST && isset($_POST['processar_checklist'])) {
    $dados_checklist = $_POST;
    
    // Analisar resultados com IA
    $resultado_ia = analisarChecklistIA($dados_checklist, $_SESSION['diagnostico']);
    
    $_SESSION['diagnostico']['resultado_ia'] = $resultado_ia;
    $_SESSION['diagnostico']['checklist'] = $dados_checklist;
    
    header('Location: diagnostico.php?pagina=resultado');
    exit;
}

// Função IA para análise do checklist
function analisarChecklistIA($checklist, $diagnostico) {
    $problemas = [];
    $solucoes = [];
    $gravidade = 'baixa';
    
    // Análise de temperatura (ΔT)
    if(isset($checklist['temp_entrada']) && isset($checklist['temp_saida'])) {
        $delta_t = $checklist['temp_entrada'] - $checklist['temp_saida'];
        
        if($delta_t < 8) {
            $problemas[] = "Baixa diferença de temperatura (ΔT = {$delta_t}°C) - Ideal é entre 8°C e 15°C";
            $solucoes[] = "Verificar carga de gás, limpeza de filtros e serpentinas";
            $gravidade = 'media';
        } elseif($delta_t > 15) {
            $problemas[] = "Alta diferença de temperatura (ΔT = {$delta_t}°C) - Pode indicar superaquecimento";
            $solucoes[] = "Verificar ventilador, fluxo de ar e temperatura ambiente";
            $gravidade = 'media';
        }
    }
    
    // Análise de tensão
    if(isset($checklist['tensao_alimentacao'])) {
        $tensao = $checklist['tensao_alimentacao'];
        $tensao_ideal = 220; // Para maioria dos equipamentos
        
        if($tensao < 200 || $tensao > 240) {
            $problemas[] = "Tensão fora da faixa ideal: {$tensao}V (Ideal: 220V ±10%)";
            $solucoes[] = "Verificar instalação elétrica e estabilizador";
            $gravidade = 'alta';
        }
    }
    
    // Análise de corrente
    if(isset($checklist['corrente_compressor'])) {
        $corrente = $checklist['corrente_compressor'];
        $btus = $diagnostico['btus'];
        
        // Valores de referência por BTU
        $corrente_esperada = calcularCorrenteEsperada($btus);
        
        if($corrente > $corrente_esperada * 1.2) {
            $problemas[] = "Corrente elevada no compressor: {$corrente}A (Esperado: ~{$corrente_esperada}A)";
            $solucoes[] = "Verificar compressor, capacitor e ventilação";
            $gravidade = 'alta';
        } elseif($corrente < $corrente_esperada * 0.8) {
            $problemas[] = "Corrente baixa no compressor: {$corrente}A (Esperado: ~{$corrente_esperada}A)";
            $solucoes[] = "Verificar capacitor de partida e fiação";
            $gravidade = 'media';
        }
    }
    
    // Análise de código de erro
    if(!empty($diagnostico['codigo_erro'])) {
        $info_erro = buscarCodigoErro($diagnostico['marca'], $diagnostico['codigo_erro']);
        if($info_erro) {
            $problemas[] = "Código de erro {$diagnostico['codigo_erro']}: {$info_erro['descricao']}";
            $solucoes[] = $info_erro['solucao'];
            $gravidade = $info_erro['gravidade'] ?? 'media';
        }
    }
    
    // Análise de sensores
    if(isset($checklist['sensor_ambiente']) && isset($checklist['sensor_serpentina'])) {
        $sensor_ambiente = $checklist['sensor_ambiente'];
        $sensor_serpentina = $checklist['sensor_serpentina'];
        
        // Verificar se os sensores estão dentro da faixa esperada
        if($sensor_ambiente < 1000 || $sensor_ambiente > 30000) {
            $problemas[] = "Sensor de ambiente com valor fora da faixa: {$sensor_ambiente}Ω";
            $solucoes[] = "Testar e possivelmente substituir sensor de temperatura ambiente";
            $gravidade = 'media';
        }
        
        if($sensor_serpentina < 1000 || $sensor_serpentina > 30000) {
            $problemas[] = "Sensor de serpentina com valor fora da faixa: {$sensor_serpentina}Ω";
            $solucoes[] = "Testar e possivelmente substituir sensor de temperatura da serpentina";
            $gravidade = 'media';
        }
    }
    
    // Análise de isolamento
    if(isset($checklist['teste_isolamento'])) {
        $isolamento = $checklist['teste_isolamento'];
        
        if($isolamento < 2) {
            $problemas[] = "Isolamento elétrico insuficiente: {$isolamento}MΩ (Mínimo: 2MΩ)";
            $solucoes[] = "Verificar fiação, compressor e componentes por fuga de corrente";
            $gravidade = 'alta';
        }
    }
    
    return [
        'problemas' => $problemas,
        'solucoes' => $solucoes,
        'gravidade' => $gravidade,
        'diagnostico_final' => count($problemas) > 0 ? 'Equipamento com defeito' : 'Equipamento em condições normais',
        'recomendacoes' => gerarRecomendacoes($checklist, $diagnostico)
    ];
}

// Função auxiliar para calcular corrente esperada
function calcularCorrenteEsperada($btus) {
    $tabela_corrente = [
        9000 => 4.5,
        12000 => 6.0,
        18000 => 9.0,
        24000 => 12.0,
        30000 => 15.0,
        36000 => 18.0,
        48000 => 24.0,
        60000 => 30.0
    ];
    
    return $tabela_corrente[$btus] ?? $btus / 2000; // Aproximação geral
}

// Função para buscar código de erro
function buscarCodigoErro($marca, $codigo) {
    include 'codigos_erro.php';
    
    if(isset($codigos_erro[$marca][$codigo])) {
        return $codigos_erro[$marca][$codigo];
    }
    
    // Buscar em códigos genéricos
    foreach($codigos_erro['GENERICO'] as $padrao => $info) {
        if(strpos($codigo, $padrao) !== false) {
            return $info;
        }
    }
    
    return null;
}

// Função para gerar recomendações
function gerarRecomendacoes($checklist, $diagnostico) {
    $recomendacoes = [];
    
    // Recomendações baseadas nos dados coletados
    if(isset($checklist['temp_ambiente']) && $checklist['temp_ambiente'] > 30) {
        $recomendacoes[] = "Ambiente muito quente pode sobrecarregar o equipamento";
    }
    
    if(isset($checklist['limpeza_filtros']) && $checklist['limpeza_filtros'] == 'ruim') {
        $recomendacoes[] = "Realizar limpeza completa dos filtros";
    }
    
    if(isset($checklist['vazamento_gas']) && $checklist['vazamento_gas'] == 'sim') {
        $recomendacoes[] = "Localizar e reparar vazamento de gás antes de recarregar";
    }
    
    // Recomendações gerais de manutenção
    $recomendacoes[] = "Manutenção preventiva a cada 6 meses";
    $recomendacoes[] = "Limpeza de filtros a cada 15 dias em uso intenso";
    
    return $recomendacoes;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Diagnóstico IA - ClimaTech</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .diagnostico-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .diagnostico-step {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        
        .step.active {
            background: var(--primary);
            color: white;
        }
        
        .step.completed {
            background: var(--success);
            color: white;
        }
        
        .ia-resultado {
            border-left: 4px solid var(--primary);
            padding-left: 20px;
            margin: 20px 0;
        }
        
        .problema-item {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .solucao-item {
            background: #d1edff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .gravidade-alta {
            border-left-color: var(--danger);
        }
        
        .gravidade-media {
            border-left-color: var(--warning);
        }
        
        .gravidade-baixa {
            border-left-color: var(--success);
        }
    </style>
</head>
<body>
    <?php include '../admin/includes/header-admin.php'; ?>
    
    <div class="admin-main">
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../admin/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="diagnostico.php" class="active">🔍 Diagnóstico IA</a></li>
                    <li><a href="checklist.php">📋 Checklist</a></li>
                    <li><a href="relatorios.php">📄 Relatórios</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-content">
            <div class="diagnostico-container">
                <div class="page-header">
                    <h2>🔍 Sistema de Diagnóstico por IA</h2>
                    <p>Diagnóstico inteligente para equipamentos de ar condicionado</p>
                </div>
                
                <!-- Indicador de Passos -->
                <div class="step-indicator">
                    <div class="step <?php echo $pagina == 'inicio' ? 'active' : 'completed'; ?>">1</div>
                    <div class="step <?php echo $pagina == 'checklist' ? 'active' : ($pagina == 'resultado' ? 'completed' : ''); ?>">2</div>
                    <div class="step <?php echo $pagina == 'resultado' ? 'active' : ''; ?>">3</div>
                </div>
                
                <?php if($mensagem): ?>
                <div class="alert alert-info"><?php echo $mensagem; ?></div>
                <?php endif; ?>
                
                <!-- Página Inicial do Diagnóstico -->
                <?php if($pagina == 'inicio'): ?>
                <div class="diagnostico-step">
                    <h3>Informações do Equipamento</h3>
                    <p>Preencha os dados do equipamento para iniciar o diagnóstico</p>
                    
                    <form method="POST" class="form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="marca">Marca do Equipamento *</label>
                                <select id="marca" name="marca" class="form-control" required>
                                    <option value="">Selecione a marca</option>
                                    <option value="Samsung">Samsung</option>
                                    <option value="LG">LG</option>
                                    <option value="Midea">Midea</option>
                                    <option value="Gree">Gree</option>
                                    <option value="TCL">TCL</option>
                                    <option value="Springer">Springer</option>
                                    <option value="Consul">Consul</option>
                                    <option value="Philco">Philco</option>
                                    <option value="Elgin">Elgin</option>
                                    <option value="Daikin">Daikin</option>
                                    <option value="Fujitsu">Fujitsu</option>
                                    <option value="Outra">Outra</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" id="modelo" name="modelo" class="form-control" placeholder="Ex: Wind-Free, Dual Inverter, etc.">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="btus">Capacidade (BTUs) *</label>
                                <select id="btus" name="btus" class="form-control" required>
                                    <option value="">Selecione os BTUs</option>
                                    <option value="7000">7.000 BTUs</option>
                                    <option value="9000">9.000 BTUs</option>
                                    <option value="12000">12.000 BTUs</option>
                                    <option value="18000">18.000 BTUs</option>
                                    <option value="24000">24.000 BTUs</option>
                                    <option value="30000">30.000 BTUs</option>
                                    <option value="36000">36.000 BTUs</option>
                                    <option value="48000">48.000 BTUs</option>
                                    <option value="60000">60.000 BTUs</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tipo_equipamento">Tipo de Equipamento *</label>
                                <select id="tipo_equipamento" name="tipo_equipamento" class="form-control" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="Split Hi-Wall">Split Hi-Wall</option>
                                    <option value="Split Piso-Teto">Split Piso-Teto</option>
                                    <option value="Split Cassete">Split Cassete</option>
                                    <option value="Split Inverter">Split Inverter</option>
                                    <option value="Janela">Janela</option>
                                    <option value="Portátil">Portátil</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="sintoma">Sintoma/Problema *</label>
                            <select id="sintoma" name="sintoma" class="form-control" required>
                                <option value="">Selecione o sintoma principal</option>
                                <option value="nao_liga">Não liga</option>
                                <option value="nao_resfria">Não resfria</option>
                                <option value="resfria_pouco">Resfria pouco</option>
                                <option value="gela_e_descongela">Gela e descongela</option>
                                <option value="barulho_incomum">Barulho incomum</option>
                                <option value="vazamento_agua">Vazamento de água</option>
                                <option value="cheiro_estranho">Cheiro estranho</option>
                                <option value="luz_piscando">Luz piscando/painel com erro</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="codigo_erro">Código de Erro (se aplicável)</label>
                            <input type="text" id="codigo_erro" name="codigo_erro" class="form-control" placeholder="Ex: E1, P1, F3, etc.">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="iniciar_diagnostico" class="btn btn-primary">
                                Iniciar Diagnóstico
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Checklist Técnico -->
                <?php if($pagina == 'checklist' && isset($_SESSION['diagnostico'])): 
                $diagnostico = $_SESSION['diagnostico'];
                ?>
                <div class="diagnostico-step">
                    <h3>Checklist Técnico - <?php echo $diagnostico['marca']; ?> <?php echo $diagnostico['btus']; ?> BTUs</h3>
                    <p>Preencha as medições e observações técnicas</p>
                    
                    <form method="POST" class="form">
                        <!-- Medições de Temperatura -->
                        <div class="form-section">
                            <h4>🌡️ Medições de Temperatura</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="temp_ambiente">Temperatura Ambiente (°C)</label>
                                    <input type="number" id="temp_ambiente" name="temp_ambiente" class="form-control" step="0.1" placeholder="Ex: 25.5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="temp_entrada">Temp. Entrada/Retorno (°C)</label>
                                    <input type="number" id="temp_entrada" name="temp_entrada" class="form-control" step="0.1" placeholder="Ex: 24.0">
                                </div>
                                
                                <div class="form-group">
                                    <label for="temp_saida">Temp. Saída/Insuflamento (°C)</label>
                                    <input type="number" id="temp_saida" name="temp_saida" class="form-control" step="0.1" placeholder="Ex: 15.5">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="delta_t_calculado">ΔT Calculado (Diferença)</label>
                                <input type="text" id="delta_t_calculado" class="form-control" readonly style="background: #f8f9fa;">
                                <small class="form-text">Ideal: 8°C a 15°C</small>
                            </div>
                        </div>
                        
                        <!-- Medições Elétricas -->
                        <div class="form-section">
                            <h4>⚡ Medições Elétricas</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="tensao_alimentacao">Tensão de Alimentação (V)</label>
                                    <input type="number" id="tensao_alimentacao" name="tensao_alimentacao" class="form-control" step="0.1" placeholder="Ex: 220.0">
                                </div>
                                
                                <div class="form-group">
                                    <label for="corrente_compressor">Corrente do Compressor (A)</label>
                                    <input type="number" id="corrente_compressor" name="corrente_compressor" class="form-control" step="0.1" placeholder="Ex: 6.5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="corrente_ventilador">Corrente do Ventilador (A)</label>
                                    <input type="number" id="corrente_ventilador" name="corrente_ventilador" class="form-control" step="0.1" placeholder="Ex: 0.8">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sensores -->
                        <div class="form-section">
                            <h4>🌡️ Verificação de Sensores</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sensor_ambiente">Sensor Ambiente (Ω)</label>
                                    <input type="number" id="sensor_ambiente" name="sensor_ambiente" class="form-control" placeholder="Resistência em Ohms">
                                </div>
                                
                                <div class="form-group">
                                    <label for="sensor_serpentina">Sensor Serpentina (Ω)</label>
                                    <input type="number" id="sensor_serpentina" name="sensor_serpentina" class="form-control" placeholder="Resistência em Ohms">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testes de Isolamento -->
                        <div class="form-section">
                            <h4>🔌 Teste de Isolamento</h4>
                            
                            <div class="form-group">
                                <label for="teste_isolamento">Isolamento Elétrico (MΩ)</label>
                                <input type="number" id="teste_isolamento" name="teste_isolamento" class="form-control" step="0.1" placeholder="Ex: 5.0">
                                <small class="form-text">Mínimo aceitável: 2MΩ</small>
                            </div>
                        </div>
                        
                        <!-- Observações Visuais -->
                        <div class="form-section">
                            <h4>👀 Observações Visuais</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="limpeza_filtros">Estado dos Filtros</label>
                                    <select id="limpeza_filtros" name="limpeza_filtros" class="form-control">
                                        <option value="">Selecione</option>
                                        <option value="bom">Bom</option>
                                        <option value="regular">Regular</option>
                                        <option value="ruim">Ruim/Sujos</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="vazamento_gas">Indício de Vazamento</label>
                                    <select id="vazamento_gas" name="vazamento_gas" class="form-control">
                                        <option value="">Selecione</option>
                                        <option value="nao">Não</option>
                                        <option value="suspeita">Suspeita</option>
                                        <option value="sim">Sim/Confirmado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="observacoes_gerais">Observações Gerais</label>
                                <textarea id="observacoes_gerais" name="observacoes_gerais" class="form-control" rows="4" placeholder="Descreva outras observações importantes..."></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="processar_checklist" class="btn btn-primary">
                                Processar com IA
                            </button>
                            <a href="diagnostico.php?pagina=inicio" class="btn btn-secondary">Voltar</a>
                        </div>
                    </form>
                </div>
                
                <script>
                // Cálculo automático do ΔT
                document.addEventListener('DOMContentLoaded', function() {
                    const tempEntrada = document.getElementById('temp_entrada');
                    const tempSaida = document.getElementById('temp_saida');
                    const deltaT = document.getElementById('delta_t_calculado');
                    
                    function calcularDeltaT() {
                        if(tempEntrada.value && tempSaida.value) {
                            const entrada = parseFloat(tempEntrada.value);
                            const saida = parseFloat(tempSaida.value);
                            const resultado = entrada - saida;
                            deltaT.value = resultado.toFixed(1) + '°C';
                            
                            // Colorir conforme resultado
                            if(resultado >= 8 && resultado <= 15) {
                                deltaT.style.borderColor = '#28a745';
                            } else {
                                deltaT.style.borderColor = '#dc3545';
                            }
                        } else {
                            deltaT.value = '';
                        }
                    }
                    
                    tempEntrada.addEventListener('input', calcularDeltaT);
                    tempSaida.addEventListener('input', calcularDeltaT);
                });
                </script>
                <?php endif; ?>
                
                <!-- Resultado do Diagnóstico -->
                <?php if($pagina == 'resultado' && isset($_SESSION['diagnostico']['resultado_ia'])): 
                $diagnostico = $_SESSION['diagnostico'];
                $resultado = $diagnostico['resultado_ia'];
                ?>
                <div class="diagnostico-step">
                    <h3>🎯 Resultado do Diagnóstico por IA</h3>
                    
                    <div class="ia-resultado <?php echo 'gravidade-' . $resultado['gravidade']; ?>">
                        <h4>Diagnóstico Final: <?php echo $resultado['diagnostico_final']; ?></h4>
                        <p>Equipamento: <?php echo $diagnostico['marca']; ?> <?php echo $diagnostico['btus']; ?> BTUs - <?php echo $diagnostico['tipo_equipamento']; ?></p>
                        <p>Sintoma reportado: <?php echo $diagnostico['sintoma']; ?></p>
                        <?php if($diagnostico['codigo_erro']): ?>
                        <p>Código de erro: <?php echo $diagnostico['codigo_erro']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Problemas Identificados -->
                    <?php if(count($resultado['problemas']) > 0): ?>
                    <div class="form-section">
                        <h4>⚠️ Problemas Identificados</h4>
                        <?php foreach($resultado['problemas'] as $problema): ?>
                        <div class="problema-item">
                            <?php echo $problema; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success">
                        <h4>✅ Nenhum problema crítico identificado</h4>
                        <p>O equipamento está funcionando dentro dos parâmetros normais.</p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Soluções Recomendadas -->
                    <?php if(count($resultado['solucoes']) > 0): ?>
                    <div class="form-section">
                        <h4>🔧 Soluções Recomendadas</h4>
                        <?php foreach($resultado['solucoes'] as $solucao): ?>
                        <div class="solucao-item">
                            <?php echo $solucao; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recomendações Gerais -->
                    <?php if(count($resultado['recomendacoes']) > 0): ?>
                    <div class="form-section">
                        <h4>💡 Recomendações</h4>
                        <ul>
                            <?php foreach($resultado['recomendacoes'] as $recomendacao): ?>
                            <li><?php echo $recomendacao; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Ações -->
                    <div class="form-actions">
                        <a href="relatorios.php?gerar_pdf=1" class="btn btn-primary" target="_blank">
                            📄 Gerar Relatório PDF
                        </a>
                        <a href="diagnostico.php?pagina=inicio" class="btn btn-secondary">
                            🆕 Novo Diagnóstico
                        </a>
                        <a href="../admin/dashboard.php" class="btn btn-outline">
                            📊 Voltar ao Dashboard
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <?php include '../admin/includes/footer-admin.php'; ?>
</body>
</html>