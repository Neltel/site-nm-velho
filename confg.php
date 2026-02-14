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
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
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
?>