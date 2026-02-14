<?php
// admin/produtos.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';

// Processar upload de imagem
function uploadImagem($file) {
    if($file['error'] == UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($extensao, $extensoes_permitidas)) {
            $nome_arquivo = uniqid() . '.' . $extensao;
            $caminho_destino = '../uploads/produtos/' . $nome_arquivo;
            
            if(move_uploaded_file($file['tmp_name'], $caminho_destino)) {
                return $nome_arquivo;
            }
        }
    }
    return null;
}

// Processar ações
if($_POST) {
    $nome = sanitize($_POST['nome']);
    $descricao = sanitize($_POST['descricao']);
    $preco = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco']);
    $categoria = sanitize($_POST['categoria']);
    $marca = sanitize($_POST['marca']);
    $btus = $_POST['btus'] ?: null;
    $estoque = $_POST['estoque'] ?: 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        if($acao == 'adicionar') {
            $imagem = null;
            if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
                $imagem = uploadImagem($_FILES['imagem']);
            }
            
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, categoria, marca, btus, estoque, imagem, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descricao, $preco, $categoria, $marca, $btus, $estoque, $imagem, $ativo]);
            $mensagem = "Produto adicionado com sucesso!";
            
        } elseif($acao == 'editar' && $id > 0) {
            // Buscar produto atual para manter imagem se não for alterada
            $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto_atual = $stmt->fetch(PDO::FETCH_ASSOC);
            $imagem = $produto_atual['imagem'];
            
            // Se enviou nova imagem, fazer upload
            if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
                $nova_imagem = uploadImagem($_FILES['imagem']);
                if($nova_imagem) {
                    // Deletar imagem antiga se existir
                    if($imagem && file_exists('../uploads/produtos/' . $imagem)) {
                        unlink('../uploads/produtos/' . $imagem);
                    }
                    $imagem = $nova_imagem;
                }
            }
            
            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, categoria = ?, marca = ?, btus = ?, estoque = ?, imagem = ?, ativo = ? WHERE id = ?");
            $stmt->execute([$nome, $descricao, $preco, $categoria, $marca, $btus, $estoque, $imagem, $ativo, $id]);
            $mensagem = "Produto atualizado com sucesso!";
        }
    } catch(PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}

// Excluir produto
if($acao == 'excluir' && $id > 0) {
    try {
        // Buscar imagem para deletar
        $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($produto['imagem'] && file_exists('../uploads/produtos/' . $produto['imagem'])) {
            unlink('../uploads/produtos/' . $produto['imagem']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $mensagem = "Produto excluído com sucesso!";
        $acao = 'listar';
    } catch(PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
    }
}

// Listar produtos
if($acao == 'listar') {
    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY nome");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="page-header">
        <h2>Gerenciar Produtos</h2>
        <a href="?acao=adicionar" class="btn btn-primary">+ Adicionar Produto</a>
    </div>

    <?php if($mensagem): ?>
    <div class="alert alert-success"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Marca</th>
                        <th>BTUs</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produtos as $produto): ?>
                    <tr>
                        <td>
                            <?php if($produto['imagem']): ?>
                            <img src="../uploads/produtos/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="product-thumb">
                            <?php else: ?>
                            <div class="product-thumb placeholder">❄️</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $produto['nome']; ?></strong>
                            <br><small class="text-muted"><?php echo $produto['categoria']; ?></small>
                        </td>
                        <td><?php echo $produto['marca'] ?: '-'; ?></td>
                        <td><?php echo $produto['btus'] ? $produto['btus'] . ' BTUs' : '-'; ?></td>
                        <td><?php echo formatarMoeda($produto['preco']); ?></td>
                        <td>
                            <span class="estoque-badge <?php echo $produto['estoque'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                <?php echo $produto['estoque']; ?> un
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $produto['ativo'] ? 'ativo' : 'inativo'; ?>">
                                <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?acao=editar&id=<?php echo $produto['id']; ?>" class="btn-sm btn-edit">Editar</a>
                                <a href="?acao=excluir&id=<?php echo $produto['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
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
    
    $produto = [];
    if($acao == 'editar' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$produto) {
            echo "<div class='alert alert-danger'>Produto não encontrado!</div>";
            include 'includes/footer-admin.php';
            exit;
        }
    }
    ?>

    <div class="page-header">
        <h2><?php echo $acao == 'adicionar' ? 'Adicionar Produto' : 'Editar Produto'; ?></h2>
        <a href="produtos.php" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if($mensagem): ?>
    <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome do Produto *</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo $produto['nome'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="categoria">Categoria *</label>
                    <select id="categoria" name="categoria" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="Split" <?php echo ($produto['categoria'] ?? '') == 'Split' ? 'selected' : ''; ?>>Split</option>
                        <option value="Janela" <?php echo ($produto['categoria'] ?? '') == 'Janela' ? 'selected' : ''; ?>>Janela</option>
                        <option value="Portátil" <?php echo ($produto['categoria'] ?? '') == 'Portátil' ? 'selected' : ''; ?>>Portátil</option>
                        <option value="Cassete" <?php echo ($produto['categoria'] ?? '') == 'Cassete' ? 'selected' : ''; ?>>Cassete</option>
                        <option value="Piso-Teto" <?php echo ($produto['categoria'] ?? '') == 'Piso-Teto' ? 'selected' : ''; ?>>Piso-Teto</option>
                        <option value="Acessórios" <?php echo ($produto['categoria'] ?? '') == 'Acessórios' ? 'selected' : ''; ?>>Acessórios</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-control" value="<?php echo $produto['marca'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="btus">BTUs</label>
                    <select id="btus" name="btus" class="form-control">
                        <option value="">Selecione</option>
                        <option value="7000" <?php echo ($produto['btus'] ?? '') == '7000' ? 'selected' : ''; ?>>7.000 BTUs</option>
                        <option value="9000" <?php echo ($produto['btus'] ?? '') == '9000' ? 'selected' : ''; ?>>9.000 BTUs</option>
                        <option value="12000" <?php echo ($produto['btus'] ?? '') == '12000' ? 'selected' : ''; ?>>12.000 BTUs</option>
                        <option value="18000" <?php echo ($produto['btus'] ?? '') == '18000' ? 'selected' : ''; ?>>18.000 BTUs</option>
                        <option value="24000" <?php echo ($produto['btus'] ?? '') == '24000' ? 'selected' : ''; ?>>24.000 BTUs</option>
                        <option value="30000" <?php echo ($produto['btus'] ?? '') == '30000' ? 'selected' : ''; ?>>30.000 BTUs</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo $produto['descricao'] ?? ''; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="preco">Preço *</label>
                    <input type="text" id="preco" name="preco" class="form-control money" value="<?php echo formatarMoeda($produto['preco'] ?? 0); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="estoque">Estoque *</label>
                    <input type="number" id="estoque" name="estoque" class="form-control" value="<?php echo $produto['estoque'] ?? 0; ?>" required min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="imagem">Imagem do Produto</label>
                <input type="file" id="imagem" name="imagem" class="form-control" accept="image/*">
                <?php if(isset($produto['imagem']) && $produto['imagem']): ?>
                <div class="current-image">
                    <p>Imagem atual:</p>
                    <img src="../uploads/produtos/<?php echo $produto['imagem']; ?>" alt="Imagem atual" class="current-image-preview">
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="ativo" <?php echo ($produto['ativo'] ?? 1) ? 'checked' : ''; ?>>
                    <span class="checkmark"></span>
                    Produto ativo
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $acao == 'adicionar' ? 'Adicionar Produto' : 'Atualizar Produto'; ?>
                </button>
                <a href="produtos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <?php
}

include 'includes/footer-admin.php';
?>