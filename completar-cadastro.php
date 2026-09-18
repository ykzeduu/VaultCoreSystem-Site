<?php
session_start();
require __DIR__ . '/includes/config.php';

if (!isset($_SESSION['loja_usuario_id']) || !isset($_SESSION['loja_cliente_id'])) {
    header('Location: loja-login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['documento'] ?? '');
    $cep       = trim($_POST['cep'] ?? '');
    $endereco  = trim($_POST['endereco'] ?? '');
    $telefone  = trim($_POST['telefone'] ?? '');

    if ($documento === '' || $cep === '' || $endereco === '' || $telefone === '') {
        $_SESSION['flash_erro'] = 'Preencha todos os campos para concluir seu cadastro.';
        header('Location: index.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE clientes SET cnpj = ?, cep = ?, endereco = ?, contato_secundario = ? WHERE id = ?");
    $stmt->execute([$documento, $cep, $endereco, $telefone, $_SESSION['loja_cliente_id']]);

    $stmt = $pdo->prepare("UPDATE usuarios SET perfil_completo = 1 WHERE id = ?");
    $stmt->execute([$_SESSION['loja_usuario_id']]);

    $_SESSION['loja_perfil_completo'] = true;
}

header('Location: index.php');
exit;
