<?php
// includes/functions.php

/**
 * Funções auxiliares para o sistema
 */

/**
 * Formata valor em moeda brasileira
 */
function formatarMoeda($valor) {
    if(empty($valor)) $valor = 0;
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Remove formatação de moeda e retorna valor numérico
 */
function desformatarMoeda($valor) {
    if(empty($valor)) return 0;
    return (float) str_replace(['R$', '.', ','], ['', '', '.'], $valor);
}

/**
 * Sanitiza dados de entrada
 */
function sanitize($data) {
    if(is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Gera link do WhatsApp
 */
function gerarLinkWhatsApp($telefone, $mensagem) {
    $numero = preg_replace('/[^0-9]/', '', $telefone);
    $mensagem_encoded = urlencode($mensagem);
    return "https://wa.me/{$numero}?text={$mensagem_encoded}";
}

/**
 * Gera mensagem padrão para orçamento
 */
function gerarMensagemOrcamento($orcamento, $materiais = [], $configuracoes = []) {
    $taxa_cartao_avista = $configuracoes['taxa_cartao_avista'] ?? 0;
    $taxa_cartao_parcelado = $configuracoes['taxa_cartao_parcelado'] ?? 0;
    
    $valor_total = $orcamento['valor_total'];
    $valor_avista = $valor_total * (1 + ($taxa_cartao_avista / 100));
    $valor_parcelado = $valor_total * (1 + ($taxa_cartao_parcelado / 100));
    
    $mensagem = "📋 *ORÇAMENTO - N&M REFRIGERAÇÃO*\n\n";
    $mensagem .= "👤 *Cliente:* {$orcamento['cliente_nome']}\n";
    $mensagem .= "📞 *Telefone:* {$orcamento['cliente_telefone']}\n";
    $mensagem .= "🔧 *Serviço:* {$orcamento['servico_nome']}\n\n";
    
    if(!empty($materiais)) {
        $mensagem .= "📦 *Materiais Inclusos:*\n";
        foreach($materiais as $material) {
            $mensagem .= "• {$material['nome']} - {$material['quantidade']} {$material['unidade_medida']}\n";
        }
        $mensagem .= "\n";
    }
    
    $mensagem .= "💰 *VALORES:*\n";
    $mensagem .= "💵 *À Vista (Dinheiro/PIX):* " . formatarMoeda($valor_total) . "\n";
    $mensagem .= "💳 *Cartão à Vista:* " . formatarMoeda($valor_avista) . "\n";
    $mensagem .= "💳 *Cartão Parcelado:* " . formatarMoeda($valor_parcelado) . "\n\n";
    
    $mensagem .= "📞 *Entre em contato para agendar:*\n";
    $mensagem .= "WhatsApp: (17) 9 9624-0725\n\n";
    $mensagem .= "📍 *N&M Refrigeração* - Especialistas em conforto térmico!";
    
    return $mensagem;
}

/**
 * Calcula valores com taxas de cartão
 */
function calcularValoresComTaxas($valor_base, $configuracoes) {
    $taxa_avista = $configuracoes['taxa_cartao_avista'] ?? 0;
    $taxa_parcelado = $configuracoes['taxa_cartao_parcelado'] ?? 0;
    
    return [
        'avista' => $valor_base * (1 + ($taxa_avista / 100)),
        'parcelado' => $valor_base * (1 + ($taxa_parcelado / 100)),
        'base' => $valor_base
    ];
}

/**
 * Gera QR Code PIX (simulação - em produção usar biblioteca apropriada)
 */
function gerarQRCodePIX($valor, $chave_pix) {
    // Em produção, usar biblioteca como:
    // https://github.com/renatomb/php-qrcode-pix
    // Por enquanto retornamos uma URL simulada
    $valor_formatado = number_format($valor, 2, '.', '');
    return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=pix:{$chave_pix}?amount={$valor_formatado}";
}

/**
 * Busca configurações do banco
 */
function buscarConfiguracoes($pdo) {
    $configuracoes = [];
    
    // Buscar da tabela configuracoes
    $stmt = $pdo->prepare("SELECT chave, valor FROM configuracoes");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($resultados as $row) {
        $configuracoes[$row['chave']] = $row['valor'];
    }
    
    // Buscar da tabela config_agendamento
    $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($resultados as $row) {
        $configuracoes[$row['chave']] = $row['valor'];
    }
    
    return $configuracoes;
}
?>