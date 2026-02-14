<?php
// admin/update-configs.php
include 'includes/auth.php';
include 'includes/header-admin.php';
include_once '../includes/config.php';

$mensagem = '';

// Atualizar estrutura do banco se necessário
if(isset($_POST['update_database'])) {
    try {
        $pdo->beginTransaction();
        
        // Adicionar coluna de ordem se não existir
        $pdo->exec("ALTER TABLE config_site ADD COLUMN IF NOT EXISTS ordem INT DEFAULT 0");
        
        // Atualizar ordens padrão
        $ordens = [
            'site_nome' => 1, 'site_slogan' => 2, 'site_logo' => 3, 'site_favicon' => 4, 'site_endereco' => 5,
            'site_telefone' => 1, 'site_telefone2' => 2, 'site_email' => 3, 'site_whatsapp' => 4, 'site_horario_funcionamento' => 5,
            'cor_primaria' => 1, 'cor_secundaria' => 2, 'cor_accent' => 3, 'cor_texto' => 4, 'cor_fundo' => 5, 'cor_header' => 6, 'cor_footer' => 7,
            'meta_titulo' => 1, 'meta_descricao' => 2, 'palavras_chave' => 3, 'google_analytics' => 4, 'google_maps_api' => 5,
            'facebook_url' => 1, 'instagram_url' => 2, 'linkedin_url' => 3, 'youtube_url' => 4,
            'whatsapp_ativo' => 1, 'agendamento_online' => 2, 'formulario_contato' => 3, 'depoimentos_ativo' => 4, 'blog_ativo' => 5,
            'manutencao' => 1, 'manutencao_mensagem' => 2, 'debug_mode' => 3,
            'cidades_atendidas' => 1, 'raio_atendimento' => 2,
            'especialidades' => 1, 'anos_experiencia' => 2, 'clientes_atendidos' => 3
        ];
        
        foreach($ordens as $chave => $ordem) {
            $stmt = $pdo->prepare("UPDATE config_site SET ordem = ? WHERE chave = ?");
            $stmt->execute([$ordem, $chave]);
        }
        
        $pdo->commit();
        $mensagem = "<div class='alert alert-success'>✅ Estrutura do banco atualizada com sucesso!</div>";
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $mensagem = "<div class='alert alert-danger'>❌ Erro ao atualizar banco: " . $e->getMessage() . "</div>";
    }
}

// Verificar status atual
try {
    // Verificar se coluna ordem existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM config_site LIKE 'ordem'");
    $stmt->execute();
    $ordem_exists = $stmt->fetch() !== false;
    
    // Contar configurações
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM config_site");
    $stmt->execute();
    $total_configs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Verificar configurações faltantes
    $configs_necessarias = ['site_favicon', 'site_horario_funcionamento', 'cor_header', 'cor_footer', 'meta_titulo', 'google_analytics', 'google_maps_api', 'linkedin_url', 'youtube_url', 'formulario_contato', 'depoimentos_ativo', 'blog_ativo', 'manutencao_mensagem', 'debug_mode', 'raio_atendimento', 'especialidades', 'anos_experiencia', 'clientes_atendidos'];
    
    $stmt = $pdo->prepare("SELECT chave FROM config_site WHERE chave IN ('" . implode("','", $configs_necessarias) . "')");
    $stmt->execute();
    $configs_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $configs_faltantes = array_diff($configs_necessarias, $configs_existentes);
    
} catch(PDOException $e) {
    $mensagem = "<div class='alert alert-danger'>❌ Erro ao verificar status: " . $e->getMessage() . "</div>";
}
?>

<div class="page-header">
    <h2>🔄 Atualizar Sistema de Configurações</h2>
    <p>Execute migrações e atualizações do banco de dados</p>
</div>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>📊 Status Atual</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Configurações totais
                        <span class="badge bg-primary rounded-pill"><?php echo $total_configs; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Coluna "ordem"
                        <span class="badge <?php echo $ordem_exists ? 'bg-success' : 'bg-warning'; ?> rounded-pill">
                            <?php echo $ordem_exists ? '✅ Presente' : '❌ Faltando'; ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Configurações faltantes
                        <span class="badge <?php echo empty($configs_faltantes) ? 'bg-success' : 'bg-warning'; ?> rounded-pill">
                            <?php echo count($configs_faltantes); ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>⚡ Ações de Atualização</h4>
            </div>
            <div class="card-body">
                <?php if(!$ordem_exists || !empty($configs_faltantes)): ?>
                <div class="alert alert-info">
                    <strong>Atualização disponível!</strong> Execute a atualização para adicionar novos recursos.
                </div>
                
                <form method="POST">
                    <button type="submit" name="update_database" class="btn btn-primary w-100">
                        🔄 Executar Atualização
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-success">
                    <strong>✅ Sistema atualizado!</strong> Todas as configurações estão em dia.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($configs_faltantes)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h4>📋 Configurações que serão adicionadas</h4>
    </div>
    <div class="card-body">
        <ul>
            <?php foreach($configs_faltantes as $config): ?>
            <li><code><?php echo $config; ?></code></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<style>
.card {
    margin-bottom: 20px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.list-group-item {
    border: 1px solid rgba(0,0,0,0.125);
}
</style>

<?php include 'includes/footer-admin.php'; ?>