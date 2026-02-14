<?php
/**
 * admin/login.php
 * 
 * Sistema de login seguro para administradores
 * Implementa proteção contra:
 * - SQL Injection (usando PDO prepared statements)
 * - Brute force (limite de tentativas)
 * - Session hijacking (regeneração de ID de sessão)
 * - XSS (sanitização de dados)
 */

// Inclui arquivo de configuração principal
require_once '../confg.php';

// Redirecionar se já estiver logado
if(isAdminLogado()) {
    redirect('dashboard.php');
}

// Variáveis para controle de tentativas de login
$ip = $_SERVER['REMOTE_ADDR'];
$tentativa_key = 'login_tentativas_' . md5($ip);
$bloqueio_key = 'login_bloqueado_' . md5($ip);

// Verificar se IP está bloqueado
if(isset($_SESSION[$bloqueio_key]) && $_SESSION[$bloqueio_key] > time()) {
    $tempo_restante = $_SESSION[$bloqueio_key] - time();
    $erro = "Muitas tentativas de login. Tente novamente em " . ceil($tempo_restante / 60) . " minutos.";
    registrarLog('security', "IP bloqueado tentou acessar login", ['ip' => $ip]);
}
// Processar login
elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $erro = "Token de segurança inválido. Por favor, recarregue a página.";
        registrarLog('security', "Tentativa de login com CSRF token inválido", ['ip' => $ip]);
    } else {
        // Sanitizar dados de entrada
        $usuario = sanitize($_POST['usuario']);
        $senha = $_POST['senha']; // Não sanitizar a senha (pode conter caracteres especiais)
        
        // Validar campos
        if(empty($usuario) || empty($senha)) {
            $erro = "Por favor, preencha todos os campos.";
        } else {
            try {
                // Buscar administrador no banco de dados usando prepared statement
                $stmt = $pdo->prepare("SELECT * FROM administradores WHERE usuario = ? AND ativo = 1");
                $stmt->execute([$usuario]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar se usuário existe e senha está correta
                if($admin && password_verify($senha, $admin['senha'])) {
                    // Login bem-sucedido
                    
                    // Regenerar ID de sessão para prevenir session fixation
                    session_regenerate_id(true);
                    
                    // Definir variáveis de sessão
                    $_SESSION['admin_logado'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nome'] = $admin['nome'];
                    $_SESSION['admin_usuario'] = $admin['usuario'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_nivel'] = $admin['nivel_acesso'] ?? 'atendente';
                    $_SESSION['ultimo_acesso'] = time();
                    
                    // Limpar tentativas de login
                    unset($_SESSION[$tentativa_key]);
                    unset($_SESSION[$bloqueio_key]);
                    
                    // Atualizar último acesso no banco
                    $stmt = $pdo->prepare("UPDATE administradores SET ultimo_acesso = NOW() WHERE id = ?");
                    $stmt->execute([$admin['id']]);
                    
                    // Registrar log de acesso bem-sucedido
                    registrarLog('info', "Login bem-sucedido", [
                        'usuario' => $usuario,
                        'ip' => $ip
                    ]);
                    
                    // Redirecionar para dashboard
                    redirect('dashboard.php');
                    
                } else {
                    // Login falhou - incrementar contador de tentativas
                    if(!isset($_SESSION[$tentativa_key])) {
                        $_SESSION[$tentativa_key] = 0;
                    }
                    $_SESSION[$tentativa_key]++;
                    
                    // Bloquear se excedeu máximo de tentativas
                    if($_SESSION[$tentativa_key] >= MAX_LOGIN_ATTEMPTS) {
                        $_SESSION[$bloqueio_key] = time() + LOGIN_TIMEOUT;
                        $erro = "Muitas tentativas de login. Você foi bloqueado por " . (LOGIN_TIMEOUT / 60) . " minutos.";
                        
                        registrarLog('security', "IP bloqueado por excesso de tentativas de login", [
                            'ip' => $ip,
                            'tentativas' => $_SESSION[$tentativa_key]
                        ]);
                    } else {
                        $tentativas_restantes = MAX_LOGIN_ATTEMPTS - $_SESSION[$tentativa_key];
                        $erro = "Usuário ou senha incorretos. Tentativas restantes: " . $tentativas_restantes;
                        
                        registrarLog('warning', "Tentativa de login falhou", [
                            'usuario' => $usuario,
                            'ip' => $ip,
                            'tentativa' => $_SESSION[$tentativa_key]
                        ]);
                    }
                }
            } catch(PDOException $e) {
                // Erro no banco de dados - não expor detalhes
                $erro = "Erro ao processar login. Tente novamente.";
                error_log("Erro no login: " . $e->getMessage());
                registrarLog('error', "Erro no processamento de login", [
                    'erro' => $e->getMessage(),
                    'ip' => $ip
                ]);
            }
        }
    }
}

// Gerar novo token CSRF para o formulário
$csrf_token = generateCsrfToken();
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo | N&M Refrigeração</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Reset e configurações básicas */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Background gradiente */
        body {
            background: linear-gradient(135deg, #0066cc 0%, #00a8ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Container principal do login */
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Animação de entrada */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Cabeçalho do login */
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* Logo */
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .login-logo-icon {
            font-size: 3rem;
            color: #0066cc;
        }
        
        .login-logo h1 {
            color: #0066cc;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.95rem;
            margin-top: 5px;
        }
        
        /* Alertas */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            font-size: 0.9rem;
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
        
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        
        /* Formulário */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
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
        
        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        /* Botão */
        .btn {
            width: 100%;
            padding: 13px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover:not(:disabled) {
            background: #0052a3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,102,204,0.3);
        }
        
        .btn:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        /* Rodapé do login */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 0.85rem;
        }
        
        /* Info de credenciais (apenas para desenvolvimento) */
        .credentials-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.85rem;
        }
        
        .credentials-info h4 {
            margin-bottom: 10px;
            color: #856404;
            font-size: 0.9rem;
        }
        
        .credentials-info code {
            background: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #d63031;
        }
        
        .credentials-info p {
            margin: 5px 0;
        }
        
        /* Link de voltar ao site */
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #0066cc;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #0052a3;
            text-decoration: underline;
        }
        
        /* Responsividade */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-logo h1 {
                font-size: 1.5rem;
            }
            
            .login-logo-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Cabeçalho com logo -->
        <div class="login-header">
            <div class="login-logo">
                <span class="login-logo-icon">❄️</span>
                <h1>N&M Refrigeração</h1>
            </div>
            <p>Painel Administrativo</p>
        </div>
        
        <!-- Mensagens de erro ou aviso -->
        <?php if(isset($erro)): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Erro:</strong> <?php echo htmlspecialchars($erro); ?>
        </div>
        <?php endif; ?>
        
        <!-- Formulário de login -->
        <form method="POST" autocomplete="off">
            <!-- Token CSRF para segurança -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <!-- Campo de usuário -->
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input 
                    type="text" 
                    id="usuario" 
                    name="usuario" 
                    class="form-control" 
                    required 
                    autofocus
                    autocomplete="username"
                    <?php echo isset($_SESSION[$bloqueio_key]) ? 'disabled' : ''; ?>
                    value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                >
            </div>
            
            <!-- Campo de senha -->
            <div class="form-group">
                <label for="senha">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    class="form-control" 
                    required
                    autocomplete="current-password"
                    <?php echo isset($_SESSION[$bloqueio_key]) ? 'disabled' : ''; ?>
                >
            </div>
            
            <!-- Botão de submit -->
            <button 
                type="submit" 
                class="btn"
                <?php echo isset($_SESSION[$bloqueio_key]) ? 'disabled' : ''; ?>
            >
                <?php echo isset($_SESSION[$bloqueio_key]) ? '🔒 Bloqueado' : '🔐 Entrar no Painel'; ?>
            </button>
        </form>
        
        <!-- Rodapé -->
        <div class="login-footer">
            <p>Sistema de Gerenciamento Integrado</p>
            <a href="../index.php" class="back-link">← Voltar ao site</a>
            
            <?php if ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false): ?>
            <!-- Credenciais de teste (APENAS EM DESENVOLVIMENTO) -->
            <div class="credentials-info">
                <h4>🔐 Credenciais de Desenvolvimento</h4>
                <p><strong>Usuário:</strong> <code>admin</code></p>
                <p><strong>Senha:</strong> <code>admin123</code></p>
                <p style="margin-top: 10px; font-size: 0.75rem; color: #856404;">
                    ⚠️ Estas credenciais são apenas para ambiente de desenvolvimento.<br>
                    Em produção, altere a senha imediatamente!
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Script para foco automático no campo usuário
        document.addEventListener('DOMContentLoaded', function() {
            // Foco no primeiro campo disponível
            const usuarioField = document.getElementById('usuario');
            if(usuarioField && !usuarioField.disabled) {
                usuarioField.focus();
            }
        });

        // Prevenir múltiplos submits
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            if(btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = '⏳ Processando...';
                // Reabilitar após 3 segundos se algo der errado
                setTimeout(function() {
                    btn.disabled = false;
                    btn.innerHTML = '🔐 Entrar no Painel';
                }, 3000);
            }
        });
    </script>
</body>
</html>