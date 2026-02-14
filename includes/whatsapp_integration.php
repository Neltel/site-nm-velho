<?php
/**
 * includes/whatsapp_integration.php
 * 
 * Classe para integração com WhatsApp API
 * Envia notificações automáticas para clientes sobre:
 * - Agendamentos
 * - Orçamentos
 * - Cobranças
 * - Lembretes de manutenção
 */

class WhatsAppIntegration {
    
    private $pdo;
    private $apiKey;
    private $numero;
    private $ativo;
    
    /**
     * Construtor
     * @param PDO $pdo Conexão com banco de dados
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->carregarConfiguracoes();
    }
    
    /**
     * Carrega configurações do WhatsApp do banco
     */
    private function carregarConfiguracoes() {
        try {
            $stmt = $this->pdo->query("SELECT chave, valor FROM configuracoes WHERE chave LIKE 'whatsapp_%'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                switch ($row['chave']) {
                    case 'whatsapp_api_key':
                        $this->apiKey = $row['valor'];
                        break;
                    case 'whatsapp_numero':
                        $this->numero = $row['valor'];
                        break;
                    case 'whatsapp_ativo':
                        $this->ativo = ($row['valor'] === 'true' || $row['valor'] === '1');
                        break;
                }
            }
        } catch (PDOException $e) {
            $this->ativo = false;
            error_log("Erro ao carregar configurações WhatsApp: " . $e->getMessage());
        }
    }
    
    /**
     * Envia notificação de agendamento
     * 
     * @param int $agendamentoId ID do agendamento
     * @return array Resultado do envio
     */
    public function notificarAgendamento($agendamentoId) {
        if (!$this->ativo) {
            return ['sucesso' => false, 'mensagem' => 'WhatsApp desativado'];
        }
        
        try {
            // Buscar dados do agendamento
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.nome as cliente_nome, c.whatsapp, c.telefone,
                       s.nome as servico_nome
                FROM agendamentos a
                LEFT JOIN clientes c ON a.cliente_id = c.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                WHERE a.id = ?
            ");
            $stmt->execute([$agendamentoId]);
            $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$agendamento) {
                return ['sucesso' => false, 'mensagem' => 'Agendamento não encontrado'];
            }
            
            // Número do cliente (WhatsApp ou telefone)
            $telefoneCliente = $agendamento['whatsapp'] ?? $agendamento['telefone'];
            if (empty($telefoneCliente)) {
                return ['sucesso' => false, 'mensagem' => 'Cliente sem telefone cadastrado'];
            }
            
            // Montar mensagem
            $data = date('d/m/Y', strtotime($agendamento['data_agendamento']));
            $hora = date('H:i', strtotime($agendamento['hora_agendamento']));
            
            $mensagem = "*🗓️ AGENDAMENTO CONFIRMADO*\n\n";
            $mensagem .= "Olá, {$agendamento['cliente_nome']}!\n\n";
            $mensagem .= "Seu agendamento foi confirmado:\n\n";
            $mensagem .= "📋 *Serviço:* {$agendamento['servico_nome']}\n";
            $mensagem .= "📅 *Data:* {$data}\n";
            $mensagem .= "🕐 *Horário:* {$hora}\n";
            
            if (!empty($agendamento['endereco'])) {
                $mensagem .= "📍 *Local:* {$agendamento['endereco']}\n";
            }
            
            if (!empty($agendamento['observacoes'])) {
                $mensagem .= "\n💬 *Observações:* {$agendamento['observacoes']}\n";
            }
            
            $mensagem .= "\n---\n";
            $mensagem .= "Em caso de dúvidas, entre em contato conosco.\n";
            $mensagem .= "Atenciosamente,\n*N&M Refrigeração*";
            
            // Enviar mensagem
            $resultado = $this->enviarMensagem($telefoneCliente, $mensagem, 'agendamento', $agendamentoId);
            
            // Atualizar status no banco
            if ($resultado['sucesso']) {
                $stmt = $this->pdo->prepare("
                    UPDATE agendamentos 
                    SET notificado_whatsapp = 1, data_notificacao = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$agendamentoId]);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao enviar notificação: ' . $e->getMessage()];
        }
    }
    
    /**
     * Envia orçamento via WhatsApp
     * 
     * @param int $orcamentoId ID do orçamento
     * @param string $pdfPath Caminho do PDF (opcional)
     * @return array Resultado do envio
     */
    public function enviarOrcamento($orcamentoId, $pdfPath = null) {
        if (!$this->ativo) {
            return ['sucesso' => false, 'mensagem' => 'WhatsApp desativado'];
        }
        
        try {
            // Buscar dados do orçamento
            $stmt = $this->pdo->prepare("
                SELECT o.*, c.nome as cliente_nome, c.whatsapp, c.telefone
                FROM orcamentos o
                LEFT JOIN clientes c ON o.cliente_id = c.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orcamentoId]);
            $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$orcamento) {
                return ['sucesso' => false, 'mensagem' => 'Orçamento não encontrado'];
            }
            
            $telefoneCliente = $orcamento['whatsapp'] ?? $orcamento['telefone'];
            if (empty($telefoneCliente)) {
                return ['sucesso' => false, 'mensagem' => 'Cliente sem telefone cadastrado'];
            }
            
            // Montar mensagem
            $numeroOrcamento = $orcamento['numero_orcamento'] ?? str_pad($orcamentoId, 6, '0', STR_PAD_LEFT);
            $valorFormatado = 'R$ ' . number_format($orcamento['valor_total'], 2, ',', '.');
            
            $mensagem = "*📋 ORÇAMENTO DISPONÍVEL*\n\n";
            $mensagem .= "Olá, {$orcamento['cliente_nome']}!\n\n";
            $mensagem .= "Seu orçamento está pronto:\n\n";
            $mensagem .= "🔢 *Número:* {$numeroOrcamento}\n";
            $mensagem .= "💰 *Valor Total:* {$valorFormatado}\n";
            $mensagem .= "📅 *Validade:* " . ($orcamento['validade_dias'] ?? 15) . " dias\n";
            
            if (!empty($orcamento['descricao'])) {
                $mensagem .= "\n📝 *Descrição:*\n{$orcamento['descricao']}\n";
            }
            
            $mensagem .= "\n---\n";
            $mensagem .= "Para aprovar este orçamento ou tirar dúvidas, responda esta mensagem.\n";
            $mensagem .= "\nAtenciosamente,\n*N&M Refrigeração*";
            
            // Enviar com PDF se disponível
            $anexos = $pdfPath ? [$pdfPath] : null;
            $resultado = $this->enviarMensagem($telefoneCliente, $mensagem, 'orcamento', $orcamentoId, $anexos);
            
            // Atualizar status no banco
            if ($resultado['sucesso']) {
                $stmt = $this->pdo->prepare("
                    UPDATE orcamentos 
                    SET enviado_whatsapp = 1, data_envio_whatsapp = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$orcamentoId]);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao enviar orçamento: ' . $e->getMessage()];
        }
    }
    
    /**
     * Envia lembrete de cobrança
     * 
     * @param int $cobrancaId ID da cobrança
     * @return array Resultado do envio
     */
    public function enviarLembreteCobranca($cobrancaId) {
        if (!$this->ativo) {
            return ['sucesso' => false, 'mensagem' => 'WhatsApp desativado'];
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, cl.nome as cliente_nome, cl.whatsapp, cl.telefone
                FROM cobrancas c
                LEFT JOIN clientes cl ON c.cliente_id = cl.id
                WHERE c.id = ?
            ");
            $stmt->execute([$cobrancaId]);
            $cobranca = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cobranca) {
                return ['sucesso' => false, 'mensagem' => 'Cobrança não encontrada'];
            }
            
            $telefoneCliente = $cobranca['whatsapp'] ?? $cobranca['telefone'];
            if (empty($telefoneCliente)) {
                return ['sucesso' => false, 'mensagem' => 'Cliente sem telefone cadastrado'];
            }
            
            $valorFormatado = 'R$ ' . number_format($cobranca['valor'], 2, ',', '.');
            $dataVencimento = date('d/m/Y', strtotime($cobranca['data_vencimento']));
            
            // Verificar se está vencida
            $hoje = new DateTime();
            $vencimento = new DateTime($cobranca['data_vencimento']);
            $diasAtraso = $hoje->diff($vencimento)->days;
            $vencida = $hoje > $vencimento;
            
            $mensagem = "*💳 LEMBRETE DE PAGAMENTO*\n\n";
            $mensagem .= "Olá, {$cobranca['cliente_nome']}!\n\n";
            
            if ($vencida) {
                $mensagem .= "⚠️ *COBRANÇA VENCIDA*\n\n";
                $mensagem .= "Identificamos um pagamento em atraso:\n\n";
                $mensagem .= "📝 *Descrição:* {$cobranca['descricao']}\n";
                $mensagem .= "💰 *Valor:* {$valorFormatado}\n";
                $mensagem .= "📅 *Vencimento:* {$dataVencimento} ({$diasAtraso} dias de atraso)\n";
            } else {
                $mensagem .= "Este é um lembrete de pagamento:\n\n";
                $mensagem .= "📝 *Descrição:* {$cobranca['descricao']}\n";
                $mensagem .= "💰 *Valor:* {$valorFormatado}\n";
                $mensagem .= "📅 *Vencimento:* {$dataVencimento}\n";
            }
            
            $mensagem .= "\n💳 *Formas de Pagamento:*\n";
            $mensagem .= "• PIX\n• Dinheiro\n• Cartão de Crédito/Débito\n";
            
            $mensagem .= "\n---\n";
            $mensagem .= "Para efetuar o pagamento ou negociar, entre em contato.\n";
            $mensagem .= "\nAtenciosamente,\n*N&M Refrigeração*";
            
            $resultado = $this->enviarMensagem($telefoneCliente, $mensagem, 'cobranca', $cobrancaId);
            
            if ($resultado['sucesso']) {
                $stmt = $this->pdo->prepare("
                    UPDATE cobrancas 
                    SET notificado = 1, data_notificacao = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$cobrancaId]);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao enviar lembrete: ' . $e->getMessage()];
        }
    }
    
    /**
     * Envia mensagem via WhatsApp API
     * 
     * @param string $telefone Número do telefone
     * @param string $mensagem Texto da mensagem
     * @param string $tipo Tipo de notificação
     * @param int $referenciaId ID de referência
     * @param array $anexos Arquivos para anexar (opcional)
     * @return array Resultado do envio
     */
    private function enviarMensagem($telefone, $mensagem, $tipo = null, $referenciaId = null, $anexos = null) {
        try {
            // Limpar número de telefone
            $telefone = preg_replace('/[^0-9]/', '', $telefone);
            
            // Se não tiver código do país, adicionar +55 (Brasil)
            if (strlen($telefone) <= 11) {
                $telefone = '55' . $telefone;
            }
            
            // IMPLEMENTAÇÃO REAL DA API
            // Aqui você integraria com a API real do WhatsApp Business ou serviço similar
            // Exemplos: Twilio, MessageBird, WhatSender, etc.
            
            // SIMULAÇÃO PARA DESENVOLVIMENTO
            $enviado = true; // Simular sucesso
            $statusApi = 'enviado';
            $mensagemErro = null;
            
            /* EXEMPLO DE INTEGRAÇÃO REAL:
            $apiUrl = 'https://api.whatsapp.com/send';
            $response = $this->chamarAPI($apiUrl, [
                'api_key' => $this->apiKey,
                'phone' => $telefone,
                'message' => $mensagem,
                'attachments' => $anexos
            ]);
            $enviado = $response['success'];
            $statusApi = $response['status'];
            $mensagemErro = $response['error'] ?? null;
            */
            
            // Registrar no banco de dados
            $stmt = $this->pdo->prepare("
                INSERT INTO notificacoes_whatsapp 
                (telefone, tipo_notificacao, mensagem, status, mensagem_erro, 
                 agendamento_id, orcamento_id, cobranca_id, anexos)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $agendamentoId = ($tipo === 'agendamento') ? $referenciaId : null;
            $orcamentoId = ($tipo === 'orcamento') ? $referenciaId : null;
            $cobrancaId = ($tipo === 'cobranca') ? $referenciaId : null;
            $anexosJson = $anexos ? json_encode($anexos) : null;
            
            $stmt->execute([
                $telefone,
                $tipo,
                $mensagem,
                $statusApi,
                $mensagemErro,
                $agendamentoId,
                $orcamentoId,
                $cobrancaId,
                $anexosJson
            ]);
            
            // Registrar log
            if (function_exists('registrarLog')) {
                registrarLog('info', 'Mensagem WhatsApp enviada', [
                    'telefone' => $telefone,
                    'tipo' => $tipo,
                    'status' => $statusApi
                ]);
            }
            
            return [
                'sucesso' => $enviado,
                'mensagem' => $enviado ? 'Mensagem enviada com sucesso' : 'Erro ao enviar mensagem',
                'detalhes' => [
                    'telefone' => $telefone,
                    'status' => $statusApi,
                    'erro' => $mensagemErro
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao enviar mensagem: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Gera link do WhatsApp para abertura direta
     * 
     * @param string $telefone Número do telefone
     * @param string $mensagem Mensagem pré-preenchida (opcional)
     * @return string URL do WhatsApp
     */
    public static function gerarLink($telefone, $mensagem = '') {
        // Limpar número
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        // Adicionar código do país se necessário
        if (strlen($telefone) <= 11) {
            $telefone = '55' . $telefone;
        }
        
        // Codificar mensagem
        $mensagemCodificada = urlencode($mensagem);
        
        // Retornar link
        return "https://wa.me/{$telefone}" . ($mensagem ? "?text={$mensagemCodificada}" : "");
    }
    
    /**
     * Verifica se WhatsApp está ativo
     * 
     * @return bool True se ativo
     */
    public function estaAtivo() {
        return $this->ativo;
    }
}
?>
