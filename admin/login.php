<?php

session_start();
// admin/login.php
include '../includes/config.php';

// Redirecionar se já estiver logado
if(isset($_SESSION['admin_logado'])) {
    header('Location: index.php');
    exit;
}

// Processar login
if($_POST) {
    $usuario = sanitize($_POST['usuario']);
    $senha = $_POST['senha'];
    
    // DEBUG: Mostrar o que está sendo enviado (remover depois)
    // error_log("Tentativa de login: Usuário: $usuario, Senha: $senha");
    
    $stmt = $pdo->prepare("SELECT * FROM administradores WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($admin) {
        // DEBUG: Mostrar hash da senha no banco (remover depois)
        // error_log("Hash no banco: " . $admin['senha']);
        
        // Verificar senha
        if(password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_usuario'] = $admin['usuario'];
            
            // DEBUG: Login bem sucedido
            // error_log("Login bem sucedido para: " . $admin['usuario']);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = "Senha incorreta!";
            // DEBUG: Senha não confere
            // error_log("Senha incorreta para: " . $usuario);
        }
    } else {
        $erro = "Usuário não encontrado!";
        // DEBUG: Usuário não existe
        // error_log("Usuário não encontrado: " . $usuario);
    }
}

// Se chegou aqui, mostrar formulário de login
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0066cc 0%, #00a8ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .login-logo-icon {
            font-size: 2.5rem;
            color: #0066cc;
        }
        
        .login-logo h1 {
            color: #0066cc;
            font-size: 1.8rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert-danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .alert-info {
            background: #d1edff;
            border-color: #0066cc;
            color: #004085;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #0052a3;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 0.85rem;
        }
        
        .credentials-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.8rem;
        }
        
        .credentials-info h4 {
            margin-bottom: 8px;
            color: #856404;
        }
        
        .credentials-info code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <span class="login-logo-icon">🌡️</span>
                <h1>ClimaTech</h1>
            </div>
            <p>Painel Administrativo</p>
        </div>
        
        <?php if(isset($erro)): ?>
        <div class="alert alert-danger">
            <?php echo $erro; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" class="form-control" required 
                       value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            
            <button type="submit" class="btn">Entrar no Painel</button>
        </form>
        
        <div class="login-footer">
            <p>Sistema de Gerenciamento ClimaTech</p>
            
            <div class="credentials-info">
                <h4>🔐 Credenciais de Teste</h4>
                <p><strong>Usuário:</strong> <code>admin</code></p>
                <p><strong>Senha:</strong> <code>123456</code></p>
                <p style="margin-top: 8px; font-size: 0.75rem; color: #856404;">
                    ⚠️ Estas credenciais são para ambiente de desenvolvimento
                </p>
            </div>
        </div>
    </div>

    <script>
        // Foco automático no campo usuário
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('usuario').focus();
        });

        // Mostrar/ocultar senha (opcional)
        document.getElementById('senha').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>