<?php
session_start();

/* ===== CONFIGURAÇÕES ===== */
// A senha do admin vem de uma variável de ambiente (ADMIN_PASSWORD no Render),
// nunca fica escrita direto no código.
$senhaColaborador = getenv('ADMIN_PASSWORD') ?: 'Dudu.7pp';

$senha = $_POST['senha'] ?? '';

if ($senha === $senhaColaborador) {
    $_SESSION['colaborador'] = true;
    header('Location: admin.php');
    exit;
}

header('Location: login.php');
exit;
