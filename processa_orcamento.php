<?php
// processa_orcamento.php
include 'includes/config.php';

if($_POST) {
    // Coletar dados do formulário
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    $servico_id = $_POST['servico_id'];
    $marca = sanitize($_POST['marca']);
    $btus = $_POST['btus'];
    $tipo = sanitize($_POST['tipo']);
    $observacoes = sanitize($_POST['observacoes']);
    
    try {
        // Inserir cliente
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone]);
        $cliente_id = $pdo->lastInsertId();
        
        // Buscar informações do serviço
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
        $stmt->execute([$servico_id]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calcular valor total (aqui você pode adicionar lógica mais complexa)
        $valor_total = $servico['preco_base'];
        
        // Inserir orçamento
        $stmt = $pdo->prepare("INSERT INTO orcamentos (cliente_id, servico_id, equipamento_marca, equipamento_btus, equipamento_tipo, descricao, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cliente_id, $servico_id, $marca, $btus, $tipo, $observacoes, $valor_total]);
        $orcamento_id = $pdo->lastInsertId();
        
        // Enviar email de confirmação (implementar depois)
        // enviarEmailConfirmacao($email, $nome, $orcamento_id);
        
        // Redirecionar para página de sucesso
        header('Location: orcamento_sucesso.php?id=' . $orcamento_id);
        exit;
        
    } catch(PDOException $e) {
        // Log de erro
        error_log("Erro ao processar orçamento: " . $e->getMessage());
        
        // Redirecionar para página de erro
        header('Location: orcamento.php?erro=1');
        exit;
    }
} else {
    // Se não for POST, redirecionar
    header('Location: orcamento.php');
    exit;
}
?>