<?php
// assets/css/dynamic-styles.php

// Configuração básica do banco de dados (sem depender do config.php)
$host = 'localhost';
$dbname = 'climatech';
$username = 'root';
$password = '';

header('Content-Type: text/css');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar configurações do site
    $stmt = $pdo->prepare("SELECT chave, valor FROM config_site");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $config = [];
    foreach($resultados as $row) {
        $config[$row['chave']] = $row['valor'];
    }
    
} catch(PDOException $e) {
    // Fallback para valores padrão
    $config = [
        'cor_primaria' => '#2563eb',
        'cor_secundaria' => '#1e40af',
        'cor_accent' => '#f59e0b',
        'cor_texto' => '#1f2937',
        'cor_fundo' => '#ffffff'
    ];
}
?>

:root {
    --primary: <?php echo $config['cor_primaria'] ?? '#2563eb'; ?>;
    --secondary: <?php echo $config['cor_secundaria'] ?? '#1e40af'; ?>;
    --accent: <?php echo $config['cor_accent'] ?? '#f59e0b'; ?>;
    --text: <?php echo $config['cor_texto'] ?? '#1f2937'; ?>;
    --background: <?php echo $config['cor_fundo'] ?? '#ffffff'; ?>;
}

/* Aplicar as cores dinamicamente */
.hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
}

.btn {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}

.btn:hover {
    background: var(--secondary) !important;
    border-color: var(--secondary) !important;
}

.btn-accent {
    background: #2563eb !important;
  border-color: #253f77 !important;
}

.btn-accent:hover {
    background: #253f77 !important;
    opacity: 0.9 !important;
}

.service-icon,
.contact-icon {
    color: var(--primary) !important;
}

.service-content h3,
.product-content h3 {
    color: var(--primary) !important;
}

.product-price,
.service-content .preco {
    color: var(--accent) !important;
    font-weight: bold !important;
}

.logo h1 {
    color: var(--primary) !important;
}

nav ul li a:hover {
    color: var(--primary) !important;
}

.service-card:hover {
    border-color: var(--primary) !important;
}

.product-card:hover {
    border-color: var(--primary) !important;
}

.form-control:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.1) !important;
}

/* Adicionar variáveis RGB para efeitos */
:root {
    --primary-rgb: <?php 
        $primaria = $config['cor_primaria'] ?? '#2563eb';
        list($r, $g, $b) = sscanf($primaria, "#%02x%02x%02x");
        echo "$r, $g, $b";
    ?>;
}