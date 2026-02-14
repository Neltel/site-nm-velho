
<?php
// cidades-atendidas.php
include 'includes/header.php';

// Buscar configurações do site
$config_site = getConfigSite($pdo);

// Buscar cidades atendidas
$cidades_text = $config_site['cidades_atendidas'] ?? "São José do Rio Preto\nMirassol\nBady Bassitt\nIpiguá\nJosé Bonifácio\nNova Granada\nMirassolândia\nTanabi\nUchoa\nCedral";
$cidades_array = explode("\n", $cidades_text);

// Filtrar cidades vazias
$cidades_array = array_filter(array_map('trim', $cidades_array));
?>

<!-- Hero Cidades -->
<section class="cidades-hero-modern">
    <div class="container">
        <div class="cidades-hero-content">
            <div class="cidades-hero-text">
                <h1 class="cidades-hero-title">
                    <span class="title-gradient">Área de Atendimento</span>
                    <span class="title-accent">N&M Refrigeração</span>
                </h1>
                <p class="cidades-hero-subtitle">
                    Levamos conforto térmico de qualidade para toda a região de São José do Rio Preto
                </p>
                <div class="cidades-hero-features">
                    <div class="cidades-feature">
                        <i class="fas fa-truck"></i>
                        <span>Atendimento Móvel</span>
                    </div>
                    <div class="cidades-feature">
                        <i class="fas fa-clock"></i>
                        <span>Agendamento Rápido</span>
                    </div>
                    <div class="cidades-feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Serviço Garantido</span>
                    </div>
                </div>
            </div>
            <div class="cidades-hero-image">
                <div class="cidades-image-wrapper">
                    <div class="cidades-floating-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="cidades-floating-icon icon-2">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="cidades-floating-icon icon-3">
                        <i class="fas fa-route"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cidades-section-modern">
    <div class="container">
        <div class="section-header-modern">
            <span class="section-label-modern">Cobertura Total</span>
            <h2 class="section-title-modern">Todas as Cidades que Atendemos</h2>
            <p class="section-subtitle-modern">
                Confira abaixo todas as cidades onde a <?php echo htmlspecialchars($config_site['site_nome']); ?> 
                oferece seus serviços especializados em climatização.
            </p>
        </div>

        <div class="cidades-stats-modern">
            <div class="stat-card-modern">
                <div class="stat-icon">
                    <i class="fas fa-city"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($cidades_array); ?>+</h3>
                    <p>Cidades Atendidas</p>
                </div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>1000+</h3>
                    <p>Clientes Satisfeitos</p>
                </div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>24/7</h3>
                    <p>Agendamento Online</p>
                </div>
            </div>
        </div>

        

        <div class="cidades-map-section">
            <div class="map-header">
                <h3>📍 Localização Estratégica</h3>
                <p>Atendemos toda a região com base em São José do Rio Preto</p>
            </div>
            <div class="map-container-modern">
                <div class="map-visual">
                    <div class="central-city">
                        <div class="central-marker">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="central-label">
                            <strong>São José do Rio Preto</strong>
                            <span>Base Principal</span>
                        </div>
                    </div>
                    
                    <div class="surrounding-cities">
                        <?php 
                        $surrounding = array_slice($cidades_array, 0, 8);
                        $angles = [0, 45, 90, 135, 180, 225, 270, 315];
                        foreach($surrounding as $index => $city): 
                            if($index < 8):
                        ?>
                        <div class="city-marker" style="--angle: <?php echo $angles[$index]; ?>deg;">
                            <div class="marker-dot"></div>
                            <div class="marker-label"><?php echo htmlspecialchars(substr(trim($city), 0, 15)); ?></div>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="cidades-cta-modern">
            <div class="cta-content-modern">
                <div class="cta-text-modern">
                    <h2>Sua cidade não está na lista?</h2>
                    <p>Entre em contato conosco para verificar a disponibilidade na sua região!</p>
                </div>
                <div class="cta-buttons-modern">
                    <a href="contato.php" class="cta-btn-primary">
                        <i class="fas fa-phone-alt"></i>
                        Entrar em Contato
                    </a>
                    <?php if($config_site['whatsapp_ativo'] == '1'): ?>
                    <a href="https://wa.me/<?php echo $config_site['whatsapp_numero']; ?>" 
                       class="cta-btn-whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    <?php endif; ?>
                    <a href="sistema-ia.php" class="cta-btn-accent">
                        <i class="fas fa-calendar-check"></i>
                        Agendar Online
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================================================
   ESTILOS MODERNOS PARA CIDADES-ATENDIDAS.PHP
============================================================================ */

/* HERO CIDADES */
.cidades-hero-modern {
    background: linear-gradient(135deg, 
        rgba(0, 102, 204, 0.08) 0%,
        rgba(0, 168, 255, 0.04) 100%);
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.cidades-hero-modern::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.2) 100%);
    clip-path: polygon(100% 0, 0 0, 100% 100%);
}

.cidades-hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 2;
}

.cidades-hero-text {
    max-width: 600px;
}

.cidades-hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
}

.title-gradient {
    background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: block;
}

.title-accent {
    color: var(--primary);
    display: block;
}

.cidades-hero-subtitle {
    font-size: 1.3rem;
    color: #555;
    line-height: 1.7;
    margin-bottom: 40px;
}

.cidades-hero-features {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

.cidades-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    padding: 15px 25px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.cidades-feature:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1);
}

.cidades-feature i {
    color: var(--primary);
    font-size: 1.4rem;
}

.cidades-feature span {
    font-weight: 600;
    color: var(--dark);
    font-size: 1rem;
}

.cidades-hero-image {
    position: relative;
}

.cidades-image-wrapper {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px;
    padding: 60px;
    position: relative;
    box-shadow: 
        0 20px 40px rgba(var(--primary-rgb), 0.15),
        inset 0 0 0 1px rgba(255,255,255,0.1);
}

.cidades-floating-icon {
    position: absolute;
    background: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--primary);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    animation: float-cidades 6s ease-in-out infinite;
}

.cidades-floating-icon:nth-child(1) {
    top: 20px;
    left: 20px;
}

.cidades-floating-icon.icon-2 {
    top: 50%;
    right: 20px;
    animation-delay: -2s;
}

.cidades-floating-icon.icon-3 {
    bottom: 20px;
    left: 50%;
    animation-delay: -4s;
}

@keyframes float-cidades {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(10px, -20px) rotate(5deg); }
    66% { transform: translate(-10px, 10px) rotate(-5deg); }
}

/* SEÇÃO PRINCIPAL */
.cidades-section-modern {
    padding: 80px 0;
    background: #f8fafc;
}

.section-header-modern {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 60px;
}

.section-label-modern {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 10px 25px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.section-title-modern {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 15px;
    line-height: 1.2;
}

.section-subtitle-modern {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
}

/* STATS */
.cidades-stats-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.stat-card-modern {
    background: white;
    padding: 30px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    flex-shrink: 0;
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin: 0;
    line-height: 1;
}

.stat-content p {
    color: #666;
    font-size: 0.95rem;
    margin: 5px 0 0;
}

/* GRID DE CIDADES */
.cidades-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 80px;
}

.cidade-card-modern {
    perspective: 1000px;
    height: 200px;
}

.cidade-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.8s;
    transform-style: preserve-3d;
    cursor: pointer;
}

.cidade-card-modern:hover .cidade-card-inner {
    transform: rotateY(180deg);
}

.cidade-card-inner > div {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 20px;
    padding: 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

/* Frente do card */
.cidade-card-inner > div:first-child {
    background: white;
    border: 3px solid var(--city-color);
}

.cidade-number {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 2.5rem;
    font-weight: 800;
    color: rgba(var(--primary-rgb), 0.1);
    line-height: 1;
}

.cidade-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--city-color) 0%, color-mix(in srgb, var(--city-color) 80%, white) 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    color: white;
    font-size: 24px;
}

.cidade-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 10px;
}

.cidade-content p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.cidade-badge {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Verso do card (hover) */
.cidade-hover {
    background: linear-gradient(135deg, var(--city-color) 0%, color-mix(in srgb, var(--city-color) 80%, black) 100%);
    color: white;
    transform: rotateY(180deg);
}

.hover-content {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.hover-content i {
    font-size: 3rem;
    margin-bottom: 20px;
    opacity: 0.9;
}

.hover-content span {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 25px;
    line-height: 1.4;
}

.hover-btn {
    background: white;
    color: var(--city-color);
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.hover-btn:hover {
    background: rgba(255,255,255,0.9);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* MAPA VISUAL */
.cidades-map-section {
    background: white;
    border-radius: 20px;
    padding: 50px;
    margin-bottom: 60px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

.map-header {
    text-align: center;
    margin-bottom: 40px;
}

.map-header h3 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 10px;
}

.map-header p {
    color: #666;
    font-size: 1.1rem;
}

.map-container-modern {
    position: relative;
    height: 400px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-visual {
    position: relative;
    width: 600px;
    height: 600px;
}

.central-city {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.central-marker {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 32px;
    box-shadow: 
        0 10px 30px rgba(var(--primary-rgb), 0.3),
        0 0 0 10px rgba(var(--primary-rgb), 0.1);
    animation: pulse-central 2s ease-in-out infinite;
}

@keyframes pulse-central {
    0%, 100% { 
        box-shadow: 
            0 10px 30px rgba(var(--primary-rgb), 0.3),
            0 0 0 10px rgba(var(--primary-rgb), 0.1);
    }
    50% { 
        box-shadow: 
            0 10px 30px rgba(var(--primary-rgb), 0.3),
            0 0 0 20px rgba(var(--primary-rgb), 0);
    }
}

.central-label strong {
    display: block;
    font-size: 1.3rem;
    color: var(--dark);
    margin-bottom: 5px;
}

.central-label span {
    color: #666;
    font-size: 0.9rem;
}

.surrounding-cities {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.city-marker {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: 
        translate(-50%, -50%)
        rotate(var(--angle))
        translate(200px)
        rotate(calc(-1 * var(--angle)));
    text-align: center;
}

.marker-dot {
    width: 12px;
    height: 12px;
    background: var(--primary);
    border-radius: 50%;
    margin: 0 auto 10px;
    position: relative;
}

.marker-dot::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    background: rgba(var(--primary-rgb), 0.2);
    border-radius: 50%;
}

.marker-label {
    font-size: 0.8rem;
    color: var(--dark);
    font-weight: 500;
    white-space: nowrap;
}

/* CTA SECTION */
.cidades-cta-modern {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px;
    padding: 60px;
    color: white;
    text-align: center;
}

.cta-content-modern {
    max-width: 800px;
    margin: 0 auto;
}

.cta-text-modern h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    line-height: 1.2;
}

.cta-text-modern p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 40px;
}

.cta-buttons-modern {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-btn-primary,
.cta-btn-whatsapp,
.cta-btn-accent {
    padding: 18px 35px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    font-size: 1.1rem;
    min-width: 200px;
    justify-content: center;
}

.cta-btn-primary {
    background: white;
    color: var(--primary);
}

.cta-btn-primary:hover {
    background: #f0f0f0;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.cta-btn-whatsapp {
    background: #25D366;
    color: white;
}

.cta-btn-whatsapp:hover {
    background: #1da851;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
}

.cta-btn-accent {
    background: #ff6b00;
    color: white;
    border: none;
}

.cta-btn-accent:hover {
    background: #e55a00;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 107, 0, 0.3);
}

/* RESPONSIVIDADE */
@media (max-width: 1200px) {
    .map-visual {
        width: 500px;
        height: 500px;
    }
    
    .city-marker {
        transform: 
            translate(-50%, -50%)
            rotate(var(--angle))
            translate(150px)
            rotate(calc(-1 * var(--angle)));
    }
}

@media (max-width: 992px) {
    .cidades-hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .cidades-hero-features {
        justify-content: center;
    }
    
    .cidades-hero-image {
        margin-top: 40px;
    }
    
    .cidades-grid-modern {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .map-visual {
        width: 400px;
        height: 400px;
    }
    
    .city-marker {
        transform: 
            translate(-50%, -50%)
            rotate(var(--angle))
            translate(120px)
            rotate(calc(-1 * var(--angle)));
    }
}

@media (max-width: 768px) {
    .cidades-hero-modern,
    .cidades-section-modern {
        padding: 60px 0;
    }
    
    .cidades-hero-title {
        font-size: 2.5rem;
    }
    
    .section-title-modern {
        font-size: 2rem;
    }
    
    .cidades-stats-modern {
        grid-template-columns: 1fr;
    }
    
    .cidades-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .map-container-modern {
        height: 300px;
    }
    
    .map-visual {
        width: 300px;
        height: 300px;
    }
    
    .city-marker {
        transform: 
            translate(-50%, -50%)
            rotate(var(--angle))
            translate(100px)
            rotate(calc(-1 * var(--angle)));
    }
    
    .cidades-map-section,
    .cidades-cta-modern {
        padding: 40px 25px;
    }
    
    .cta-text-modern h2 {
        font-size: 2rem;
    }
    
    .cta-buttons-modern {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cta-btn-primary,
    .cta-btn-whatsapp,
    .cta-btn-accent {
        min-width: auto;
    }
}
</style>

<?php include 'includes/footer.php'; ?>