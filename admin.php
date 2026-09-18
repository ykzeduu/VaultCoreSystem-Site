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

require __DIR__ . '/includes/config.php';

$totalProdutos   = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalClientes   = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalPedidos    = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$faturamento     = $pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos")->fetchColumn();
$estoqueBaixo    = $pdo->query("SELECT COUNT(*) FROM produtos WHERE ativo = 1 AND estoque <= 2")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel Administrativo | VaultCore</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php" class="ativo">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="clientes.php">Clientes</a>
        <a href="cupons.php">Cupons</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<section class="conteudo">
    <h1>Bem-vindo, Colaborador</h1>
    <p class="subtitulo">Central de comando da loja VaultCore.</p>

    <div class="finance-grid" style="margin-top:26px;">
        <div class="card-fin azul">
            <h3>Faturamento total</h3>
            <p class="valor">R$ <?= number_format($faturamento, 2, ',', '.') ?></p>
        </div>
        <div class="card-fin verde">
            <h3>Pedidos confirmados</h3>
            <p class="valor"><?= $totalPedidos ?></p>
        </div>
        <div class="card-fin amarelo">
            <h3>Produtos com estoque baixo</h3>
            <p class="valor"><?= $estoqueBaixo ?></p>
        </div>
    </div>

    <div class="cards">
        <h2>Áreas do sistema</h2>

        <div class="grid-cards">
            <div class="card">
                <div class="icon">🖥️</div>
                <h2>Produtos</h2>
                <p><?= $totalProdutos ?> produto(s) cadastrado(s). Gerencie preço, estoque e fotos do catálogo.</p>
                <a href="produtos.php" class="btn-card">GERENCIAR PRODUTOS</a>
            </div>

            <div class="card">
                <div class="icon">🧑‍💼</div>
                <h2>Clientes</h2>
                <p><?= $totalClientes ?> cliente(s) cadastrado(s), a partir das contas criadas no site.</p>
                <a href="clientes.php" class="btn-card">VER CLIENTES</a>
            </div>

            <div class="card">
                <div class="icon">🏷️</div>
                <h2>Cupons</h2>
                <p>Crie e gerencie cupons de desconto usados no carrinho da loja.</p>
                <a href="cupons.php" class="btn-card">GERENCIAR CUPONS</a>
            </div>

            <div class="card">
                <div class="icon">📊</div>
                <h2>Financeiro</h2>
                <p>Acompanhe as vendas, a forma de pagamento usada e o faturamento por período.</p>
                <a href="financeiro.php" class="btn-card">VER VENDAS</a>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-text">
            <h3>Como funciona a loja?</h3>
            <p>
                O cliente cria uma conta no site, completa o cadastro (documento,
                endereço) e compra diretamente pelo catálogo. Tudo aqui é
                fictício — nenhuma cobrança real é processada.
            </p>
        </div>
        <ul class="info-list">
            <li><strong>Catálogo:</strong> o estoque mostrado ao cliente vem direto da tabela de produtos.</li>
            <li><strong>Cadastro automático:</strong> toda conta criada no site já entra na base de clientes.</li>
            <li><strong>Checkout:</strong> o cliente escolhe Pix, cartão ou boleto (fictícios) para "pagar".</li>
            <li><strong>Cupons:</strong> descontos aplicados no carrinho antes de fechar o pedido.</li>
        </ul>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Loja de computadores
</footer>

</body>
</html>
