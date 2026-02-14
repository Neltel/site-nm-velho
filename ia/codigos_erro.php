<?php
// ia/codigos_erro.php

$codigos_erro = [
    'GENERICO' => [
        'E0' => [
            'descricao' => 'Erro de comunicação entre unidades',
            'solucao' => 'Verificar cabos de comunicação, placas de controle e conexões',
            'gravidade' => 'alta'
        ],
        'E1' => [
            'descricao' => 'Erro de sensor de temperatura ambiente',
            'solucao' => 'Testar e substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'E2' => [
            'descricao' => 'Erro de sensor de serpentina',
            'solucao' => 'Testar e substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'E3' => [
            'descricao' => 'Proteção de alta pressão',
            'solucao' => 'Verificar ventilador externo, limpeza de serpentina e carga de gás',
            'gravidade' => 'alta'
        ],
        'E4' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos de gás, filtro secador e válvula de expansão',
            'gravidade' => 'alta'
        ],
        'P0' => [
            'descricao' => 'Proteção do compressor',
            'solucao' => 'Verificar capacitor, sobrecarga e temperatura do compressor',
            'gravidade' => 'alta'
        ],
        'P1' => [
            'descricao' => 'Proteção do ventilador',
            'solucao' => 'Verificar motor do ventilador, capacitor e obstruções',
            'gravidade' => 'media'
        ]
    ],
    
    'Samsung' => [
        'E101' => [
            'descricao' => 'Erro de comunicação entre placas',
            'solucao' => 'Verificar cabos flat e conexões entre placas interna e externa',
            'gravidade' => 'alta'
        ],
        'E110' => [
            'descricao' => 'Tensão de alimentação anormal',
            'solucao' => 'Verificar tensão da rede e estabilizador',
            'gravidade' => 'alta'
        ],
        'E301' => [
            'descricao' => 'Erro no sensor de temperatura ambiente',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'E302' => [
            'descricao' => 'Erro no sensor de temperatura da serpentina',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'E401' => [
            'descricao' => 'Proteção de alta pressão do sistema',
            'solucao' => 'Verificar ventilador externo e limpeza do condensador',
            'gravidade' => 'alta'
        ],
        'E501' => [
            'descricao' => 'Proteção do compressor por sobrecarga',
            'solucao' => 'Verificar capacitor do compressor e carga de gás',
            'gravidade' => 'alta'
        ]
    ],
    
    'LG' => [
        'CH01' => [
            'descricao' => 'Erro de comunicação entre unidades',
            'solucao' => 'Verificar cabos de comunicação e conexões',
            'gravidade' => 'alta'
        ],
        'CH02' => [
            'descricao' => 'Erro no sensor de temperatura ambiente',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'CH03' => [
            'descricao' => 'Erro no sensor de temperatura da serpentina',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'CH04' => [
            'descricao' => 'Proteção de alta pressão',
            'solucao' => 'Verificar ventilador externo e limpeza do condensador',
            'gravidade' => 'alta'
        ],
        'CH05' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos de gás e carga do sistema',
            'gravidade' => 'alta'
        ],
        'CH06' => [
            'descricao' => 'Proteção do compressor',
            'solucao' => 'Verificar capacitor e condições do compressor',
            'gravidade' => 'alta'
        ]
    ],
    
    'Midea' => [
        'E0' => [
            'descricao' => 'Erro de comunicação',
            'solucao' => 'Verificar cabos entre unidade interna e externa',
            'gravidade' => 'alta'
        ],
        'E1' => [
            'descricao' => 'Erro no sensor ambiente',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'E2' => [
            'descricao' => 'Erro no sensor da serpentina',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'E3' => [
            'descricao' => 'Proteção de alta pressão',
            'solucao' => 'Verificar ventilador externo e limpeza',
            'gravidade' => 'alta'
        ],
        'E4' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos e carga de gás',
            'gravidade' => 'alta'
        ],
        'E5' => [
            'descricao' => 'Proteção do compressor',
            'solucao' => 'Verificar capacitor e condições do compressor',
            'gravidade' => 'alta'
        ],
        'P1' => [
            'descricao' => 'Proteção do ventilador',
            'solucao' => 'Verificar motor do ventilador e obstruções',
            'gravidade' => 'media'
        ]
    ],
    
    'Gree' => [
        'E1' => [
            'descricao' => 'Erro no sensor de temperatura ambiente',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'E2' => [
            'descricao' => 'Erro no sensor da serpentina',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'E3' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos e carga de gás',
            'gravidade' => 'alta'
        ],
        'E4' => [
            'descricao' => 'Proteção de alta pressão',
            'solucao' => 'Verificar ventilador externo e limpeza',
            'gravidade' => 'alta'
        ],
        'E5' => [
            'descricao' => 'Proteção do compressor por sobrecarga',
            'solucao' => 'Verificar capacitor e condições do compressor',
            'gravidade' => 'alta'
        ],
        'E6' => [
            'descricao' => 'Erro de comunicação',
            'solucao' => 'Verificar cabos entre unidades',
            'gravidade' => 'alta'
        ]
    ],
    
    'Daikin' => [
        'A0' => [
            'descricao' => 'Erro no sensor ambiente interno',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        'A1' => [
            'descricao' => 'Erro na placa interna',
            'solucao' => 'Verificar ou substituir placa de controle interna',
            'gravidade' => 'alta'
        ],
        'A3' => [
            'descricao' => 'Erro no dreno',
            'solucao' => 'Verificar bomba de dreno e tubulação',
            'gravidade' => 'media'
        ],
        'C4' => [
            'descricao' => 'Erro no sensor da serpentina interna',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        'C9' => [
            'descricao' => 'Erro no sensor externo',
            'solucao' => 'Substituir sensor de temperatura externo',
            'gravidade' => 'media'
        ],
        'E0' => [
            'descricao' => 'Proteção de segurança',
            'solucao' => 'Verificar sensor de fluxo de ar e ventilador',
            'gravidade' => 'media'
        ],
        'E3' => [
            'descricao' => 'Proteção de alta pressão',
            'solucao' => 'Verificar ventilador externo e limpeza',
            'gravidade' => 'alta'
        ],
        'E4' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos e carga de gás',
            'gravidade' => 'alta'
        ],
        'H3' => [
            'descricao' => 'Proteção do compressor',
            'solucao' => 'Verificar capacitor e condições do compressor',
            'gravidade' => 'alta'
        ],
        'H9' => [
            'descricao' => 'Sensor de temperatura externo',
            'solucao' => 'Substituir sensor de temperatura do condensador',
            'gravidade' => 'media'
        ],
        'J3' => [
            'descricao' => 'Erro no sensor da serpentina externa',
            'solucao' => 'Substituir sensor de temperatura da serpentina externa',
            'gravidade' => 'media'
        ],
        'J6' => [
            'descricao' => 'Erro no sensor de temperatura do compressor',
            'solucao' => 'Substituir sensor de temperatura do compressor',
            'gravidade' => 'alta'
        ],
        'L4' => [
            'descricao' => 'Proteção de temperatura do motor do ventilador',
            'solucao' => 'Verificar motor do ventilador e obstruções',
            'gravidade' => 'media'
        ],
        'L5' => [
            'descricao' => 'Proteção de sobrecarga do IPM',
            'solucao' => 'Verificar placa de potência e conexões',
            'gravidade' => 'alta'
        ],
        'P1' => [
            'descricao' => 'Proteção de ventilador interno',
            'solucao' => 'Verificar motor do ventilador interno',
            'gravidade' => 'media'
        ],
        'P4' => [
            'descricao' => 'Proteção de temperatura da tubulação',
            'solucao' => 'Verificar sensor de temperatura da tubulação',
            'gravidade' => 'media'
        ],
        'U0' => [
            'descricao' => 'Tensão de alimentação baixa',
            'solucao' => 'Verificar tensão da rede e estabilizador',
            'gravidade' => 'alta'
        ],
        'U2' => [
            'descricao' => 'Tensão de alimentação baixa',
            'solucao' => 'Verificar tensão da rede e estabilizador',
            'gravidade' => 'alta'
        ],
        'U4' => [
            'descricao' => 'Erro de comunicação entre unidades',
            'solucao' => 'Verificar cabos de comunicação e conexões',
            'gravidade' => 'alta'
        ]
    ],
    
    'Fujitsu' => [
        '00' => [
            'descricao' => 'Operação normal',
            'solucao' => 'Nenhuma ação necessária',
            'gravidade' => 'baixa'
        ],
        '01' => [
            'descricao' => 'Proteção de alta pressão do compressor',
            'solucao' => 'Verificar ventilador externo e limpeza do condensador',
            'gravidade' => 'alta'
        ],
        '02' => [
            'descricao' => 'Proteção do compressor por sobrecarga',
            'solucao' => 'Verificar capacitor e condições do compressor',
            'gravidade' => 'alta'
        ],
        '03' => [
            'descricao' => 'Proteção de alta descarga do compressor',
            'solucao' => 'Verificar carga de gás e ventilação',
            'gravidade' => 'alta'
        ],
        '05' => [
            'descricao' => 'Proteção de baixa pressão',
            'solucao' => 'Verificar vazamentos e carga de gás',
            'gravidade' => 'alta'
        ],
        '07' => [
            'descricao' => 'Proteção de temperatura da serpentina',
            'solucao' => 'Verificar sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        '08' => [
            'descricao' => 'Proteção de sobrecorrente do motor externo',
            'solucao' => 'Verificar motor do ventilador externo',
            'gravidade' => 'media'
        ],
        '09' => [
            'descricao' => 'Proteção de temperatura do compressor',
            'solucao' => 'Verificar sensor de temperatura do compressor',
            'gravidade' => 'alta'
        ],
        '11' => [
            'descricao' => 'Erro de sensor de temperatura ambiente',
            'solucao' => 'Substituir sensor de temperatura ambiente',
            'gravidade' => 'media'
        ],
        '12' => [
            'descricao' => 'Erro de sensor de temperatura da serpentina',
            'solucao' => 'Substituir sensor de temperatura da serpentina',
            'gravidade' => 'media'
        ],
        '13' => [
            'descricao' => 'Erro de sensor de temperatura do compressor',
            'solucao' => 'Substituir sensor de temperatura do compressor',
            'gravidade' => 'alta'
        ],
        '14' => [
            'descricao' => 'Erro de sensor de temperatura externo',
            'solucao' => 'Substituir sensor de temperatura externo',
            'gravidade' => 'media'
        ],
        '15' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação',
            'solucao' => 'Substituir sensor de temperatura da tubulação',
            'gravidade' => 'media'
        ],
        '1A' => [
            'descricao' => 'Erro de comunicação entre unidades',
            'solucao' => 'Verificar cabos de comunicação e conexões',
            'gravidade' => 'alta'
        ],
        '1C' => [
            'descricao' => 'Erro na placa de controle externa',
            'solucao' => 'Verificar ou substituir placa de controle externa',
            'gravidade' => 'alta'
        ],
        '1E' => [
            'descricao' => 'Erro na placa de controle interna',
            'solucao' => 'Verificar ou substituir placa de controle interna',
            'gravidade' => 'alta'
        ],
        '1F' => [
            'descricao' => 'Erro de configuração do sistema',
            'solucao' => 'Verificar configurações e reiniciar sistema',
            'gravidade' => 'media'
        ],
        '89' => [
            'descricao' => 'Erro de comunicação com controle remoto',
            'solucao' => 'Verificar controle remoto e sensor IR',
            'gravidade' => 'media'
        ],
        '96' => [
            'descricao' => 'Erro de ventilador interno',
            'solucao' => 'Verificar motor do ventilador interno',
            'gravidade' => 'media'
        ],
        '9A' => [
            'descricao' => 'Erro de dreno',
            'solucao' => 'Verificar bomba de dreno e tubulação',
            'gravidade' => 'media'
        ],
        '9F' => [
            'descricao' => 'Erro de sensor de umidade',
            'solucao' => 'Substituir sensor de umidade',
            'gravidade' => 'media'
        ],
        'A0' => [
            'descricao' => 'Erro de sensor de qualidade do ar',
            'solucao' => 'Substituir sensor de qualidade do ar',
            'gravidade' => 'baixa'
        ],
        'A1' => [
            'descricao' => 'Erro na placa de controle de ar',
            'solucao' => 'Verificar ou substituir placa de controle de ar',
            'gravidade' => 'alta'
        ],
        'A3' => [
            'descricao' => 'Erro de sensor de temperatura de retorno',
            'solucao' => 'Substituir sensor de temperatura de retorno',
            'gravidade' => 'media'
        ],
        'A6' => [
            'descricao' => 'Erro de motor do dampers',
            'solucao' => 'Verificar motor dos dampers e obstruções',
            'gravidade' => 'media'
        ],
        'A7' => [
            'descricao' => 'Erro de abertura/fehcamento dos dampers',
            'solucao' => 'Verificar posição dos dampers e motor',
            'gravidade' => 'media'
        ],
        'AA' => [
            'descricao' => 'Erro de sensor de temperatura de insuflamento',
            'solucao' => 'Substituir sensor de temperatura de insuflamento',
            'gravidade' => 'media'
        ],
        'Ab' => [
            'descricao' => 'Erro de sensor de umidade de retorno',
            'solucao' => 'Substituir sensor de umidade de retorno',
            'gravidade' => 'media'
        ],
        'AE' => [
            'descricao' => 'Erro de sensor de umidade de insuflamento',
            'solucao' => 'Substituir sensor de umidade de insuflamento',
            'gravidade' => 'media'
        ],
        'b0' => [
            'descricao' => 'Erro de sensor de temperatura do trocador de calor',
            'solucao' => 'Substituir sensor de temperatura do trocador de calor',
            'gravidade' => 'media'
        ],
        'b5' => [
            'descricao' => 'Erro de sensor de temperatura da água',
            'solucao' => 'Substituir sensor de temperatura da água',
            'gravidade' => 'media'
        ],
        'b6' => [
            'descricao' => 'Erro de sensor de temperatura externa da água',
            'solucao' => 'Substituir sensor de temperatura externa da água',
            'gravidade' => 'media'
        ],
        'b9' => [
            'descricao' => 'Erro de sensor de temperatura do tanque de água quente',
            'solucao' => 'Substituir sensor de temperatura do tanque de água quente',
            'gravidade' => 'media'
        ],
        'BE' => [
            'descricao' => 'Erro de sensor de temperatura da serpentina do aquecimento',
            'solucao' => 'Substituir sensor de temperatura da serpentina do aquecimento',
            'gravidade' => 'media'
        ],
        'C0' => [
            'descricao' => 'Erro de sensor de temperatura do trocador de calor do aquecimento',
            'solucao' => 'Substituir sensor de temperatura do trocador de calor do aquecimento',
            'gravidade' => 'media'
        ],
        'C1' => [
            'descricao' => 'Erro de sensor de temperatura do tanque de armazenamento',
            'solucao' => 'Substituir sensor de temperatura do tanque de armazenamento',
            'gravidade' => 'media'
        ],
        'C2' => [
            'descricao' => 'Erro de sensor de temperatura do aquecedor auxiliar',
            'solucao' => 'Substituir sensor de temperatura do aquecedor auxiliar',
            'gravidade' => 'media'
        ],
        'C4' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de gás',
            'solucao' => 'Substituir sensor de temperatura da tubulação de gás',
            'gravidade' => 'media'
        ],
        'C5' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de líquido',
            'solucao' => 'Substituir sensor de temperatura da tubulação de líquido',
            'gravidade' => 'media'
        ],
        'C9' => [
            'descricao' => 'Erro de sensor de temperatura de descarga do compressor',
            'solucao' => 'Substituir sensor de temperatura de descarga do compressor',
            'gravidade' => 'alta'
        ],
        'CC' => [
            'descricao' => 'Erro de sensor de temperatura de sucção do compressor',
            'solucao' => 'Substituir sensor de temperatura de sucção do compressor',
            'gravidade' => 'alta'
        ],
        'CE' => [
            'descricao' => 'Erro de sensor de temperatura do motor do compressor',
            'solucao' => 'Substituir sensor de temperatura do motor do compressor',
            'gravidade' => 'alta'
        ],
        'E0' => [
            'descricao' => 'Erro de sensor de pressão de alta',
            'solucao' => 'Substituir sensor de pressão de alta',
            'gravidade' => 'alta'
        ],
        'E1' => [
            'descricao' => 'Erro de sensor de pressão de baixa',
            'solucao' => 'Substituir sensor de pressão de baixa',
            'gravidade' => 'alta'
        ],
        'E3' => [
            'descricao' => 'Erro de sensor de pressão diferencial',
            'solucao' => 'Substituir sensor de pressão diferencial',
            'gravidade' => 'media'
        ],
        'E5' => [
            'descricao' => 'Erro de sensor de fluxo de água',
            'solucao' => 'Substituir sensor de fluxo de água',
            'gravidade' => 'media'
        ],
        'E6' => [
            'descricao' => 'Erro de sensor de nível de água',
            'solucao' => 'Substituir sensor de nível de água',
            'gravidade' => 'media'
        ],
        'E9' => [
            'descricao' => 'Erro de sensor de umidade externo',
            'solucao' => 'Substituir sensor de umidade externo',
            'gravidade' => 'media'
        ],
        'EA' => [
            'descricao' => 'Erro de sensor de pressão da água',
            'solucao' => 'Substituir sensor de pressão da água',
            'gravidade' => 'media'
        ],
        'F0' => [
            'descricao' => 'Erro de sensor de temperatura do trocador de calor do aquecimento 2',
            'solucao' => 'Substituir sensor de temperatura do trocador de calor do aquecimento 2',
            'gravidade' => 'media'
        ],
        'F3' => [
            'descricao' => 'Erro de sensor de temperatura do aquecedor auxiliar 2',
            'solucao' => 'Substituir sensor de temperatura do aquecedor auxiliar 2',
            'gravidade' => 'media'
        ],
        'F4' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de gás 2',
            'solucao' => 'Substituir sensor de temperatura da tubulação de gás 2',
            'gravidade' => 'media'
        ],
        'F5' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de líquido 2',
            'solucao' => 'Substituir sensor de temperatura da tubulação de líquido 2',
            'gravidade' => 'media'
        ],
        'F9' => [
            'descricao' => 'Erro de sensor de temperatura de descarga do compressor 2',
            'solucao' => 'Substituir sensor de temperatura de descarga do compressor 2',
            'gravidade' => 'alta'
        ],
        'FC' => [
            'descricao' => 'Erro de sensor de temperatura de sucção do compressor 2',
            'solucao' => 'Substituir sensor de temperatura de sucção do compressor 2',
            'gravidade' => 'alta'
        ],
        'H0' => [
            'descricao' => 'Erro de sensor de temperatura do trocador de calor 3',
            'solucao' => 'Substituir sensor de temperatura do trocador de calor 3',
            'gravidade' => 'media'
        ],
        'H1' => [
            'descricao' => 'Erro de sensor de temperatura do aquecedor auxiliar 3',
            'solucao' => 'Substituir sensor de temperatura do aquecedor auxiliar 3',
            'gravidade' => 'media'
        ],
        'H4' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de gás 3',
            'solucao' => 'Substituir sensor de temperatura da tubulação de gás 3',
            'gravidade' => 'media'
        ],
        'H5' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de líquido 3',
            'solucao' => 'Substituir sensor de temperatura da tubulação de líquido 3',
            'gravidade' => 'media'
        ],
        'H9' => [
            'descricao' => 'Erro de sensor de temperatura de descarga do compressor 3',
            'solucao' => 'Substituir sensor de temperatura de descarga do compressor 3',
            'gravidade' => 'alta'
        ],
        'HC' => [
            'descricao' => 'Erro de sensor de temperatura de sucção do compressor 3',
            'solucao' => 'Substituir sensor de temperatura de sucção do compressor 3',
            'gravidade' => 'alta'
        ],
        'J0' => [
            'descricao' => 'Erro de sensor de temperatura do trocador de calor 4',
            'solucao' => 'Substituir sensor de temperatura do trocador de calor 4',
            'gravidade' => 'media'
        ],
        'J1' => [
            'descricao' => 'Erro de sensor de temperatura do aquecedor auxiliar 4',
            'solucao' => 'Substituir sensor de temperatura do aquecedor auxiliar 4',
            'gravidade' => 'media'
        ],
        'J4' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de gás 4',
            'solucao' => 'Substituir sensor de temperatura da tubulação de gás 4',
            'gravidade' => 'media'
        ],
        'J5' => [
            'descricao' => 'Erro de sensor de temperatura da tubulação de líquido 4',
            'solucao' => 'Substituir sensor de temperatura da tubulação de líquido 4',
            'gravidade' => 'media'
        ],
        'J9' => [
            'descricao' => 'Erro de sensor de temperatura de descarga do compressor 4',
            'solucao' => 'Substituir sensor de temperatura de descarga do compressor 4',
            'gravidade' => 'alta'
        ],
        'JC' => [
            'descricao' => 'Erro de sensor de temperatura de sucção do compressor 4',
            'solucao' => 'Substituir sensor de temperatura de sucção do compressor 4',
            'gravidade' => 'alta'
        ]
    ]
];

// Função para buscar código de erro
function buscarCodigoErro($marca, $codigo) {
    global $codigos_erro;
    
    // Normalizar código (remover espaços, converter para maiúsculas)
    $codigo = strtoupper(trim($codigo));
    
    // Buscar na marca específica
    if(isset($codigos_erro[$marca][$codigo])) {
        return $codigos_erro[$marca][$codigo];
    }
    
    // Buscar em códigos genéricos
    if(isset($codigos_erro['GENERICO'][$codigo])) {
        return $codigos_erro['GENERICO'][$codigo];
    }
    
    // Buscar por padrão (se o código começar com...)
    foreach($codigos_erro['GENERICO'] as $padrao => $info) {
        if(strpos($codigo, $padrao) === 0) {
            return $info;
        }
    }
    
    // Se não encontrou, retornar informação genérica
    return [
        'descricao' => 'Código de erro não identificado na base de dados',
        'solucao' => 'Consultar manual técnico do fabricante ou entrar em contato com suporte especializado',
        'gravidade' => 'media'
    ];
}

return $codigos_erro;
?>