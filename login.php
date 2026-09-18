<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Área Administrativa | VaultCore</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container login-page">
    <h1>Área Administrativa</h1>
    <p class="sub">Acesso restrito a colaboradores</p>

    <form method="post" action="processa-login.php" class="form-login" style="display:block">
        <input type="hidden" name="tipo" value="admin">

        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" required autofocus>
        </div>

        <button class="btn-entrar full">Entrar</button>
    </form>

    <div class="link-site">
        <a href="index.php">← Voltar para o site</a>
    </div>
</div>

</body>
</html>
