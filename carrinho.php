<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php?voltar=carrinho.php');
    exit;
}

$erroCupom = '';

/* Aplicar cupom */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aplicar_cupom'])) {
    $codigo = strtoupper(trim($_POST['codigo_cupom'] ?? ''));

    if ($codigo === '') {
        unset($_SESSION['cupom']);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM cupons WHERE codigo = ? AND ativo = 1");
        $stmt->execute([$codigo]);
        $cupom = $stmt->fetch();

        if (!$cupom) {
            $erroCupom = 'Cupom inválido ou expirado.';
            unset($_SESSION['cupom']);
        } else {
            $_SESSION['cupom'] = $cupom;
        }
    }
}

/* Remover item */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_item'])) {
    $produtoId = (int)$_POST['remover_item'];
    unset($_SESSION['carrinho'][$produtoId]);
}

/* Monta os itens do carrinho com dados atuais do produto */
$itens = [];
$subtotal = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtosBanco = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

    foreach ($_SESSION['carrinho'] as $produtoId => $quantidade) {
        if (!isset($produtosBanco[$produtoId])) continue;
        $p = $produtosBanco[$produtoId];
        $p['id'] = $produtoId; // FETCH_UNIQUE remove o id da linha (vira a chave do array), então devolvemos ele aqui
        $quantidade = min($quantidade, $p['estoque']); // nunca deixa passar do estoque atual
        if ($quantidade <= 0) continue;

        $itens[] = [
            'produto' => $p,
            'quantidade' => $quantidade,
            'total' => $p['preco'] * $quantidade,
        ];
        $subtotal += $p['preco'] * $quantidade;
    }
}

$desconto = 0;
$cupom = $_SESSION['cupom'] ?? null;
if ($cupom) {
    $desconto = $cupom['tipo'] === 'percentual'
        ? $subtotal * ($cupom['valor'] / 100)
        : min($cupom['valor'], $subtotal);
}
$total = $subtotal - $desconto;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="alternate icon" href="assets/img/favicon.ico">
<title>Carrinho | VaultCore</title>
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
        <a href="carrinho.php" class="ativo">Carrinho</a>
        <a href="meus-pedidos.php">Meus Pedidos</a>
    </nav>
    <a href="loja-logout.php" class="admin-btn">Sair da conta</a>
</header>

<main class="container">
    <h1>Seu carrinho</h1>

    <?php if (!empty($_SESSION['flash_erro'])): ?>
        <div class="alerta"><?= htmlspecialchars($_SESSION['flash_erro']) ?></div>
        <?php unset($_SESSION['flash_erro']); ?>
    <?php endif; ?>

    <?php if (!$itens): ?>
        <div class="cta-box" style="margin-top:24px;">
            <p>Seu carrinho está vazio.</p>
            <a href="index.php" class="btn" style="margin-top:12px; display:inline-block;">Ver produtos</a>
        </div>
    <?php else: ?>

        <table style="margin-top:20px;">
            <thead>
                <tr><th>Produto</th><th>Qtd.</th><th>Preço</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                <tr>
                    <td data-label="Produto">
                        <strong><?= htmlspecialchars($item['produto']['nome']) ?></strong>
                        <span class="info-secundaria"><?= htmlspecialchars($item['produto']['especificacoes']) ?></span>
                    </td>
                    <td data-label="Qtd."><?= $item['quantidade'] ?></td>
                    <td data-label="Preço">R$ <?= number_format($item['produto']['preco'], 2, ',', '.') ?></td>
                    <td data-label="Subtotal">R$ <?= number_format($item['total'], 2, ',', '.') ?></td>
                    <td data-label="">
                        <form method="post">
                            <button type="submit" name="remover_item" value="<?= $item['produto']['id'] ?>" class="btn-delete" style="padding:6px 12px; font-size:12px;">Remover</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cliente-select" style="margin-top:24px;">
            <label>Cupom de desconto</label>
            <?php if ($erroCupom): ?><div class="alerta"><?= htmlspecialchars($erroCupom) ?></div><?php endif; ?>
            <form method="post" style="display:flex; gap:10px;">
                <input type="text" name="codigo_cupom" placeholder="Ex: BEMVINDO10" value="<?= htmlspecialchars($cupom['codigo'] ?? '') ?>" style="flex:1;">
                <button type="submit" name="aplicar_cupom" value="1" class="btn">Aplicar</button>
            </form>
            <?php if ($cupom): ?>
                <span class="subtotal-tag" style="margin-top:10px;">Cupom "<?= htmlspecialchars($cupom['codigo']) ?>" aplicado</span>
            <?php endif; ?>
        </div>

        <div class="resumo-fixo">
            <div>
                <p>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
                <?php if ($desconto > 0): ?>
                    <p>Desconto: -R$ <?= number_format($desconto, 2, ',', '.') ?></p>
                <?php endif; ?>
                <h2 style="color:#fff; margin-top:6px;">Total: R$ <?= number_format($total, 2, ',', '.') ?></h2>
            </div>
            <a href="checkout.php" class="btn" style="background:var(--ciano);">Finalizar Compra</a>
        </div>

    <?php endif; ?>
</main>

</body>
</html>
