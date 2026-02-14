<?php
// admin/servicos.php - GESTÃO COMPLETA DE SERVIÇOS COM CONTROLE DE TEMPO
include 'includes/auth.php';
include 'includes/header-admin.php';

include '../includes/config.php';

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = '';
$erro = '';

// Garantir que $pdo está disponível
if (!isset($pdo)) {
    die("Erro: Conexão com banco de dados não estabelecida.");
}

// Função para verificar e criar colunas se necessário
function verificarEstruturaTabela($pdo) {
    try {
        // Verificar quais colunas existem
        $stmt = $pdo->prepare("DESCRIBE servicos");
        $stmt->execute();
        $colunas_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Colunas necessárias
        $colunas_necessarias = [
            'duracao_padrao_min' => "ALTER TABLE servicos ADD COLUMN duracao_padrao_min INT DEFAULT 120",
            'duracao_min_min' => "ALTER TABLE servicos ADD COLUMN duracao_min_min INT DEFAULT 60",
            'duracao_max_min' => "ALTER TABLE servicos ADD COLUMN duracao_max_min INT DEFAULT 240",
            'intervalo_entre_servicos_min' => "ALTER TABLE servicos ADD COLUMN intervalo_entre_servicos_min INT DEFAULT 30",
            'max_por_dia' => "ALTER TABLE servicos ADD COLUMN max_por_dia INT DEFAULT 3",
            'precisa_materiais' => "ALTER TABLE servicos ADD COLUMN precisa_materiais TINYINT(1) DEFAULT 1"
        ];
        
        $colunas_adicionadas = [];
        
        foreach ($colunas_necessarias as $coluna => $sql) {
            if (!in_array($coluna, $colunas_existentes)) {
                try {
                    $pdo->exec($sql);
                    $colunas_adicionadas[] = $coluna;
                } catch (PDOException $e) {
                    // Ignorar erro se coluna já existir (pode acontecer em concorrência)
                    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        return $colunas_adicionadas;
        
    } catch (PDOException $e) {
        // Se a tabela não existir, criar com estrutura completa
        if (strpos($e->getMessage(), 'servicos doesn\'t exist') !== false) {
            $sql = "CREATE TABLE IF NOT EXISTS servicos (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                descricao TEXT,
                preco_base DECIMAL(10,2),
                categoria ENUM('instalacao','manutencao','limpeza','reparo','remocao'),
                ativo TINYINT(1) DEFAULT 1,
                duracao_padrao_min INT DEFAULT 120,
                duracao_min_min INT DEFAULT 60,
                duracao_max_min INT DEFAULT 240,
                intervalo_entre_servicos_min INT DEFAULT 30,
                max_por_dia INT DEFAULT 3,
                precisa_materiais TINYINT(1) DEFAULT 1,
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                duracao_media_min INT DEFAULT 120,
                duracao_media_max INT DEFAULT 240,
                tempo_setup_min INT DEFAULT 30,
                complexidade ENUM('baixa','media','alta') DEFAULT 'media',
                duracao_horas DECIMAL(4,2) DEFAULT 1.00,
                tipo_equipamento VARCHAR(50)
            )";
            
            $pdo->exec($sql);
            return ['tabela_criada' => true];
        }
        throw $e;
    }
}

// Verificar e atualizar estrutura da tabela
try {
    $colunas_adicionadas = verificarEstruturaTabela($pdo);
    if (!empty($colunas_adicionadas)) {
        $sucesso = "Estrutura atualizada: " . implode(', ', $colunas_adicionadas);
    }
} catch (PDOException $e) {
    $erro = "Erro na estrutura: " . $e->getMessage();
}

// Processar ações POST
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar dados
    $nome = isset($_POST['nome']) ? sanitize($_POST['nome']) : '';
    $descricao = isset($_POST['descricao']) ? sanitize($_POST['descricao']) : '';
    
    // Processar preço
    $preco_base = 0;
    if(isset($_POST['preco_base'])) {
        $preco_str = str_replace(['R$', '.', ',', ' '], ['', '', '.', ''], $_POST['preco_base']);
        $preco_base = floatval($preco_str);
    }
    
    $categoria = isset($_POST['categoria']) ? sanitize($_POST['categoria']) : '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $complexidade = isset($_POST['complexidade']) ? sanitize($_POST['complexidade']) : 'media';
    $tipo_equipamento = isset($_POST['tipo_equipamento']) ? sanitize($_POST['tipo_equipamento']) : '';
    
    // Novos campos de tempo
    $duracao_padrao_min = intval($_POST['duracao_padrao_min'] ?? 120);
    $duracao_min_min = intval($_POST['duracao_min_min'] ?? 60);
    $duracao_max_min = intval($_POST['duracao_max_min'] ?? 240);
    $intervalo_entre_servicos_min = intval($_POST['intervalo_entre_servicos_min'] ?? 30);
    $max_por_dia = intval($_POST['max_por_dia'] ?? 3);
    $precisa_materiais = isset($_POST['precisa_materiais']) ? 1 : 0;

    try {
        if($acao == 'adicionar') {
            // Inserir novo serviço com todas as colunas
            $stmt = $pdo->prepare("INSERT INTO servicos 
                (nome, descricao, preco_base, categoria, ativo, complexidade, tipo_equipamento,
                 duracao_padrao_min, duracao_min_min, duracao_max_min,
                 intervalo_entre_servicos_min, max_por_dia, precisa_materiais,
                 duracao_media_min, duracao_media_max, tempo_setup_min, duracao_horas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $nome, $descricao, $preco_base, $categoria, $ativo, $complexidade, $tipo_equipamento,
                $duracao_padrao_min, $duracao_min_min, $duracao_max_min,
                $intervalo_entre_servicos_min, $max_por_dia, $precisa_materiais,
                $duracao_padrao_min, // duracao_media_min
                $duracao_max_min,    // duracao_media_max
                30,                  // tempo_setup_min
                ($duracao_padrao_min / 60) // duracao_horas
            ]);
            
            $sucesso = "Serviço adicionado com sucesso!";
            $acao = 'listar';
            
        } elseif($acao == 'editar' && $id > 0) {
            // Atualizar serviço existente
            $stmt = $pdo->prepare("UPDATE servicos 
                SET nome = ?, descricao = ?, preco_base = ?, 
                    categoria = ?, ativo = ?, complexidade = ?, tipo_equipamento = ?,
                    duracao_padrao_min = ?, duracao_min_min = ?, 
                    duracao_max_min = ?, intervalo_entre_servicos_min = ?,
                    max_por_dia = ?, precisa_materiais = ?,
                    duracao_media_min = ?, duracao_media_max = ?,
                    duracao_horas = ?
                WHERE id = ?");
            
            $stmt->execute([
                $nome, $descricao, $preco_base, $categoria, $ativo, $complexidade, $tipo_equipamento,
                $duracao_padrao_min, $duracao_min_min, $duracao_max_min,
                $intervalo_entre_servicos_min, $max_por_dia, $precisa_materiais,
                $duracao_padrao_min, // duracao_media_min
                $duracao_max_min,    // duracao_media_max
                ($duracao_padrao_min / 60), // duracao_horas
                $id
            ]);
            
            $sucesso = "Serviço atualizado com sucesso!";
            $acao = 'listar';
        }
    } catch(PDOException $e) {
        $erro = "Erro no banco de dados: " . $e->getMessage();
    }
}

// Excluir serviço
if($acao == 'excluir' && $id > 0) {
    try {
        // Verificar se existem agendamentos com este serviço
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE servico_id = ? AND status NOT IN ('cancelado', 'finalizado')");
        $stmt->execute([$id]);
        $agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($agendamentos > 0) {
            $erro = "Não é possível excluir este serviço. Existem $agendamentos agendamento(s) ativo(s) vinculado(s) a ele.";
            $acao = 'listar';
        } else {
            $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = ?");
            $stmt->execute([$id]);
            $sucesso = "Serviço excluído com sucesso!";
            $acao = 'listar';
        }
    } catch(PDOException $e) {
        $erro = "Erro ao excluir: " . $e->getMessage();
    }
}

// Buscar estatísticas para os cards
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM servicos");
    $stmt->execute();
    $total_servicos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM servicos WHERE ativo = 1");
    $stmt->execute();
    $servicos_ativos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM servicos WHERE categoria = 'instalacao'");
    $stmt->execute();
    $instalacoes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM servicos WHERE categoria = 'manutencao'");
    $stmt->execute();
    $manutencoes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Tempo total médio de serviços ativos
    $stmt = $pdo->prepare("SELECT AVG(duracao_padrao_min) as tempo_medio FROM servicos WHERE ativo = 1");
    $stmt->execute();
    $tempo_medio_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tempo_medio_min = $tempo_medio_result['tempo_medio'] ?? 0;
    $tempo_medio_horas = round($tempo_medio_min / 60, 1);
    
} catch(PDOException $e) {
    $erro = "Erro ao buscar estatísticas: " . $e->getMessage();
}

// Listar serviços
if($acao == 'listar') {
    $categoria_filter = $_GET['categoria'] ?? '';
    $status_filter = $_GET['status'] ?? '';
    $duracao_filter = $_GET['duracao'] ?? '';
    
    // Construir query com filtros
    $sql = "SELECT * FROM servicos WHERE 1=1";
    $params = [];
    
    if($categoria_filter) {
        $sql .= " AND categoria = ?";
        $params[] = $categoria_filter;
    }
    
    if($status_filter !== '') {
        $sql .= " AND ativo = ?";
        $params[] = $status_filter;
    }
    
    if($duracao_filter) {
        if ($duracao_filter === '480+') {
            $sql .= " AND duracao_padrao_min > 480";
        } else {
            $duracao_val = intval($duracao_filter);
            $sql .= " AND duracao_padrao_min <= ?";
            $params[] = $duracao_val;
        }
    }
    
    $sql .= " ORDER BY categoria, nome";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-tools me-2"></i>
            Gerenciar Serviços
        </div>
        <div class="alert alert-warning alert-modern">
            <i class="fas fa-clock me-2"></i>
            Configure o tempo de execução de cada serviço para gerenciar agendamentos
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
                <i class="fas fa-tools"></i>
                <div class="number"><?php echo $total_servicos ?? 0; ?></div>
                <div>Total Serviços</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-check-circle"></i>
                <div class="number"><?php echo $servicos_ativos ?? 0; ?></div>
                <div>Serviços Ativos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-bolt"></i>
                <div class="number"><?php echo $instalacoes ?? 0; ?></div>
                <div>Instalações</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-clock"></i>
                <div class="number"><?php echo $tempo_medio_horas ?? 0; ?>h</div>
                <div>Tempo Médio</div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Serviços
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="?acao=adicionar" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-plus me-2"></i>Adicionar Serviço
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <div class="filters-section mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Categoria</label>
                        <select id="categoriaFilter" class="form-control form-control-modern">
                            <option value="">Todas as categorias</option>
                            <option value="instalacao" <?php echo $categoria_filter == 'instalacao' ? 'selected' : ''; ?>>Instalação</option>
                            <option value="manutencao" <?php echo $categoria_filter == 'manutencao' ? 'selected' : ''; ?>>Manutenção</option>
                            <option value="limpeza" <?php echo $categoria_filter == 'limpeza' ? 'selected' : ''; ?>>Limpeza</option>
                            <option value="reparo" <?php echo $categoria_filter == 'reparo' ? 'selected' : ''; ?>>Reparo</option>
                            <option value="remocao" <?php echo $categoria_filter == 'remocao' ? 'selected' : ''; ?>>Remoção</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="statusFilter" class="form-control form-control-modern">
                            <option value="">Todos os status</option>
                            <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Ativos</option>
                            <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inativos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Duração Máxima</label>
                        <select id="duracaoFilter" class="form-control form-control-modern">
                            <option value="">Qualquer duração</option>
                            <option value="60" <?php echo $duracao_filter == '60' ? 'selected' : ''; ?>>Até 1 hora</option>
                            <option value="120" <?php echo $duracao_filter == '120' ? 'selected' : ''; ?>>Até 2 horas</option>
                            <option value="240" <?php echo $duracao_filter == '240' ? 'selected' : ''; ?>>Até 4 horas</option>
                            <option value="480" <?php echo $duracao_filter == '480' ? 'selected' : ''; ?>>Até 8 horas</option>
                            <option value="480+" <?php echo $duracao_filter == '480+' ? 'selected' : ''; ?>>Mais de 8 horas</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" id="searchServicos" placeholder="Buscar serviços..." class="form-control form-control-modern">
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" onclick="filtrarServicos()" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                    <button type="button" onclick="limparFiltros()" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-modern" id="tabelaServicos">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Serviço</th>
                            <th width="120">Categoria</th>
                            <th width="120">Preço Base</th>
                            <th width="150">Tempo</th>
                            <th width="100">Status</th>
                            <th width="180" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($servicos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-tools fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum serviço encontrado</p>
                                    <a href="?acao=adicionar" class="btn btn-primary-modern btn-modern">
                                        <i class="fas fa-plus me-2"></i>Adicionar Primeiro Serviço
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($servicos as $servico): 
                                // Calcular horas e minutos
                                $duracao_padrao = $servico['duracao_padrao_min'] ?? $servico['duracao_media_min'] ?? 120;
                                $horas = floor($duracao_padrao / 60);
                                $minutos = $duracao_padrao % 60;
                                $tempo_formatado = '';
                                if ($horas > 0) {
                                    $tempo_formatado .= "{$horas}h ";
                                }
                                if ($minutos > 0) {
                                    $tempo_formatado .= "{$minutos}min";
                                }
                                if ($tempo_formatado == '') {
                                    $tempo_formatado = '0min';
                                }
                                
                                // Tempo total com intervalo
                                $intervalo = $servico['intervalo_entre_servicos_min'] ?? 30;
                                $tempo_total = $duracao_padrao + $intervalo;
                                $horas_total = floor($tempo_total / 60);
                                $minutos_total = $tempo_total % 60;
                                $tempo_total_formatado = '';
                                if ($horas_total > 0) {
                                    $tempo_total_formatado .= "{$horas_total}h ";
                                }
                                if ($minutos_total > 0) {
                                    $tempo_total_formatado .= "{$minutos_total}min";
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-primary">#<?php echo $servico['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="service-info">
                                        <strong><?php echo htmlspecialchars($servico['nome']); ?></strong>
                                        <?php if($servico['descricao']): ?>
                                        <p class="service-description"><?php echo htmlspecialchars(substr($servico['descricao'], 0, 100)); ?><?php echo strlen($servico['descricao']) > 100 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                        <?php if(($servico['precisa_materiais'] ?? 1) == 1): ?>
                                        <span class="material-badge"><i class="fas fa-box me-1"></i>Materiais</span>
                                        <?php endif; ?>
                                        <?php if(($servico['max_por_dia'] ?? 0) > 0): ?>
                                        <span class="limit-badge"><i class="fas fa-calendar-alt me-1"></i>Max: <?php echo $servico['max_por_dia']; ?>/dia</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge category-<?php echo $servico['categoria']; ?>">
                                        <?php 
                                        $categorias = [
                                            'instalacao' => 'Instalação',
                                            'manutencao' => 'Manutenção',
                                            'limpeza' => 'Limpeza',
                                            'reparo' => 'Reparo',
                                            'remocao' => 'Remoção'
                                        ];
                                        echo $categorias[$servico['categoria']] ?? ucfirst($servico['categoria']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success price-value">
                                        R$ <?php echo number_format($servico['preco_base'], 2, ',', '.'); ?>
                                    </strong>
                                </td>
                                <td>
                                    <div class="time-info" data-bs-toggle="tooltip" data-bs-placement="top" 
                                         title="Serviço: <?php echo $tempo_formatado; ?> + Intervalo: <?php echo $intervalo; ?>min = Total: <?php echo $tempo_total_formatado; ?>">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $tempo_formatado; ?>
                                        <?php if($intervalo > 0): ?>
                                        <small class="d-block text-muted">+<?php echo $intervalo; ?>min intervalo</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $servico['ativo'] ? 'ativo' : 'inativo'; ?>">
                                        <?php echo $servico['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?acao=editar&id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-primary btn-modern" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=excluir&id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-danger btn-modern" title="Excluir" 
                                           onclick="return confirm('Tem certeza que deseja excluir este serviço?\n\n<?php echo addslashes($servico['nome']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../agendamentos.php?servico_id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-success btn-modern" title="Agendar" target="_blank">
                                            <i class="fas fa-calendar-plus"></i>
                                        </a>
                                        <?php if($servico['ativo']): ?>
                                        <a href="../orcamento.php?servico=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-warning btn-modern" title="Solicitar Orçamento" target="_blank">
                                            <i class="fas fa-file-invoice-dollar"></i>
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
    function filtrarServicos() {
        const categoria = document.getElementById('categoriaFilter').value;
        const status = document.getElementById('statusFilter').value;
        const duracao = document.getElementById('duracaoFilter').value;
        const url = new URL(window.location.href);
        
        if(categoria) {
            url.searchParams.set('categoria', categoria);
        } else {
            url.searchParams.delete('categoria');
        }
        
        if(status !== '') {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        if(duracao) {
            url.searchParams.set('duracao', duracao);
        } else {
            url.searchParams.delete('duracao');
        }
        
        window.location.href = url.toString();
    }
    
    function limparFiltros() {
        window.location.href = 'servicos.php';
    }
    
    // Busca em tempo real
    document.getElementById('searchServicos').addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaServicos tbody tr');
        
        linhas.forEach(linha => {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
    
    // Inicializar tooltips do Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>

    <?php
} elseif($acao == 'adicionar' || $acao == 'editar') {
    
    $servico = [];
    if($acao == 'editar' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
        $stmt->execute([$id]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$servico) {
            echo "<div class='alert alert-danger alert-modern'>Serviço não encontrado!</div>";
            include 'includes/footer-admin.php';
            exit;
        }
    }
    
    // Valores padrão para novo serviço
    $duracao_padrao = $servico['duracao_padrao_min'] ?? $servico['duracao_media_min'] ?? 120;
    $duracao_min = $servico['duracao_min_min'] ?? 60;
    $duracao_max = $servico['duracao_max_min'] ?? 240;
    $intervalo = $servico['intervalo_entre_servicos_min'] ?? 30;
    $max_dia = $servico['max_por_dia'] ?? 3;
    $precisa_materiais = $servico['precisa_materiais'] ?? 1;
    $complexidade = $servico['complexidade'] ?? 'media';
    $tipo_equipamento = $servico['tipo_equipamento'] ?? '';
    ?>

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-<?php echo $acao == 'adicionar' ? 'plus' : 'edit'; ?> me-2"></i>
            <?php echo $acao == 'adicionar' ? 'Adicionar Serviço' : 'Editar Serviço'; ?>
        </div>
        <div class="header-actions">
            <a href="servicos.php" class="btn btn-outline-primary btn-modern">
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
            <form method="POST" class="config-form" id="servicoForm">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Seção de Informações Básicas -->
                        <div class="config-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Informações do Serviço
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome do Serviço *</label>
                                <input type="text" name="nome" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($servico['nome'] ?? ''); ?>" 
                                       placeholder="Ex: Instalação de Ar Condicionado Split" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control form-control-modern" rows="4" 
                                          placeholder="Descreva detalhes do serviço, materiais utilizados, tempo de execução..."><?php echo htmlspecialchars($servico['descricao'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Esta descrição será exibida para os clientes</small>
                            </div>
                        </div>
                        
                        <!-- Seção de Duração e Tempo -->
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock me-2"></i>Configurações de Tempo
                            </h5>
                            
                            <div class="alert alert-info alert-modern mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Configure o tempo de execução para gerenciar melhor os agendamentos. O sistema reservará automaticamente o tempo total (serviço + intervalo) para cada cliente.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duração Padrão (minutos) *</label>
                                    <input type="number" name="duracao_padrao_min" class="form-control form-control-modern" 
                                           value="<?php echo $duracao_padrao; ?>" 
                                           min="30" step="15" required id="duracaoPadrao">
                                    <small class="form-text text-muted">Tempo médio de execução</small>
                                    <div class="time-preview mt-1" id="previewPadrao"></div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duração Mínima (minutos)</label>
                                    <input type="number" name="duracao_min_min" class="form-control form-control-modern" 
                                           value="<?php echo $duracao_min; ?>" 
                                           min="15" step="15" id="duracaoMinima">
                                    <small class="form-text text-muted">Tempo mínimo necessário</small>
                                    <div class="time-preview mt-1" id="previewMinima"></div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duração Máxima (minutos)</label>
                                    <input type="number" name="duracao_max_min" class="form-control form-control-modern" 
                                           value="<?php echo $duracao_max; ?>" 
                                           min="60" step="30" id="duracaoMaxima">
                                    <small class="form-text text-muted">Tempo máximo esperado</small>
                                    <div class="time-preview mt-1" id="previewMaxima"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Intervalo entre Serviços (min)</label>
                                    <input type="number" name="intervalo_entre_servicos_min" class="form-control form-control-modern" 
                                           value="<?php echo $intervalo; ?>" 
                                           min="0" step="15" id="intervaloServicos">
                                    <small class="form-text text-muted">Tempo necessário entre este serviço e outro para preparação/limpeza</small>
                                    <div class="time-preview mt-1" id="previewIntervalo"></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Máximo por Dia</label>
                                    <input type="number" name="max_por_dia" class="form-control form-control-modern" 
                                           value="<?php echo $max_dia; ?>" 
                                           min="0" max="20">
                                    <small class="form-text text-muted">Quantos deste serviço podem ser agendados por dia (0 = ilimitado)</small>
                                </div>
                            </div>
                            
                            <div class="time-summary alert alert-warning">
                                <h6><i class="fas fa-calculator me-2"></i>Resumo do Tempo</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Tempo do Serviço:</strong> <span id="summaryServico">0h 0min</span></p>
                                        <p><strong>Intervalo:</strong> <span id="summaryIntervalo">0min</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Tempo Total Reservado:</strong> <span id="summaryTotal" class="text-success fw-bold">0h 0min</span></p>
                                        <p><small class="text-muted">Este é o tempo que será bloqueado no calendário para cada cliente</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="config-section">
                            <h5 class="section-title">
                                <i class="fas fa-cog me-2"></i>Configurações
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Categoria *</label>
                                <select name="categoria" class="form-control form-control-modern" required>
                                    <option value="">Selecione uma categoria</option>
                                    <option value="instalacao" <?php echo ($servico['categoria'] ?? '') == 'instalacao' ? 'selected' : ''; ?>>Instalação</option>
                                    <option value="manutencao" <?php echo ($servico['categoria'] ?? '') == 'manutencao' ? 'selected' : ''; ?>>Manutenção</option>
                                    <option value="limpeza" <?php echo ($servico['categoria'] ?? '') == 'limpeza' ? 'selected' : ''; ?>>Limpeza</option>
                                    <option value="reparo" <?php echo ($servico['categoria'] ?? '') == 'reparo' ? 'selected' : ''; ?>>Reparo</option>
                                    <option value="remocao" <?php echo ($servico['categoria'] ?? '') == 'remocao' ? 'selected' : ''; ?>>Remoção</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Preço Base *</label>
                                <input type="text" name="preco_base" class="form-control form-control-modern money" 
                                       value="<?php echo formatarMoeda($servico['preco_base'] ?? 0); ?>" 
                                       placeholder="R$ 0,00" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tipo de Equipamento</label>
                                <input type="text" name="tipo_equipamento" class="form-control form-control-modern" 
                                       value="<?php echo htmlspecialchars($tipo_equipamento); ?>" 
                                       placeholder="Ex: Split, Janela, Portátil...">
                                <small class="form-text text-muted">Opcional - para referência interna</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Complexidade</label>
                                <select name="complexidade" class="form-control form-control-modern">
                                    <option value="baixa" <?php echo $complexidade == 'baixa' ? 'selected' : ''; ?>>Baixa</option>
                                    <option value="media" <?php echo $complexidade == 'media' ? 'selected' : ''; ?>>Média</option>
                                    <option value="alta" <?php echo $complexidade == 'alta' ? 'selected' : ''; ?>>Alta</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check form-check-modern">
                                <input type="checkbox" class="form-check-input" name="precisa_materiais" value="1" 
                                       <?php echo $precisa_materiais ? 'checked' : ''; ?>>
                                <label class="form-check-label">Precisa de materiais específicos</label>
                                <small class="form-text text-muted d-block">Se marcado, será solicitado no orçamento</small>
                            </div>
                            
                            <div class="mb-3 form-check form-check-modern">
                                <input type="checkbox" class="form-check-input" name="ativo" value="1" 
                                       <?php echo ($servico['ativo'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Serviço ativo</label>
                                <small class="form-text text-muted d-block">Serviços inativos não aparecem para agendamento</small>
                            </div>
                        </div>
                        
                        <!-- Exemplo de horários -->
                        <div class="config-section mt-4">
                            <h5 class="section-title">
                                <i class="fas fa-calendar-alt me-2"></i>Exemplo de Agendamento
                            </h5>
                            <div class="example-schedule">
                                <p>Se um cliente agendar este serviço às <strong>09:00</strong>:</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-play-circle text-success me-2"></i> Início: <span id="exemploInicio">09:00</span></li>
                                    <li><i class="fas fa-tools text-primary me-2"></i> Serviço: <span id="exemploServico">0h 0min</span></li>
                                    <li><i class="fas fa-pause text-warning me-2"></i> Intervalo: <span id="exemploIntervalo">0min</span></li>
                                    <li><i class="fas fa-stop-circle text-danger me-2"></i> Fim: <span id="exemploFim">09:00</span></li>
                                </ul>
                                <div class="alert alert-sm alert-info mt-2">
                                    <i class="fas fa-info-circle me-1"></i> O horário das <span id="exemploInicioFim">09:00 às 09:00</span> ficará reservado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $acao == 'adicionar' ? 'Adicionar Serviço' : 'Atualizar Serviço'; ?>
                    </button>
                    <a href="servicos.php" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Função para formatar minutos em horas
    function formatarTempo(minutos) {
        const horas = Math.floor(minutos / 60);
        const mins = minutos % 60;
        let resultado = '';
        
        if (horas > 0) {
            resultado += horas + 'h ';
        }
        if (mins > 0) {
            resultado += mins + 'min';
        }
        
        return resultado.trim() || '0min';
    }
    
    // Função para calcular e atualizar todos os previews
    function atualizarPreviews() {
        // Obter valores
        const duracaoPadrao = parseInt(document.getElementById('duracaoPadrao').value) || 120;
        const duracaoMinima = parseInt(document.getElementById('duracaoMinima').value) || 60;
        const duracaoMaxima = parseInt(document.getElementById('duracaoMaxima').value) || 240;
        const intervalo = parseInt(document.getElementById('intervaloServicos').value) || 30;
        
        // Atualizar previews individuais
        document.getElementById('previewPadrao').textContent = formatarTempo(duracaoPadrao);
        document.getElementById('previewMinima').textContent = formatarTempo(duracaoMinima);
        document.getElementById('previewMaxima').textContent = formatarTempo(duracaoMaxima);
        document.getElementById('previewIntervalo').textContent = formatarTempo(intervalo);
        
        // Atualizar resumo
        document.getElementById('summaryServico').textContent = formatarTempo(duracaoPadrao);
        document.getElementById('summaryIntervalo').textContent = formatarTempo(intervalo);
        
        const tempoTotal = duracaoPadrao + intervalo;
        document.getElementById('summaryTotal').textContent = formatarTempo(tempoTotal);
        
        // Atualizar exemplo de agendamento
        document.getElementById('exemploServico').textContent = formatarTempo(duracaoPadrao);
        document.getElementById('exemploIntervalo').textContent = formatarTempo(intervalo);
        
        // Calcular hora fim do exemplo
        const horaInicio = '09:00';
        const [hora, minuto] = horaInicio.split(':').map(Number);
        const totalMinutosInicio = hora * 60 + minuto;
        const totalMinutosFim = totalMinutosInicio + tempoTotal;
        
        const horaFimHora = Math.floor(totalMinutosFim / 60);
        const horaFimMinuto = totalMinutosFim % 60;
        const horaFimFormatada = `${horaFimHora.toString().padStart(2, '0')}:${horaFimMinuto.toString().padStart(2, '0')}`;
        
        document.getElementById('exemploFim').textContent = horaFimFormatada;
        document.getElementById('exemploInicioFim').textContent = `${horaInicio} às ${horaFimFormatada}`;
        
        // Validações
        const duracaoMinimaInput = document.getElementById('duracaoMinima');
        const duracaoMaximaInput = document.getElementById('duracaoMaxima');
        
        if (duracaoMinima > duracaoPadrao) {
            duracaoMinimaInput.classList.add('is-invalid');
            duracaoMinimaInput.title = 'A duração mínima não pode ser maior que a duração padrão';
        } else {
            duracaoMinimaInput.classList.remove('is-invalid');
            duracaoMinimaInput.title = '';
        }
        
        if (duracaoMaxima < duracaoPadrao) {
            duracaoMaximaInput.classList.add('is-invalid');
            duracaoMaximaInput.title = 'A duração máxima não pode ser menor que a duração padrão';
        } else {
            duracaoMaximaInput.classList.remove('is-invalid');
            duracaoMaximaInput.title = '';
        }
    }
    
    // Adicionar eventos aos campos de tempo
    document.addEventListener('DOMContentLoaded', function() {
        const camposTempo = ['duracaoPadrao', 'duracaoMinima', 'duracaoMaxima', 'intervaloServicos'];
        
        camposTempo.forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener('input', atualizarPreviews);
                campo.addEventListener('change', atualizarPreviews);
            }
        });
        
        // Inicializar previews
        atualizarPreviews();
        
        // Máscara para moeda
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
            
            // Formatar inicialmente se tiver valor
            if (input.value) {
                let value = input.value.replace(/\D/g, '');
                value = (value / 100).toFixed(2) + '';
                value = value.replace(".", ",");
                value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
                input.value = 'R$ ' + value;
            }
        });
    });
    
    // Validação do formulário
    document.getElementById('servicoForm').addEventListener('submit', function(e) {
        const duracaoPadrao = parseInt(document.getElementById('duracaoPadrao').value) || 0;
        const duracaoMinima = parseInt(document.getElementById('duracaoMinima').value) || 0;
        const duracaoMaxima = parseInt(document.getElementById('duracaoMaxima').value) || 0;
        
        let erros = [];
        
        if (duracaoMinima > duracaoPadrao) {
            erros.push('A duração mínima não pode ser maior que a duração padrão!');
        }
        
        if (duracaoMaxima < duracaoPadrao) {
            erros.push('A duração máxima não pode ser menor que a duração padrão!');
        }
        
        if (duracaoPadrao < 30) {
            erros.push('A duração padrão deve ser de pelo menos 30 minutos!');
        }
        
        if (duracaoMinima < 15 && duracaoMinima > 0) {
            erros.push('A duração mínima deve ser de pelo menos 15 minutos!');
        }
        
        if (erros.length > 0) {
            e.preventDefault();
            alert('Por favor, corrija os seguintes erros:\n\n' + erros.join('\n'));
            return false;
        }
        
        return true;
    });
    </script>

    <?php
}

// Estilos específicos para serviços
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
    height: 100%;
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

.stats-card div:last-child {
    font-size: 0.9rem;
    opacity: 0.9;
}

.filters-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.service-info {
    line-height: 1.4;
}

.service-description {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0.5rem 0 0 0;
    line-height: 1.4;
    max-height: 3em;
    overflow: hidden;
    text-overflow: ellipsis;
}

.material-badge, .limit-badge {
    display: inline-block;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    margin: 2px 4px 2px 0;
    font-weight: 500;
}

.material-badge {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.limit-badge {
    background: #f0f9ff;
    color: #0c4a6e;
    border: 1px solid #7dd3fc;
}

.category-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-block;
    white-space: nowrap;
}

.category-instalacao { background: #3b82f6; color: white; }
.category-manutencao { background: #10b981; color: white; }
.category-limpeza { background: #f59e0b; color: white; }
.category-reparo { background: #ef4444; color: white; }
.category-remocao { background: #8b5cf6; color: white; }

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
    font-weight: 600;
}

.time-info {
    font-size: 0.875rem;
    color: #4b5563;
}

.time-info i {
    color: var(--primary-color);
}

.time-preview {
    font-size: 0.8rem;
    color: #6b7280;
    padding: 2px 6px;
    background: #f3f4f6;
    border-radius: 4px;
    display: inline-block;
}

.time-summary {
    background: #fffbeb;
    border: 1px solid #fcd34d;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.time-summary h6 {
    color: #92400e;
    margin-bottom: 0.75rem;
}

.example-schedule {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e5e7eb;
}

.example-schedule ul {
    margin-top: 0.5rem;
    padding-left: 0;
}

.example-schedule li {
    padding: 4px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
}

.example-schedule li:last-child {
    border-bottom: none;
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Validação */
.is-invalid {
    border-color: #dc2626 !important;
    background-color: #fef2f2;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25);
}

/* Responsivo */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .service-info {
        min-width: 200px;
    }
    
    .time-summary .row {
        flex-direction: column;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}

/* Animações */
@keyframes highlight {
    0% { background-color: #fef3c7; }
    100% { background-color: transparent; }
}

.highlight {
    animation: highlight 1s ease;
}

/* Formulário moderno */
.form-control-modern:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
}

.form-check-modern .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-modern {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-modern {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary-modern:hover {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
    transform: translateY(-2px);
}

/* Alertas modernos */
.alert-modern {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Tooltip personalizado */
.tooltip-inner {
    background-color: #1f2937;
    border-radius: 6px;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.tooltip.bs-tooltip-top .tooltip-arrow::before {
    border-top-color: #1f2937;
}

.tooltip.bs-tooltip-bottom .tooltip-arrow::before {
    border-bottom-color: #1f2937;
}

.tooltip.bs-tooltip-start .tooltip-arrow::before {
    border-left-color: #1f2937;
}

.tooltip.bs-tooltip-end .tooltip-arrow::before {
    border-right-color: #1f2937;
}
</style>

<?php include 'includes/footer-admin.php'; ?>