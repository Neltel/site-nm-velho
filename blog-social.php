<?php
// blog-social.php

// Iniciar sessão e incluir cabeçalho (que já busca $config_site e $pdo)
// O seu header.php já faz isso:
include 'includes/header.php';

// 1. Verificar se o Instagram está ativo
$instagram_ativo = ($config_site['feed_instagram_ativo'] ?? '0') == '1';
$instagram_token = $config_site['feed_instagram_token'] ?? '';
$instagram_limite = intval($config_site['feed_instagram_limite'] ?? 6);

// Variáveis para o feed
$posts = [];
$erro_feed = '';

// 2. Lógica de busca dos posts
if ($instagram_ativo && !empty($instagram_token)) {
    // URL da API do Instagram (Graph API)
    // Usamos 'me/media' para obter os posts do usuário autenticado
    // Os campos (fields) solicitados são: id, caption (legenda), media_url (URL da imagem/vídeo), media_type, permalink (link para o post original)
    $api_url = "https://graph.instagram.com/me/media?fields=id,caption,media_url,media_type,permalink,timestamp&access_token={$instagram_token}&limit={$instagram_limite}";

    // Cache: Tenta ler do cache primeiro para não sobrecarregar a API
    $cache_file = 'cache/instagram_feed.json';
    $cache_time = 3600; // 1 hora de cache (em segundos)

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        // Se o cache é válido, lê o arquivo
        $data_json = file_get_contents($cache_file);
        $data = json_decode($data_json, true);
    } else {
        // Se o cache expirou ou não existe, faz a requisição à API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data_json = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($data_json, true);

        if ($http_code == 200 && isset($data['data'])) {
            $posts = $data['data'];
            // Salva o novo cache
            if (!file_exists('cache')) {
                mkdir('cache', 0777, true);
            }
            file_put_contents($cache_file, $data_json);
        } else {
            $erro_feed = "Erro ao buscar posts do Instagram. Código: {$http_code}. Mensagem: " . ($data['error']['message'] ?? 'Desconhecida.');
            // Tenta usar o cache antigo em caso de falha da API
            if (file_exists($cache_file)) {
                 $data_json_old = file_get_contents($cache_file);
                 $data_old = json_decode($data_json_old, true);
                 if (isset($data_old['data'])) {
                     $posts = $data_old['data'];
                     $erro_feed = "Falha na API, exibindo posts em cache.";
                 }
            }
        }
    }
    
    if (isset($data['data'])) {
        $posts = $data['data'];
    }

} elseif ($instagram_ativo && empty($instagram_token)) {
    $erro_feed = 'O feed do Instagram está ativo, mas o Token de Acesso não foi configurado no painel de administração.';
}

// Configurações de SEO/Meta
$page_title = 'Blog e Mídias Sociais - ' . $config_site['site_nome'];
$page_description = 'Acompanhe as últimas novidades, dicas e posts das nossas redes sociais, incluindo Instagram, TikTok e Facebook.';
// O restante do SEO será tratado pelo seu includes/header.php
?>

<main>
    <section class="hero-page">
        <div class="container">
            <h1>Nosso Blog e Feeds Sociais</h1>
            <p><?php echo htmlspecialchars($page_description); ?></p>
        </div>
    </section>

    <section class="social-feed-section py-5">
        <div class="container">
            <?php if ($erro_feed): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $erro_feed; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($posts)): ?>
                <h2>Posts Recentes do Instagram</h2>
                <div class="social-grid">
                    <?php foreach ($posts as $post): ?>
                        <div class="social-post-card">
                            <a href="<?php echo htmlspecialchars($post['permalink']); ?>" target="_blank" rel="noopener noreferrer">
                                <figure class="post-media">
                                    <?php if (($post['media_type'] ?? '') == 'VIDEO'): ?>
                                        <video controls poster="<?php echo htmlspecialchars($post['media_url'] ?? ''); ?>">
                                            <source src="<?php echo htmlspecialchars($post['media_url'] ?? ''); ?>" type="video/mp4">
                                            Seu navegador não suporta a tag de vídeo.
                                        </video>
                                        <div class="post-type-overlay"><i class="fas fa-video"></i></div>
                                    <?php elseif (($post['media_type'] ?? '') == 'CAROUSEL_ALBUM' || ($post['media_type'] ?? '') == 'IMAGE'): ?>
                                        <img src="<?php echo htmlspecialchars($post['media_url'] ?? ''); ?>" alt="<?php echo htmlspecialchars(substr($post['caption'] ?? '', 0, 100)); ?>..." loading="lazy">
                                        <?php if (($post['media_type'] ?? '') == 'CAROUSEL_ALBUM'): ?>
                                            <div class="post-type-overlay"><i class="fas fa-images"></i></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </figure>
                                <div class="post-caption">
                                    <p><?php echo nl2br(htmlspecialchars(substr($post['caption'] ?? '', 0, 150) . (strlen($post['caption'] ?? '') > 150 ? '...' : ''))); ?></p>
                                    <span class="post-date"><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['timestamp'] ?? '')); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-5">
                    <a href="<?php echo htmlspecialchars($config_site['instagram_url'] ?? '#'); ?>" class="btn btn-primary btn-lg" target="_blank" rel="noopener noreferrer">
                        Ver Mais no Instagram <i class="fab fa-instagram ms-2"></i>
                    </a>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <h2>Em Breve Mais Conteúdo!</h2>
                    <p>Parece que o feed ainda não está configurado ou não temos posts suficientes.</p>
                    <a href="<?php echo htmlspecialchars($config_site['instagram_url'] ?? '#'); ?>" class="btn btn-secondary mt-3" target="_blank" rel="noopener noreferrer">
                        Visite Nosso Instagram <i class="fab fa-instagram ms-2"></i>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if (($config_site['feed_tiktok_ativo'] ?? '0') == '1'): ?>
                <h2 class="mt-5 pt-3">Nosso TikTok</h2>
                <p>Aqui você pode inserir o código `iframe` ou `widget` de terceiros que o TikTok fornece (ou que um serviço como Elfsight fornece).</p>
                <?php endif; ?>

        </div>
    </section>
</main>

<?php 
// O seu footer.php já faz isso:
include 'includes/footer.php'; 
?>