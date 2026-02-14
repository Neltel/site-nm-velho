<?php
// admin/includes/config-functions.php

/**
 * Função para obter configuração do site
 */
function getConfig($chave, $padrao = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT valor FROM config_site WHERE chave = ?");
        $stmt->execute([$chave]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['valor'] : $padrao;
    } catch(PDOException $e) {
        error_log("Erro ao buscar configuração {$chave}: " . $e->getMessage());
        return $padrao;
    }
}

/**
 * Função para obter múltiplas configurações
 */
function getConfigs($chaves = []) {
    global $pdo;
    $configs = [];
    
    try {
        if(empty($chaves)) {
            $stmt = $pdo->prepare("SELECT chave, valor FROM config_site");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $placeholders = str_repeat('?,', count($chaves) - 1) . '?';
            $stmt = $pdo->prepare("SELECT chave, valor FROM config_site WHERE chave IN ($placeholders)");
            $stmt->execute($chaves);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        foreach($results as $row) {
            $configs[$row['chave']] = $row['valor'];
        }
        
        return $configs;
    } catch(PDOException $e) {
        error_log("Erro ao buscar configurações: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para atualizar configuração
 */
function updateConfig($chave, $valor) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO config_site (chave, valor) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE valor = ?");
        return $stmt->execute([$chave, $valor, $valor]);
    } catch(PDOException $e) {
        error_log("Erro ao atualizar configuração {$chave}: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter cidades atendidas como array
 */
function getCidadesAtendidas() {
    $cidades_config = getConfig('cidades_atendidas', '');
    $cidades_array = explode("\n", $cidades_config);
    
    return array_filter(array_map('trim', $cidades_array));
}

/**
 * Função para verificar se modo manutenção está ativo
 */
function isModoManutencao() {
    return getConfig('manutencao', '0') === '1';
}

/**
 * Função para obter cores do site como array
 */
function getCoresSite() {
    return [
        'primaria' => getConfig('cor_primaria', '#2563eb'),
        'secundaria' => getConfig('cor_secundaria', '#1e40af'),
        'accent' => getConfig('cor_accent', '#f59e0b'),
        'texto' => getConfig('cor_texto', '#1f2937'),
        'fundo' => getConfig('cor_fundo', '#ffffff'),
        'header' => getConfig('cor_header', '#ffffff'),
        'footer' => getConfig('cor_footer', '#1f2937')
    ];
}

/**
 * Função para gerar CSS das cores dinamicamente
 */
function gerarCSSVariaveisCores() {
    $cores = getCoresSite();
    
    $css = ":root {\n";
    foreach($cores as $nome => $valor) {
        $css .= "  --cor-{$nome}: {$valor};\n";
    }
    $css .= "}\n";
    
    return $css;
}
?>