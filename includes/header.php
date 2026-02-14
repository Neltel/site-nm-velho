
<?php
// includes/header.php - VERSÃO ATUALIZADA COM DESIGN MODERNO

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuração
include_once 'config.php';

// Verificar se a conexão PDO existe
if (!isset($pdo)) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        error_log("Erro de conexão: " . $e->getMessage());
        die("Erro de conexão com o banco de dados");
    }
}

// Buscar configurações do site
try {
    $config_site = getConfigSite($pdo);
} catch(Exception $e) {
    error_log("Erro ao buscar configurações: " . $e->getMessage());
    // Configurações padrão em caso de erro
    $config_site = [
        'site_nome' => 'N&M Refrigeração',
        'site_slogan' => 'Especialistas em Ar Condicionado',
        'site_logo' => '',
        'site_telefone' => '(17) 99624-0725',
        'site_email' => 'contato@climatech.com.br',
        'meta_descricao' => 'Especialistas em ar condicionado em São José do Rio Preto',
        'palavras_chave' => 'ar condicionado, instalação, manutenção',
        'whatsapp_ativo' => '1',
        'whatsapp_numero' => '5517996240725',
        'facebook_url' => '',
        'instagram_url' => ''
    ];
}

// Configurações dinâmicas de SEO por página
$pagina_atual = basename($_SERVER['PHP_SELF']);
$titulo = $descricao = $palavras_chave = '';

// URL canônica
$url_canonical = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

switch($pagina_atual) {
    case 'index.php':
        $titulo = $config_site['meta_titulo'] ?? "Ar Condicionado São José do Rio Preto | " . $config_site['site_nome'];
        $descricao = $config_site['meta_descricao'] ?? $config_site['site_slogan'] . " Instalação, manutenção, limpeza e venda de ar condicionado. Atendemos toda região.";
        $palavras_chave = $config_site['palavras_chave'] ?? "ar condicionado São José do Rio Preto, instalação ar condicionado, manutenção ar condicionado";
        break;
        
    case 'servicos.php':
        $titulo = "Serviços de Ar Condicionado | " . $config_site['site_nome'];
        $descricao = "Serviços completos de ar condicionado: instalação profissional, manutenção preventiva, limpeza técnica e reparos especializados.";
        $palavras_chave = "serviços ar condicionado, manutenção, limpeza, reparos, instalação profissional";
        break;
        
    case 'produtos.php':
        $titulo = "Ar Condicionado à Venda | " . $config_site['site_nome'];
        $descricao = "Compre ar condicionado com os melhores preços. Samsung, LG, Midea, Gree e mais. Instalação inclusa.";
        $palavras_chave = "venda ar condicionado, ar condicionado preço, split, inverter, Samsung, LG, Midea";
        break;
        
    case 'cidades-atendidas.php':
        $titulo = "Cidades Atendidas | " . $config_site['site_nome'];
        $descricao = "Confira todas as cidades onde a " . $config_site['site_nome'] . " oferece serviços de ar condicionado. Atendemos toda região.";
        $palavras_chave = "cidades atendidas, região, São José do Rio Preto, Mirassol, Bady Bassitt";
        break;
        
    case 'contato.php':
        $titulo = "Contato | " . $config_site['site_nome'];
        $descricao = "Entre em contato com a " . $config_site['site_nome'] . " para orçamentos, agendamentos e dúvidas sobre ar condicionado.";
        $palavras_chave = "contato, orçamento, agendamento, telefone, email";
        break;
        
    case 'orcamento.php':
        $titulo = "Solicitar Orçamento | " . $config_site['site_nome'];
        $descricao = "Solicite seu orçamento para instalação, manutenção ou reparo de ar condicionado. Atendemos São José do Rio Preto e região.";
        $palavras_chave = "orçamento, preço, custo, valor instalação, preço manutenção";
        break;
        
    case 'agendamento.php':
        $titulo = "Agendar Serviço | " . $config_site['site_nome'];
        $descricao = "Agende seu serviço de ar condicionado. Instalação, manutenção, limpeza e reparos. Horários flexíveis.";
        $palavras_chave = "agendamento, agendar serviço, horário, visita técnica";
        break;
        
    default:
        $titulo = $config_site['meta_titulo'] ?? $config_site['site_nome'] . " - São José do Rio Preto";
        $descricao = $config_site['meta_descricao'] ?? $config_site['site_slogan'];
        $palavras_chave = $config_site['palavras_chave'] ?? "ar condicionado, refrigeração, climatização, São José do Rio Preto";
}

// Verificar se existe logo
$site_logo = $config_site['site_logo'] ?? '';
$site_nome = $config_site['site_nome'] ?? 'N&M Refrigeração';

// Buscar cidades para o schema
$cidades_text = $config_site['cidades_atendidas'] ?? "São José do Rio Preto\nMirassol\nBady Bassitt\nIpiguá\nJosé Bonifácio";
$cidades_array = explode("\n", $cidades_text);
$cidades_schema = array_slice(array_map('trim', $cidades_array), 0, 10);

// Horário de funcionamento
$horario_funcionamento = $config_site['site_horario_funcionamento'] ?? "Segunda a Sexta: 8h às 18h\nSábado: 8h às 12h";

// Schema Markup otimizado para HVAC Business
$schema_markup = '
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "HVACBusiness",
    "@id": "http://' . $_SERVER['HTTP_HOST'] . '/#organization",
    "name": "' . htmlspecialchars($config_site['site_nome'] ?? 'N&M Refrigeração') . '",
    "description": "' . htmlspecialchars($config_site['site_slogan'] ?? 'Especialistas em Ar Condicionado') . '",
    "url": "http://' . $_SERVER['HTTP_HOST'] . '",
    "telephone": "' . ($config_site['site_telefone'] ?? '(17) 99624-0725') . '",
    "email": "' . ($config_site['site_email'] ?? 'contato@climatech.com.br') . '",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "' . ($config_site['site_endereco'] ?? 'São José do Rio Preto') . '",
        "addressLocality": "São José do Rio Preto",
        "addressRegion": "SP",
        "addressCountry": "BR",
        "postalCode": "15000-000"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": "-20.8116",
        "longitude": "-49.3791"
    },
    "openingHours": [
        "Mo-Fr 08:00-18:00",
        "Sa 08:00-12:00"
    ],
    "areaServed": ' . json_encode($cidades_schema) . ',
    "serviceType": [
        "Air Conditioning Installation",
        "Air Conditioning Repair",
        "Air Conditioning Maintenance",
        "HVAC Services"
    ],
    "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Serviços de Ar Condicionado",
        "itemListElement": [
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Instalação de Ar Condicionado"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Manutenção Preventiva"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Limpeza Técnica"
                }
            },
            {
                "@type": "Offer",
                "itemOffered": {
                    "@type": "Service",
                    "name": "Conserto e Reparo"
                }
            }
        ]
    },
    "sameAs": [
        "' . ($config_site['facebook_url'] ?? '') . '",
        "' . ($config_site['instagram_url'] ?? '') . '",
        "' . ($config_site['whatsapp_url'] ?? '') . '"
    ],
    "priceRange": "$$",
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "' . ($config_site['clientes_atendidos'] ?? '1000') . '"
    },
    "founder": {
        "@type": "Person",
        "name": "Equipe N&M Refrigeração"
    },
    "foundingDate": "2020",
    "numberOfEmployees": {
        "@type": "QuantitativeValue",
        "value": "5"
    },
    "knowsAbout": [
        "Ar Condicionado Split",
        "Ar Condicionado Inverter",
        "Instalação HVAC",
        "Manutenção Preventiva",
        "Limpeza Técnica",
        "Reparo de Compressores",
        "Carga de Gás Refrigerante"
    ]
}
</script>
';

// Breadcrumb Schema
$breadcrumb_schema = '
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Início",
            "item": "http://' . $_SERVER['HTTP_HOST'] . '"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "' . htmlspecialchars($titulo) . '",
            "item": "' . $url_canonical . '"
        }
    ]
}
</script>
';

// Google Analytics Code
$google_analytics = $config_site['google_analytics'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR" itemscope itemtype="https://schema.org/HVACBusiness">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Meta Tags Essenciais para SEO -->
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($descricao); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($palavras_chave); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($config_site['site_nome'] ?? 'N&M Refrigeração'); ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $url_canonical; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $url_canonical; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($titulo); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($descricao); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($config_site['site_nome'] ?? 'N&M Refrigeração'); ?>">
    <?php if($site_logo && file_exists('assets/images/' . $site_logo)): ?>
    <meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/images/<?php echo $site_logo; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <?php endif; ?>
    <meta property="og:locale" content="pt_BR">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $url_canonical; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($titulo); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($descricao); ?>">
    <?php if($site_logo && file_exists('assets/images/' . $site_logo)): ?>
    <meta property="twitter:image" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/images/<?php echo $site_logo; ?>">
    <?php endif; ?>
    
    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="<?php echo $config_site['cor_primaria'] ?? '#0066cc'; ?>">
    <meta name="msapplication-TileColor" content="<?php echo $config_site['cor_primaria'] ?? '#0066cc'; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($config_site['site_nome'] ?? 'N&M Refrigeração'); ?>">
    
    <!-- Schema Markup -->
    <?php echo $schema_markup; ?>
    <?php echo $breadcrumb_schema; ?>
    
    <!-- Favicon -->
    <?php if(isset($config_site['site_favicon']) && !empty($config_site['site_favicon'])): ?>
    <link rel="icon" type="image/x-icon" href="assets/images/<?php echo $config_site['site_favicon']; ?>">
    <link rel="apple-touch-icon" href="assets/images/<?php echo $config_site['site_favicon']; ?>">
    <?php else: ?>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="css/style.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" crossorigin>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="assets/css/dynamic-styles.php?v=<?php echo time(); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Google Analytics -->
    <?php echo $google_analytics; ?>
    
    <!-- Google Maps API -->
    <?php if(isset($config_site['google_maps_api']) && !empty($config_site['google_maps_api'])): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $config_site['google_maps_api']; ?>&libraries=places"></script>
    <?php endif; ?>
    
    <style>
    /* ============================================================================
       ESTILOS MODERNOS PARA HEADER
       Compatíveis com Painel Admin (mantém variáveis CSS)
    ============================================================================ */
    
    /* HEADER MODERNO */
    header {
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 0.98) 0%,
            rgba(255, 255, 255, 0.95) 100%);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    header:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
    }
    
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* LOGO MODERNA */
    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
    }
    
    .logo img {
        max-height: 60px;
        width: auto;
        transition: all 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
    }
    
    .logo:hover img {
        transform: scale(1.05);
    }
    
    .logo-text {
        display: flex;
        flex-direction: column;
    }
    
    .logo-name {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
    }
    
    .logo-slogan {
        font-size: 0.85rem;
        color: var(--primary);
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    /* NAVEGAÇÃO MODERNA */
    nav ul {
        display: flex;
        list-style: none;
        gap: 10px;
        margin: 0;
        padding: 0;
    }
    
    nav ul li {
        position: relative;
    }
    
    nav ul li a {
        text-decoration: none;
        color: var(--dark);
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 20px;
        border-radius: 50px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }
    
    nav ul li a::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 50px;
        z-index: -1;
    }
    
    nav ul li a:hover {
        color: white;
        transform: translateY(-2px);
    }
    
    nav ul li a:hover::before {
        opacity: 1;
    }
    
    nav ul li a.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);
    }
    
    nav ul li a i {
        font-size: 1.1rem;
    }
    
    /* BOTÃO WHATSAPP MODERNO */
    .header-contato .btn {
        background: linear-gradient(135deg, #25D366 0%, #1DA851 100%);
        color: white;
        padding: 14px 28px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        border: none;
        cursor: pointer;
        animation: pulse 2s infinite;
    }
    
    .header-contato .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(37, 211, 102, 0.4);
        animation: none;
    }
    
    .header-contato .btn:active {
        transform: translateY(-1px);
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }
        50% {
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.5);
        }
        100% {
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }
    }
    
    /* MENU MOBILE */
    .menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--dark);
        cursor: pointer;
        padding: 10px;
    }
    
    /* RESPONSIVIDADE */
    @media (max-width: 1024px) {
        .header-container {
            padding: 15px;
        }
        
        nav ul {
            gap: 5px;
        }
        
        nav ul li a {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        
        .logo img {
            max-height: 50px;
        }
        
        .logo-name {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .header-container {
            flex-wrap: wrap;
            padding: 10px 15px;
        }
        
        .menu-toggle {
            display: block;
            order: 2;
        }
        
        nav {
            order: 4;
            width: 100%;
            margin-top: 15px;
            display: none;
        }
        
        nav.active {
            display: block;
        }
        
        nav ul {
            flex-direction: column;
            gap: 10px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        nav ul li a {
            padding: 15px;
            justify-content: center;
            border-radius: 10px;
        }
        
        .header-contato {
            order: 3;
            margin-left: auto;
        }
        
        .header-contato .btn {
            padding: 12px 20px;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .logo-text {
            display: none;
        }
        
        .logo img {
            max-height: 45px;
        }
        
        .header-contato .btn span {
            display: none;
        }
        
        .header-contato .btn i {
            margin: 0;
            font-size: 1.2rem;
        }
    }
    
    /* Adiciona variáveis RGB para efeitos */
    :root {
        --primary-rgb: 0, 102, 204;
        --secondary-rgb: 0, 168, 255;
    }
    </style>
</head>
<body itemscope itemtype="https://schema.org/WebPage">
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=AW-17548161949" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Debug da Logo (REMOVER EM PRODUÇÃO) -->
    <?php if(($config_site['debug_mode'] ?? '0') == '1'): ?>
    <div style="position: fixed; top: 10px; left: 10px; background: #333; color: white; padding: 10px; z-index: 9999; font-size: 12px;" id="debug-logo">
        <strong>Debug Logo:</strong><br>
        Arquivo: <?php echo $site_logo ?? 'Nenhum'; ?><br>
        Base URL: <?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>
    </div>
    <?php endif; ?>

    <!-- Header Moderno -->
    <header role="banner" itemprop="hasPart" itemscope itemtype="https://schema.org/WPHeader">
        <div class="container header-container">
            <a href="index.php" class="logo" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                <?php
                // CÓDIGO DA LOGO ORIGINAL (MANTIDO EXATAMENTE COMO ESTAVA)
                $logo_encontrada = false;
                $logo_base64 = '';

                if($site_logo) {
                    $caminhos_tentar = [
                        'assets/images/' . $site_logo,
                        '../assets/images/' . $site_logo,
                        dirname(__DIR__) . '/assets/images/' . $site_logo,
                        $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' . $site_logo
                    ];
                    
                    foreach($caminhos_tentar as $caminho) {
                        if(file_exists($caminho) && is_readable($caminho)) {
                            $logo_data = @file_get_contents($caminho);
                            if($logo_data !== false && !empty($logo_data)) {
                                $extensao = strtolower(pathinfo($caminho, PATHINFO_EXTENSION));
                                $mime_types = [
                                    'png' => 'image/png',
                                    'jpg' => 'image/jpeg', 
                                    'jpeg' => 'image/jpeg',
                                    'gif' => 'image/gif',
                                    'webp' => 'image/webp',
                                    'svg' => 'image/svg+xml'
                                ];
                                
                                $mime_type = $mime_types[$extensao] ?? 'image/png';
                                $logo_base64 = 'data:' . $mime_type . ';base64,' . base64_encode($logo_data);
                                $logo_encontrada = true;
                                break;
                            }
                        }
                    }
                }

                if(!$logo_encontrada) {
                    $pastas_tentar = [
                        'assets/images/',
                        '../assets/images/',
                        dirname(__DIR__) . '/assets/images/'
                    ];
                    
                    foreach($pastas_tentar as $pasta) {
                        if(file_exists($pasta)) {
                            $arquivos = glob($pasta . 'logo*.{png,jpg,jpeg,gif,webp,svg}', GLOB_BRACE);
                            if(!empty($arquivos)) {
                                $primeira_logo = $arquivos[0];
                                $logo_data = @file_get_contents($primeira_logo);
                                if($logo_data !== false && !empty($logo_data)) {
                                    $extensao = strtolower(pathinfo($primeira_logo, PATHINFO_EXTENSION));
                                    $mime_types = [
                                        'png' => 'image/png',
                                        'jpg' => 'image/jpeg',
                                        'jpeg' => 'image/jpeg', 
                                        'gif' => 'image/gif',
                                        'webp' => 'image/webp',
                                        'svg' => 'image/svg+xml'
                                    ];
                                    $mime_type = $mime_types[$extensao] ?? 'image/png';
                                    $logo_base64 = 'data:' . $mime_type . ';base64,' . base64_encode($logo_data);
                                    $logo_encontrada = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                if($logo_encontrada && !empty($logo_base64)): 
                ?>
                <img src="<?php echo $logo_base64; ?>" alt="<?php echo htmlspecialchars($site_nome); ?>" itemprop="url">
                <meta itemprop="caption" content="<?php echo htmlspecialchars($site_nome); ?>">
                <?php else: ?>
                <!-- FALLBACK: Logo não encontrada, mostrar nome -->
                <div class="logo-text">
                    <div class="logo-name"><?php echo htmlspecialchars($site_nome); ?></div>
                    <div class="logo-slogan">Especialistas em Ar Condicionado</div>
                </div>
                <?php endif; ?>
            </a>
            
            <button class="menu-toggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
                <ul>
                    <li>
                        <a href="index.php" class="<?php echo $pagina_atual == 'index.php' ? 'active' : ''; ?>" itemprop="url">
                            <i class="fas fa-home"></i>
                            <span>Início</span>
                        </a>
                    </li>
                    <li>
                        <a href="sistema-ia.php" class="<?php echo $pagina_atual == 'sistema-ia.php' ? 'active' : ''; ?>" itemprop="url">
                            <i class="fas fa-calendar-check"></i>
                            <span>Solicitar Orçamento</span>
                        </a>
                    </li>
                    <li>
                        <a href="cidades-atendidas.php" class="<?php echo $pagina_atual == 'cidades-atendidas.php' ? 'active' : ''; ?>" itemprop="url">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Cidades Atendidas</span>
                        </a>
                    </li>
                    <li>
                        <a href="contato.php" class="<?php echo $pagina_atual == 'contato.php' ? 'active' : ''; ?>" itemprop="url">
                            <i class="fas fa-headset"></i>
                            <span>Contato</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="header-contato">
                <?php if(($config_site['whatsapp_ativo'] ?? '0') == '1'): ?>
                <a href="https://wa.me/<?php echo $config_site['whatsapp_numero'] ?? ''; ?>" class="btn" target="_blank" itemprop="telephone" content="<?php echo $config_site['whatsapp_numero'] ?? ''; ?>">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
    // Menu Mobile Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('nav');
        
        if (menuToggle && nav) {
            menuToggle.addEventListener('click', function() {
                nav.classList.toggle('active');
                menuToggle.innerHTML = nav.classList.contains('active') 
                    ? '<i class="fas fa-times"></i>' 
                    : '<i class="fas fa-bars"></i>';
            });
            
            // Fechar menu ao clicar fora
            document.addEventListener('click', function(event) {
                if (!nav.contains(event.target) && !menuToggle.contains(event.target)) {
                    nav.classList.remove('active');
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });
        }
        
        // Header scroll effect
        let lastScroll = 0;
        const header = document.querySelector('header');
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%)';
            }
            
            lastScroll = currentScroll;
        });
    });
    </script>

    <main role="main" itemprop="mainContentOfPage">