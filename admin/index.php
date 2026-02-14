<?php
// admin/index.php
include '../includes/config.php';

// admin/index.php
session_start();
if(isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] == true){
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>
// Estatísticas para o dashboard
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes");
$stmt->execute();
$total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orcamentos");
$stmt->execute();
$total_orcamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos");
$stmt->execute();
$total_agendamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produtos");
$stmt->execute();
$total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard {
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        .admin-header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav ul {
            display: flex;
            list-style: none;
        }
        .admin-nav ul li {
            margin-left: 20px;
        }
        .admin-nav ul li a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
        }
        .admin-nav ul li a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container admin-nav">
            <div class="logo">
                <span class="logo-icon">🌡️</span>
                <h1>ClimaTech Admin</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="servicos.php">Serviços</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="clientes.php">Clientes</a></li>
                    <li><a href="orcamentos.php">Orçamentos</a></li>
                    <li><a href="agendamentos.php">Agendamentos</a></li>
                    <li><a href="configuracoes.php">Configurações</a></li>
                    <li><a href="logout.php">Sair</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="container dashboard">
        <h2>Dashboard</h2>
        <p>Bem-vindo, <?php echo $_SESSION['admin_nome']; ?>!</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_clientes; ?></div>
                <div class="stat-label">Clientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orcamentos; ?></div>
                <div class="stat-label">Orçamentos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_agendamentos; ?></div>
                <div class="stat-label">Agendamentos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_produtos; ?></div>
                <div class="stat-label">Produtos</div>
            </div>
        </div>
        
        <div class="recent-activity">
            <h3>Atividade Recente</h3>
            <!-- Aqui você pode adicionar uma tabela com orçamentos e agendamentos recentes -->
        </div>
    </div>
</body>
</html>