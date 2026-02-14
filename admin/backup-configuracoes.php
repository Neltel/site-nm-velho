<?php
// admin/backup-configuracoes.php
include 'includes/auth.php';
include 'includes/header-admin.php';
include_once '../includes/config.php';

$mensagem = '';

// Backup das configurações
if(isset($_POST['backup'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM config_site ORDER BY categoria, ordem");
        $stmt->execute();
        $configuracoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $backup_data = [
            'data_backup' => date('Y-m-d H:i:s'),
            'total_configs' => count($configuracoes),
            'configuracoes' => $configuracoes
        ];
        
        $backup_json = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'backup-configs-' . date('Y-m-d-H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $backup_json;
        exit;
        
    } catch(PDOException $e) {
        $mensagem = "<div class='alert alert-danger'>❌ Erro ao criar backup: " . $e->getMessage() . "</div>";
    }
}

// Restaurar backup
if(isset($_POST['restore']) && isset($_FILES['backup_file'])) {
    try {
        if($_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
            $conteudo = file_get_contents($_FILES['backup_file']['tmp_name']);
            $backup_data = json_decode($conteudo, true);
            
            if($backup_data && isset($backup_data['configuracoes'])) {
                $pdo->beginTransaction();
                
                // Limpar configurações atuais
                $pdo->exec("DELETE FROM config_site");
                
                // Restaurar configurações do backup
                $stmt = $pdo->prepare("INSERT INTO config_site (chave, valor, tipo, categoria, descricao, ordem) VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach($backup_data['configuracoes'] as $config) {
                    $stmt->execute([
                        $config['chave'],
                        $config['valor'],
                        $config['tipo'],
                        $config['categoria'],
                        $config['descricao'],
                        $config['ordem'] ?? 0
                    ]);
                }
                
                $pdo->commit();
                $mensagem = "<div class='alert alert-success'>✅ Backup restaurado com sucesso! " . 
                           count($backup_data['configuracoes']) . " configurações importadas.</div>";
            } else {
                $mensagem = "<div class='alert alert-danger'>❌ Arquivo de backup inválido!</div>";
            }
        }
    } catch(PDOException $e) {
        $pdo->rollBack();
        $mensagem = "<div class='alert alert-danger'>❌ Erro ao restaurar backup: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="page-header">
    <h2>💾 Backup das Configurações</h2>
    <p>Faça backup ou restaure as configurações do site</p>
</div>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>📥 Exportar Backup</h4>
            </div>
            <div class="card-body">
                <p>Exporte todas as configurações atuais para um arquivo JSON.</p>
                
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM config_site");
                $stmt->execute();
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                ?>
                
                <div class="info-box">
                    <strong>Configurações atuais:</strong> <?php echo $total; ?> registros
                </div>
                
                <form method="POST">
                    <button type="submit" name="backup" class="btn btn-success">
                        💾 Baixar Backup Completo
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>📤 Restaurar Backup</h4>
            </div>
            <div class="card-body">
                <p>Restaura as configurações a partir de um arquivo JSON de backup.</p>
                
                <div class="alert alert-warning">
                    ⚠️ <strong>Atenção:</strong> Esta ação irá substituir TODAS as configurações atuais!
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="backup_file" class="form-label">Selecione o arquivo de backup:</label>
                        <input type="file" id="backup_file" name="backup_file" class="form-control" accept=".json" required>
                    </div>
                    
                    <button type="submit" name="restore" class="btn btn-warning" 
                            onclick="return confirm('⚠️ ATENÇÃO: Isso irá substituir TODAS as configurações atuais. Tem certeza?')">
                        🔄 Restaurar Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h4>📋 Configurações Atuais</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Chave</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT categoria, chave, valor, tipo FROM config_site ORDER BY categoria, chave");
                    $stmt->execute();
                    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($configs as $config):
                    ?>
                    <tr>
                        <td><span class="badge bg-primary"><?php echo $config['categoria']; ?></span></td>
                        <td><code><?php echo $config['chave']; ?></code></td>
                        <td>
                            <?php 
                            if($config['tipo'] == 'boolean') {
                                echo $config['valor'] == '1' ? '✅ Ativo' : '❌ Inativo';
                            } else {
                                echo strlen($config['valor']) > 50 ? 
                                    substr($config['valor'], 0, 50) . '...' : 
                                    $config['valor'];
                            }
                            ?>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo $config['tipo']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.card {
    margin-bottom: 20px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    border-left: 4px solid #28a745;
}

.badge {
    font-size: 0.7rem;
}
</style>

<?php include 'includes/footer-admin.php'; ?>