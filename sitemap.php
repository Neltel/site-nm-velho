<?php
// sitemap.php
include 'includes/config.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

// Páginas estáticas
$paginas = [
    '' => '2024-01-15', // Página inicial
    'servicos.php' => '2024-01-15',
    'produtos.php' => '2024-01-15',
    'orcamento.php' => '2024-01-15',
    'agendamento.php' => '2024-01-15',
    'contato.php' => '2024-01-15'
];

foreach($paginas as $pagina => $data) {
    $url = "https://www.climatech.com.br/" . $pagina;
    echo "<url>";
    echo "<loc>" . htmlspecialchars($url) . "</loc>";
    echo "<lastmod>" . $data . "</lastmod>";
    echo "<changefreq>weekly</changefreq>";
    echo "<priority>" . ($pagina == '' ? '1.0' : '0.8') . "</priority>";
    echo "</url>";
}

// Serviços (do banco de dados)
try {
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($servicos as $servico) {
        $url = "https://www.climatech.com.br/servico.php?id=" . $servico['id'];
        echo "<url>";
        echo "<loc>" . htmlspecialchars($url) . "</loc>";
        echo "<lastmod>2024-01-15</lastmod>";
        echo "<changefreq>monthly</changefreq>";
        echo "<priority>0.7</priority>";
        echo "</url>";
    }
} catch(PDOException $e) {
    // Não adiciona serviços se houver erro
}

// Produtos (do banco de dados)
try {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ativo = 1");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($produtos as $produto) {
        $url = "https://www.climatech.com.br/produto.php?id=" . $produto['id'];
        echo "<url>";
        echo "<loc>" . htmlspecialchars($url) . "</loc>";
        echo "<lastmod>2024-01-15</lastmod>";
        echo "<changefreq>monthly</changefreq>";
        echo "<priority>0.7</priority>";
        echo "</url>";
    }
} catch(PDOException $e) {
    // Não adiciona produtos se houver erro
}

echo '</urlset>';
?>