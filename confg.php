<?php
// confg.php - Configuração principal do sistema
// Arquivo de configuração centralizado para todo o sistema

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// ==========================================
define('DB_HOST', 'localhost');          // Host do banco de dados
define('DB_NAME', 'nmrefrig_imperio');   // Nome do banco de dados
define('DB_USER', 'nmrefrig_imperio');   // Usuário do banco
define('DB_PASS', 'JEJ5qnvpLRbACP7tUhu6'); // Senha do banco

// ==========================================
// CONFIGURAÇÕES DO SITE
// ==========================================
define('SITE_NOME', 'N&M Refrigeração');
define('SITE_DESCRICAO', 'Sistema Integrado de Gestão - Ar Condicionado e Refrigeração');
define('SITE_URL', 'http://localhost'); // Ajustar para URL de produção

// ==========================================
// CONFIGURAÇÕES DE SEGURANÇA
// ==========================================
define('CSRF_TOKEN_TIME', 3600); // Tempo de validade do token CSRF em segundos
define('SESSION_TIMEOUT', 1800); // Timeout de sessão em segundos (30 minutos)
define('MAX_LOGIN_ATTEMPTS', 5); // Máximo de tentativas de login
define('LOGIN_TIMEOUT', 900); // Tempo de bloqueio após tentativas falhas (15 minutos)

// ==========================================
// CONFIGURAÇÕES DE UPLOAD
// ==========================================
define('UPLOAD_MAX_SIZE', 5242880); // 5MB em bytes
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// ==========================================
// CONFIGURAÇÕES DE LOGS
// ==========================================
define('LOG_PATH', __DIR__ . '/logs/');
define('LOG_ENABLED', true);

// ==========================================
// CONEXÃO COM O BANCO DE DADOS (PDO)
// ==========================================
try {
    // Cria conexão PDO com charset UTF-8
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de erro: exceções
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de fetch padrão: array associativo
            PDO::ATTR_EMULATE_PREPARES => false, // Desabilita emulação de prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // Define charset
        ]
    );
} catch(PDOException $e) {
    // Em produção, não expor detalhes do erro
    error_log("Erro de conexão com banco de dados: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// ==========================================
// CONEXÃO MYSQLI (para compatibilidade com código legado)
// ==========================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    error_log("Erro de conexão MySQLi: " . $conn->connect_error);
    die("Erro ao conectar ao banco de dados.");
}
$conn->set_charset("utf8mb4");

// ==========================================
// FUNÇÕES ÚTEIS DE SEGURANÇA
// ==========================================

/**
 * Sanitiza dados de entrada para prevenir XSS
 * @param mixed $data Dados a serem sanitizados
 * @return mixed Dados sanitizados
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitiza dados para uso em SQL (usa PDO preferencialmente)
 * @param string $data Dados a serem escapados
 * @return string Dados escapados
 */
function escapeSql($data) {
    global $conn;
    return $conn->real_escape_string($data);
}

/**
 * Valida email
 * @param string $email Email a ser validado
 * @return bool True se válido, false caso contrário
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida telefone brasileiro
 * @param string $telefone Telefone a ser validado
 * @return bool True se válido, false caso contrário
 */
function validarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    return strlen($telefone) >= 10 && strlen($telefone) <= 11;
}

/**
 * Formata valor monetário para exibição
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata data para padrão brasileiro
 * @param string $data Data no formato Y-m-d
 * @return string Data formatada em d/m/Y
 */
function formatarData($data) {
    if (empty($data)) return '';
    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt ? $dt->format('d/m/Y') : $data;
}

/**
 * Formata data e hora para padrão brasileiro
 * @param string $dataHora Data/hora no formato Y-m-d H:i:s
 * @return string Data/hora formatada em d/m/Y H:i
 */
function formatarDataHora($dataHora) {
    if (empty($dataHora)) return '';
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $dataHora);
    return $dt ? $dt->format('d/m/Y H:i') : $dataHora;
}

/**
 * Redireciona para URL
 * @param string $url URL de destino
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Gera token CSRF
 * @return string Token gerado
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token']) || 
        empty($_SESSION['csrf_token_time']) || 
        time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_TIME) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 * @param string $token Token a ser validado
 * @return bool True se válido, false caso contrário
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || 
        empty($_SESSION['csrf_token_time']) || 
        time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_TIME) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Registra log de sistema
 * @param string $tipo Tipo de log (info, warning, error, security)
 * @param string $mensagem Mensagem do log
 * @param array $dados Dados adicionais (opcional)
 */
function registrarLog($tipo, $mensagem, $dados = []) {
    if (!LOG_ENABLED) return;
    
    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $user = $_SESSION['admin_nome'] ?? $_SESSION['cliente_nome'] ?? 'Anônimo';
    
    $logEntry = sprintf(
        "[%s] [%s] [IP: %s] [User: %s] %s",
        $timestamp,
        strtoupper($tipo),
        $ip,
        $user,
        $mensagem
    );
    
    if (!empty($dados)) {
        $logEntry .= ' | Dados: ' . json_encode($dados, JSON_UNESCAPED_UNICODE);
    }
    
    $logEntry .= PHP_EOL;
    
    // Cria diretório de logs se não existir
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Verifica se usuário está autenticado como admin
 * @return bool True se autenticado, false caso contrário
 */
function isAdminLogado() {
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

/**
 * Verifica se cliente está autenticado
 * @return bool True se autenticado, false caso contrário
 */
function isClienteLogado() {
    return isset($_SESSION['cliente_logado']) && $_SESSION['cliente_logado'] === true;
}

/**
 * Requer autenticação de admin (redireciona se não autenticado)
 */
function requireAdminAuth() {
    if (!isAdminLogado()) {
        registrarLog('security', 'Tentativa de acesso não autorizado à área admin');
        redirect('/admin/login.php');
    }
}

/**
 * Requer autenticação de cliente (redireciona se não autenticado)
 */
function requireClienteAuth() {
    if (!isClienteLogado()) {
        registrarLog('security', 'Tentativa de acesso não autorizado à área do cliente');
        redirect('/cliente/login.php');
    }
}

// ==========================================
// HEADERS DE SEGURANÇA
// ==========================================
header('X-Content-Type-Options: nosniff'); // Previne MIME sniffing
header('X-XSS-Protection: 1; mode=block'); // Proteção XSS
header('X-Frame-Options: SAMEORIGIN'); // Previne clickjacking
header('Referrer-Policy: strict-origin-when-cross-origin'); // Controla envio de referrer

// ==========================================
// TIMEZONE
// ==========================================
date_default_timezone_set('America/Sao_Paulo'); // Define timezone para Brasil

?>