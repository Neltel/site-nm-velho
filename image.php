<?php
// image.php - Servir imagens com verificação de segurança
if(isset($_GET['img'])) {
    $image_path = 'uploads/produtos/' . basename($_GET['img']);
    
    // Verificar se o arquivo existe e é uma imagem
    if(file_exists($image_path) && in_array(strtolower(pathinfo($image_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        
        // Determinar o tipo MIME
        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        $ext = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
        $mime = $mime_types[$ext] ?? 'image/jpeg';
        
        // Enviar cabeçalhos
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($image_path));
        
        // Ler e enviar a imagem
        readfile($image_path);
        exit;
    }
}

// Se chegou aqui, imagem não encontrada
header('HTTP/1.0 404 Not Found');
echo 'Imagem não encontrada';
?>