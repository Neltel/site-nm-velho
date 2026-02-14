// Enviar mensagem para WhatsApp - Orçamento
function enviarWhatsAppOrcamento($pdo, $dados_orcamento) {
    $config = getConfigAgendamento($pdo);
    $whatsapp = isset($config['whatsapp_empresa']) ? $config['whatsapp_empresa'] : '5517999999999';
    
    // Buscar nome do serviço
    try {
        $stmt = $pdo->prepare("SELECT nome FROM servicos WHERE id = ?");
        $stmt->execute([$dados_orcamento['servico_id']]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_servico = $servico ? $servico['nome'] : 'Serviço não encontrado';
    } catch(PDOException $e) {
        $nome_servico = 'Serviço não encontrado';
    }
    
    // Preparar mensagem específica para orçamento
    $mensagem = "💰 *NOVO ORÇAMENTO SOLICITADO - ClimaTech*

👤 *Cliente:* {$dados_orcamento['nome']}
📞 *Telefone:* {$dados_orcamento['telefone']}
📧 *E-mail:* {$dados_orcamento['email']}

🔧 *Serviço Solicitado:* {$nome_servico}

⚙️ *Detalhes do Equipamento:*
🏷️ *Marca:* {$dados_orcamento['marca']}
❄️ *BTUs:* {$dados_orcamento['btus']}
🔧 *Tipo:* {$dados_orcamento['tipo']}

📝 *Observações:*
{$dados_orcamento['observacoes']}

⏰ *Solicitado via Site*";
    
    // Codificar mensagem para URL
    $mensagem_encoded = urlencode($mensagem);
    
    // Gerar link do WhatsApp
    $link_whatsapp = "https://wa.me/{$whatsapp}?text={$mensagem_encoded}";
    
    return $link_whatsapp;
}