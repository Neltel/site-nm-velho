<?php
// admin/includes/auth.php
session_start();
if(!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true){
    header('Location: login.php');
    exit;
}
?>