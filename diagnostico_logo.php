<?php
include 'includes/config.php';

echo "<h2>🔍 Diagnóstico da Logo</h2>";

// Verificar no banco
$stmt = $pdo->prepare("SELECT * FROM config_site WHERE chave = 'site_logo'");
$stmt->execute();
$logo_db = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>1. Banco de Dados:</h3>";
if($logo_db) {
    echo "✅ Logo no BD: <strong>" . $logo_db['valor'] . "</strong><br>";
} else {
    echo "❌ Logo não encontrada no BD<br>";
}

// Verificar arquivos
echo "<h3>2. Verificação de Arquivos:</h3>";
$caminhos_verificar = [
    '../assets/images/' . $logo_db['valor'],
    'assets/images/' . $logo_db['valor'],
    './assets/images/' . $logo_db['valor']
];

foreach($caminhos_verificar as $caminho) {
    $existe = file_exists($caminho) ? '✅' : '❌';
    echo "$existe $caminho<br>";
    
    if(file_exists($caminho)) {
        echo "&nbsp;&nbsp;📏 Tamanho: " . filesize($caminho) . " bytes<br>";
        echo "&nbsp;&nbsp;🖼️ Preview: <img src='$caminho' style='max-width: 200px; border: 1px solid #ccc;'><br><br>";
    }
}

// Listar todos os arquivos na pasta
echo "<h3>3. Todos os arquivos em assets/images/:</h3>";
$pasta = 'assets/images/';
if(file_exists($pasta)) {
    $arquivos = scandir($pasta);
    foreach($arquivos as $arquivo) {
        if($arquivo != '.' && $arquivo != '..') {
            $caminho_completo = $pasta . $arquivo;
            $existe = file_exists($caminho_completo) ? '✅' : '❌';
            $is_logo = ($arquivo == $logo_db['valor']) ? '🎯 <strong>' . $arquivo . '</strong>' : $arquivo;
            echo "$existe $is_logo - " . filesize($caminho_completo) . " bytes<br>";
        }
    }
} else {
    echo "❌ Pasta $pasta não existe!<br>";
}
?>