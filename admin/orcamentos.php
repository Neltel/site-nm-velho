<?php
// admin/orcamentos.php - VERSÃO COMPLETA ATUALIZADA
include 'includes/auth.php';
include 'includes/header-admin.php';
include '../includes/config.php';

// Funções auxiliares
if (!function_exists('formatarMoeda')) {
    function formatarMoeda($valor) {
        if(empty($valor) || !is_numeric($valor)) $valor = 0;
        return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
    }
}

if (!function_exists('moedaParaFloat')) {
    function moedaParaFloat($valor) {
        if(empty($valor) || trim($valor) === 'R$ 0,00') return 0;
        
        $valor = str_replace(['R$', ' ', '\\u00a0'], '', $valor);
        
        if (strpos($valor, '.') !== false && strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }
        elseif (strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor);
        }
        
        return floatval($valor);
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if(is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('gerarLinkWhatsApp')) {
    function gerarLinkWhatsApp($telefone, $mensagem) {
        $numero = preg_replace('/[^0-9]/', '', $telefone);
        
        if (substr($numero, 0, 2) !== '55') {
            $numero = '55' . $numero;
        }
        
        $mensagem_encoded = rawurlencode($mensagem);
        return "https://wa.me/{$numero}?text={$mensagem_encoded}";
    }
}

// FUNÇÕES PARA PARCELAMENTO
function calcularParcelamento($valor_total, $taxa_parcelado, $max_parcelas = 12) {
    $parcelas = [];
    $valor_com_taxa = $valor_total * (1 + ($taxa_parcelado / 100));
    
    for($i = 1; $i <= $max_parcelas; $i++) {
        $valor_parcela = $valor_com_taxa / $i;
        $parcelas[$i] = [
            'quantidade' => $i,
            'valor_parcela' => round($valor_parcela, 2),
            'valor_total' => round($valor_com_taxa, 2)
        ];
    }
    
    return $parcelas;
}

// FUNÇÕES PARA MATERIAIS
function buscarMateriaisOrcamento($pdo, $orcamento_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT om.*, m.nome, m.descricao, m.categoria, m.preco_unitario, m.unidade_medida
            FROM orcamento_materiais om
            JOIN materiais m ON om.material_id = m.id
            WHERE om.orcamento_id = ?
        ");
        $stmt->execute([$orcamento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erro ao buscar materiais do orçamento: " . $e->getMessage());
        return [];
    }
}

function salvarMateriaisOrcamento($pdo, $orcamento_id, $materiais) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orcamento_materiais WHERE orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        
        foreach($materiais as $material_id => $dados) {
            if(isset($dados['usar']) && $dados['usar'] == 1 && !empty($dados['quantidade']) && $dados['quantidade'] > 0) {
                $quantidade = floatval(str_replace(',', '.', $dados['quantidade']));
                
                $stmt = $pdo->prepare("
                    INSERT INTO orcamento_materiais (orcamento_id, material_id, quantidade) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$orcamento_id, $material_id, $quantidade]);
            }
        }
        return true;
    } catch(PDOException $e) {
        error_log("Erro ao salvar materiais do orçamento: " . $e->getMessage());
        return false;
    }
}

function calcularTotalMateriais($materiais) {
    $total = 0;
    foreach($materiais as $material) {
        $total += floatval($material['preco_unitario']) * floatval($material['quantidade']);
    }
    return $total;
}

// FUNÇÕES PARA SERVIÇOS
function buscarServicosOrcamento($pdo, $orcamento_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT os.*, s.nome, s.descricao, s.preco_base, s.categoria
            FROM orcamento_servicos os
            JOIN servicos s ON os.servico_id = s.id
            WHERE os.orcamento_id = ?
        ");
        $stmt->execute([$orcamento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erro ao buscar serviços do orçamento: " . $e->getMessage());
        return [];
    }
}

function salvarServicosOrcamento($pdo, $orcamento_id, $servicos) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orcamento_servicos WHERE orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        
        foreach($servicos as $servico_id => $dados) {
            if(isset($dados['usar']) && $dados['usar'] == 1) {
                $quantidade = isset($dados['quantidade']) ? intval($dados['quantidade']) : 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO orcamento_servicos (orcamento_id, servico_id, quantidade) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$orcamento_id, $servico_id, $quantidade]);
            }
        }
        return true;
    } catch(PDOException $e) {
        error_log("Erro ao salvar serviços do orçamento: " . $e->getMessage());
        return false;
    }
}

function calcularTotalServicos($servicos) {
    $total = 0;
    foreach($servicos as $servico) {
        $quantidade = isset($servico['quantidade']) ? intval($servico['quantidade']) : 1;
        $total += floatval($servico['preco_base']) * $quantidade;
    }
    return $total;
}

// FUNÇÕES PARA PRODUTOS
function buscarProdutosOrcamento($pdo, $orcamento_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT op.*, p.nome, p.descricao, p.preco, p.marca, p.btus, p.categoria
            FROM orcamento_produtos op
            JOIN produtos p ON op.produto_id = p.id
            WHERE op.orcamento_id = ?
        ");
        $stmt->execute([$orcamento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Se a tabela não existir, criar
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orcamento_produtos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                orcamento_id INT NOT NULL,
                produto_id INT NOT NULL,
                quantidade DECIMAL(10,2) DEFAULT 1.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
                FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
            )
        ");
        return [];
    }
}

function salvarProdutosOrcamento($pdo, $orcamento_id, $produtos) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orcamento_produtos WHERE orcamento_id = ?");
        $stmt->execute([$orcamento_id]);
        
        foreach($produtos as $produto_id => $dados) {
            if(isset($dados['usar']) && $dados['usar'] == 1) {
                $quantidade = isset($dados['quantidade']) ? floatval(str_replace(',', '.', $dados['quantidade'])) : 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO orcamento_produtos (orcamento_id, produto_id, quantidade) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$orcamento_id, $produto_id, $quantidade]);
            }
        }
        return true;
    } catch(PDOException $e) {
        error_log("Erro ao salvar produtos do orçamento: " . $e->getMessage());
        return false;
    }
}

function calcularTotalProdutos($produtos) {
    $total = 0;
    foreach($produtos as $produto) {
        $preco = isset($produto['preco']) ? floatval($produto['preco']) : 0;
        $quantidade = isset($produto['quantidade']) ? floatval($produto['quantidade']) : 1;
        $total += $preco * $quantidade;
    }
    return $total;
}

// FUNÇÃO PARA OBTER SERVIÇO PRINCIPAL
function buscarServicoPrincipal($pdo, $orcamento_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT s.* FROM orcamento_servicos os
            JOIN servicos s ON os.servico_id = s.id
            WHERE os.orcamento_id = ?
            ORDER BY os.id LIMIT 1
        ");
        $stmt->execute([$orcamento_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return null;
    }
}

// FUNÇÃO PARA VERIFICAR CONFLITO DE AGENDAMENTO
function verificarConflitoAgendamento($pdo, $data_inicio, $hora_inicio, $data_fim = null, $hora_fim = null, $ignorar_orcamento_id = null) {
    try {
        $sql = "SELECT COUNT(*) as total FROM agendamentos 
                WHERE status != 'cancelado' 
                AND data_agendamento = ? 
                AND hora_agendamento = ?";
        
        $params = [$data_inicio, $hora_inicio];
        
        if($ignorar_orcamento_id) {
            $sql .= " AND (orcamento_id IS NULL OR orcamento_id != ?)";
            $params[] = $ignorar_orcamento_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0;
    } catch(PDOException $e) {
        error_log("Erro ao verificar conflito de agendamento: " . $e->getMessage());
        return false;
    }
}

// Função para salvar agendamento
function salvarAgendamentoOrcamento($pdo, $orcamento_id, $dados_agendamento) {
    try {
        if(!empty($dados_agendamento['data_inicio']) && !empty($dados_agendamento['hora_inicio'])) {
            $conflito = verificarConflitoAgendamento(
                $pdo, 
                $dados_agendamento['data_inicio'], 
                $dados_agendamento['hora_inicio'],
                $dados_agendamento['data_fim'] ?? $dados_agendamento['data_inicio'],
                $dados_agendamento['hora_fim'] ?? $dados_agendamento['hora_inicio'],
                $orcamento_id
            );
            
            if($conflito) {
                throw new Exception("Ja existe um agendamento para esta data e horario. Por favor, escolha outro horario.");
            }
            
            $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                                 FROM orcamentos o 
                                 JOIN clientes c ON o.cliente_id = c.id 
                                 WHERE o.id = ?");
            $stmt->execute([$orcamento_id]);
            $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$orcamento) return false;
            
            $servico_principal = buscarServicoPrincipal($pdo, $orcamento_id);
            $servico_id = $servico_principal ? $servico_principal['id'] : 1;
            
            $stmt = $pdo->prepare("SELECT id FROM agendamentos WHERE orcamento_id = ?");
            $stmt->execute([$orcamento_id]);
            $agendamento_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($agendamento_existente) {
                $stmt = $pdo->prepare("
                    UPDATE agendamentos 
                    SET data_agendamento = ?, hora_agendamento = ?, data_fim = ?, hora_fim = ?, observacoes = ?
                    WHERE orcamento_id = ?
                ");
                $stmt->execute([
                    $dados_agendamento['data_inicio'],
                    $dados_agendamento['hora_inicio'],
                    $dados_agendamento['data_fim'] ?? $dados_agendamento['data_inicio'],
                    $dados_agendamento['hora_fim'] ?? $dados_agendamento['hora_inicio'],
                    "Agendamento via orcamento #{$orcamento_id} - {$orcamento['cliente_nome']}",
                    $orcamento_id
                ]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO agendamentos (cliente_id, servico_id, data_agendamento, hora_agendamento, data_fim, hora_fim, observacoes, status, origem, orcamento_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'agendado', 'orcamento', ?)
                ");
                $observacoes = "Agendamento via orcamento #{$orcamento_id} - {$orcamento['cliente_nome']}";
                $stmt->execute([
                    $orcamento['cliente_id'],
                    $servico_id,
                    $dados_agendamento['data_inicio'],
                    $dados_agendamento['hora_inicio'],
                    $dados_agendamento['data_fim'] ?? $dados_agendamento['data_inicio'],
                    $dados_agendamento['hora_fim'] ?? $dados_agendamento['hora_inicio'],
                    $observacoes,
                    $orcamento_id
                ]);
            }
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Erro ao salvar agendamento: " . $e->getMessage());
        throw new Exception("Erro ao salvar agendamento: " . $e->getMessage());
    }
}

// FUNÇÃO PARA ATUALIZAR DADOS DO CLIENTE
function atualizarDadosCliente($pdo, $cliente_id, $dados_cliente) {
    try {
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET nome = ?, email = ?, telefone = ?, rua = ?, numero = ?, bairro = ?, cidade = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $dados_cliente['nome'],
            $dados_cliente['email'],
            $dados_cliente['telefone'],
            $dados_cliente['rua'] ?? '',
            $dados_cliente['numero'] ?? '',
            $dados_cliente['bairro'] ?? '',
            $dados_cliente['cidade'] ?? '',
            $cliente_id
        ]);
        return true;
    } catch(PDOException $e) {
        error_log("Erro ao atualizar dados do cliente: " . $e->getMessage());
        return false;
    }
}

// FUNÇÃO PARA CRIAR NOVO CLIENTE
function criarNovoCliente($pdo, $dados_cliente) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO clientes (nome, email, telefone, rua, numero, bairro, cidade, data_cadastro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $dados_cliente['nome'],
            $dados_cliente['email'] ?? '',
            $dados_cliente['telefone'],
            $dados_cliente['rua'] ?? '',
            $dados_cliente['numero'] ?? '',
            $dados_cliente['bairro'] ?? '',
            $dados_cliente['cidade'] ?? ''
        ]);
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        error_log("Erro ao criar novo cliente: " . $e->getMessage());
        throw new Exception("Erro ao criar cliente: " . $e->getMessage());
    }
}

// CORREÇÃO DA ESTRUTURA DA TABELA SE NECESSÁRIO
try {
    // Verificar se a coluna valor_total precisa ser alterada
    $stmt = $pdo->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'orcamentos' 
                         AND COLUMN_NAME = 'valor_total'");
    $coluna_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($coluna_info && strpos($coluna_info['COLUMN_TYPE'], 'decimal(10,2)') !== false) {
        $pdo->exec("ALTER TABLE orcamentos MODIFY valor_total DECIMAL(12,2)");
    }
} catch(PDOException $e) {
    // Ignora erro se não conseguir alterar
}

// VARIÁVEIS PRINCIPAIS
$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$mensagem = '';
$sucesso = '';
$erro = '';

// PROCESSAR AÇÕES QUE FAZEM REDIRECT PRIMEIRO
if($acao == 'enviar_whatsapp' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone
                              FROM orcamentos o 
                              LEFT JOIN clientes c ON o.cliente_id = c.id 
                              WHERE o.id = ?");
        $stmt->execute([$id]);
        $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$orcamento) {
            $erro = "Orcamento nao encontrado!";
        } else {
            $materiais_orcamento = buscarMateriaisOrcamento($pdo, $id);
            $servicos_orcamento = buscarServicosOrcamento($pdo, $id);
            $produtos_orcamento = buscarProdutosOrcamento($pdo, $id);
            $servico_principal = buscarServicoPrincipal($pdo, $id);
            
            // Buscar configurações de taxas
            $configs_taxas = $pdo->query("SELECT chave, valor FROM configuracoes WHERE chave IN ('taxa_cartao_avista', 'taxa_cartao_parcelado', 'chave_pix')")
                               ->fetchAll(PDO::FETCH_KEY_PAIR);
            
            $valor_total = floatval($orcamento['valor_total']);
            $taxa_parcelado = floatval($configs_taxas['taxa_cartao_parcelado'] ?? 21);
            $parcelamento = calcularParcelamento($valor_total, $taxa_parcelado, 12);
            
            $mensagem_whatsapp = "*ORCAMENTO - N&M REFRIGERACAO*\n\n";
            $mensagem_whatsapp .= "*Cliente:* {$orcamento['cliente_nome']}\n";
            $mensagem_whatsapp .= "*Telefone:* {$orcamento['cliente_telefone']}\n";
            
            if($servico_principal) {
                $mensagem_whatsapp .= "*Servico Principal:* {$servico_principal['nome']}\n";
            }
            
            
            // Produtos
            if(!empty($produtos_orcamento)) {
                $mensagem_whatsapp .= "\n*PRODUTOS INCLUSOS:*\n";
                foreach($produtos_orcamento as $produto) {
                    $total_produto = $produto['preco'] * $produto['quantidade'];
                    $mensagem_whatsapp .= "- {$produto['nome']} - {$produto['quantidade']} un - " . formatarMoeda($total_produto) . "\n";
                }
            }
            
            // Materiais
            $mensagem_whatsapp .= "\n*MATERIAIS INCLUSOS:*\n";
            if(!empty($materiais_orcamento)) {
                foreach($materiais_orcamento as $material) {
                    $total_material = $material['preco_unitario'] * $material['quantidade'];
                    $mensagem_whatsapp .= "- {$material['nome']} \n";
                }
            } else {
                $mensagem_whatsapp .= "- Todos os materiais necessarios inclusos\n";
            }
            
            // Serviços
            if(!empty($servicos_orcamento)) {
                $mensagem_whatsapp .= "\n*SERVICOS:*\n";
                foreach($servicos_orcamento as $servico) {
                    $quantidade = $servico['quantidade'] > 1 ? " ({$servico['quantidade']}x)" : "";
                    $total_servico = $servico['preco_base'] * ($servico['quantidade'] ?? 1);
                    $mensagem_whatsapp .= "- {$servico['nome']}{$quantidade} \n";
                }
            }
            
            $mensagem_whatsapp .= "\n*VALOR TOTAL:* " . formatarMoeda($valor_total) . "\n\n";
            
            // OPÇÕES DE PAGAMENTO
            $mensagem_whatsapp .= "*A VISTA (Dinheiro/PIX):* " . formatarMoeda($valor_total) . "\n";
            if(isset($configs_taxas['chave_pix'])) {
                $mensagem_whatsapp .= "   PIX: {$configs_taxas['chave_pix']}\n   Banco: Nu Pagamentos \n   Nome: Neltel Sindeaux Severino Neto \n";
            }
            
            $mensagem_whatsapp .= "\n*CARTAO DE CREDITO:*\n";
            foreach([1, 2, 3, 6, 12] as $parcela) {
                if(isset($parcelamento[$parcela])) {
                    $valor_parcela = $parcelamento[$parcela]['valor_parcela'];
                    $mensagem_whatsapp .= "  {$parcela}x de " . formatarMoeda($valor_parcela) . "\n";
                }
            }
            $mensagem_whatsapp .= "  *(Taxa de {$taxa_parcelado}% inclusa)*\n";
            
            if($orcamento['observacoes_admin']) {
                $mensagem_whatsapp .= "\n*Observacoes:*\n{$orcamento['observacoes_admin']}\n\n";
            }
            
            $mensagem_whatsapp .= "*N&M Refrigeracao* - Especialistas em conforto termico!\n";
            $mensagem_whatsapp .= "*WhatsApp:* (17) 9 9624-0725";
            
            $link_whatsapp = gerarLinkWhatsApp($orcamento['cliente_telefone'], $mensagem_whatsapp);
            
            $stmt = $pdo->prepare("UPDATE orcamentos SET status = 'enviado' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirecionar para WhatsApp
            echo "<script>window.open('{$link_whatsapp}', '_blank'); setTimeout(function() { window.history.back(); }, 1000);</script>";
            exit;
        }
    } catch(PDOException $e) {
        $erro = "Erro ao enviar WhatsApp: " . $e->getMessage();
    }
}

if($acao == 'gerar_pdf' && $id > 0) {
    header("Location: gerar_pdf_orcamento.php?id=" . $id);
    exit;
}

// EXCLUIR ORÇAMENTO
if($acao == 'excluir' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        
        if(!$stmt->fetch()) {
            $erro = "Orcamento nao encontrado!";
        } else {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("DELETE FROM orcamento_materiais WHERE orcamento_id = ?");
            $stmt->execute([$id]);
            
            $stmt = $pdo->prepare("DELETE FROM orcamento_servicos WHERE orcamento_id = ?");
            $stmt->execute([$id]);
            
            // Deletar produtos se a tabela existir
            $tabela_produtos = $pdo->query("SHOW TABLES LIKE 'orcamento_produtos'")->fetch();
            if($tabela_produtos) {
                $stmt = $pdo->prepare("DELETE FROM orcamento_produtos WHERE orcamento_id = ?");
                $stmt->execute([$id]);
            }
            
            $stmt = $pdo->prepare("DELETE FROM orcamentos WHERE id = ?");
            $stmt->execute([$id]);
            
            $pdo->commit();
            
            $sucesso = "Orcamento excluido com sucesso!";
            $acao = 'listar';
        }
    } catch(PDOException $e) {
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erro = "Erro ao excluir orcamento: " . $e->getMessage();
    }
}

// PROCESSAR FORMULÁRIOS POST - EDIÇÃO
if($_POST && $acao == 'editar' && $id > 0) {
    $status = sanitize($_POST['status']);
    $valor_total_input = $_POST['valor_total'];
    $observacoes_admin = sanitize($_POST['observacoes_admin']);
    
    // Dados do cliente (EDITÁVEIS)
    $cliente_nome = sanitize($_POST['cliente_nome'] ?? '');
    $cliente_email = sanitize($_POST['cliente_email'] ?? '');
    $cliente_telefone = sanitize($_POST['cliente_telefone'] ?? '');
    $cliente_rua = sanitize($_POST['cliente_rua'] ?? '');
    $cliente_numero = sanitize($_POST['cliente_numero'] ?? '');
    $cliente_bairro = sanitize($_POST['cliente_bairro'] ?? '');
    $cliente_cidade = sanitize($_POST['cliente_cidade'] ?? '');
    
    // Observações do cliente (EDITÁVEL)
    $descricao = sanitize($_POST['descricao'] ?? '');

    try {
        $valor_total = moedaParaFloat($valor_total_input);
        
        if(!is_numeric($valor_total) || $valor_total < 0) {
            throw new Exception("Valor total invalido. Por favor, insira um valor numerico positivo.");
        }
        
        if($valor_total > 999999999.99) {
            throw new Exception("Valor total muito alto. O valor maximo permitido e R$ 999.999.999,99.");
        }

        $pdo->beginTransaction();
        
        // Atualizar orçamento
        $stmt = $pdo->prepare("UPDATE orcamentos SET status = ?, valor_total = ?, observacoes_admin = ?, descricao = ? WHERE id = ?");
        $stmt->execute([$status, $valor_total, $observacoes_admin, $descricao, $id]);
        
        // Buscar cliente_id do orçamento
        $stmt = $pdo->prepare("SELECT cliente_id FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        $orcamento_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $cliente_id = $orcamento_info['cliente_id'];
        
        // Atualizar dados do cliente
        if($cliente_id) {
            $dados_cliente = [
                'nome' => $cliente_nome,
                'email' => $cliente_email,
                'telefone' => $cliente_telefone,
                'rua' => $cliente_rua,
                'numero' => $cliente_numero,
                'bairro' => $cliente_bairro,
                'cidade' => $cliente_cidade
            ];
            
            $result = atualizarDadosCliente($pdo, $cliente_id, $dados_cliente);
            if(!$result) throw new Exception("Erro ao atualizar dados do cliente");
        }
        
        // Salvar materiais
        if(isset($_POST['materiais']) && is_array($_POST['materiais'])) {
            $result = salvarMateriaisOrcamento($pdo, $id, $_POST['materiais']);
            if(!$result) throw new Exception("Erro ao salvar materiais");
        }
        
        // Salvar serviços
        if(isset($_POST['servicos']) && is_array($_POST['servicos'])) {
            $result = salvarServicosOrcamento($pdo, $id, $_POST['servicos']);
            if(!$result) throw new Exception("Erro ao salvar servicos");
        }
        
        // Salvar produtos
        if(isset($_POST['produtos']) && is_array($_POST['produtos'])) {
            $result = salvarProdutosOrcamento($pdo, $id, $_POST['produtos']);
            if(!$result) throw new Exception("Erro ao salvar produtos");
        }
        
        // Salvar agendamento se existir
        if(isset($_POST['agendamento_data_inicio']) && !empty($_POST['agendamento_data_inicio'])) {
            $dados_agendamento = [
                'data_inicio' => $_POST['agendamento_data_inicio'],
                'hora_inicio' => $_POST['agendamento_hora_inicio'],
                'data_fim' => $_POST['agendamento_data_fim'] ?? $_POST['agendamento_data_inicio'],
                'hora_fim' => $_POST['agendamento_hora_fim'] ?? $_POST['agendamento_hora_inicio']
            ];
            
            $result = salvarAgendamentoOrcamento($pdo, $id, $dados_agendamento);
            if(!$result) {
                throw new Exception("Erro ao salvar agendamento");
            }
        }
        
        $pdo->commit();
        $sucesso = "Orcamento atualizado com sucesso!";
        
    } catch(Exception $e) {
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erro = $e->getMessage();
    }
}

// PROCESSAR FORMULÁRIOS POST - GERAR ORÇAMENTO
if($_POST && $acao == 'gerar_orcamento' && $id > 0) {
    try {
        $valor_total_input = $_POST['valor_total'];
        $observacoes_admin = sanitize($_POST['observacoes_admin']);
        $desconto = moedaParaFloat($_POST['desconto'] ?? 0);
        $acrescimo = moedaParaFloat($_POST['acrescimo'] ?? 0);
        $motivo_desconto = sanitize($_POST['motivo_desconto'] ?? '');
        $motivo_acrescimo = sanitize($_POST['motivo_acrescimo'] ?? '');
        
        // Dados do cliente (editáveis também aqui)
        $cliente_nome = sanitize($_POST['cliente_nome'] ?? '');
        $cliente_email = sanitize($_POST['cliente_email'] ?? '');
        $cliente_telefone = sanitize($_POST['cliente_telefone'] ?? '');
        $descricao = sanitize($_POST['descricao'] ?? '');
        
        $valor_total = moedaParaFloat($valor_total_input);
        
        if(!is_numeric($valor_total) || $valor_total < 0) {
            throw new Exception("Valor total invalido: " . $valor_total_input);
        }
        
        if($valor_total > 999999999.99) {
            throw new Exception("Valor total muito alto. O valor maximo permitido e R$ 999.999.999,99.");
        }

        $pdo->beginTransaction();
        
        $observacoes_final = $observacoes_admin;
        if($desconto > 0) {
            $observacoes_final .= "\nDesconto aplicado: " . formatarMoeda($desconto);
            if($motivo_desconto) $observacoes_final .= " - Motivo: {$motivo_desconto}";
        }
        if($acrescimo > 0) {
            $observacoes_final .= "\nAcrescimo aplicado: " . formatarMoeda($acrescimo);
            if($motivo_acrescimo) $observacoes_final .= " - Motivo: {$motivo_acrescimo}";
        }
        
        // Atualizar orçamento
        $stmt = $pdo->prepare("UPDATE orcamentos SET valor_total = ?, observacoes_admin = ?, status = 'gerado', descricao = ? WHERE id = ?");
        $stmt->execute([$valor_total, trim($observacoes_final), $descricao, $id]);
        
        // Buscar cliente_id do orçamento
        $stmt = $pdo->prepare("SELECT cliente_id FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        $orcamento_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $cliente_id = $orcamento_info['cliente_id'];
        
        // Atualizar dados do cliente se houver alterações
        if($cliente_id && $cliente_nome) {
            $dados_cliente = [
                'nome' => $cliente_nome,
                'email' => $cliente_email,
                'telefone' => $cliente_telefone,
                'rua' => '',
                'numero' => '',
                'bairro' => '',
                'cidade' => ''
            ];
            
            $result = atualizarDadosCliente($pdo, $cliente_id, $dados_cliente);
            if(!$result) throw new Exception("Erro ao atualizar dados do cliente");
        }
        
        // Salvar materiais
        if(isset($_POST['materiais']) && is_array($_POST['materiais'])) {
            $result = salvarMateriaisOrcamento($pdo, $id, $_POST['materiais']);
            if(!$result) throw new Exception("Erro ao salvar materiais");
        }
        
        // Salvar serviços
        if(isset($_POST['servicos']) && is_array($_POST['servicos'])) {
            $result = salvarServicosOrcamento($pdo, $id, $_POST['servicos']);
            if(!$result) throw new Exception("Erro ao salvar servicos");
        }
        
        // Salvar produtos
        if(isset($_POST['produtos']) && is_array($_POST['produtos'])) {
            $result = salvarProdutosOrcamento($pdo, $id, $_POST['produtos']);
            if(!$result) throw new Exception("Erro ao salvar produtos");
        }
        
        // Salvar agendamento se existir
        if(isset($_POST['agendamento_data_inicio']) && !empty($_POST['agendamento_data_inicio'])) {
            $dados_agendamento = [
                'data_inicio' => $_POST['agendamento_data_inicio'],
                'hora_inicio' => $_POST['agendamento_hora_inicio'],
                'data_fim' => $_POST['agendamento_data_fim'] ?? $_POST['agendamento_data_inicio'],
                'hora_fim' => $_POST['agendamento_hora_fim'] ?? $_POST['agendamento_hora_inicio']
            ];
            
            $result = salvarAgendamentoOrcamento($pdo, $id, $dados_agendamento);
            if(!$result) {
                throw new Exception("Erro ao salvar agendamento");
            }
        }
        
        $pdo->commit();
        $sucesso = "Orcamento gerado com sucesso!";
        
        if(isset($_POST['enviar_whatsapp'])) {
            $sucesso .= " | <a href='?acao=enviar_whatsapp&id={$id}' class='btn btn-success btn-sm' target='_blank'>Enviar WhatsApp</a>";
        }
        
    } catch(Exception $e) {
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erro = $e->getMessage();
    }
}

// PROCESSAR NOVO ORÇAMENTO
if($_POST && $acao == 'novo') {
    try {
        $cliente_nome = sanitize($_POST['cliente_nome'] ?? '');
        $cliente_telefone = sanitize($_POST['cliente_telefone'] ?? '');
        $cliente_email = sanitize($_POST['cliente_email'] ?? '');
        $descricao = sanitize($_POST['descricao'] ?? '');
        $equipamento_marca = sanitize($_POST['equipamento_marca'] ?? '');
        $equipamento_btus = sanitize($_POST['equipamento_btus'] ?? '');
        $equipamento_tipo = sanitize($_POST['equipamento_tipo'] ?? '');
        
        if(empty($cliente_nome) || empty($cliente_telefone)) {
            throw new Exception("Nome e telefone do cliente sao obrigatorios.");
        }
        
        $pdo->beginTransaction();
        
        // Verificar se cliente já existe pelo telefone
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE telefone = ?");
        $stmt->execute([$cliente_telefone]);
        $cliente_existente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($cliente_existente) {
            $cliente_id = $cliente_existente['id'];
            // Atualizar dados do cliente existente
            $dados_cliente = [
                'nome' => $cliente_nome,
                'email' => $cliente_email,
                'telefone' => $cliente_telefone,
                'rua' => '',
                'numero' => '',
                'bairro' => '',
                'cidade' => ''
            ];
            $result = atualizarDadosCliente($pdo, $cliente_id, $dados_cliente);
            if(!$result) throw new Exception("Erro ao atualizar cliente existente");
        } else {
            // Criar novo cliente
            $dados_cliente = [
                'nome' => $cliente_nome,
                'email' => $cliente_email,
                'telefone' => $cliente_telefone,
                'rua' => '',
                'numero' => '',
                'bairro' => '',
                'cidade' => ''
            ];
            $cliente_id = criarNovoCliente($pdo, $dados_cliente);
        }
        
        // Criar novo orçamento
        $stmt = $pdo->prepare("
            INSERT INTO orcamentos (cliente_id, equipamento_marca, equipamento_btus, equipamento_tipo, descricao, status, data_solicitacao) 
            VALUES (?, ?, ?, ?, ?, 'pendente', NOW())
        ");
        $stmt->execute([$cliente_id, $equipamento_marca, $equipamento_btus, $equipamento_tipo, $descricao]);
        $novo_id = $pdo->lastInsertId();
        
        $pdo->commit();
        
        $sucesso = "Novo orcamento criado com sucesso! ID: #{$novo_id}";
        $acao = 'editar';
        $id = $novo_id;
        
    } catch(Exception $e) {
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erro = $e->getMessage();
    }
}

// LISTAR ORÇAMENTOS
if($acao == 'listar') {
    $status_filter = $_GET['status'] ?? '';
    $pagina = $_GET['pagina'] ?? 1;
    $por_pagina = 15;
    $offset = ($pagina - 1) * $por_pagina;
    
    $mes_selecionado = $_GET['mes'] ?? date('m');
    $ano_selecionado = $_GET['ano'] ?? date('Y');
    
    // Construir query
    $sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone
            FROM orcamentos o 
            LEFT JOIN clientes c ON o.cliente_id = c.id 
            WHERE 1=1";
    $params = [];
    
    if($status_filter) {
        $sql .= " AND o.status = ?";
        $params[] = $status_filter;
    }
    
    $sql .= " AND MONTH(o.data_solicitacao) = ? AND YEAR(o.data_solicitacao) = ?";
    $params[] = $mes_selecionado;
    $params[] = $ano_selecionado;
    
    $sql .= " ORDER BY o.data_solicitacao DESC LIMIT $por_pagina OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total para paginação
    $sql_count = "SELECT COUNT(*) as total FROM orcamentos o WHERE 1=1";
    if($status_filter) {
        $sql_count .= " AND o.status = ?";
    }
    $sql_count .= " AND MONTH(o.data_solicitacao) = ? AND YEAR(o.data_solicitacao) = ?";
    
    $stmt = $pdo->prepare($sql_count);
    $stmt->execute($status_filter ? [$status_filter, $mes_selecionado, $ano_selecionado] : [$mes_selecionado, $ano_selecionado]);
    $total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_orcamentos / $por_pagina);
    
    // Estatísticas
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as total FROM orcamentos WHERE MONTH(data_solicitacao) = ? AND YEAR(data_solicitacao) = ? GROUP BY status");
    $stmt->execute([$mes_selecionado, $ano_selecionado]);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="mb-3 mb-md-0"><i class="fas fa-file-invoice-dollar"></i> Gerenciar Orcamentos</h2>
        <div class="header-actions">
            <a href="?acao=novo" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Novo Orcamento
            </a>
            <span class="total-info badge bg-secondary fs-6"><i class="fas fa-chart-bar"></i> Total: <?php echo $total_orcamentos; ?> orcamentos</span>
        </div>
    </div>

    <?php if($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="acao" value="listar">
                
                <div class="col-md-3 col-sm-6">
                    <label for="mes" class="form-label"><strong>Mes:</strong></label>
                    <select id="mes" name="mes" class="form-select">
                        <?php
                        $meses = [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Marco', 4 => 'Abril',
                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                        ];
                        foreach($meses as $num => $nome): ?>
                            <option value="<?php echo $num; ?>" <?php echo $num == $mes_selecionado ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label for="ano" class="form-label"><strong>Ano:</strong></label>
                    <select id="ano" name="ano" class="form-select">
                        <?php
                        $ano_atual = date('Y');
                        for($i = $ano_atual; $i >= $ano_atual - 5; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $ano_selecionado ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label for="statusFilter" class="form-label"><strong>Status:</strong></label>
                    <select id="statusFilter" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" <?php echo $status_filter == 'pendente' ? 'selected' : ''; ?>>Pendentes</option>
                        <option value="gerado" <?php echo $status_filter == 'gerado' ? 'selected' : ''; ?>>Gerados</option>
                        <option value="enviado" <?php echo $status_filter == 'enviado' ? 'selected' : ''; ?>>Enviados</option>
                        <option value="aprovado" <?php echo $status_filter == 'aprovado' ? 'selected' : ''; ?>>Aprovados</option>
                        <option value="concluido" <?php echo $status_filter == 'concluido' ? 'selected' : ''; ?>>Concluidos</option>
                        <option value="recusado" <?php echo $status_filter == 'recusado' ? 'selected' : ''; ?>>Recusados</option>
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Aplicar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <?php 
        $status_info = [
            'pendente' => ['label' => 'Pendentes', 'icon' => 'fas fa-clock', 'color' => 'warning'],
            'gerado' => ['label' => 'Gerados', 'icon' => 'fas fa-file-invoice', 'color' => 'info'],
            'enviado' => ['label' => 'Enviados', 'icon' => 'fas fa-paper-plane', 'color' => 'primary'],
            'aprovado' => ['label' => 'Aprovados', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            'concluido' => ['label' => 'Concluidos', 'icon' => 'fas fa-flag-checkered', 'color' => 'success'],
            'recusado' => ['label' => 'Recusados', 'icon' => 'fas fa-times-circle', 'color' => 'danger']
        ];
        
        foreach($status_info as $status_key => $info):
            $total = 0;
            foreach($stats as $stat) {
                if($stat['status'] == $status_key) {
                    $total = $stat['total'];
                    break;
                }
            }
        ?>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card border-<?php echo $info['color']; ?> shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <div class="stat-icon text-<?php echo $info['color']; ?> mb-2">
                        <i class="<?php echo $info['icon']; ?> fa-2x"></i>
                    </div>
                    <h3 class="stat-number mb-1"><?php echo $total; ?></h3>
                    <p class="stat-label text-muted mb-0 small"><?php echo $info['label']; ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Lista de Orçamentos -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Orcamentos</h5>
            <div class="mt-2 mt-md-0">
                <small class="text-muted">Mostrando <?php echo count($orcamentos); ?> de <?php echo $total_orcamentos; ?> orcamentos</small>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th>Cliente</th>
                            <th class="d-none d-md-table-cell">Servico</th>
                            <th class="d-none d-lg-table-cell">Equipamento</th>
                            <th width="120" class="d-none d-sm-table-cell">Data</th>
                            <th width="140">Valor</th>
                            <th width="120">Status</th>
                            <th width="200" class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orcamentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Nenhum orcamento encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($orcamentos as $orcamento): 
                                $servico_principal = buscarServicoPrincipal($pdo, $orcamento['id']);
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-primary">#<?php echo $orcamento['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong class="d-block"><?php echo $orcamento['cliente_nome']; ?></strong>
                                            <?php if($orcamento['cliente_telefone']): ?>
                                            <small class="text-muted d-block d-md-none"><?php echo $orcamento['cliente_telefone']; ?></small>
                                            <?php endif; ?>
                                            <small class="text-muted d-block d-sm-none">
                                                <?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php if($servico_principal): ?>
                                        <span class="badge bg-light text-dark"><?php echo $servico_principal['nome']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Servico nao definido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small class="text-muted">
                                        <?php echo $orcamento['equipamento_marca'] ?: '-'; ?>
                                        <?php echo $orcamento['equipamento_btus'] ? ' - ' . $orcamento['equipamento_btus'] . ' BTUs' : ''; ?>
                                    </small>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <small><?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?></small>
                                </td>
                                <td>
                                    <?php if($orcamento['valor_total'] && $orcamento['valor_total'] > 0): ?>
                                    <strong class="text-success"><?php echo formatarMoeda($orcamento['valor_total']); ?></strong>
                                    <?php else: ?>
                                    <span class="text-muted">Nao definido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge status-<?php echo $orcamento['status']; ?>">
                                        <?php echo ucfirst($orcamento['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm flex-wrap" role="group">
                                        <a href="?acao=editar&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?acao=gerar_orcamento&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-success" title="Gerar Orcamento">
                                            <i class="fas fa-calculator"></i>
                                        </a>
                                        <a href="?acao=enviar_whatsapp&id=<?php echo $orcamento['id']; ?>" 
                                           class="btn btn-outline-success" 
                                           title="Enviar WhatsApp" 
                                           target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <a href="gerar_pdf_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-danger" title="Gerar PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="?acao=excluir&id=<?php echo $orcamento['id']; ?>" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir o orcamento #<?php echo $orcamento['id']; ?>?')">
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
        </div>
        
        <!-- Paginação -->
        <?php if($total_paginas > 1): ?>
        <div class="card-footer bg-white">
            <nav aria-label="Paginacao">
                <ul class="pagination justify-content-center mb-0 flex-wrap">
                    <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?>&mes=<?php echo $mes_selecionado; ?>&ano=<?php echo $ano_selecionado; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <style>
    .stat-card {
        transition: transform 0.2s;
        border-left: 4px solid !important;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }
    
    .badge.status-pendente { background: #ffc107; color: #000; }
    .badge.status-gerado { background: #0dcaf0; color: #000; }
    .badge.status-enviado { background: #6f42c1; color: #fff; }
    .badge.status-aprovado { background: #198754; color: #fff; }
    .badge.status-concluido { background: #20c997; color: #fff; }
    .badge.status-recusado { background: #dc3545; color: #fff; }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .header-actions {
            margin-top: 10px;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>

    <?php
} 
// NOVO ORÇAMENTO
elseif($acao == 'novo') {
    ?>
    
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="mb-3 mb-md-0"><i class="fas fa-plus-circle"></i> Novo Orcamento</h2>
        <div class="header-actions">
            <a href="orcamentos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <?php if($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus"></i> Criar Novo Orcamento</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="cliente_nome" class="form-label">Nome do Cliente *</label>
                        <input type="text" id="cliente_nome" name="cliente_nome" class="form-control" 
                               placeholder="Digite o nome completo do cliente" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cliente_telefone" class="form-label">Telefone *</label>
                        <input type="text" id="cliente_telefone" name="cliente_telefone" class="form-control" 
                               placeholder="(00) 0 0000-0000" required>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="cliente_email" class="form-label">E-mail (opcional)</label>
                        <input type="email" id="cliente_email" name="cliente_email" class="form-control" 
                               placeholder="email@exemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label for="equipamento_tipo" class="form-label">Tipo de Equipamento</label>
                        <input type="text" id="equipamento_tipo" name="equipamento_tipo" class="form-control" 
                               placeholder="Ex: Ar-condicionado Split, Geladeira, etc.">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="equipamento_marca" class="form-label">Marca do Equipamento</label>
                        <input type="text" id="equipamento_marca" name="equipamento_marca" class="form-control" 
                               placeholder="Ex: Samsung, LG, etc.">
                    </div>
                    <div class="col-md-6">
                        <label for="equipamento_btus" class="form-label">BTUs / Capacidade</label>
                        <input type="text" id="equipamento_btus" name="equipamento_btus" class="form-control" 
                               placeholder="Ex: 9000 BTUs, 250L, etc.">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="descricao" class="form-label">Descricao do Problema / Servico *</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="4" 
                              placeholder="Descreva o problema ou servico necessario..." required></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="endereco_servico" class="form-label">Endereco do Servico (opcional)</label>
                    <textarea id="endereco_servico" name="endereco_servico" class="form-control" rows="2" 
                              placeholder="Caso seja diferente do endereco cadastrado do cliente"></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-plus-circle"></i> Criar Orcamento
                    </button>
                    <a href="orcamentos.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <style>
    .form-select, .form-control {
        border-radius: 0.5rem;
    }
    .btn-lg {
        padding: 0.75rem 2rem;
    }
    </style>
    
    <script>
    // Máscara para telefone
    document.getElementById('cliente_telefone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        if (value.length > 10) {
            // Formato: (00) 0 0000-0000
            value = value.replace(/^(\d{2})(\d{1})(\d{4})(\d{4})$/, '($1) $2 $3-$4');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
        } else if (value.length > 0) {
            value = value.replace(/^(\d*)$/, '($1');
        }
        
        e.target.value = value;
    });
    </script>
    
    <?php
    include 'includes/footer-admin.php';
    exit;
}
// EDITAR ORÇAMENTO
elseif($acao == 'editar' && $id > 0) {
    $orcamento = [];
    $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone, 
                          c.rua as cliente_rua, c.numero as cliente_numero, c.bairro as cliente_bairro, c.cidade as cliente_cidade
                          FROM orcamentos o 
                          LEFT JOIN clientes c ON o.cliente_id = c.id 
                          WHERE o.id = ?");
    $stmt->execute([$id]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$orcamento) {
        echo "<div class='alert alert-danger'>Orcamento nao encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    $materiais_orcamento = buscarMateriaisOrcamento($pdo, $id);
    $servicos_orcamento = buscarServicosOrcamento($pdo, $id);
    $produtos_orcamento = buscarProdutosOrcamento($pdo, $id);
    
    $agendamento_existente = [];
    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE orcamento_id = ?");
    $stmt->execute([$id]);
    $agendamento_existente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Buscar todos os materiais, serviços e produtos
    $stmt = $pdo->prepare("SELECT * FROM materiais WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $servico_principal = buscarServicoPrincipal($pdo, $id);
    
    // Buscar configurações de taxas
    $configs_taxas = $pdo->query("SELECT chave, valor FROM configuracoes WHERE chave IN ('taxa_cartao_parcelado')")
                        ->fetchAll(PDO::FETCH_KEY_PAIR);
    $taxa_parcelado = floatval($configs_taxas['taxa_cartao_parcelado'] ?? 21);
    
    // Calcular valores iniciais
    $total_materiais = calcularTotalMateriais($materiais_orcamento);
    $total_servicos = calcularTotalServicos($servicos_orcamento);
    $total_produtos = calcularTotalProdutos($produtos_orcamento);
    $valor_base = $total_materiais + $total_servicos + $total_produtos;
    $valor_total = floatval($orcamento['valor_total']) ?: $valor_base;
    $parcelamento = calcularParcelamento($valor_total, $taxa_parcelado, 12);
    ?>

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="mb-3 mb-md-0"><i class="fas fa-edit"></i> Editar Orcamento #<?php echo $orcamento['id']; ?></h2>
        <div class="header-actions">
            <div class="btn-group flex-wrap">
                <a href="?acao=gerar_orcamento&id=<?php echo $orcamento['id']; ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-calculator"></i> Gerar Orcamento
                </a>
                <a href="?acao=enviar_whatsapp&id=<?php echo $orcamento['id']; ?>" class="btn btn-success btn-sm" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="gerar_pdf_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="orcamentos.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <?php if($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-lg-8">
                <!-- Informações do Cliente - EDITÁVEIS -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Informacoes do Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_nome" class="form-label">Nome *</label>
                                <input type="text" id="cliente_nome" name="cliente_nome" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_nome']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_telefone" class="form-label">Telefone *</label>
                                <input type="text" id="cliente_telefone" name="cliente_telefone" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_telefone']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_email" class="form-label">E-mail</label>
                                <input type="email" id="cliente_email" name="cliente_email" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_cidade" class="form-label">Cidade</label>
                                <input type="text" id="cliente_cidade" name="cliente_cidade" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_cidade'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Observacoes do Cliente</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="3"><?php echo htmlspecialchars($orcamento['descricao']); ?></textarea>
                            <small class="text-muted">Estas observacoes sao do cliente e serao visiveis para ele</small>
                        </div>
                    </div>
                </div>

                <!-- Controle de Valores -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-percentage"></i> Controle de Valores</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status *</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="pendente" <?php echo $orcamento['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="gerado" <?php echo $orcamento['status'] == 'gerado' ? 'selected' : ''; ?>>Gerado</option>
                                    <option value="enviado" <?php echo $orcamento['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                    <option value="aprovado" <?php echo $orcamento['status'] == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                    <option value="concluido" <?php echo $orcamento['status'] == 'concluido' ? 'selected' : ''; ?>>Concluido</option>
                                    <option value="recusado" <?php echo $orcamento['status'] == 'recusado' ? 'selected' : ''; ?>>Recusado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="subtotal_calc" class="form-label">Subtotal Calculado</label>
                                <input type="text" id="subtotal_calc" class="form-control" 
                                       value="<?php echo formatarMoeda($valor_base); ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="valor_total" class="form-label">Valor Final *</label>
                                <input type="text" id="valor_total" name="valor_total" class="form-control money" 
                                       value="<?php echo formatarMoeda($valor_total); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agendamento -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Agendamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label for="agendamento_data_inicio" class="form-label">Data Inicio</label>
                                <input type="date" id="agendamento_data_inicio" name="agendamento_data_inicio" 
                                       class="form-control" 
                                       value="<?php echo $agendamento_existente['data_agendamento'] ?? ''; ?>"
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="agendamento_hora_inicio" class="form-label">Hora Inicio</label>
                                <select id="agendamento_hora_inicio" name="agendamento_hora_inicio" class="form-select">
                                    <option value="">Selecione</option>
                                    <?php
                                    $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                    foreach($horarios as $horario):
                                        $selected = ($agendamento_existente['hora_agendamento'] ?? '') == $horario ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $horario; ?>" <?php echo $selected; ?>>
                                        <?php echo $horario; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="agendamento_data_fim" class="form-label">Data Fim</label>
                                <input type="date" id="agendamento_data_fim" name="agendamento_data_fim" 
                                       class="form-control" 
                                       value="<?php echo $agendamento_existente['data_fim'] ?? ''; ?>"
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="agendamento_hora_fim" class="form-label">Hora Fim</label>
                                <select id="agendamento_hora_fim" name="agendamento_hora_fim" class="form-select">
                                    <option value="">Selecione</option>
                                    <?php
                                    $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                    foreach($horarios as $horario):
                                        $selected = ($agendamento_existente['hora_fim'] ?? '') == $horario ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $horario; ?>" <?php echo $selected; ?>>
                                        <?php echo $horario; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos para Venda -->
                <?php if(!empty($todos_produtos)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-snowflake"></i> Produtos para Venda</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllProdutos" class="form-check-input">
                                        </th>
                                        <th>Produto</th>
                                        <th width="100" class="text-center">BTUs</th>
                                        <th width="100" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Preco Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_produtos as $produto): 
                                        $produto_no_orcamento = null;
                                        foreach($produtos_orcamento as $prod_orc) {
                                            if($prod_orc['produto_id'] == $produto['id']) {
                                                $produto_no_orcamento = $prod_orc;
                                                break;
                                            }
                                        }
                                        
                                        $usar = $produto_no_orcamento ? true : false;
                                        $quantidade = $produto_no_orcamento ? $produto_no_orcamento['quantidade'] : 1;
                                        $subtotal = $produto['preco'] * $quantidade;
                                    ?>
                                    <tr class="produto-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="produtos[<?php echo $produto['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input produto-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $produto['nome']; ?></strong>
                                            <br><small class="text-muted"><?php echo $produto['marca']; ?> - <?php echo $produto['categoria']; ?></small>
                                            <?php if($produto['descricao']): ?>
                                            <br><small class="text-muted"><?php echo substr($produto['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $produto['btus']; ?></td>
                                        <td class="text-center">
                                            <input type="number" name="produtos[<?php echo $produto['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="1" step="1" 
                                                   class="form-control form-control-sm quantidade-produto"
                                                   data-preco="<?php echo $produto['preco']; ?>">
                                        </td>
                                        <td class="text-end preco-produto"><?php echo formatarMoeda($produto['preco']); ?></td>
                                        <td class="text-end subtotal-produto"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Serviços -->
                <?php if(!empty($todos_servicos)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Servicos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllServicos" class="form-check-input">
                                        </th>
                                        <th>Servico</th>
                                        <th width="100" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Valor Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_servicos as $servico): 
                                        $servico_no_orcamento = null;
                                        foreach($servicos_orcamento as $serv_orc) {
                                            if($serv_orc['servico_id'] == $servico['id']) {
                                                $servico_no_orcamento = $serv_orc;
                                                break;
                                            }
                                        }
                                        
                                        $usar = $servico_no_orcamento ? true : false;
                                        $quantidade = $servico_no_orcamento ? $servico_no_orcamento['quantidade'] : 1;
                                        $subtotal = $servico['preco_base'] * $quantidade;
                                    ?>
                                    <tr class="servico-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="servicos[<?php echo $servico['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input servico-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $servico['nome']; ?></strong>
                                            <?php if($servico['descricao']): ?>
                                            <br><small class="text-muted"><?php echo substr($servico['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="servicos[<?php echo $servico['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="1" step="1" 
                                                   class="form-control form-control-sm quantidade-servico"
                                                   data-preco="<?php echo $servico['preco_base']; ?>">
                                        </td>
                                        <td class="text-end valor-servico"><?php echo formatarMoeda($servico['preco_base']); ?></td>
                                        <td class="text-end subtotal-servico"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Materiais -->
                <?php if(!empty($todos_materiais)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> Materiais</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllMateriais" class="form-check-input">
                                        </th>
                                        <th>Material</th>
                                        <th width="120" class="d-none d-sm-table-cell">Categoria</th>
                                        <th width="120" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Preco Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_materiais as $material): 
                                        $material_no_orcamento = null;
                                        foreach($materiais_orcamento as $mat_orc) {
                                            if($mat_orc['material_id'] == $material['id']) {
                                                $material_no_orcamento = $mat_orc;
                                                break;
                                            }
                                        }
                                        
                                        $quantidade = $material_no_orcamento ? $material_no_orcamento['quantidade'] : ($material['quantidade_padrao'] ?? 1);
                                        $usar = $material_no_orcamento ? true : false;
                                        $subtotal = $material['preco_unitario'] * $quantidade;
                                    ?>
                                    <tr class="material-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="materiais[<?php echo $material['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input material-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $material['nome']; ?></strong>
                                            <?php if($material['descricao']): ?>
                                            <br><small class="text-muted d-none d-md-inline"><?php echo substr($material['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-secondary"><?php echo $material['categoria']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="materiais[<?php echo $material['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="0" step="0.5" 
                                                   class="form-control form-control-sm quantidade-material" 
                                                   data-preco="<?php echo $material['preco_unitario']; ?>">
                                            <small class="text-muted"><?php echo $material['unidade_medida']; ?></small>
                                        </td>
                                        <td class="text-end preco-material"><?php echo formatarMoeda($material['preco_unitario']); ?></td>
                                        <td class="text-end subtotal-material"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Observações Internas -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Observacoes Internas</h5>
                    </div>
                    <div class="card-body">
                        <textarea id="observacoes_admin" name="observacoes_admin" class="form-control" rows="4"><?php echo htmlspecialchars($orcamento['observacoes_admin']); ?></textarea>
                        <small class="text-muted">Estas observacoes ficam apenas internas, nao sao enviadas ao cliente.</small>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <!-- Resumo e Ações -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Resumo do Orcamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="resumo-valores mb-4">
                            <?php if(!empty($todos_produtos)): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Produtos:</span>
                                <span id="total-produtos"><?php echo formatarMoeda($total_produtos); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Servicos:</span>
                                <span id="total-servicos"><?php echo formatarMoeda($total_servicos); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Materiais:</span>
                                <span id="total-materiais"><?php echo formatarMoeda($total_materiais); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span><strong>Subtotal:</strong></span>
                                <span><strong id="subtotal"><?php echo formatarMoeda($valor_base); ?></strong></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fs-5"><strong>Valor Final:</strong></span>
                                <span class="fs-5 text-success" id="valor-final-display"><?php echo formatarMoeda($valor_total); ?></span>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Atualizar Orcamento
                            </button>
                            <a href="?acao=enviar_whatsapp&id=<?php echo $id; ?>" class="btn btn-success btn-lg" target="_blank">
                                <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                            </a>
                            <a href="gerar_pdf_orcamento.php?id=<?php echo $id; ?>" class="btn btn-danger btn-lg" target="_blank">
                                <i class="fas fa-file-pdf"></i> Gerar PDF
                            </a>
                            <a href="orcamentos.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatarMoedaJS(valor) {
            return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+,)/g, '$&.');
        }
        
        function moedaParaFloatJS(valor) {
            if(!valor || valor === 'R$ 0,00') return 0;
            valor = valor.toString().replace('R$ ', '').replace(/\./g, '').replace(',', '.');
            return parseFloat(valor) || 0;
        }
        
        function calcularTotais() {
            let totalProdutos = 0;
            let totalServicos = 0;
            let totalMateriais = 0;
            
            // Produtos
            document.querySelectorAll('.produto-row').forEach(row => {
                const checkbox = row.querySelector('.produto-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-produto');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked) {
                    const quantidade = parseFloat(quantidadeInput.value) || 1;
                    const subtotal = preco * quantidade;
                    totalProdutos += subtotal;
                    row.querySelector('.subtotal-produto').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-produto').textContent = 'R$ 0,00';
                }
            });
            
            // Serviços
            document.querySelectorAll('.servico-row').forEach(row => {
                const checkbox = row.querySelector('.servico-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-servico');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked) {
                    const quantidade = parseFloat(quantidadeInput.value) || 1;
                    const subtotal = preco * quantidade;
                    totalServicos += subtotal;
                    row.querySelector('.subtotal-servico').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-servico').textContent = 'R$ 0,00';
                }
            });
            
            // Materiais
            document.querySelectorAll('.material-row').forEach(row => {
                const checkbox = row.querySelector('.material-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-material');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked && quantidadeInput.value > 0) {
                    const quantidade = parseFloat(quantidadeInput.value) || 0;
                    const subtotal = preco * quantidade;
                    totalMateriais += subtotal;
                    row.querySelector('.subtotal-material').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-material').textContent = 'R$ 0,00';
                }
            });
            
            // Atualizar totais parciais
            if(document.getElementById('total-produtos')) {
                document.getElementById('total-produtos').textContent = formatarMoedaJS(totalProdutos);
            }
            document.getElementById('total-servicos').textContent = formatarMoedaJS(totalServicos);
            document.getElementById('total-materiais').textContent = formatarMoedaJS(totalMateriais);
            
            const subtotal = totalProdutos + totalServicos + totalMateriais;
            if(document.getElementById('subtotal_calc')) {
                document.getElementById('subtotal_calc').value = formatarMoedaJS(subtotal);
            }
            document.getElementById('subtotal').textContent = formatarMoedaJS(subtotal);
            
            // Atualizar valor final display
            const valorTotal = moedaParaFloatJS(document.getElementById('valor_total').value);
            document.getElementById('valor-final-display').textContent = formatarMoedaJS(valorTotal);
            
            return { totalProdutos, totalServicos, totalMateriais, subtotal };
        }
        
        function aplicarMascaraMonetaria(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value === '') {
                    e.target.value = 'R$ 0,00';
                    return;
                }
                
                if (value.length > 2) {
                    const reais = value.slice(0, -2);
                    const centavos = value.slice(-2);
                    value = reais + '.' + centavos;
                } else if (value.length === 2) {
                    value = '0.' + value;
                } else if (value.length === 1) {
                    value = '0.0' + value;
                }
                
                let [reais, cents] = parseFloat(value).toFixed(2).split('.');
                reais = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                
                e.target.value = `R$ ${reais},${cents}`;
            });
            
            input.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '' || value === '0') {
                    e.target.value = 'R$ 0,00';
                }
                calcularTotais();
            });
        }
        
        // Event Listeners
        document.querySelectorAll('.produto-checkbox, .servico-checkbox, .material-checkbox').forEach(element => {
            element.addEventListener('change', calcularTotais);
        });
        
        document.querySelectorAll('.quantidade-produto, .quantidade-servico, .quantidade-material').forEach(input => {
            input.addEventListener('input', calcularTotais);
        });
        
        // Aplicar máscara monetária
        document.querySelectorAll('.money').forEach(aplicarMascaraMonetaria);
        
        // Atualizar quando valor total mudar
        document.getElementById('valor_total').addEventListener('input', calcularTotais);
        
        // Select All
        if(document.getElementById('selectAllProdutos')) {
            document.getElementById('selectAllProdutos').addEventListener('change', function() {
                document.querySelectorAll('.produto-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        if(document.getElementById('selectAllServicos')) {
            document.getElementById('selectAllServicos').addEventListener('change', function() {
                document.querySelectorAll('.servico-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        if(document.getElementById('selectAllMateriais')) {
            document.getElementById('selectAllMateriais').addEventListener('change', function() {
                document.querySelectorAll('.material-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        calcularTotais();
    });
    </script>

    <style>
    .money { text-align: right; }
    .table-sm td, .table-sm th { padding: 0.5rem; }
    .resumo-valores { background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; }
    .btn-lg { padding: 0.75rem 1.5rem; font-size: 1.1rem; }
    </style>

    <?php
} 
// GERAR ORÇAMENTO (PÁGINA COMPLETA)
elseif($acao == 'gerar_orcamento' && $id > 0) {
    $orcamento = [];
    $stmt = $pdo->prepare("SELECT o.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone
                          FROM orcamentos o 
                          LEFT JOIN clientes c ON o.cliente_id = c.id 
                          WHERE o.id = ?");
    $stmt->execute([$id]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$orcamento) {
        echo "<div class='alert alert-danger'>Orcamento nao encontrado!</div>";
        include 'includes/footer-admin.php';
        exit;
    }
    
    $materiais_orcamento = buscarMateriaisOrcamento($pdo, $id);
    $servicos_orcamento = buscarServicosOrcamento($pdo, $id);
    $produtos_orcamento = buscarProdutosOrcamento($pdo, $id);
    
    // Buscar todos os materiais, serviços e produtos
    $stmt = $pdo->prepare("SELECT * FROM materiais WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ativo = 1 ORDER BY categoria, nome");
    $stmt->execute();
    $todos_produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $servico_principal = buscarServicoPrincipal($pdo, $id);
    
    // Buscar configurações de taxas
    $configs_taxas = $pdo->query("SELECT chave, valor FROM configuracoes WHERE chave IN ('taxa_cartao_avista', 'taxa_cartao_parcelado')")
                        ->fetchAll(PDO::FETCH_KEY_PAIR);
    $taxa_parcelado = floatval($configs_taxas['taxa_cartao_parcelado'] ?? 21);
    
    // Calcular valores iniciais
    $total_materiais = calcularTotalMateriais($materiais_orcamento);
    $total_servicos = calcularTotalServicos($servicos_orcamento);
    $total_produtos = calcularTotalProdutos($produtos_orcamento);
    $valor_base = $total_materiais + $total_servicos + $total_produtos;
    $valor_total = floatval($orcamento['valor_total']) ?: $valor_base;
    $parcelamento = calcularParcelamento($valor_total, $taxa_parcelado, 12);
    ?>

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="mb-3 mb-md-0"><i class="fas fa-calculator"></i> Gerar Orcamento - <?php echo $orcamento['cliente_nome']; ?></h2>
        <div class="header-actions">
            <a href="orcamentos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <?php if($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-lg-8">
                <!-- Informações do Cliente - EDITÁVEIS TAMBÉM AQUI -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Informacoes do Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_nome" class="form-label">Nome *</label>
                                <input type="text" id="cliente_nome" name="cliente_nome" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_nome']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_telefone" class="form-label">Telefone *</label>
                                <input type="text" id="cliente_telefone" name="cliente_telefone" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_telefone']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_email" class="form-label">E-mail</label>
                                <input type="email" id="cliente_email" name="cliente_email" class="form-control" 
                                       value="<?php echo htmlspecialchars($orcamento['cliente_email']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Observacoes do Cliente</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="3"><?php echo htmlspecialchars($orcamento['descricao']); ?></textarea>
                            <small class="text-muted">Estas observacoes sao do cliente e serao visiveis para ele</small>
                        </div>
                    </div>
                </div>

                <!-- Controle de Valores -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-percentage"></i> Controle de Valores</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="subtotal_calc" class="form-label">Subtotal Calculado</label>
                                <input type="text" id="subtotal_calc" class="form-control" 
                                       value="<?php echo formatarMoeda($valor_base); ?>" readonly>
                                <small class="text-muted">Calculado automaticamente</small>
                            </div>
                            <div class="col-md-4">
                                <label for="desconto" class="form-label">Desconto</label>
                                <input type="text" id="desconto" name="desconto" class="form-control money" 
                                       value="R$ 0,00">
                                <input type="text" id="motivo_desconto" name="motivo_desconto" 
                                       class="form-control form-control-sm mt-1" placeholder="Motivo do desconto">
                            </div>
                            <div class="col-md-4">
                                <label for="acrescimo" class="form-label">Acrescimo</label>
                                <input type="text" id="acrescimo" name="acrescimo" class="form-control money" 
                                       value="R$ 0,00">
                                <input type="text" id="motivo_acrescimo" name="motivo_acrescimo" 
                                       class="form-control form-control-sm mt-1" placeholder="Motivo do acrescimo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <label for="valor_total" class="form-label">Valor Final *</label>
                                <input type="text" id="valor_total" name="valor_total" class="form-control money" 
                                       value="<?php echo formatarMoeda($valor_total); ?>" required>
                                <small class="text-muted">Edite se necessario</small>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="btn-sincronizar" class="btn btn-outline-warning mt-4">
                                    <i class="fas fa-sync-alt"></i> Usar valor calculado
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info p-2">
                                    <strong>Resumo: </strong>
                                    Subtotal: <span id="subtotal_display"><?php echo formatarMoeda($valor_base); ?></span> 
                                    - Desconto: <span id="desconto_display">R$ 0,00</span>
                                    + Acrescimo: <span id="acrescimo_display">R$ 0,00</span>
                                    = <span id="valor_final" class="fs-4 text-success"><?php echo formatarMoeda($valor_total); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos para Venda -->
                <?php if(!empty($todos_produtos)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-snowflake"></i> Produtos para Venda</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllProdutos" class="form-check-input">
                                        </th>
                                        <th>Produto</th>
                                        <th width="100" class="text-center">BTUs</th>
                                        <th width="100" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Preco Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_produtos as $produto): 
                                        $produto_no_orcamento = null;
                                        foreach($produtos_orcamento as $prod_orc) {
                                            if($prod_orc['produto_id'] == $produto['id']) {
                                                $produto_no_orcamento = $prod_orc;
                                                break;
                                            }
                                        }
                                        
                                        $usar = $produto_no_orcamento ? true : false;
                                        $quantidade = $produto_no_orcamento ? $produto_no_orcamento['quantidade'] : 1;
                                        $subtotal = $produto['preco'] * $quantidade;
                                    ?>
                                    <tr class="produto-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="produtos[<?php echo $produto['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input produto-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $produto['nome']; ?></strong>
                                            <br><small class="text-muted"><?php echo $produto['marca']; ?> - <?php echo $produto['categoria']; ?></small>
                                            <?php if($produto['descricao']): ?>
                                            <br><small class="text-muted"><?php echo substr($produto['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $produto['btus']; ?></td>
                                        <td class="text-center">
                                            <input type="number" name="produtos[<?php echo $produto['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="1" step="1" 
                                                   class="form-control form-control-sm quantidade-produto"
                                                   data-preco="<?php echo $produto['preco']; ?>">
                                        </td>
                                        <td class="text-end preco-produto"><?php echo formatarMoeda($produto['preco']); ?></td>
                                        <td class="text-end subtotal-produto"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Serviços -->
                <?php if(!empty($todos_servicos)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Servicos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllServicos" class="form-check-input">
                                        </th>
                                        <th>Servico</th>
                                        <th width="100" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Valor Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_servicos as $servico): 
                                        $servico_no_orcamento = null;
                                        foreach($servicos_orcamento as $serv_orc) {
                                            if($serv_orc['servico_id'] == $servico['id']) {
                                                $servico_no_orcamento = $serv_orc;
                                                break;
                                            }
                                        }
                                        
                                        $usar = $servico_no_orcamento ? true : false;
                                        $quantidade = $servico_no_orcamento ? $servico_no_orcamento['quantidade'] : 1;
                                        $subtotal = $servico['preco_base'] * $quantidade;
                                    ?>
                                    <tr class="servico-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="servicos[<?php echo $servico['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input servico-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $servico['nome']; ?></strong>
                                            <?php if($servico['descricao']): ?>
                                            <br><small class="text-muted"><?php echo substr($servico['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="servicos[<?php echo $servico['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="1" step="1" 
                                                   class="form-control form-control-sm quantidade-servico"
                                                   data-preco="<?php echo $servico['preco_base']; ?>">
                                        </td>
                                        <td class="text-end valor-servico"><?php echo formatarMoeda($servico['preco_base']); ?></td>
                                        <td class="text-end subtotal-servico"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Materiais -->
                <?php if(!empty($todos_materiais)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> Materiais</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30" class="text-center">
                                            <input type="checkbox" id="selectAllMateriais" class="form-check-input">
                                        </th>
                                        <th>Material</th>
                                        <th width="120" class="d-none d-sm-table-cell">Categoria</th>
                                        <th width="120" class="text-center">Quantidade</th>
                                        <th width="120" class="text-end">Preco Unit.</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($todos_materiais as $material): 
                                        $material_no_orcamento = null;
                                        foreach($materiais_orcamento as $mat_orc) {
                                            if($mat_orc['material_id'] == $material['id']) {
                                                $material_no_orcamento = $mat_orc;
                                                break;
                                            }
                                        }
                                        
                                        $quantidade = $material_no_orcamento ? $material_no_orcamento['quantidade'] : ($material['quantidade_padrao'] ?? 1);
                                        $usar = $material_no_orcamento ? true : false;
                                        $subtotal = $material['preco_unitario'] * $quantidade;
                                    ?>
                                    <tr class="material-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="materiais[<?php echo $material['id']; ?>][usar]" 
                                                   value="1" <?php echo $usar ? 'checked' : ''; ?> 
                                                   class="form-check-input material-checkbox">
                                        </td>
                                        <td>
                                            <strong><?php echo $material['nome']; ?></strong>
                                            <?php if($material['descricao']): ?>
                                            <br><small class="text-muted d-none d-md-inline"><?php echo substr($material['descricao'], 0, 80); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-secondary"><?php echo $material['categoria']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="materiais[<?php echo $material['id']; ?>][quantidade]" 
                                                   value="<?php echo $quantidade; ?>" 
                                                   min="0" step="0.5" 
                                                   class="form-control form-control-sm quantidade-material" 
                                                   data-preco="<?php echo $material['preco_unitario']; ?>">
                                            <small class="text-muted"><?php echo $material['unidade_medida']; ?></small>
                                        </td>
                                        <td class="text-end preco-material"><?php echo formatarMoeda($material['preco_unitario']); ?></td>
                                        <td class="text-end subtotal-material"><?php echo formatarMoeda($subtotal); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Observações Internas -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Observacoes Internas</h5>
                    </div>
                    <div class="card-body">
                        <textarea id="observacoes_admin" name="observacoes_admin" class="form-control" rows="4"><?php echo htmlspecialchars($orcamento['observacoes_admin']); ?></textarea>
                        <small class="text-muted">Estas observacoes ficam apenas internas, nao sao enviadas ao cliente.</small>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <!-- Resumo e Ações -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Resumo do Orcamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="resumo-valores mb-4">
                            <?php if(!empty($todos_produtos)): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Produtos:</span>
                                <span id="total-produtos"><?php echo formatarMoeda($total_produtos); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Servicos:</span>
                                <span id="total-servicos"><?php echo formatarMoeda($total_servicos); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Materiais:</span>
                                <span id="total-materiais"><?php echo formatarMoeda($total_materiais); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span><strong>Subtotal:</strong></span>
                                <span><strong id="subtotal"><?php echo formatarMoeda($valor_base); ?></strong></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Desconto:</span>
                                <span id="total-desconto">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-warning">
                                <span>Acrescimo:</span>
                                <span id="total-acrescimo">R$ 0,00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fs-5"><strong>Valor Final:</strong></span>
                                <span class="fs-5 text-success" id="valor-final-display"><?php echo formatarMoeda($valor_total); ?></span>
                            </div>
                        </div>

                        <!-- Parcelamento -->
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-credit-card"></i> Parcelamento no Cartao</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Parc.</th>
                                                <th class="text-center">Valor Parcela</th>
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabela-parcelamento">
                                            <?php foreach([1, 2, 3, 6, 12] as $parcela): 
                                                if(isset($parcelamento[$parcela])):
                                            ?>
                                            <tr data-parcela="<?php echo $parcela; ?>">
                                                <td class="text-center"><?php echo $parcela; ?>x</td>
                                                <td class="text-end valor-parcela"><?php echo formatarMoeda($parcelamento[$parcela]['valor_parcela']); ?></td>
                                                <td class="text-end valor-total-parcela"><?php echo formatarMoeda($parcelamento[$parcela]['valor_total']); ?></td>
                                            </tr>
                                            <?php endif; endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted d-block mt-2">Taxa cartao: <?php echo $taxa_parcelado; ?>% (incluida nos valores)</small>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-grid gap-2">
                            <button type="submit" name="salvar" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Salvar Orcamento
                            </button>
                            <button type="submit" name="enviar_whatsapp" class="btn btn-success btn-lg">
                                <i class="fab fa-whatsapp"></i> Salvar e Enviar WhatsApp
                            </button>
                            <a href="gerar_pdf_orcamento.php?id=<?php echo $id; ?>" class="btn btn-danger btn-lg" target="_blank">
                                <i class="fas fa-file-pdf"></i> Gerar PDF
                            </a>
                            <a href="orcamentos.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatarMoedaJS(valor) {
            return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+,)/g, '$&.');
        }
        
        function moedaParaFloatJS(valor) {
            if(!valor || valor === 'R$ 0,00') return 0;
            valor = valor.toString().replace('R$ ', '').replace(/\./g, '').replace(',', '.');
            return parseFloat(valor) || 0;
        }
        
        function calcularTotais() {
            let totalProdutos = 0;
            let totalServicos = 0;
            let totalMateriais = 0;
            
            // Produtos
            document.querySelectorAll('.produto-row').forEach(row => {
                const checkbox = row.querySelector('.produto-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-produto');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked) {
                    const quantidade = parseFloat(quantidadeInput.value) || 1;
                    const subtotal = preco * quantidade;
                    totalProdutos += subtotal;
                    row.querySelector('.subtotal-produto').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-produto').textContent = 'R$ 0,00';
                }
            });
            
            // Serviços
            document.querySelectorAll('.servico-row').forEach(row => {
                const checkbox = row.querySelector('.servico-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-servico');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked) {
                    const quantidade = parseFloat(quantidadeInput.value) || 1;
                    const subtotal = preco * quantidade;
                    totalServicos += subtotal;
                    row.querySelector('.subtotal-servico').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-servico').textContent = 'R$ 0,00';
                }
            });
            
            // Materiais
            document.querySelectorAll('.material-row').forEach(row => {
                const checkbox = row.querySelector('.material-checkbox');
                const quantidadeInput = row.querySelector('.quantidade-material');
                const preco = parseFloat(quantidadeInput.dataset.preco);
                
                if(checkbox.checked && quantidadeInput.value > 0) {
                    const quantidade = parseFloat(quantidadeInput.value) || 0;
                    const subtotal = preco * quantidade;
                    totalMateriais += subtotal;
                    row.querySelector('.subtotal-material').textContent = formatarMoedaJS(subtotal);
                } else {
                    row.querySelector('.subtotal-material').textContent = 'R$ 0,00';
                }
            });
            
            // Atualizar totais parciais
            if(document.getElementById('total-produtos')) {
                document.getElementById('total-produtos').textContent = formatarMoedaJS(totalProdutos);
            }
            document.getElementById('total-servicos').textContent = formatarMoedaJS(totalServicos);
            document.getElementById('total-materiais').textContent = formatarMoedaJS(totalMateriais);
            
            const subtotal = totalProdutos + totalServicos + totalMateriais;
            if(document.getElementById('subtotal_calc')) {
                document.getElementById('subtotal_calc').value = formatarMoedaJS(subtotal);
            }
            document.getElementById('subtotal_display').textContent = formatarMoedaJS(subtotal);
            document.getElementById('subtotal').textContent = formatarMoedaJS(subtotal);
            
            // Calcular desconto e acréscimo
            const desconto = moedaParaFloatJS(document.getElementById('desconto').value);
            const acrescimo = moedaParaFloatJS(document.getElementById('acrescimo').value);
            
            document.getElementById('total-desconto').textContent = formatarMoedaJS(desconto);
            document.getElementById('desconto_display').textContent = formatarMoedaJS(desconto);
            document.getElementById('total-acrescimo').textContent = formatarMoedaJS(acrescimo);
            document.getElementById('acrescimo_display').textContent = formatarMoedaJS(acrescimo);
            
            // Calcular valor final SUGERIDO
            const valorFinalSugerido = subtotal - desconto + acrescimo;
            document.getElementById('valor_final').textContent = formatarMoedaJS(valorFinalSugerido);
            
            // Atualizar valor final display
            document.getElementById('valor-final-display').textContent = formatarMoedaJS(valorFinalSugerido);
            
            // Atualizar parcelamento com valor sugerido
            atualizarParcelamento(valorFinalSugerido);
            
            // Verificar se há diferença com o valor total informado
            const valorTotalInformado = moedaParaFloatJS(document.getElementById('valor_total').value);
            if (Math.abs(valorTotalInformado - valorFinalSugerido) > 0.01) {
                document.getElementById('valor_total').classList.add('border-warning');
                document.getElementById('valor_final').classList.add('text-warning');
            } else {
                document.getElementById('valor_total').classList.remove('border-warning');
                document.getElementById('valor_final').classList.remove('text-warning');
            }
            
            return { totalProdutos, totalServicos, totalMateriais, subtotal, valorFinalSugerido };
        }
        
        function atualizarParcelamento(valorTotal) {
            const taxa = <?php echo $taxa_parcelado; ?>;
            const valorComTaxa = valorTotal * (1 + (taxa / 100));
            
            // Atualizar valores na tabela de parcelamento
            const tabela = document.getElementById('tabela-parcelamento');
            const linhas = tabela.querySelectorAll('tr');
            
            linhas.forEach(linha => {
                const parcela = parseInt(linha.getAttribute('data-parcela'));
                if (parcela > 0) {
                    const valorParcela = valorComTaxa / parcela;
                    const celulaParcela = linha.querySelector('.valor-parcela');
                    const celulaTotal = linha.querySelector('.valor-total-parcela');
                    
                    if (celulaParcela) celulaParcela.textContent = formatarMoedaJS(valorParcela);
                    if (celulaTotal) celulaTotal.textContent = formatarMoedaJS(valorComTaxa);
                }
            });
        }
        
        function aplicarMascaraMonetaria(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value === '') {
                    e.target.value = 'R$ 0,00';
                    return;
                }
                
                if (value.length > 2) {
                    const reais = value.slice(0, -2);
                    const centavos = value.slice(-2);
                    value = reais + '.' + centavos;
                } else if (value.length === 2) {
                    value = '0.' + value;
                } else if (value.length === 1) {
                    value = '0.0' + value;
                }
                
                let [reais, cents] = parseFloat(value).toFixed(2).split('.');
                reais = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                
                e.target.value = `R$ ${reais},${cents}`;
                
                // Atualizar cálculos após a formatação
                setTimeout(calcularTotais, 10);
            });
            
            input.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '' || value === '0') {
                    e.target.value = 'R$ 0,00';
                }
                calcularTotais();
            });
        }
        
        // Event Listeners
        document.querySelectorAll('.produto-checkbox, .servico-checkbox, .material-checkbox').forEach(element => {
            element.addEventListener('change', calcularTotais);
        });
        
        document.querySelectorAll('.quantidade-produto, .quantidade-servico, .quantidade-material').forEach(input => {
            input.addEventListener('input', calcularTotais);
        });
        
        // Aplicar máscara monetária
        document.querySelectorAll('.money').forEach(aplicarMascaraMonetaria);
        
        // Atualizar quando desconto/acréscimo mudam
        document.getElementById('desconto').addEventListener('input', calcularTotais);
        document.getElementById('acrescimo').addEventListener('input', calcularTotais);
        document.getElementById('valor_total').addEventListener('input', function() {
            calcularTotais();
        });
        
        // Botão sincronizar
        document.getElementById('btn-sincronizar').addEventListener('click', function() {
            const valorSugerido = document.getElementById('valor_final').textContent;
            document.getElementById('valor_total').value = valorSugerido;
            document.getElementById('valor_total').classList.remove('border-warning');
            document.getElementById('valor_final').classList.remove('text-warning');
            calcularTotais();
        });
        
        // Select All
        if(document.getElementById('selectAllProdutos')) {
            document.getElementById('selectAllProdutos').addEventListener('change', function() {
                document.querySelectorAll('.produto-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        if(document.getElementById('selectAllServicos')) {
            document.getElementById('selectAllServicos').addEventListener('change', function() {
                document.querySelectorAll('.servico-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        if(document.getElementById('selectAllMateriais')) {
            document.getElementById('selectAllMateriais').addEventListener('change', function() {
                document.querySelectorAll('.material-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calcularTotais();
            });
        }
        
        // Inicializar cálculos
        calcularTotais();
    });
    </script>

    <style>
    .money { text-align: right; }
    .table-sm td, .table-sm th { padding: 0.5rem; }
    .resumo-valores { background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; }
    .btn-lg { padding: 0.75rem 1.5rem; font-size: 1.1rem; }
    .border-warning { border-color: #ffc107 !important; border-width: 2px !important; }
    </style>

    <?php
}

include 'includes/footer-admin.php';
?>