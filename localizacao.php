<?php
// localizacao.php
include 'includes/config.php';

// Definir cidade baseada na URL
$cidade = $_GET['cidade'] ?? 'sao-jose-rio-preto';
$cidades = [
    'sao-jose-rio-preto' => [
        'nome' => 'São José do Rio Preto',
        'titulo' => 'Ar Condicionado São José do Rio Preto | ClimaTech Especialista',
        'descricao' => 'ClimaTech: especialista em ar condicionado em São José do Rio Preto. Instalação, manutenção, limpeza e venda. Atendimento rápido na região central.',
        'coordenadas' => ['lat' => -20.8170, 'lng' => -49.3790]
    ],
    'mirassol' => [
        'nome' => 'Mirassol',
        'titulo' => 'Ar Condicionado Mirassol | ClimaTech Atendimento na Região',
        'descricao' => 'Serviços de ar condicionado em Mirassol. ClimaTech atende toda região com instalação profissional e manutenção especializada.',
        'coordenadas' => ['lat' => -20.8161, 'lng' => -49.5211]
    ],
    'bady-bassitt' => [
        'nome' => 'Bady Bassitt', 
        'titulo' => 'Ar Condicionado Bady Bassitt | ClimaTech Serviços Especializados',
        'descricao' => 'Ar condicionado em Bady Bassitt com a ClimaTech. Instalação rápida, manutenção preventiva e reparos especializados.',
        'coordenadas' => ['lat' => -20.9189, 'lng' => -49.4436]
    ]
];

$dados_cidade = $cidades[$cidade] ?? $cidades['sao-jose-rio-preto'];

include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Ar Condicionado em <?php echo $dados_cidade['nome']; ?> - ClimaTech</h1>
        <p>Especialistas em instalação, manutenção e venda de ar condicionado em <?php echo $dados_cidade['nome']; ?> e toda região</p>
        <a href="orcamento.php?cidade=<?php echo $cidade; ?>" class="btn btn-accent">Solicitar Orçamento em <?php echo $dados_cidade['nome']; ?></a>
    </div>
</section>

<section>
    <div class="container">
        <div class="localizacao-content">
            <h2>Serviços de Ar Condicionado em <?php echo $dados_cidade['nome']; ?></h2>
            
            <p>A <strong>ClimaTech</strong> é a empresa especializada em <strong>ar condicionado em <?php echo $dados_cidade['nome']; ?></strong> e atende toda região com serviços profissionais e garantidos.</p>
            
            <div class="servicos-locais">
                <h3>Nossos Serviços em <?php echo $dados_cidade['nome']; ?></h3>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">🔧</div>
                        <div class="service-content">
                            <h4>Instalação Profissional</h4>
                            <p>Instalação de ar condicionado em <?php echo $dados_cidade['nome']; ?> com técnicos especializados e materiais de qualidade.</p>
                            <a href="orcamento.php?servico=instalacao&cidade=<?php echo $cidade; ?>" class="btn">Solicitar Instalação</a>
                        </div>
                    </div>
                    
                    <div class="service-card">
                        <div class="service-icon">🛠️</div>
                        <div class="service-content">
                            <h4>Manutenção Preventiva</h4>
                            <p>Manutenção regular para seu ar condicionado em <?php echo $dados_cidade['nome']; ?> funcionar sempre perfeito.</p>
                            <a href="orcamento.php?servico=manutencao&cidade=<?php echo $cidade; ?>" class="btn">Agendar Manutenção</a>
                        </div>
                    </div>
                    
                    <div class="service-card">
                        <div class="service-icon">🧹</div>
                        <div class="service-content">
                            <h4>Limpeza Técnica</h4>
                            <p>Limpeza completa do seu ar condicionado em <?php echo $dados_cidade['nome']; ?> para melhorar performance e saúde.</p>
                            <a href="orcamento.php?servico=limpeza&cidade=<?php echo $cidade; ?>" class="btn">Solicitar Limpeza</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="area-atendimento">
                <h3>Atendemos em <?php echo $dados_cidade['nome']; ?> e Região</h3>
                <p>Nossa equipe atende <strong><?php echo $dados_cidade['nome']; ?></strong> e todas as cidades próximas com a mesma qualidade e rapidez.</p>
                
                <div class="cidades-vizinhas">
                    <h4>Cidades da Região que Atendemos:</h4>
                    <ul class="cidades-lista">
                        <li>✅ São José do Rio Preto</li>
                        <li>✅ Mirassol</li>
                        <li>✅ Bady Bassitt</li>
                        <li>✅ Ipiguá</li>
                        <li>✅ José Bonifácio</li>
                        <li>✅ Nova Granada</li>
                        <li>✅ Mirassolândia</li>
                        <li>✅ Tanabi</li>
                        <li>✅ Uchoa</li>
                        <li>✅ Cedral</li>
                        <li>✅ Potirendaba</li>
                        <li>✅ Guapiaçu</li>
                    </ul>
                </div>
            </div>
            
            <div class="cta-local">
                <h3>Precisa de Ar Condicionado em <?php echo $dados_cidade['nome']; ?>?</h3>
                <p>Entre em contato agora mesmo e solicite um orçamento sem compromisso!</p>
                <div class="cta-buttons">
                    <a href="https://wa.me/5517999999999?text=Olá! Gostaria de um orçamento para ar condicionado em <?php echo urlencode($dados_cidade['nome']); ?>" 
                       class="btn btn-success" target="_blank">
                       📞 WhatsApp Rápido
                    </a>
                    <a href="tel:+5517999999999" class="btn btn-primary">📱 Ligar Agora</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Schema Markup específico para a cidade -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "Ar Condicionado em <?php echo $dados_cidade['nome']; ?>",
    "description": "Serviços especializados de ar condicionado em <?php echo $dados_cidade['nome']; ?>",
    "provider": {
        "@type": "HVACBusiness",
        "name": "ClimaTech",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "<?php echo $dados_cidade['nome']; ?>",
            "addressRegion": "SP"
        }
    },
    "areaServed": {
        "@type": "City",
        "name": "<?php echo $dados_cidade['nome']; ?>"
    },
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
                    "name": "Manutenção de Ar Condicionado"
                }
            }
        ]
    }
}
</script>

<?php include 'includes/footer.php'; ?>