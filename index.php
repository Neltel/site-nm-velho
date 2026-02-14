
<?php
// index.php
include 'includes/config.php';
include 'includes/header.php';
?>

<!-- Hero Section Modernizado -->
<section class="hero-modern">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="title-gradient">Especialistas em</span>
                    <span class="title-accent">Ar Condicionado</span>
                </h1>
                <h2 class="hero-subtitle">Em São José do Rio Preto e Região</h2>
                <p class="hero-description">
                    Instalação profissional, manutenção especializada, limpeza técnica e reparos rápidos. 
                    Trazemos o conforto térmico ideal para seu lar ou empresa.
                </p>
                <div class="hero-buttons">
                    <a href="sistema-ia.php" class="btn-hero-primary">
                        <i class="fas fa-calendar-check"></i>
                        <span>Solicitar Orçamento</span>
                    </a>
                    <a href="contato.php" class="btn-hero-secondary">
                        <i class="fas fa-headset"></i>
                        <span>Falar com Especialista</span>
                    </a>
                </div>
                <div class="hero-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Atendimento Rápido</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Garantia nos Serviços</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-tools"></i>
                        <span>Técnicos Especializados</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="image-wrapper">
                    <div class="floating-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <div class="floating-icon icon-2">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div class="floating-icon icon-3">
                        <i class="fas fa-temperature-low"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Serviços em Destaque Modernizado -->
<section id="servicos-destaque" class="section-modern">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Nossos Serviços</span>
            <h2 class="section-title">Soluções Completas em Climatização</h2>
            <p class="section-subtitle">Serviços especializados com qualidade e garantia para sua tranquilidade</p>
        </div>
        
        <div class="services-grid-modern">
            <?php
            // Check if database is available
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1 LIMIT 6");
                    $stmt->execute();
                    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    error_log("Erro ao buscar serviços: " . $e->getMessage());
                    $servicos = [];
                }
            } else {
                // Default services when database is not available
                $servicos = [
                    ['id' => 1, 'nome' => 'Instalação de Ar Condicionado', 'descricao' => 'Instalação completa com material incluído', 'categoria' => 'instalacao'],
                    ['id' => 2, 'nome' => 'Manutenção Preventiva', 'descricao' => 'Limpeza e verificação completa do equipamento', 'categoria' => 'manutencao'],
                    ['id' => 3, 'nome' => 'Limpeza Técnica Completa', 'descricao' => 'Limpeza interna e externa com produtos específicos', 'categoria' => 'limpeza'],
                    ['id' => 4, 'nome' => 'Reparo Técnico Especializado', 'descricao' => 'Diagnóstico e reparo de problemas técnicos', 'categoria' => 'reparo'],
                    ['id' => 5, 'nome' => 'Remoção de Equipamento', 'descricao' => 'Remoção segura do ar condicionado', 'categoria' => 'remocao'],
                ];
            }
            
            foreach($servicos as $servico):
                $icones = [
                    'instalacao' => 'fa-tools',
                    'manutencao' => 'fa-user-cog',
                    'limpeza' => 'fa-broom',
                    'reparo' => 'fa-wrench',
                    'remocao' => 'fa-truck-loading'
                ];
                $icon = $icones[$servico['categoria']] ?? 'fa-snowflake';
            ?>
            <div class="service-card-modern">
                <div class="service-icon-modern">
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <div class="service-content-modern">
                    <h3><?php echo $servico['nome']; ?></h3>
                    <p><?php echo $servico['descricao']; ?></p>
                    <a href="sistema-ia.php?servico=<?php echo $servico['id']; ?>" class="service-btn">
                        <span>Solicitar</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="service-badge">POPULAR</div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section-cta">
            <a href="sistema-ia.php" class="btn-cta">
                <i class="fas fa-list-alt"></i>
                <span>Que tal um Orçamento</span>
            </a>
        </div>
    </div>
</section>

<!-- Porque Escolher Modernizado -->
<section id="porque-escolher" class="section-modern bg-gradient">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Porque Escolher</span>
            <h2 class="section-title">A Melhor Escolha para Sua Climatização</h2>
            <p class="section-subtitle">Diferenciais que fazem a diferença no seu conforto</p>
        </div>
        
        <div class="features-grid-modern">
            <div class="feature-card-modern">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-award"></i>
                </div>
                <h3>Experiência Comprovada</h3>
                <p>Anos de experiência atendendo São José do Rio Preto e região com excelência e compromisso.</p>
            </div>
            
            <div class="feature-card-modern">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Atendimento Rápido</h3>
                <p>Agendamento rápido e atendimento eficiente para resolver seu problema no menor tempo possível.</p>
            </div>
            
            <div class="feature-card-modern">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Profissionais Qualificados</h3>
                <p>Técnicos especializados e constantemente treinados nas melhores práticas do mercado.</p>
            </div>
            
            <div class="feature-card-modern">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Garantia Extendida</h3>
                <p>Todos nossos serviços possuem garantia estendida para sua total tranquilidade.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Agendamento -->
<section class="cta-section-modern">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h2>Precisa de um Ar Condicionado Funcionando Perfeitamente?</h2>
                <p>Nossa equipe está pronta para atender você com agilidade e profissionalismo.</p>
            </div>
            <div class="cta-buttons">
                <a href="sistema-ia.php" class="btn-cta-primary">
                    <i class="fas fa-calendar-alt"></i>
                    Agendar Agora
                </a>
                <a href="https://wa.me/<?php echo $config_site['whatsapp_numero']; ?>" class="btn-cta-whatsapp" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================================================
   ESTILOS MODERNOS PARA INDEX.PHP
   Compatíveis com Painel Admin (mantém variáveis CSS)
============================================================================ */

/* HERO MODERNO */
.hero-modern {
    background: linear-gradient(135deg, 
        rgba(0, 102, 204, 0.1) 0%,
        rgba(0, 168, 255, 0.05) 100%);
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.hero-modern::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 100%);
    clip-path: polygon(100% 0, 0 0, 100% 100%);
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 2;
}

.hero-text {
    max-width: 600px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
    background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.title-accent {
    color: var(--primary);
    -webkit-text-fill-color: var(--primary);
}

.hero-subtitle {
    font-size: 1.5rem;
    color: var(--dark);
    margin-bottom: 25px;
    font-weight: 600;
}

.hero-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
    margin-bottom: 40px;
}

.hero-buttons {
    display: flex;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.btn-hero-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 16px 32px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2);
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(var(--primary-rgb), 0.3);
}

.btn-hero-secondary {
    background: white;
    color: var(--primary);
    padding: 16px 32px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: 2px solid var(--primary);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.btn-hero-secondary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-3px);
}

.hero-features {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--dark);
    font-weight: 500;
}

.feature-item i {
    color: var(--primary);
    font-size: 1.2rem;
}

.hero-image {
    position: relative;
}

.image-wrapper {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px;
    padding: 60px;
    position: relative;
    transform: perspective(1000px) rotateY(-5deg);
    box-shadow: 
        -20px 20px 60px rgba(var(--primary-rgb), 0.2),
        inset 0 0 0 1px rgba(255,255,255,0.1);
}

.floating-icon {
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
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    animation: float 6s ease-in-out infinite;
}

.floating-icon:nth-child(1) {
    top: 20px;
    left: 20px;
}

.floating-icon.icon-2 {
    top: 50%;
    right: 20px;
    animation-delay: -2s;
}

.floating-icon.icon-3 {
    bottom: 20px;
    left: 50%;
    animation-delay: -4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* SEÇÃO MODERNA */
.section-modern {
    padding: 100px 0;
}

.bg-gradient {
    background: linear-gradient(135deg, 
        rgba(0, 102, 204, 0.03) 0%,
        rgba(0, 168, 255, 0.01) 100%);
}

.section-header {
    text-align: center;
    margin-bottom: 60px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.section-label {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 15px;
    line-height: 1.2;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
}

/* SERVIÇOS MODERNOS */
.services-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.service-card-modern {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    position: relative;
    transition: all 0.4s ease;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    overflow: hidden;
}

.service-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px 20px 0 0;
}

.service-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.service-icon-modern {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
    font-size: 32px;
    color: white;
}

.service-content-modern h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 15px;
}

.service-content-modern p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 80px;
}

.service-price {
    display: flex;
    align-items: baseline;
    gap: 10px;
    margin-bottom: 25px;
}

.service-price span {
    color: #888;
    font-size: 0.9rem;
}

.service-price strong {
    color: var(--primary);
    font-size: 1.5rem;
    font-weight: 700;
}

.service-btn {
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

.service-btn:hover {
    background: var(--primary);
    color: white;
    transform: translateX(5px);
}

.service-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #ff6b00;
    color: white;
    padding: 5px 15px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* FEATURES MODERNOS */
.features-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
}

.feature-card-modern {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.feature-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.feature-icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 32px;
    color: white;
}

.feature-card-modern h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 15px;
}

.feature-card-modern p {
    color: #666;
    line-height: 1.6;
}

/* CTA SECTION */
.cta-section-modern {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    padding: 80px 0;
    color: white;
}

.cta-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 40px;
}

.cta-text h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    line-height: 1.2;
}

.cta-text p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.btn-cta-primary {
    background: white;
    color: var(--primary);
    padding: 18px 36px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.btn-cta-whatsapp {
    background: #25D366;
    color: white;
    padding: 18px 36px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-cta-whatsapp:hover {
    background: #1da851;
    transform: translateY(-3px);
}

.section-cta {
    text-align: center;
    margin-top: 40px;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-cta:hover {
    background: var(--primary);
    color: white;
}

/* RESPONSIVIDADE */
@media (max-width: 992px) {
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .hero-buttons {
        justify-content: center;
    }
    
    .hero-features {
        justify-content: center;
    }
    
    .image-wrapper {
        margin-top: 40px;
        transform: none;
    }
    
    .cta-content {
        flex-direction: column;
        text-align: center;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .hero-modern,
    .section-modern {
        padding: 60px 0;
    }
    
    .services-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .features-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .hero-buttons,
    .cta-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary,
    .btn-cta-primary,
    .btn-cta-whatsapp {
        justify-content: center;
    }
}

/* Adiciona variáveis RGB para efeitos */
:root {
    --primary-rgb: 0, 102, 204;
    --secondary-rgb: 0, 168, 255;
}
</style>

<?php include 'includes/footer.php'; ?>