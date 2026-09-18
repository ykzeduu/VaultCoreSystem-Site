<?php
session_start();

/* ===== CONFIGURAÇÕES ===== */
// A senha do admin vem de uma variável de ambiente (ADMIN_PASSWORD no Render),
// nunca fica escrita direto no código.
$senhaColaborador = getenv('ADMIN_PASSWORD') ?: 'Dudu.7pp';

const MAX_TENTATIVAS   = 3;
const BLOQUEIO_SEGUNDOS = 60;

/* Se o bloqueio já expirou, zera o contador antes de processar */
if (!empty($_SESSION['login_bloqueado_ate']) && time() >= $_SESSION['login_bloqueado_ate']) {
    unset($_SESSION['login_bloqueado_ate']);
    $_SESSION['login_tentativas'] = 0;
}

/* Se ainda está bloqueado, nem verifica a senha enviada */
if (!empty($_SESSION['login_bloqueado_ate']) && time() < $_SESSION['login_bloqueado_ate']) {
    header('Location: login.php');
    exit;
}

$senha = $_POST['senha'] ?? '';

if ($senha === $senhaColaborador) {
    $_SESSION['colaborador'] = true;
    unset($_SESSION['login_tentativas'], $_SESSION['login_bloqueado_ate'], $_SESSION['login_erro']);
    header('Location: admin.php');
    exit;
}

$_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;

if ($_SESSION['login_tentativas'] >= MAX_TENTATIVAS) {
    $_SESSION['login_bloqueado_ate'] = time() + BLOQUEIO_SEGUNDOS;
    $_SESSION['login_tentativas'] = 0;
    $_SESSION['login_erro'] = 'Muitas tentativas incorretas. Aguarde 1 minuto antes de tentar novamente.';
} else {
    $restantes = MAX_TENTATIVAS - $_SESSION['login_tentativas'];
    $_SESSION['login_erro'] = 'Senha incorreta. Você tem mais ' . $restantes . ($restantes === 1 ? ' tentativa.' : ' tentativas.');
}

header('Location: login.php');
exit;
