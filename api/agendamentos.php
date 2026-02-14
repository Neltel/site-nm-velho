<?php
// api/agendamentos.php
header('Content-Type: application/json');
include '../includes/config.php';
include '../includes/funcoes_agendamento.php';

$acao = $_GET['acao'] ?? '';

switch($acao) {
    case 'horarios_disponiveis':
        horariosDisponiveis();
        break;
        
    case 'verificar_disponibilidade':
        verificarDisponibilidadeAPI();
        break;
        
    default:
        echo json_encode(['erro' => 'Ação não especificada']);
        break;
}

function horariosDisponiveis() {
    global $pdo;
    
    $data = $_GET['data'] ?? '';
    
    if(!$data) {
        echo json_encode(['erro' => 'Data não especificada']);
        return;
    }
    
    $horarios = buscarHorariosDisponiveis($data, $pdo);
    $agendamentos = buscarAgendamentosDoDia($data, $pdo);
    
    echo json_encode([
        'data' => $data,
        'horarios' => $horarios,
        'agendamentos' => $agendamentos,
        'total_horarios' => count($horarios),
        'total_agendamentos' => count($agendamentos)
    ]);
}

function verificarDisponibilidadeAPI() {
    global $pdo;
    
    $data = $_GET['data'] ?? '';
    $hora = $_GET['hora'] ?? '';
    
    if(!$data || !$hora) {
        echo json_encode(['erro' => 'Data e hora não especificadas']);
        return;
    }
    
    $disponibilidade = verificarDisponibilidade($data, $hora, $pdo);
    
    echo json_encode([
        'data' => $data,
        'hora' => $hora,
        'disponibilidade' => $disponibilidade
    ]);
}
?>