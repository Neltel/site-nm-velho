<?php
// servicos.php
include 'includes/config.php';
include 'includes/header.php';
?>

<section class="section-title">
    <div class="container">
        <h2>Nossos Serviços</h2>
        <p>Conheça todos os nossos serviços especializados em climatização</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="services-categories">
            <div class="category-filter">
                <button class="btn-filter active" data-category="all">Todos</button>
                <button class="btn-filter" data-category="instalacao">Instalação</button>
                <button class="btn-filter" data-category="manutencao">Manutenção</button>
                <button class="btn-filter" data-category="limpeza">Limpeza</button>
                <button class="btn-filter" data-category="reparo">Reparo</button>
                <button class="btn-filter" data-category="remocao">Remoção</button>
            </div>
        </div>

        <div class="services-grid">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
            $stmt->execute();
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($servicos as $servico):
                $icones = [
                    'instalacao' => '🔧',
                    'manutencao' => '🛠️',
                    'limpeza' => '🧹',
                    'reparo' => '🔍',
                    'remocao' => '🚚'
                ];
            ?>
            <div class="service-card" data-category="<?php echo $servico['categoria']; ?>">
                <div class="service-icon">
                    <?php echo $icones[$servico['categoria']] ?? '❄️'; ?>
                </div>
                <div class="service-content">
                    <span class="service-category"><?php echo ucfirst($servico['categoria']); ?></span>
                    <h3><?php echo $servico['nome']; ?></h3>
                    <p><?php echo $servico['descricao']; ?></p>
                    
                    <?php if($servico['preco_base'] > 0): ?>
                    <div class="service-price">
                        <span class="price-label">A partir de</span>
                        <span class="price-value"><?php echo formatarMoeda($servico['preco_base']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="service-actions">
                        <a href="orcamento.php?servico=<?php echo $servico['id']; ?>" class="btn">Solicitar Orçamento</a>
                        <a href="agendamento.php?servico=<?php echo $servico['id']; ?>" class="btn btn-outline">Agendar</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// Filtro de categorias
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const serviceCards = document.querySelectorAll('.service-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Atualizar botões ativos
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar serviços
            serviceCards.forEach(card => {
                if(category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>