<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';

$mes = (int)($_GET['mes'] ?? date('n'));
$ano = (int)($_GET['ano'] ?? date('Y'));

/* Pedidos do mês selecionado */
$stmt = $pdo->prepare("
    SELECT p.*, c.razao_social AS cliente_nome
    FROM pedidos p
    LEFT JOIN usuarios u ON u.id = p.usuario_id
    LEFT JOIN clientes c ON c.id = u.cliente_id
    WHERE MONTH(p.criado_em) = ? AND YEAR(p.criado_em) = ?
    ORDER BY p.criado_em DESC
");
$stmt->execute([$mes, $ano]);
$pedidos = $stmt->fetchAll();

$faturamentoMes = array_sum(array_column($pedidos, 'total'));
$totalPedidosMes = count($pedidos);
$ticketMedio = $totalPedidosMes > 0 ? $faturamentoMes / $totalPedidosMes : 0;

/* Quebra por forma de pagamento */
$porPagamento = ['pix' => 0, 'cartao' => 0, 'boleto' => 0];
foreach ($pedidos as $p) {
    if (isset($porPagamento[$p['forma_pagamento']])) {
        $porPagamento[$p['forma_pagamento']] += $p['total'];
    }
}

/* Faturamento por dia do mês, para o gráfico */
$diasNoMes = (int)date('t', mktime(0, 0, 0, $mes, 1, $ano));
$porDia = array_fill(1, $diasNoMes, 0);
foreach ($pedidos as $p) {
    $dia = (int)date('j', strtotime($p['criado_em']));
    $porDia[$dia] += $p['total'];
}

$mesesPt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Financeiro | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="clientes.php">Clientes</a>
        <a href="cupons.php">Cupons</a>
        <a href="financeiro.php" class="ativo">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <h2>Vendas</h2>

    <form method="get" style="display:flex; gap:10px; margin-bottom:24px;">
        <select name="mes">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m === $mes ? 'selected' : '' ?>><?= $mesesPt[$m] ?></option>
            <?php endfor; ?>
        </select>
        <select name="ano">
            <?php for ($a = date('Y'); $a >= date('Y') - 3; $a--): ?>
                <option value="<?= $a ?>" <?= $a === $ano ? 'selected' : '' ?>><?= $a ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <div class="finance-grid">
        <div class="card-fin azul">
            <h3>Faturamento do mês</h3>
            <p class="valor">R$ <?= number_format($faturamentoMes, 2, ',', '.') ?></p>
        </div>
        <div class="card-fin verde">
            <h3>Pedidos no mês</h3>
            <p class="valor"><?= $totalPedidosMes ?></p>
        </div>
        <div class="card-fin amarelo">
            <h3>Ticket médio</h3>
            <p class="valor">R$ <?= number_format($ticketMedio, 2, ',', '.') ?></p>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1fr; margin-top:26px;">
        <div class="card">
            <h3>Faturamento por dia</h3>
            <canvas id="graficoVendas" height="90"></canvas>
        </div>
        <div class="card">
            <h3>Forma de pagamento</h3>
            <p style="margin-top:10px;">🔵 Pix: R$ <?= number_format($porPagamento['pix'], 2, ',', '.') ?></p>
            <p>🟣 Cartão: R$ <?= number_format($porPagamento['cartao'], 2, ',', '.') ?></p>
            <p>🟠 Boleto: R$ <?= number_format($porPagamento['boleto'], 2, ',', '.') ?></p>
        </div>
    </div>

    <h3 style="margin-top:30px;">Pedidos do período</h3>
    <table style="margin-top:10px;">
        <thead>
            <tr><th>Pedido</th><th>Cliente</th><th>Data</th><th>Pagamento</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php if ($pedidos): ?>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td data-label="Pedido">#<?= $p['id'] ?></td>
                    <td data-label="Cliente"><?= htmlspecialchars($p['cliente_nome'] ?? '—') ?></td>
                    <td data-label="Data"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                    <td data-label="Pagamento"><?= ['pix'=>'Pix','cartao'=>'Cartão','boleto'=>'Boleto'][$p['forma_pagamento']] ?? '—' ?></td>
                    <td data-label="Total">R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                    <td data-label="Status"><span class="status pago"><?= htmlspecialchars($p['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; padding:40px; color:#999;">Nenhuma venda neste período.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<script>
const ctx = document.getElementById('graficoVendas');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_keys($porDia)) ?>],
        datasets: [{
            label: 'Faturamento (R$)',
            data: [<?= implode(',', array_map(fn($v) => number_format($v, 2, '.', ''), $porDia)) ?>],
            backgroundColor: '#5b7cfa',
            hoverBackgroundColor: '#3d56d6',
            borderRadius: 6,
            maxBarThickness: 42
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#eef0f8' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

</body>
</html>
