<?php
// confirmacao_orcamento.php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

$orcamento_id = $_GET['id'] ?? 0;

if(!$orcamento_id) {
    header('Location: orcamento_completo.php');
    exit;
}

// Buscar dados do orçamento
$stmt = $pdo->prepare("
    SELECT o.*, 
           c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone,
           a.data_agendamento, a.hora_agendamento, a.hora_fim
    FROM orcamentos o
    LEFT JOIN clientes c ON o.cliente_id = c.id
    LEFT JOIN agendamentos a ON o.agendamento_id = a.id
    WHERE o.id = ?
");
$stmt->execute([$orcamento_id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$orcamento) {
    header('Location: orcamento_completo.php');
    exit;
}

// Buscar serviços
$stmt = $pdo->prepare("
    SELECT sm.*, s.nome as servico_nome
    FROM orcamento_servicos_multiplos sm
    JOIN servicos s ON sm.servico_id = s.id
    WHERE sm.orcamento_id = ?
");
$stmt->execute([$orcamento_id]);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação Confirmada - N&M Refrigeração</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container py-5">
        <div class="text-center mb-5">
            <div class="confirmation-icon mb-4">
                <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h1 class="display-5 fw-bold">Solicitação Confirmada!</h1>
            <p class="lead text-muted">Seu orçamento foi recebido e o agendamento foi realizado com sucesso.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Resumo da Solicitação #<?php echo $orcamento_id; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h5><i class="fas fa-user me-2"></i> Dados do Cliente</h5>
                                <hr>
                                <p><strong>Nome:</strong> <?php echo $orcamento['cliente_nome']; ?></p>
                                <p><strong>Telefone:</strong> <?php echo $orcamento['cliente_telefone']; ?></p>
                                <p><strong>E-mail:</strong> <?php echo $orcamento['cliente_email']; ?></p>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <h5><i class="fas fa-calendar-alt me-2"></i> Agendamento</h5>
                                <hr>
                                <?php if($orcamento['data_agendamento']): ?>
                                <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($orcamento['data_agendamento'])); ?></p>
                                <p><strong>Horário:</strong> <?php echo $orcamento['hora_agendamento']; ?> - <?php echo $orcamento['hora_fim']; ?></p>
                                <?php endif; ?>
                                <p><strong>Status:</strong> <span class="badge bg-warning">Aguardando confirmação</span></p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="fas fa-tools me-2"></i> Serviços Solicitados</h5>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Serviço</th>
                                            <th class="text-center">Quantidade</th>
                                            <th class="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($servicos as $servico): ?>
                                        <tr>
                                            <td><?php echo $servico['servico_nome']; ?></td>
                                            <td class="text-center"><?php echo $servico['quantidade']; ?></td>
                                            <td class="text-end"><?php echo formatarMoeda($servico['preco_unitario'] * $servico['quantidade']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong><?php echo formatarMoeda($orcamento['valor_total']); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i> Próximos Passos:</h6>
                            <ol class="mb-0">
                                <li>Você receberá um e-mail de confirmação em breve</li>
                                <li>Nossa equipe entrará em contato para confirmar os detalhes</li>
                                <li>O orçamento será enviado para aprovação</li>
                                <li>Após aprovação, o serviço será realizado na data agendada</li>
                            </ol>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-home me-2"></i> Voltar para Home
                            </a>
                            <a href="meus_pedidos.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-clipboard-list me-2"></i> Meus Pedidos
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Dúvidas? Entre em contato: (17) 9 9624-0725 | contato@nmrefrigeracao.com.br
                    </small>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>