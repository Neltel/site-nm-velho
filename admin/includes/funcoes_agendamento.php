<?php
// includes/funcoes_agendamento.php

/**
 * Verifica se uma data/horário está disponível
 */
function verificarDisponibilidade($data, $hora, $pdo, $agendamento_id = null) {
    // Buscar configurações
    $configs = buscarConfiguracoesAgendamento($pdo);
    
    // Verificar se é feriado
    if($configs['bloquear_feriados'] && ehFeriado($data, $pdo)) {
        return [
            'disponivel' => false,
            'motivo' => 'Feriado nacional'
        ];
    }
    
    // Verificar se é final de semana
    if(ehFinalSemana($data)) {
        return [
            'disponivel' => true,
            'tipo' => 'final_semana',
            'acrescimo' => $configs['valor_final_semana']
        ];
    }
    
    // Verificar se está fora do horário comercial
    $fora_horario = ehForaHorarioComercial($hora, $configs);
    if($fora_horario) {
        return [
            'disponivel' => true,
            'tipo' => 'fora_horario',
            'acrescimo' => $configs['valor_fora_horario']
        ];
    }
    
    // Verificar se está no horário de almoço
    if(ehHorarioAlmoco($hora, $configs)) {
        return [
            'disponivel' => false,
            'motivo' => 'Horário de almoço'
        ];
    }
    
    // Verificar conflitos com outros agendamentos
    $conflitos = verificarConflitosAgendamento($data, $hora, $pdo, $agendamento_id, $configs);
    if($conflitos > 0) {
        return [
            'disponivel' => false,
            'motivo' => 'Horário já ocupado'
        ];
    }
    
    // Verificar limite diário
    if(atingiuLimiteDiario($data, $pdo, $configs)) {
        return [
            'disponivel' => false,
            'motivo' => 'Limite diário de agendamentos atingido'
        ];
    }
    
    return [
        'disponivel' => true,
        'tipo' => 'normal',
        'acrescimo' => $configs['valor_horario_normal']
    ];
}

/**
 * Busca configurações do agendamento
 */
function buscarConfiguracoesAgendamento($pdo) {
    $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
    $stmt->execute();
    $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    return [
        'horario_inicio' => $configs['horario_inicio'] ?? '08:00',
        'horario_fim' => $configs['horario_fim'] ?? '18:00',
        'intervalo_agendamento' => intval($configs['intervalo_agendamento'] ?? 60),
        'tempo_deslocamento' => intval($configs['tempo_deslocamento'] ?? 30),
        'max_agendamentos_dia' => intval($configs['max_agendamentos_dia'] ?? 4),
        'dias_antecipacao' => intval($configs['dias_antecipacao'] ?? 30),
        'valor_horario_normal' => floatval($configs['valor_horario_normal'] ?? 0),
        'valor_fora_horario' => floatval($configs['valor_fora_horario'] ?? 30),
        'valor_final_semana' => floatval($configs['valor_final_semana'] ?? 50),
        'valor_feriado' => floatval($configs['valor_feriado'] ?? 100),
        'bloquear_feriados' => boolval($configs['bloquear_feriados'] ?? 1),
        'horario_almoco_inicio' => $configs['horario_almoco_inicio'] ?? '12:00',
        'horario_almoco_fim' => $configs['horario_almoco_fim'] ?? '13:00'
    ];
}

/**
 * Verifica se é feriado
 */
function ehFeriado($data, $pdo) {
    $ano = date('Y', strtotime($data));
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM feriados 
        WHERE (data_feriado = ? OR (recorrente = 1 AND DATE_FORMAT(data_feriado, '%m-%d') = DATE_FORMAT(?, '%m-%d')))
    ");
    $stmt->execute([$data, $data]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
}

/**
 * Verifica se é final de semana
 */
function ehFinalSemana($data) {
    $dia_semana = date('N', strtotime($data));
    return $dia_semana >= 6; // 6 = sábado, 7 = domingo
}

/**
 * Verifica se está fora do horário comercial
 */
function ehForaHorarioComercial($hora, $configs) {
    $hora_time = strtotime($hora);
    $inicio_time = strtotime($configs['horario_inicio']);
    $fim_time = strtotime($configs['horario_fim']);
    
    return $hora_time < $inicio_time || $hora_time > $fim_time;
}

/**
 * Verifica se é horário de almoço
 */
function ehHorarioAlmoco($hora, $configs) {
    $hora_time = strtotime($hora);
    $almoco_inicio = strtotime($configs['horario_almoco_inicio']);
    $almoco_fim = strtotime($configs['horario_almoco_fim']);
    
    return $hora_time >= $almoco_inicio && $hora_time <= $almoco_fim;
}

/**
 * Verifica conflitos com outros agendamentos
 */
function verificarConflitosAgendamento($data, $hora, $pdo, $agendamento_id, $configs) {
    $intervalo = $configs['intervalo_agendamento'];
    $tempo_deslocamento = $configs['tempo_deslocamento'];
    
    // Calcular janela de tempo (considerando intervalo + deslocamento)
    $hora_inicio = date('H:i:s', strtotime("-$intervalo minutes", strtotime($hora)));
    $hora_fim = date('H:i:s', strtotime("+$intervalo minutes +$tempo_deslocamento minutes", strtotime($hora)));
    
    $sql = "
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE data_agendamento = ? 
        AND status IN ('agendado', 'confirmado')
        AND (
            (hora_agendamento BETWEEN ? AND ?)
            OR (? BETWEEN hora_agendamento AND DATE_ADD(hora_agendamento, INTERVAL ? MINUTE))
        )
    ";
    
    $params = [$data, $hora_inicio, $hora_fim, $hora, $intervalo];
    
    if($agendamento_id) {
        $sql .= " AND id != ?";
        $params[] = $agendamento_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

/**
 * Verifica se atingiu o limite diário de agendamentos
 */
function atingiuLimiteDiario($data, $pdo, $configs) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE data_agendamento = ? 
        AND status IN ('agendado', 'confirmado')
    ");
    $stmt->execute([$data]);
    
    $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return $total_agendamentos >= $configs['max_agendamentos_dia'];
}

/**
 * Busca horários disponíveis para uma data
 */
function buscarHorariosDisponiveis($data, $pdo) {
    $configs = buscarConfiguracoesAgendamento($pdo);
    $horarios_disponiveis = [];
    
    // Verificar se a data é válida
    if(!validarDataAgendamento($data, $configs)) {
        return [];
    }
    
    // Gerar todos os horários possíveis
    $horarios = gerarHorariosPossiveis($configs);
    
    foreach($horarios as $hora) {
        $disponibilidade = verificarDisponibilidade($data, $hora, $pdo);
        
        if($disponibilidade['disponivel']) {
            $horarios_disponiveis[] = [
                'hora' => $hora,
                'tipo' => $disponibilidade['tipo'],
                'acrescimo' => $disponibilidade['acrescimo'],
                'label' => formatarLabelHorario($hora, $disponibilidade['tipo'])
            ];
        }
    }
    
    return $horarios_disponiveis;
}

/**
 * Valida se a data está dentro das regras
 */
function validarDataAgendamento($data, $configs) {
    $data_obj = new DateTime($data);
    $hoje = new DateTime();
    $limite = new DateTime("+{$configs['dias_antecipacao']} days");
    
    // Verificar se está no passado
    if($data_obj < $hoje) {
        return false;
    }
    
    // Verificar se ultrapassa o limite de dias
    if($data_obj > $limite) {
        return false;
    }
    
    return true;
}

/**
 * Gera todos os horários possíveis baseado nas configurações
 */
function gerarHorariosPossiveis($configs) {
    $horarios = [];
    
    $inicio = strtotime($configs['horario_inicio']);
    $fim = strtotime($configs['horario_fim']);
    $intervalo = $configs['intervalo_agendamento'] * 60; // converter para segundos
    
    $almoco_inicio = strtotime($configs['horario_almoco_inicio']);
    $almoco_fim = strtotime($configs['horario_almoco_fim']);
    
    $hora_atual = $inicio;
    
    while($hora_atual <= $fim) {
        $hora_formatada = date('H:i', $hora_atual);
        
        // Pular horário de almoço
        if(!($hora_atual >= $almoco_inicio && $hora_atual < $almoco_fim)) {
            $horarios[] = $hora_formatada;
        }
        
        $hora_atual += $intervalo;
    }
    
    return $horarios;
}

/**
 * Formata label do horário com ícones
 */
function formatarLabelHorario($hora, $tipo) {
    $icones = [
        'normal' => '🟢',
        'fora_horario' => '🟡',
        'final_semana' => '🔵'
    ];
    
    $labels = [
        'normal' => 'Horário Normal',
        'fora_horario' => 'Fora do Horário',
        'final_semana' => 'Final de Semana'
    ];
    
    $icone = $icones[$tipo] ?? '⚪';
    $label = $labels[$tipo] ?? 'Especial';
    
    return "{$icone} {$hora} - {$label}";
}

/**
 * Calcula valor com acréscimo
 */
function calcularValorComAcrescimo($valor_base, $acrescimo_percentual) {
    $acrescimo = ($valor_base * $acrescimo_percentual) / 100;
    return $valor_base + $acrescimo;
}

/**
 * Busca agendamentos do dia
 */
function buscarAgendamentosDoDia($data, $pdo) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.nome as cliente_nome, c.telefone, s.nome as servico_nome
        FROM agendamentos a
        LEFT JOIN clientes c ON a.cliente_id = c.id
        LEFT JOIN servicos s ON a.servico_id = s.id
        WHERE a.data_agendamento = ?
        AND a.status IN ('agendado', 'confirmado')
        ORDER BY a.hora_agendamento
    ");
    $stmt->execute([$data]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>