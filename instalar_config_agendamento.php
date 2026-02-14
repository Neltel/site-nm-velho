<?php
// instalar_config_agendamento.php
include 'includes/config.php';

echo "<h2>Instalando Configurações do Agendamento...</h2>";

try {
    // Criar tabela se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS config_agendamento (
            id INT PRIMARY KEY AUTO_INCREMENT,
            horario_inicio TIME DEFAULT '08:00:00',
            horario_fim TIME DEFAULT '18:00:00',
            horario_especial_inicio TIME DEFAULT '18:00:00',
            horario_especial_fim TIME DEFAULT '20:00:00',
            bloquear_finais_semana BOOLEAN DEFAULT TRUE,
            valor_hora_especial DECIMAL(10,2) DEFAULT 50.00,
            valor_final_semana DECIMAL(10,2) DEFAULT 150.00,
            limite_agendamentos_dia INT DEFAULT 8,
            whatsapp_empresa VARCHAR(20) DEFAULT '5517999999999',
            mensagem_whatsapp TEXT,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    echo "✅ Tabela criada com sucesso!<br>";
    
    // Verificar se já existe configuração
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM config_agendamento");
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if($total == 0) {
        // Inserir configuração padrão
        $stmt = $pdo->prepare("
            INSERT INTO config_agendamento (
                horario_inicio, horario_fim, horario_especial_inicio, horario_especial_fim,
                bloquear_finais_semana, valor_hora_especial, valor_final_semana,
                limite_agendamentos_dia, whatsapp_empresa, mensagem_whatsapp
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            '08:00:00',
            '18:00:00',
            '18:00:00',
            '20:00:00',
            1,
            50.00,
            150.00,
            8,
            '5517999999999',
            '📅 *Novo Agendamento ClimaTech*

👤 *Cliente:* {nome}
📞 *Telefone:* {telefone}
📧 *E-mail:* {email}
📍 *Endereço:* {endereco}

🔧 *Serviço:* {servico}
📅 *Data:* {data}
⏰ *Horário:* {hora}

💬 *Observações:*
{observacoes}

⚡ *Agendado via Site*'
        ]);
        
        echo "✅ Configuração padrão inserida!<br>";
    } else {
        echo "✅ Configuração já existe!<br>";
    }
    
    echo "<h3>🎉 Instalação concluída com sucesso!</h3>";
    echo "<p>Agora você pode <a href='agendamento.php'>testar o agendamento</a> ou <a href='admin/configuracoes.php'>configurar no painel admin</a>.</p>";
    
} catch(PDOException $e) {
    echo "❌ Erro na instalação: " . $e->getMessage();
}
?>