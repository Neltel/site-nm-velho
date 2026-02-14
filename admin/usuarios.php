<?php
// admin/usuarios.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';

// Primeiro, vamos verificar e atualizar a estrutura da tabela se necessário
try {
    // Verificar se a coluna 'ativo' existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM administradores LIKE 'ativo'");
    $stmt->execute();
    $coluna_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$coluna_existe) {
        // Adicionar coluna ativo se não existir
        $pdo->exec("ALTER TABLE administradores ADD COLUMN ativo BOOLEAN DEFAULT TRUE AFTER email");
    }
} catch(PDOException $e) {
    // Ignorar erro se a coluna já existir
}

// Verificar se é super admin
$stmt = $pdo->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_atual = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar ações
if($_POST) {
    $nome = sanitize($_POST['nome']);
    $usuario = sanitize($_POST['usuario']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        if($acao == 'adicionar') {
            // Verificar se usuário já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM administradores WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $usuario_existe = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if($usuario_existe > 0) {
                $mensagem = "Erro: Já existe um usuário com este nome de usuário!";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO administradores (usuario, senha, nome, email, ativo) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$usuario, $senha_hash, $nome, $email, $ativo]);
                $mensagem = "Usuário adicionado com sucesso!";
            }
            
        } elseif($acao == 'editar' && $id > 0) {
            // Verificar se é o próprio usuário ou super admin
            if($id == $_SESSION['admin_id'] || $admin_atual['usuario'] == 'admin') {
                $sql = "UPDATE administradores SET nome = ?, email = ?, ativo = ?";
                $params = [$nome, $email, $ativo];
                
                if(!empty($senha)) {
                    $sql .= ", senha = ?";
                    $params[] = password_hash($senha, PASSWORD_DEFAULT);
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $mensagem = "Usuário atualizado com sucesso!";
            } else {
                $mensagem = "Erro: Você não tem permissão para editar este usuário!";
            }
        }
    } catch(PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}

// Excluir usuário
if($acao == 'excluir' && $id > 0) {
    // Não permitir excluir o próprio usuário ou o super admin
    if($id == $_SESSION['admin_id']) {
        $mensagem = "Erro: Você não pode excluir sua própria conta!";
    } elseif($id == 1) {
        $mensagem = "Erro: Não é possível excluir o usuário administrador principal!";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM administradores WHERE id = ?");
            $stmt->execute([$id]);
            $mensagem = "Usuário excluído com sucesso!";
            $acao = 'listar';
        } catch(PDOException $e) {
            $mensagem = "Erro ao excluir: " . $e->getMessage();
        }
    }
}

// Listar usuários
if($acao == 'listar') {
    $stmt = $pdo->prepare("SELECT * FROM administradores ORDER BY data_criacao DESC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="page-header">
        <h2>Gerenciar Usuários</h2>
        <?php if($admin_atual['usuario'] == 'admin'): ?>
        <a href="?acao=adicionar" class="btn btn-primary">+ Adicionar Usuário</a>
        <?php endif; ?>
    </div>

    <?php if($mensagem): ?>
    <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Data Criação</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): 
                    // Garantir que a chave 'ativo' existe
                    $ativo = isset($usuario['ativo']) ? $usuario['ativo'] : 1;
                    ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td>
                            <strong><?php echo $usuario['usuario']; ?></strong>
                            <?php if($usuario['usuario'] == 'admin'): ?>
                            <br><small class="text-muted badge badge-primary">Super Admin</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $usuario['nome']; ?></td>
                        <td><?php echo $usuario['email']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($usuario['data_criacao'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $ativo ? 'ativo' : 'inativo'; ?>">
                                <?php echo $ativo ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if($usuario['id'] == $_SESSION['admin_id'] || $admin_atual['usuario'] == 'admin'): ?>
                                <a href="?acao=editar&id=<?php echo $usuario['id']; ?>" class="btn-sm btn-edit">Editar</a>
                                <?php endif; ?>
                                
                                <?php if($admin_atual['usuario'] == 'admin' && $usuario['id'] != $_SESSION['admin_id'] && $usuario['id'] != 1): ?>
                                <a href="?acao=excluir&id=<?php echo $usuario['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
} elseif($acao == 'adicionar' || $acao == 'editar') {
    
    // Verificar permissão para adicionar/editar
    if($acao == 'adicionar' && $admin_atual['usuario'] != 'admin') {
        echo "<div class='alert alert-danger'>Você não tem permissão para adicionar usuários!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    $usuario = [];
    if($acao == 'editar' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM administradores WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$usuario) {
            echo "<div class='alert alert-danger'>Usuário não encontrado!</div>";
            include 'includes/footer-admin.php';
            exit;
        }
        
        // Verificar permissão para editar
        if($id != $_SESSION['admin_id'] && $admin_atual['usuario'] != 'admin') {
            echo "<div class='alert alert-danger'>Você não tem permissão para editar este usuário!</div>";
            include 'includes/footer-admin.php';
            exit;
        }
    }
    
    // Garantir que a chave 'ativo' existe no array
    if(!isset($usuario['ativo'])) {
        $usuario['ativo'] = 1;
    }
    ?>

    <div class="page-header">
        <h2><?php echo $acao == 'adicionar' ? 'Adicionar Usuário' : 'Editar Usuário'; ?></h2>
        <a href="usuarios.php" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if($mensagem): ?>
    <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo $usuario['nome'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="usuario">Nome de Usuário *</label>
                    <input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo $usuario['usuario'] ?? ''; ?>" 
                           <?php echo $acao == 'editar' ? 'readonly' : ''; ?> required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $usuario['email'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">
                    <?php echo $acao == 'adicionar' ? 'Senha *' : 'Nova Senha (deixe em branco para manter a atual)'; ?>
                </label>
                <input type="password" id="senha" name="senha" class="form-control" 
                       <?php echo $acao == 'adicionar' ? 'required' : ''; ?> minlength="6">
                <small class="form-text">Mínimo 6 caracteres</small>
            </div>

            <?php if($admin_atual['usuario'] == 'admin' || ($acao == 'editar' && $id == $_SESSION['admin_id'])): ?>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="ativo" <?php echo ($usuario['ativo'] ?? 1) ? 'checked' : ''; ?>>
                    <span class="checkmark"></span>
                    Usuário ativo
                </label>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $acao == 'adicionar' ? 'Adicionar Usuário' : 'Atualizar Usuário'; ?>
                </button>
                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <?php
}

include 'includes/footer-admin.php';
?>