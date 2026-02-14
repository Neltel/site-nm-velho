<?php
// admin/clientes.php
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = '';
$erro = '';

if($_POST && $acao == 'editar' && $id > 0) {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    
    // NOVOS CAMPOS DE ENDEREÇO
    $rua = sanitize($_POST['rua'] ?? '');
    $numero = sanitize($_POST['numero'] ?? '');
    $bairro = sanitize($_POST['bairro'] ?? '');
    $cidade = sanitize($_POST['cidade'] ?? '');

    try {
        // ATUALIZAÇÃO DA QUERY: Adicionando os 4 novos campos de endereço
        $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, rua = ?, numero = ?, bairro = ?, cidade = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $telefone, $rua, $numero, $bairro, $cidade, $id]);
        $sucesso = "Cliente atualizado com sucesso!";
    } catch(PDOException $e) {
        $erro = "Erro: " . $e->getMessage();
    }
}

// Excluir cliente
if($acao == 'excluir' && $id > 0) {
    try {
        // Verificar se o cliente tem orçamentos ou agendamentos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos WHERE cliente_id = ?");
        $stmt->execute([$id]);
        $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE cliente_id = ?");
        $stmt->execute([$id]);
        $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if($total_orcamentos > 0 || $total_agendamentos > 0) {
            $erro = "Não é possível excluir este cliente pois existem orçamentos ou agendamentos vinculados a ele.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $sucesso = "Cliente excluído com sucesso!";
        }
        $acao = 'listar';
    } catch(PDOException $e) {
        $erro = "Erro ao excluir: " . $e->getMessage();
    }
}

// Buscar estatísticas para os cards
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes");
    $stmt->execute();
    $total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes WHERE DATE(data_cadastro) = CURDATE()");
    $stmt->execute();
    $clientes_hoje = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos");
    $stmt->execute();
    $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos");
    $stmt->execute();
    $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch(PDOException $e) {
    // Tabela pode não existir ainda
}

// Listar clientes
if($acao == 'listar') {
    $pagina = $_GET['pagina'] ?? 1;
    $por_pagina = 15;
    $offset = ($pagina - 1) * $por_pagina;
    
    // Buscar clientes
    $stmt = $pdo->prepare("SELECT * FROM clientes ORDER BY data_cadastro DESC LIMIT $por_pagina OFFSET $offset");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total de clientes para paginação
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes");
    $stmt->execute();
    $total_filtrado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_filtrado / $por_pagina);
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-users me-2"></i>
            Gerenciar Clientes
        </div>
        <div class="alert alert-info alert-modern">
            <i class="fas fa-info-circle me-2"></i>
            Cadastro e gestão de clientes do sistema
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
                <i class="fas fa-users"></i>
                <div class="number"><?php echo $total_clientes ?? 0; ?></div>
                <div>Total Clientes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-user-plus"></i>
                <div class="number"><?php echo $clientes_hoje ?? 0; ?></div>
                <div>Cadastros Hoje</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <div class="number"><?php echo $total_orcamentos ?? 0; ?></div>
                <div>Total Orçamentos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-calendar-check"></i>
                <div class="number"><?php echo $total_agendamentos ?? 0; ?></div>
                <div>Total Agendamentos</div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Clientes
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <span class="total-info me-3">
                        Mostrando <?php echo count($clientes); ?> de <?php echo $total_filtrado; ?> clientes
                    </span>
                    <button class="btn btn-outline-primary btn-modern" onclick="exportarClientes()">
                        <i class="fas fa-download me-2"></i>Exportar CSV
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <div class="filters-section mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Buscar Clientes</label>
                        <input type="text" id="searchClientes" placeholder="Buscar por nome, e-mail, telefone..." class="form-control form-control-modern">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ordenar por</label>
                        <select id="ordenarClientes" class="form-control form-control-modern">
                            <option value="recentes">Mais Recentes</option>
                            <option value="antigos">Mais Antigos</option>
                            <option value="nome">Nome A-Z</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-modern" id="tabelaClientes">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Cliente</th>
                            <th width="200">Contato</th>
                            <th width="120">Data Cadastro</th>
                            <th width="120">Orçamentos</th>
                            <th width="150" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum cliente encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($clientes as $cliente): 
                            // Buscar total de orçamentos do cliente
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos WHERE cliente_id = ?");
                            $stmt->execute([$cliente['id']]);
                            $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            
                            // Buscar total de agendamentos do cliente
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE cliente_id = ?");
                            $stmt->execute([$cliente['id']]);
                            $total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-primary">#<?php echo $cliente['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="client-info">
                                        <strong><?php echo htmlspecialchars($cliente['nome']); ?></strong>
                                        <?php 
                                        $endereco_resumido = '';
                                        $rua_num = trim(($cliente['rua'] ?? '') . ' ' . ($cliente['numero'] ?? ''));
                                        $bairro_cidade = trim(($cliente['bairro'] ?? '') . ' / ' . ($cliente['cidade'] ?? ''));
                                        
                                        if ($rua_num && $bairro_cidade) {
                                            $endereco_resumido = $rua_num . ' | ' . $bairro_cidade;
                                        } elseif ($rua_num) {
                                            $endereco_resumido = $rua_num;
                                        } elseif ($bairro_cidade) {
                                            $endereco_resumido = $bairro_cidade;
                                        } elseif ($cliente['endereco'] ?? '') { 
                                            // Fallback: se o cliente for antigo e tiver apenas o campo 'endereco'
                                            $endereco_resumido = $cliente['endereco'];
                                        }
                                        
                                        if($endereco_resumido): 
                                        ?>
                                        <p class="client-address"><?php echo htmlspecialchars(substr($endereco_resumido, 0, 80)); ?><?php echo strlen($endereco_resumido) > 80 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <?php if($cliente['email']): ?>
                                        <div class="contact-item">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            <span><?php echo $cliente['email']; ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($cliente['telefone']): ?>
                                        <div class="contact-item">
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <span><?php echo $cliente['telefone']; ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('d/m/Y', strtotime($cliente['data_cadastro'])); ?>
                                        <br>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($cliente['data_cadastro'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="stats-mini">
                                        <div class="stat-badge">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                            <span><?php echo $total_orcamentos; ?></span>
                                        </div>
                                        <div class="stat-badge">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo $total_agendamentos; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?acao=editar&id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=visualizar&id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-info btn-modern" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?acao=excluir&id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-danger btn-modern" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if($total_paginas > 1): ?>
            <div class="pagination-modern mt-4">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>" class="pagination-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function exportarClientes() {
        window.location.href = 'exportar_clientes.php';
    }
    
    // Busca em tempo real
    document.getElementById('searchClientes').addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaClientes tbody tr');
        
        linhas.forEach(linha => {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
    
    // Ordenação
    document.getElementById('ordenarClientes').addEventListener('change', function(e) {
        // Implementar lógica de ordenação aqui
        console.log('Ordenar por:', e.target.value);
    });
    </script>

    <?php
} elseif($acao == 'editar' && $id > 0) {
    
    $cliente = [];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$cliente) {
        echo "<div class='alert alert-danger alert-modern'>Cliente não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-edit me-2"></i>
            Editar Cliente
        </div>
        <div class="header-actions">
            <a href="clientes.php" class="btn btn-outline-primary btn-modern">
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
                                <i class="fas fa-user me-2"></i>Informações Pessoais
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="nome" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($cliente['nome']); ?>" 
                                       placeholder="Digite o nome completo do cliente" required>
                            </div>
                            
                            <div class="mb-3">
                            
                            <h5 class="section-title mt-4">
                                <i class="fas fa-map-marker-alt me-2"></i>Informações de Endereço
                            </h5>

                            <div class="mb-3">
                                <label class="form-label">Rua e Número</label>
                                <div class="row g-3">
                                    <div class="col-md-9">
                                        <input type="text" name="rua" class="form-control form-control-modern" 
                                               value="<?php echo htmlspecialchars($cliente['rua'] ?? ''); ?>" 
                                               placeholder="Rua / Avenida">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="numero" class="form-control form-control-modern" 
                                               value="<?php echo htmlspecialchars($cliente['numero'] ?? ''); ?>" 
                                               placeholder="Nº">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Bairro e Cidade</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" name="bairro" class="form-control form-control-modern" 
                                               value="<?php echo htmlspecialchars($cliente['bairro'] ?? ''); ?>" 
                                               placeholder="Bairro">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="cidade" class="form-control form-control-modern" 
                                               value="<?php echo htmlspecialchars($cliente['cidade'] ?? ''); ?>" 
                                               placeholder="Cidade">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-address-book me-2"></i>Contato
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($cliente['email']); ?>" 
                                       placeholder="exemplo@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Telefone *</label>
                                <input type="tel" name="telefone" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($cliente['telefone']); ?>" 
                                       placeholder="(11) 99999-9999" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>Atualizar Cliente
                    </button>
                    <a href="clientes.php" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php
} elseif($acao == 'visualizar' && $id > 0) {
    
    $cliente = [];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$cliente) {
        echo "<div class='alert alert-danger alert-modern'>Cliente não encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    // Buscar orçamentos do cliente
    $stmt = $pdo->prepare("SELECT o.*, s.nome as servico_nome 
                          FROM orcamentos o 
                          LEFT JOIN servicos s ON o.servico_id = s.id 
                          WHERE o.cliente_id = ? 
                          ORDER BY o.data_solicitacao DESC");
    $stmt->execute([$id]);
    $orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar agendamentos do cliente
    $stmt = $pdo->prepare("SELECT a.*, s.nome as servico_nome 
                          FROM agendamentos a 
                          LEFT JOIN servicos s ON a.servico_id = s.id 
                          WHERE a.cliente_id = ? 
                          ORDER BY a.data_agendamento DESC");
    $stmt->execute([$id]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-eye me-2"></i>
            Detalhes do Cliente
        </div>
        <div class="header-actions">
            <a href="?acao=editar&id=<?php echo $cliente['id']; ?>" class="btn btn-primary-modern btn-modern">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="clientes.php" class="btn btn-outline-primary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="modern-card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-md-6">
                    <div class="config-section">
                        <h5 class="section-title">
                            <i class="fas fa-user me-2"></i>Informações Pessoais
                        </h5>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Nome:</label>
                                <span class="info-value"><?php echo $cliente['nome']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>E-mail:</label>
                                <span class="info-value"><?php echo $cliente['email'] ?: '<span class="text-muted">-</span>'; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Telefone:</label>
                                <span class="info-value"><?php echo $cliente['telefone'] ?: '<span class="text-muted">-</span>'; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Data de Cadastro:</label>
                                <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($cliente['data_cadastro'])); ?></span>
                            </div>
                            <?php if(($cliente['rua'] ?? '') || ($cliente['endereco'] ?? '')): ?>
                            <div class="info-item">
                                <label>Rua:</label>
                                <span class="info-value"><?php echo htmlspecialchars($cliente['rua'] ?? ''); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Nº:</label>
                                <span class="info-value"><?php echo htmlspecialchars($cliente['numero'] ?? ''); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Bairro:</label>
                                <span class="info-value"><?php echo htmlspecialchars($cliente['bairro'] ?? ''); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Cidade:</label>
                                <span class="info-value"><?php echo htmlspecialchars($cliente['cidade'] ?? ''); ?></span>
                            </div>
                            <?php elseif($cliente['endereco'] ?? ''): // Fallback para clientes antigos ?>
                            <div class="info-item">
                                <label>Endereço Completo:</label>
                                <span class="info-value"><?php echo htmlspecialchars($cliente['endereco']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="col-md-6">
                    <div class="config-section">
                        <h5 class="section-title">
                            <i class="fas fa-chart-bar me-2"></i>Estatísticas
                        </h5>
                        
                        <div class="stats-grid">
                            <div class="stat-card-mini">
                                <div class="stat-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo count($orcamentos); ?></div>
                                    <div class="stat-label">Orçamentos</div>
                                </div>
                            </div>
                            <div class="stat-card-mini">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo count($agendamentos); ?></div>
                                    <div class="stat-label">Agendamentos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orçamentos do Cliente -->
    <div class="modern-card mb-4">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>Orçamentos do Cliente
            </h5>
        </div>
        <div class="card-body">
            <?php if(count($orcamentos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Serviço</th>
                            <th>Equipamento</th>
                            <th width="100">Data</th>
                            <th width="120">Valor</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orcamentos as $orcamento): ?>
                        <tr>
                            <td>
                                <strong class="text-primary">#<?php echo $orcamento['id']; ?></strong>
                            </td>
                            <td><?php echo $orcamento['servico_nome']; ?></td>
                            <td>
                                <?php if($orcamento['equipamento_marca'] || $orcamento['equipamento_btus']): ?>
                                <div class="equipamento-info">
                                    <?php echo $orcamento['equipamento_marca'] ?: ''; ?>
                                    <?php echo $orcamento['equipamento_btus'] ? ' - ' . $orcamento['equipamento_btus'] . ' BTUs' : ''; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?></td>
                            <td>
                                <strong class="text-success">
                                    <?php echo $orcamento['valor_total'] ? 'R$ ' . number_format($orcamento['valor_total'], 2, ',', '.') : '-'; ?>
                                </strong>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $orcamento['status']; ?>">
                                    <?php echo ucfirst($orcamento['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-file-invoice-dollar fa-2x text-muted mb-3"></i>
                <p class="text-muted">Nenhum orçamento encontrado para este cliente.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Agendamentos do Cliente -->
    <div class="modern-card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-calendar-alt me-2"></i>Agendamentos do Cliente
            </h5>
        </div>
        <div class="card-body">
            <?php if(count($agendamentos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Serviço</th>
                            <th width="100">Data</th>
                            <th width="80">Hora</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($agendamentos as $agendamento): ?>
                        <tr>
                            <td>
                                <strong class="text-primary">#<?php echo $agendamento['id']; ?></strong>
                            </td>
                            <td><?php echo $agendamento['servico_nome']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td>
                                <span class="time-badge"><?php echo $agendamento['hora_agendamento']; ?></span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                    <?php echo ucfirst($agendamento['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                <p class="text-muted">Nenhum agendamento encontrado para este cliente.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
}

// Estilos específicos para clientes
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

.client-info {
    line-height: 1.4;
}

.client-address {
    font-size: 0.875rem;
    color: var(--gray);
    margin: 0.5rem 0 0 0;
    line-height: 1.4;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.contact-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
}

.date-info {
    line-height: 1.4;
}

.stats-mini {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.stat-badge {
    background: #e2e8f0;
    color: #4a5568;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-badge i {
    font-size: 0.7rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-card-mini {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    border-left: 4px solid var(--primary-color);
}

.stat-card-mini .stat-icon {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.stat-card-mini .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

.stat-card-mini .stat-label {
    font-size: 0.875rem;
    color: var(--gray);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-block;
}

.status-pendente { background: #fbbf24; color: #78350f; }
.status-aprovado { background: #10b981; color: white; }
.status-recusado { background: #ef4444; color: white; }
.status-agendado { background: #3b82f6; color: white; }
.status-confirmado { background: #10b981; color: white; }
.status-realizado { background: #8b5cf6; color: white; }
.status-cancelado { background: #6b7280; color: white; }

.time-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.equipamento-info {
    font-size: 0.875rem;
    color: var(--gray);
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

.pagination-modern {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.pagination-item {
    padding: 8px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: var(--gray);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-item:hover,
.pagination-item.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

/* Responsivo */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-info {
        min-width: 150px;
    }
    
    .stats-mini {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer-admin.php'; ?>