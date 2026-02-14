<?php
// verificar_imagens.php
include 'includes/config.php';

echo "<h2>Verificação de Imagens</h2>";

// Verificar pasta de uploads
$pasta_uploads = 'uploads/produtos/';
echo "<h3>Pasta: $pasta_uploads</h3>";
echo "Existe: " . (file_exists($pasta_uploads) ? '✅ SIM' : '❌ NÃO') . "<br>";
echo "É legível: " . (is_readable($pasta_uploads) ? '✅ SIM' : '❌ NÃO') . "<br>";

if (file_exists($pasta_uploads)) {
    $arquivos = scandir($pasta_uploads);
    echo "<h4>Arquivos na pasta:</h4>";
    foreach($arquivos as $arquivo) {
        if($arquivo != '.' && $arquivo != '..') {
            $caminho_completo = $pasta_uploads . $arquivo;
            echo "- $arquivo (tamanho: " . filesize($caminho_completo) . " bytes)<br>";
        }
    }
} else {
    echo "<p style='color: red;'>A pasta não existe!</p>";
}

// Verificar imagens no banco de dados
echo "<h3>Imagens no Banco de Dados:</h3>";
$stmt = $pdo->prepare("SELECT id, nome, imagem FROM produtos WHERE imagem IS NOT NULL");
$stmt->execute();
$produtos_com_imagem = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($produtos_com_imagem) > 0) {
    foreach($produtos_com_imagem as $produto) {
        $caminho_imagem = $pasta_uploads . $produto['imagem'];
        $existe_na_pasta = file_exists($caminho_imagem);
        
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>Produto:</strong> " . $produto['nome'] . " (ID: " . $produto['id'] . ")<br>";
        echo "<strong>Imagem no BD:</strong> " . $produto['imagem'] . "<br>";
        echo "<strong>Na pasta:</strong> " . ($existe_na_pasta ? '✅ SIM' : '❌ NÃO') . "<br>";
        
        if($existe_na_pasta) {
            echo "<img src='$caminho_imagem' style='max-width: 200px; margin-top: 10px;'><br>";
        }
        
        echo "</div>";
    }
} else {
    echo "<p>Nenhum produto com imagem no banco de dados.</p>";
}
?>

<img src="image.php?img=<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">