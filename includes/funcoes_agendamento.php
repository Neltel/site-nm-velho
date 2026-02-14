<?php
// includes/funcoes_agendamento.php
// Enviar mensagem para WhatsApp - Orçamento
function enviarWhatsAppOrcamento($pdo, $dados_orcamento) {
    $config = getConfigAgendamento($pdo);
    $whatsapp = isset($config['whatsapp_empresa']) ? $config['whatsapp_empresa'] : '5517999999999';
    
    // Buscar nome do serviço
    try {
        $stmt = $pdo->prepare("SELECT nome, preco_base FROM servicos WHERE id = ?");
        $stmt->execute([$dados_orcamento['servico_id']]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_servico = $servico ? $servico['nome'] : 'Serviço não encontrado';
        $preco_servico = $servico ? formatarMoeda($servico['preco_base']) : 'Não informado';
    } catch(PDOException $e) {
        $nome_servico = 'Serviço não encontrado';
        $preco_servico = 'Não informado';
    }
    
    // Preparar mensagem completa para orçamento
    $mensagem = "💰 *SOLICITAÇÃO DE ORÇAMENTO*

👤 *DADOS DO CLIENTE*
• *Nome:* {$dados_orcamento['nome']}
• *Telefone:* {$dados_orcamento['telefone']}
• *E-mail:* {$dados_orcamento['email']}

🔧 *SERVIÇO SOLICITADO*
• *Serviço:* {$nome_servico}
• *Valor Base:* {$preco_servico}

⚙️ *INFORMAÇÕES DO EQUIPAMENTO*
• *Marca:* {$dados_orcamento['marca']}
• *Capacidade (BTUs):* {$dados_orcamento['btus']} BTUs
• *Tipo:* {$dados_orcamento['tipo']}

📝 *OBSERVAÇÕES*
{$dados_orcamento['observacoes']}

⏰ *Solicitado via Site em* " . date('d/m/Y \à\s H:i') . "
📞 *Entraremos em contato em breve!*";
    
    // Codificar mensagem para URL
    $mensagem_encoded = urlencode($mensagem);
    
    // Gerar link do WhatsApp
    $link_whatsapp = "https://wa.me/{$whatsapp}?text={$mensagem_encoded}";
    
    return $link_whatsapp;
}
// Buscar configurações do agendamento
function getConfigAgendamento($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = [];
        foreach($resultados as $row) {
            $config[$row['chave']] = $row['valor'];
        }
        
        // Valores padrão caso não existam no banco
        $config_padrao = [
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'horario_especial_inicio' => '18:00',
            'horario_especial_fim' => '20:00',
            'bloquear_finais_semana' => '1',
            'valor_hora_especial' => '50.00',
            'valor_final_semana' => '150.00',
            'limite_agendamentos_dia' => '8',
            'whatsapp_empresa' => '5511999999999',
            'mensagem_whatsapp' => '📅 *Novo Agendamento*

👤 *Cliente:* {nome}
📞 *Telefone:* {telefone}
📧 *E-mail:* {email}
📍 *Endereço:* {endereco}

🔧 *Serviço:* {servico}
📅 *Data:* {data}
⏰ *Horário:* {hora}

💬 *Observações:*
{observacoes}

⚡ *Agendado via Site*'
        ];
        
        // Combinar configurações do banco com padrões
        return array_merge($config_padrao, $config);
        
    } catch(PDOException $e) {
        // Retornar valores padrão em caso de erro
        return [
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'horario_especial_inicio' => '18:00',
            'horario_especial_fim' => '20:00',
            'bloquear_finais_semana' => '1',
            'valor_hora_especial' => '50.00',
            'valor_final_semana' => '150.00',
            'limite_agendamentos_dia' => '8',
            'whatsapp_empresa' => '5511999999999',
            'mensagem_whatsapp' => '📅 *Novo Agendamento*

👤 *Cliente:* {nome}
📞 *Telefone:* {telefone}
📧 *E-mail:* {email}
📍 *Endereço:* {endereco}

🔧 *Serviço:* {servico}
📅 *Data:* {data}
⏰ *Horário:* {hora}

💬 *Observações:*
{observacoes}

⚡ *Agendado via Site*'
        ];
    }
}

// Gerar horários disponíveis para uma data específica
function gerarHorariosDisponiveis($pdo, $data) {
    $config = getConfigAgendamento($pdo);
    $horarios = [];
    
    // Horário comercial normal
    $inicio = new DateTime($config['horario_inicio']);
    $fim = new DateTime($config['horario_fim']);
    
    while($inicio < $fim) {
        $hora_str = $inicio->format('H:i');
        
        // Verificar se horário está disponível
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ? AND status IN ('agendado', 'confirmado')");
        $stmt->execute([$data, $hora_str]);
        $ocupado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if($ocupado == 0) {
            $horarios[] = [
                'hora' => $hora_str,
                'especial' => false
            ];
        }
        
        $inicio->modify('+1 hour');
    }
    
    // Horário especial (se configurado)
    if(isset($config['horario_especial_inicio']) && $config['horario_especial_inicio'] && 
       isset($config['horario_especial_fim']) && $config['horario_especial_fim']) {
        $inicio_especial = new DateTime($config['horario_especial_inicio']);
        $fim_especial = new DateTime($config['horario_especial_fim']);
        
        while($inicio_especial < $fim_especial) {
            $hora_str = $inicio_especial->format('H:i');
            
            // Verificar se horário está disponível
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ? AND status IN ('agendado', 'confirmado')");
            $stmt->execute([$data, $hora_str]);
            $ocupado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if($ocupado == 0) {
                $horarios[] = [
                    'hora' => $hora_str,
                    'especial' => true
                ];
            }
            
            $inicio_especial->modify('+1 hour');
        }
    }
    
    return $horarios;
}

// Gerar datas disponíveis para agendamento
// Gerar datas disponíveis para agendamento - VERSÃO CORRIGIDA
// Gerar datas disponíveis para agendamento - VERSÃO CORRIGIDA COM TIMEZONE
function gerarDatasDisponiveis($pdo) {
    $config = getConfigAgendamento($pdo);
    $datas_disponiveis = [];
    
    // Dias futuros disponíveis para agendamento
    try {
        $stmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave = 'dias_agendamento'");
        $stmt->execute();
        $dias_config = $stmt->fetch(PDO::FETCH_ASSOC);
        $dias_futuros = $dias_config ? intval($dias_config['valor']) : 30;
    } catch(PDOException $e) {
        $dias_futuros = 30;
    }
    
    // ✅ CORREÇÃO: Data atual com timezone explícito
    $data_atual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $data_atual->setTime(0, 0, 0); // Zerar hora para evitar problemas
    
    for ($i = 0; $i < $dias_futuros; $i++) {
        $data = clone $data_atual;
        $data->modify("+{$i} days");
        $data_str = $data->format('Y-m-d');
        $dia_semana = $data->format('l');
        
        // ✅ DEBUG: Verificar data gerada
        error_log("Data gerada: {$data_str} - Dia: {$dia_semana}");
        
        $dia_semana_traduzido = traduzirDiaSemanaIngles($dia_semana);
        
        // Verificar se é final de semana
        $is_final_semana = in_array($dia_semana, ['Saturday', 'Sunday']);
        
        if ($is_final_semana && isset($config['bloquear_finais_semana']) && $config['bloquear_finais_semana'] == '1') {
            continue;
        }
        
        // Verificar limite diário
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND status IN ('agendado', 'confirmado')");
            $stmt->execute([$data_str]);
            $agendamentos_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $limite_diario = isset($config['limite_agendamentos_dia']) ? intval($config['limite_agendamentos_dia']) : 8;
            
            if ($agendamentos_dia >= $limite_diario) {
                continue;
            }
        } catch(PDOException $e) {
            // Continuar mesmo com erro
        }
        
        // Gerar horários disponíveis
        $horarios_disponiveis = gerarHorariosDisponiveis($pdo, $data_str);
        
        if (count($horarios_disponiveis) > 0) {
            $datas_disponiveis[] = [
                'data' => $data_str,
                'formatada' => $data->format('d/m/Y'),
                'dia_semana' => $dia_semana_traduzido,
                'dia_semana_ingles' => $dia_semana,
                'total_horarios' => count($horarios_disponiveis)
            ];
        }
    }
    
    return $datas_disponiveis;
}

// ✅ CORREÇÃO: Função específica para traduzir dias da semana do inglês
function traduzirDiaSemanaIngles($diaIngles) {
    $dias = [
        'Monday' => 'Segunda',
        'Tuesday' => 'Terça', 
        'Wednesday' => 'Quarta',
        'Thursday' => 'Quinta',
        'Friday' => 'Sexta',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
    return $dias[$diaIngles] ?? $diaIngles;
}

// Verificar disponibilidade de um horário específico
function verificarDisponibilidade($pdo, $data, $hora) {
    $config = getConfigAgendamento($pdo);
    
    // Verificar se já existe agendamento neste horário
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ? AND status IN ('agendado', 'confirmado')");
        $stmt->execute([$data, $hora]);
        $ocupado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($ocupado > 0) {
            return ['disponivel' => false, 'motivo' => 'Horário já ocupado'];
        }
        
        // Verificar limite diário
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND status IN ('agendado', 'confirmado')");
        $stmt->execute([$data]);
        $agendamentos_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $limite_diario = isset($config['limite_agendamentos_dia']) ? intval($config['limite_agendamentos_dia']) : 8;
        
        if ($agendamentos_dia >= $limite_diario) {
            return ['disponivel' => false, 'motivo' => 'Limite diário de agendamentos atingido'];
        }
    } catch(PDOException $e) {
        return ['disponivel' => false, 'motivo' => 'Erro ao verificar disponibilidade'];
    }
    
    // Verificar se é final de semana bloqueado
    $dia_semana = date('l', strtotime($data));
    $is_final_semana = in_array($dia_semana, ['Saturday', 'Sunday']);
    
    if ($is_final_semana && isset($config['bloquear_finais_semana']) && $config['bloquear_finais_semana'] == '1') {
        return ['disponivel' => false, 'motivo' => 'Não atendemos finais de semana'];
    }
    
    // Verificar se está dentro do horário comercial
    $hora_dt = DateTime::createFromFormat('H:i', $hora);
    $inicio = DateTime::createFromFormat('H:i', $config['horario_inicio']);
    $fim = DateTime::createFromFormat('H:i', $config['horario_fim']);
    
    $dentro_horario_comercial = ($hora_dt >= $inicio && $hora_dt < $fim);
    
    // Verificar se está dentro do horário especial
    $dentro_horario_especial = false;
    if (isset($config['horario_especial_inicio']) && $config['horario_especial_inicio'] && 
        isset($config['horario_especial_fim']) && $config['horario_especial_fim']) {
        $inicio_especial = DateTime::createFromFormat('H:i', $config['horario_especial_inicio']);
        $fim_especial = DateTime::createFromFormat('H:i', $config['horario_especial_fim']);
        $dentro_horario_especial = ($hora_dt >= $inicio_especial && $hora_dt < $fim_especial);
    }
    
    if (!$dentro_horario_comercial && !$dentro_horario_especial) {
        return ['disponivel' => false, 'motivo' => 'Fora do horário de atendimento'];
    }
    
    return ['disponivel' => true, 'motivo' => ''];
}

// Calcular acréscimo por horário especial ou final de semana - VERSÃO DEBUG
function calcularAcrescimoHorario($data, $hora) {
    global $pdo;
    $config = getConfigAgendamento($pdo);
    $acrescimo = 0;
    
    echo "<!-- DEBUG: Data: $data, Hora: $hora -->";
    echo "<!-- DEBUG: Config horario_especial_inicio: " . ($config['horario_especial_inicio'] ?? 'NULL') . " -->";
    echo "<!-- DEBUG: Config horario_especial_fim: " . ($config['horario_especial_fim'] ?? 'NULL') . " -->";
    echo "<!-- DEBUG: Config valor_hora_especial: " . ($config['valor_hora_especial'] ?? 'NULL') . " -->";
    
    // Verificar se é final de semana (0 = Domingo, 6 = Sábado)
    $dia_semana = date('w', strtotime($data));
    $is_final_semana = ($dia_semana == 0 || $dia_semana == 6);
    
    echo "<!-- DEBUG: Dia semana: $dia_semana, Final semana: " . ($is_final_semana ? 'SIM' : 'NÃO') . " -->";
    
    // Aplicar acréscimo de final de semana SE configurado e SE não estiver bloqueado
    $bloquear_finais_semana = isset($config['bloquear_finais_semana']) && $config['bloquear_finais_semana'] == '1';
    
    if($is_final_semana && !$bloquear_finais_semana && isset($config['valor_final_semana']) && $config['valor_final_semana'] > 0) {
        $acrescimo += floatval($config['valor_final_semana']);
        echo "<!-- DEBUG: Acréscimo final semana aplicado: " . $config['valor_final_semana'] . " -->";
    }
    
    // Verificar se é horário especial
    if(isset($config['horario_especial_inicio']) && $config['horario_especial_inicio'] && 
       isset($config['horario_especial_fim']) && $config['horario_especial_fim']) {
        
        $hora_dt = DateTime::createFromFormat('H:i', $hora);
        $inicio_especial = DateTime::createFromFormat('H:i', $config['horario_especial_inicio']);
        $fim_especial = DateTime::createFromFormat('H:i', $config['horario_especial_fim']);
        
        // Debug das horas
        echo "<!-- DEBUG: Hora selecionada: " . $hora_dt->format('H:i') . " -->";
        echo "<!-- DEBUG: Inicio especial: " . $inicio_especial->format('H:i') . " -->";
        echo "<!-- DEBUG: Fim especial: " . $fim_especial->format('H:i') . " -->";
        
        $dentro_horario_especial = ($hora_dt >= $inicio_especial && $hora_dt < $fim_especial);
        
        echo "<!-- DEBUG: Dentro horário especial: " . ($dentro_horario_especial ? 'SIM' : 'NÃO') . " -->";
        
        if($dentro_horario_especial && isset($config['valor_hora_especial']) && $config['valor_hora_especial'] > 0) {
            $acrescimo += floatval($config['valor_hora_especial']);
            echo "<!-- DEBUG: Acréscimo horário especial aplicado: " . $config['valor_hora_especial'] . " -->";
        }
    }
    
    echo "<!-- DEBUG: Acréscimo total: $acrescimo -->";
    return $acrescimo;
}

// Enviar mensagem para WhatsApp
function enviarWhatsAppAgendamento($pdo, $dados_agendamento) {
    $config = getConfigAgendamento($pdo);
    $whatsapp = isset($config['whatsapp_empresa']) ? $config['whatsapp_empresa'] : '5517999999999';
    
    // Buscar nome do serviço
    try {
        $stmt = $pdo->prepare("SELECT nome FROM servicos WHERE id = ?");
        $stmt->execute([$dados_agendamento['servico_id']]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_servico = $servico ? $servico['nome'] : 'Serviço não encontrado';
    } catch(PDOException $e) {
        $nome_servico = 'Serviço não encontrado';
    }
    
    // Preparar mensagem
    $mensagem = isset($config['mensagem_whatsapp']) ? $config['mensagem_whatsapp'] : 'Novo agendamento recebido';
    $mensagem = str_replace('{nome}', $dados_agendamento['nome'], $mensagem);
    $mensagem = str_replace('{telefone}', $dados_agendamento['telefone'], $mensagem);
    $mensagem = str_replace('{email}', $dados_agendamento['email'], $mensagem);
    $mensagem = str_replace('{endereco}', $dados_agendamento['endereco'], $mensagem);
    $mensagem = str_replace('{servico}', $nome_servico, $mensagem);
    $mensagem = str_replace('{data}', date('d/m/Y', strtotime($dados_agendamento['data_agendamento'])), $mensagem);
    $mensagem = str_replace('{hora}', $dados_agendamento['hora_agendamento'], $mensagem);
    $mensagem = str_replace('{observacoes}', $dados_agendamento['observacoes'] ?: 'Nenhuma observação', $mensagem);
    
    // Codificar mensagem para URL
    $mensagem_encoded = urlencode($mensagem);
    
    // Gerar link do WhatsApp
    $link_whatsapp = "https://wa.me/{$whatsapp}?text={$mensagem_encoded}";
    
    return $link_whatsapp;
}
?>