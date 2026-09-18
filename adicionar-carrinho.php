<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php?voltar=index.php');
    exit;
}

$produtoId  = (int)($_POST['produto_id'] ?? 0);
$quantidade = max(1, (int)($_POST['quantidade'] ?? 1));

$stmt = $pdo->prepare("SELECT id, estoque FROM produtos WHERE id = ? AND ativo = 1");
$stmt->execute([$produtoId]);
$produto = $stmt->fetch();

if (!$produto) {
    $_SESSION['flash_erro'] = 'Produto não encontrado.';
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$jaNoCarrinho = $_SESSION['carrinho'][$produtoId] ?? 0;
$novaQuantidade = $jaNoCarrinho + $quantidade;

// Nunca deixa o carrinho pedir mais do que existe em estoque
if ($novaQuantidade > $produto['estoque']) {
    $novaQuantidade = $produto['estoque'];
}

$_SESSION['carrinho'][$produtoId] = $novaQuantidade;

header('Location: carrinho.php');
exit;
