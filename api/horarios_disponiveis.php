<?php
// api/horarios_disponiveis.php
header('Content-Type: application/json');

// O caminho para os includes deve ser relativo ao diretório API
include '../includes/config.php';
include '../includes/funcoes_agendamento.php';

if(isset($_GET['data'])) {
    $data = $_GET['data'];
    
    // Validar formato da data
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        echo json_encode([]);
        exit;
    }
    
    // Gerar horários disponíveis para a data
    // Chama a função CORRIGIDA de funcoes_agendamento.php
    $horarios = gerarHorariosDisponiveis($pdo, $data);
    
    echo json_encode($horarios);
} else {
    echo json_encode([]);
}
?>