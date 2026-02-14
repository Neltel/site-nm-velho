<?php
// ia/calculos_tecnicos.php

/**
 * Calcula a diferença de temperatura (ΔT)
 */
function calcularDeltaT($temp_entrada, $temp_saida) {
    return $temp_entrada - $temp_saida;
}

/**
 * Avalia se o ΔT está dentro do ideal
 */
function avaliarDeltaT($delta_t) {
    if($delta_t >= 8 && $delta_t <= 15) {
        return [
            'status' => 'ideal',
            'mensagem' => 'ΔT dentro da faixa ideal (8°C - 15°C)',
            'gravidade' => 'baixa'
        ];
    } elseif($delta_t < 8) {
        return [
            'status' => 'baixo',
            'mensagem' => "ΔT baixo ({$delta_t}°C) - Pode indicar baixa carga de gás ou filtros sujos",
            'gravidade' => 'media'
        ];
    } else {
        return [
            'status' => 'alto',
            'mensagem' => "ΔT alto ({$delta_t}°C) - Pode indicar superaquecimento",
            'gravidade' => 'media'
        ];
    }
}

/**
 * Calcula corrente esperada baseada nos BTUs
 */
function calcularCorrenteEsperada($btus, $tensao = 220) {
    $tabela_corrente = [
        7000 => ['220v' => 3.5, '127v' => 6.1],
        9000 => ['220v' => 4.5, '127v' => 7.8],
        12000 => ['220v' => 6.0, '127v' => 10.4],
        18000 => ['220v' => 9.0, '127v' => 15.6],
        24000 => ['220v' => 12.0, '127v' => 20.8],
        30000 => ['220v' => 15.0, '127v' => 26.0],
        36000 => ['220v' => 18.0, '127v' => 31.2],
        48000 => ['220v' => 24.0, '127v' => 41.6],
        60000 => ['220v' => 30.0, '127v' => 52.0]
    ];
    
    if(isset($tabela_corrente[$btus])) {
        $tensao_chave = $tensao >= 200 ? '220v' : '127v';
        return $tabela_corrente[$btus][$tensao_chave];
    }
    
    // Cálculo aproximado se não estiver na tabela
    return $btus / ($tensao >= 200 ? 2000 : 1150);
}

/**
 * Avalia se a corrente está dentro do esperado
 */
function avaliarCorrente($corrente_medida, $corrente_esperada) {
    $tolerancia = 0.2; // 20% de tolerância
    
    $minima = $corrente_esperada * (1 - $tolerancia);
    $maxima = $corrente_esperada * (1 + $tolerancia);
    
    if($corrente_medida >= $minima && $corrente_medida <= $maxima) {
        return [
            'status' => 'normal',
            'mensagem' => "Corrente dentro da faixa esperada ({$minima}A - {$maxima}A)",
            'gravidade' => 'baixa'
        ];
    } elseif($corrente_medida < $minima) {
        return [
            'status' => 'baixa',
            'mensagem' => "Corrente baixa ({$corrente_medida}A) - Esperado: ~{$corrente_esperada}A",
            'gravidade' => 'media'
        ];
    } else {
        return [
            'status' => 'alta',
            'mensagem' => "Corrente alta ({$corrente_medida}A) - Esperado: ~{$corrente_esperada}A",
            'gravidade' => 'alta'
        ];
    }
}

/**
 * Avalia tensão de alimentação
 */
function avaliarTensao($tensao) {
    if($tensao >= 200 && $tensao <= 240) {
        return [
            'status' => 'ideal',
            'mensagem' => 'Tensão dentro da faixa ideal (200V - 240V)',
            'gravidade' => 'baixa'
        ];
    } elseif($tensao >= 190 && $tensao < 200) {
        return [
            'status' => 'baixa',
            'mensagem' => "Tensão um pouco baixa ({$tensao}V) - Pode afetar desempenho",
            'gravidade' => 'media'
        ];
    } elseif($tensao > 240 && $tensao <= 250) {
        return [
            'status' => 'alta',
            'mensagem' => "Tensão um pouco alta ({$tensao}V) - Pode danificar componentes",
            'gravidade' => 'media'
        ];
    } else {
        return [
            'status' => 'critica',
            'mensagem' => "Tensão crítica ({$tensao}V) - Risco de danos ao equipamento",
            'gravidade' => 'alta'
        ];
    }
}

/**
 * Avalia resistência de sensores NTC
 */
function avaliarSensorNTC($resistencia) {
    if($resistencia >= 5000 && $resistencia <= 30000) {
        return [
            'status' => 'normal',
            'mensagem' => 'Sensor dentro da faixa operacional',
            'gravidade' => 'baixa'
        ];
    } elseif($resistencia < 1000) {
        return [
            'status' => 'curto',
            'mensagem' => "Possível curto no sensor ({$resistencia}Ω)",
            'gravidade' => 'alta'
        ];
    } elseif($resistencia > 100000) {
        return [
            'status' => 'aberto',
            'mensagem' => "Possível circuito aberto no sensor ({$resistencia}Ω)",
            'gravidade' => 'alta'
        ];
    } else {
        return [
            'status' => 'fora_faixa',
            'mensagem' => "Sensor fora da faixa esperada ({$resistencia}Ω)",
            'gravidade' => 'media'
        ];
    }
}

/**
 * Avalia teste de isolamento
 */
function avaliarIsolamento($megohms) {
    if($megohms >= 2) {
        return [
            'status' => 'bom',
            'mensagem' => "Isolamento adequado ({$megohms}MΩ)",
            'gravidade' => 'baixa'
        ];
    } elseif($megohms >= 1 && $megohms < 2) {
        return [
            'status' => 'baixo',
            'mensagem' => "Isolamento baixo ({$megohms}MΩ) - Pode indicar umidade ou desgaste",
            'gravidade' => 'media'
        ];
    } else {
        return [
            'status' => 'critico',
            'mensagem' => "Isolamento crítico ({$megohms}MΩ) - Risco de choque elétrico",
            'gravidade' => 'alta'
        ];
    }
}

/**
 * Calcula consumo aproximado em kWh
 */
function calcularConsumo($btus, $horas_uso = 8, $dias = 30, $preco_kwh = 0.80) {
    // Converter BTUs para Watts (aproximação)
    $watts = $btus * 0.293;
    
    // Considerar que o compressor fica ligado 70% do tempo em uso normal
    $consumo_diario = ($watts * $horas_uso * 0.7) / 1000;
    $consumo_mensal = $consumo_diario * $dias;
    $custo_mensal = $consumo_mensal * $preco_kwh;
    
    return [
        'consumo_diario_kwh' => round($consumo_diario, 2),
        'consumo_mensal_kwh' => round($consumo_mensal, 2),
        'custo_mensal' => round($custo_mensal, 2)
    ];
}

/**
 * Calcula capacidade necessária baseada no ambiente
 */
function calcularBTUsNecessarios($area_m2, $pessoas = 1, $equipamentos = 0, $incidencia_sol = 'medio') {
    // Base: 600 BTUs por m²
    $btus_base = $area_m2 * 600;
    
    // Adicionar BTUs por pessoa
    $btus_base += $pessoas * 600;
    
    // Adicionar BTUs por equipamento eletrônico
    $btus_base += $equipamentos * 600;
    
    // Ajustar por incidência solar
    $fator_sol = [
        'baixo' => 1.0,
        'medio' => 1.1,
        'alto' => 1.2
    ];
    
    $btus_ajustado = $btus_base * ($fator_sol[$incidencia_sol] ?? 1.1);
    
    // Arredondar para o modelo comercial mais próximo
    $modelos = [7000, 9000, 12000, 18000, 24000, 30000, 36000, 48000, 60000];
    
    foreach($modelos as $modelo) {
        if($btus_ajustado <= $modelo) {
            return $modelo;
        }
    }
    
    return 60000; // Máximo
}

/**
 * Calcula pressões esperadas no sistema
 */
function calcularPressoesEsperadas($btus, $temp_ambiente = 25) {
    // Valores aproximados para R410A
    $pressao_baixa_base = 80; // PSI
    $pressao_alta_base = 250; // PSI
    
    // Ajustar pela temperatura ambiente
    $ajuste_temp = ($temp_ambiente - 25) * 2;
    
    $pressao_baixa = $pressao_baixa_base + $ajuste_temp;
    $pressao_alta = $pressao_alta_base + ($ajuste_temp * 3);
    
    // Ajustar pela capacidade
    $fator_capacidade = $btus / 12000;
    $pressao_baixa *= $fator_capacidade;
    $pressao_alta *= $fator_capacidade;
    
    return [
        'pressao_baixa_psi' => round($pressao_baixa),
        'pressao_alta_psi' => round($pressao_alta),
        'pressao_baixa_bar' => round($pressao_baixa * 0.069, 1),
        'pressao_alta_bar' => round($pressao_alta * 0.069, 1)
    ];
}
?>