
<?php
// contato.php
include 'includes/config.php';
include 'includes/header.php';

// Buscar configurações do site
$config_site = getConfigSite($pdo);

// Processar formulário de contato (MANTIDO, mas sem o HTML do formulário)
if($_POST) {
    // Usando sanitização (função sanitize precisa estar definida em includes/config.php)
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    $assunto = sanitize($_POST['assunto']);
    $mensagem = sanitize($_POST['mensagem']);

    // Aqui você pode integrar com um serviço de email
    // Ou salvar no banco de dados
    $sucesso = true;

    if($sucesso) {
        // Estilo atualizado para a notificação
        echo '<div class="contact-success-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="success-content">
                    <h4>Mensagem enviada com sucesso!</h4>
                    <p>Entraremos em contato em breve.</p>
                </div>
              </div>';
    }
}

// Lista completa de redes sociais
$redes_sociais_completas = [
    'facebook_url' => ['fa-facebook-f', 'Facebook', '#3b5998'],
    'instagram_url' => ['fa-instagram', 'Instagram', '#e4405f'],
    'whatsapp_url' => ['fa-whatsapp', 'WhatsApp', '#25d366'],
    'linkedin_url' => ['fa-linkedin-in', 'LinkedIn', '#0077b5'],
    'youtube_url' => ['fa-youtube', 'YouTube', '#ff0000'],
    'tiktok_url' => ['fa-tiktok', 'TikTok', '#000000'],
    'twitter_url' => ['fa-x-twitter', 'Twitter/X', '#000000'],
    'pinterest_url' => ['fa-pinterest-p', 'Pinterest', '#bd081c'],
    'telegram_url' => ['fa-telegram-plane', 'Telegram', '#0088cc'],
    'getninja_url' => ['fa-solid fa-user-ninja', 'GetNinja', '#FFEA52'],
    'olx_url' => ['fa-tags', 'OLX', '#e4761b'],
    'mercado_livre_url' => ['fa-shopping-cart', 'Mercado Livre', '#ffe600'],
    'google_meu_negocio_url' => ['fa-location-dot', 'Google Meu Negócio', '#4285f4'],
    'tripadvisor_url' => ['fa-tripadvisor', 'TripAdvisor', '#3aae63'],
    'airbnb_url' => ['fa-airbnb', 'Airbnb', '#ff5a5f'],
    'booking_url' => ['fa-hotel', 'Booking', '#003580'],
    'spotify_url' => ['fa-spotify', 'Spotify', '#1db954'],
    'soundcloud_url' => ['fa-soundcloud', 'SoundCloud', '#ff8800'],
    'twitch_url' => ['fa-twitch', 'Twitch', '#6441a5'],
    'discord_url' => ['fa-discord', 'Discord', '#7289da'],
    'github_url' => ['fa-github', 'GitHub', '#181717'],
    'behance_url' => ['fa-behance', 'Behance', '#1769ff'],
    'dribbble_url' => ['fa-dribbble', 'Dribbble', '#ea4c89'],
    'medium_url' => ['fa-medium', 'Medium', '#000000'],
    'reddit_url' => ['fa-reddit-alien', 'Reddit', '#ff4500'],
    'quora_url' => ['fa-quora', 'Quora', '#b92b27'],
    'vimeo_url' => ['fa-vimeo-v', 'Vimeo', '#1ab7ea'],
    'flickr_url' => ['fa-flickr', 'Flickr', '#ff0084'],
    'snapchat_url' => ['fa-snapchat-ghost', 'Snapchat', '#fffc00'],
    'wechat_url' => ['fa-weixin', 'WeChat', '#09b83e'],
    'line_url' => ['fa-line', 'LINE', '#00c300'],
    'vk_url' => ['fa-vk', 'VK', '#4a76a8'],
    'tumblr_url' => ['fa-tumblr', 'Tumblr', '#35465c'],
    'blogger_url' => ['fa-blogger', 'Blogger', '#ff5722'],
    'wordpress_url' => ['fa-wordpress-simple', 'WordPress', '#21759b'],
];

// Checar se há alguma rede social configurada
$tem_redes_sociais = false;
foreach($redes_sociais_completas as $chave => $info) {
    if (!empty($config_site[$chave])) {
        $tem_redes_sociais = true;
        break;
    }
}
if (!$tem_redes_sociais && !empty($config_site['whatsapp_numero']) && ($config_site['whatsapp_ativo'] == '1')) {
    $tem_redes_sociais = true;
}
?>

<!-- Hero Contato -->
<section class="contact-hero-modern">
    <div class="container">
        <div class="contact-hero-content">
            <div class="contact-hero-text">
                <h1 class="contact-hero-title">Fale Conosco</h1>
                <p class="contact-hero-subtitle">Estamos aqui para ajudar com suas dúvidas e solicitações</p>
                <div class="contact-hero-features">
                    <div class="contact-feature">
                        <i class="fas fa-headset"></i>
                        <span>Atendimento Personalizado</span>
                    </div>
                    <div class="contact-feature">
                        <i class="fas fa-clock"></i>
                        <span>Resposta Rápida</span>
                    </div>
                    <div class="contact-feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Segurança Garantida</span>
                    </div>
                </div>
            </div>
            <div class="contact-hero-image">
                <div class="contact-image-wrapper">
                    <div class="contact-floating-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="contact-floating-icon icon-2">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-floating-icon icon-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-section-modern">
    <div class="container">
        <div class="contact-grid-modern">
            
            <!-- Card Informações de Contato -->
            <div class="contact-info-card-modern">
                <div class="card-header-modern">
                    <div class="card-icon">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <h3 class="card-title-modern">Informações de Contato</h3>
                </div>
                
                <div class="contact-details-modern">
                    <?php if(!empty($config_site['site_telefone']) || !empty($config_site['site_telefone2'])): ?>
                    <div class="contact-item-modern">
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-item-content">
                            <h4>Telefones</h4>
                            <?php if(!empty($config_site['site_telefone'])): ?>
                            <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $config_site['site_telefone']); ?>" class="contact-link">
                                <i class="fas fa-phone"></i>
                                <?php echo $config_site['site_telefone']; ?>
                            </a>
                            <?php endif; ?>
                            <?php if(!empty($config_site['site_telefone2'])): ?>
                            <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $config_site['site_telefone2']); ?>" class="contact-link">
                                <i class="fas fa-mobile-alt"></i>
                                <?php echo $config_site['site_telefone2']; ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($config_site['site_email'])): ?>
                    <div class="contact-item-modern">
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-item-content">
                            <h4>E-mail</h4>
                            <a href="mailto:<?php echo $config_site['site_email']; ?>" class="contact-link">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo $config_site['site_email']; ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($config_site['site_endereco'])): ?>
                    <div class="contact-item-modern">
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-item-content">
                            <h4>Endereço</h4>
                            <div class="contact-address">
                                <i class="fas fa-building"></i>
                                <?php echo $config_site['site_endereco']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-item-modern">
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-item-content">
                            <h4>Horário de Atendimento</h4>
                            <div class="contact-schedule">
                                <div class="schedule-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <div>
                                        <strong>Segunda a Sexta</strong>
                                        <span>8h às 18h</span>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <i class="fas fa-calendar-week"></i>
                                    <div>
                                        <strong>Sábado</strong>
                                        <span>8h às 12h</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(($config_site['agendamento_online'] ?? '0') == '1'): ?>
                <div class="contact-action-modern">
                    <a href="agendamento.php" class="contact-action-btn">
                        <i class="far fa-calendar-alt"></i>
                        <span>Agendar Serviço Online</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>

            </div>
            
            <!-- Card Redes Sociais e Área -->
            <div class="contact-side-cards-modern">
                
                <?php if ($tem_redes_sociais): ?>
                <div class="social-card-modern">
                    <div class="card-header-modern">
                        <div class="card-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3 class="card-title-modern">Conecte-se Conosco</h3>
                    </div>
                    <p class="social-description">Siga-nos nas redes sociais e fique por dentro das novidades</p>
                    
                    <div class="social-grid-modern">
                        <?php 
                        $whatsapp_exibido = false;
                        
                        foreach($redes_sociais_completas as $chave => $info): 
                            $icon_class = $info[0];
                            $nome = $info[1];
                            $cor = $info[2];
                            $url = $config_site[$chave] ?? '';
                            
                            if($chave === 'whatsapp_url') {
                                $whatsapp_exibido = true;
                                if(empty($url) && !empty($config_site['whatsapp_numero']) && ($config_site['whatsapp_ativo'] == '1')) {
                                    $url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $config_site['whatsapp_numero']);
                                }
                            }
                            
                            if (!empty($url)): 
                                $icon_prefix = (strpos($icon_class, 'fa-solid') !== false) ? 'fa-solid' : 'fab';
                                if($icon_prefix === 'fa-solid') $icon_class = str_replace('fa-solid ', '', $icon_class);
                        ?>
                        <a href="<?php echo $url; ?>" 
                           target="_blank" 
                           class="social-icon-modern"
                           style="--social-color: <?php echo $cor; ?>;"
                           title="<?php echo $nome; ?>">
                            <div class="social-icon-wrapper">
                                <i class="<?php echo $icon_prefix; ?> <?php echo $icon_class; ?>"></i>
                            </div>
                            <span><?php echo $nome; ?></span>
                        </a>
                        <?php endif;
                        endforeach; 
                        
                        if (!$whatsapp_exibido && !empty($config_site['whatsapp_numero']) && ($config_site['whatsapp_ativo'] == '1')): ?>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $config_site['whatsapp_numero']); ?>" 
                           target="_blank" 
                           class="social-icon-modern"
                           style="--social-color: #25d366;"
                           title="WhatsApp">
                            <div class="social-icon-wrapper">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <span>WhatsApp</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="area-card-modern">
                    <div class="card-header-modern">
                        <div class="card-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3 class="card-title-modern">Área de Atendimento</h3>
                    </div>
                    <p class="area-description-modern">
                        <i class="fas fa-search-location"></i> Atendemos <strong>São José do Rio Preto</strong> e região:
                    </p>
                    <div class="city-cloud-modern">
                        <?php
                        $cidades_text = $config_site['cidades_atendidas'] ?? "São José do Rio Preto\nMirassol\nBady Bassitt\nIpiguá\nJosé Bonifácio\nNova Granada\nMirassolândia\nTanabi\nUchoa\nCedral";
                        $cidades_array = explode("\n", $cidades_text);
                        
                        foreach($cidades_array as $cidade) {
                            $cidade = trim($cidade);
                            if($cidade) {
                                echo '<span class="city-tag-modern">' . $cidade . '</span>';
                            }
                        }
                        ?>
                    </div>
                    <div class="area-cta">
                        <a href="cidades-atendidas.php" class="area-link">
                            <i class="fas fa-map"></i>
                            Ver todas as cidades
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <!-- Formulário de Contato --
        <div class="contact-form-section-modern">
            <div class="form-header">
                <h3>Envie sua Mensagem</h3>
                <p>Preencha o formulário abaixo e entraremos em contato o mais breve possível</p>
            </div>
            
            <form method="POST" class="contact-form-modern">
                <div class="form-grid">
                    <div class="form-group-modern">
                        <label for="nome">
                            <i class="fas fa-user"></i>
                            Seu Nome Completo
                        </label>
                        <input type="text" id="nome" name="nome" required 
                               placeholder="Digite seu nome completo">
                    </div>
                    
                    <div class="form-group-modern">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Seu E-mail
                        </label>
                        <input type="email" id="email" name="email" required 
                               placeholder="seu@email.com">
                    </div>
                    
                    <div class="form-group-modern">
                        <label for="telefone">
                            <i class="fas fa-phone"></i>
                            Telefone/WhatsApp
                        </label>
                        <input type="tel" id="telefone" name="telefone" required 
                               placeholder="(11) 99999-9999">
                    </div>
                    
                    <div class="form-group-modern">
                        <label for="assunto">
                            <i class="fas fa-tag"></i>
                            Assunto
                        </label>
                        <select id="assunto" name="assunto" required>
                            <option value="">Selecione o assunto</option>
                            <option value="orcamento">Solicitar Orçamento</option>
                            <option value="duvida">Dúvida Técnica</option>
                            <option value="agendamento">Agendamento</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group-modern full-width">
                        <label for="mensagem">
                            <i class="fas fa-comment-dots"></i>
                            Sua Mensagem
                        </label>
                        <textarea id="mensagem" name="mensagem" rows="6" required 
                                  placeholder="Descreva sua necessidade ou dúvida..."></textarea>
                    </div>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="submit-btn-modern">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Mensagem
                    </button>
                </div>
            </form>
        </div>-->
        
    </div>
</section>

<style>
/* ============================================================================
   ESTILOS MODERNOS PARA CONTATO.PHP
============================================================================ */

/* HERO CONTATO */
.contact-hero-modern {
    background: linear-gradient(135deg, 
        rgba(0, 102, 204, 0.08) 0%,
        rgba(0, 168, 255, 0.04) 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.contact-hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.contact-hero-text {
    max-width: 600px;
}

.contact-hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 15px;
    background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.contact-hero-subtitle {
    font-size: 1.3rem;
    color: #555;
    margin-bottom: 30px;
    line-height: 1.6;
}

.contact-hero-features {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

.contact-feature {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: white;
    border-radius: 50px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.contact-feature:hover {
    transform: translateY(-3px);
}

.contact-feature i {
    color: var(--primary);
    font-size: 1.2rem;
}

.contact-feature span {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.95rem;
}

.contact-hero-image {
    position: relative;
}

.contact-image-wrapper {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px;
    padding: 60px;
    position: relative;
    box-shadow: 
        0 20px 40px rgba(var(--primary-rgb), 0.15),
        inset 0 0 0 1px rgba(255,255,255,0.1);
}

.contact-floating-icon {
    position: absolute;
    background: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--primary);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    animation: float-contact 5s ease-in-out infinite;
}

.contact-floating-icon:nth-child(1) {
    top: 30px;
    left: 30px;
}

.contact-floating-icon.icon-2 {
    top: 50%;
    right: 30px;
    animation-delay: -1.5s;
}

.contact-floating-icon.icon-3 {
    bottom: 30px;
    left: 30%;
    animation-delay: -3s;
}

@keyframes float-contact {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(5deg); }
}

/* SEÇÃO PRINCIPAL */
.contact-section-modern {
    padding: 80px 0;
    background: #f8fafc;
}

.contact-grid-modern {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 60px;
}

.contact-info-card-modern {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.contact-info-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
}

.card-header-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
}

.card-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.card-title-modern {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

/* ITENS DE CONTATO */
.contact-details-modern {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.contact-item-modern {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.contact-item-modern:hover {
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transform: translateX(5px);
}

.contact-icon-wrapper {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.1);
}

.contact-item-content {
    flex: 1;
}

.contact-item-content h4 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 10px;
}

.contact-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
    text-decoration: none;
    padding: 8px 0;
    transition: color 0.3s ease;
}

.contact-link:hover {
    color: var(--primary);
}

.contact-link i {
    width: 20px;
    color: var(--primary);
}

.contact-address {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
    line-height: 1.5;
}

.contact-schedule {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.schedule-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.schedule-item i {
    color: var(--primary);
    font-size: 1.2rem;
}

.schedule-item div {
    display: flex;
    flex-direction: column;
}

.schedule-item strong {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.95rem;
}

.schedule-item span {
    color: #666;
    font-size: 0.9rem;
}

.contact-action-modern {
    margin-top: 40px;
}

.contact-action-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 20px 30px;
    border-radius: 15px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2);
}

.contact-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(var(--primary-rgb), 0.3);
}

/* CARDS LATERAIS */
.contact-side-cards-modern {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.social-card-modern,
.area-card-modern {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.social-card-modern::before,
.area-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
}

.social-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 25px;
}

.social-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
}

.social-icon-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 10px;
    background: #f8fafc;
    border-radius: 15px;
    text-decoration: none;
    color: var(--dark);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.social-icon-modern:hover {
    background: var(--social-color);
    color: white;
    transform: translateY(-5px);
    border-color: var(--social-color);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.social-icon-wrapper {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--social-color);
    transition: all 0.3s ease;
}

.social-icon-modern:hover .social-icon-wrapper {
    background: rgba(255,255,255,0.2);
    color: white;
}

.social-icon-modern span {
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
}

/* ÁREA DE ATENDIMENTO */
.area-description-modern {
    color: #666;
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 1.1rem;
}

.area-description-modern strong {
    color: var(--primary);
}

.city-cloud-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 30px;
}

.city-tag-modern {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.2);
    transition: all 0.3s ease;
}

.city-tag-modern:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
}

.area-cta {
    text-align: center;
}

.area-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    padding: 12px 25px;
    border: 2px solid var(--primary);
    border-radius: 50px;
    transition: all 0.3s ease;
}

.area-link:hover {
    background: var(--primary);
    color: white;
    transform: translateX(5px);
}

/* FORMULÁRIO DE CONTATO */
.contact-form-section-modern {
    background: white;
    border-radius: 20px;
    padding: 50px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    margin-top: 40px;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h3 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    font-size: 1.1rem;
}

.contact-form-modern .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 40px;
}

.form-group-modern.full-width {
    grid-column: 1 / -1;
}

.form-group-modern {
    position: relative;
}

.form-group-modern label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.form-group-modern label i {
    color: var(--primary);
}

.form-group-modern input,
.form-group-modern select,
.form-group-modern textarea {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-group-modern input:focus,
.form-group-modern select:focus,
.form-group-modern textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.form-group-modern select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%230066cc' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 20px center;
    background-size: 16px;
}

.form-footer {
    text-align: center;
}

.submit-btn-modern {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    border: none;
    padding: 18px 50px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2);
}

.submit-btn-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(var(--primary-rgb), 0.3);
}

/* MENSAGEM DE SUCESSO */
.contact-success-message {
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border: 2px solid #10b981;
    border-radius: 15px;
    padding: 25px;
    margin: 30px auto;
    max-width: 600px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.success-icon {
    width: 60px;
    height: 60px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
}

.success-content h4 {
    color: #065f46;
    margin-bottom: 5px;
    font-size: 1.3rem;
}

.success-content p {
    color: #047857;
    margin: 0;
}

/* RESPONSIVIDADE */
@media (max-width: 1200px) {
    .contact-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .contact-form-modern .form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .contact-hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .contact-hero-features {
        justify-content: center;
    }
    
    .contact-hero-image {
        margin-top: 40px;
    }
    
    .contact-section-modern {
        padding: 40px 0;
    }
    
    .contact-info-card-modern,
    .social-card-modern,
    .area-card-modern,
    .contact-form-section-modern {
        padding: 30px 20px;
    }
    
    .social-grid-modern {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .city-cloud-modern {
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>