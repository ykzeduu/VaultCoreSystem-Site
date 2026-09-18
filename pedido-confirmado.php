<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php');
    exit;
}

$pedidoId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$pedidoId, $_SESSION['loja_usuario_id']]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pedido_itens WHERE pedido_id = ?");
$stmt->execute([$pedidoId]);
$itens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="alternate icon" href="assets/img/favicon.ico">
<title>Pedido confirmado | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container" style="max-width:560px; padding-top:90px;">
    <div class="card" style="text-align:center;">
        <div style="font-size:44px;">✅</div>
        <h1>Pedido confirmado!</h1>
        <p class="sub">Pedido <strong>#<?= $pedido['id'] ?></strong> — <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></p>

        <div class="alerta" style="background:var(--info-bg); color:var(--info); border-color:#bcdff5; margin-top:16px;">
            Este é um site de demonstração. Nenhuma cobrança real foi feita — a compra é fictícia.
        </div>

        <table style="margin-top:20px; text-align:left;">
            <thead><tr><th>Produto</th><th>Qtd.</th><th>Valor</th></tr></thead>
            <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td data-label="Produto"><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td data-label="Qtd."><?= $item['quantidade'] ?></td>
                    <td data-label="Valor">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top:16px;">Subtotal: R$ <?= number_format($pedido['subtotal'], 2, ',', '.') ?></p>
        <?php if ($pedido['desconto'] > 0): ?>
            <p>Desconto<?= $pedido['cupom_codigo'] ? ' (' . htmlspecialchars($pedido['cupom_codigo']) . ')' : '' ?>: -R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?></p>
        <?php endif; ?>
        <p style="font-size:20px; font-weight:700; color:var(--azul-900);">
            Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
        </p>
        <p class="info-secundaria">Forma de pagamento: <?= ['pix'=>'Pix','cartao'=>'Cartão de crédito','boleto'=>'Boleto'][$pedido['forma_pagamento']] ?? $pedido['forma_pagamento'] ?></p>

        <div style="margin-top:24px; display:flex; gap:12px; justify-content:center;">
            <a href="index.php" class="btn">Voltar à loja</a>
            <a href="meus-pedidos.php" class="btn-editar">Ver meus pedidos</a>
        </div>
    </div>
</div>

</body>
</html>
