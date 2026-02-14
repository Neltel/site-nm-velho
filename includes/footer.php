
<?php
// includes/footer.php - VERSÃO ATUALIZADA COM DESIGN MODERNO

// Buscar configurações do site se não estiver definido
if (!isset($config_site)) {
    include_once 'config.php';
    $config_site = getConfigSite($pdo);
}

// Verificar se existe logo - ADICIONAR ESTA PARTE
$logo_file = $config_site['site_logo'] ?? '';
$logo_path = __DIR__ . '../assets/images/' . $logo_file;

// URL para a logo - CORREÇÃO
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($base_url, '/\\');
$logo_url = (!empty($logo_file) && file_exists($logo_path)) ? $base_url . '../assets/images/' . $logo_file : null;

// Se a URL absoluta não funcionar, tente esta versão relativa:
//$logo_url = (!empty($logo_file) && file_exists($logo_path)) ? 'assets/images/' . $logo_file : null;
?>
    <!-- Footer Moderno -->
    <footer class="footer-modern">
        <div class="container">
            <!-- Wave Top -->
            
            
            <!-- Main Footer Content -->
            <div class="footer-main">
                <div class="footer-grid-modern">
                    <!-- Coluna 1: Logo e Sobre -->
                    <div class="footer-column-modern">
                        <div class="footer-brand">
                            <div class="footer-logo-wrapper">
                                <?php
                                // CÓDIGO DA LOGO ORIGINAL (MANTIDO EXATAMENTE COMO ESTAVA)
                                $logo_encontrada = false;
                                $logo_base64 = '';

                                // CORREÇÃO: Usar $config_site em vez de $site_logo
                                $site_logo_footer = $config_site['site_logo'] ?? '';

                                if($site_logo_footer) {
                                    $caminhos_tentar = [
                                        'assets/images/' . $site_logo_footer,
                                        '../assets/images/' . $site_logo_footer,
                                        dirname(__DIR__) . '/assets/images/' . $site_logo_footer,
                                        $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' . $site_logo_footer
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
                                <img src="<?php echo $logo_base64; ?>" alt="<?php echo $config_site['site_nome']; ?>" class="footer-logo">
                                <?php else: ?>
                                <!-- FALLBACK: Logo não encontrada, mostrar nome -->
                                <div class="footer-logo-text">
                                    <h3><?php echo $config_site['site_nome']; ?></h3>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="brand-info">
                                <h3 class="brand-name"><?php echo $config_site['site_nome']; ?></h3>
                                <p class="brand-slogan"><?php echo $config_site['site_slogan']; ?> com anos de experiência atendendo São José do Rio Preto e região.</p>
                            </div>
                        </div>
                        
                        <div class="footer-about">
                            <p>Especialistas em climatização, oferecendo soluções completas em ar condicionado com qualidade e garantia.</p>
                        </div>
                        
                        <div class="footer-social">
                            <h4>Siga-nos</h4>
                            <div class="social-icons">
                                <?php if(!empty($config_site['facebook_url'])): ?>
                                <a href="<?php echo $config_site['facebook_url']; ?>" class="social-icon" target="_blank" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if(!empty($config_site['instagram_url'])): ?>
                                <a href="<?php echo $config_site['instagram_url']; ?>" class="social-icon" target="_blank" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if(!empty($config_site['tiktok_url'])): ?>
                                <a href="<?php echo $config_site['tiktok_url']; ?>" class="social-icon" target="_blank" title="TikTok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if(!empty($config_site['whatsapp_numero']) && $config_site['whatsapp_ativo'] == '1'): ?>
                                <a href="https://wa.me/<?php echo $config_site['whatsapp_numero']; ?>" class="social-icon whatsapp-icon" target="_blank" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna 2: Serviços -->
                    <div class="footer-column-modern">
                        <h3 class="footer-title">Nossos Serviços</h3>
                        <ul class="footer-links">
                            <li>
                                <a href="sistema-ia.php?servico=5">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Instalação de Ar Condicionado</span>
                                </a>
                            </li>
                            <li>
                                <a href="sistema-ia.php?servico=6">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Limpeza Completa</span>
                                </a>
                            </li>
                            <li>
                                <a href="sistema-ia.php?servico=7">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Limpeza Técnica</span>
                                </a>
                            </li>
                            <li>
                                <a href="sistema-ia.php?servico=8">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Manutenção e Reparo</span>
                                </a>
                            </li>
                            <li>
                                <a href="sistema-ia.php?servico=9">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Remoção de Equipamentos</span>
                                </a>
                            </li>
                        </ul>
                        
                        <?php if($config_site['agendamento_online'] == '1'): ?>
                        <div class="footer-cta">
                            <a href="sistema-ia.php" class="btn-footer-agendar">
                                <i class="fas fa-calendar-alt"></i>
                                Agendar Serviço Online
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Coluna 3: Contato -->
                    <div class="footer-column-modern">
                        <h3 class="footer-title">Entre em Contato</h3>
                        <ul class="footer-contact">
                            <?php if(!empty($config_site['site_telefone'])): ?>
                            <li class="contact-item" style="background: #3a3c49;">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Telefone</span>
                                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $config_site['site_telefone']); ?>" class="contact-value">
                                        <?php echo $config_site['site_telefone']; ?>
                                    </a>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($config_site['site_telefone2'])): ?>
                            <li class="contact-item" style="background: #3a3c49;">
                                <div class="contact-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Celular</span>
                                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $config_site['site_telefone2']); ?>" class="contact-value">
                                        <?php echo $config_site['site_telefone2']; ?>
                                    </a>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($config_site['site_email'])): ?>
                            <li class="contact-item" style="background: #3a3c49;">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">E-mail</span>
                                    <a href="mailto:<?php echo $config_site['site_email']; ?>" class="contact-value">
                                        <?php echo $config_site['site_email']; ?>
                                    </a>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($config_site['site_endereco'])): ?>
                            <li class="contact-item" style="background: #3a3c49;">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Endereço</span>
                                    <span class="contact-value"><?php echo $config_site['site_endereco']; ?></span>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <li class="contact-item" style="background: #3a3c49;">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Horário</span>
                                    <span class="contact-value">Seg-Sex: 8h às 18h | Sáb: 8h às 12h</span>
                                </div>
                            </li>
                        </ul>
                        
                        <?php if(!empty($config_site['whatsapp_numero']) && $config_site['whatsapp_ativo'] == '1'): ?>
                        <div class="footer-whatsapp">
                            <a href="https://wa.me/<?php echo $config_site['whatsapp_numero']; ?>" class="btn-footer-whatsapp" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                                Falar no WhatsApp
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Coluna 4: Cidades -->
                    <div class="footer-column-modern">
                        <h3 class="footer-title">Cidades Atendidas</h3>
                        <div class="footer-cities">
                            <?php
                            // Buscar cidades atendidas
                            $cidades_text = $config_site['cidades_atendidas'] ?? "São José do Rio Preto\nMirassol\nBady Bassitt\nIpiguá\nJosé Bonifácio\nNova Granada\nMirassolândia\nTanabi\nUchoa\nCedral";
                            $cidades_array = explode("\n", $cidades_text);
                            $cidades_array = array_filter(array_map('trim', $cidades_array));
                            $max_cidades = 8; // Mostrar no máximo 8 cidades
                            
                            for($i = 0; $i < min($max_cidades, count($cidades_array)); $i++) {
                                $cidade = trim($cidades_array[$i]);
                                if($cidade): ?>
                                <span class="city-tag"><?php echo $cidade; ?></span>
                            <?php endif; } ?>
                            
                            <?php if(count($cidades_array) > $max_cidades): ?>
                            <span class="city-tag more">+<?php echo count($cidades_array) - $max_cidades; ?> cidades</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="footer-map-link">
                            <a href="cidades-atendidas.php" class="btn-footer-map">
                                <i class="fas fa-map-marked-alt"></i>
                                Ver Todas as Cidades
                            </a>
                        </div>
                        
                        <div class="footer-newsletter">
                            <h4>Receba Ofertas</h4>
                            <p>Cadastre-se para receber promoções exclusivas!</p>
                            <form class="newsletter-form">
                                <input type="email" placeholder="Seu melhor e-mail" required>
                                <button type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="footer-bottom">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $config_site['site_nome']; ?>. Todos os direitos reservados.</p>
                    <p class="dev-credit">Desenvolvido com ❤️ para seu conforto térmico</p>
                </div>
                
                <?php if($config_site['manutencao'] == '1'): ?>
                <div class="maintenance-notice">
                    <i class="fas fa-tools"></i>
                    <span>Site em modo de manutenção</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <!-- Botão Flutuante do WhatsApp -->
    <?php if(!empty($config_site['whatsapp_numero']) && $config_site['whatsapp_ativo'] == '1'): ?>
    <a href="https://wa.me/<?php echo $config_site['whatsapp_numero']; ?>" 
       class="whatsapp-float" 
       target="_blank" 
       title="Fale conosco no WhatsApp"
       aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="float-pulse"></span>
    </a>
    <?php endif; ?>

    <!-- Botão Voltar ao Topo -->
    <button class="back-to-top" aria-label="Voltar ao topo">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="js/script.js"></script>
    
    <!-- Google Analytics -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17548161949"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-17548161949');
    </script>

    <style>
    /* ============================================================================
       ESTILOS MODERNOS PARA FOOTER
       Compatíveis com Painel Admin (mantém variáveis CSS)
    ============================================================================ */
    
    /* FOOTER MODERNO */
    .footer-modern {
        background: linear-gradient(135deg, 
            var(--dark) 0%,
            #1a1a2e 100%);
        color: white;
        position: relative;
        padding-top: 80px;
        margin-top: 100px;
    }
    
    .footer-wave {
        position: absolute;
        top: -60px;
        left: 0;
        width: 100%;
        height: 80px;
        color: var(--dark);
        transform: rotate(180deg);
    }
    
    .footer-main {
        padding: 60px 0 40px;
    }
    
    .footer-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }
    
    /* COLUNAS */
    .footer-column-modern {
        padding: 0 15px;
    }
    
    .footer-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 10px;
        color: white;
    }
    
    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 2px;
    }
    
    /* BRAND SECTION */
    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .footer-logo-wrapper {
        max-width: 200px;
    }
    
    .footer-logo {
        max-height: 80px;
        width: auto;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        transition: all 0.3s ease;
    }
    
    .footer-logo:hover {
        transform: scale(1.05);
    }
    
    .brand-name {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 5px;
        background: linear-gradient(90deg, white 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .brand-slogan {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    /* ABOUT */
    .footer-about {
        margin-bottom: 30px;
    }
    
    .footer-about p {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
        font-size: 0.95rem;
    }
    
    /* SOCIAL ICONS */
    .footer-social h4 {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: white;
    }
    
    .social-icons {
        display: flex;
        gap: 15px;
    }
    
    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: white;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .social-icon:hover {
        background: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);
    }
    
    .whatsapp-icon:hover {
        background: #25D366;
    }
    
    /* LINKS */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 12px;
    }
    
    .footer-links a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 8px 0;
    }
    
    .footer-links a:hover {
        color: var(--primary);
        transform: translateX(5px);
    }
    
    .footer-links a i {
        color: var(--primary);
        font-size: 0.8rem;
        transition: all 0.3s ease;
    }
    
    .footer-links a:hover i {
        transform: translateX(3px);
    }
    
    /* CONTACT */
    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0 0 30px 0;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    
    .contact-info {
        flex: 1;
    }
    
    .contact-label {
        display: block;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 3px;
    }
    
    .contact-value {
        display: block;
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .contact-value:hover {
        color: var(--primary);
    }
    
    /* CITIES */
    .footer-cities {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 30px;
    }
    
    .city-tag {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .city-tag:hover {
        background: var(--primary);
        transform: translateY(-2px);
    }
    
    .city-tag.more {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        font-weight: 600;
    }
    
    /* BUTTONS */
    .btn-footer-agendar {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 14px 25px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 15px;
        width: 100%;
        justify-content: center;
    }
    
    .btn-footer-agendar:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.3);
    }
    
    .btn-footer-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #25D366 0%, #1DA851 100%);
        color: white;
        padding: 14px 25px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
    }
    
    .btn-footer-whatsapp:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
    }
    
    .btn-footer-map {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
        margin-bottom: 25px;
    }
    
    .btn-footer-map:hover {
        background: var(--primary);
        transform: translateY(-3px);
    }
    
    /* NEWSLETTER */
    .footer-newsletter h4 {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: white;
    }
    
    .footer-newsletter p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    
    .newsletter-form {
        display: flex;
        gap: 5px;
    }
    
    .newsletter-form input {
        flex: 1;
        padding: 12px 15px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 0.9rem;
    }
    
    .newsletter-form input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .newsletter-form button {
        background: var(--primary);
        color: white;
        border: none;
        width: 50px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .newsletter-form button:hover {
        background: var(--secondary);
        transform: scale(1.05);
    }
    
    /* BOTTOM */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 30px 0;
        text-align: center;
    }
    
    .copyright {
        margin-bottom: 15px;
    }
    
    .copyright p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        margin: 5px 0;
    }
    
    .dev-credit {
        color: var(--primary) !important;
        font-weight: 500;
    }
    
    .maintenance-notice {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 235, 59, 0.1);
        color: #ffeb3b;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 235, 59, 0.3);
    }
    
    /* WHATSAPP FLOAT */
    .whatsapp-float {
        position: fixed;
        width: 70px;
        height: 70px;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #25D366 0%, #1DA851 100%);
        color: white;
        border-radius: 50%;
        text-align: center;
        font-size: 32px;
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .whatsapp-float:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 30px rgba(37, 211, 102, 0.6);
    }
    
    .float-pulse {
        position: absolute;
        width: 100%;
        height: 100%;
        background: inherit;
        border-radius: 50%;
        z-index: -1;
        opacity: 0.6;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.6;
        }
        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }
    
    /* BACK TO TOP */
    .back-to-top {
        position: fixed;
        width: 50px;
        height: 50px;
        bottom: 100px;
        left: 30px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        z-index: 999;
        box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);
        transition: all 0.3s ease;
        display: none;
        align-items: center;
        justify-content: center;
    }
    
    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.4);
    }
    
    .back-to-top.show {
        display: flex;
    }
    
    /* RESPONSIVIDADE */
    @media (max-width: 1024px) {
        .footer-grid-modern {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        
        .footer-modern {
            padding-top: 60px;
        }
    }
    
    @media (max-width: 768px) {
        .footer-grid-modern {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .footer-column-modern {
            padding: 0;
        }
        
        .footer-main {
            padding: 40px 0 30px;
        }
        
        .footer-wave {
            top: -40px;
            height: 50px;
        }
        
        .whatsapp-float {
            width: 60px;
            height: 60px;
            font-size: 28px;
            bottom: 20px;
            right: 20px;
        }
        
        .back-to-top {
            width: 45px;
            height: 45px;
            bottom: 90px;
            left: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .social-icons {
            justify-content: center;
        }
        
        .footer-brand {
            text-align: center;
            align-items: center;
        }
        
        .footer-logo-wrapper {
            margin: 0 auto;
        }
    }
    
    /* Adiciona variáveis RGB para efeitos */
    :root {
        --primary-rgb: 0, 102, 204;
        --secondary-rgb: 0, 168, 255;
    }
    </style>

    <script>
    // Back to Top Button
    document.addEventListener('DOMContentLoaded', function() {
        const backToTop = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
                setTimeout(() => backToTop.classList.add('show'), 10);
            } else {
                backToTop.classList.remove('show');
                setTimeout(() => {
                    if (!backToTop.classList.contains('show')) {
                        backToTop.style.display = 'none';
                    }
                }, 300);
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Newsletter Form
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                
                // Aqui você pode adicionar a lógica para enviar o email
                // Para demonstração, apenas mostramos um alerta
                alert(`Obrigado por se cadastrar! Você receberá nossas ofertas em: ${email}`);
                this.reset();
            });
        }
    });
    </script>
</body>
</html>