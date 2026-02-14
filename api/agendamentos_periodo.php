<?php
// api/agendamentos_periodo.php
header('Content-Type: application/json');

include '../includes/config.php';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-d');
$data_fim = $_GET['data_fim'] ?? $data_inicio;

$stmt = $pdo->prepare("
    SELECT a.*, c.nome as nome_cliente, 
           DATE_FORMAT(a.data_agendamento, '%d/%m') as data_agendamento
    FROM agendamentos a
    LEFT JOIN clientes c ON a.cliente_id = c.id
    WHERE a.data_agendamento BETWEEN ? AND ?
    AND a.status IN ('agendado', 'confirmado')
    ORDER BY a.data_agendamento, a.hora_agendamento
");

$stmt->execute([$data_inicio, $data_fim]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($agendamentos);
?>