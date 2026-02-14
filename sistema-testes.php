<?php
// sistema-ia.php - SISTEMA UNIFICADO COM IA CONVERSACIONAL (VERSÃO SIMPLIFICADA)
// SEM: Taxas adicionais, Sistema de email
// COM: Rate limiting, Logs detalhados, Backup automático, WhatsApp

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Incluir configuração
include 'includes/config.php';
include 'includes/funcoes_agendamento.php';

// ============================================================================
// FUNÇÕES DE LOG E AUDITORIA
// ============================================================================

/**
 * Registrar log detalhado para auditoria
 */
function registrarLog($tipo, $mensagem, $dados = [], $ip = null) {
    global $pdo;
    
    if (!$ip) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    $sessao_id = session_id();
    $pagina = $_SERVER['PHP_SELF'] ?? 'desconhecido';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    $dados_json = !empty($dados) ? json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '{}';
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs_sistema 
            (tipo, mensagem, dados, ip_address, user_agent, sessao_id, pagina, referer, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $tipo,
            substr($mensagem, 0, 500),
            $dados_json,
            $ip,
            substr($user_agent, 0, 255),
            $sessao_id,
            $pagina,
            $referer
        ]);
        
    } catch (PDOException $e) {
        // Fallback para arquivo de log se o banco falhar
        $log_entry = date('Y-m-d H:i:s') . " [$tipo] $mensagem | IP: $ip | Dados: " . json_encode($dados) . "\n";
        file_put_contents('logs/sistema_ia.log', $log_entry, FILE_APPEND);
        return true;
    }
}

/**
 * Criar tabela de logs se não existir
 */
function criarTabelaLogs($pdo) {
    try {
        $sql = "
        CREATE TABLE IF NOT EXISTS logs_sistema (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo VARCHAR(50) NOT NULL,
            mensagem VARCHAR(500) NOT NULL,
            dados JSON,
            ip_address VARCHAR(45),
            user_agent VARCHAR(255),
            sessao_id VARCHAR(128),
            pagina VARCHAR(255),
            referer VARCHAR(500),
            data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tipo (tipo),
            INDEX idx_data (data_criacao),
            INDEX idx_ip (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao criar tabela de logs: " . $e->getMessage());
        return false;
    }
}

// Criar tabela de logs na primeira execução
criarTabelaLogs($pdo);

// ============================================================================
// RATE LIMITING - Prevenção de abuso
// ============================================================================

/**
 * Verificar e aplicar rate limiting
 */
function verificarRateLimit($tipo = 'agendamento', $limite = 10, $periodo = 3600) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    $sessao_id = session_id();
    
    // Chave única para este IP/sessão/tipo
    $chave = "rate_limit_{$tipo}_{$ip}_{$sessao_id}";
    
    if (!isset($_SESSION[$chave])) {
        $_SESSION[$chave] = [
            'contador' => 0,
            'primeira_tentativa' => time(),
            'bloqueado_ate' => 0
        ];
    }
    
    $dados_limite = &$_SESSION[$chave];
    
    // Se estiver bloqueado, verificar se já pode liberar
    if ($dados_limite['bloqueado_ate'] > time()) {
        $tempo_restante = $dados_limite['bloqueado_ate'] - time();
        
        registrarLog('rate_limit', "Tentativa bloqueada - IP: $ip", [
            'tipo' => $tipo,
            'contador' => $dados_limite['contador'],
            'bloqueado_ate' => date('Y-m-d H:i:s', $dados_limite['bloqueado_ate']),
            'tempo_restante' => $tempo_restante
        ], $ip);
        
        return [
            'permitido' => false,
            'contador' => $dados_limite['contador'],
            'limite' => $limite,
            'bloqueado_ate' => $dados_limite['bloqueado_ate'],
            'tempo_restante' => $tempo_restante,
            'mensagem' => "Muitas tentativas. Tente novamente em " . ceil($tempo_restante / 60) . " minutos."
        ];
    }
    
    // Se passou o período, resetar contador
    if ((time() - $dados_limite['primeira_tentativa']) > $periodo) {
        $dados_limite['contador'] = 0;
        $dados_limite['primeira_tentativa'] = time();
        $dados_limite['bloqueado_ate'] = 0;
    }
    
    // Incrementar contador
    $dados_limite['contador']++;
    
    // Verificar se excedeu o limite
    if ($dados_limite['contador'] > $limite) {
        // Bloquear por 2x o período
        $dados_limite['bloqueado_ate'] = time() + ($periodo * 2);
        
        registrarLog('rate_limit_bloqueio', "Usuário bloqueado - IP: $ip", [
            'tipo' => $tipo,
            'contador' => $dados_limite['contador'],
            'limite' => $limite,
            'bloqueado_ate' => date('Y-m-d H:i:s', $dados_limite['bloqueado_ate'])
        ], $ip);
        
        return [
            'permitido' => false,
            'contador' => $dados_limite['contador'],
            'limite' => $limite,
            'bloqueado_ate' => $dados_limite['bloqueado_ate'],
            'tempo_restante' => $periodo * 2,
            'mensagem' => "Limite de tentativas excedido. Bloqueado por " . ($periodo * 2 / 60) . " minutos."
        ];
    }
    
    return [
        'permitido' => true,
        'contador' => $dados_limite['contador'],
        'limite' => $limite,
        'restante' => $limite - $dados_limite['contador']
    ];
}

// Aplicar rate limiting para requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $rate_limit = verificarRateLimit('post_request', 30, 300);
    
    if (!$rate_limit['permitido']) {
        http_response_code(429);
        die(json_encode([
            'erro' => true,
            'mensagem' => $rate_limit['mensagem']
        ]));
    }
}

// ============================================================================
// FUNÇÕES BÁSICAS DO SISTEMA
// ============================================================================

// ✅ NOVA FUNÇÃO: Verificar disponibilidade de horários
function verificarDisponibilidadeIA($pdo, $data, $hora) {
    try {
        // Verificar quantos agendamentos já existem neste horário
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM agendamentos 
            WHERE data_agendamento = ? 
            AND hora_agendamento = ?
            AND status NOT IN ('cancelado', 'rejeitado')
        ");
        $stmt->execute([$data, $hora]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Configurações do sistema
        $config = getConfigAgendamento($pdo);
        $limite_diario = $config['limite_agendamentos_dia'] ?? 8;
        
        // Verificar limite diário para a data
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_dia 
            FROM agendamentos 
            WHERE data_agendamento = ?
            AND status NOT IN ('cancelado', 'rejeitado')
        ");
        $stmt->execute([$data]);
        $resultado_dia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $mensagens = [];
        
        // Verificar se horário específico está disponível (máximo 1 por horário)
        if ($resultado['total'] > 0) {
            $mensagens[] = "Horário {$hora} já está ocupado";
        }
        
        // Verificar limite diário
        if ($resultado_dia['total_dia'] >= $limite_diario) {
            $mensagens[] = "Limite diário de {$limite_diario} agendamentos atingido para {$data}";
        }
        
        if (!empty($mensagens)) {
            return [
                'disponivel' => false,
                'motivo' => implode('. ', $mensagens)
            ];
        }
        
        return ['disponivel' => true, 'motivo' => ''];
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar disponibilidade IA: " . $e->getMessage());
        registrarLog('erro_banco', "Falha ao verificar disponibilidade: " . $e->getMessage(), [
            'data' => $data,
            'hora' => $hora
        ]);
        return ['disponivel' => false, 'motivo' => 'Erro no sistema'];
    }
}

// ✅ NOVA FUNÇÃO: Obter horários ocupados para uma data
function getHorariosOcupados($pdo, $data) {
    $horarios_ocupados = [];
    try {
        $stmt = $pdo->prepare("
            SELECT hora_agendamento 
            FROM agendamentos 
            WHERE data_agendamento = ? 
            AND status NOT IN ('cancelado', 'rejeitado')
        ");
        $stmt->execute([$data]);
        $ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $horarios_ocupados = array_flip($ocupados);
    } catch (PDOException $e) {
        error_log("Erro ao buscar horários ocupados: " . $e->getMessage());
        registrarLog('erro_banco', "Falha ao buscar horários ocupados: " . $e->getMessage(), [
            'data' => $data
        ]);
    }
    return $horarios_ocupados;
}

// ============================================================================
// INICIALIZAÇÃO DA SESSÃO E CONVERSA
// ============================================================================

// Limpar conversa
if (isset($_GET['limpar']) && $_GET['limpar'] == 1) {
    $sessao_antiga = session_id();
    unset($_SESSION['ia_conversa']);
    session_destroy();
    session_start();
    
    registrarLog('sessao', "Conversa reiniciada pelo usuário", [
        'sessao_antiga' => $sessao_antiga,
        'sessao_nova' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ]);
    
    header('Location: sistema-ia.php');
    exit;
}

// Aplicar rate limiting para novas conversas
$rate_limit_conversa = verificarRateLimit('nova_conversa', 5, 3600);
if (!$rate_limit_conversa['permitido'] && !isset($_SESSION['ia_conversa'])) {
    die("
        <html>
        <head>
            <title>Limite Excedido</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .container { max-width: 600px; margin: 0 auto; }
                .error { background: #fff3f3; border: 2px solid #ff6b6b; padding: 30px; border-radius: 10px; }
                h1 { color: #ff6b6b; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error'>
                    <h1>⏰ Limite de Acesso Excedido</h1>
                    <p>{$rate_limit_conversa['mensagem']}</p>
                    <p>Por favor, tente novamente mais tarde ou entre em contato conosco pelo WhatsApp.</p>
                    <p><a href='index.php'>Voltar para a página inicial</a></p>
                </div>
            </div>
        </body>
        </html>
    ");
}

// Inicializar sessão se não existir
if (!isset($_SESSION['ia_conversa'])) {
    $_SESSION['ia_conversa'] = [
        'etapa' => 1,
        'dados' => [],
        'servicos_selecionados' => [],
        'equipamentos_btus' => [],
        'total_equipamentos' => 0,
        'total_servicos_valor' => 0,
        'mostrar_popup_desconto' => false,
        'ultima_pergunta' => '',
        'servicos_disponiveis' => [],
        'inicio_conversa' => date('Y-m-d H:i:s'),
        'equipamento_info' => []
    ];
    
    registrarLog('conversa_inicio', "Nova conversa iniciada", [
        'sessao_id' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido'
    ]);
    
    // Inicializar serviços
    try {
        $stmt = $pdo->prepare("SELECT id, nome, descricao, preco_base FROM servicos WHERE ativo = 1 ORDER BY nome");
        $stmt->execute();
        $_SESSION['ia_conversa']['servicos_disponiveis'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        registrarLog('servicos_carregados', "Serviços carregados do banco", [
            'quantidade' => count($_SESSION['ia_conversa']['servicos_disponiveis'])
        ]);
    } catch (PDOException $e) {
        $_SESSION['ia_conversa']['servicos_disponiveis'] = [
            ['id' => 5, 'nome' => 'Instalação de ar condicionado', 'preco_base' => 350.00],
            ['id' => 6, 'nome' => 'Limpeza com remoção do equipamento', 'preco_base' => 550.00],
            ['id' => 7, 'nome' => 'Limpeza Completa (no local com bolsa coletora)', 'preco_base' => 200.00],
            ['id' => 8, 'nome' => 'Manutenção corretiva, Diagnóstico e Reparo', 'preco_base' => 100.00],
            ['id' => 9, 'nome' => 'Remoção de Equipamento', 'preco_base' => 300.00]
        ];
        
        registrarLog('servicos_fallback', "Usando lista padrão de serviços", [
            'quantidade' => count($_SESSION['ia_conversa']['servicos_disponiveis']),
            'erro' => $e->getMessage()
        ]);
    }
    
    // Saudação inicial
    $hora = date('H');
    if ($hora >= 5 && $hora < 12) {
        $saudacao = 'Bom dia';
    } elseif ($hora >= 12 && $hora < 18) {
        $saudacao = 'Boa tarde';
    } else {
        $saudacao = 'Boa noite';
    }
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => "🤖 $saudacao! Tudo bem com você?\n\nEu sou a Laura, assistente virtual da N&M Refrigeração.\nVou te ajudar a agendar seu serviço de forma rápida e fácil. Vamos lá?",
        'tipo' => 'mensagem'
    ];
}

// ============================================================================
// PROCESSAMENTO DAS RESPOSTAS
// ============================================================================

// Processar respostas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $resposta = $_POST['resposta'] ?? '';
    
    // Aplicar rate limiting para processamento
    $rate_limit_process = verificarRateLimit('processamento', 50, 300);
    
    if (!$rate_limit_process['permitido']) {
        http_response_code(429);
        echo json_encode([
            'erro' => true,
            'mensagem' => $rate_limit_process['mensagem']
        ]);
        exit;
    }
    
    // Registrar tentativa de processamento
    registrarLog('processamento_tentativa', "Processando ação: $acao", [
        'acao' => $acao,
        'resposta' => substr($resposta, 0, 100),
        'sessao' => session_id(),
        'etapa' => $_SESSION['ia_conversa']['etapa'] ?? 1
    ]);
    
    processarRespostaSimples($acao, $resposta);
}

// Função principal de processamento
function processarRespostaSimples($acao, $resposta) {
    global $pdo;
    
    $dados = &$_SESSION['ia_conversa']['dados'];
    
    switch ($acao) {
        case 'iniciar':
            $_SESSION['ia_conversa']['ultima_pergunta'] = [
                'texto' => "Vamos começar!\n\nQual o seu nome completo?",
                'tipo' => 'pergunta_texto',
                'acao' => 'nome'
            ];
            
            registrarLog('conversa_etapa', "Início do agendamento", [
                'etapa' => 'inicio',
                'sessao' => session_id()
            ]);
            break;
            
        case 'nome':
            $dados['nome'] = trim($resposta);
            $primeiro_nome = explode(' ', $dados['nome'])[0];
            
            registrarLog('conversa_etapa', "Nome recebido", [
                'etapa' => 'nome',
                'nome' => $dados['nome'],
                'primeiro_nome' => $primeiro_nome
            ]);
            
            $_SESSION['ia_conversa']['ultima_pergunta'] = [
                'texto' => "Perfeito, $primeiro_nome! 😊\n\nAgora preciso do seu número de WhatsApp pra gente se comunicar melhor.\n\n📱 Pode digitar assim: (17) 99624-0725",
                'tipo' => 'pergunta_texto',
                'acao' => 'whatsapp'
            ];
            break;
            
        case 'whatsapp':
            $telefone_limpo = preg_replace('/[^0-9]/', '', $resposta);
            
            if (strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
                registrarLog('conversa_erro', "Telefone inválido", [
                    'etapa' => 'whatsapp',
                    'telefone_entrada' => $resposta,
                    'telefone_limpo' => $telefone_limpo
                ]);
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "❌ **Ops, número inválido!**\n\nPode verificar se digitou certo? Precisa ter DDD + número.\n\nExemplo: (17) 99624-0725",
                    'tipo' => 'pergunta_texto',
                    'acao' => 'whatsapp'
                ];
                break;
            }
            
            // Formatar telefone
            if (strlen($telefone_limpo) == 11) {
                $telefone_formatado = '(' . substr($telefone_limpo, 0, 2) . ') ' . 
                                      substr($telefone_limpo, 2, 1) . ' ' . 
                                      substr($telefone_limpo, 3, 4) . '-' . 
                                      substr($telefone_limpo, 7);
            } else {
                $telefone_formatado = '(' . substr($telefone_limpo, 0, 2) . ') ' . 
                                      substr($telefone_limpo, 2, 4) . '-' . 
                                      substr($telefone_limpo, 6);
            }
            
            $dados['telefone'] = $telefone_limpo;
            $dados['telefone_formatado'] = $telefone_formatado;
            $primeiro_nome = explode(' ', $dados['nome'])[0];
            
            // Verificar se é cliente existente
            try {
                $stmt = $pdo->prepare("SELECT * FROM clientes WHERE telefone = ?");
                $stmt->execute([$telefone_limpo]);
                $cliente_existente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cliente_existente) {
                    $cliente_id = $cliente_existente['id'];
                    $dados['cliente_id'] = $cliente_id;
                    
                    // Verificar se o último orçamento tem status 'concluido'
                    $stmt = $pdo->prepare("
                        SELECT status 
                        FROM orcamentos 
                        WHERE cliente_id = ? 
                        AND status = 'concluido'
                        ORDER BY data_solicitacao DESC 
                        LIMIT 1
                    ");
                    $stmt->execute([$cliente_id]);
                    $ultimo_orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($ultimo_orcamento) {
                        $dados['desconto_fidelidade'] = 5;
                        $_SESSION['ia_conversa']['mostrar_popup_desconto'] = true;
                        
                        registrarLog('cliente_fidelidade', "Cliente existente com desconto", [
                            'cliente_id' => $cliente_id,
                            'nome' => $dados['nome'],
                            'telefone' => $telefone_limpo
                        ]);
                    }
                }
            } catch (PDOException $e) {
                registrarLog('erro_banco', "Falha ao verificar cliente existente: " . $e->getMessage(), [
                    'telefone' => $telefone_limpo
                ]);
            }
            
            registrarLog('conversa_etapa', "Telefone recebido", [
                'etapa' => 'whatsapp',
                'telefone_limpo' => $telefone_limpo,
                'telefone_formatado' => $telefone_formatado,
                'cliente_existente' => isset($cliente_existente)
            ]);
            
            // Mostrar serviços
            $opcoes = [];
            foreach ($_SESSION['ia_conversa']['servicos_disponiveis'] as $servico) {
                $opcoes[] = [
                    'texto' => $servico['nome'],
                    'acao' => 'selecionar_servico',
                    'valor' => $servico['nome']
                ];
            }
            
            $_SESSION['ia_conversa']['ultima_pergunta'] = [
                'texto' => "✅ Ótimo! Anotei aqui.\n\nMuito prazer em conhecer você, $primeiro_nome! 😊\n\nQual serviço você precisa hoje?",
                'tipo' => 'pergunta_botoes',
                'acao' => '',
                'opcoes' => $opcoes
            ];
            break;
            
        case 'selecionar_servico':
            // Encontrar o serviço selecionado
            $servico_selecionado = null;
            foreach ($_SESSION['ia_conversa']['servicos_disponiveis'] as $servico) {
                if ($servico['nome'] == $resposta) {
                    $servico_selecionado = $servico;
                    break;
                }
            }
            
            if ($servico_selecionado) {
                $_SESSION['ia_conversa']['servico_temp'] = [
                    'id' => $servico_selecionado['id'],
                    'nome' => $servico_selecionado['nome'],
                    'preco' => $servico_selecionado['preco_base']
                ];
                
                registrarLog('servico_selecionado', "Serviço selecionado", [
                    'servico_id' => $servico_selecionado['id'],
                    'servico_nome' => $servico_selecionado['nome'],
                    'preco' => $servico_selecionado['preco_base']
                ]);
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "✅ **Ótima escolha! ($resposta)\n\nQuantos equipamentos vão precisar desse serviço?\n\nDigite o número (ex: 1, 2, 3...):",
                    'tipo' => 'pergunta_texto',
                    'acao' => 'quantidade_equipamentos'
                ];
            }
            break;
            
        case 'quantidade_equipamentos':
            $quantidade = intval($resposta);
            
            if ($quantidade < 1) {
                registrarLog('conversa_erro', "Quantidade inválida", [
                    'etapa' => 'quantidade_equipamentos',
                    'entrada' => $resposta,
                    'quantidade' => $quantidade
                ]);
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "❌ Ops, número inválido!\n\nPrecisa ser 1 ou mais. Pode digitar novamente?",
                    'tipo' => 'pergunta_texto',
                    'acao' => 'quantidade_equipamentos'
                ];
                break;
            }
            
            $servico_temp = $_SESSION['ia_conversa']['servico_temp'];
            $servico = [
                'id' => $servico_temp['id'],
                'nome' => $servico_temp['nome'],
                'quantidade' => $quantidade,
                'preco_unitario' => $servico_temp['preco'],
                'subtotal' => $servico_temp['preco'] * $quantidade
            ];
            
            $_SESSION['ia_conversa']['servicos_selecionados'][] = $servico;
            
            // Armazenar informações do serviço atual para os BTUs
            $_SESSION['ia_conversa']['servico_btu_atual'] = [
                'servico_id' => $servico['id'],
                'servico_nome' => $servico['nome'],
                'quantidade' => $quantidade,
                'equipamentos_restantes' => $quantidade,
                'equipamentos_coletados' => 0
            ];
            
            // Inicializar array para armazenar BTUs por equipamento
            if (!isset($_SESSION['ia_conversa']['equipamento_info'])) {
                $_SESSION['ia_conversa']['equipamento_info'] = [];
            }
            
            // Calcular totais
            $total_equipamentos = 0;
            $total_valor = 0;
            foreach ($_SESSION['ia_conversa']['servicos_selecionados'] as $s) {
                $total_equipamentos += $s['quantidade'];
                $total_valor += $s['subtotal'];
            }
            
            $_SESSION['ia_conversa']['total_equipamentos'] = $total_equipamentos;
            $_SESSION['ia_conversa']['total_servicos_valor'] = $total_valor;
            
            registrarLog('quantidade_adicionada', "Quantidade de equipamentos adicionada", [
                'servico' => $servico_temp['nome'],
                'quantidade' => $quantidade,
                'subtotal' => $servico['subtotal'],
                'total_equipamentos' => $total_equipamentos,
                'total_valor' => $total_valor
            ]);
            
            // Verificar se quer mais serviços
            $servicos_selecionados_ids = array_column($_SESSION['ia_conversa']['servicos_selecionados'], 'id');
            $servicos_restantes = array_filter(
                $_SESSION['ia_conversa']['servicos_disponiveis'],
                function($s) use ($servicos_selecionados_ids) {
                    return !in_array($s['id'], $servicos_selecionados_ids);
                }
            );
            
            if (!empty($servicos_restantes)) {
                $opcoes = [
                    ['texto' => '✅ Sim, preciso de mais um serviço', 'acao' => 'mais_servicos', 'valor' => 'sim'],
                    ['texto' => '❌ Não, é só isso mesmo', 'acao' => 'mais_servicos', 'valor' => 'nao']
                ];
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "✅ Anotado! {$servico_temp['nome']} (x$quantidade)\n\nPrecisa de mais algum serviço?",
                    'tipo' => 'pergunta_botoes',
                    'opcoes' => $opcoes
                ];
            } else {
                // Começar a coletar BTUs
                iniciarColetaBTUs();
            }
            break;
            
        case 'mais_servicos':
            if ($resposta == 'sim') {
                $servicos_selecionados_ids = array_column($_SESSION['ia_conversa']['servicos_selecionados'], 'id');
                $servicos_restantes = array_filter(
                    $_SESSION['ia_conversa']['servicos_disponiveis'],
                    function($s) use ($servicos_selecionados_ids) {
                        return !in_array($s['id'], $servicos_selecionados_ids);
                    }
                );
                
                if (empty($servicos_restantes)) {
                    // Começar a coletar BTUs
                    iniciarColetaBTUs();
                } else {
                    $opcoes = [];
                    foreach ($servicos_restantes as $servico) {
                        $opcoes[] = [
                            'texto' => $servico['nome'],
                            'acao' => 'selecionar_servico',
                            'valor' => $servico['nome']
                        ];
                    }
                    
                    $_SESSION['ia_conversa']['ultima_pergunta'] = [
                        'texto' => "Escolha outro serviço:",
                        'tipo' => 'pergunta_botoes',
                        'opcoes' => $opcoes
                    ];
                }
            } else {
                // Começar a coletar BTUs
                iniciarColetaBTUs();
            }
            break;
            
        case 'btu_equipamento':
            $resposta = trim($resposta);
            
            // Validar se é um número de BTU válido
            if (!preg_match('/^\d+$/', $resposta) || intval($resposta) < 1000) {
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "❌ Valor inválido!\n\nPor favor, digite um valor de BTU válido (ex: 7000, 9000, 12000, 18000).",
                    'tipo' => 'pergunta_texto',
                    'acao' => 'btu_equipamento'
                ];
                break;
            }
            
            $servico_btu = $_SESSION['ia_conversa']['servico_btu_atual'];
            $equipamento_num = $servico_btu['equipamentos_coletados'] + 1;
            $total_equipamentos_servico = $servico_btu['quantidade'];
            
            // Armazenar BTU do equipamento com informações do serviço
            if (!isset($_SESSION['ia_conversa']['equipamento_info'][$servico_btu['servico_id']])) {
                $_SESSION['ia_conversa']['equipamento_info'][$servico_btu['servico_id']] = [];
            }
            
            $_SESSION['ia_conversa']['equipamento_info'][$servico_btu['servico_id']][] = [
                'numero' => $equipamento_num,
                'servico_nome' => $servico_btu['servico_nome'],
                'btu' => $resposta
            ];
            
            // Atualizar contadores
            $_SESSION['ia_conversa']['servico_btu_atual']['equipamentos_coletados'] = $equipamento_num;
            $_SESSION['ia_conversa']['servico_btu_atual']['equipamentos_restantes'] = $total_equipamentos_servico - $equipamento_num;
            
            registrarLog('btu_adicionado', "BTU adicionado para equipamento", [
                'servico_nome' => $servico_btu['servico_nome'],
                'servico_id' => $servico_btu['servico_id'],
                'equipamento_num' => $equipamento_num,
                'total_equipamentos_servico' => $total_equipamentos_servico,
                'btu' => $resposta
            ]);
            
            // Verificar se já coletou todos os equipamentos deste serviço
            if ($equipamento_num >= $total_equipamentos_servico) {
                // Verificar se há mais serviços que precisam de BTUs
                avancarParaProximoServicoBTU();
            } else {
                // Continuar coletando BTUs para este serviço
                $proximo_num = $equipamento_num + 1;
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "✅ Ótimo! BTU {$resposta} anotado para o equipamento {$equipamento_num}.\n\nEquipamento {$proximo_num} do serviço: {$servico_btu['servico_nome']}\n\nQuantos BTUs tem este equipamento?\n\n*Dica: 7.000, 9.000, 12.000, 18.000...*",
                    'tipo' => 'pergunta_texto',
                    'acao' => 'btu_equipamento'
                ];
            }
            break;
            
        case 'endereco':
            $dados['endereco_rua'] = $_POST['rua'] ?? '';
            $dados['endereco_numero'] = $_POST['numero'] ?? '';
            $dados['endereco_bairro'] = $_POST['bairro'] ?? '';
            $dados['endereco_cidade'] = $_POST['cidade'] ?? '';
            
            if (empty($dados['endereco_rua']) || empty($dados['endereco_cidade'])) {
                registrarLog('conversa_erro', "Endereço incompleto", [
                    'etapa' => 'endereco',
                    'rua' => $dados['endereco_rua'],
                    'cidade' => $dados['endereco_cidade']
                ]);
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "❌ Ops, faltou alguma coisa!\n\nPreciso pelo menos da rua e cidade. Pode completar?",
                    'tipo' => 'pergunta_endereco'
                ];
                break;
            }
            
            $endereco_completo = "{$dados['endereco_rua']}, {$dados['endereco_numero']} - {$dados['endereco_bairro']}, {$dados['endereco_cidade']}";
            
            registrarLog('endereco_recebido', "Endereço completo recebido", [
                'etapa' => 'endereco',
                'endereco' => $endereco_completo
            ]);
            
            // Gerar datas disponíveis
            gerarDatasSimples();
            break;
            
        case 'selecionar_data':
            $dados['data_agendamento'] = $resposta;
            $data_obj = DateTime::createFromFormat('Y-m-d', $resposta);
            $dados['data_formatada'] = $data_obj->format('d/m/Y');
            
            registrarLog('data_selecionada', "Data selecionada para agendamento", [
                'data' => $resposta,
                'data_formatada' => $dados['data_formatada']
            ]);
            
            // Gerar horários DISPONÍVEIS
            gerarHorariosSimples($resposta);
            break;
            
        case 'selecionar_horario':
            $dados['hora_agendamento'] = $resposta;
            
            registrarLog('horario_selecionado', "Horário selecionado para agendamento", [
                'data' => $dados['data_agendamento'],
                'horario' => $resposta
            ]);
            
            // Mostrar resumo
            mostrarResumoSIMPLES();
            break;
            
        case 'confirmar_resumo':
            if ($resposta == 'sim') {
                salvarAgendamento();
            } else {
                $opcoes = [
                    ['texto' => '📝 Nome ou telefone', 'acao' => 'corrigir_item', 'valor' => 'dados_pessoais'],
                    ['texto' => '🔧 Serviços ou BTUs', 'acao' => 'corrigir_item', 'valor' => 'servicos'],
                    ['texto' => '🏠 Endereço', 'acao' => 'corrigir_item', 'valor' => 'endereco'],
                    ['texto' => '📅 Data ou horário', 'acao' => 'corrigir_item', 'valor' => 'agendamento']
                ];
                
                $_SESSION['ia_conversa']['ultima_pergunta'] = [
                    'texto' => "Sem problemas! 😊\n\nO que você gostaria de ajustar?",
                    'tipo' => 'pergunta_botoes',
                    'opcoes' => $opcoes
                ];
            }
            break;
            
        // Função para corrigir itens específicos
        case 'corrigir_item':
            switch ($resposta) {
                case 'dados_pessoais':
                    // Voltar para etapa de nome
                    unset($dados['nome']);
                    unset($dados['telefone']);
                    unset($dados['telefone_formatado']);
                    unset($dados['cliente_id']);
                    unset($dados['desconto_fidelidade']);
                    
                    $_SESSION['ia_conversa']['ultima_pergunta'] = [
                        'texto' => "Vamos corrigir seus dados pessoais.\n\nQual o seu nome completo?",
                        'tipo' => 'pergunta_texto',
                        'acao' => 'nome'
                    ];
                    break;
                    
                case 'servicos':
                    // Voltar para seleção de serviços
                    $_SESSION['ia_conversa']['servicos_selecionados'] = [];
                    $_SESSION['ia_conversa']['total_equipamentos'] = 0;
                    $_SESSION['ia_conversa']['total_servicos_valor'] = 0;
                    $_SESSION['ia_conversa']['equipamento_info'] = [];
                    
                    $opcoes = [];
                    foreach ($_SESSION['ia_conversa']['servicos_disponiveis'] as $servico) {
                        $opcoes[] = [
                            'texto' => $servico['nome'],
                            'acao' => 'selecionar_servico',
                            'valor' => $servico['nome']
                        ];
                    }
                    
                    $primeiro_nome = explode(' ', $dados['nome'])[0];
                    
                    $_SESSION['ia_conversa']['ultima_pergunta'] = [
                        'texto' => "Vamos corrigir os serviços.\n\n$primeiro_nome, qual serviço você precisa hoje?",
                        'tipo' => 'pergunta_botoes',
                        'opcoes' => $opcoes
                    ];
                    break;
                    
                case 'endereco':
                    // Voltar para etapa de endereço
                    unset($dados['endereco_rua']);
                    unset($dados['endereco_numero']);
                    unset($dados['endereco_bairro']);
                    unset($dados['endereco_cidade']);
                    
                    $_SESSION['ia_conversa']['ultima_pergunta'] = [
                        'texto' => "Vamos corrigir o endereço.\n\nPor favor, preencha seu endereço abaixo:",
                        'tipo' => 'pergunta_endereco'
                    ];
                    break;
                    
                case 'agendamento':
                    // Voltar para seleção de data
                    unset($dados['data_agendamento']);
                    unset($dados['data_formatada']);
                    unset($dados['hora_agendamento']);
                    
                    gerarDatasSimples();
                    break;
            }
            break;
            
        case 'voltar_para_datas':
            gerarDatasSimples();
            break;
            
        case 'fechar_popup':
            $_SESSION['ia_conversa']['mostrar_popup_desconto'] = false;
            registrarLog('popup_fechado', "Popup de desconto fechado pelo usuário");
            break;
            
        // Caso para pular coleta de BTUs se não houver equipamentos
        case 'pular_btus':
            $_SESSION['ia_conversa']['ultima_pergunta'] = [
                'texto' => "✅ Perfeito! Sem equipamentos específicos.\n\nVamos para o endereço então...\n\nPode preencher seu endereço abaixo:",
                'tipo' => 'pergunta_endereco'
            ];
            break;
    }
}

// ✅ FUNÇÃO: Iniciar coleta de BTUs
function iniciarColetaBTUs() {
    $total_equipamentos = $_SESSION['ia_conversa']['total_equipamentos'];
    
    if ($total_equipamentos == 0) {
        // Se não há equipamentos, perguntar se quer pular BTUs
        $opcoes = [
            ['texto' => '✅ Sim, avançar para endereço', 'acao' => 'pular_btus', 'valor' => 'sim'],
            ['texto' => '❌ Não, preciso adicionar equipamentos', 'acao' => 'corrigir_item', 'valor' => 'servicos']
        ];
        
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "📝 Você não especificou nenhum equipamento.\n\nDeseja avançar para o endereço ou voltar para adicionar equipamentos?",
            'tipo' => 'pergunta_botoes',
            'opcoes' => $opcoes
        ];
        return;
    }
    
    // Encontrar o primeiro serviço que precisa de BTUs
    $servicos_com_equipamentos = array_filter($_SESSION['ia_conversa']['servicos_selecionados'], function($s) {
        return $s['quantidade'] > 0;
    });
    
    if (empty($servicos_com_equipamentos)) {
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "✅ Perfeito! Sem equipamentos específicos.\n\nVamos para o endereço então...\n\nPode preencher seu endereço abaixo:",
            'tipo' => 'pergunta_endereco'
        ];
        return;
    }
    
    // Pegar o primeiro serviço com equipamentos
    $primeiro_servico = reset($servicos_com_equipamentos);
    
    // Configurar serviço atual para coleta de BTUs
    $_SESSION['ia_conversa']['servico_btu_atual'] = [
        'servico_id' => $primeiro_servico['id'],
        'servico_nome' => $primeiro_servico['nome'],
        'quantidade' => $primeiro_servico['quantidade'],
        'equipamentos_restantes' => $primeiro_servico['quantidade'],
        'equipamentos_coletados' => 0
    ];
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => "🔧 Vamos coletar informações dos equipamentos!\n\nServiço: {$primeiro_servico['nome']}\n\nEquipamento 1 de {$primeiro_servico['quantidade']}:\n\nQuantos BTUs tem este equipamento?\n\n*Dica: 7.000, 9.000, 12.000, 18.000...*",
        'tipo' => 'pergunta_texto',
        'acao' => 'btu_equipamento'
    ];
}

// ✅ FUNÇÃO: Avançar para próximo serviço que precisa de BTUs
function avancarParaProximoServicoBTU() {
    $servicos_selecionados = $_SESSION['ia_conversa']['servicos_selecionados'];
    $servico_atual_id = $_SESSION['ia_conversa']['servico_btu_atual']['servico_id'];
    
    // Encontrar serviços que ainda não tiveram BTUs coletados
    $servicos_pendentes = [];
    
    foreach ($servicos_selecionados as $servico) {
        if ($servico['quantidade'] > 0) {
            // Verificar se este serviço já teve BTUs coletados
            $btus_coletados = isset($_SESSION['ia_conversa']['equipamento_info'][$servico['id']]) 
                ? count($_SESSION['ia_conversa']['equipamento_info'][$servico['id']]) 
                : 0;
            
            if ($btus_coletados < $servico['quantidade']) {
                $servicos_pendentes[] = [
                    'servico' => $servico,
                    'btus_coletados' => $btus_coletados,
                    'restantes' => $servico['quantidade'] - $btus_coletados
                ];
            }
        }
    }
    
    if (empty($servicos_pendentes)) {
        // Todos os BTUs foram coletados
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "✅ Ótimo! Anotei todos os BTUs de todos os equipamentos.\n\nAgora preciso do seu endereço para calcularmos a rota e o deslocamento.",
            'tipo' => 'pergunta_endereco'
        ];
        return;
    }
    
    // Pegar o próximo serviço pendente
    $proximo_servico = reset($servicos_pendentes);
    $servico_info = $proximo_servico['servico'];
    
    // Configurar serviço atual para coleta de BTUs
    $_SESSION['ia_conversa']['servico_btu_atual'] = [
        'servico_id' => $servico_info['id'],
        'servico_nome' => $servico_info['nome'],
        'quantidade' => $servico_info['quantidade'],
        'equipamentos_restantes' => $proximo_servico['restantes'],
        'equipamentos_coletados' => $proximo_servico['btus_coletados']
    ];
    
    $equipamento_num = $proximo_servico['btus_coletados'] + 1;
    $total_equipamentos_servico = $servico_info['quantidade'];
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => "🔧 Próximo serviço: {$servico_info['nome']}\n\nEquipamento {$equipamento_num} de {$total_equipamentos_servico}:\n\nQuantos BTUs tem este equipamento?\n\n*Dica: 7.000, 9.000, 12.000, 18.000...*",
        'tipo' => 'pergunta_texto',
        'acao' => 'btu_equipamento'
    ];
}

// ✅ FUNÇÃO: Gerar apenas DATAS DISPONÍVEIS
function gerarDatasSimples() {
    global $pdo;
    
    $datas_opcoes = [];
    $data_atual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    
    // Obter configurações para limite diário
    $config = getConfigAgendamento($pdo);
    $limite_diario = $config['limite_agendamentos_dia'] ?? 8;
    
    for ($i = 1; $i <= 14; $i++) {
        $data = clone $data_atual;
        $data->modify("+{$i} days");
        
        $data_ymd = $data->format('Y-m-d');
        $data_br = $data->format('d/m/Y');
        
        // Verificar disponibilidade da data
        $disponivel = true;
        $mensagem = "";
        
        try {
            // Verificar quantidade de agendamentos nesta data
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total_dia 
                FROM agendamentos 
                WHERE data_agendamento = ?
                AND status NOT IN ('cancelado', 'rejeitado')
            ");
            $stmt->execute([$data_ymd]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total_dia'] >= $limite_diario) {
                $disponivel = false;
                $mensagem = " (Lotado)";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar disponibilidade data: " . $e->getMessage());
            registrarLog('erro_banco', "Falha ao verificar disponibilidade data: " . $e->getMessage(), [
                'data' => $data_ymd
            ]);
        }
        
        $texto = "{$data_br}{$mensagem}";
        
        if (!$disponivel) {
            $texto .= " ❌";
        }
        
        $datas_opcoes[] = [
            'texto' => $texto,
            'acao' => $disponivel ? 'selecionar_data' : '',
            'valor' => $disponivel ? $data_ymd : '',
            'disabled' => !$disponivel
        ];
    }
    
    // Filtrar apenas datas disponíveis
    $datas_disponiveis = array_filter($datas_opcoes, function($data) {
        return !$data['disabled'];
    });
    
    if (empty($datas_disponiveis)) {
        registrarLog('datas_indisponiveis', "Nenhuma data disponível nos próximos 14 dias", [
            'total_datas' => count($datas_opcoes),
            'datas_disponiveis' => 0
        ]);
        
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "❌ Nenhuma data disponível nos próximos 14 dias!\n\n" .
                     "Todas as datas estão com agendamentos completos.\n" .
                     "Por favor, entre em contato conosco pelo WhatsApp para verificar disponibilidade.",
            'tipo' => 'erro_agendamento'
        ];
        return;
    }
    
    registrarLog('datas_geradas', "Datas disponíveis geradas", [
        'total_disponiveis' => count($datas_disponiveis),
        'total_verificadas' => count($datas_opcoes)
    ]);
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => "Escolha uma data DISPONÍVEL:\n\n" .
                 "*Mostrando apenas datas com horários livres*",
        'tipo' => 'pergunta_botoes',
        'opcoes' => $datas_disponiveis
    ];
}

// ✅ FUNÇÃO: Gerar apenas HORÁRIOS DISPONÍVEIS
function gerarHorariosSimples($data) {
    global $pdo;
    
    // Obter horários ocupados para esta data
    $horarios_ocupados = getHorariosOcupados($pdo, $data);
    
    // Obter configurações do sistema
    $config = getConfigAgendamento($pdo);
    $limite_diario = $config['limite_agendamentos_dia'] ?? 8;
    
    // Verificar quantidade de agendamentos do dia
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_dia 
        FROM agendamentos 
        WHERE data_agendamento = ?
        AND status NOT IN ('cancelado', 'rejeitado')
    ");
    $stmt->execute([$data]);
    $resultado_dia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $dia_cheio = ($resultado_dia['total_dia'] >= $limite_diario);
    
    // Horários padrão (8:00 às 19:00)
    $horarios_opcoes = [];
    for ($h = 8; $h <= 19; $h++) {
        $hora = sprintf('%02d:00', $h);
        
        // Verificar se horário está ocupado ou dia está cheio
        $ocupado = isset($horarios_ocupados[$hora]) || $dia_cheio;
        
        $texto = "{$hora}";
        
        if ($ocupado) {
            $texto .= " ❌ (Indisponível)";
        }
        
        $horarios_opcoes[] = [
            'texto' => $texto,
            'acao' => $ocupado ? '' : 'selecionar_horario',
            'valor' => $ocupado ? '' : $hora,
            'disabled' => $ocupado
        ];
    }
    
    // Filtrar apenas horários disponíveis
    $horarios_disponiveis = array_filter($horarios_opcoes, function($h) {
        return !$h['disabled'];
    });
    
    $data_obj = DateTime::createFromFormat('Y-m-d', $data);
    $data_formatada = $data_obj->format('d/m/Y');
    
    $mensagem = "📅 Data: {$data_formatada}\n\n";
    
    if ($dia_cheio) {
        $mensagem .= "❌ DATA LOTADA: Esta data já atingiu o limite de {$limite_diario} agendamentos.\n";
        $mensagem .= "Por favor, escolha outra data.\n\n";
        
        registrarLog('data_lotada', "Data completamente lotada", [
            'data' => $data,
            'limite_diario' => $limite_diario,
            'agendamentos_hoje' => $resultado_dia['total_dia']
        ]);
    } elseif (empty($horarios_disponiveis)) {
        $mensagem .= "❌ NENHUM HORÁRIO DISPONÍVEL para esta data.\n";
        $mensagem .= "Todos os horários estão ocupados.\n\n";
        $mensagem .= "Escolha outra data:\n";
        
        registrarLog('horarios_indisponiveis', "Todos horários indisponíveis para data", [
            'data' => $data,
            'total_horarios' => count($horarios_opcoes),
            'horarios_disponiveis' => 0,
            'horarios_ocupados' => count($horarios_ocupados)
        ]);
        
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => $mensagem,
            'tipo' => 'pergunta_botoes',
            'opcoes' => [
                ['texto' => '📅 Escolher outra data', 'acao' => 'voltar_para_datas', 'valor' => 'voltar']
            ]
        ];
        return;
    } else {
        $mensagem .= "✅ Horários DISPONÍVEIS:\n";
        
        registrarLog('horarios_gerados', "Horários disponíveis gerados", [
            'data' => $data,
            'total_disponiveis' => count($horarios_disponiveis),
            'total_verificados' => count($horarios_opcoes)
        ]);
    }
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => $mensagem,
        'tipo' => 'pergunta_botoes',
        'opcoes' => $horarios_disponiveis
    ];
}

// ✅ FUNÇÃO: Calcular valor final com apenas desconto de fidelidade
function calcularValorFinalComDesconto($total_valor, $desconto_percentual = 0) {
    $resultado = [
        'valor_final' => $total_valor,
        'detalhes' => '',
        'tem_desconto' => false,
        'desconto_valor' => 0
    ];
    
    // Calcular desconto se houver
    if ($desconto_percentual > 0) {
        $desconto_valor = $total_valor * ($desconto_percentual / 100);
        $valor_final = $total_valor - $desconto_valor;
        
        $resultado['valor_final'] = $valor_final;
        $resultado['tem_desconto'] = true;
        $resultado['desconto_valor'] = $desconto_valor;
        $resultado['detalhes'] = "🎉 DESCONTO DE 5%: R$ -" . number_format($desconto_valor, 2, ',', '.');
    }
    
    return $resultado;
}

// ✅ FUNÇÃO: Mostrar resumo simples
function mostrarResumoSIMPLES() {
    $dados = $_SESSION['ia_conversa']['dados'];
    $servicos_selecionados = $_SESSION['ia_conversa']['servicos_selecionados'];
    $equipamento_info = $_SESSION['ia_conversa']['equipamento_info'] ?? [];
    $total_valor = $_SESSION['ia_conversa']['total_servicos_valor'];
    
    // Calcular desconto de fidelidade
    $desconto_percentual = $dados['desconto_fidelidade'] ?? 0;
    $calculo = calcularValorFinalComDesconto($total_valor, $desconto_percentual);
    
    $resumo = "📋 RESUMO DO SEU AGENDAMENTO\n\n";
    $resumo .= "👤 CLIENTE\n";
    $resumo .= "• Nome: " . $dados['nome'] . "\n";
    $resumo .= "• WhatsApp: " . ($dados['telefone_formatado'] ?? $dados['telefone']) . "\n\n";
    
    $resumo .= "🏠 ENDEREÇO\n";
    $resumo .= "• " . ($dados['endereco_rua'] ?? '') . ", " . 
               ($dados['endereco_numero'] ?? '') . " - " . 
               ($dados['endereco_bairro'] ?? '') . ", " . 
               ($dados['endereco_cidade'] ?? '') . "\n\n";
    
    $resumo .= "🔧 SERVIÇOS SOLICITADOS\n";
    foreach ($servicos_selecionados as $servico) {
        $valor_servico = number_format($servico['subtotal'], 2, ',', '.');
        $resumo .= "• " . $servico['nome'] . " (x" . $servico['quantidade'] . ") \n";
        
        // Mostrar BTUs se houver para este serviço
        if (isset($equipamento_info[$servico['id']]) && !empty($equipamento_info[$servico['id']])) {
            foreach ($equipamento_info[$servico['id']] as $equipamento) {
                $resumo .= "  └─ Equipamento {$equipamento['numero']}: {$equipamento['btu']} BTUs\n";
            }
        }
    }
    
    $resumo .= "\n📅 AGENDAMENTO\n";
    $resumo .= "• Data: " . $dados['data_formatada'] . "\n";
    $resumo .= "• Horário: " . $dados['hora_agendamento'] . "\n\n";
    
    // Mostrar desconto se houver
    if ($calculo['tem_desconto']) {
        $resumo .= "🎉 BÔNUS DE FIDELIDADE (5% DE DESCONTO)\n";
        $resumo .= $calculo['detalhes'] . "\n\n";
        
        $valor_final_formatado = number_format($calculo['valor_final'], 2, ',', '.');
        $resumo .= "💰 VALOR TOTAL COM DESCONTO: R$ {$valor_final_formatado}\n\n";
    } else {
        $valor_total_formatado = number_format($total_valor, 2, ',', '.');
        //$resumo .= "💰 VALOR TOTAL: R$ {$valor_total_formatado}\n\n";
    }
    
    $resumo .= "Tudo certo com essas informações?";
    
    $opcoes = [
        ['texto' => '✅ Sim, está tudo correto', 'acao' => 'confirmar_resumo', 'valor' => 'sim'],
        ['texto' => '❌ Não, preciso ajustar algo', 'acao' => 'confirmar_resumo', 'valor' => 'nao']
    ];
    
    registrarLog('resumo_mostrado', "Resumo do agendamento mostrado ao usuário", [
        'cliente' => $dados['nome'],
        'total_servicos' => count($servicos_selecionados),
        'valor_total' => $total_valor,
        'tem_desconto' => $calculo['tem_desconto']
    ]);
    
    $_SESSION['ia_conversa']['ultima_pergunta'] = [
        'texto' => $resumo,
        'tipo' => 'pergunta_botoes',
        'opcoes' => $opcoes
    ];
}

// ✅ FUNÇÃO: Salvar agendamento
function salvarAgendamento() {
    global $pdo;
    
    $dados = $_SESSION['ia_conversa']['dados'];
    $servicos_selecionados = $_SESSION['ia_conversa']['servicos_selecionados'];
    $equipamento_info = $_SESSION['ia_conversa']['equipamento_info'] ?? [];
    $total_valor = $_SESSION['ia_conversa']['total_servicos_valor'];
    
    registrarLog('salvamento_inicio', "Iniciando salvamento do agendamento", [
        'cliente' => $dados['nome'],
        'data' => $dados['data_agendamento'],
        'hora' => $dados['hora_agendamento'],
        'total_servicos' => count($servicos_selecionados)
    ]);
    
    // ✅ VERIFICAÇÃO DE DISPONIBILIDADE ANTES DE SALVAR
    $disponibilidade = verificarDisponibilidadeIA($pdo, $dados['data_agendamento'], $dados['hora_agendamento']);
    
    if (!$disponibilidade['disponivel']) {
        registrarLog('salvamento_bloqueado', "Agendamento bloqueado - horário indisponível", [
            'motivo' => $disponibilidade['motivo'],
            'cliente' => $dados['nome'],
            'data' => $dados['data_agendamento'],
            'hora' => $dados['hora_agendamento']
        ]);
        
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "❌ Horário indisponível!\n\n" .
                     "Desculpe, o horário selecionado não está mais disponível.\n" .
                     "Motivo: {$disponibilidade['motivo']}\n\n" .
                     "Por favor, escolha outra data/horário.",
            'tipo' => 'erro_agendamento'
        ];
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // 1. Verificar/Inserir cliente
        if (isset($dados['cliente_id'])) {
            $cliente_id = $dados['cliente_id'];
            $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, rua = ?, numero = ?, bairro = ?, cidade = ? WHERE id = ?");
            $stmt->execute([
                $dados['nome'],
                $dados['endereco_rua'] ?? '',
                $dados['endereco_numero'] ?? '',
                $dados['endereco_bairro'] ?? '',
                $dados['endereco_cidade'] ?? '',
                $cliente_id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone, rua, numero, bairro, cidade) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $dados['nome'],
                $dados['telefone'],
                $dados['endereco_rua'] ?? '',
                $dados['endereco_numero'] ?? '',
                $dados['endereco_bairro'] ?? '',
                $dados['endereco_cidade'] ?? ''
            ]);
            $cliente_id = $pdo->lastInsertId();
            
            registrarLog('cliente_novo', "Novo cliente criado", [
                'cliente_id' => $cliente_id,
                'nome' => $dados['nome'],
                'telefone' => $dados['telefone']
            ]);
        }
        
        // 2. Calcular valor com desconto
        $desconto_percentual = $dados['desconto_fidelidade'] ?? 0;
        $calculo = calcularValorFinalComDesconto($total_valor, $desconto_percentual);
        
        $valor_total = $calculo['valor_final'];
        
        // 3. Criar orçamento
        $descricao = "=== SERVIÇOS SOLICITADOS ===\n";
        foreach ($servicos_selecionados as $servico) {
            $subtotal = number_format($servico['subtotal'], 2, ',', '.');
            $descricao .= "• " . $servico['nome'] . " (x" . $servico['quantidade'] . ") - R$ {$subtotal}\n";
            
            // Adicionar informações de BTUs se houver
            if (isset($equipamento_info[$servico['id']]) && !empty($equipamento_info[$servico['id']])) {
                $descricao .= "  Detalhes dos equipamentos:\n";
                foreach ($equipamento_info[$servico['id']] as $equipamento) {
                    $descricao .= "  - Equipamento {$equipamento['numero']}: {$equipamento['btu']} BTUs\n";
                }
            }
        }
        
        $descricao .= "\n=== AGENDAMENTO ===\n";
        $descricao .= "Data: " . $dados['data_formatada'] . "\n";
        $descricao .= "Horário: " . $dados['hora_agendamento'] . "\n";
        $descricao .= "Cliente: " . $dados['nome'] . "\n";
        $descricao .= "WhatsApp: " . ($dados['telefone_formatado'] ?? $dados['telefone']) . "\n\n";
        
        if ($calculo['tem_desconto']) {
            $descricao .= "=== DESCONTO APLICADO ===\n";
            $desconto_formatado = number_format($calculo['desconto_valor'], 2, ',', '.');
            $descricao .= "• Desconto de fidelidade: -5% (R$ -{$desconto_formatado})\n";
        }
        
        $descricao .= "\n=== VALORES ===\n";
        $valor_servicos_formatado = number_format($total_valor, 2, ',', '.');
        $valor_total_formatado = number_format($valor_total, 2, ',', '.');
        
        $descricao .= "Total serviços: R$ {$valor_servicos_formatado}\n";
        if ($calculo['tem_desconto']) {
            $descricao .= "Desconto: R$ -" . number_format($calculo['desconto_valor'], 2, ',', '.') . "\n";
        }
        $descricao .= "Valor final: R$ {$valor_total_formatado}\n";
        
        $stmt = $pdo->prepare("INSERT INTO orcamentos (cliente_id, descricao, valor_total, status, data_solicitacao) VALUES (?, ?, ?, 'pendente', NOW())");
        $stmt->execute([$cliente_id, $descricao, $valor_total]);
        $orcamento_id = $pdo->lastInsertId();
        
        registrarLog('orcamento_criado', "Orçamento criado", [
            'orcamento_id' => $orcamento_id,
            'cliente_id' => $cliente_id,
            'valor_total' => $valor_total
        ]);
        
        // 4. Criar agendamento
        $primeiro_servico_id = $servicos_selecionados[0]['id'];
        $endereco_completo = ($dados['endereco_rua'] ?? '') . ', ' . 
                            ($dados['endereco_numero'] ?? '') . ' - ' . 
                            ($dados['endereco_bairro'] ?? '') . ', ' . 
                            ($dados['endereco_cidade'] ?? '');
        
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, servico_id, data_agendamento, hora_agendamento, endereco, observacoes, status, origem, origem_id, data_criacao) VALUES (?, ?, ?, ?, ?, ?, 'agendado', 'sistema_ia', ?, NOW())");
        $stmt->execute([
            $cliente_id,
            $primeiro_servico_id,
            $dados['data_agendamento'],
            $dados['hora_agendamento'],
            $endereco_completo,
            $descricao,
            $orcamento_id
        ]);
        $agendamento_id = $pdo->lastInsertId();
        
        registrarLog('agendamento_criado', "Agendamento criado com sucesso", [
            'agendamento_id' => $agendamento_id,
            'orcamento_id' => $orcamento_id,
            'cliente_id' => $cliente_id,
            'data' => $dados['data_agendamento'],
            'hora' => $dados['hora_agendamento'],
            'valor_total' => $valor_total
        ]);
        
        $pdo->commit();
        
        // 5. Gerar link WhatsApp
        $config_agendamento = getConfigAgendamento($pdo);
        $whatsapp_empresa = $config_agendamento['whatsapp_empresa'] ?? '5517996240725';
        
        $mensagem_whatsapp = "📋 *NOVO AGENDAMENTO - N&M REFRIGERAÇÃO*\n\n";
        $mensagem_whatsapp .= "👤 *Cliente:* " . $dados['nome'] . "\n";
        $mensagem_whatsapp .= "📞 *WhatsApp:* " . ($dados['telefone_formatado'] ?? $dados['telefone']) . "\n";
        $mensagem_whatsapp .= "📍 *Endereço:* " . $endereco_completo . "\n\n";
        
        $mensagem_whatsapp .= "🔧 *SERVIÇOS:*\n";
        foreach ($servicos_selecionados as $servico) {
            $subtotal = number_format($servico['subtotal'], 2, ',', '.');
            $mensagem_whatsapp .= "• " . $servico['nome'] . " (x" . $servico['quantidade'] . ") - R$ {$subtotal}\n";
            
            // Adicionar BTUs no WhatsApp se houver
            if (isset($equipamento_info[$servico['id']]) && !empty($equipamento_info[$servico['id']])) {
                foreach ($equipamento_info[$servico['id']] as $equipamento) {
                    $mensagem_whatsapp .= "  └─ Equipamento {$equipamento['numero']}: {$equipamento['btu']} BTUs\n";
                }
            }
        }
        
        $mensagem_whatsapp .= "\n📅 *AGENDAMENTO:*\n";
        $mensagem_whatsapp .= "• Data: " . $dados['data_formatada'] . "\n";
        $mensagem_whatsapp .= "• Horário: " . $dados['hora_agendamento'] . "\n\n";
        
        if ($calculo['tem_desconto']) {
            $mensagem_whatsapp .= "🎉 *DESCONTO DE FIDELIDADE:*\n";
            $desconto_formatado = number_format($calculo['desconto_valor'], 2, ',', '.');
            $mensagem_whatsapp .= "• -5%: R$ -{$desconto_formatado}\n";
        }
        
        $valor_total_formatado = number_format($valor_total, 2, ',', '.');
        $mensagem_whatsapp .= "💰 *VALOR FINAL TOTAL:* R$ {$valor_total_formatado}\n\n";
        
        $mensagem_whatsapp .= "⚡ *ID Agendamento:* #" . $agendamento_id . "\n";
        $mensagem_whatsapp .= "💼 *ID Orçamento:* #" . $orcamento_id . "\n\n";
        $mensagem_whatsapp .= "🏷️ *Solicitado via Sistema IA*\n";
        $mensagem_whatsapp .= "🤖 *Assistente: Laura*";
        
        $link_whatsapp = "https://wa.me/{$whatsapp_empresa}?text=" . urlencode($mensagem_whatsapp);
        
        // Registrar log de sucesso
        registrarLog('agendamento_concluido', "Agendamento concluído com sucesso ", [
            'agendamento_id' => $agendamento_id,
            'cliente' => $dados['nome'],
            'telefone' => $dados['telefone'],
            'data' => $dados['data_agendamento'],
            'hora' => $dados['hora_agendamento'],
            'valor_total' => $valor_total,
            'tempo_conversa' => time() - strtotime($_SESSION['ia_conversa']['inicio_conversa'])
        ]);
        
        // Mostrar mensagem final
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "🎉 AGENDAMENTO CONCLUÍDO COM SUCESSO! 🎉\n\n" .
                     "✅ Seu agendamento foi registrado!\n" .
                     "📋 *ID Agendamento:* #$agendamento_id\n" .
                     "💰 *ID Orçamento:* #$orcamento_id\n" .
                     //"💰 *Valor total: R$ {$valor_total_formatado}*\n" .
                     "📱 Clique abaixo para enviar para nosso WhatsApp:",
            'tipo' => 'final',
            'link_whatsapp' => $link_whatsapp,
            'valor_total' => $valor_total_formatado
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        
        registrarLog('agendamento_erro', "Erro ao salvar agendamento: " . $e->getMessage(), [
            'cliente' => $dados['nome'],
            'telefone' => $dados['telefone'],
            'erro' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $_SESSION['ia_conversa']['ultima_pergunta'] = [
            'texto' => "❌ Ops, aconteceu um problema!\n\n" .
                     "Não consegui salvar seu agendamento. 😔\n" .
                     "Pode tentar novamente?\n\n" .
                     "Se o problema persistir, entre em contato diretamente pelo WhatsApp.",
            'tipo' => 'erro'
        ];
    }
}

// ============================================================================
// CRIAÇÃO DAS TABELAS DE SUPORTE
// ============================================================================

function criarTabelasSuporte($pdo) {
    try {
        // Tabela de logs (já criada anteriormente)
        
        return true;
    } catch (Exception $e) {
        error_log("Erro ao criar tabelas de suporte: " . $e->getMessage());
        return false;
    }
}

// Executar criação de tabelas
criarTabelasSuporte($pdo);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - N&M Refrigeração</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ============================================================================
           ESTILOS MODERNOS PARA SISTEMA IA
           Design atraente e moderno para empresa de ar condicionado
        ============================================================================ */
        
        /* VARIÁVEIS ADICIONAIS */
        :root {
            --ai-primary: var(--primary);
            --ai-secondary: var(--secondary);
            --ai-accent: #ff6b00;
            --ai-light: #f8fafc;
            --ai-dark: #1e293b;
            --ai-success: #10b981;
            --ai-warning: #f59e0b;
            --ai-error: #ef4444;
            --ai-border: #e2e8f0;
            --ai-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --ai-gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        /* CONTAINER PRINCIPAL */
        .ia-container-modern {
            min-height: 100vh;
            background: linear-gradient(135deg, 
                rgba(0, 102, 204, 0.03) 0%,
                rgba(0, 168, 255, 0.01) 100%);
            padding: 40px 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .ia-content-modern {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--ai-shadow);
            border: 1px solid var(--ai-border);
        }
        
        /* HEADER MODERNO */
        .ia-header-modern {
            background: var(--ai-gradient);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .ia-header-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.1)" d="M0,0 L100,0 L100,100 Z"/></svg>');
            background-size: cover;
        }
        
        .ia-header-content {
            position: relative;
            z-index: 2;
        }
        
        .ia-avatar {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: var(--ai-primary);
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .ia-header-modern h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        
        .ia-header-modern p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .ia-action-buttons-modern {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .ia-btn-modern {
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .ia-new-chat-modern {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .ia-new-chat-modern:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .ia-go-home-modern {
            background: white;
            color: var(--ai-primary);
        }
        
        .ia-go-home-modern:hover {
            background: var(--ai-light);
            transform: translateY(-2px);
        }
        
        /* CONTEÚDO DA CONVERSA */
        .ia-conversation-modern {
            padding: 40px;
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }
        
        /* MENSAGEM DO ASSISTENTE */
        .ia-message-modern {
            max-width: 80%;
            margin-bottom: 30px;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ia-message-content {
            background: var(--ai-light);
            border-radius: 20px 20px 20px 5px;
            padding: 25px;
            position: relative;
            border: 1px solid var(--ai-border);
        }
        
        .ia-message-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: -10px;
            width: 20px;
            height: 20px;
            background: var(--ai-light);
            border-left: 1px solid var(--ai-border);
            border-bottom: 1px solid var(--ai-border);
            transform: rotate(45deg);
        }
        
        .ia-message-content p {
            color: var(--ai-dark);
            line-height: 1.7;
            font-size: 1.1rem;
            margin-bottom: 0;
            white-space: pre-line;
        }
        
        .ia-message-content strong {
            color: var(--ai-primary);
            font-weight: 700;
        }
        
        /* FORMULÁRIOS E INPUTS */
        .ia-response-form-modern {
            margin-top: auto;
            padding-top: 30px;
            border-top: 2px dashed var(--ai-border);
        }
        
        /* INPUT DE TEXTO */
        .ia-text-input-modern {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid var(--ai-border);
            border-radius: 16px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: white;
            color: var(--ai-dark);
        }
        
        .ia-text-input-modern:focus {
            outline: none;
            border-color: var(--ai-primary);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
        }
        
        .ia-text-input-modern::placeholder {
            color: #94a3b8;
        }
        
        /* BOTÕES DE OPÇÃO */
        .ia-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .ia-btn-opcao-modern {
            width: 100%;
            padding: 20px;
            background: white;
            border: 2px solid var(--ai-border);
            border-radius: 16px;
            color: var(--ai-dark);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-align: left;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .ia-btn-opcao-modern:hover:not(:disabled) {
            background: var(--ai-light);
            border-color: var(--ai-primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .ia-btn-opcao-modern:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }
        
        .ia-btn-opcao-modern:active:not(:disabled) {
            transform: translateY(0);
        }
        
        /* BOTÃO DE ENVIO */
        .ia-submit-btn-modern {
            width: 100%;
            padding: 20px;
            background: var(--ai-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 15px;
        }
        
        .ia-submit-btn-modern:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(var(--primary-rgb), 0.3);
        }
        
        .ia-submit-btn-modern:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }
        
        /* FORMULÁRIO DE ENDEREÇO */
        .ia-address-form-modern {
            display: grid;
            gap: 15px;
        }
        
        .ia-address-form-modern input {
            padding: 16px 20px;
            border: 2px solid var(--ai-border);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .ia-address-form-modern input:focus {
            outline: none;
            border-color: var(--ai-primary);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
        }
        
        /* FINALIZAÇÃO */
        .ia-final-message {
            text-align: center;
            padding: 40px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
            border-radius: 20px;
            margin-top: 20px;
        }
        
        .ia-success-icon {
            font-size: 64px;
            color: var(--ai-success);
            margin-bottom: 20px;
            animation: bounce 1s;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        
        .ia-final-message h3 {
            font-size: 2rem;
            color: var(--ai-primary);
            margin-bottom: 15px;
            font-weight: 800;
        }
        
        .ia-final-message p {
            color: var(--ai-dark);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .ia-whatsapp-btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #25D366;
            color: white;
            padding: 20px 40px;
            border-radius: 16px;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .ia-whatsapp-btn-modern:hover {
            background: #1da851;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
        }
        
        .ia-final-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        /* POPUP DE DESCONTO MODERNO */
        .ia-popup-overlay-modern {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }
        
        .ia-popup-container-modern {
            background: white;
            border-radius: 24px;
            padding: 50px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: popIn 0.5s ease-out;
        }
        
        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .ia-popup-icon-modern {
            font-size: 64px;
            margin-bottom: 20px;
            color: var(--ai-primary);
            animation: pulse 2s infinite;
        }
        
        .ia-popup-container-modern h2 {
            font-size: 2rem;
            color: var(--ai-dark);
            margin-bottom: 15px;
            font-weight: 800;
        }
        
        .ia-popup-container-modern p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .ia-popup-button-modern {
            width: 100%;
            padding: 20px;
            background: var(--ai-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .ia-popup-button-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(var(--primary-rgb), 0.3);
        }
        
        /* DICAS */
        .ia-dicas-modern {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 2px solid #fed7aa;
            border-radius: 16px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .ia-dicas-modern strong {
            color: #ea580c;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .ia-dicas-modern ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .ia-dicas-modern li {
            color: #7c2d12;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        /* PROGRESS BAR */
        .ia-progress-modern {
            height: 6px;
            background: var(--ai-border);
            border-radius: 3px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .ia-progress-bar {
            height: 100%;
            background: var(--ai-gradient);
            border-radius: 3px;
            transition: width 0.3s ease;
            width: 0%;
        }
        
        /* MENSAGEM DE ERRO */
        .ia-erro-agendamento {
            text-align: center;
            padding: 40px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 20px;
            border: 2px solid #fbbf24;
            margin-top: 20px;
        }
        
        .ia-erro-agendamento .ia-success-icon {
            color: #f59e0b;
        }
        
        .ia-erro-agendamento h3 {
            color: #f59e0b;
        }
        
        /* BOTÃO INDISPONÍVEL */
        .btn-indisponivel {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .btn-indisponivel:hover {
            transform: none !important;
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            box-shadow: none !important;
        }
        
        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            .ia-content-modern {
                border-radius: 0;
                margin: 0;
                min-height: 100vh;
            }
            
            .ia-header-modern,
            .ia-conversation-modern {
                padding: 30px 20px;
            }
            
            .ia-header-modern h1 {
                font-size: 2rem;
            }
            
            .ia-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            
            .ia-options-grid {
                grid-template-columns: 1fr;
            }
            
            .ia-popup-container-modern {
                padding: 30px 20px;
            }
            
            .ia-final-message {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .ia-action-buttons-modern {
                flex-direction: column;
            }
            
            .ia-btn-modern {
                width: 100%;
                justify-content: center;
            }
            
            .ia-final-actions {
                flex-direction: column;
            }
        }
        
        /* ANIMAÇÕES ADICIONAIS */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .shake {
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Pop-up de desconto -->
    <?php if (isset($_SESSION['ia_conversa']['mostrar_popup_desconto']) && $_SESSION['ia_conversa']['mostrar_popup_desconto']): ?>
    <div class="ia-popup-overlay-modern">
        <div class="ia-popup-container-modern">
            <div class="ia-popup-icon-modern">🎁</div>
            <h2>Bem-vindo de volta!</h2>
            <p>Como você já é nosso cliente, você ganhou <strong style="color: var(--ai-primary);">5% de desconto</strong> neste agendamento! 🎉</p>
            <form method="POST">
                <input type="hidden" name="acao" value="fechar_popup">
                <button type="submit" class="ia-popup-button-modern">
                    <i class="fas fa-check-circle"></i> Continuar Agendamento
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="ia-container-modern">
        <div class="ia-content-modern">
            <div class="ia-header-modern">
                <div class="ia-header-content">
                    <div class="ia-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h1>🤖 Assistente Virtual</h1>
                    <p>Agendamento rápido e fácil com a Laura 😊</p>
                    
                    <div class="ia-action-buttons-modern">
                        <a href="sistema-ia.php?limpar=1" class="ia-btn-modern ia-new-chat-modern">
                            <i class="fas fa-sync-alt"></i> Nova Conversa
                        </a>
                        <a href="index.php" class="ia-btn-modern ia-go-home-modern">
                            <i class="fas fa-home"></i> Voltar para Home
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="ia-conversation-modern">
                <?php 
                $pergunta = $_SESSION['ia_conversa']['ultima_pergunta'];
                
                // Barra de progresso
                $etapa = $_SESSION['ia_conversa']['etapa'] ?? 1;
                $progresso = min(100, ($etapa * 15));
                ?>
                
                <div class="ia-progress-modern">
                    <div class="ia-progress-bar" style="width: <?php echo $progresso; ?>%"></div>
                </div>
                
                <div class="ia-message-modern">
                    <div class="ia-message-content">
                        <p><?php echo nl2br(htmlspecialchars($pergunta['texto'])); ?></p>
                    </div>
                </div>
                
                <div class="ia-response-form-modern">
                    <?php if ($pergunta['tipo'] == 'mensagem'): ?>
                        <form method="POST">
                            <input type="hidden" name="acao" value="iniciar">
                            <button type="submit" class="ia-submit-btn-modern">
                                <i class="fas fa-play-circle"></i> Começar Agendamento
                            </button>
                        </form>
                        
                    <?php elseif ($pergunta['tipo'] == 'pergunta_texto'): ?>
                        <form method="POST">
                            <input type="hidden" name="acao" value="<?php echo $pergunta['acao']; ?>">
                            <input type="text" name="resposta" class="ia-text-input-modern" 
                                   placeholder="Digite sua resposta..." required autofocus
                                   onkeyup="formatarTelefoneInput(this, '<?php echo $pergunta['acao']; ?>')">
                            <button type="submit" class="ia-submit-btn-modern">
                                <i class="fas fa-paper-plane"></i> Enviar Resposta
                            </button>
                        </form>
                        
                    <?php elseif ($pergunta['tipo'] == 'pergunta_botoes'): ?>
                        <div class="ia-options-grid">
                            <?php foreach ($pergunta['opcoes'] as $opcao): ?>
                                <form method="POST">
                                    <input type="hidden" name="acao" value="<?php echo $opcao['acao']; ?>">
                                    <input type="hidden" name="resposta" value="<?php echo $opcao['valor']; ?>">
                                    <button type="submit" class="ia-btn-opcao-modern <?php echo (isset($opcao['disabled']) && $opcao['disabled']) ? 'btn-indisponivel' : ''; ?>"
                                            <?php echo (isset($opcao['disabled']) && $opcao['disabled']) ? 'disabled' : ''; ?>>
                                        <?php 
                                        // Adiciona ícone baseado no texto
                                        if (isset($opcao['disabled']) && $opcao['disabled']) {
                                            echo '<i class="fas fa-times-circle" style="color: var(--ai-error);"></i>';
                                        } elseif (strpos($opcao['texto'], '✅') !== false || strpos($opcao['texto'], 'Sim') !== false) {
                                            echo '<i class="fas fa-check-circle" style="color: var(--ai-success);"></i>';
                                        } elseif (strpos($opcao['texto'], '❌') !== false || strpos($opcao['texto'], 'Não') !== false) {
                                            echo '<i class="fas fa-times-circle" style="color: var(--ai-error);"></i>';
                                        } else {
                                            echo '<i class="fas fa-chevron-right" style="color: var(--ai-primary);"></i>';
                                        }
                                        ?>
                                        <span><?php echo $opcao['texto']; ?></span>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php elseif ($pergunta['tipo'] == 'pergunta_endereco'): ?>
                        <form method="POST" class="ia-address-form-modern">
                            <input type="hidden" name="acao" value="endereco">
                            
                            <input type="text" name="rua" placeholder="Rua, Avenida *" required 
                                   value="<?php echo $_SESSION['ia_conversa']['dados']['endereco_rua'] ?? ''; ?>"
                                   class="fade-in">
                            
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                                <input type="text" name="numero" placeholder="Número *" required 
                                       value="<?php echo $_SESSION['ia_conversa']['dados']['endereco_numero'] ?? ''; ?>"
                                       class="fade-in">
                                <input type="text" name="bairro" placeholder="Bairro *" required 
                                       value="<?php echo $_SESSION['ia_conversa']['dados']['endereco_bairro'] ?? ''; ?>"
                                       class="fade-in">
                            </div>
                            
                            <input type="text" name="cidade" placeholder="Cidade *" required 
                                   value="<?php echo $_SESSION['ia_conversa']['dados']['endereco_cidade'] ?? ''; ?>"
                                   class="fade-in">
                            
                            <button type="submit" class="ia-submit-btn-modern">
                                <i class="fas fa-map-marker-alt"></i> Enviar Endereço
                            </button>
                        </form>
                        
                    <?php elseif ($pergunta['tipo'] == 'erro_agendamento'): ?>
                        <div class="ia-erro-agendamento">
                            <div class="ia-success-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3>Horário Indisponível</h3>
                            <p><?php echo nl2br(htmlspecialchars($pergunta['texto'])); ?></p>
                            
                            <div class="ia-final-actions">
                                <form method="POST">
                                    <input type="hidden" name="acao" value="voltar_para_datas">
                                    <button type="submit" class="ia-btn-modern ia-go-home-modern">
                                        <i class="fas fa-calendar-alt"></i> Escolher Outra Data
                                    </button>
                                </form>
                                <a href="sistema-ia.php?limpar=1" class="ia-btn-modern ia-go-home-modern">
                                    <i class="fas fa-redo"></i> Começar Novamente
                                </a>
                            </div>
                        </div>
                        
                    <?php elseif ($pergunta['tipo'] == 'final' && isset($pergunta['link_whatsapp'])): ?>
                        <div class="ia-final-message">
                            <div class="ia-success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>🎉 Agendamento Concluído!</h3>
                            <p>✅ Seu agendamento foi registrado com sucesso!</p>
                           <!-- <p>💰 <strong>Valor total: R$ <?php echo $pergunta['valor_total']; ?></strong></p>-->
                            
                            <a href="<?php echo $pergunta['link_whatsapp']; ?>" target="_blank" class="ia-whatsapp-btn-modern">
                                <i class="fab fa-whatsapp"></i> Enviar para WhatsApp
                            </a>
                            
                            <div class="ia-final-actions">
                                <a href="sistema-ia.php?limpar=1" class="ia-btn-modern ia-new-chat-modern" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
  color: white;
  box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);">
                                    <i class="fas fa-plus-circle"></i> Novo Agendamento
                                </a>
                                <a href="index.php" class="ia-btn-modern ia-go-home-modern">
                                    <i class="fas fa-home"></i> Voltar para Home
                                </a>
                            </div>
                        </div>
                        
                    <?php elseif ($pergunta['tipo'] == 'erro'): ?>
                        <div class="ia-erro-agendamento" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 2px solid #fecaca;">
                            <div class="ia-success-icon" style="color: var(--ai-error);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 style="color: var(--ai-error);">Ops, ocorreu um erro!</h3>
                            <p>Não conseguimos processar seu agendamento no momento.</p>
                            <p style="color: #64748b; font-size: 0.9rem;">Por favor, tente novamente ou entre em contato diretamente.</p>
                            
                            <div class="ia-final-actions">
                                <a href="sistema-ia.php?limpar=1" class="ia-btn-modern ia-new-chat-modern" style="background: var(--ai-error);">
                                    <i class="fas fa-redo"></i> Tentar Novamente
                                </a>
                                <a href="index.php" class="ia-btn-modern ia-go-home-modern">
                                    <i class="fas fa-home"></i> Voltar para Home
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!in_array($pergunta['tipo'], ['final', 'erro', 'erro_agendamento'])): ?>
                <div class="ia-dicas-modern">
                    <strong><i class="fas fa-lightbulb"></i> Dicas para um atendimento rápido:</strong>
                    <ul>
                        <li>Responda às perguntas da Laura</li>
                        <li>Use números para quantidades</li>
                        <li>Para BTUs, informe o valor correto (ex: 7000, 9000, 12000)</li>
                        <li>O sistema salva automaticamente</li>
                        <li><strong>✅ Datas e horários em VERMELHO estão INDISPONÍVEIS</strong></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Formatar telefone enquanto digita
    function formatarTelefoneInput(input, acao) {
        if (acao === 'whatsapp') {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 0) {
                if (value.length <= 2) {
                    value = '(' + value;
                } else if (value.length <= 7) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                } else if (value.length <= 11) {
                    value = '(' + value.substring(0, 2) + ') ' + 
                            value.substring(2, 3) + ' ' + 
                            value.substring(3, 7) + '-' + 
                            value.substring(7);
                }
                
                input.value = value;
            }
        }
    }
    
    // Desabilitar botão ao enviar formulário
    document.addEventListener('DOMContentLoaded', function() {
        const textInput = document.querySelector('.ia-text-input-modern');
        if (textInput) {
            textInput.focus();
            textInput.addEventListener('input', function() {
                const submitBtn = this.closest('form').querySelector('button[type="submit"]');
                if (submitBtn && this.value.trim() !== '') {
                    submitBtn.style.background = 'var(--ai-success)';
                }
            });
        }
        
        // Animar botões ao passar o mouse
        const buttons = document.querySelectorAll('.ia-btn-opcao-modern:not(:disabled), .ia-submit-btn-modern:not(:disabled)');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Desabilitar botão ao enviar formulário
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
                    submitBtn.style.opacity = '0.8';
                    
                    // Mostrar animação de carregamento
                    const conversation = document.querySelector('.ia-conversation-modern');
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'ia-message-modern';
                    loadingDiv.innerHTML = `
                        <div class="ia-message-content">
                            <p><i class="fas fa-spinner fa-spin"></i> Processando sua resposta...</p>
                        </div>
                    `;
                    conversation.insertBefore(loadingDiv, document.querySelector('.ia-response-form-modern'));
                }
            }
        });
        
        // Efeito de digitação para nova mensagem
        const lastMessage = document.querySelector('.ia-message-content p');
        if (lastMessage && !lastMessage.dataset.animated) {
            const originalText = lastMessage.textContent;
            lastMessage.textContent = '';
            lastMessage.dataset.animated = 'true';
            
            let i = 0;
            const typeWriter = () => {
                if (i < originalText.length) {
                    lastMessage.textContent += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 20);
                }
            };
            
            // Inicia apenas se for uma nova conversa
            if (window.location.search.indexOf('limpar=1') === -1) {
                setTimeout(typeWriter, 500);
            } else {
                lastMessage.textContent = originalText;
            }
        }
        
        // Corrigir o problema dos botões - APENAS desabilitar os que realmente têm 'disabled' no array PHP
        document.querySelectorAll('.ia-btn-opcao-modern').forEach(btn => {
            // Verificar se o botão tem texto "❌" mas NÃO deve ser desabilitado
            const btnText = btn.textContent;
            const hasRedX = btnText.includes('❌') || btnText.includes('Não, é só isso mesmo');
            const hasDisabledAttr = btn.hasAttribute('disabled');
            
            // Só desabilitar se realmente estiver marcado como disabled no HTML
            if (hasRedX && hasDisabledAttr) {
                btn.classList.add('btn-indisponivel');
            } else if (hasRedX && !hasDisabledAttr) {
                // Se tem ❌ mas não tem disabled, manter habilitado
                btn.classList.remove('btn-indisponivel');
                btn.disabled = false;
            }
        });
    });
    
    // Efeito de confete para finalização
    function triggerConfetti() {
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    }
    
    // Verifica se está na página final
    const finalMessage = document.querySelector('.ia-final-message');
    if (finalMessage) {
        setTimeout(triggerConfetti, 500);
    }
    </script>
    
    <!-- Confetti JS -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>