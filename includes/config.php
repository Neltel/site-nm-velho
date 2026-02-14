<?php
// includes/config.php - VERSÃO CORRIGIDA SEM FUNÇÕES DUPLICADAS

// Configurações para hospedagem
$is_localhost = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                 strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

if ($is_localhost) {
    // Configurações locais
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nmrefrig_climatech');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('SITE_URL', 'http://localhost/climatech');
} else {
    // Configurações da hospedagem - COM HTTPS
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nmrefrig_climatech');
    define('DB_USER', 'nmrefrig_climatech');
    define('DB_PASS', 'JTa2!qI@Zx0a94');
    define('SITE_URL', 'https://climatech-sjrp.com.br');
}

// Configurações do site (valores padrão - serão sobrescritos pelo banco)
define('SITE_NOME', 'N&M Refrigeração');
define('SITE_DESCRICAO', 'Especialista em ar condicionado em São José do Rio Preto. Instalação, manutenção, limpeza e venda de ar condicionado. Atendemos toda região.');
define('SITE_TELEFONE', '(17) 9 9624-0725');
define('SITE_EMAIL', 'contato@climatech-sjrp.com.br');

// CORREÇÃO: Mover configurações de sessão ANTES de session_start()
$session_path = '/home/nmrefrig/tmp';
if (!is_dir($session_path)) {
    if (!mkdir($session_path, 0755, true)) {
        $session_path = '/tmp';
    }
}

// Definir caminho da sessão ANTES de iniciar a sessão
ini_set('session.save_path', $session_path);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 0);

// Iniciar sessão APÓS configurar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Definir timezone para Brasil
date_default_timezone_set('America/Sao_Paulo');

// ============================================================================
// FUNÇÕES DE CONFIGURAÇÃO (APENAS AS ESSENCIAIS - REMOVIDAS AS DUPLICADAS)
// ============================================================================

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
        'primaria' => getConfig('cor_primaria', '#6a74e6'),
        'secundaria' => getConfig('cor_secundaria', '#3a1dc9'),
        'accent' => getConfig('cor_accent', '#0080ff'),
        'texto' => getConfig('cor_texto', '#000084'),
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

// ============================================================================
// FUNÇÕES EXISTENTES (MANTIDAS PARA COMPATIBILIDADE)
// ============================================================================

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function formatarMoeda($valor) {
    if($valor == 0 || $valor == '') return 'R$ 0,00';
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if(strlen($telefone) == 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    } elseif(strlen($telefone) == 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }
    return $telefone;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Função para buscar configurações do site (compatibilidade)
function getConfigSite($pdo) {
    return getConfigs();
}

// ============================================================================
// INICIALIZAÇÃO DO SISTEMA
// ============================================================================

// Verificar modo manutenção
if(isModoManutencao() && !isset($_SESSION['admin_id'])) {
    $mensagem = getConfig('manutencao_mensagem', 'Site em manutenção. Volte em breve!');
    die("<h1>🚧 Site em Manutenção</h1><p>{$mensagem}</p>");
}

// Carregar configurações essenciais para constantes
$configs_site = getConfigs([
    'site_nome', 'site_slogan', 'site_logo', 'site_telefone', 'site_email',
    'meta_titulo', 'meta_descricao', 'whatsapp_ativo', 'site_whatsapp'
]);

// Atualizar constantes com valores do banco (apenas se não foram definidas)
if(!defined('SITE_NOME_UPDATED')) {
    if(!empty($configs_site['site_nome'])) {
        define('SITE_NOME', $configs_site['site_nome']);
        define('SITE_NOME_UPDATED', true);
    }
}

if(!defined('SITE_DESCRICAO_UPDATED')) {
    if(!empty($configs_site['site_slogan'])) {
        define('SITE_DESCRICAO', $configs_site['site_slogan']);
        define('SITE_DESCRICAO_UPDATED', true);
    }
}

if(!defined('SITE_TELEFONE_UPDATED')) {
    if(!empty($configs_site['site_telefone'])) {
        define('SITE_TELEFONE', $configs_site['site_telefone']);
        define('SITE_TELEFONE_UPDATED', true);
    }
}

if(!defined('SITE_EMAIL_UPDATED')) {
    if(!empty($configs_site['site_email'])) {
        define('SITE_EMAIL', $configs_site['site_email']);
        define('SITE_EMAIL_UPDATED', true);
    }
}
?>