<?php
// assets/css/dynamic-styles.php

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'climatech';
$username = 'root';
$password = '';

header('Content-Type: text/css');

try {
    // Conexão com o banco
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
    // Fallback para valores padrão se houver erro
    $config = [
        'cor_primaria' => '#2563eb',
        'cor_secundaria' => '#1e40af',
        'cor_accent' => '#f59e0b',
        'cor_texto' => '#1f2937',
        'cor_fundo' => '#ffffff'
    ];
}

// Converter cores HEX para RGB
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}
?>

:root {
    /* Cores principais */
    --primary: <?php echo $config['cor_primaria'] ?? '#2563eb'; ?>;
    --secondary: <?php echo $config['cor_secundaria'] ?? '#1e40af'; ?>;
    --accent: <?php echo $config['cor_accent'] ?? '#f59e0b'; ?>;
    --text: <?php echo $config['cor_texto'] ?? '#1f2937'; ?>;
    --background: <?php echo $config['cor_fundo'] ?? '#ffffff'; ?>;
    
    /* Cores RGB para efeitos */
    --primary-rgb: <?php echo hexToRgb($config['cor_primaria'] ?? '#2563eb'); ?>;
    --secondary-rgb: <?php echo hexToRgb($config['cor_secundaria'] ?? '#1e40af'); ?>;
    --accent-rgb: <?php echo hexToRgb($config['cor_accent'] ?? '#f59e0b'); ?>;
}

/* ===== ESTILOS DINÂMICOS ===== */

/* Header e Navegação */
.logo h1 {
    color: var(--primary) !important;
}

nav ul li a:hover {
    color: var(--primary) !important;
}

/* Botões */
.btn {
    background: var(--primary) !important;
    border: 2px solid var(--primary) !important;
}

.btn:hover {
    background: var(--secondary) !important;
    border-color: var(--secondary) !important;
}

.btn-accent {
    background: var(--accent) !important;
    border-color: var(--accent) !important;
}

.btn-accent:hover {
    background: var(--accent) !important;
    opacity: 0.9 !important;
    filter: brightness(0.9) !important;
}

.btn-outline {
    background: transparent !important;
    color: var(--primary) !important;
    border: 2px solid var(--primary) !important;
}

.btn-outline:hover {
    background: var(--primary) !important;
    color: white !important;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
}

/* Cards de Serviços e Produtos */
.service-card:hover,
.product-card:hover {
    border-color: var(--primary) !important;
    box-shadow: 0 5px 20px rgba(var(--primary-rgb), 0.15) !important;
}

.service-icon,
.contact-icon {
    color: var(--primary) !important;
    background: rgba(var(--primary-rgb), 0.1) !important;
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

/* Formulários */
.form-control:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1) !important;
}

/* Filtros */
.btn-filter.active {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: white !important;
}

/* Alertas */
.alert-info {
    border-left-color: var(--primary) !important;
    background: rgba(var(--primary-rgb), 0.1) !important;
}

/* Footer */
footer {
    background: var(--text) !important;
}

/* Efeitos de hover específicos */
.service-card:hover .service-icon,
.product-card:hover .product-image {
    transform: scale(1.05) !important;
    transition: all 0.3s ease !important;
}

/* Responsividade das cores */
@media (max-width: 768px) {
    .hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 120%) !important;
    }
}