<?php
// admin/materiais.php

include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

// Verificar se a tabela existe, se não, criar
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS materiais (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(50),
            preco_unitario DECIMAL(10,2),
            unidade_medida VARCHAR(20) DEFAULT 'unidade',
            estoque INT DEFAULT 0,
            estoque_minimo INT DEFAULT 0,
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch(PDOException $e) {
    // Tabela já existe, continuar normalmente
}

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = $_GET['sucesso'] ?? '';
$erro = $_GET['erro'] ?? '';

// Processar ações
if($_POST) {
    $nome = sanitize($_POST['nome']);
    $descricao = sanitize($_POST['descricao']);
    $categoria = sanitize($_POST['categoria']);
    $preco_unitario = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco_unitario']);
    $unidade_medida = sanitize($_POST['unidade_medida']);
    $estoque = $_POST['estoque'] ?: 0;
    $estoque_minimo = $_POST['estoque_minimo'] ?: 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        if($acao == 'adicionar') {
            $stmt = $pdo->prepare("INSERT INTO materiais (nome, descricao, categoria, preco_unitario, unidade_medida, estoque, estoque_minimo, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descricao, $categoria, $preco_unitario, $unidade_medida, $estoque, $estoque_minimo, $ativo]);
            
            echo "<script>window.location.href = 'materiais.php?sucesso=Material adicionado com sucesso!';</script>";
            exit;
            
        } elseif($acao == 'editar' && $id > 0) {
            $stmt = $pdo->prepare("UPDATE materiais SET nome = ?, descricao = ?, categoria = ?, preco_unitario = ?, unidade_medida = ?, estoque = ?, estoque_minimo = ?, ativo = ? WHERE id = ?");
            $stmt->execute([$nome, $descricao, $categoria, $preco_unitario, $unidade_medida, $estoque, $estoque_minimo, $ativo, $id]);
            
            echo "<script>window.location.href = 'materiais.php?sucesso=Material atualizado com sucesso!';</script>";
            exit;
        }
    } catch(PDOException $e) {
        $erro = "Erro: " . $e->getMessage();
    }
}

// Excluir material - VERSÃO CORRIGIDA COM VERIFICAÇÃO DE ORÇAMENTOS E SERVIÇOS
if($acao == 'excluir' && $id > 0) {
    try {
        // Primeiro verificar se o material está sendo usado em algum orçamento ou serviço
        $stmt = $pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM orcamento_materiais WHERE material_id = ?) as total_orcamentos,
                (SELECT COUNT(*) FROM servico_materiais WHERE material_id = ?) as total_servicos
        ");
        $stmt->execute([$id, $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $total_uso = $result['total_orcamentos'] + $result['total_servicos'];
        
        if($total_uso > 0) {
            // Material está sendo usado - marcar como inativo em vez de excluir
            $stmt = $pdo->prepare("UPDATE materiais SET ativo = 0 WHERE id = ?");
            $stmt->execute([$id]);
            
            $mensagem_uso = "Material marcado como inativo porque está sendo usado em ";
            if($result['total_orcamentos'] > 0 && $result['total_servicos'] > 0) {
                $mensagem_uso .= "orçamentos e serviços";
            } elseif($result['total_orcamentos'] > 0) {
                $mensagem_uso .= "orçamentos";
            } else {
                $mensagem_uso .= "serviços";
            }
            $mensagem_uso .= ". Para excluir permanentemente, primeiro remova-o de todos os registros.";
            
            echo "<script>
                alert('$mensagem_uso');
                window.location.href = 'materiais.php?sucesso=" . urlencode($mensagem_uso) . "';
            </script>";
            exit;
        } else {
            // Material não está em uso - pode excluir normalmente
            $stmt = $pdo->prepare("DELETE FROM materiais WHERE id = ?");
            $stmt->execute([$id]);
            
            echo "<script>
                alert('Material excluído com sucesso!');
                window.location.href = 'materiais.php?sucesso=Material excluído com sucesso!';
            </script>";
            exit;
        }
        
    } catch(PDOException $e) {
        echo "<script>
            alert('Erro ao processar material: " . addslashes($e->getMessage()) . "');
            window.location.href = 'materiais.php';
        </script>";
        exit;
    }
}

// Ajustar estoque - VERSÃO CORRIGIDA
if($acao == 'ajustar_estoque' && $id > 0 && isset($_POST['novo_estoque'])) {
    $novo_estoque = $_POST['novo_estoque'];
    
    try {
        $stmt = $pdo->prepare("UPDATE materiais SET estoque = ? WHERE id = ?");
        $stmt->execute([$novo_estoque, $id]);
        
        echo "<script>window.location.href = 'materiais.php?sucesso=Estoque ajustado com sucesso!';</script>";
        exit;
        
    } catch(PDOException $e) {
        echo "<script>
            alert('Erro ao ajustar estoque: " . addslashes($e->getMessage()) . "');
            window.location.href = 'materiais.php';
        </script>";
        exit;
    }
}

// Buscar estatísticas para os cards
try {
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total,
        SUM(estoque) as total_estoque,
        SUM(CASE WHEN estoque <= estoque_minimo THEN 1 ELSE 0 END) as estoque_baixo,
        SUM(CASE WHEN estoque = 0 THEN 1 ELSE 0 END) as estoque_zero
        FROM materiais WHERE ativo = 1");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Valor total em estoque
    $stmt = $pdo->prepare("SELECT SUM(preco_unitario * estoque) as valor_total FROM materiais WHERE ativo = 1");
    $stmt->execute();
    $valor_total = $stmt->fetch(PDO::FETCH_ASSOC)['valor_total'];
} catch(PDOException $e) {
    // Tabela pode não existir ainda
}

// Listar materiais
if($acao == 'listar') {
    $categoria_filter = $_GET['categoria'] ?? '';
    $estoque_filter = $_GET['estoque'] ?? '';
    $mostrar_inativos = $_GET['mostrar_inativos'] ?? false;
    
    // Construir query com filtros
    $sql = "SELECT * FROM materiais WHERE 1=1";
    $params = [];
    
    if(!$mostrar_inativos) {
        $sql .= " AND ativo = 1";
    }
    
    if($categoria_filter) {
        $sql .= " AND categoria = ?";
        $params[] = $categoria_filter;
    }
    
    if($estoque_filter == 'baixo') {
        $sql .= " AND estoque <= estoque_minimo AND estoque > 0";
    } elseif($estoque_filter == 'zero') {
        $sql .= " AND estoque = 0";
    }
    
    $sql .= " ORDER BY ativo DESC, nome";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Categorias disponíveis
    $stmt = $pdo->prepare("SELECT DISTINCT categoria FROM materiais WHERE categoria IS NOT NULL ORDER BY categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-boxes me-2"></i>
            Gerenciar Materiais
        </div>
        <div class="alert alert-warning alert-modern">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Controle de estoque e cadastro de materiais para orçamentos
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

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-boxes"></i>
                <div class="number"><?php echo $stats['total'] ?? 0; ?></div>
                <div>Total Materiais</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-cubes"></i>
                <div class="number"><?php echo $stats['total_estoque'] ?? 0; ?></div>
                <div>Itens em Estoque</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="number"><?php echo $stats['estoque_baixo'] ?? 0; ?></div>
                <div>Estoque Baixo</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-danger">
                <i class="fas fa-times-circle"></i>
                <div class="number"><?php echo $stats['estoque_zero'] ?? 0; ?></div>
                <div>Sem Estoque</div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Materiais
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="?acao=adicionar" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-plus me-2"></i>Adicionar Material
                    </a>
                    <?php if($mostrar_inativos): ?>
                        <a href="materiais.php" class="btn btn-outline-primary btn-modern">
                            <i class="fas fa-eye me-2"></i>Ocultar Inativos
                        </a>
                    <?php else: ?>
                        <a href="?mostrar_inativos=1" class="btn btn-outline-primary btn-modern">
                            <i class="fas fa-eye-slash me-2"></i>Mostrar Inativos
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <div class="filters-section mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Categoria</label>
                        <select id="categoriaFilter" class="form-control form-control-modern">
                            <option value="">Todas as categorias</option>
                            <?php foreach($categorias as $categoria): ?>
                            <option value="<?php echo $categoria; ?>" <?php echo $categoria_filter == $categoria ? 'selected' : ''; ?>>
                                <?php echo $categoria; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Situação do Estoque</label>
                        <select id="estoqueFilter" class="form-control form-control-modern">
                            <option value="">Todo estoque</option>
                            <option value="baixo" <?php echo $estoque_filter == 'baixo' ? 'selected' : ''; ?>>Estoque Baixo</option>
                            <option value="zero" <?php echo $estoque_filter == 'zero' ? 'selected' : ''; ?>>Sem Estoque</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" id="searchMateriais" placeholder="Buscar materiais..." class="form-control form-control-modern">
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" onclick="filtrarMateriais()" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                    <button type="button" onclick="limparFiltros()" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-modern" id="tabelaMateriais">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Material</th>
                            <th width="120">Categoria</th>
                            <th width="120">Preço Unitário</th>
                            <th width="140">Estoque</th>
                            <th width="100">Unidade</th>
                            <th width="100">Status</th>
                            <th width="180" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($materiais)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum material encontrado</p>
                                    <a href="?acao=adicionar" class="btn btn-primary-modern btn-modern">
                                        <i class="fas fa-plus me-2"></i>Adicionar Primeiro Material
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($materiais as $material): 
                            $estoque_class = '';
                            $estoque_icon = '';
                            if($material['estoque'] == 0) {
                                $estoque_class = 'out-of-stock';
                                $estoque_icon = 'fas fa-times-circle';
                            } elseif($material['estoque'] <= $material['estoque_minimo']) {
                                $estoque_class = 'low-stock';
                                $estoque_icon = 'fas fa-exclamation-triangle';
                            } else {
                                $estoque_icon = 'fas fa-check-circle';
                            }
                            
                            $status_class = $material['ativo'] ? 'ativo' : 'inativo';
                            $status_text = $material['ativo'] ? 'Ativo' : 'Inativo';
                            ?>
                            <tr class="<?php echo $estoque_class; ?>-row <?php echo !$material['ativo'] ? 'inactive-row' : ''; ?>">
                                <td>
                                    <strong class="text-primary">#<?php echo $material['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="material-info">
                                        <strong><?php echo htmlspecialchars($material['nome']); ?></strong>
                                        <?php if($material['descricao']): ?>
                                        <p class="material-description"><?php echo htmlspecialchars(substr($material['descricao'], 0, 80)); ?><?php echo strlen($material['descricao']) > 80 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($material['categoria']): ?>
                                    <span class="category-badge category-<?php echo strtolower(str_replace(' ', '-', $material['categoria'])); ?>">
                                        <?php echo $material['categoria']; ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-success price-value">
                                        R$ <?php echo number_format($material['preco_unitario'], 2, ',', '.'); ?>
                                    </strong>
                                </td>
                                <td>
                                    <div class="estoque-info">
                                        <i class="<?php echo $estoque_icon; ?> me-1"></i>
                                        <span class="estoque-value <?php echo $estoque_class; ?>">
                                            <?php echo $material['estoque']; ?>
                                        </span>
                                        <?php if($material['estoque_minimo'] > 0): ?>
                                        <small class="estoque-minimo">min: <?php echo $material['estoque_minimo']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="unidade-badge"><?php echo $material['unidade_medida']; ?></span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?acao=editar&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=ajustar_estoque&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-warning btn-modern" title="Ajustar Estoque">
                                            <i class="fas fa-cubes"></i>
                                        </a>
                                        <?php if($material['ativo']): ?>
                                        <a href="?acao=excluir&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-danger btn-modern" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este material?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="?acao=reativar&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-success btn-modern" title="Reativar" onclick="return confirm('Tem certeza que deseja reativar este material?')">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function filtrarMateriais() {
        const categoria = document.getElementById('categoriaFilter').value;
        const estoque = document.getElementById('estoqueFilter').value;
        const url = new URL(window.location.href);
        
        if(categoria) {
            url.searchParams.set('categoria', categoria);
        } else {
            url.searchParams.delete('categoria');
        }
        
        if(estoque) {
            url.searchParams.set('estoque', estoque);
        } else {
            url.searchParams.delete('estoque');
        }
        
        window.location.href = url.toString();
    }
    
    function limparFiltros() {
        window.location.href = 'materiais.php';
    }
    
    // Busca em tempo real
    document.getElementById('searchMateriais').addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaMateriais tbody tr');
        
        linhas.forEach(linha => {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
    </script>

    <?php
} elseif($acao == 'adicionar' || $acao == 'editar') {
    
    $material = [];
    if($acao == 'editar' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM materiais WHERE id = ?");
        $stmt->execute([$id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$material) {
            echo "<div class='alert alert-danger alert-modern'>Material não encontrado!</div>";
            include 'includes/footer-admin.php';
            exit;
        }
    }
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-<?php echo $acao == 'adicionar' ? 'plus' : 'edit'; ?> me-2"></i>
            <?php echo $acao == 'adicionar' ? 'Adicionar Material' : 'Editar Material'; ?>
        </div>
        <div class="header-actions">
            <a href="materiais.php" class="btn btn-outline-primary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
            </a>
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

    <div class="modern-card">
        <div class="card-body">
            <form method="POST" class="config-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Informações do Material
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome do Material *</label>
                                <input type="text" name="nome" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($material['nome'] ?? ''); ?>" 
                                       placeholder="Ex: Tubo de Cobre 1/4" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control form-control-modern" rows="4" 
                                          placeholder="Descreva características, especificações técnicas..."><?php echo htmlspecialchars($material['descricao'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-cog me-2"></i>Configurações
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-control form-control-modern">
                                    <option value="">Selecione uma categoria</option>
                                    <option value="Tubulação" <?php echo ($material['categoria'] ?? '') == 'Tubulação' ? 'selected' : ''; ?>>Tubulação</option>
                                    <option value="Isolamento" <?php echo ($material['categoria'] ?? '') == 'Isolamento' ? 'selected' : ''; ?>>Isolamento</option>
                                    <option value="Fiação" <?php echo ($material['categoria'] ?? '') == 'Fiação' ? 'selected' : ''; ?>>Fiação</option>
                                    <option value="Disjuntores" <?php echo ($material['categoria'] ?? '') == 'Disjuntores' ? 'selected' : ''; ?>>Disjuntores</option>
                                    <option value="Acessórios" <?php echo ($material['categoria'] ?? '') == 'Acessórios' ? 'selected' : ''; ?>>Acessórios</option>
                                    <option value="Ferramentas" <?php echo ($material['categoria'] ?? '') == 'Ferramentas' ? 'selected' : ''; ?>>Ferramentas</option>
                                    <option value="Outros" <?php echo ($material['categoria'] ?? '') == 'Outros' ? 'selected' : ''; ?>>Outros</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Unidade de Medida</label>
                                <select name="unidade_medida" class="form-control form-control-modern">
                                    <option value="unidade" <?php echo ($material['unidade_medida'] ?? '') == 'unidade' ? 'selected' : ''; ?>>Unidade</option>
                                    <option value="metro" <?php echo ($material['unidade_medida'] ?? '') == 'metro' ? 'selected' : ''; ?>>Metro</option>
                                    <option value="caixa" <?php echo ($material['unidade_medida'] ?? '') == 'caixa' ? 'selected' : ''; ?>>Caixa</option>
                                    <option value="rolo" <?php echo ($material['unidade_medida'] ?? '') == 'rolo' ? 'selected' : ''; ?>>Rolo</option>
                                    <option value="par" <?php echo ($material['unidade_medida'] ?? '') == 'par' ? 'selected' : ''; ?>>Par</option>
                                    <option value="litro" <?php echo ($material['unidade_medida'] ?? '') == 'litro' ? 'selected' : ''; ?>>Litro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-dollar-sign me-2"></i>Preço
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Preço Unitário *</label>
                                <input type="text" name="preco_unitario" class="form-control form-control-modern money" 
                                       value="<?php echo formatarMoeda($material['preco_unitario'] ?? 0); ?>" 
                                       placeholder="R$ 0,00" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-cubes me-2"></i>Estoque
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Estoque Atual</label>
                                <input type="number" name="estoque" class="form-control form-control-modern" 
                                       value="<?php echo $material['estoque'] ?? 0; ?>" min="0">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Estoque Mínimo</label>
                                <input type="number" name="estoque_minimo" class="form-control form-control-modern" 
                                       value="<?php echo $material['estoque_minimo'] ?? 0; ?>" min="0">
                                <small class="form-text text-muted">Alerta quando estoque atingir este valor</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-toggle-on me-2"></i>Status
                            </h5>
                            
                            <div class="mb-3 form-check form-check-modern">
                                <input type="checkbox" class="form-check-input" name="ativo" value="1" 
                                       <?php echo ($material['ativo'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Material ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $acao == 'adicionar' ? 'Adicionar Material' : 'Atualizar Material'; ?>
                    </button>
                    <a href="materiais.php" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php
} elseif($acao == 'ajustar_estoque' && $id > 0) {
    
    $material = [];
    $stmt = $pdo->prepare("SELECT * FROM materiais WHERE id = ?");
    $stmt->execute([$id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$material) {
        echo "<div class='alert alert-danger alert-modern'>Material não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-cubes me-2"></i>
            Ajustar Estoque
        </div>
        <div class="header-actions">
            <a href="materiais.php" class="btn btn-outline-primary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-body">
            <div class="config-section mb-4">
                <h5 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>Informações do Material
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>Nome:</label>
                            <span class="info-value"><?php echo $material['nome']; ?></span>
                        </div>
                        <div class="info-item">
                            <label>Categoria:</label>
                            <span class="info-value"><?php echo $material['categoria'] ?: '-'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>Estoque Atual:</label>
                            <span class="info-value <?php echo $material['estoque'] == 0 ? 'out-of-stock' : ($material['estoque'] <= $material['estoque_minimo'] ? 'low-stock' : ''); ?>">
                                <?php echo $material['estoque']; ?> <?php echo $material['unidade_medida']; ?>
                            </span>
                        </div>
                        <?php if($material['estoque_minimo'] > 0): ?>
                        <div class="info-item">
                            <label>Estoque Mínimo:</label>
                            <span class="info-value"><?php echo $material['estoque_minimo']; ?> <?php echo $material['unidade_medida']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" class="config-form">
                <div class="config-section">
                    <h5 class="section-title">
                        <i class="fas fa-edit me-2"></i>Ajuste de Estoque
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Novo Estoque *</label>
                                <input type="number" name="novo_estoque" class="form-control form-control-modern" 
                                       value="<?php echo $material['estoque']; ?>" min="0" required>
                                <small class="form-text text-muted">Digite a nova quantidade em estoque</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>Atualizar Estoque
                    </button>
                    <a href="materiais.php" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php
} elseif($acao == 'reativar' && $id > 0) {
    // Reativar material inativo
    try {
        $stmt = $pdo->prepare("UPDATE materiais SET ativo = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        echo "<script>
            alert('Material reativado com sucesso!');
            window.location.href = 'materiais.php?sucesso=Material reativado com sucesso!';
        </script>";
        exit;
        
    } catch(PDOException $e) {
        echo "<script>
            alert('Erro ao reativar material: " . addslashes($e->getMessage()) . "');
            window.location.href = 'materiais.php';
        </script>";
        exit;
    }
}

// Estilos específicos para materiais
?>
<style>
.stats-card {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 1.5rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
}

.stats-card.stats-warning {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.stats-card.stats-danger {
    background: linear-gradient(135deg, #ef4444, #f87171);
}

.stats-card i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-card .number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.filters-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.material-info {
    line-height: 1.4;
}

.material-description {
    font-size: 0.875rem;
    color: var(--gray);
    margin: 0.5rem 0 0 0;
    line-height: 1.4;
}

.category-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-block;
}

.category-tubulação { background: #3b82f6; color: white; }
.category-isolamento { background: #10b981; color: white; }
.category-fiação { background: #f59e0b; color: white; }
.category-disjuntores { background: #ef4444; color: white; }
.category-acessórios { background: #8b5cf6; color: white; }
.category-ferramentas { background: #6b7280; color: white; }
.category-outros { background: #64748b; color: white; }

.estoque-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.estoque-value {
    font-weight: 600;
    font-size: 1rem;
}

.estoque-value.low-stock {
    color: #f59e0b;
}

.estoque-value.out-of-stock {
    color: #ef4444;
}

.estoque-minimo {
    font-size: 0.75rem;
    color: var(--gray);
}

.low-stock-row {
    background: rgba(245, 158, 11, 0.05) !important;
    border-left: 4px solid #f59e0b;
}

.out-of-stock-row {
    background: rgba(239, 68, 68, 0.05) !important;
    border-left: 4px solid #ef4444;
}

.inactive-row {
    background: rgba(107, 114, 128, 0.05) !important;
    border-left: 4px solid #6b7280;
    opacity: 0.7;
}

.unidade-badge {
    background: #e2e8f0;
    color: #4a5568;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-block;
}

.status-ativo { background: #10b981; color: white; }
.status-inativo { background: #6b7280; color: white; }

.price-value {
    font-size: 1rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.action-buttons .btn {
    padding: 6px 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-item label {
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
}

.info-value {
    color: var(--gray);
    text-align: right;
}

/* Responsivo */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .estoque-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .material-info {
        min-width: 200px;
    }
}

/* Formatação de moeda */
.money {
    text-align: right;
}

.money::placeholder {
    text-align: left;
}
</style>

<script>
// Máscara para formato de moeda
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('.money');
    
    moneyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = 'R$ ' + value;
        });
    });
});
</script>

<?php include 'includes/footer-admin.php'; ?>