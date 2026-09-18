<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

$formaPagamento = $_POST['forma_pagamento'] ?? 'pix';
if (!in_array($formaPagamento, ['pix', 'cartao', 'boleto'])) {
    $formaPagamento = 'pix';
}

$pdo->beginTransaction();
try {
    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    // Trava as linhas dos produtos até o fim da transação, evitando duas compras simultâneas estourarem o estoque
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($ids);
    $produtosBanco = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

    $itensValidos = [];
    $subtotal = 0;

    foreach ($_SESSION['carrinho'] as $produtoId => $quantidade) {
        if (!isset($produtosBanco[$produtoId])) continue;
        $p = $produtosBanco[$produtoId];
        $p['id'] = $produtoId; // FETCH_UNIQUE remove o id da linha (vira a chave do array), então devolvemos ele aqui
        if ($quantidade > $p['estoque']) {
            throw new Exception('Estoque insuficiente para "' . $p['nome'] . '".');
        }
        $itensValidos[] = ['produto' => $p, 'quantidade' => $quantidade];
        $subtotal += $p['preco'] * $quantidade;
    }

    if (!$itensValidos) {
        throw new Exception('Carrinho vazio.');
    }

    $desconto = 0;
    $cupomCodigo = null;
    $cupom = $_SESSION['cupom'] ?? null;
    if ($cupom) {
        $desconto = $cupom['tipo'] === 'percentual' ? $subtotal * ($cupom['valor'] / 100) : min($cupom['valor'], $subtotal);
        $cupomCodigo = $cupom['codigo'];
    }
    $total = $subtotal - $desconto;

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, subtotal, desconto, total, cupom_codigo, forma_pagamento, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmado')");
    $stmt->execute([$_SESSION['loja_usuario_id'], $subtotal, $desconto, $total, $cupomCodigo, $formaPagamento]);
    $pedidoId = $pdo->lastInsertId();

    foreach ($itensValidos as $item) {
        $p = $item['produto'];
        $qtd = $item['quantidade'];

        $stmt = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, produto_nome, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$pedidoId, $p['id'], $p['nome'], $qtd, $p['preco']]);

        $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");
        $stmt->execute([$qtd, $p['id'], $qtd]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Estoque insuficiente para "' . $p['nome'] . '".');
        }
    }

    $pdo->commit();

    unset($_SESSION['carrinho'], $_SESSION['cupom']);

    header('Location: pedido-confirmado.php?id=' . $pedidoId);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_erro'] = $e->getMessage() ?: 'Não foi possível concluir a compra.';
    header('Location: carrinho.php');
    exit;
}
