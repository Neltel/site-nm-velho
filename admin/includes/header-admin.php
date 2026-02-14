<?php
// admin/includes/header-admin.php
if(!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - N&M Refrigeração</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Customizado -->
    <style>

        
        :root {
            --primary-color: <?php echo $configs_site['cor_primaria'] ?? '#6a74e6'; ?>;
            --secondary-color: <?php echo $configs_site['cor_secundaria'] ?? '#3a1dc9'; ?>;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            overflow-x: hidden;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Header Styles */
        .admin-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }

        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-logo span {
            font-size: 2rem;
            background: rgba(255,255,255,0.2);
            padding: 8px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .admin-logo h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(45deg, #fff, #e0f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1001;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .sidebar-nav {
            padding: 2rem 1rem;
            margin-top: var(--header-height);
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar-nav li {
            margin-bottom: 8px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--accent-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-nav a:hover::before {
            transform: scaleY(1);
        }

        .sidebar-nav a.active {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .sidebar-nav a.active::before {
            transform: scaleY(1);
        }

        .sidebar-nav i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }

        .admin-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            min-height: calc(100vh - var(--header-height) - 4rem);
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Cards Modernos */
        .modern-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 0 0 1rem 0;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary-color);
        }

        /* Botões Modernos */
        .btn-modern {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, var(--warning-color), #fbbf24);
            color: white;
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, var(--danger-color), #f87171);
            color: white;
        }

        /* Badges Modernos */
        .badge-modern {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-success {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, var(--warning-color), #fbbf24);
            color: white;
        }

        .badge-danger {
            background: linear-gradient(135deg, var(--danger-color), #f87171);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, var(--accent-color), #60a5fa);
            color: white;
        }

        /* Tabelas Modernas */
        .table-modern {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table-modern tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }

            .admin-header {
                left: 0;
            }

            .admin-main {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        /* Scrollbar Personalizada */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-nav">
                <ul>
                    <li>
                        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="financeiro.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'financeiro.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Financeiro</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="servicos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'servicos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tools"></i>
                            <span>Serviços</span>
                        </a>
                    </li>
                    <li>
                        <a href="produtos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'produtos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-snowflake"></i>
                            <span>Produtos</span>
                        </a>
                    </li>
                    <li>
                        <a href="materiais.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'materiais.php' ? 'active' : ''; ?>">
                            <i class="fas fa-boxes"></i>
                            <span>Materiais</span>
                        </a>
                    </li>
                    <li>
                        <a href="clientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="orcamentos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orcamentos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Orçamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="agendamentos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'agendamentos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Agendamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-cog"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <li>
                        <a href="configuracoes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cogs"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                    <li>
                        <a href="configuracoes-site.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes-site.php' ? 'active' : ''; ?>">
                            <i class="fas fa-globe"></i>
                            <span>Configurações do Site</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Header -->
        <header class="admin-header">
            <div class="admin-nav">
                <div class="admin-logo">
                    <span>❄️</span>
                    <h1>N&M Refrigeração</h1>
                </div>
                <div class="admin-user">
                    <div class="user-avatar">
                        <?php 
                        $iniciais = strtoupper(substr($_SESSION['admin_nome'] ?? 'A', 0, 1));
                        echo $iniciais;
                        ?>
                    </div>
                    <span>Olá, <?php echo $_SESSION['admin_nome'] ?? 'Admin'; ?></span>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Sair
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="admin-main">
            <main class="admin-content fade-in">
                <!-- O conteúdo específico de cada página será inserido aqui -->