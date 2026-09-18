<?php
session_start();

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/config.php';

$id_cliente = $_GET['id'] ?? null;
if (!$id_cliente) { header("Location: financeiro.php"); exit; }

/* --- LÓGICA DE ALTERAR STATUS --- */
if (isset($_GET['mudar_status']) && isset($_GET['parcela_id'])) {
    $stmt = $pdo->prepare("UPDATE financeiro SET status = ? WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$_GET['mudar_status'], $_GET['parcela_id'], $id_cliente]);
    header("Location: ver-financeiro.php?id=$id_cliente");
    exit;
}

/* --- LÓGICA DE EXCLUIR CONTRATO (VOLTA PARA ESTOQUE E APAGA FINANCEIRO) --- */
if (isset($_GET['excluir_contrato']) && isset($_GET['tag'])) {
    $tag_excluir = $_GET['tag'];

    try {
        $pdo->beginTransaction();

        // 1. Deleta as parcelas financeiras deste equipamento para este cliente
        $stmt = $pdo->prepare("DELETE FROM financeiro WHERE cliente_id = ? AND descricao LIKE ?");
        $stmt->execute([$id_cliente, "%".$tag_excluir."%"]);

        // 2. CORREÇÃO: Volta o equipamento para o estoque (limpa cliente_id e muda status)
        $stmt_up = $pdo->prepare("UPDATE equipamentos SET cliente_id = NULL, status = 'disponivel' WHERE codigo_equipamento = ?");
        $stmt_up->execute([$tag_excluir]);

        $pdo->commit();

        header("Location: ver-financeiro.php?id=$id_cliente&msg=contrato_encerrado");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao encerrar contrato: " . $e->getMessage());
    }
}

/* DADOS */
$stmt_cli = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt_cli->execute([$id_cliente]);
$cliente = $stmt_cli->fetch();

$stmt_equips = $pdo->prepare("SELECT * FROM equipamentos WHERE cliente_id = ?");
$stmt_equips->execute([$id_cliente]);
$equipamentos = $stmt_equips->fetchAll();

$stmt_fin = $pdo->prepare("SELECT * FROM financeiro WHERE cliente_id = ? ORDER BY data_vencimento ASC");
$stmt_fin->execute([$id_cliente]);
$todas_parcelas = $stmt_fin->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão Financeira | VaultCore</title>
<link rel="stylesheet" href="assets/style.css">
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
    <div class="cliente-header">
        <h2><?= htmlspecialchars($cliente['nome_fantasia']) ?></h2>
        <p>CNPJ: <?= $cliente['cnpj'] ?> | Razão Social: <?= htmlspecialchars($cliente['razao_social']) ?></p>
    </div>

    <div class="grid-ativos">
        <?php foreach($equipamentos as $e): ?>
        <div class="card-ativo">
            <h3><?= htmlspecialchars($e['codigo_equipamento']) ?></h3>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                Modelo: <?= !empty($e['modelo']) ? htmlspecialchars($e['modelo']) : 'Modelo não informado' ?>
            </p>
            <button class="btn-ver" onclick="filtrarParcelas('<?= $e['codigo_equipamento'] ?>')">VER MENSALIDADES</button>
            <a href="?id=<?= $id_cliente ?>&excluir_contrato=1&tag=<?= $e['codigo_equipamento'] ?>" 
               class="btn-del-contrato" onclick="return confirm('Excluir todas as parcelas deste equipamento e retorná-lo ao estoque?')">Excluir Contrato</a>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="area-financeira">
        <h3 id="titulo-tab" style="margin-bottom: 15px; color: #152534;"></h3>
        <table>
            <thead>
                <tr>
                    <th>Vencimento</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ação de Status</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela"></tbody>
        </table>
    </div>
</main>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura
</footer>

<script>
const parcelas = <?= json_encode($todas_parcelas) ?>;
const cliId = '<?= $id_cliente ?>';

function filtrarParcelas(tag) {
    const area = document.getElementById('area-financeira');
    const corpo = document.getElementById('corpo-tabela');
    document.getElementById('titulo-tab').innerText = "Mensalidades: " + tag;
    
    corpo.innerHTML = "";
    const filtradas = parcelas.filter(p => p.descricao.includes(tag));

    filtradas.forEach(p => {
        let btn = p.status === 'pago' 
            ? `<a href="?id=${cliId}&mudar_status=pendente&parcela_id=${p.id}" class="btn-status bg-pendente">MARCAR PENDENTE</a>`
            : `<a href="?id=${cliId}&mudar_status=pago&parcela_id=${p.id}" class="btn-status bg-pago">MARCAR PAGO</a>`;

        corpo.innerHTML += `
            <tr>
                <td>${p.data_vencimento}</td>
                <td>${p.descricao}</td>
                <td style="font-weight:bold;">R$ ${p.valor}</td>
                <td><span class="status-pill ${p.status}">${p.status.toUpperCase()}</span></td>
                <td>${btn}</td>
            </tr>`;
    });

    area.style.display = 'block';
    area.scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>