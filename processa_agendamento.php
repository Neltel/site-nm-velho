<?php
// processa_agendamento.php
include 'includes/config.php';

if($_POST) {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    $servico_id = $_POST['servico_id'];
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];
    $observacoes = sanitize($_POST['observacoes']);
    
    try {
        // Verificar disponibilidade
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ? AND status IN ('agendado', 'confirmado')");
        $stmt->execute([$data_agendamento, $hora_agendamento]);
        $agendamentos_existentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if($agendamentos_existentes > 0) {
            header('Location: agendamento.php?erro=disponibilidade');
            exit;
        }
        
        // Inserir cliente
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone]);
        $cliente_id = $pdo->lastInsertId();
        
        // Inserir agendamento
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, servico_id, data_agendamento, hora_agendamento, observacoes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cliente_id, $servico_id, $data_agendamento, $hora_agendamento, $observacoes]);
        $agendamento_id = $pdo->lastInsertId();
        
        // Enviar confirmação (implementar depois)
        // enviarConfirmacaoAgendamento($email, $nome, $data_agendamento, $hora_agendamento);
        
        header('Location: agendamento_sucesso.php?id=' . $agendamento_id);
        exit;
        
    } catch(PDOException $e) {
        error_log("Erro ao processar agendamento: " . $e->getMessage());
        header('Location: agendamento.php?erro=1');
        exit;
    }
} else {
    header('Location: agendamento.php');
    exit;
}
?>