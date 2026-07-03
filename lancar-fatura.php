<?php
session_start();
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) { header("Location: login.php"); exit; }

$pdo = new PDO("mysql:host=sql311.infinityfree.com;dbname=if0_41023013_db_clientes;charset=utf8", "if0_41023013", "2kVHu71TF3ly", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// --- LÓGICA DE PROCESSAMENTO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cliente_id'])) {
    try {
        $pdo->beginTransaction();
        $cliente_id = $_POST['cliente_id'];
        $equipamentos = $_POST['equip'];

        foreach ($equipamentos as $e) {
            $tag = $e['tag']; 
            $modelo_nome = $e['modelo_nome']; 
            $duracao = (int)$e['duracao'];
            $vencimento_dia = (int)$e['vencimento'];
            
            $valor_base = (float)$e['plano_valor']; 
            // Agora somamos o valor_geracao que vem travado do banco
            $valor_adicionais = (float)$e['valor_geracao'] + (float)$e['suporte'] + (float)$e['setup'];
            $desconto = (float)$e['desconto'];
            
            $subtotal = $valor_base + $valor_adicionais;
            $valor_final = $subtotal - ($subtotal * ($desconto / 100));

            for ($i = 0; $i < $duracao; $i++) {
                $data_venc = date('Y-m-d', strtotime("+$i month", strtotime(date('Y-m-').$vencimento_dia)));
                $stmt = $pdo->prepare("INSERT INTO financeiro (cliente_id, descricao, valor, data_vencimento, status, tipo) VALUES (?, ?, ?, ?, 'pendente', 'receita')");
                $stmt->execute([
                    $cliente_id, 
                    "Locação: $tag ($modelo_nome) - Parc. ".($i+1)."/$duracao", 
                    $valor_final, 
                    $data_venc 
                ]);
            }

            $sql_update_equip = "UPDATE equipamentos SET cliente_id = ?, status = 'locado' WHERE codigo_equipamento = ?";
            $pdo->prepare($sql_update_equip)->execute([$cliente_id, $tag]);
        }

        $pdo->commit();
        header("Location: financeiro.php?msg=sucesso");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao lançar contrato: " . $e->getMessage());
    }
}

$clientes = $pdo->query("SELECT id, nome_fantasia FROM clientes ORDER BY nome_fantasia ASC")->fetchAll();

/* BUSCA EQUIPAMENTOS - Agora incluindo a Geração */
$equip_disponiveis = $pdo->query("SELECT codigo_equipamento, modelo, geracao FROM equipamentos WHERE status = 'disponivel' ORDER BY codigo_equipamento ASC")->fetchAll();

$precos_modelos = [
    "Essencial A" => 149.99,
    "Essencial B" => 179.99,
    "Performance A" => 199.99,
    "Performance B" => 219.99
];

// Tabela de preços das gerações para o cálculo JS
$precos_geracoes = [
    "Bronze" => 0.00,
    "Prata" => 40.00,
    "Ouro" => 100.00
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lançar Contrato | VaultCore</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", sans-serif; }
        body { background: #f2f4f6; padding: 140px 20px 50px; color: #1f2937; }
        header { position: fixed; top: 0; left: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
        .logo { font-size: 24px; font-weight: 600; color: #ffffff; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .cliente-select { background: #f8fafc; padding: 25px; border-radius: 12px; border: 2px solid #e2e8f0; margin-bottom: 30px; }
        .equip-card { background: #fff; border: 1px solid #e5e7eb; padding: 25px; border-radius: 15px; margin-bottom: 25px; position: relative; border-left: 8px solid #1e899e; transition: 0.3s; }
        .grid-equip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px; }
        .half { grid-column: span 2; }
        label { display: block; margin-bottom: 5px; font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        input, select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #f9fafb; font-size: 14px; }
        input:read-only { background: #e5e7eb; color: #4b5563; cursor: not-allowed; }
        .btn-add { 
        background: #152534; 
        color: white; 
        border: none; 
        padding: 15px 25px; 
        border-radius: 10px 10px 10px 10px; /* Arredonda só em cima para encaixar na barra se quiser, ou mantenha 10px para tudo */
        cursor: pointer; 
        font-weight: bold; 
        margin-top: 20px;    /* Espaço em relação ao último card */
        margin-bottom: 0;     /* Remove margem de baixo para encostar na barra preta */
        display: inline-block;
        }
        .resumo-fixo { background: #152534; color: white; padding: 30px; border-radius: 20px; margin-top: 40px; display: flex; justify-content: space-between; align-items: center; position: bottom; bottom: 20px; }
        .subtotal-tag { background: #e0f2f1; color: #00796b; padding: 8px 15px; border-radius: 8px; font-weight: bold; display: inline-block; margin-top: 10px; }
        .btn-remove { position: absolute; top: 20px; right: 20px; background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="financeiro.php" style="color:#fff; text-decoration:none; font-weight:bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2>Efetivar Contrato de Locação</h2>
    <form action="" method="post">
        <div class="cliente-select">
            <label>Selecione o Cliente (Locatário)</label>
            <select name="cliente_id" required>
                <option value="">-- Escolha o cliente --</option>
                <?php foreach($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome_fantasia']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="lista-equipamentos"></div>

        <button type="button" class="btn-add" onclick="adicionarMaquina()">+ VINCULAR EQUIPAMENTO</button>

        <div class="resumo-fixo">
            <div>
                <span style="color: #9ca3af; font-size: 13px;">FATURAMENTO MENSAL ESTIMADO</span>
                <div style="font-size: 32px; font-weight: 800; color: #65c9d1;" id="total-geral">R$ 0,00</div>
            </div>
            <button type="submit" style="background: #1e899e; color: white; border: none; padding: 18px 45px; border-radius: 12px; font-weight: bold; cursor: pointer;">SALVAR CONTRATO E GERAR PARCELAS</button>
        </div>
    </form>
</main>

<datalist id="lista_disponiveis">
    <?php foreach($equip_disponiveis as $eq): ?>
        <option value="<?= $eq['codigo_equipamento'] ?>" data-modelo="<?= $eq['modelo'] ?>" data-geracao="<?= $eq['geracao'] ?>">
    <?php endforeach; ?>
</datalist>

<script>
const precosModelos = <?= json_encode($precos_modelos) ?>;
const precosGeracoes = <?= json_encode($precos_geracoes) ?>;
let contador = 0;

function adicionarMaquina() {
    contador++;
    const container = document.getElementById('lista-equipamentos');
    const div = document.createElement('div');
    div.className = 'equip-card';
    div.id = 'card-' + contador;
    div.innerHTML = `
        <button type="button" class="btn-remove" onclick="removerMaquina(${contador})">REMOVER</button>
        <div class="grid-equip">
            <div class="half">
                <label>TAG do Equipamento (Estoque)</label>
                <input type="text" name="equip[${contador}][tag]" list="lista_disponiveis" oninput="identificarEquipamento(${contador}, this)" required placeholder="Digite a TAG...">
            </div>
            <div>
                <label>Duração (Meses)</label>
                <input type="number" name="equip[${contador}][duracao]" value="12" min="1" required>
            </div>
            <div>
                <label>Vencimento (Dia)</label>
                <input type="number" name="equip[${contador}][vencimento]" value="10" min="1" max="31" required>
            </div>
            
            <div class="half">
                <label>Modelo Detectado</label>
                <input type="text" name="equip[${contador}][modelo_nome]" id="modelo_nome_${contador}" readonly placeholder="Aguardando TAG...">
            </div>

            <div>
                <label>Valor Base (Mensal)</label>
                <input type="number" step="0.01" name="equip[${contador}][plano_valor]" id="plano_valor_${contador}" readonly value="0.00">
            </div>

            <div>
                <label>Geração (ID Hardware)</label>
                <input type="text" id="geracao_nome_${contador}" readonly placeholder="---">
                <input type="hidden" name="equip[${contador}][valor_geracao]" id="geracao_valor_${contador}" value="0">
            </div>

            <div>
                <label>Suporte</label>
                <select name="equip[${contador}][suporte]" class="calc" onchange="calcularTudo()">
                    <option value="0">Não</option>
                    <option value="80">Sim (+R$ 80)</option>
                </select>
            </div>
            <div>
                <label>Setup Completo</label>
                <select name="equip[${contador}][setup]" class="calc" onchange="calcularTudo()">
                    <option value="0">Não</option>
                    <option value="40">Sim (+R$ 40)</option>
                </select>
            </div>
            <div>
                <label>Desconto (%)</label>
                <input type="number" name="equip[${contador}][desconto]" class="calc desc" value="0" oninput="calcularTudo()">
            </div>
            <div class="half" style="text-align: right;">
                <div class="subtotal-tag" id="total-item-${contador}">Subtotal: R$ 0,00</div>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function identificarEquipamento(id, input) {
    const datalist = document.getElementById('lista_disponiveis');
    const option = Array.from(datalist.options).find(opt => opt.value === input.value);
    
    const campoModelo = document.getElementById('modelo_nome_' + id);
    const campoPreco = document.getElementById('plano_valor_' + id);
    const campoGerNome = document.getElementById('geracao_nome_' + id);
    const campoGerVal = document.getElementById('geracao_valor_' + id);

    if (option) {
        const modelo = option.getAttribute('data-modelo');
        const geracao = option.getAttribute('data-geracao');
        
        campoModelo.value = modelo;
        campoPreco.value = (precosModelos[modelo] || 0).toFixed(2);
        
        campoGerNome.value = geracao;
        campoGerVal.value = precosGeracoes[geracao] || 0;
        
        input.style.borderColor = "#10b981";
    } else {
        campoModelo.value = "";
        campoPreco.value = "0.00";
        campoGerNome.value = "";
        campoGerVal.value = 0;
        input.style.borderColor = "#d1d5db";
    }
    calcularTudo();
}

function removerMaquina(id) { document.getElementById('card-' + id).remove(); calcularTudo(); }

function calcularTudo() {
    let totalGeral = 0;
    document.querySelectorAll('.equip-card').forEach(card => {
        const id = card.id.split('-')[1];
        
        let base = parseFloat(document.getElementById('plano_valor_'+id).value) || 0;
        let vGeracao = parseFloat(document.getElementById('geracao_valor_'+id).value) || 0;
        let vSuporte = parseFloat(card.querySelector(`select[name*="[suporte]"]`).value) || 0;
        let vSetup = parseFloat(card.querySelector(`select[name*="[setup]"]`).value) || 0;
        
        let sub = base + vGeracao + vSuporte + vSetup;
        
        let desc = parseFloat(card.querySelector('.desc').value) || 0;
        sub = sub - (sub * (desc / 100));
        
        document.getElementById('total-item-' + id).innerText = "Subtotal: R$ " + sub.toLocaleString('pt-br', {minimumFractionDigits: 2});
        totalGeral += sub;
    });
    document.getElementById('total-geral').innerText = "R$ " + totalGeral.toLocaleString('pt-br', {minimumFractionDigits: 2});
}

adicionarMaquina();
</script>
</body>
</html>