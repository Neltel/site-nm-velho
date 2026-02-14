<?php
// popular_banco.php
// Arquivo para popular o banco de dados com dados iniciais

include 'includes/config.php';

echo "<h2>🌡️ Populando Banco de Dados - ClimaTech</h2>";

try {
    // Serviços padrão
    $servicos = [
        [
            'nome' => 'Instalação Completa 9000 BTUs',
            'descricao' => 'Instalação profissional com todos os materiais inclusos para ar condicionado de 9000 BTUs',
            'preco_base' => 299.00,
            'categoria' => 'instalacao',
            'ativo' => 1
        ],
        [
            'nome' => 'Instalação Completa 12000 BTUs', 
            'descricao' => 'Instalação profissional com todos os materiais inclusos para ar condicionado de 12000 BTUs',
            'preco_base' => 349.00,
            'categoria' => 'instalacao',
            'ativo' => 1
        ],
        [
            'nome' => 'Instalação Completa 18000 BTUs',
            'descricao' => 'Instalação profissional com todos os materiais inclusos para ar condicionado de 18000 BTUs',
            'preco_base' => 399.00,
            'categoria' => 'instalacao',
            'ativo' => 1
        ],
        [
            'nome' => 'Instalação Completa 24000 BTUs',
            'descricao' => 'Instalação profissional com todos os materiais inclusos para ar condicionado de 24000 BTUs',
            'preco_base' => 449.00,
            'categoria' => 'instalacao',
            'ativo' => 1
        ],
        [
            'nome' => 'Instalação Básica (Sem Material)',
            'descricao' => 'Instalação profissional sem incluir materiais - cliente fornece equipamento e materiais',
            'preco_base' => 199.00,
            'categoria' => 'instalacao',
            'ativo' => 1
        ],
        [
            'nome' => 'Manutenção Preventiva',
            'descricao' => 'Limpeza completa, verificação de componentes e calibração do sistema',
            'preco_base' => 149.00,
            'categoria' => 'manutencao',
            'ativo' => 1
        ],
        [
            'nome' => 'Limpeza Completa',
            'descricao' => 'Limpeza interna e externa completa com higienização',
            'preco_base' => 129.00,
            'categoria' => 'limpeza',
            'ativo' => 1
        ],
        [
            'nome' => 'Diagnóstico e Reparo',
            'descricao' => 'Diagnóstico completo e reparo do equipamento com garantia',
            'preco_base' => 99.00,
            'categoria' => 'reparo',
            'ativo' => 1
        ],
        [
            'nome' => 'Remoção de Equipamento',
            'descricao' => 'Remoção segura do equipamento com preservação do gás refrigerante',
            'preco_base' => 89.00,
            'categoria' => 'remocao',
            'ativo' => 1
        ],
        [
            'nome' => 'Recarga de Gás',
            'descricao' => 'Recarga completa de gás refrigerante com teste de vazamento',
            'preco_base' => 199.00,
            'categoria' => 'reparo',
            'ativo' => 1
        ]
    ];

    // Inserir serviços
    $servicos_inseridos = 0;
    foreach($servicos as $servico) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO servicos (nome, descricao, preco_base, categoria, ativo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $servico['nome'],
            $servico['descricao'], 
            $servico['preco_base'],
            $servico['categoria'],
            $servico['ativo']
        ]);
        $servicos_inseridos += $stmt->rowCount();
    }

    echo "<p>✅ <strong>{$servicos_inseridos}</strong> serviços inseridos</p>";

    // Produtos padrão
    $produtos = [
        [
            'nome' => 'Ar Condicionado Split 9000 BTUs',
            'descricao' => 'Split Hi-Wall 9000 BTUs Frio - Ideal para ambientes de até 12m²',
            'preco' => 1299.00,
            'categoria' => 'Split',
            'marca' => 'Springer',
            'btus' => 9000,
            'estoque' => 5,
            'ativo' => 1
        ],
        [
            'nome' => 'Ar Condicionado Split 12000 BTUs',
            'descricao' => 'Split Hi-Wall 12000 BTUs Frio - Ideal para ambientes de até 16m²',
            'preco' => 1599.00,
            'categoria' => 'Split', 
            'marca' => 'Springer',
            'btus' => 12000,
            'estoque' => 3,
            'ativo' => 1
        ],
        [
            'nome' => 'Ar Condicionado Split 18000 BTUs',
            'descricao' => 'Split Hi-Wall 18000 BTUs Frio - Ideal para ambientes de até 24m²',
            'preco' => 2199.00,
            'categoria' => 'Split',
            'marca' => 'LG',
            'btus' => 18000,
            'estoque' => 2,
            'ativo' => 1
        ],
        [
            'nome' => 'Kit Instalação Completo',
            'descricao' => 'Kit completo para instalação: tubos de cobre, isolamento, suporte, etc.',
            'preco' => 199.00,
            'categoria' => 'Acessórios',
            'marca' => 'Fortlev',
            'btus' => null,
            'estoque' => 10,
            'ativo' => 1
        ],
        [
            'nome' => 'Controle Remoto Universal',
            'descricao' => 'Controle remoto universal para ar condicionado split',
            'preco' => 49.90,
            'categoria' => 'Acessórios',
            'marca' => 'Multi',
            'btus' => null,
            'estoque' => 8,
            'ativo' => 1
        ]
    ];

    // Inserir produtos
    $produtos_inseridos = 0;
    foreach($produtos as $produto) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO produtos (nome, descricao, preco, categoria, marca, btus, estoque, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $produto['nome'],
            $produto['descricao'],
            $produto['preco'],
            $produto['categoria'],
            $produto['marca'],
            $produto['btus'],
            $produto['estoque'],
            $produto['ativo']
        ]);
        $produtos_inseridos += $stmt->rowCount();
    }

    echo "<p>✅ <strong>{$produtos_inseridos}</strong> produtos inseridos</p>";

    // Materiais padrão
    $materiais = [
        [
            'nome' => 'Tubo de Cobre 1/4"',
            'descricao' => 'Tubo de cobre para refrigeração 1/4" - rolo 15 metros',
            'categoria' => 'Tubulação',
            'preco_unitario' => 89.90,
            'unidade_medida' => 'metro',
            'estoque' => 50,
            'estoque_minimo' => 10,
            'ativo' => 1
        ],
        [
            'nome' => 'Tubo de Cobre 3/8"',
            'descricao' => 'Tubo de cobre para refrigeração 3/8" - rolo 15 metros',
            'categoria' => 'Tubulação',
            'preco_unitario' => 119.90,
            'unidade_medida' => 'metro',
            'estoque' => 40,
            'estoque_minimo' => 8,
            'ativo' => 1
        ],
        [
            'nome' => 'Isolamento Térmico 1/4"',
            'descricao' => 'Isolamento térmico para tubulação de cobre 1/4"',
            'categoria' => 'Isolamento',
            'preco_unitario' => 2.50,
            'unidade_medida' => 'metro',
            'estoque' => 100,
            'estoque_minimo' => 20,
            'ativo' => 1
        ],
        [
            'nome' => 'Isolamento Térmico 3/8"',
            'descricao' => 'Isolamento térmico para tubulação de cobre 3/8"',
            'categoria' => 'Isolamento',
            'preco_unitario' => 3.20,
            'unidade_medida' => 'metro',
            'estoque' => 80,
            'estoque_minimo' => 15,
            'ativo' => 1
        ],
        [
            'nome' => 'Cabo de Comunicação 5 Vias',
            'descricao' => 'Cabo para comunicação entre unidades interna e externa - 5 vias',
            'categoria' => 'Fiação',
            'preco_unitario' => 4.90,
            'unidade_medida' => 'metro',
            'estoque' => 60,
            'estoque_minimo' => 12,
            'ativo' => 1
        ],
        [
            'nome' => 'Disjuntor 20A',
            'descricao' => 'Disjuntor bipolar 20A para proteção do circuito',
            'categoria' => 'Disjuntores',
            'preco_unitario' => 29.90,
            'unidade_medida' => 'unidade',
            'estoque' => 15,
            'estoque_minimo' => 3,
            'ativo' => 1
        ],
        [
            'nome' => 'Suporte para Unidade Externa',
            'descricao' => 'Suporte em aço galvanizado para unidade externa',
            'categoria' => 'Acessórios',
            'preco_unitario' => 79.90,
            'unidade_medida' => 'unidade',
            'estoque' => 8,
            'estoque_minimo' => 2,
            'ativo' => 1
        ]
    ];

    // Inserir materiais
    $materiais_inseridos = 0;
    foreach($materiais as $material) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO materiais (nome, descricao, categoria, preco_unitario, unidade_medida, estoque, estoque_minimo, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $material['nome'],
            $material['descricao'],
            $material['categoria'],
            $material['preco_unitario'],
            $material['unidade_medida'],
            $material['estoque'],
            $material['estoque_minimo'],
            $material['ativo']
        ]);
        $materiais_inseridos += $stmt->rowCount();
    }

    echo "<p>✅ <strong>{$materiais_inseridos}</strong> materiais inseridos</p>";

    echo "<h3>🎉 Banco de dados populado com sucesso!</h3>";
    echo "<p>Agora você pode acessar:</p>";
    echo "<ul>";
    echo "<li><a href='orcamento.php'>orcamento.php</a> - Para ver os serviços disponíveis</li>";
    echo "<li><a href='admin/login.php'>admin/login.php</a> - Para acessar o painel admin</li>";
    echo "<li><strong>Usuário:</strong> admin | <strong>Senha:</strong> 123456</li>";
    echo "</ul>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Erro ao popular banco de dados: " . $e->getMessage() . "</p>";
}
?>