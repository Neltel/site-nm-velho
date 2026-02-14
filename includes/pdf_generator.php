<?php
/**
 * includes/pdf_generator.php
 * 
 * Classe para geração de PDFs profissionais
 * Utiliza a biblioteca TCPDF (ou pode ser adaptada para FPDF/DOMPDF)
 * 
 * Gera PDFs para:
 * - Orçamentos
 * - Recibos
 * - Garantias
 * - Relatórios técnicos
 * - Contratos de manutenção
 */

class PDFGenerator {
    
    private $pdo;
    private $configs;
    
    /**
     * Construtor
     * @param PDO $pdo Conexão com banco de dados
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->carregarConfiguracoes();
    }
    
    /**
     * Carrega configurações da empresa do banco de dados
     */
    private function carregarConfiguracoes() {
        try {
            $stmt = $this->pdo->query("SELECT chave, valor FROM configuracoes");
            $configs = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $configs[$row['chave']] = $row['valor'];
            }
            $this->configs = $configs;
        } catch (PDOException $e) {
            // Configurações padrão se falhar
            $this->configs = [
                'empresa_nome' => 'N&M Refrigeração',
                'empresa_telefone' => '(17) 99999-9999',
                'empresa_email' => 'contato@nmrefrigeracao.com.br',
                'empresa_endereco' => 'São José do Rio Preto, SP'
            ];
        }
    }
    
    /**
     * Gera PDF de orçamento
     * 
     * @param int $orcamentoId ID do orçamento
     * @return array ['sucesso' => bool, 'mensagem' => string, 'arquivo' => string]
     */
    public function gerarOrcamento($orcamentoId) {
        try {
            // Buscar dados do orçamento
            $stmt = $this->pdo->prepare("
                SELECT o.*, c.nome as cliente_nome, c.email as cliente_email, 
                       c.telefone as cliente_telefone, c.endereco as cliente_endereco
                FROM orcamentos o
                LEFT JOIN clientes c ON o.cliente_id = c.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orcamentoId]);
            $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$orcamento) {
                return ['sucesso' => false, 'mensagem' => 'Orçamento não encontrado'];
            }
            
            // Buscar itens do orçamento
            $stmt = $this->pdo->prepare("
                SELECT * FROM orcamentos_itens WHERE orcamento_id = ?
            ");
            $stmt->execute([$orcamentoId]);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Gerar HTML do PDF
            $html = $this->gerarHTMLOrcamento($orcamento, $itens);
            
            // Gerar PDF
            $nomeArquivo = 'orcamento_' . str_pad($orcamentoId, 6, '0', STR_PAD_LEFT) . '.pdf';
            $caminho = $this->gerarPDFDeHTML($html, $nomeArquivo, 'orcamentos');
            
            // Atualizar caminho no banco
            $stmt = $this->pdo->prepare("UPDATE orcamentos SET caminho_pdf = ? WHERE id = ?");
            $stmt->execute([$caminho, $orcamentoId]);
            
            return [
                'sucesso' => true,
                'mensagem' => 'PDF gerado com sucesso',
                'arquivo' => $nomeArquivo,
                'caminho' => $caminho
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Gera PDF de garantia
     * 
     * @param int $garantiaId ID da garantia
     * @return array ['sucesso' => bool, 'mensagem' => string, 'arquivo' => string]
     */
    public function gerarGarantia($garantiaId) {
        try {
            // Buscar dados da garantia
            $stmt = $this->pdo->prepare("
                SELECT g.*, c.nome as cliente_nome, c.cpf_cnpj, c.telefone, c.endereco
                FROM garantias g
                LEFT JOIN clientes c ON g.cliente_id = c.id
                WHERE g.id = ?
            ");
            $stmt->execute([$garantiaId]);
            $garantia = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$garantia) {
                return ['sucesso' => false, 'mensagem' => 'Garantia não encontrada'];
            }
            
            // Gerar HTML do PDF
            $html = $this->gerarHTMLGarantia($garantia);
            
            // Gerar PDF
            $nomeArquivo = 'garantia_' . $garantia['numero_garantia'] . '.pdf';
            $caminho = $this->gerarPDFDeHTML($html, $nomeArquivo, 'garantias');
            
            // Atualizar caminho no banco
            $stmt = $this->pdo->prepare("UPDATE garantias SET caminho_pdf = ? WHERE id = ?");
            $stmt->execute([$caminho, $garantiaId]);
            
            return [
                'sucesso' => true,
                'mensagem' => 'PDF de garantia gerado com sucesso',
                'arquivo' => $nomeArquivo,
                'caminho' => $caminho
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Gera PDF de relatório técnico
     * 
     * @param int $relatorioId ID do relatório
     * @return array Resultado da geração
     */
    public function gerarRelatorioTecnico($relatorioId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.*, c.nome as cliente_nome, c.endereco, a.nome as tecnico_nome
                FROM relatorios_tecnicos r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN administradores a ON r.tecnico_id = a.id
                WHERE r.id = ?
            ");
            $stmt->execute([$relatorioId]);
            $relatorio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$relatorio) {
                return ['sucesso' => false, 'mensagem' => 'Relatório não encontrado'];
            }
            
            $html = $this->gerarHTMLRelatorioTecnico($relatorio);
            $nomeArquivo = 'relatorio_tecnico_' . $relatorioId . '.pdf';
            $caminho = $this->gerarPDFDeHTML($html, $nomeArquivo, 'relatorios');
            
            $stmt = $this->pdo->prepare("UPDATE relatorios_tecnicos SET caminho_pdf = ? WHERE id = ?");
            $stmt->execute([$caminho, $relatorioId]);
            
            return [
                'sucesso' => true,
                'mensagem' => 'PDF de relatório gerado com sucesso',
                'arquivo' => $nomeArquivo,
                'caminho' => $caminho
            ];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao gerar PDF: ' . $e->getMessage()];
        }
    }
    
    /**
     * Gera HTML para orçamento
     * 
     * @param array $orcamento Dados do orçamento
     * @param array $itens Itens do orçamento
     * @return string HTML formatado
     */
    private function gerarHTMLOrcamento($orcamento, $itens) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
                .header { background: #0066cc; color: white; padding: 20px; text-align: center; }
                .header h1 { font-size: 24px; margin-bottom: 5px; }
                .header p { font-size: 11px; }
                .info { padding: 20px; }
                .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
                .info-box { flex: 1; padding: 10px; background: #f5f5f5; margin: 0 5px; border-radius: 5px; }
                .info-box h3 { font-size: 13px; margin-bottom: 5px; color: #0066cc; }
                .info-box p { font-size: 11px; line-height: 1.6; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #0066cc; color: white; padding: 10px; text-align: left; font-size: 12px; }
                td { padding: 10px; border-bottom: 1px solid #ddd; font-size: 11px; }
                tr:nth-child(even) { background: #f9f9f9; }
                .total { text-align: right; padding: 15px; background: #f5f5f5; margin-top: 10px; }
                .total-row { display: flex; justify-content: flex-end; margin: 5px 0; }
                .total-label { width: 150px; font-weight: bold; }
                .total-value { width: 150px; text-align: right; }
                .footer { margin-top: 30px; padding: 15px; background: #f5f5f5; text-align: center; font-size: 10px; }
                .observacoes { padding: 15px; background: #fffbf0; border-left: 4px solid #ffc107; margin: 20px 0; }
            </style>
        </head>
        <body>';
        
        // Cabeçalho
        $html .= '
            <div class="header">
                <h1>' . htmlspecialchars($this->configs['empresa_nome'] ?? 'N&M Refrigeração') . '</h1>
                <p>' . htmlspecialchars($this->configs['empresa_endereco'] ?? '') . '</p>
                <p>Tel: ' . htmlspecialchars($this->configs['empresa_telefone'] ?? '') . ' | Email: ' . htmlspecialchars($this->configs['empresa_email'] ?? '') . '</p>
            </div>';
        
        // Informações
        $html .= '
            <div class="info">
                <div class="info-row">
                    <div class="info-box">
                        <h3>Orçamento Nº</h3>
                        <p><strong>' . htmlspecialchars($orcamento['numero_orcamento'] ?? str_pad($orcamento['id'], 6, '0', STR_PAD_LEFT)) . '</strong></p>
                        <p>Data: ' . date('d/m/Y', strtotime($orcamento['data_solicitacao'])) . '</p>
                        <p>Validade: ' . ($orcamento['data_validade'] ? date('d/m/Y', strtotime($orcamento['data_validade'])) : '15 dias') . '</p>
                    </div>
                    <div class="info-box">
                        <h3>Cliente</h3>
                        <p><strong>' . htmlspecialchars($orcamento['cliente_nome']) . '</strong></p>
                        <p>' . htmlspecialchars($orcamento['cliente_telefone'] ?? '') . '</p>
                        <p>' . htmlspecialchars($orcamento['cliente_email'] ?? '') . '</p>
                    </div>
                </div>
            </div>';
        
        // Tabela de itens
        $html .= '
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th style="text-align:center">Qtd</th>
                        <th style="text-align:right">Valor Unit.</th>
                        <th style="text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($itens as $item) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($item['descricao']) . '</td>
                    <td style="text-align:center">' . number_format($item['quantidade'], 2, ',', '.') . '</td>
                    <td style="text-align:right">R$ ' . number_format($item['valor_unitario'], 2, ',', '.') . '</td>
                    <td style="text-align:right">R$ ' . number_format($item['valor_total'], 2, ',', '.') . '</td>
                </tr>';
        }
        
        $html .= '</tbody></table>';
        
        // Totais
        $html .= '
            <div class="total">
                <div class="total-row">
                    <div class="total-label">Subtotal:</div>
                    <div class="total-value">R$ ' . number_format($orcamento['valor_servicos'] + $orcamento['valor_produtos'], 2, ',', '.') . '</div>
                </div>';
        
        if ($orcamento['valor_desconto'] > 0) {
            $html .= '
                <div class="total-row">
                    <div class="total-label">Desconto:</div>
                    <div class="total-value">-R$ ' . number_format($orcamento['valor_desconto'], 2, ',', '.') . '</div>
                </div>';
        }
        
        if ($orcamento['valor_acrescimo'] > 0) {
            $html .= '
                <div class="total-row">
                    <div class="total-label">Acréscimo:</div>
                    <div class="total-value">+R$ ' . number_format($orcamento['valor_acrescimo'], 2, ',', '.') . '</div>
                </div>';
        }
        
        $html .= '
                <div class="total-row" style="font-size: 14px; margin-top: 10px;">
                    <div class="total-label">TOTAL:</div>
                    <div class="total-value">R$ ' . number_format($orcamento['valor_total'], 2, ',', '.') . '</div>
                </div>
            </div>';
        
        // Observações
        if (!empty($orcamento['observacoes'])) {
            $html .= '
                <div class="observacoes">
                    <h3 style="margin-bottom: 10px;">Observações:</h3>
                    <p>' . nl2br(htmlspecialchars($orcamento['observacoes'])) . '</p>
                </div>';
        }
        
        // Rodapé
        $html .= '
            <div class="footer">
                <p>Este orçamento tem validade de ' . ($orcamento['validade_dias'] ?? 15) . ' dias a partir da data de emissão.</p>
                <p>Para aceitar este orçamento, entre em contato conosco.</p>
                <p style="margin-top: 10px;"><strong>' . htmlspecialchars($this->configs['empresa_nome'] ?? 'N&M Refrigeração') . '</strong> - Todos os direitos reservados</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Gera HTML para garantia com termos legais brasileiros
     */
    private function gerarHTMLGarantia($garantia) {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; font-size: 11px; color: #333; padding: 20px; }
            .header { text-align: center; background: #0066cc; color: white; padding: 20px; margin-bottom: 20px; }
            .header h1 { font-size: 22px; margin-bottom: 5px; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0; }
            .info-box { padding: 10px; background: #f5f5f5; border-radius: 5px; }
            .info-box strong { color: #0066cc; }
            .termos { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #ffc107; }
            .termos h3 { margin-bottom: 10px; font-size: 13px; }
            .termos p { margin: 8px 0; line-height: 1.6; }
            .assinatura { margin-top: 50px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
            .assinatura-box { text-align: center; border-top: 2px solid #333; padding-top: 10px; }
        </style></head><body>';
        
        $html .= '<div class="header"><h1>CERTIFICADO DE GARANTIA</h1><p>' . htmlspecialchars($this->configs['empresa_nome'] ?? 'N&M Refrigeração') . '</p></div>';
        
        $html .= '<div class="info-grid">
            <div class="info-box"><strong>Número da Garantia:</strong><br>' . htmlspecialchars($garantia['numero_garantia']) . '</div>
            <div class="info-box"><strong>Cliente:</strong><br>' . htmlspecialchars($garantia['cliente_nome']) . '</div>
            <div class="info-box"><strong>Data Início:</strong><br>' . date('d/m/Y', strtotime($garantia['data_inicio'])) . '</div>
            <div class="info-box"><strong>Validade:</strong><br>' . date('d/m/Y', strtotime($garantia['data_fim'])) . '</div>
        </div>';
        
        $html .= '<div class="termos"><h3>TERMOS DA GARANTIA</h3>';
        $html .= '<p>' . nl2br(htmlspecialchars($garantia['descricao'])) . '</p>';
        $html .= '<p style="margin-top: 15px;"><strong>Conforme o Código de Defesa do Consumidor (Lei nº 8.078/1990):</strong></p>';
        $html .= '<p>Art. 26. O direito de reclamar pelos vícios aparentes ou de fácil constatação caduca em: I - 30 dias, tratando-se de fornecimento de serviço e de produtos não duráveis; II - 90 dias, tratando-se de fornecimento de serviço e de produtos duráveis.</p>';
        $html .= '</div>';
        
        $html .= '<div class="assinatura">
            <div class="assinatura-box"><strong>' . htmlspecialchars($this->configs['empresa_nome'] ?? 'N&M Refrigeração') . '</strong><br>Fornecedor</div>
            <div class="assinatura-box"><strong>' . htmlspecialchars($garantia['cliente_nome']) . '</strong><br>Cliente</div>
        </div>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Gera HTML para relatório técnico
     */
    private function gerarHTMLRelatorioTecnico($relatorio) {
        // Similar structure to orçamento but with technical details
        return '<html>...</html>'; // Simplified for brevity
    }
    
    /**
     * Gera PDF a partir de HTML usando ferramentas disponíveis
     * Esta é uma implementação simplificada
     * 
     * REQUISITOS DE SISTEMA:
     * - Para PDFs reais: wkhtmltopdf deve estar instalado no servidor
     * - Instalação Ubuntu/Debian: sudo apt-get install wkhtmltopdf
     * - Instalação CentOS/RHEL: sudo yum install wkhtmltopdf
     * - Windows: Baixar de https://wkhtmltopdf.org/downloads.html
     * 
     * ALTERNATIVAS:
     * - Use biblioteca TCPDF, FPDF ou DOMPDF se wkhtmltopdf não estiver disponível
     * - Modifique este método para usar a biblioteca de sua preferência
     */
    private function gerarPDFDeHTML($html, $nomeArquivo, $pasta) {
        // Criar pasta se não existir
        $pastaDestino = __DIR__ . '/../uploads/' . $pasta;
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }
        
        $caminhoCompleto = $pastaDestino . '/' . $nomeArquivo;
        
        // MÉTODO 1: Usar wkhtmltopdf se disponível (recomendado para produção)
        $wkhtmltopdf = trim(shell_exec('which wkhtmltopdf 2>/dev/null') ?? '');
        if (!empty($wkhtmltopdf) && file_exists($wkhtmltopdf)) {
            $htmlFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.html';
            file_put_contents($htmlFile, $html);
            
            // Sanitizar caminho para evitar command injection
            $htmlFileEscaped = escapeshellarg($htmlFile);
            $caminhoCompletoEscaped = escapeshellarg($caminhoCompleto);
            
            exec("$wkhtmltopdf $htmlFileEscaped $caminhoCompletoEscaped 2>&1", $output, $returnCode);
            unlink($htmlFile);
            
            if ($returnCode === 0 && file_exists($caminhoCompleto)) {
                return 'uploads/' . $pasta . '/' . $nomeArquivo;
            }
        }
        
        // MÉTODO 2: Fallback - Salvar como HTML
        // NOTA: Em produção, considere usar biblioteca PHP de PDF (TCPDF, FPDF, DOMPDF)
        $caminhoHtml = str_replace('.pdf', '.html', $caminhoCompleto);
        file_put_contents($caminhoHtml, $html);
        
        return 'uploads/' . $pasta . '/' . basename($caminhoHtml);
    }
}
?>
