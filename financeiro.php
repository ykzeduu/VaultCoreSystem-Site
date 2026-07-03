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
$pdo = new PDO(
    "mysql:host=sql311.infinityfree.com;dbname=if0_41023013_db_clientes;charset=utf8",
    "if0_41023013",
    "2kVHu71TF3ly",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

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
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
    html, body { height: 100%; }

    body {
        background: #f2f4f6;
        color: #1f2937;
        overflow-y: scroll; /* BARRINHA SEMPRE VISÍVEL */
        display: flex;
        flex-direction: column;
    }
    header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 600; color: #ffffff; }
    nav { display: flex; align-items: center; gap: 30px; }
    nav a { color: #d1d5db; text-decoration: none; font-size: 15px; padding-bottom: 6px; border-bottom: 3px solid transparent; transition: 0.2s; }
    nav a.ativo { color: #65c9d1; border-bottom: 3px solid #65c9d1; }
    .logout-btn { background: #1e899e; color: #ffffff; padding: 8px 18px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; }

    main { padding: 170px 70px 50px; max-width: 1400px; margin: auto; width: 100%; flex: 1; }

    .header-acoes { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    
    /* BARRA DE PESQUISA PADRÃO */
    .search-box { background: #fff; padding: 20px; border-radius: 16px; margin-bottom: 30px; display: flex; gap: 10px; align-items: center; border: 2px solid #1e899e; }
    .search-box input, .search-box select { padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; }
    .search-box button { padding: 12px 25px; background: #152534; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

    .finance-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px; }
    .card-fin { background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .card-fin h3 { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-bottom: 10px; font-weight: 800; }
    .card-fin .valor { font-size: 28px; font-weight: 700; color: #152534; }
    .card-fin.verde { border-left: 6px solid #10b981; }
    .card-fin.amarelo { border-left: 6px solid #f59e0b; }
    .card-fin.azul { border-left: 6px solid #1e899e; }

    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    th, td { padding: 18px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { background: #f9fafb; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }

    .btn-detalhes { background: #152534; color: #fff; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.2s; }
    .btn-detalhes:hover { background: #1e899e; }

    .btn-novo { background: #1e899e; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; }
    
    footer { background: #152534; color: #ffffff; text-align: center; padding: 26px; font-size: 14px; margin-top: 40px; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
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