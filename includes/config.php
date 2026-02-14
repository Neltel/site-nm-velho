<?php
// includes/config.php
session_start();

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
?>