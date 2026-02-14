<?php
// corrigir_agendamentos.php
include 'includes/config.php';

echo "<h2>🔧 Corrigindo Estrutura da Tabela Agendamentos...</h2>";

try {
    // Verificar se a coluna acrescimo_especial existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM agendamentos LIKE 'acrescimo_especial'");
    $stmt->execute();
    $coluna_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$coluna_existe) {
        echo "❌ Coluna 'acrescimo_especial' não encontrada. Adicionando...<br>";
        
        // Adicionar coluna acrescimo_especial
        $pdo->exec("ALTER TABLE agendamentos ADD COLUMN acrescimo_especial DECIMAL(10,2) DEFAULT 0.00 AFTER observacoes");
        
        echo "✅ Coluna 'acrescimo_especial' adicionada com sucesso!<br>";
    } else {
        echo "✅ Coluna 'acrescimo_especial' já existe!<br>";
    }
    
    // Verificar outras colunas importantes
    $colunas_necessarias = [
        'tempo_estimado_minutos' => "ALTER TABLE agendamentos ADD COLUMN tempo_estimado_minutos INT DEFAULT 60 AFTER endereco",
        'status' => "ALTER TABLE agendamentos ADD COLUMN status ENUM('agendado', 'confirmado', 'realizado', 'cancelado') DEFAULT 'agendado' AFTER acrescimo_especial"
    ];
    
    foreach($colunas_necessarias as $coluna => $sql) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM agendamentos LIKE ?");
        $stmt->execute([$coluna]);
        $existe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$existe) {
            echo "❌ Coluna '{$coluna}' não encontrada. Adicionando...<br>";
            $pdo->exec($sql);
            echo "✅ Coluna '{$coluna}' adicionada com sucesso!<br>";
        } else {
            echo "✅ Coluna '{$coluna}' já existe!<br>";
        }
    }
    
    echo "<h3>🎉 Correção concluída com sucesso!</h3>";
    echo "<p>Agora você pode <a href='agendamento.php'>testar o agendamento</a>.</p>";
    
} catch(PDOException $e) {
    echo "❌ Erro na correção: " . $e->getMessage();
    echo "<br><br>Detalhes técnicos: " . $e->getMessage();
}
?>