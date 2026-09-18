<?php
session_start();

/* LOGOUT DIRETO */
if (isset($_GET['sair'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/includes/config.php';

/* FILTRO DE MÊS/ANO E BUSCA */
$mes_filtro = $_GET['mes'] ?? date('m');
$ano_filtro = $_GET['ano'] ?? date('Y');
$busca = $_GET['busca'] ?? '';

/* TOTAIS PARA OS CARDS (Mês Selecionado) */
$sql_totais = "SELECT 
    SUM(CASE WHEN status = 'pago' THEN valor ELSE 0 END) as total_recebido,
    SUM(CASE WHEN status = 'pendente' THEN valor ELSE 0 END) as total_pendente
    FROM financeiro 
    WHERE MONTH(data_vencimento) = :mes AND YEAR(data_vencimento) = :ano";

$stmt_totais = $pdo->prepare($sql_totais);
$stmt_totais->execute(['mes' => $mes_filtro, 'ano' => $ano_filtro]);
$totais = $stmt_totais->fetch();

/* LISTAGEM DE CLIENTES E VALORES DO MÊS */
// Agrupamos por cliente para saber quanto cada um deve no mês atual
$query_clientes = "SELECT 
    c.id, 
    c.nome_fantasia, 
    c.razao_social, 
    SUM(f.valor) as valor_mes,
    MIN(f.data_vencimento) as proximo_vencimento
    FROM clientes c
    INNER JOIN financeiro f ON c.id = f.cliente_id
    WHERE MONTH(f.data_vencimento) = :mes AND YEAR(f.data_vencimento) = :ano";

if ($busca !== '') {
    $query_clientes .= " AND (c.nome_fantasia LIKE :busca OR c.razao_social LIKE :busca)";
}

$query_clientes .= " GROUP BY c.id ORDER BY c.nome_fantasia ASC";

$stmt_cli = $pdo->prepare($query_clientes);
$params = ['mes' => $mes_filtro, 'ano' => $ano_filtro];
if ($busca !== '') $params['busca'] = "%$busca%";
$stmt_cli->execute($params);
$clientes_financeiro = $stmt_cli->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão Financeira | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="equipamentos.php">Equipamentos</a>
        <a href="clientes.php">Clientes</a>
        <a href="chamados.php">Chamados</a>
        <a href="financeiro.php" class="ativo">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <div>
            <h2 style="font-size: 28px; color: #152534;">Gestão de Contratos</h2>
            <p style="color: #6b7280;">Faturamento do período: <?= $mes_filtro ?>/<?= $ano_filtro ?></p>
        </div>
        <a href="lancar-fatura.php" class="btn-novo">+ NOVO CONTRATO</a>
    </div>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Pesquisar cliente..." value="<?= htmlspecialchars($busca) ?>" style="flex: 2;">
            <select name="mes">
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $mes_filtro == $m ? 'selected' : '' ?>>Mês <?= $m ?></option>
                <?php endfor; ?>
            </select>
            <select name="ano">
                <option value="2025" <?= $ano_filtro == '2025' ? 'selected' : '' ?>>2025</option>
                <option value="2026" <?= $ano_filtro == '2026' ? 'selected' : '' ?>>2026</option>
            </select>
            <button type="submit">Filtrar</button>
        </form>
    </div>

    <div class="finance-grid">
        <div class="card-fin verde">
            <h3>Recebido</h3>
            <span class="valor">R$ <?= number_format($totais['total_recebido'] ?? 0, 2, ',', '.') ?></span>
        </div>
        <div class="card-fin amarelo">
            <h3>Pendente</h3>
            <span class="valor">R$ <?= number_format($totais['total_pendente'] ?? 0, 2, ',', '.') ?></span>
        </div>
        <div class="card-fin azul">
            <h3>Total do Mês</h3>
            <span class="valor">R$ <?= number_format(($totais['total_recebido'] + $totais['total_pendente']), 2, ',', '.') ?></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente / Locatário</th>
                <th>Próximo Vencimento</th>
                <th>Total no Mês</th>
                <th style="text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes_financeiro as $c): ?>
            <tr>
                <td>
                    <div style="font-weight: bold; color: #152534;"><?= htmlspecialchars($c['nome_fantasia']) ?></div>
                    <div style="font-size: 11px; color: #9ca3af;"><?= htmlspecialchars($c['razao_social']) ?></div>
                </td>
                <td><?= date('d/m/Y', strtotime($c['proximo_vencimento'])) ?></td>
                <td style="font-weight: bold; color: #1e899e;">R$ <?= number_format($c['valor_mes'], 2, ',', '.') ?></td>
                <td style="text-align: center;">
                    <a href="ver-financeiro.php?id=<?= $c['id'] ?>" class="btn-detalhes">VERIFICAR CLIENTE</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($clientes_financeiro)): ?>
                <tr><td colspan="4" style="text-align:center; padding: 40px; color: #9ca3af;">Nenhuma movimentação para este filtro.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>© 2026 — VaultCore | Gestão de Infraestrutura</footer>

</body>
</html>