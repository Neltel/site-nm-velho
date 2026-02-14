<?php
// includes/config.php

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'climatech');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do site
define('SITE_NOME', 'ClimaTech');
define('SITE_DESCRICAO', 'Especialistas em Ar Condicionado');
define('SITE_URL', 'http://localhost/climatech');

// Conexão com o banco de dados
$pdo = null;
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Incluir e executar criação de tabelas se necessário
    include_once __DIR__ . '/database.php';
} catch(PDOException $e) {
    // Log the error but don't stop execution
    error_log("Erro de conexão com banco de dados: " . $e->getMessage());
    // Allow the site to continue with default configurations
}

// Funções úteis
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function redirect($url) {
    // Validate URL to prevent open redirect attacks
    if (strpos($url, '://') !== false) {
        // Absolute URL - ensure it's on the same domain
        $parsed = parse_url($url);
        $current_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (!isset($parsed['host']) || $parsed['host'] !== $current_host) {
            // Don't redirect to external domains
            error_log("Attempted redirect to external URL: $url");
            $url = '/';
        }
    }
    header("Location: $url");
    exit;
}

// Validate email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Busca configurações do site do banco de dados
 */
function getConfigSite($pdo) {
    $configuracoes = [];
    
    // If no database connection, return default values
    if (!$pdo) {
        return [
            'site_nome' => SITE_NOME,
            'site_slogan' => SITE_DESCRICAO,
            'site_descricao' => SITE_DESCRICAO,
            'meta_descricao' => SITE_DESCRICAO,
            'meta_titulo' => SITE_NOME,
            'site_telefone' => '(17) 99624-0725',
            'site_email' => 'contato@climatech.com.br',
            'palavras_chave' => 'ar condicionado, instalação, manutenção',
            'whatsapp_ativo' => '1',
            'whatsapp_numero' => '5517996240725',
            'facebook_url' => '',
            'instagram_url' => '',
            'site_logo' => ''
        ];
    }
    
    try {
        // Buscar da tabela configuracoes
        $stmt = $pdo->prepare("SELECT chave, valor FROM configuracoes");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($resultados as $row) {
            $configuracoes[$row['chave']] = $row['valor'];
        }
        
        // Buscar da tabela config_agendamento se existir
        try {
            $stmt = $pdo->prepare("SELECT chave, valor FROM config_agendamento");
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($resultados as $row) {
                $configuracoes[$row['chave']] = $row['valor'];
            }
        } catch(PDOException $e) {
            // Tabela config_agendamento pode não existir ainda
        }
    } catch(PDOException $e) {
        error_log("Erro ao buscar configurações: " . $e->getMessage());
    }
    
    return $configuracoes;
}

/**
 * Retorna serviços padrão quando banco de dados não está disponível
 */
function getDefaultServices() {
    return [
        ['id' => 1, 'nome' => 'Instalação de Ar Condicionado', 'descricao' => 'Instalação completa com material incluído', 'categoria' => 'instalacao'],
        ['id' => 2, 'nome' => 'Manutenção Preventiva', 'descricao' => 'Limpeza e verificação completa do equipamento', 'categoria' => 'manutencao'],
        ['id' => 3, 'nome' => 'Limpeza Técnica Completa', 'descricao' => 'Limpeza interna e externa com produtos específicos', 'categoria' => 'limpeza'],
        ['id' => 4, 'nome' => 'Reparo Técnico Especializado', 'descricao' => 'Diagnóstico e reparo de problemas técnicos', 'categoria' => 'reparo'],
        ['id' => 5, 'nome' => 'Remoção de Equipamento', 'descricao' => 'Remoção segura do ar condicionado', 'categoria' => 'remocao'],
    ];
}
?>