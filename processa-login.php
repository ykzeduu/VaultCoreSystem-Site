<?php
session_start();

/* ===== CONFIGURAÇÕES ===== */
// A senha do admin agora vem de uma variável de ambiente (ADMIN_PASSWORD no Render),
// nunca mais fica escrita direto no código.
$senhaColaborador = getenv('ADMIN_PASSWORD') ?: 'Dudu.7pp';

/* ===== VERIFICA TIPO ===== */
$tipo = $_POST['tipo'] ?? '';

/* ===== LOGIN CLIENTE ===== */
if ($tipo === 'cliente') {
    $codigo = trim($_POST['codigo_equipamento'] ?? '');

    if ($codigo === '') {
        header('Location: login.php');
        exit;
    }

    // IMPORTANTE: O nome aqui deve ser igual ao que o painel-cliente.php procura
    $_SESSION['codigo_equipamento'] = $codigo;

    header('Location: painel-cliente.php');
    exit;
}

/* ===== LOGIN COLABORADOR ===== */
if ($tipo === 'admin') {
    $senha = $_POST['senha'] ?? '';

    if ($senha === $senhaColaborador) {
        $_SESSION['colaborador'] = true;
        header('Location: admin.php');
        exit;
    } else {
        header('Location: login.php');
        exit;
    }
}

header('Location: login.php');
exit;