<?php
// agendamento.php - VERSÃO SIMPLIFICADA

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuração
include 'includes/config.php';
include 'includes/header.php';

// Verificar se funções de agendamento existem
if (file_exists('includes/funcoes_agendamento.php')) {
    include 'includes/funcoes_agendamento.php';
} else {
    // Funções básicas de fallback
    function getConfigAgendamento($pdo) {
        return [
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'horario_especial_inicio' => '18:00',
            'horario_especial_fim' => '20:00',
            'bloquear_finais_semana' => '0',
            'valor_hora_especial' => '50.00',
            'valor_final_semana' => '100.00',
            'limite_agendamentos_dia' => '8',
            'whatsapp_empresa' => '5517996240725'
        ];
    }

    function verificarDisponibilidade($pdo, $data, $hora) {
        return ['disponivel' => true, 'motivo' => ''];
    }

    function calcularAcrescimoHorario($data, $hora) {
        return 0;
    }

    function enviarWhatsAppAgendamento($pdo, $dados_agendamento) {
        $whatsapp = '5517996240725';
        
        // Buscar nome do serviço
        try {
            $stmt = $pdo->prepare("SELECT nome FROM servicos WHERE id = ?");
            $stmt->execute([$dados_agendamento['servico_id']]);
            $servico = $stmt->fetch(PDO::FETCH_ASSOC);
            $nome_servico = $servico ? $servico['nome'] : 'Serviço não encontrado';
        } catch(PDOException $e) {
            $nome_servico = 'Serviço não encontrado';
        }
        
        // DEBUG: Verificar os dados recebidos
        error_log("Dados recebidos no WhatsApp: " . print_r($dados_agendamento, true));
        
        // Montar endereço completo a partir dos campos separados
        $endereco_completo = "";
        if (!empty($dados_agendamento['endereco_rua'])) {
            $endereco_completo .= $dados_agendamento['endereco_rua'];
            if (!empty($dados_agendamento['endereco_numero'])) {
                $endereco_completo .= ", " . $dados_agendamento['endereco_numero'];
            }
            if (!empty($dados_agendamento['endereco_bairro'])) {
                $endereco_completo .= " - " . $dados_agendamento['endereco_bairro'];
            }
            if (!empty($dados_agendamento['endereco_cidade'])) {
                $endereco_completo .= ", " . $dados_agendamento['endereco_cidade'];
            }
        } else {
            // Fallback para o endereço antigo se necessário
            $endereco_completo = $dados_agendamento['endereco'] ?? 'Endereço não informado';
        }
        
        $mensagem = "📅 *NOVO AGENDAMENTO*

👤 *Cliente:* {$dados_agendamento['nome']}
📞 *Telefone:* {$dados_agendamento['telefone']}
📧 *E-mail:* {$dados_agendamento['email']}
📍 *Endereço:* {$endereco_completo}

🔧 *Serviço:* {$nome_servico}
📅 *Data:* " . date('d/m/Y', strtotime($dados_agendamento['data_agendamento'])) . "
⏰ *Horário:* {$dados_agendamento['hora_agendamento']}

💬 *Observações:*
{$dados_agendamento['observacoes']}

⚡ *Agendado via Site*";
        
        return "https://wa.me/{$whatsapp}?text=" . urlencode($mensagem);
    }

    function gerarDatasDisponiveis($pdo) {
        $datas_disponiveis = [];
        $data_atual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        
        for ($i = 1; $i <= 30; $i++) {
            $data = clone $data_atual;
            $data->modify("+{$i} days");
            $data_str = $data->format('Y-m-d');
            $dia_semana = $data->format('l');
            
            $dias_traduzidos = [
                'Monday' => 'Segunda',
                'Tuesday' => 'Terça', 
                'Wednesday' => 'Quarta',
                'Thursday' => 'Quinta',
                'Friday' => 'Sexta',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $dia_semana_traduzido = $dias_traduzidos[$dia_semana] ?? $dia_semana;
            
            $datas_disponiveis[] = [
                'data' => $data_str,
                'formatada' => $data->format('d/m/Y'),
                'dia_semana' => $dia_semana_traduzido,
                'total_horarios' => 8
            ];
        }
        
        return $datas_disponiveis;
    }
}

// Buscar configurações
$config = getConfigAgendamento($pdo);

// Inicializar variáveis
$erro = '';
$sucesso = '';
$whatsapp_auto = '';

// Processar agendamento
if($_POST && isset($_POST['nome'])) {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    $servico_id = $_POST['servico_id'];
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];
    
    // Capturar os novos campos de endereço separados
    $endereco_rua = sanitize($_POST['endereco_rua']);
    $endereco_numero = sanitize($_POST['endereco_numero']);
    $endereco_bairro = sanitize($_POST['endereco_bairro']);
    $endereco_cidade = sanitize($_POST['endereco_cidade']);
    
    // Juntar os campos de endereço para manter compatibilidade com o banco existente
    $endereco_completo = $endereco_rua;
    if (!empty($endereco_numero)) {
        $endereco_completo .= ", " . $endereco_numero;
    }
    if (!empty($endereco_bairro)) {
        $endereco_completo .= " - " . $endereco_bairro;
    }
    if (!empty($endereco_cidade)) {
        $endereco_completo .= ", " . $endereco_cidade;
    }
    
    $observacoes = sanitize($_POST['observacoes']);
    
    // Verificar disponibilidade
    $disponibilidade = verificarDisponibilidade($pdo, $data_agendamento, $hora_agendamento);
    
    if(!$disponibilidade['disponivel']) {
        $erro = "Desculpe, este horário não está mais disponível. Motivo: " . $disponibilidade['motivo'];
    } else {
        try {
            // Verificar se cliente já existe (por telefone)
            $stmt = $pdo->prepare("SELECT id FROM clientes WHERE telefone = ?");
            $stmt->execute([$telefone]);
            $cliente_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($cliente_existente) {
                $cliente_id = $cliente_existente['id'];
                // Atualizar dados do cliente
                $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $cliente_id]);
            } else {
                // Inserir novo cliente
                $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $email, $telefone]);
                $cliente_id = $pdo->lastInsertId();
            }
            
            // Calcular tempo estimado (2 horas para instalação, 1 hora para outros serviços)
            $tempo_estimado = 60; // Valor padrão
            try {
                $stmt = $pdo->prepare("SELECT categoria FROM servicos WHERE id = ?");
                $stmt->execute([$servico_id]);
                $servico = $stmt->fetch(PDO::FETCH_ASSOC);
                if($servico && $servico['categoria'] == 'instalacao') {
                    $tempo_estimado = 120;
                }
            } catch(PDOException $e) {
                // Usar valor padrão em caso de erro
            }
            
            // Calcular acréscimo
            $acrescimo = calcularAcrescimoHorario($data_agendamento, $hora_agendamento);
            
            // Inserir agendamento (usando endereço_completo para manter compatibilidade)
            $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, servico_id, data_agendamento, hora_agendamento, endereco, tempo_estimado_minutos, observacoes, acrescimo_especial) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $servico_id, $data_agendamento, $hora_agendamento, $endereco_completo, $tempo_estimado, $observacoes, $acrescimo]);
            
            $agendamento_id = $pdo->lastInsertId();
            
            // Preparar dados para WhatsApp - CORRIGIDO: Incluir todos os campos de endereço
            $dados_whatsapp = [
                'nome' => $nome,
                'telefone' => $telefone,
                'email' => $email,
                'endereco_rua' => $endereco_rua,
                'endereco_numero' => $endereco_numero,
                'endereco_bairro' => $endereco_bairro,
                'endereco_cidade' => $endereco_cidade,
                'endereco' => $endereco_completo, // Para compatibilidade
                'servico_id' => $servico_id,
                'data_agendamento' => $data_agendamento,
                'hora_agendamento' => $hora_agendamento,
                'observacoes' => $observacoes
            ];
            
            // Gerar link do WhatsApp
            $link_whatsapp = enviarWhatsAppAgendamento($pdo, $dados_whatsapp);
            
            $sucesso = "Agendamento solicitado com sucesso! Entraremos em contato para confirmar.";
            
            if($acrescimo > 0) {
                $sucesso .= " Observação: Este agendamento possui acréscimo de R$ " . number_format($acrescimo, 2, ',', '.') . " por ser em horário especial.";
            }
            
            // Adicionar link do WhatsApp
            $sucesso .= "<br><br><a href='{$link_whatsapp}' target='_blank' class='btn btn-success' style='color: white;' id='whatsappLink'>💬 Enviar Mensagem no WhatsApp</a>";
            
            // ✅ JAVASCRIPT PARA ABRIR WHATSAPP AUTOMATICAMENTE
            $whatsapp_auto = "
            <script>
            // Abrir WhatsApp automaticamente após 1 segundo
            setTimeout(function() {
                console.log('Abrindo WhatsApp automaticamente...');
                window.open('{$link_whatsapp}', '_blank');
                
                // Também clica no link como fallback
                document.getElementById('whatsappLink').click();
            }, 1000);
            </script>
            ";
            
        } catch(PDOException $e) {
            // Log do erro para debug
            error_log("Erro no agendamento: " . $e->getMessage());
            $erro = "Erro ao processar agendamento. Por favor, tente novamente.";
        }
    }
}

// Gerar datas disponíveis
try {
    $datas_disponiveis = gerarDatasDisponiveis($pdo);
} catch(Exception $e) {
    error_log("Erro ao gerar datas disponíveis: " . $e->getMessage());
    // Fallback: gerar datas básicas
    $datas_disponiveis = [];
    $data_atual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    
    for ($i = 1; $i <= 30; $i++) {
        $data = clone $data_atual;
        $data->modify("+{$i} days");
        $data_str = $data->format('Y-m-d');
        $dia_semana = $data->format('l');
        
        $dias_traduzidos = [
            'Monday' => 'Segunda',
            'Tuesday' => 'Terça', 
            'Wednesday' => 'Quarta',
            'Thursday' => 'Quinta',
            'Friday' => 'Sexta',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $dia_semana_traduzido = $dias_traduzidos[$dia_semana] ?? $dia_semana;
        
        $datas_disponiveis[] = [
            'data' => $data_str,
            'formatada' => $data->format('d/m/Y'),
            'dia_semana' => $dia_semana_traduzido,
            'total_horarios' => 8
        ];
    }
}
?>

<section class="section-title">
    <div class="container">
        <h2>Agendar Serviço</h2>
        <p>Escolha a data e horário disponíveis para seu atendimento</p>
    </div>
</section>

<section class="booking">
    <div class="container">
        <?php if($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if($sucesso): ?>
        <div class="alert alert-success">
            <?php echo $sucesso; ?>
        </div>
        
        <!-- ✅ EXECUTAR O JAVASCRIPT DO WHATSAPP AUTOMÁTICO -->
        <?php echo $whatsapp_auto; ?>
        
        <?php endif; ?>
        
        <?php if(!$sucesso): ?>
        <!-- MOSTRAR FORMULÁRIO APENAS SE NÃO HOUVER SUCESSO -->
        <div class="booking-container">
            <div class="booking-content">
                <h3>Agendamento Inteligente</h3>
                <p>Nosso sistema mostra apenas <strong>datas e horários realmente disponíveis</strong>, considerando:</p>
                <ul>
                    <li>✅ Horários já agendados</li>
                    <li>✅ Limite diário de serviços (<?php echo $config['limite_agendamentos_dia']; ?> por dia)</li>
                    <li>✅ Horário comercial (<?php echo date('H:i', strtotime($config['horario_inicio'])); ?> às <?php echo date('H:i', strtotime($config['horario_fim'])); ?>)</li>
                    <?php if($config['horario_especial_inicio']): ?>
                    <li>✅ Horário especial (<?php echo date('H:i', strtotime($config['horario_especial_inicio'])); ?> às <?php echo date('H:i', strtotime($config['horario_especial_fim'])); ?>)</li>
                    <?php endif; ?>
                    <?php if($config['bloquear_finais_semana'] == '1'): ?>
                    <li>❌ Não atendemos finais de semana</li>
                    <?php else: ?>
                    <li>✅ Atendemos finais de semana</li>
                    <?php endif; ?>
                </ul>
                
                <?php if($config['valor_hora_especial'] > 0 || $config['valor_final_semana'] > 0): ?>
                <div class="info-box">
                    <h4>💡 Informações sobre valores:</h4>
                    <?php if($config['valor_hora_especial'] > 0): ?>
                    <p>Horário especial (<?php echo date('H:i', strtotime($config['horario_especial_inicio'])); ?>-<?php echo date('H:i', strtotime($config['horario_especial_fim'])); ?>): 
                    <strong>+ R$ <?php echo number_format($config['valor_hora_especial'], 2, ',', '.'); ?></strong></p>
                    <?php endif; ?>
                    
                    <?php if($config['valor_final_semana'] > 0): ?>
                    <p>Finais de semana: <strong>+ R$ <?php echo number_format($config['valor_final_semana'], 2, ',', '.'); ?></strong></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="booking-form">
                <form method="POST" id="formAgendamento">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo $_POST['nome'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone *</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" required value="<?php echo $_POST['telefone'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                    
                    <!-- NOVOS CAMPOS DE ENDEREÇO SEPARADOS -->
                    <div class="form-group">
                        <label for="endereco_rua">Rua *</label>
                        <input type="text" id="endereco_rua" name="endereco_rua" class="form-control" required placeholder="Nome da rua, avenida, etc." value="<?php echo $_POST['endereco_rua'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="endereco_numero">Número *</label>
                            <input type="text" id="endereco_numero" name="endereco_numero" class="form-control" required placeholder="Número do local" value="<?php echo $_POST['endereco_numero'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="endereco_bairro">Bairro *</label>
                            <input type="text" id="endereco_bairro" name="endereco_bairro" class="form-control" required placeholder="Nome do bairro" value="<?php echo $_POST['endereco_bairro'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="endereco_cidade">Cidade *</label>
                        <input type="text" id="endereco_cidade" name="endereco_cidade" class="form-control" required placeholder="Nome da cidade" value="<?php echo $_POST['endereco_cidade'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="servico_id">Serviço Desejado *</label>
                        <select id="servico_id" name="servico_id" class="form-control" required>
                            <option value="">Selecione um serviço</option>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1");
                            $stmt->execute();
                            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach($servicos as $servico):
                                $selected = ($_POST['servico_id'] ?? '') == $servico['id'] ? 'selected' : '';
                            ?>
                            <option value="<?php echo $servico['id']; ?>" <?php echo $selected; ?> data-categoria="<?php echo $servico['categoria']; ?>">
                                <?php echo $servico['nome']; ?> <!--- <?php echo formatarMoeda($servico['preco_base']); ?>-->
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_agendamento">Data do Serviço *</label>
                            <select id="data_agendamento" name="data_agendamento" class="form-control" required>
                                <option value="">Selecione uma data disponível</option>
                                <?php foreach($datas_disponiveis as $data): ?>
                                <option value="<?php echo $data['data']; ?>" 
                                        data-horarios="<?php echo $data['total_horarios']; ?>"
                                        <?php echo ($_POST['data_agendamento'] ?? '') == $data['data'] ? 'selected' : ''; ?>>
                                    <?php echo $data['formatada']; ?> (<?php echo traduzirDiaSemana($data['dia_semana']); ?>)
                                    - <?php echo $data['total_horarios']; ?> horários
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text">Mostrando <?php echo count($datas_disponiveis); ?> datas disponíveis</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="hora_agendamento">Horário *</label>
                            <select id="hora_agendamento" name="hora_agendamento" class="form-control" required disabled>
                                <option value="">Primeiro selecione uma data</option>
                            </select>
                            <div id="loadingHorarios" style="display: none;">
                                <small>Carregando horários disponíveis...</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" class="form-control" rows="4" placeholder="Alguma informação adicional que precisamos saber..."><?php echo $_POST['observacoes'] ?? ''; ?></textarea>
                    </div>
                    
                    <div id="infoAgendamento" class="info-agendamento" style="display: none;">
                        <h4>📋 Resumo do Agendamento</h4>
                        <div id="detalhesAgendamento"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-accent" style="width: 100%;" id="btnAgendar">
                        ✅ Solicitar Agendamento & Abrir WhatsApp
                    </button>
                    
                    <div class="text-center" style="margin-top: 15px;">
                        <small class="text-muted">
                            💡 Após clicar, o WhatsApp abrirá automaticamente para confirmarmos seu agendamento
                        </small>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- MOSTRAR BOTÃO PARA NOVO AGENDAMENTO SE HOUVER SUCESSO -->
        <div class="text-center" style="padding: 40px;">
            <h3>🎉 Agendamento Confirmado!</h3>
            <p>Obrigado por agendar conosco. Em instantes o WhatsApp será aberto para finalizarmos os detalhes.</p>
            <a href="agendamento.php" class="btn btn-primary">📅 Fazer Novo Agendamento</a>
            <a href="index.php" class="btn btn-secondary">🏠 Voltar para Home</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Função para traduzir dias da semana
function traduzirDiaSemana(diaIngles) {
    const dias = {
        'Monday': 'Segunda',
        'Tuesday': 'Terça', 
        'Wednesday': 'Quarta',
        'Thursday': 'Quinta',
        'Friday': 'Sexta',
        'Saturday': 'Sábado',
        'Sunday': 'Domingo'
    };
    return dias[diaIngles] || diaIngles;
}

// Carregar horários quando selecionar uma data
document.getElementById('data_agendamento').addEventListener('change', function() {
    const dataSelecionada = this.value;
    const selectHorario = document.getElementById('hora_agendamento');
    const loading = document.getElementById('loadingHorarios');
    
    console.log('✅ Data selecionada no select:', dataSelecionada);
    
    if(!dataSelecionada) {
        selectHorario.innerHTML = '<option value="">Primeiro selecione uma data</option>';
        selectHorario.disabled = true;
        return;
    }
    
    // Mostrar loading
    selectHorario.disabled = true;
    loading.style.display = 'block';
    selectHorario.innerHTML = '<option value="">Carregando...</option>';
    
    // Fazer requisição para buscar horários
    fetch('api/horarios_disponiveis.php?data=' + dataSelecionada)
        .then(response => response.json())
        .then(horarios => {
            selectHorario.innerHTML = '';
            
            if(horarios.length > 0) {
                horarios.forEach(horario => {
                    const option = document.createElement('option');
                    option.value = horario.hora;
                    option.textContent = horario.hora;
                    
                    if(horario.especial) {
                        option.textContent += ' ⭐ (Horário Especial)';
                        option.dataset.especial = 'true';
                    }
                    
                    selectHorario.appendChild(option);
                });
                selectHorario.disabled = false;
            } else {
                selectHorario.innerHTML = '<option value="">Nenhum horário disponível</option>';
                selectHorario.disabled = true;
            }
            
            loading.style.display = 'none';
        })
        .catch(error => {
            console.error('Erro:', error);
            selectHorario.innerHTML = '<option value="">Erro ao carregar horários</option>';
            selectHorario.disabled = true;
            loading.style.display = 'none';
        });
});

// Atualizar informações quando selecionar horário
document.getElementById('hora_agendamento').addEventListener('change', function() {
    const data = document.getElementById('data_agendamento').value;
    const hora = this.value;
    const servicoSelect = document.getElementById('servico_id');
    const servicoSelected = servicoSelect.options[servicoSelect.selectedIndex];
    
    if(data && hora && servicoSelected.value) {
        const infoDiv = document.getElementById('infoAgendamento');
        const detalhesDiv = document.getElementById('detalhesAgendamento');
        
        // ✅ CORREÇÃO: Criar data sem conversão de timezone
        const [ano, mes, dia] = data.split('-');
        
        // ✅ CORREÇÃO: Usar a data diretamente sem conversão do browser
        const dataObj = new Date(ano, mes - 1, dia); // mês é 0-based no JavaScript
        const diaSemana = dataObj.getDay();
        
        // ✅ CORREÇÃO: Buscar o dia da semana correto do select
        const dataSelect = document.getElementById('data_agendamento');
        const optionSelecionada = dataSelect.options[dataSelect.selectedIndex];
        const textoData = optionSelecionada.textContent;
        
        // Extrair o dia da semana do texto da option
        const diaSemanaTexto = textoData.match(/\(([^)]+)\)/)?.[1] || '';
        
        console.log('DEBUG Data:', {
            dataInput: data,
            dataObj: dataObj.toISOString(),
            diaSemanaJS: diaSemana,
            diaSemanaTexto: diaSemanaTexto,
            textoCompleto: textoData
        });
        
        // Calcular se é horário especial
        const horaEspecial = this.options[this.selectedIndex].dataset.especial === 'true';
        
        // ✅ CORREÇÃO: Usar o dia da semana do texto em vez de calcular
        const isFinalSemana = diaSemanaTexto === 'Sábado' || diaSemanaTexto === 'Domingo';
        
        // Configurações do sistema
        const bloquearFinaisSemana = <?php echo $config['bloquear_finais_semana'] == '1' ? 'true' : 'false'; ?>;
        const valorHoraEspecial = <?php echo floatval($config['valor_hora_especial']); ?>;
        const valorFinalSemana = <?php echo floatval($config['valor_final_semana']); ?>;
        
        let acrescimo = 0;
        let infoAcrescimo = '';
        let avisoFinalSemana = '';

        // Acréscimo por horário especial
        if(horaEspecial && valorHoraEspecial > 0) {
            acrescimo += valorHoraEspecial;
        }
        
        // Acréscimo por final de semana (só se não estiver bloqueado)
        if(isFinalSemana && !bloquearFinaisSemana && valorFinalSemana > 0) {
            acrescimo += valorFinalSemana;
        }
        
        // Aviso se for final de semana bloqueado
        if(isFinalSemana && bloquearFinaisSemana) {
            avisoFinalSemana = `<p class="text-warning"><strong>⚠️ Atenção:</strong> Não atendemos aos finais de semana.</p>`;
        }
        
        if(acrescimo > 0) {
            infoAcrescimo = `<p><strong>Acréscimo:</strong> R$ ${acrescimo.toFixed(2).replace('.', ',')} <strong> - Este valor sera adicionado a mais no orçamento.</strong></p>`;
        }
        
        // ✅ CORREÇÃO: Formatar data sem conversão de timezone
        const dataFormatada = `${dia}/${mes}/${ano}`;
        
        detalhesDiv.innerHTML = `
            <p><strong>Serviço:</strong> ${servicoSelected.textContent}</p>
            <p><strong>Data:</strong> ${dataFormatada} (${diaSemanaTexto})</p>
            <p><strong>Horário:</strong> ${hora} ${horaEspecial ? '⭐' : ''}</p>
            ${infoAcrescimo}
            ${avisoFinalSemana}
            <p><small>💡 Confirmaremos seu agendamento em até 2 horas úteis</small></p>
        `;
        
        infoDiv.style.display = 'block';
    }
});

// Validação do formulário
document.getElementById('formAgendamento').addEventListener('submit', function(e) {
    const data = document.getElementById('data_agendamento').value;
    const hora = document.getElementById('hora_agendamento').value;
    
    if(!data || !hora) {
        e.preventDefault();
        alert('Por favor, selecione uma data e horário disponíveis.');
        return false;
    }
    
    // Mostrar loading no botão
    const btn = document.getElementById('btnAgendar');
    btn.disabled = true;
    btn.innerHTML = 'Processando...';
});
</script>

<style>
.info-box {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 5px;
    padding: 15px;
    margin: 15px 0;
}

.info-agendamento {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin: 15px 0;
}

.info-agendamento h4 {
    margin-top: 0;
    color: var(--primary);
}

.select-horario-especial {
    color: #e67e22;
    font-weight: bold;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 10px;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    color: white;
}

.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.form-row .form-group {
    flex: 1;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<?php 
// Função auxiliar para traduzir dias da semana
function traduzirDiaSemana($diaIngles) {
    $dias = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda',
        'Tuesday' => 'Terça', 
        'Wednesday' => 'Quarta',
        'Thursday' => 'Quinta',
        'Friday' => 'Sexta',
        'Saturday' => 'Sábado'
    ];
    return $dias[$diaIngles] ?? $diaIngles;
}

include 'includes/footer.php'; 
?>