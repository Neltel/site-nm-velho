<?php
// assets/css/config-styles.php
header('Content-Type: text/css');

include '../includes/config.php';

// Buscar cores do banco de dados
try {
    $stmt = $pdo->prepare("SELECT chave, valor FROM config_site WHERE chave LIKE 'cor_%'");
    $stmt->execute();
    $cores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch(PDOException $e) {
    // Cores padrão em caso de erro
    $cores = [
        'cor_primaria' => '#2563eb',
        'cor_secundaria' => '#1e40af',
        'cor_accent' => '#f59e0b',
        'cor_texto' => '#1f2937',
        'cor_fundo' => '#ffffff',
        'cor_header' => '#ffffff',
        'cor_footer' => '#1f2937'
    ];
}
?>

/* Variáveis CSS dinâmicas baseadas nas configurações */
:root {
    --primary: <?php echo $cores['cor_primaria'] ?? '#2563eb'; ?>;
    --secondary: <?php echo $cores['cor_secundaria'] ?? '#1e40af'; ?>;
    --accent: <?php echo $cores['cor_accent'] ?? '#f59e0b'; ?>;
    --text: <?php echo $cores['cor_texto'] ?? '#1f2937'; ?>;
    --background: <?php echo $cores['cor_fundo'] ?? '#ffffff'; ?>;
    --header-bg: <?php echo $cores['cor_header'] ?? '#ffffff'; ?>;
    --footer-bg: <?php echo $cores['cor_footer'] ?? '#1f2937'; ?>;
    
    /* Cores derivadas */
    --primary-light: <?php echo adjustBrightness($cores['cor_primaria'] ?? '#2563eb', 30); ?>;
    --primary-dark: <?php echo adjustBrightness($cores['cor_primaria'] ?? '#2563eb', -20); ?>;
    --text-light: <?php echo adjustBrightness($cores['cor_texto'] ?? '#1f2937', 40); ?>;
}

/* Estilos baseados nas configurações */
body {
    background-color: var(--background);
    color: var(--text);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
    background-color: var(--header-bg);
    border-bottom: 2px solid var(--primary);
}

.footer {
    background-color: var(--footer-bg);
    color: white;
}

.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

.text-primary {
    color: var(--primary) !important;
}

.bg-primary {
    background-color: var(--primary) !important;
}

/* Utilitários para cores dinâmicas */
.primary-gradient {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
}

.accent-border {
    border-left: 4px solid var(--accent);
}

<?php
// Função para ajustar brilho da cor (usada acima)
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    
    if(strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + $r * $percent / 100));
    $g = max(0, min(255, $g + $g * $percent / 100));
    $b = max(0, min(255, $b + $b * $percent / 100));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) 
               . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) 
               . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
?>