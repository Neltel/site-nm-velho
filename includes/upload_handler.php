<?php
/**
 * includes/upload_handler.php
 * 
 * Classe para gerenciamento seguro de uploads de arquivos
 * Implementa validações de segurança contra:
 * - Tipos de arquivo não permitidos
 * - Tamanho excessivo de arquivo
 * - Nomes de arquivo maliciosos
 * - Upload de scripts executáveis
 */

class UploadHandler {
    
    /**
     * Tipos MIME permitidos por categoria
     */
    private static $tiposPermitidos = [
        'imagem' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'documento' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ],
        'todos' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ]
    ];
    
    /**
     * Extensões permitidas por categoria
     */
    private static $extensoesPermitidas = [
        'imagem' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'documento' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
        'todos' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']
    ];
    
    /**
     * Tamanho máximo padrão (5MB)
     */
    private static $tamanhoMaximo = 5242880; // 5MB em bytes
    
    /**
     * Faz upload de um arquivo
     * 
     * @param array $arquivo Array do arquivo ($_FILES['campo'])
     * @param string $destino Pasta de destino (ex: 'clientes', 'produtos')
     * @param string $categoria Categoria de arquivo ('imagem', 'documento', 'todos')
     * @param int $tamanhoMax Tamanho máximo em bytes (opcional)
     * @return array ['sucesso' => bool, 'mensagem' => string, 'arquivo' => string]
     */
    public static function upload($arquivo, $destino, $categoria = 'todos', $tamanhoMax = null) {
        // Validar se arquivo foi enviado
        if (!isset($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'sucesso' => false,
                'mensagem' => 'Nenhum arquivo foi enviado.',
                'arquivo' => null
            ];
        }
        
        // Verificar erros no upload
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'sucesso' => false,
                'mensagem' => self::getErroUpload($arquivo['error']),
                'arquivo' => null
            ];
        }
        
        // Definir tamanho máximo
        $tamanhoMax = $tamanhoMax ?? self::$tamanhoMaximo;
        
        // Validar tamanho do arquivo
        if ($arquivo['size'] > $tamanhoMax) {
            $tamanhoMaxMB = round($tamanhoMax / 1048576, 2);
            return [
                'sucesso' => false,
                'mensagem' => "Arquivo muito grande. Tamanho máximo: {$tamanhoMaxMB}MB",
                'arquivo' => null
            ];
        }
        
        // Validar tipo MIME
        $tiposPermitidos = self::$tiposPermitidos[$categoria] ?? self::$tiposPermitidos['todos'];
        if (!in_array($arquivo['type'], $tiposPermitidos)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Tipo de arquivo não permitido.',
                'arquivo' => null
            ];
        }
        
        // Obter extensão do arquivo
        $nomeOriginal = $arquivo['name'];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        
        // Validar extensão
        $extensoesPermitidas = self::$extensoesPermitidas[$categoria] ?? self::$extensoesPermitidas['todos'];
        if (!in_array($extensao, $extensoesPermitidas)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Extensão de arquivo não permitida.',
                'arquivo' => null
            ];
        }
        
        // Gerar nome único para o arquivo
        $nomeArquivo = self::gerarNomeUnico($extensao);
        
        // Criar pasta de destino se não existir
        $pastaDestino = __DIR__ . '/../uploads/' . $destino;
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }
        
        // Caminho completo do arquivo
        $caminhoCompleto = $pastaDestino . '/' . $nomeArquivo;
        
        // Mover arquivo para destino
        if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
            // Definir permissões seguras
            chmod($caminhoCompleto, 0644);
            
            // Registrar log
            if (function_exists('registrarLog')) {
                registrarLog('info', 'Upload de arquivo realizado', [
                    'arquivo' => $nomeArquivo,
                    'destino' => $destino,
                    'tamanho' => $arquivo['size']
                ]);
            }
            
            return [
                'sucesso' => true,
                'mensagem' => 'Arquivo enviado com sucesso!',
                'arquivo' => $nomeArquivo,
                'caminho' => 'uploads/' . $destino . '/' . $nomeArquivo,
                'tamanho' => $arquivo['size'],
                'tipo' => $arquivo['type'],
                'nome_original' => $nomeOriginal
            ];
        } else {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao salvar o arquivo no servidor.',
                'arquivo' => null
            ];
        }
    }
    
    /**
     * Faz upload de múltiplos arquivos
     * 
     * @param array $arquivos Array de arquivos ($_FILES['campo'])
     * @param string $destino Pasta de destino
     * @param string $categoria Categoria de arquivo
     * @param int $tamanhoMax Tamanho máximo em bytes (opcional)
     * @return array Array de resultados
     */
    public static function uploadMultiplo($arquivos, $destino, $categoria = 'todos', $tamanhoMax = null) {
        $resultados = [];
        
        // Reorganizar array se necessário (quando multiple="multiple")
        if (isset($arquivos['name']) && is_array($arquivos['name'])) {
            $count = count($arquivos['name']);
            for ($i = 0; $i < $count; $i++) {
                $arquivo = [
                    'name' => $arquivos['name'][$i],
                    'type' => $arquivos['type'][$i],
                    'tmp_name' => $arquivos['tmp_name'][$i],
                    'error' => $arquivos['error'][$i],
                    'size' => $arquivos['size'][$i]
                ];
                
                $resultados[] = self::upload($arquivo, $destino, $categoria, $tamanhoMax);
            }
        }
        
        return $resultados;
    }
    
    /**
     * Exclui um arquivo
     * 
     * @param string $caminho Caminho do arquivo relativo à raiz do projeto
     * @return bool True se excluído com sucesso
     */
    public static function excluir($caminho) {
        $caminhoCompleto = __DIR__ . '/../' . $caminho;
        
        if (file_exists($caminhoCompleto)) {
            if (unlink($caminhoCompleto)) {
                // Registrar log
                if (function_exists('registrarLog')) {
                    registrarLog('info', 'Arquivo excluído', ['caminho' => $caminho]);
                }
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Gera nome único para arquivo
     * 
     * @param string $extensao Extensão do arquivo
     * @return string Nome único
     */
    private static function gerarNomeUnico($extensao) {
        return date('YmdHis') . '_' . uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extensao;
    }
    
    /**
     * Retorna mensagem de erro baseada no código de erro do PHP
     * 
     * @param int $codigo Código de erro
     * @return string Mensagem de erro
     */
    private static function getErroUpload($codigo) {
        switch ($codigo) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'O arquivo excede o tamanho máximo permitido.';
            case UPLOAD_ERR_PARTIAL:
                return 'O arquivo foi enviado parcialmente.';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo foi enviado.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporária não encontrada.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever arquivo no disco.';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bloqueado por extensão do PHP.';
            default:
                return 'Erro desconhecido no upload.';
        }
    }
    
    /**
     * Valida se é uma imagem real (verificação de conteúdo)
     * 
     * @param string $caminho Caminho do arquivo
     * @return bool True se for uma imagem válida
     */
    public static function validarImagem($caminho) {
        $info = @getimagesize($caminho);
        return $info !== false;
    }
    
    /**
     * Obtém informações sobre tipos permitidos
     * 
     * @param string $categoria Categoria
     * @return array Informações sobre tipos permitidos
     */
    public static function getTiposPermitidos($categoria = 'todos') {
        return [
            'tipos_mime' => self::$tiposPermitidos[$categoria] ?? [],
            'extensoes' => self::$extensoesPermitidas[$categoria] ?? [],
            'tamanho_maximo' => self::$tamanhoMaximo,
            'tamanho_maximo_mb' => round(self::$tamanhoMaximo / 1048576, 2)
        ];
    }
}
?>
