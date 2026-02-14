<?php
// includes/config.php
// Arquivo de configuração legado - redireciona para confg.php principal
// Mantido para compatibilidade com código existente

// Inclui o arquivo de configuração principal
require_once __DIR__ . '/../confg.php';

// Variáveis de compatibilidade para código legado que usa essas variáveis
$hostname = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$dbname = DB_NAME;

/**
 * Retorna configurações do site (compatibilidade)
 * @return array Configurações do banco de dados
 */
function getConfigSite() {
    return [
        'hostname' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PASS,
        'dbname' => DB_NAME,
    ];
}
?>