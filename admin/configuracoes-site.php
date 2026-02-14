<?php
// admin/configuracoes-site.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include_once '../includes/config.php';

$mensagem = '';
$sucesso = '';
$erro = '';

// Função para upload de arquivos
function uploadArquivo($file, $nome_base, $pasta_destino) {
    if($file['error'] == UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
        
        if(in_array($extensao, $extensoes_permitidas)) {
            $nome_arquivo = $nome_base . '.' . $extensao;
            $caminho_destino = $pasta_destino . $nome_arquivo;
            
            // Criar pasta se não existir
            if (!file_exists($pasta_destino)) {
                mkdir($pasta_destino, 0777, true);
            }
            
            // Verificar e remover arquivos antigos com o mesmo nome base
            $arquivos_antigos = glob($pasta_destino . $nome_base . '.*');
            foreach($arquivos_antigos as $arquivo_antigo) {
                if(is_file($arquivo_antigo)) {
                    unlink($arquivo_antigo);
                }
            }
            
            if(move_uploaded_file($file['tmp_name'], $caminho_destino)) {
                return $nome_arquivo;
            }
        }
    }
    return null;
}

// Processar atualização de configurações
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Atualizar configurações de texto
        if(isset($_POST['config'])) {
            foreach($_POST['config'] as $chave => $valor) {
                // Truncar valores muito longos para evitar erro
                $valor_truncado = $valor;
                if (strlen($valor) > 10000) {
                    $valor_truncado = substr($valor, 0, 10000);
                    error_log("Aviso: Valor truncado para chave: " . $chave);
                }
                
                $stmt = $pdo->prepare("UPDATE config_site SET valor = ? WHERE chave = ?");
                $stmt->execute([trim($valor_truncado), $chave]);
            }
        }
        
        // Processar upload de logo
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $logo = uploadArquivo($_FILES['logo'], 'logo', '../assets/images/');
            if($logo) {
                $stmt = $pdo->prepare("INSERT INTO config_site (chave, valor, tipo, categoria, descricao) 
                                     VALUES ('site_logo', ?, 'image', 'empresa', 'Logo do site') 
                                     ON DUPLICATE KEY UPDATE valor = ?");
                $stmt->execute([$logo, $logo]);
            }
        }
        
        // Processar upload de favicon
        if(isset($_FILES['favicon']) && $_FILES['favicon']['error'] == 0) {
            $favicon = uploadArquivo($_FILES['favicon'], 'favicon', '../assets/images/');
            if($favicon) {
                $stmt = $pdo->prepare("INSERT INTO config_site (chave, valor, tipo, categoria, descricao) 
                                     VALUES ('site_favicon', ?, 'image', 'empresa', 'Favicon do site') 
                                     ON DUPLICATE KEY UPDATE valor = ?");
                $stmt->execute([$favicon, $favicon]);
            }
        }
        
        $pdo->commit();
        $sucesso = "Configurações atualizadas com sucesso!";
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao atualizar configurações: " . $e->getMessage();
        error_log("Erro SQL: " . $e->getMessage());
    }
}

// Buscar configurações atuais ordenadas
$stmt = $pdo->prepare("SELECT chave, valor, tipo, categoria, descricao FROM config_site ORDER BY categoria, ordem, chave");
$stmt->execute();
$configuracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar por categoria
$configs_por_categoria = [];
foreach($configuracoes as $config) {
    $configs_por_categoria[$config['categoria']][] = $config;
}

// Buscar arquivos atuais
$logo_atual = getConfig('site_logo');
$favicon_atual = getConfig('site_favicon');
?>

<div class="page-header">
    <div class="page-title">
        <i class="fas fa-cogs me-2"></i>
        Configurações do Site
    </div>
    <div class="alert alert-warning alert-modern">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Personalize as informações, aparência e funcionalidades do seu site
    </div>
</div>

<!-- Alertas -->
<?php if ($sucesso): ?>
    <div class="alert alert-success alert-modern">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $sucesso; ?>
    </div>
<?php endif; ?>

<?php if ($erro): ?>
    <div class="alert alert-danger alert-modern">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $erro; ?>
    </div>
<?php endif; ?>

<!-- Navegação Rápida -->
<div class="modern-card mb-4">
    <div class="card-body">
        <h5 class="section-title mb-3">
            <i class="fas fa-compass me-2"></i>Navegação Rápida
        </h5>
        <div class="nav-links-grid">
            <a href="#empresa" class="nav-link-item">
                <i class="fas fa-building me-2"></i>Empresa
            </a>
            <a href="#contato" class="nav-link-item">
                <i class="fas fa-phone me-2"></i>Contato
            </a>
            <a href="#cores" class="nav-link-item">
                <i class="fas fa-palette me-2"></i>Cores
            </a>
            <a href="#seo" class="nav-link-item">
                <i class="fas fa-search me-2"></i>SEO
            </a>
            <a href="#social" class="nav-link-item">
                <i class="fas fa-share-alt me-2"></i>Redes Sociais
            </a>
            <a href="#cidades" class="nav-link-item">
                <i class="fas fa-map-marker-alt me-2"></i>Cidades
            </a>
            <a href="#funcionalidades" class="nav-link-item">
                <i class="fas fa-sliders-h me-2"></i>Funcionalidades
            </a>
            <a href="#sistema" class="nav-link-item">
                <i class="fas fa-tools me-2"></i>Sistema
            </a>
        </div>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" class="config-form" id="configForm">
    
    <!-- Logo e Favicon -->
    <div class="modern-card mb-4" id="empresa">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-images me-2"></i>Logo e Favicon
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="config-section">
                        <h6 class="section-title">
                            <i class="fas fa-image me-2"></i>Upload da Logo
                        </h6>
                        <div class="mb-3">
                            <input type="file" id="logo" name="logo" class="form-control form-control-modern" accept="image/*">
                            <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WEBP, SVG (Recomendado: PNG transparente 300x100px)</small>
                        </div>
                        
                        <?php if($logo_atual && file_exists('../assets/images/' . $logo_atual)): ?>
                        <div class="current-file-preview">
                            <label class="form-label">Logo atual:</label>
                            <div class="file-preview">
                                <img src="../assets/images/<?php echo $logo_atual; ?>?v=<?php echo time(); ?>" 
                                     alt="Logo atual" 
                                     class="preview-image">
                                <div class="file-info">
                                    <span class="file-name"><?php echo $logo_atual; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="config-section">
                        <h6 class="section-title">
                            <i class="fas fa-flag me-2"></i>Upload do Favicon
                        </h6>
                        <div class="mb-3">
                            <input type="file" id="favicon" name="favicon" class="form-control form-control-modern" accept="image/x-icon,image/vnd.microsoft.icon,.ico">
                            <small class="form-text text-muted">Formatos: ICO, PNG (Recomendado: ICO 32x32px ou PNG 64x64px)</small>
                        </div>
                        
                        <?php if($favicon_atual && file_exists('../assets/images/' . $favicon_atual)): ?>
                        <div class="current-file-preview">
                            <label class="form-label">Favicon atual:</label>
                            <div class="file-preview">
                                <img src="../assets/images/<?php echo $favicon_atual; ?>?v=<?php echo time(); ?>" 
                                     alt="Favicon atual" 
                                     class="preview-image favicon">
                                <div class="file-info">
                                    <span class="file-name"><?php echo $favicon_atual; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da Empresa -->
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-building me-2"></i>Informações da Empresa
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['empresa'] ?? [] as $config): 
                    if($config['chave'] != 'site_logo' && $config['chave'] != 'site_favicon'): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Informações de Contato -->
    <div class="modern-card mb-4" id="contato">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-phone me-2"></i>Informações de Contato
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['contato'] ?? [] as $config): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Cores do Site -->
    <div class="modern-card mb-4" id="cores">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-palette me-2"></i>Cores do Site
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['cores'] ?? [] as $config): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <div class="color-picker-container">
                            <input type="color" 
                                   name="config[<?php echo $config['chave']; ?>]" 
                                   class="form-control color-picker" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                            <input type="text" 
                                   class="form-control form-control-modern color-text" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>"
                                   onchange="document.querySelector('input[name=\"config[<?php echo $config['chave']; ?>]\"]').value = this.value">
                        </div>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- SEO e Analytics -->
    <div class="modern-card mb-4" id="seo">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-search me-2"></i>SEO e Analytics
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['seo'] ?? [] as $config): ?>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="4"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Redes Sociais COMPLETAS -->
    <div class="modern-card mb-4" id="social">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-share-alt me-2"></i>Redes Sociais e Plataformas
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php 
                // Lista completa de redes sociais com ícones
                $redes_sociais = [
                    'facebook_url' => ['fab fa-facebook', 'Facebook', '#1877f2'],
                    'instagram_url' => ['fab fa-instagram', 'Instagram', '#e4405f'],
                    'whatsapp_url' => ['fab fa-whatsapp', 'WhatsApp', '#25d366'],
                    'linkedin_url' => ['fab fa-linkedin', 'LinkedIn', '#0077b5'],
                    'youtube_url' => ['fab fa-youtube', 'YouTube', '#ff0000'],
                    'tiktok_url' => ['fab fa-tiktok', 'TikTok', '#000000'],
                    'twitter_url' => ['fab fa-twitter', 'Twitter/X', '#1da1f2'],
                    'pinterest_url' => ['fab fa-pinterest', 'Pinterest', '#bd081c'],
                    'telegram_url' => ['fab fa-telegram', 'Telegram', '#0088cc'],
                    'getninja_url' => ['fas fa-tools', 'GetNinja', '#ff6b00'],
                    'olx_url' => ['fas fa-tag', 'OLX', '#00aaff'],
                    'mercado_livre_url' => ['fas fa-shopping-cart', 'Mercado Livre', '#ffe600'],
                    'google_meu_negocio_url' => ['fab fa-google', 'Google Meu Negócio', '#4285f4'],
                    'tripadvisor_url' => ['fab fa-tripadvisor', 'TripAdvisor', '#00af87'],
                    'airbnb_url' => ['fab fa-airbnb', 'Airbnb', '#ff5a5f'],
                    'booking_url' => ['fas fa-hotel', 'Booking', '#003580'],
                    'spotify_url' => ['fab fa-spotify', 'Spotify', '#1db954'],
                    'soundcloud_url' => ['fab fa-soundcloud', 'SoundCloud', '#ff3300'],
                    'twitch_url' => ['fab fa-twitch', 'Twitch', '#9146ff'],
                    'discord_url' => ['fab fa-discord', 'Discord', '#5865f2'],
                    'github_url' => ['fab fa-github', 'GitHub', '#181717'],
                    'behance_url' => ['fab fa-behance', 'Behance', '#1769ff'],
                    'dribbble_url' => ['fab fa-dribbble', 'Dribbble', '#ea4c89'],
                    'medium_url' => ['fab fa-medium', 'Medium', '#000000'],
                    'reddit_url' => ['fab fa-reddit', 'Reddit', '#ff4500'],
                    'quora_url' => ['fab fa-quora', 'Quora', '#b92b27'],
                    'vimeo_url' => ['fab fa-vimeo', 'Vimeo', '#1ab7ea'],
                    'flickr_url' => ['fab fa-flickr', 'Flickr', '#0063dc'],
                    'snapchat_url' => ['fab fa-snapchat', 'Snapchat', '#fffc00'],
                    'wechat_url' => ['fab fa-weixin', 'WeChat', '#07c160'],
                    'line_url' => ['fab fa-line', 'LINE', '#00c300'],
                    'vk_url' => ['fab fa-vk', 'VK', '#4680c2'],
                    'tumblr_url' => ['fab fa-tumblr', 'Tumblr', '#36465d'],
                    'blogger_url' => ['fab fa-blogger', 'Blogger', '#ff5722'],
                    'wordpress_url' => ['fab fa-wordpress', 'WordPress', '#21759b']
                ];
                
                foreach($redes_sociais as $chave => $info): 
                    $icone = $info[0];
                    $nome = $info[1];
                    $cor = $info[2];
                    
                    // Buscar valor atual desta rede social
                    $valor_atual = '';
                    foreach($configs_por_categoria['social'] ?? [] as $config) {
                        if($config['chave'] === $chave) {
                            $valor_atual = $config['valor'];
                            break;
                        }
                    }
                ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="<?php echo $icone; ?> me-2" style="color: <?php echo $cor; ?>"></i>
                            <?php echo $nome; ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: <?php echo $cor; ?>; color: white;">
                                <i class="<?php echo $icone; ?>"></i>
                            </span>
                            <input type="url" 
                                   name="config[<?php echo $chave; ?>]" 
                                   class="form-control form-control-modern" 
                                   placeholder="https://..."
                                   value="<?php echo htmlspecialchars($valor_atual); ?>">
                        </div>
                        <small class="form-text text-muted"><?php echo $chave; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Cidades Atendidas -->
    <div class="modern-card mb-4" id="cidades">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-map-marker-alt me-2"></i>Área de Atendimento
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['cidades'] ?? [] as $config): ?>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="6"
                                  placeholder="Digite cada cidade em uma linha separada"
                                  id="config_cidades_atendidas"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                        
                        <?php if($config['chave'] == 'cidades_atendidas'): ?>
                        <div class="cidades-preview mt-3">
                            <h6 class="section-title">
                                <i class="fas fa-eye me-2"></i>Preview das Cidades
                            </h6>
                            <div class="preview-box" id="preview-cidades">
                                <?php
                                $cidades_array = explode("\n", $config['valor']);
                                foreach($cidades_array as $cidade) {
                                    if(trim($cidade)) {
                                        echo "<div class='cidade-item'>• " . htmlspecialchars(trim($cidade)) . "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Funcionalidades -->
    <div class="modern-card mb-4" id="funcionalidades">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-sliders-h me-2"></i>Funcionalidades do Site
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['funcionalidades'] ?? [] as $config): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-check-modern form-switch">
                            <input type="checkbox" 
                                   name="config[<?php echo $config['chave']; ?>]" 
                                   value="1" 
                                   <?php echo $config['valor'] == '1' ? 'checked' : ''; ?>
                                   class="form-check-input"
                                   id="config_<?php echo $config['chave']; ?>">
                            <label class="form-check-label" for="config_<?php echo $config['chave']; ?>">
                                <?php echo $config['descricao']; ?>
                            </label>
                        </div>
                        <input type="hidden" name="config[<?php echo $config['chave']; ?>]" value="0">
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sistema -->
    <div class="modern-card mb-4" id="sistema">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-tools me-2"></i>Configurações do Sistema
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['sistema'] ?? [] as $config): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="3"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php elseif($config['tipo'] == 'boolean'): ?>
                        <select name="config[<?php echo $config['chave']; ?>]" 
                                class="form-control form-control-modern">
                            <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativo</option>
                            <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Inativo</option>
                        </select>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Informações de Negócio -->
    <?php if(isset($configs_por_categoria['negocio'])): ?>
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-briefcase me-2"></i>Informações do Negócio
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($configs_por_categoria['negocio'] ?? [] as $config): ?>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $config['descricao']; ?></label>
                        <?php if($config['tipo'] == 'textarea'): ?>
                        <textarea name="config[<?php echo $config['chave']; ?>]" 
                                  class="form-control form-control-modern" 
                                  rows="4"><?php echo htmlspecialchars($config['valor']); ?></textarea>
                        <?php else: ?>
                        <input type="<?php echo $config['tipo']; ?>" 
                               name="config[<?php echo $config['chave']; ?>]" 
                               class="form-control form-control-modern" 
                               value="<?php echo htmlspecialchars($config['valor']); ?>">
                        <?php endif; ?>
                        <small class="form-text text-muted"><?php echo $config['chave']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-actions mt-4">
        <button type="submit" class="btn btn-primary-modern btn-modern">
            <i class="fas fa-save me-2"></i>Salvar Todas as Configurações
        </button>
        <button type="button" id="btnReset" class="btn btn-outline-primary btn-modern">
            <i class="fas fa-undo me-2"></i>Restaurar Valores Padrão
        </button>
        <a href="dashboard.php" class="btn btn-outline-primary btn-modern">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>
</form>

<!-- Estilos específicos para configurações -->
<style>
.nav-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.nav-link-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    color: var(--dark-color);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-link-item:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}

.config-section {
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.section-title {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
}

.color-picker-container {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.color-picker {
    width: 70px;
    height: 45px;
    padding: 0;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.color-picker:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.current-file-preview {
    margin-top: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
}

.file-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.preview-image {
    max-width: 200px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px;
    background: white;
}

.preview-image.favicon {
    width: 64px;
    height: 64px;
}

.file-info {
    flex: 1;
}

.file-name {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: var(--gray);
    background: #f8fafc;
    padding: 0.5rem;
    border-radius: 6px;
    display: inline-block;
}

.cidades-preview {
    margin-top: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.preview-box {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    max-height: 200px;
    overflow-y: auto;
}

.cidade-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
    color: var(--gray);
}

.cidade-item:last-child {
    border-bottom: none;
}

.input-group-text {
    background: var(--primary-color);
    color: white;
    border: 2px solid var(--primary-color);
    font-weight: 600;
    min-width: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-check-modern.form-switch {
    padding-left: 3rem;
}

.form-check-modern.form-switch .form-check-input {
    width: 3rem;
    height: 1.5rem;
    margin-left: -3rem;
    background-color: #e2e8f0;
    border: 2px solid #cbd5e0;
}

.form-check-modern.form-switch .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-modern .form-check-label {
    font-weight: 500;
    color: var(--dark-color);
    cursor: pointer;
}

.form-actions {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border: none;
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    position: sticky;
    bottom: 2rem;
    margin-top: 3rem;
}

/* Responsivo */
@media (max-width: 768px) {
    .nav-links-grid {
        grid-template-columns: 1fr;
    }
    
    .color-picker-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .file-preview {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .config-section {
        padding: 1rem;
    }
}
</style>

<script>
// Atualizar valor hexadecimal quando mudar cor
document.querySelectorAll('.color-picker').forEach(picker => {
    picker.addEventListener('input', function() {
        const textInput = this.parentNode.querySelector('.color-text');
        textInput.value = this.value;
    });
});

// Atualizar color picker quando mudar texto
document.querySelectorAll('.color-text').forEach(textInput => {
    textInput.addEventListener('input', function() {
        const colorPicker = this.parentNode.querySelector('.color-picker');
        colorPicker.value = this.value;
    });
});

// Preview em tempo real das cidades
const cidadesTextarea = document.getElementById('config_cidades_atendidas');
if(cidadesTextarea) {
    cidadesTextarea.addEventListener('input', function() {
        const preview = document.getElementById('preview-cidades');
        const cidades = this.value.split('\n');
        
        let html = '';
        cidades.forEach(cidade => {
            if(cidade.trim()) {
                html += `<div class="cidade-item">• ${cidade.trim()}</div>`;
            }
        });
        
        preview.innerHTML = html || '<div class="cidade-item text-muted">Nenhuma cidade cadastrada</div>';
    });
}

// Confirmação antes de restaurar valores padrão
document.getElementById('btnReset').addEventListener('click', function(e) {
    if(confirm('⚠️ ATENÇÃO: Isso irá restaurar TODAS as configurações para os valores padrão. As configurações atuais serão perdidas. Tem certeza?')) {
        // Marcar todos os campos para reset
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.value = field.defaultValue;
        });
        
        // Disparar eventos de change para atualizar previews
        document.querySelectorAll('.color-picker, #config_cidades_atendidas').forEach(field => {
            field.dispatchEvent(new Event('input'));
        });
        
        alert('✅ Valores restaurados para o padrão. Clique em "Salvar" para aplicar as mudanças.');
    }
});

// Navegação suave
document.querySelectorAll('.nav-link-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if(targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Validação do formulário
document.getElementById('configForm').addEventListener('submit', function(e) {
    let isValid = true;
    let errorMessage = '';
    
    // Validar emails
    const emailFields = document.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if(field.value && !field.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            isValid = false;
            errorMessage += `• E-mail inválido: ${field.previousElementSibling.textContent}\n`;
            field.style.borderColor = '#ef4444';
        } else {
            field.style.borderColor = '';
        }
    });
    
    // Validar URLs
    const urlFields = document.querySelectorAll('input[type="url"]');
    urlFields.forEach(field => {
        if(field.value && !field.value.match(/^https?:\/\/.+\..+/)) {
            isValid = false;
            errorMessage += `• URL inválida: ${field.previousElementSibling.textContent}\n`;
            field.style.borderColor = '#ef4444';
        } else {
            field.style.borderColor = '';
        }
    });
    
    if(!isValid) {
        e.preventDefault();
        alert('❌ Por favor, corrija os seguintes erros:\n\n' + errorMessage);
    } else {
        // Mostrar loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
        submitBtn.disabled = true;
        
        // Restaurar botão após 3 segundos (caso o formulário não envie)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    }
});

// Preview de imagem para uploads
document.getElementById('logo').addEventListener('change', function(e) {
    previewImage(this, '.current-file-preview:first-child .preview-image');
});

document.getElementById('favicon').addEventListener('change', function(e) {
    previewImage(this, '.current-file-preview:last-child .preview-image');
});

function previewImage(input, selector) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector(selector);
            if(preview) {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer-admin.php'; ?>