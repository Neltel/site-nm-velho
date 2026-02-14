<?php
// corrigir_admin.php
// Execute este arquivo UMA VEZ para corrigir o usuário admin, depois DELETE-O!

include 'includes/config.php';

try {
    // Verificar se o admin já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM administradores WHERE usuario = 'admin'");
    $stmt->execute();
    $admin_existe = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if($admin_existe > 0) {
        // Atualizar senha do admin existente
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE administradores SET senha = ? WHERE usuario = 'admin'");
        $stmt->execute([$senha_hash]);
        echo "✅ Senha do admin atualizada com sucesso!<br>";
        echo "Senha: 123456<br>";
        echo "Hash: " . $senha_hash . "<br>";
    } else {
        // Criar novo admin
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO administradores (usuario, senha, nome, email) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $senha_hash, 'Administrador Principal', 'admin@climatech.com.br']);
        echo "✅ Usuário admin criado com sucesso!<br>";
        echo "Usuário: admin<br>";
        echo "Senha: 123456<br>";
        echo "Hash: " . $senha_hash . "<br>";
    }
    
    echo "<br>⚠️ <strong>DELETE ESTE ARQUIVO AGORA!</strong> ⚠️";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>