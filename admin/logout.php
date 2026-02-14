<?php
/**
 * admin/logout.php
 * 
 * Script de logout seguro
 * Destrói a sessão e limpa todos os cookies relacionados
 */

// Inclui configuração
require_once '../confg.php';

// Registrar logout no log antes de destruir a sessão
if(isAdminLogado()) {
    registrarLog('info', 'Logout realizado', [
        'usuario' => $_SESSION['admin_usuario'] ?? 'desconhecido'
    ]);
}

// Limpar todas as variáveis de sessão
$_SESSION = array();

// Destruir o cookie de sessão se existir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destruir a sessão
session_destroy();

// Redirecionar para login
redirect('login.php');
?>
