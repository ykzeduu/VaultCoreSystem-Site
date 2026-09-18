<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id'])) {
    header('Location: loja-login.php?voltar=index.php');
    exit;
}

$produtoId  = (int)($_POST['produto_id'] ?? 0);
$quantidade = max(1, (int)($_POST['quantidade'] ?? 1));

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
$stmt->execute([$produtoId]);
$produto = $stmt->fetch();

if (!$produto) {
    $_SESSION['flash_erro'] = 'Produto não encontrado.';
    header('Location: index.php');
    exit;
}

if ($produto['estoque'] < $quantidade) {
    $_SESSION['flash_erro'] = 'Não há estoque suficiente de "' . $produto['nome'] . '" no momento.';
    header('Location: index.php');
    exit;
}

// Transação: cria o pedido, o item do pedido, e desconta o estoque juntos.
$pdo->beginTransaction();
try {
    $total = $produto['preco'] * $quantidade;

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, status) VALUES (?, ?, 'confirmado')");
    $stmt->execute([$_SESSION['loja_usuario_id'], $total]);
    $pedidoId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, produto_nome, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$pedidoId, $produto['id'], $produto['nome'], $quantidade, $produto['preco']]);

    $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");
    $stmt->execute([$quantidade, $produto['id'], $quantidade]);

    if ($stmt->rowCount() === 0) {
        // Alguém comprou ao mesmo tempo e o estoque acabou entre a checagem e agora.
        throw new Exception('estoque_insuficiente');
    }

    $pdo->commit();
    header('Location: pedido-confirmado.php?id=' . $pedidoId);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_erro'] = 'Não foi possível concluir a compra. Tente novamente.';
    header('Location: index.php');
    exit;
}
