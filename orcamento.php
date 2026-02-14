<?php
// orcamento.php - VERSÃO CORRIGIDA

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuração
include_once 'includes/config.php';

// Verificar se a função existe, se não, definir uma função básica
if (!function_exists('enviarWhatsAppOrcamento')) {
    function enviarWhatsAppOrcamento($pdo, $dados_orcamento) {
        // Buscar número do WhatsApp das configurações
        $whatsapp = '5517996240725'; // Número padrão

        // Preparar mensagem usando concatenação (\n) para garantir a codificação correta
        $mensagem = "💰 *SOLICITAÇÃO DE ORÇAMENTO*\n\n"
                  . "👤 *DADOS DO CLIENTE*\n"
                  . "• *Nome:* {$dados_orcamento['nome']}\n"
                  . "• *Telefone:* {$dados_orcamento['telefone']}\n"
                  . "• *E-mail:* {$dados_orcamento['email']}\n\n"
                  
                  // NOVO BLOCO DE ENDEREÇO
                  . "*ENDEREÇO*\n"
                  . "• *Rua:* {$dados_orcamento['rua']}\n"
                  . "• *Nº:* {$dados_orcamento['numero']}\n"
                  . "• *Bairro:* {$dados_orcamento['bairro']}\n"
                  . "• *Cidade:* {$dados_orcamento['cidade']}\n\n"
                  
                  . "⚙️ *INFORMAÇÕES DO EQUIPAMENTO*\n"
                  . "• *Marca:* {$dados_orcamento['marca']}\n"
                  . "• *Capacidade:* {$dados_orcamento['btus']}\n\n"

                  . "📝 *OBSERVAÇÕES*\n"
                  . "{$dados_orcamento['observacoes']}\n\n"

                  . "⏰ *Solicitado em:* " . date('d/m/Y \à\s H:i');
        
        // Codificar mensagem para URL
        $mensagem_encoded = urlencode($mensagem);
        
        // Gerar link do WhatsApp
        return "https://wa.me/{$whatsapp}?text={$mensagem_encoded}";
    }
}

// Inicializar variáveis
$erro = '';
$sucesso = '';
$whatsapp_auto = '';

// Processar formulário de orçamento
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = sanitize($_POST['nome'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $telefone = sanitize($_POST['telefone'] ?? '');
    // NOVAS VARIÁVEIS DE ENDEREÇO
    $rua = sanitize($_POST['rua'] ?? '');
    $numero = sanitize($_POST['numero'] ?? '');
    $bairro = sanitize($_POST['bairro'] ?? '');
    $cidade = sanitize($_POST['cidade'] ?? '');
    // FIM NOVAS VARIÁVEIS
    $marca = sanitize($_POST['marca'] ?? '');
    $btus = $_POST['btus'] ?? '';
    $observacoes = sanitize($_POST['observacoes'] ?? '');
    
    // Validações básicas
    if(empty($nome) || empty($email) || empty($telefone)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        try {
            // Verificar se cliente já existe (por telefone)
            $stmt = $pdo->prepare("SELECT id FROM clientes WHERE telefone = ?");
            $stmt->execute([$telefone]);
            $cliente_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($cliente_existente) {
                $cliente_id = $cliente_existente['id'];
                // Atualizar dados do cliente
                $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, email = ?, rua = ?, numero = ?, bairro = ?, cidade = ? WHERE id = ?");
$stmt->execute([$nome, $email, $rua, $numero, $bairro, $cidade, $cliente_id]);
            } else {
                // Inserir novo cliente
                $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone, rua, numero, bairro, cidade) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$nome, $email, $telefone, $rua, $numero, $bairro, $cidade]);
$cliente_id = $pdo->lastInsertId();
            }
            
            // Inserir orçamento
            $stmt = $pdo->prepare("INSERT INTO orcamentos (cliente_id,  equipamento_marca, equipamento_btus, descricao) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $marca, $btus, $observacoes]);
            $orcamento_id = $pdo->lastInsertId();
            
            // Preparar dados para WhatsApp
            $dados_orcamento = [
                'nome' => $nome,
                'telefone' => $telefone,
                'email' => $email,
                // NOVOS CAMPOS
                'rua' => $rua ? $rua : 'Não informada',
                'numero' => $numero ? $numero : 'S/N',
                'bairro' => $bairro ? $bairro : 'Não informado',
                'cidade' => $cidade ? $cidade : 'Não informada',
                // FIM NOVOS CAMPOS
                'marca' => $marca ? $marca : 'Não informada',
                'btus' => $btus ? $btus . ' BTUs' : 'Não informado',
                'observacoes' => $observacoes ? $observacoes : 'Nenhuma observação'
            ];
            
            // Gerar link do WhatsApp
            $link_whatsapp = enviarWhatsAppOrcamento($pdo, $dados_orcamento);
            
            $sucesso = "Orçamento solicitado com sucesso! Entraremos em contato em breve.";
            
            // Adicionar link do WhatsApp
            $sucesso .= "<br><br><a href='{$link_whatsapp}' target='_blank' class='btn btn-success' id='whatsappLink'>💬 Falar no WhatsApp Agora</a>";
            
            // JavaScript para abrir WhatsApp automaticamente
            $whatsapp_auto = "
            <script>
            setTimeout(function() {
                window.open('{$link_whatsapp}', '_blank');
                const link = document.getElementById('whatsappLink');
                if(link) link.click();
            }, 1000);
            </script>
            ";
            
        } catch(PDOException $e) {
            error_log("Erro no orçamento: " . $e->getMessage());
            $erro = "Erro ao processar orçamento. Por favor, tente novamente.";
        }
    }
}

// Incluir header
include 'includes/header.php';
?>

<section class="section-title">
    <div class="container">
        <h2>Solicitar Orçamento</h2>
        <p>Preencha as informações abaixo para receber um orçamento detalhado</p>
    </div>
</section>

<section>
    <div class="container">
        <?php if($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if($sucesso): ?>
        <div class="alert alert-success">
            <?php echo $sucesso; ?>
        </div>
        <?php echo $whatsapp_auto; ?>
        <?php endif; ?>
        
        <?php if(!$sucesso): ?>
        <form method="POST" class="booking-form" id="formOrcamento">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone *</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control" required value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-row">
    <div class="form-group" style="flex: 2;">
        <label for="rua">Rua</label>
        <input type="text" id="rua" name="rua" class="form-control" value="<?php echo htmlspecialchars($_POST['rua'] ?? ''); ?>">
    </div>
    <div class="form-group" style="flex: 1;">
        <label for="numero">Número</label>
        <input type="text" id="numero" name="numero" class="form-control" value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="bairro">Bairro</label>
        <input type="text" id="bairro" name="bairro" class="form-control" value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="cidade">Cidade</label>
        <input type="text" id="cidade" name="cidade" class="form-control" value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>">
    </div>
</div>
            <!--<div class="form-group">
                <label for="servico_id">Serviço Desejado *</label>
                <select id="servico_id" name="servico_id" class="form-control" required>
                    <option value="">Selecione um serviço</option>
                    <?php
                    try {
                        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE ativo = 1");
                        $stmt->execute();
                        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach($servicos as $servico):
                            $selected = ($_POST['servico_id'] ?? '') == $servico['id'] ? 'selected' : '';
                    ?>
                    <option value="<?php echo $servico['id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($servico['nome']); ?>
                    </option>
                    <?php endforeach;
                    } catch(PDOException $e) {
                        echo '<option value="">Erro ao carregar serviços</option>';
                    }
                    ?>
                </select>
            </div>-->
            
            <div class="form-row">
                <div class="form-group">
                    <label for="marca">Marca do Equipamento</label>
                    <select id="marca" name="marca" class="form-control">
                        <option value="">Selecione a marca</option>
                        <option value="Samsung" <?php echo ($_POST['marca'] ?? '') == 'Samsung' ? 'selected' : ''; ?>>Samsung</option>
                        <option value="LG" <?php echo ($_POST['marca'] ?? '') == 'LG' ? 'selected' : ''; ?>>LG</option>
                        <option value="Midea" <?php echo ($_POST['marca'] ?? '') == 'Midea' ? 'selected' : ''; ?>>Midea</option>
                        <option value="Gree" <?php echo ($_POST['marca'] ?? '') == 'Gree' ? 'selected' : ''; ?>>Gree</option>
                        <option value="TCL" <?php echo ($_POST['marca'] ?? '') == 'TCL' ? 'selected' : ''; ?>>TCL</option>
                        <option value="Springer" <?php echo ($_POST['marca'] ?? '') == 'Springer' ? 'selected' : ''; ?>>Springer</option>
                        <option value="Consul" <?php echo ($_POST['marca'] ?? '') == 'Consul' ? 'selected' : ''; ?>>Consul</option>
                        <option value="Philco" <?php echo ($_POST['marca'] ?? '') == 'Philco' ? 'selected' : ''; ?>>Philco</option>
                        <option value="Elgin" <?php echo ($_POST['marca'] ?? '') == 'Elgin' ? 'selected' : ''; ?>>Elgin</option>
                        <option value="Outra" <?php echo ($_POST['marca'] ?? '') == 'Outra' ? 'selected' : ''; ?>>Outra</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="btus">Capacidade (BTUs)</label>
                    <select id="btus" name="btus" class="form-control">
                        <option value="">Selecione os BTUs</option>
                        <option value="7000" <?php echo ($_POST['btus'] ?? '') == '7000' ? 'selected' : ''; ?>>7.000 BTUs</option>
                        <option value="9000" <?php echo ($_POST['btus'] ?? '') == '9000' ? 'selected' : ''; ?>>9.000 BTUs</option>
                        <option value="12000" <?php echo ($_POST['btus'] ?? '') == '12000' ? 'selected' : ''; ?>>12.000 BTUs</option>
                        <option value="18000" <?php echo ($_POST['btus'] ?? '') == '18000' ? 'selected' : ''; ?>>18.000 BTUs</option>
                        <option value="24000" <?php echo ($_POST['btus'] ?? '') == '24000' ? 'selected' : ''; ?>>24.000 BTUs</option>
                        <option value="30000" <?php echo ($_POST['btus'] ?? '') == '30000' ? 'selected' : ''; ?>>30.000 BTUs</option>
                        <option value="36000" <?php echo ($_POST['btus'] ?? '') == '36000' ? 'selected' : ''; ?>>36.000 BTUs</option>
                        <option value="48000" <?php echo ($_POST['btus'] ?? '') == '48000' ? 'selected' : ''; ?>>48.000 BTUs</option>
                        <option value="60000" <?php echo ($_POST['btus'] ?? '') == '60000' ? 'selected' : ''; ?>>60.000 BTUs</option>
                    </select>
                </div>
            </div>
            
            <!--<div class="form-group">
                <label for="tipo">Tipo de Equipamento</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">Selecione o tipo</option>
                    <option value="Split Hi-Wall" <?php echo ($_POST['tipo'] ?? '') == 'Split Hi-Wall' ? 'selected' : ''; ?>>Split Hi-Wall</option>
                    <option value="Split Piso-Teto" <?php echo ($_POST['tipo'] ?? '') == 'Split Piso-Teto' ? 'selected' : ''; ?>>Split Piso-Teto</option>
                    <option value="Split Cassete" <?php echo ($_POST['tipo'] ?? '') == 'Split Cassete' ? 'selected' : ''; ?>>Split Cassete</option>
                    <option value="Split Inverter" <?php echo ($_POST['tipo'] ?? '') == 'Split Inverter' ? 'selected' : ''; ?>>Split Inverter</option>
                    <option value="Janela" <?php echo ($_POST['tipo'] ?? '') == 'Janela' ? 'selected' : ''; ?>>Janela</option>
                    <option value="Portátil" <?php echo ($_POST['tipo'] ?? '') == 'Portátil' ? 'selected' : ''; ?>>Portátil</option>
                    <option value="Outro" <?php echo ($_POST['tipo'] ?? '') == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                </select>
            </div>-->
            
            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" rows="4" placeholder="Descreva o problema ou necessidades específicas... Aqui você precisa informar o que prescisa, para assim nos ajudar. Informe se deseja Instalação, Manutenção, Limpeza, etc.  "><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-accent" id="btnOrcamento">
                💰 Solicitar Orçamento & Abrir WhatsApp
            </button>
            
            <div class="text-center" style="margin-top: 15px;">
                <small class="text-muted">
                    💡 Após clicar, o WhatsApp abrirá automaticamente para conversarmos sobre seu orçamento
                </small>
            </div>
        </form>
        <?php else: ?>
        <div class="text-center" style="padding: 40px;">
            <h3>🎉 Orçamento Solicitado!</h3>
            <p>Obrigado pela sua solicitação. Em instantes o WhatsApp será aberto para conversarmos sobre seu orçamento.</p>
            <a href="orcamento.php" class="btn btn-primary">💰 Fazer Novo Orçamento</a>
            <a href="index.php" class="btn btn-secondary">🏠 Voltar para Home</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formOrcamento');
    if(form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnOrcamento');
            if(btn) {
                btn.disabled = true;
                btn.innerHTML = 'Processando...';
            }
        });
    }
});
</script>

<style>
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 10px;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    color: white;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.booking-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 2px rgba(0,102,204,0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.text-muted {
    color: #6c757d !important;
}

.text-center {
    text-align: center;
}
</style>

<?php include 'includes/footer.php'; ?>