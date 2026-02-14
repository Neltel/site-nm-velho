<?php
/**
 * admin/calculadora_tecnica.php
 * 
 * Calculadora Técnica para profissionais de refrigeração e ar condicionado
 * Ferramentas disponíveis:
 * 1. Cálculo de Carga Térmica
 * 2. Dimensionamento de Capacitor
 * 3. Bitola de Fio
 * 4. Conversor de BTUs
 * 5. Normas Técnicas
 */

require_once 'includes/auth.php';
require_once '../confg.php';

// Verificar autenticação
requireAdminAuth();

// Processar cálculos
$resultado = null;
$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_calculo'])) {
    $tipoCalculo = $_POST['tipo_calculo'];
    
    try {
        switch ($tipoCalculo) {
            case 'carga_termica':
                $resultado = calcularCargaTermica($_POST);
                break;
            case 'capacitor':
                $resultado = dimensionarCapacitor($_POST);
                break;
            case 'bitola_fio':
                $resultado = calcularBitolaFio($_POST);
                break;
            case 'converter_btus':
                $resultado = converterBTUs($_POST);
                break;
        }
        
        // Registrar uso da calculadora
        registrarLog('info', "Calculadora técnica utilizada: {$tipoCalculo}", [
            'dados' => $_POST
        ]);
        
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

/**
 * Calcula carga térmica necessária para um ambiente
 */
function calcularCargaTermica($dados) {
    // Dimensões do ambiente
    $comprimento = floatval($dados['comprimento'] ?? 0);
    $largura = floatval($dados['largura'] ?? 0);
    $altura = floatval($dados['altura'] ?? 2.5); // Padrão 2.5m
    
    // Fatores
    $pessoas = intval($dados['pessoas'] ?? 2);
    $janelas = intval($dados['janelas'] ?? 1);
    $parede_sol = isset($dados['parede_sol']) ? 1 : 0;
    $andar_cima = isset($dados['andar_cima']) ? 1 : 0;
    $equipamentos = intval($dados['equipamentos'] ?? 0);
    $iluminacao = intval($dados['iluminacao'] ?? 0);
    
    // Cálculo base (área x altura x 600)
    $area = $comprimento * $largura;
    $volume = $area * $altura;
    $btusBase = $volume * 600;
    
    // Adicional por pessoa (600 BTUs por pessoa)
    $btusPessoas = $pessoas * 600;
    
    // Adicional por janela (1000 BTUs por janela)
    $btusJanelas = $janelas * 1000;
    
    // Adicional se parede recebe sol (10%)
    $btusParede = $parede_sol ? ($btusBase * 0.10) : 0;
    
    // Adicional se tem andar acima (10%)
    $btusAndar = $andar_cima ? ($btusBase * 0.10) : 0;
    
    // Adicional por equipamentos eletrônicos (estimativa 500 BTUs cada)
    $btusEquipamentos = $equipamentos * 500;
    
    // Adicional por iluminação (estimativa 300 BTUs cada)
    $btusIluminacao = $iluminacao * 300;
    
    // Total
    $btusTotal = $btusBase + $btusPessoas + $btusJanelas + $btusParede + $btusAndar + $btusEquipamentos + $btusIluminacao;
    
    // Adicionar margem de segurança (15%)
    $btusComMargem = $btusTotal * 1.15;
    
    // Recomendar modelo
    $modelos = [
        7000 => '7.000 BTUs',
        9000 => '9.000 BTUs',
        12000 => '12.000 BTUs',
        18000 => '18.000 BTUs',
        24000 => '24.000 BTUs',
        30000 => '30.000 BTUs',
        36000 => '36.000 BTUs',
        48000 => '48.000 BTUs',
        60000 => '60.000 BTUs'
    ];
    
    $modeloRecomendado = 7000;
    foreach ($modelos as $btus => $nome) {
        if ($btusComMargem <= $btus) {
            $modeloRecomendado = $btus;
            break;
        }
    }
    
    return [
        'ambiente' => [
            'area' => round($area, 2) . ' m²',
            'volume' => round($volume, 2) . ' m³',
            'pessoas' => $pessoas,
            'janelas' => $janelas
        ],
        'calculos' => [
            'base' => round($btusBase),
            'pessoas' => round($btusPessoas),
            'janelas' => round($btusJanelas),
            'parede_sol' => round($btusParede),
            'andar_cima' => round($btusAndar),
            'equipamentos' => round($btusEquipamentos),
            'iluminacao' => round($btusIluminacao),
            'total_sem_margem' => round($btusTotal),
            'margem' => round($btusComMargem - $btusTotal),
            'total_com_margem' => round($btusComMargem)
        ],
        'recomendacao' => [
            'btus' => $modeloRecomendado,
            'modelo' => $modelos[$modeloRecomendado],
            'observacao' => 'Modelo recomendado com 15% de margem de segurança'
        ]
    ];
}

/**
 * Dimensiona capacitor para motor
 */
function dimensionarCapacitor($dados) {
    $potencia = floatval($dados['potencia'] ?? 0); // CV
    $tensao = intval($dados['tensao'] ?? 220); // V
    $fases = intval($dados['fases'] ?? 1); // 1 ou 3
    
    if ($potencia <= 0) {
        throw new Exception('Informe a potência do motor');
    }
    
    // Fórmula simplificada para motor monofásico
    if ($fases == 1) {
        // Capacitor de partida: 70-100 µF por CV
        $capacitorPartida = $potencia * 85;
        
        // Capacitor de trabalho: 30-40 µF por CV
        $capacitorTrabalho = $potencia * 35;
        
        return [
            'motor' => [
                'potencia' => $potencia . ' CV',
                'tensao' => $tensao . ' V',
                'tipo' => 'Monofásico'
            ],
            'capacitores' => [
                'partida' => [
                    'capacitancia' => round($capacitorPartida) . ' µF',
                    'tensao' => ($tensao > 127) ? '250V' : '150V',
                    'tipo' => 'Eletrolítico'
                ],
                'trabalho' => [
                    'capacitancia' => round($capacitorTrabalho) . ' µF',
                    'tensao' => ($tensao > 127) ? '250V' : '150V',
                    'tipo' => 'Permanente (run)'
                ]
            ],
            'observacao' => 'Valores aproximados. Consulte especificações do fabricante.'
        ];
    } else {
        return [
            'motor' => [
                'potencia' => $potencia . ' CV',
                'tipo' => 'Trifásico'
            ],
            'capacitores' => 'Motores trifásicos geralmente não requerem capacitor de partida',
            'observacao' => 'Para correção do fator de potência, consulte engenheiro elétrico.'
        ];
    }
}

/**
 * Calcula bitola de fio adequada
 */
function calcularBitolaFio($dados) {
    $corrente = floatval($dados['corrente'] ?? 0); // Amperes
    $distancia = floatval($dados['distancia'] ?? 0); // Metros
    $tensao = intval($dados['tensao'] ?? 220); // Volts
    $tipo_instalacao = $dados['tipo_instalacao'] ?? 'embutido';
    
    if ($corrente <= 0) {
        throw new Exception('Informe a corrente em Amperes');
    }
    
    // Tabela simplificada de bitolas (NBR 5410)
    // [corrente_max, bitola_mm2, bitola_awg]
    $tabela = [
        [10, 1.5, '16 AWG'],
        [16, 2.5, '14 AWG'],
        [24, 4, '12 AWG'],
        [32, 6, '10 AWG'],
        [41, 10, '8 AWG'],
        [57, 16, '6 AWG'],
        [76, 25, '4 AWG'],
        [101, 35, '2 AWG'],
        [125, 50, '1 AWG'],
        [151, 70, '1/0 AWG'],
        [183, 95, '2/0 AWG']
    ];
    
    $bitolaRecomendada = null;
    foreach ($tabela as $item) {
        if ($corrente <= $item[0]) {
            $bitolaRecomendada = $item;
            break;
        }
    }
    
    if (!$bitolaRecomendada) {
        $bitolaRecomendada = end($tabela);
    }
    
    // Verificar queda de tensão (3% máximo)
    $quedaTensao = (2 * $distancia * $corrente * 0.0175) / $bitolaRecomendada[1];
    $quedaPercentual = ($quedaTensao / $tensao) * 100;
    
    $alertaQueda = '';
    if ($quedaPercentual > 3) {
        $alertaQueda = '⚠️ ATENÇÃO: Queda de tensão acima de 3%. Considere usar bitola maior!';
    }
    
    return [
        'instalacao' => [
            'corrente' => $corrente . ' A',
            'distancia' => $distancia . ' m',
            'tensao' => $tensao . ' V',
            'tipo' => $tipo_instalacao
        ],
        'bitola_recomendada' => [
            'mm2' => $bitolaRecomendada[1] . ' mm²',
            'awg' => $bitolaRecomendada[2],
            'corrente_max' => $bitolaRecomendada[0] . ' A'
        ],
        'queda_tensao' => [
            'volts' => round($quedaTensao, 2) . ' V',
            'percentual' => round($quedaPercentual, 2) . ' %',
            'status' => $quedaPercentual <= 3 ? '✅ Adequada' : '❌ Elevada',
            'alerta' => $alertaQueda
        ],
        'observacao' => 'Cálculo baseado na NBR 5410. Consulte um eletricista qualificado.'
    ];
}

/**
 * Conversor de unidades BTUs
 */
function converterBTUs($dados) {
    $valor = floatval($dados['valor'] ?? 0);
    $de = $dados['de'] ?? 'btu';
    $para = $dados['para'] ?? 'watts';
    
    // Fatores de conversão
    $conversoes = [
        'btu_watts' => 0.293071,
        'watts_btu' => 3.41214,
        'btu_kcal' => 0.252164,
        'kcal_btu' => 3.96567,
        'btu_kw' => 0.000293071,
        'kw_btu' => 3412.14
    ];
    
    $chave = "{$de}_{$para}";
    $fator = $conversoes[$chave] ?? 1;
    $resultado = $valor * $fator;
    
    return [
        'entrada' => $valor . ' ' . strtoupper($de),
        'saida' => round($resultado, 2) . ' ' . strtoupper($para),
        'fator' => $fator,
        'formula' => "{$valor} × {$fator} = " . round($resultado, 2)
    ];
}

// Carregar header
require_once 'includes/header-admin.php';
?>

<div class="page-header">
    <h2><i class="fas fa-calculator me-2"></i> Calculadora Técnica</h2>
    <p class="text-muted">Ferramentas para cálculos técnicos de refrigeração e ar condicionado</p>
</div>

<?php if ($erro): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($erro); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Selecione o Tipo de Cálculo</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills mb-4" id="calculoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#cargaTermica" type="button">
                            <i class="fas fa-thermometer-half me-2"></i>Carga Térmica
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#capacitor" type="button">
                            <i class="fas fa-bolt me-2"></i>Capacitor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#bitolaFio" type="button">
                            <i class="fas fa-cable-car me-2"></i>Bitola de Fio
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#conversor" type="button">
                            <i class="fas fa-exchange-alt me-2"></i>Conversor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#normas" type="button">
                            <i class="fas fa-book me-2"></i>Normas
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab: Carga Térmica -->
                    <div class="tab-pane fade show active" id="cargaTermica" role="tabpanel">
                        <h5>Cálculo de Carga Térmica</h5>
                        <p class="text-muted">Calcule a capacidade necessária de ar condicionado para o ambiente</p>
                        
                        <form method="POST">
                            <input type="hidden" name="tipo_calculo" value="carga_termica">
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label>Comprimento (m)</label>
                                        <input type="number" step="0.1" name="comprimento" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label>Largura (m)</label>
                                        <input type="number" step="0.1" name="largura" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label>Altura (m)</label>
                                        <input type="number" step="0.1" name="altura" class="form-control" value="2.5" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Número de Pessoas</label>
                                        <input type="number" name="pessoas" class="form-control" value="2" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Número de Janelas</label>
                                        <input type="number" name="janelas" class="form-control" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Equipamentos Eletrônicos</label>
                                        <input type="number" name="equipamentos" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Lâmpadas/Iluminação</label>
                                        <input type="number" name="iluminacao" class="form-control" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="parede_sol" class="form-check-input" id="paredeSol">
                                        <label class="form-check-label" for="paredeSol">
                                            Parede recebe sol direto
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="andar_cima" class="form-check-input" id="andarCima">
                                        <label class="form-check-label" for="andarCima">
                                            Tem andar acima sem isolamento
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calculator me-2"></i>Calcular
                            </button>
                        </form>

                        <?php if ($resultado && $_POST['tipo_calculo'] === 'carga_termica'): ?>
                            <div class="mt-4">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle me-2"></i>Resultado do Cálculo</h5>
                                    <hr>
                                    <p><strong>Ambiente:</strong> <?php echo $resultado['ambiente']['area']; ?> (<?php echo $resultado['ambiente']['volume']; ?>)</p>
                                    <p><strong>BTUs Calculados:</strong> <?php echo number_format($resultado['calculos']['total_com_margem'], 0, ',', '.'); ?> BTUs</p>
                                    <p><strong>Modelo Recomendado:</strong> <?php echo $resultado['recomendacao']['modelo']; ?></p>
                                    <p class="mb-0"><em><?php echo $resultado['recomendacao']['observacao']; ?></em></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab: Capacitor -->
                    <div class="tab-pane fade" id="capacitor" role="tabpanel">
                        <h5>Dimensionamento de Capacitor</h5>
                        <form method="POST">
                            <input type="hidden" name="tipo_calculo" value="capacitor">
                            <!-- Formulário de capacitor aqui -->
                            <p class="text-muted">Em desenvolvimento...</p>
                        </form>
                    </div>

                    <!-- Outros tabs... -->
                    
                    <!-- Tab: Normas -->
                    <div class="tab-pane fade" id="normas" role="tabpanel">
                        <h5>Normas Técnicas</h5>
                        <div class="list-group">
                            <div class="list-group-item">
                                <h6>NBR 16401 - Instalações de Ar-Condicionado</h6>
                                <p class="mb-0 small text-muted">Sistemas centrais e unitários</p>
                            </div>
                            <div class="list-group-item">
                                <h6>NBR 5410 - Instalações Elétricas de Baixa Tensão</h6>
                                <p class="mb-0 small text-muted">Norma para instalações elétricas</p>
                            </div>
                            <div class="list-group-item">
                                <h6>NR-12 - Segurança em Máquinas e Equipamentos</h6>
                                <p class="mb-0 small text-muted">Norma regulamentadora</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer-admin.php'; ?>
