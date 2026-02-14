<?php
// ia/base_conhecimento.php

$base_conhecimento = [
    'sintomas_comuns' => [
        'nao_liga' => [
            'causas' => [
                'Problema na alimentação elétrica',
                'Fusível queimado', 
                'Disjuntor desarmado',
                'Placa de controle com defeito',
                'Controle remoto com problema',
                'Sensor de segurança acionado'
            ],
            'diagnostico' => 'Verificar tensão de entrada, fusíveis, disjuntores e placa de controle'
        ],
        
        'nao_resfria' => [
            'causas' => [
                'Baixa carga de gás refrigerante',
                'Compressor com defeito',
                'Ventilador externo parado',
                'Válvula de expansão bloqueada',
                'Filtro secador obstruído',
                'Vazamento no sistema'
            ],
            'diagnostico' => 'Verificar pressões do sistema, funcionamento do compressor e ventiladores'
        ],
        
        'resfria_pouco' => [
            'causas' => [
                'Filtros de ar sujos',
                'Serpentinas sujas',
                'Baixa carga de gás',
                'Ventilador interno lento',
                'Vazamento pequeno',
                'Temperatura ambiente muito alta'
            ],
            'diagnostico' => 'Verificar limpeza, carga de gás e funcionamento dos ventiladores'
        ],
        
        'gela_e_descongela' => [
            'causas' => [
                'Filtros obstruídos',
                'Ventilador interno parado',
                'Sensor de temperatura com defeito',
                'Dreno obstruído',
                'Baixa velocidade do ventilador'
            ],
            'diagnostico' => 'Verificar fluxo de ar, dreno e sensores de temperatura'
        ],
        
        'barulho_incomum' => [
            'causas' => [
                'Ventilador com folga ou sujo',
                'Compressor com problema mecânico',
                'Parafusos soltos',
                'Carcaça vibrando',
                'Motor do ventilador desbalanceado'
            ],
            'diagnostico' => 'Identificar origem do barulho e verificar componentes mecânicos'
        ],
        
        'vazamento_agua' => [
            'causas' => [
                'Dreno obstruído',
                'Bandeja de dreno furada',
                'Unidade desnivelada',
                'Isolamento térmico deficiente',
                'Bomba de dreno com defeito'
            ],
            'diagnostico' => 'Verificar dreno, bandeja e inclinação da unidade'
        ],
        
        'cheiro_estranho' => [
            'causas' => [
                'Bactérias ou fungos nos filtros',
                'Animal morto na unidade',
                'Queima de componente elétrico',
                'Sujeira acumulada',
                'Produto químico vazando'
            ],
            'diagnostico' => 'Identificar origem do odor e realizar limpeza completa'
        ],
        
        'luz_piscando' => [
            'causas' => [
                'Código de erro no sistema',
                'Problema na placa de controle',
                'Sensor com defeito',
                'Proteção de segurança acionada',
                'Problema de comunicação'
            ],
            'diagnostico' => 'Identificar código de erro e verificar componentes relacionados'
        ]
    ],
    
    'valores_referencia' => [
        'delta_t' => [
            'ideal' => '8°C - 15°C',
            'baixo' => 'Abaixo de 8°C - indica baixa eficiência',
            'alto' => 'Acima de 15°C - pode indicar superaquecimento'
        ],
        
        'pressao_r410a' => [
            'baixa_ideal' => '110-130 PSI (25°C ambiente)',
            'alta_ideal' => '300-350 PSI (25°C ambiente)',
            'fatores_ajuste' => 'Ajustar conforme temperatura ambiente'
        ],
        
        'tensao_alimentacao' => [
            'monofasica' => '220V ±10% (198V - 242V)',
            'bifasica' => '220V/380V conforme instalação',
            'critica' => 'Abaixo de 190V ou acima de 250V'
        ],
        
        'corrente_compressor' => [
            '9000_btus' => '3.5A - 4.5A (220V)',
            '12000_btus' => '5.0A - 6.5A (220V)', 
            '18000_btus' => '7.5A - 9.5A (220V)',
            '24000_btus' => '10A - 13A (220V)',
            'tolerancia' => '±20% do valor nominal'
        ],
        
        'isolamento_eletrico' => [
            'minimo' => '2 MΩ',
            'recomendado' => '5 MΩ ou mais',
            'critico' => 'Abaixo de 1 MΩ'
        ],
        
        'resistencia_sensores' => [
            'ntc_10k' => '10kΩ a 25°C',
            'faixa_operacional' => '1kΩ - 100kΩ',
            'curto' => 'Abaixo de 500Ω',
            'aberto' => 'Acima de 200kΩ'
        ]
    ],
    
    'procedimentos_manutencao' => [
        'limpeza_filtros' => [
            'frequencia' => 'A cada 15 dias em uso normal',
            'procedimento' => 'Lavar com água, secar naturalmente e reinstalar',
            'importancia' => 'Melhora eficiência e previne congelamento'
        ],
        
        'limpeza_serpentinas' => [
            'frequencia' => 'A cada 6 meses',
            'procedimento' => 'Usar produtos específicos e água pressurizada',
            'cuidados' => 'Não danificar as aletas'
        ],
        
        'verificacao_gas' => [
            'frequencia' => 'Anualmente ou quando houver perda de performance',
            'parametros' => 'Verificar pressões e temperatura de superaquecimento',
            'cuidados' => 'Só recarregar após consertar vazamentos'
        ],
        
        'limpeza_dreno' => [
            'frequencia' => 'A cada 3 meses',
            'procedimento' => 'Usar água pressurizada e produtos de limpeza',
            'sinais_obstrucao' => 'Vazamento de água interna'
        ],
        
        'verificacao_eletrica' => [
            'frequencia' => 'Anualmente',
            'itens' => 'Tensão, corrente, isolamento, conexões',
            'importancia' => 'Previne danos e garante segurança'
        ]
    ],
    
    'dicas_instalacao' => [
        'localizacao_unidade_interna' => [
            'distancia_parede' => 'Mínimo 15cm para circulação de ar',
            'altura_ideal' => '1.8m - 2.2m do piso',
            'evitar' => 'Exposição direta ao sol, fontes de calor, circulação obstruída'
        ],
        
        'localizacao_unidade_externa' => [
            'espacamento_minimo' => '30cm laterais, 50cm frontal',
            'protecao' => 'Proteger da chuva direta e sol intenso',
            'acesso' => 'Facilitar acesso para manutenção'
        ],
        
        'tubulacao' => [
            'comprimento_maximo' => '15 metros para maioria dos modelos',
            'curvas_maximas' => 'Máximo 10 curvas de 90°',
            'isolamento' => 'Isolamento térmico adequado em toda extensão'
        ],
        
        'dreno' => [
            'inclinacao_minima' => '1% de inclinação',
            'comprimento_maximo' => '5 metros sem bomba auxiliar',
            'evitar' => 'Curvas fechadas e pontos baixos'
        ],
        
        'eletrica' => [
            'disjuntor_dedicado' => 'Obrigatório para cada equipamento',
            'aterramento' => 'Fundamental para segurança',
            'bitola_fios' => 'Conforme manual do fabricante'
        ]
    ],
    
    'troubleshooting_rapido' => [
        'equipamento_nao_liga' => [
            '1' => 'Verificar se há energia na tomada',
            '2' => 'Testar controle remoto (baterias)',
            '3' => 'Verificar disjuntor e fusíveis',
            '4' => 'Testar interruptor interno da unidade'
        ],
        
        'equipamento_liga_mas_nao_resfria' => [
            '1' => 'Verificar se está no modo correto (cool)',
            '2' => 'Ajustar temperatura mais baixa',
            '3' => 'Verificar se filtros estão limpos',
            '4' => 'Ouvir se compressor e ventilador externo ligam'
        ],
        
        'vazamento_agua_interna' => [
            '1' => 'Verificar se dreno está obstruído',
            '2' => 'Conferir nivelamento da unidade',
            '3' => 'Verificar bandeja de dreno',
            '4' => 'Testar bomba de dreno (se houver)'
        ],
        
        'barulho_incomum' => [
            '1' => 'Identificar origem (interno/externo)',
            '2' => 'Verificar folgas mecânicas',
            '3' => 'Conferir se há objetos soltos',
            '4' => 'Verificar ventiladores'
        ],
        
        'cheiro_desagradavel' => [
            '1' => 'Realizar limpeza dos filtros',
            '2' => 'Verificar se há sujeira acumulada',
            '3' => 'Usar produtos de limpeza específicos',
            '4' => 'Verificar dreno e bandeja'
        ]
    ]
];

// Funções de acesso à base de conhecimento
function obterCausasSintoma($sintoma) {
    global $base_conhecimento;
    
    if(isset($base_conhecimento['sintomas_comuns'][$sintoma])) {
        return $base_conhecimento['sintomas_comuns'][$sintoma];
    }
    
    return null;
}

function obterValorReferencia($parametro) {
    global $base_conhecimento;
    
    if(isset($base_conhecimento['valores_referencia'][$parametro])) {
        return $base_conhecimento['valores_referencia'][$parametro];
    }
    
    return null;
}

function obterProcedimentoManutencao($procedimento) {
    global $base_conhecimento;
    
    if(isset($base_conhecimento['procedimentos_manutencao'][$procedimento])) {
        return $base_conhecimento['procedimentos_manutencao'][$procedimento];
    }
    
    return null;
}

function obterDicasInstalacao($dica) {
    global $base_conhecimento;
    
    if(isset($base_conhecimento['dicas_instalacao'][$dica])) {
        return $base_conhecimento['dicas_instalacao'][$dica];
    }
    
    return null;
}

function obterTroubleshooting($problema) {
    global $base_conhecimento;
    
    if(isset($base_conhecimento['troubleshooting_rapido'][$problema])) {
        return $base_conhecimento['troubleshooting_rapido'][$problema];
    }
    
    return null;
}

// Função para busca inteligente na base de conhecimento
function buscarNaBaseConhecimento($termo) {
    global $base_conhecimento;
    
    $resultados = [];
    $termo = strtolower($termo);
    
    // Buscar em sintomas
    foreach($base_conhecimento['sintomas_comuns'] as $sintoma => $info) {
        if(strpos($sintoma, $termo) !== false) {
            $resultados[] = [
                'categoria' => 'Sintoma',
                'item' => $sintoma,
                'informacao' => $info
            ];
        }
    }
    
    // Buscar em valores de referência
    foreach($base_conhecimento['valores_referencia'] as $parametro => $valores) {
        if(strpos($parametro, $termo) !== false) {
            $resultados[] = [
                'categoria' => 'Valor de Referência',
                'item' => $parametro,
                'informacao' => $valores
            ];
        }
    }
    
    // Buscar em troubleshooting
    foreach($base_conhecimento['troubleshooting_rapido'] as $problema => $passos) {
        if(strpos($problema, $termo) !== false) {
            $resultados[] = [
                'categoria' => 'Solução Rápida',
                'item' => $problema,
                'informacao' => $passos
            ];
        }
    }
    
    return $resultados;
}

return $base_conhecimento;
?>