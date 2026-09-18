<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php?voltar=checkout.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

/* Recalcula os totais (mesma lógica do carrinho) só para exibir aqui */
$ids = array_keys($_SESSION['carrinho']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtosBanco = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$subtotal = 0;
foreach ($_SESSION['carrinho'] as $produtoId => $quantidade) {
    if (!isset($produtosBanco[$produtoId])) continue;
    $quantidade = min($quantidade, $produtosBanco[$produtoId]['estoque']);
    $subtotal += $produtosBanco[$produtoId]['preco'] * $quantidade;
}

$desconto = 0;
$cupom = $_SESSION['cupom'] ?? null;
if ($cupom) {
    $desconto = $cupom['tipo'] === 'percentual' ? $subtotal * ($cupom['valor'] / 100) : min($cupom['valor'], $subtotal);
}
$total = $subtotal - $desconto;

// Gera uma "linha digitável" e um número de pedido fictício só para exibição
$linhaDigitavel = '34191.79001 01043.510047 91020.150008 4 ' . rand(10000000000, 99999999999);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Checkout | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <a href="carrinho.php" style="color:#fff; text-decoration:none; font-weight:bold;">← VOLTAR AO CARRINHO</a>
</header>

<main class="container" style="max-width:640px;">
    <h1>Finalizar compra</h1>
    <div class="alerta" style="background:var(--info-bg); color:var(--info); border-color:#bcdff5;">
        Ambiente de demonstração — nenhuma cobrança real é feita, nenhum dado de pagamento é processado de verdade.
    </div>

    <div class="card" style="margin:20px 0;">
        <p>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
        <?php if ($desconto > 0): ?><p>Desconto (<?= htmlspecialchars($cupom['codigo']) ?>): -R$ <?= number_format($desconto, 2, ',', '.') ?></p><?php endif; ?>
        <h2>Total: R$ <?= number_format($total, 2, ',', '.') ?></h2>
    </div>

    <form method="post" action="finalizar-pedido.php" id="formCheckout">
        <p class="secao-titulo">Forma de pagamento</p>

        <div class="escolha" style="justify-content:flex-start;">
            <label class="btn-escolha" style="cursor:pointer;">
                <input type="radio" name="forma_pagamento" value="pix" checked onchange="mostrarPagamento()"> Pix
            </label>
            <label class="btn-escolha" style="cursor:pointer;">
                <input type="radio" name="forma_pagamento" value="cartao" onchange="mostrarPagamento()"> Cartão
            </label>
            <label class="btn-escolha" style="cursor:pointer;">
                <input type="radio" name="forma_pagamento" value="boleto" onchange="mostrarPagamento()"> Boleto
            </label>
        </div>

        <!-- PIX -->
        <div id="pagamento-pix" class="card" style="margin-top:20px; text-align:center;">
            <p><strong>Escaneie o QR Code para pagar via Pix</strong></p>
            <svg viewBox="0 0 120 120" width="180" height="180" style="margin:14px auto; display:block; background:#fff; border:1px solid var(--borda); border-radius:8px;">
                <?php
                srand(42); // sempre o mesmo padrão "aleatório", só pra parecer um QR code
                for ($y = 0; $y < 12; $y++) {
                    for ($x = 0; $x < 12; $x++) {
                        if (rand(0, 100) > 45) {
                            echo "<rect x='" . ($x*10+5) . "' y='" . ($y*10+5) . "' width='9' height='9' fill='#152534'/>";
                        }
                    }
                }
                ?>
                <rect x="5" y="5" width="25" height="25" fill="none" stroke="#152534" stroke-width="4"/>
                <rect x="90" y="5" width="25" height="25" fill="none" stroke="#152534" stroke-width="4"/>
                <rect x="5" y="90" width="25" height="25" fill="none" stroke="#152534" stroke-width="4"/>
            </svg>
            <p class="info-secundaria">Código Pix (copia e cola) — fictício, não funciona de verdade:</p>
            <input type="text" readonly value="00020126580014BR.GOV.BCB.PIX0000FICTICIO520400005303986540<?= number_format($total,2,'.','') ?>5802BR" style="text-align:center; font-size:12px;">
        </div>

        <!-- CARTÃO -->
        <div id="pagamento-cartao" class="card" style="margin-top:20px; display:none;">
            <div class="alerta">Não insira dados reais de cartão — este formulário é apenas para demonstração e não processa pagamentos.</div>
            <div class="form-group">
                <label>Número do cartão</label>
                <input type="text" placeholder="0000 0000 0000 0000" maxlength="19">
            </div>
            <div style="display:flex; gap:12px;">
                <div class="form-group" style="flex:1;">
                    <label>Validade</label>
                    <input type="text" placeholder="MM/AA" maxlength="5">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>CVV</label>
                    <input type="text" placeholder="000" maxlength="4">
                </div>
            </div>
            <div class="form-group">
                <label>Nome impresso no cartão</label>
                <input type="text" placeholder="Nome como está no cartão">
            </div>
        </div>

        <!-- BOLETO -->
        <div id="pagamento-boleto" class="card" style="margin-top:20px; display:none; text-align:center;">
            <p><strong>Boleto gerado (fictício)</strong></p>
            <svg viewBox="0 0 300 60" width="100%" height="60" style="margin:14px 0; background:#fff;">
                <?php
                srand(7);
                $x = 5;
                while ($x < 295) {
                    $w = rand(1,4);
                    if (rand(0,1)) echo "<rect x='$x' y='5' width='$w' height='50' fill='#152534'/>";
                    $x += $w + rand(1,3);
                }
                ?>
            </svg>
            <p class="info-secundaria">Linha digitável:</p>
            <input type="text" readonly value="<?= htmlspecialchars($linhaDigitavel) ?>" style="text-align:center; font-size:12px;">
            <p class="info-secundaria" style="margin-top:8px;">Vencimento fictício: <?= date('d/m/Y', strtotime('+3 days')) ?></p>
        </div>

        <button type="submit" class="btn-save full" style="margin-top:24px;">Confirmar pedido</button>
    </form>
</main>

<script>
function mostrarPagamento() {
    const forma = document.querySelector('input[name="forma_pagamento"]:checked').value;
    document.getElementById('pagamento-pix').style.display = forma === 'pix' ? 'block' : 'none';
    document.getElementById('pagamento-cartao').style.display = forma === 'cartao' ? 'block' : 'none';
    document.getElementById('pagamento-boleto').style.display = forma === 'boleto' ? 'block' : 'none';
}
</script>

</body>
</html>
