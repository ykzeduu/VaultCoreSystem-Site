<?php
session_start();

/* Se o bloqueio por tentativas já expirou, zera o contador */
if (!empty($_SESSION['login_bloqueado_ate']) && time() >= $_SESSION['login_bloqueado_ate']) {
    unset($_SESSION['login_bloqueado_ate']);
    $_SESSION['login_tentativas'] = 0;
}

$segundosRestantes = 0;
$bloqueado = false;
if (!empty($_SESSION['login_bloqueado_ate'])) {
    $segundosRestantes = $_SESSION['login_bloqueado_ate'] - time();
    $bloqueado = $segundosRestantes > 0;
}

$erro = $_SESSION['login_erro'] ?? null;
unset($_SESSION['login_erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="alternate icon" href="assets/img/favicon.ico">
<title>Área Administrativa | VaultCore</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container login-page">
    <h1>Área Administrativa</h1>
    <p class="sub">Acesso restrito a colaboradores</p>

    <?php if ($erro && !$bloqueado): ?>
        <div class="alerta"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if ($bloqueado): ?>
        <div class="form-login" style="display:block; text-align:center;">
            <p style="font-weight:600; color: var(--perigo);">Muitas tentativas incorretas.</p>
            <p style="color: var(--texto-suave); margin-top:8px;">
                Tente novamente em <strong id="contador"><?= $segundosRestantes ?></strong> segundo(s).
            </p>
        </div>
        <script>
            (function () {
                var restante = <?= (int)$segundosRestantes ?>;
                var span = document.getElementById('contador');
                var intervalo = setInterval(function () {
                    restante--;
                    if (restante <= 0) {
                        clearInterval(intervalo);
                        window.location.reload();
                    } else {
                        span.textContent = restante;
                    }
                }, 1000);
            })();
        </script>
    <?php else: ?>
        <form method="post" action="processa-login.php" class="form-login" style="display:block">
            <input type="hidden" name="tipo" value="admin">

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required autofocus>
            </div>

            <button class="btn-entrar full">Entrar</button>
        </form>
    <?php endif; ?>

    <div class="link-site">
        <a href="index.php">← Voltar para o site</a>
    </div>
</div>

</body>
</html>
