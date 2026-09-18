<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php?voltar=meus-pedidos.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmt->execute([$_SESSION['loja_usuario_id']]);
$pedidos = $stmt->fetchAll();

// Busca os itens de todos os pedidos de uma vez só
$itensPorPedido = [];
if ($pedidos) {
    $ids = array_column($pedidos, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM pedido_itens WHERE pedido_id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $item) {
        $itensPorPedido[$item['pedido_id']][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus pedidos | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="index.php">Início</a>
        <a href="sobre.php">Sobre nós</a>
        <a href="garantia.php">Garantia</a>
        <a href="trabalhe-conosco.php">Trabalhe Conosco</a>
        <a href="carrinho.php">Carrinho<?php if (!empty($_SESSION['carrinho'])): ?> (<?= array_sum($_SESSION['carrinho']) ?>)<?php endif; ?></a>
        <a href="meus-pedidos.php" class="ativo">Meus Pedidos</a>
    </nav>
    <a href="loja-logout.php" class="admin-btn">Sair da conta</a>
</header>

<main>
    <h1>Meus pedidos</h1>
    <p class="sub">Olá, <?= htmlspecialchars($_SESSION['loja_usuario_nome']) ?> — aqui está o histórico das suas compras (fictícias) no site.</p>

    <?php if (!$pedidos): ?>
        <div class="cta-box" style="margin-top:24px;">
            <p>Você ainda não fez nenhum pedido.</p>
            <a href="index.php" class="btn" style="margin-top:12px; display:inline-block;">Ver produtos disponíveis</a>
        </div>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="card" style="margin-top:20px;">
                <div class="header-acoes">
                    <h3>Pedido #<?= $pedido['id'] ?> — <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></h3>
                    <span class="status pago"><?= htmlspecialchars($pedido['status']) ?></span>
                </div>
                <table>
                    <thead><tr><th>Produto</th><th>Qtd.</th><th>Valor</th></tr></thead>
                    <tbody>
                    <?php foreach ($itensPorPedido[$pedido['id']] ?? [] as $item): ?>
                        <tr>
                            <td data-label="Produto"><?= htmlspecialchars($item['produto_nome']) ?></td>
                            <td data-label="Qtd."><?= $item['quantidade'] ?></td>
                            <td data-label="Valor">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="text-align:right; margin-top:10px; font-weight:700; color:var(--azul-900);">
                    Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                    <?php if ($pedido['desconto'] > 0): ?>
                        <span class="info-secundaria" style="font-weight:400;">(desconto de R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?><?= $pedido['cupom_codigo'] ? ' — ' . htmlspecialchars($pedido['cupom_codigo']) : '' ?>)</span>
                    <?php endif; ?>
                </p>
                <p class="info-secundaria">Pagamento: <?= ['pix'=>'Pix','cartao'=>'Cartão de crédito','boleto'=>'Boleto'][$pedido['forma_pagamento']] ?? '—' ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
