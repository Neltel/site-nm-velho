# Sistema Web Integrado - N&M Refrigeração

## 📋 Visão Geral

Sistema web completo integrado que consolida aplicações de gestão em um único site responsivo. O sistema funciona como plataforma administrativa completa para empresas de refrigeração e ar condicionado.

## 🚀 Funcionalidades Implementadas

### ✅ Sistema de Autenticação e Segurança
- [x] Login seguro com proteção contra SQL Injection (PDO Prepared Statements)
- [x] Proteção contra Brute Force (5 tentativas, bloqueio de 15 minutos)
- [x] CSRF Token Protection
- [x] Session Regeneration (prevenção de Session Fixation)
- [x] Proteção XSS (sanitização de dados)
- [x] Logout seguro com limpeza completa de sessão
- [x] Sistema de logs de acesso e segurança
- [x] Headers de segurança (X-Frame-Options, X-XSS-Protection, etc.)

### ✅ Configuração de Banco de Dados
- [x] Configuração centralizada com credenciais fornecidas
- [x] Schema completo com 20+ tabelas
- [x] Suporte a PDO e MySQLi (compatibilidade)
- [x] Charset UTF-8MB4 para suporte completo a caracteres especiais
- [x] Indexes e Foreign Keys apropriados
- [x] Sistema de comentários nas tabelas

### ✅ Módulos Principais Existentes (Código Legado Mantido)
- [x] Dashboard com estatísticas
- [x] Gestão de Clientes (CRUD completo)
- [x] Gestão de Produtos (com estoque e imagens)
- [x] Gestão de Serviços
- [x] Gestão de Materiais
- [x] Sistema de Orçamentos
- [x] Sistema de Agendamentos
- [x] Gestão de Usuários Admin
- [x] Configurações do Sistema
- [x] Financeiro

### ✅ Novas Funcionalidades Implementadas

#### 📄 Sistema de Upload de Arquivos
- **Arquivo:** `includes/upload_handler.php`
- Classe `UploadHandler` com validação completa
- Suporte a múltiplos tipos de arquivo (imagens, documentos)
- Validação de tipo MIME e extensão
- Limite de tamanho configurável
- Geração de nomes únicos e seguros
- Logs de uploads
- Método de exclusão segura

#### 📑 Geração de PDFs
- **Arquivo:** `includes/pdf_generator.php`
- Classe `PDFGenerator` para documentos profissionais
- Geração de PDFs para:
  - Orçamentos (com itens detalhados)
  - Garantias (com termos legais brasileiros - CDC)
  - Relatórios Técnicos
- Layout profissional com cabeçalho e rodapé
- Dados da empresa configuráveis
- Formatação automática de valores

#### 📱 Integração WhatsApp
- **Arquivo:** `includes/whatsapp_integration.php`
- Classe `WhatsAppIntegration` para notificações automáticas
- Funcionalidades:
  - Notificação de agendamentos
  - Envio de orçamentos
  - Lembretes de cobrança
  - Mensagens personalizadas
- Log completo de notificações enviadas
- Suporte a anexos (PDFs)
- Geração de links WhatsApp diretos

#### 🧮 Calculadora Técnica
- **Arquivo:** `admin/calculadora_tecnica.php`
- Ferramentas profissionais:
  1. **Cálculo de Carga Térmica**
     - Baseado em dimensões do ambiente
     - Considera: pessoas, janelas, exposição solar, eletrônicos
     - Margem de segurança de 15%
     - Recomendação de modelo
  2. **Dimensionamento de Capacitor**
     - Para motores monofásicos e trifásicos
     - Capacitor de partida e trabalho
  3. **Bitola de Fio**
     - Baseado na NBR 5410
     - Cálculo de queda de tensão
     - Alerta se queda > 3%
  4. **Conversor de Unidades**
     - BTU ↔ Watts
     - BTU ↔ Kcal
     - BTU ↔ kW
  5. **Normas Técnicas**
     - Referência rápida a NBR 16401, NBR 5410, NR-12

### 📊 Estrutura do Banco de Dados

#### Tabelas Criadas
1. **administradores** - Usuários do sistema com níveis de acesso
2. **clientes** - Dados completos de clientes (PF e PJ)
3. **documentos_clientes** - Arquivos e documentos dos clientes
4. **categorias_produtos** - Categorização de produtos
5. **produtos** - Produtos para venda
6. **servicos** - Serviços oferecidos
7. **materiais** - Materiais utilizados
8. **servicos_materiais** - Relacionamento serviço-material
9. **orcamentos** - Orçamentos com status
10. **orcamentos_itens** - Itens dos orçamentos
11. **pedidos** - Pedidos de venda
12. **pedidos_itens** - Itens dos pedidos
13. **vendas** - Registro de vendas
14. **agendamentos** - Agendamentos de serviços
15. **cobrancas** - Gestão de cobranças
16. **garantias** - Certificados de garantia
17. **preventivas_pmp** - Planos de Manutenção Preventiva
18. **preventivas_execucoes** - Execuções de PMPs
19. **relatorios_tecnicos** - Relatórios técnicos
20. **configuracoes** - Configurações do sistema
21. **logs_sistema** - Logs de acesso e ações
22. **movimentacoes_estoque** - Controle de estoque
23. **notificacoes_whatsapp** - Log de mensagens WhatsApp

## 🔧 Tecnologias Utilizadas

- **Backend:** PHP 8.1+
- **Banco de Dados:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Ícones:** Font Awesome 6.4
- **Segurança:** PDO Prepared Statements, Password Hashing (bcrypt)
- **Sessões:** PHP Sessions com configurações seguras

## 📁 Estrutura de Arquivos

```
site-nm-velho/
├── admin/                          # Painel administrativo
│   ├── login.php                   # Login seguro
│   ├── logout.php                  # Logout
│   ├── dashboard.php               # Dashboard principal
│   ├── clientes.php                # Gestão de clientes
│   ├── produtos.php                # Gestão de produtos
│   ├── servicos.php                # Gestão de serviços
│   ├── materiais.php               # Gestão de materiais
│   ├── orcamentos.php              # Gestão de orçamentos
│   ├── agendamentos.php            # Gestão de agendamentos
│   ├── financeiro.php              # Relatórios financeiros
│   ├── calculadora_tecnica.php     # Calculadoras técnicas
│   ├── configuracoes.php           # Configurações do sistema
│   └── includes/                   # Arquivos include do admin
│       ├── header-admin.php        # Header com menu
│       ├── footer-admin.php        # Footer
│       └── auth.php                # Verificação de autenticação
├── includes/                       # Bibliotecas principais
│   ├── config.php                  # Configuração legado
│   ├── database.php                # Funções de banco legado
│   ├── database_schema.php         # Schema completo do BD
│   ├── upload_handler.php          # Classe para uploads
│   ├── pdf_generator.php           # Classe para PDFs
│   └── whatsapp_integration.php    # Classe para WhatsApp
├── uploads/                        # Arquivos enviados
│   ├── clientes/                   # Documentos de clientes
│   ├── produtos/                   # Imagens de produtos
│   ├── orcamentos/                 # PDFs de orçamentos
│   ├── garantias/                  # PDFs de garantias
│   └── relatorios/                 # PDFs de relatórios
├── logs/                           # Logs do sistema
├── confg.php                       # Configuração principal
├── index.php                       # Página inicial pública
└── README.md                       # Este arquivo
```

## ⚙️ Configuração

### 1. Banco de Dados

```php
// Credenciais configuradas em confg.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nmrefrig_imperio');
define('DB_USER', 'nmrefrig_imperio');
define('DB_PASS', 'JEJ5qnvpLRbACP7tUhu6');
```

### 2. Criar Tabelas

Execute o arquivo de schema para criar todas as tabelas:

```php
// Acesse via navegador
http://seudominio.com/includes/database_schema.php

// Ou via CLI
php includes/database_schema.php
```

### 3. Credenciais de Acesso Padrão

```
Usuário: admin
Senha: admin123
```

**⚠️ IMPORTANTE:** Altere a senha padrão imediatamente em produção!

### 4. Permissões de Pastas

```bash
chmod 755 uploads/
chmod 755 logs/
chmod 644 confg.php
```

## 🔒 Segurança

### Configurações de Segurança Implementadas

1. **Proteção SQL Injection:** Todos os queries usam PDO Prepared Statements
2. **Proteção XSS:** Função `sanitize()` em todos os inputs
3. **CSRF Protection:** Tokens CSRF em todos os formulários
4. **Brute Force Protection:** Limite de tentativas de login
5. **Session Security:** 
   - Session regeneration após login
   - Timeout de 30 minutos
   - Cookie seguro
6. **File Upload Security:**
   - Validação de tipo MIME
   - Validação de extensão
   - Limite de tamanho
   - Nomes únicos e aleatórios
7. **Password Security:** Bcrypt com salt automático
8. **Headers de Segurança:** X-Frame-Options, X-XSS-Protection, etc.

### Funções de Segurança Disponíveis

```php
// Sanitização
sanitize($data);                    // Remove tags HTML e caracteres especiais
escapeSql($data);                   // Escapa para SQL (use PDO preferencialmente)

// Validação
validarEmail($email);               // Valida formato de email
validarTelefone($telefone);         // Valida telefone brasileiro

// CSRF
generateCsrfToken();                // Gera token CSRF
validateCsrfToken($token);          // Valida token CSRF

// Autenticação
isAdminLogado();                    // Verifica se admin está logado
isClienteLogado();                  // Verifica se cliente está logado
requireAdminAuth();                 // Requer autenticação (redireciona se não)
requireClienteAuth();               // Requer autenticação de cliente

// Logs
registrarLog($tipo, $mensagem, $dados);  // Registra log no sistema
```

## 📝 Uso das Classes Utilitárias

### Upload de Arquivos

```php
require_once 'includes/upload_handler.php';

// Upload único
$resultado = UploadHandler::upload(
    $_FILES['arquivo'],
    'clientes',      // Pasta destino
    'documento',     // Categoria
    5242880          // Tamanho máximo (opcional)
);

if ($resultado['sucesso']) {
    $caminho = $resultado['caminho'];
}

// Upload múltiplo
$resultados = UploadHandler::uploadMultiplo($_FILES['arquivos'], 'clientes');

// Excluir arquivo
UploadHandler::excluir('uploads/clientes/arquivo.pdf');
```

### Geração de PDF

```php
require_once 'includes/pdf_generator.php';

$pdfGen = new PDFGenerator($pdo);

// Gerar PDF de orçamento
$resultado = $pdfGen->gerarOrcamento($orcamentoId);

// Gerar PDF de garantia
$resultado = $pdfGen->gerarGarantia($garantiaId);

// Gerar PDF de relatório técnico
$resultado = $pdfGen->gerarRelatorioTecnico($relatorioId);
```

### Integração WhatsApp

```php
require_once 'includes/whatsapp_integration.php';

$whatsapp = new WhatsAppIntegration($pdo);

// Notificar agendamento
$resultado = $whatsapp->notificarAgendamento($agendamentoId);

// Enviar orçamento
$resultado = $whatsapp->enviarOrcamento($orcamentoId, $pdfPath);

// Enviar lembrete de cobrança
$resultado = $whatsapp->enviarLembreteCobranca($cobrancaId);

// Gerar link direto
$link = WhatsAppIntegration::gerarLink('17999999999', 'Olá!');
```

## 🎨 Personalização

### Cores e Temas

As cores do sistema são configuráveis através do banco de dados na tabela `config_site`:

- `cor_primaria`: Cor principal (#0066cc)
- `cor_secundaria`: Cor secundária (#00a8ff)

### Logo da Empresa

Configure o logo em:
```
Configurações > Dados da Empresa > Logo
```

### Dados da Empresa

Todos os dados da empresa são configuráveis:
- Nome
- Razão Social
- CNPJ
- Telefone
- Email
- Endereço

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- ✅ Desktop
- ✅ Tablet
- ✅ Mobile
- ✅ Impressão (layouts de PDF otimizados)

## 🔄 Próximas Implementações Sugeridas

### Prioridade Alta
- [ ] Módulo de Garantias (interface de gestão)
- [ ] Módulo de PMP - Manutenção Preventiva (interface completa)
- [ ] Módulo de Cobranças (interface de gestão)
- [ ] Módulo de Vendas (interface de gestão)
- [ ] Dashboard Financeiro Completo (gráficos)
- [ ] Exportação para Excel
- [ ] Área do Cliente (login e histórico)

### Prioridade Média
- [ ] Integração real com API do WhatsApp
- [ ] Integração com IA para melhorias de texto
- [ ] Sistema de notificações push
- [ ] Backup automático
- [ ] Multi-idioma

### Prioridade Baixa
- [ ] PWA (Progressive Web App)
- [ ] Dark Mode
- [ ] Sistema de permissões granular por módulo

## 📞 Suporte

Para dúvidas ou problemas:
- Consulte este README
- Verifique os logs em `/logs/`
- Revise os comentários no código (todos em português)

## 👨‍💻 Desenvolvido por

Sistema desenvolvido com foco em:
- ✅ Segurança (proteções contra vulnerabilidades comuns)
- ✅ Escalabilidade (arquitetura modular)
- ✅ Manutenibilidade (código comentado em português)
- ✅ Performance (queries otimizados, indexes apropriados)
- ✅ Usabilidade (interface intuitiva)

## 📄 Licença

Todos os direitos reservados - N&M Refrigeração

---

**Versão:** 1.0.0  
**Última Atualização:** Fevereiro 2026  
**Status:** Em Desenvolvimento Ativo
