<?php
session_start();
require __DIR__ . '/includes/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nome, senha_hash, cliente_id, perfil_completo FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {

        // "Cura" contas antigas (criadas antes do vínculo com a tabela de
        // clientes existir): cria o registro de cliente agora, na hora do login.
        if (empty($usuario['cliente_id'])) {
            $stmt = $pdo->prepare("INSERT INTO clientes (razao_social, contato_principal) VALUES (?, ?)");
            $stmt->execute([$usuario['nome'], $email]);
            $usuario['cliente_id'] = $pdo->lastInsertId();

            $pdo->prepare("UPDATE usuarios SET cliente_id = ? WHERE id = ?")
                ->execute([$usuario['cliente_id'], $usuario['id']]);
        }

        $_SESSION['loja_usuario_id']      = $usuario['id'];
        $_SESSION['loja_usuario_nome']    = $usuario['nome'];
        $_SESSION['loja_cliente_id']      = $usuario['cliente_id'];
        $_SESSION['loja_perfil_completo'] = (bool)$usuario['perfil_completo'];

        $destino = $_GET['voltar'] ?? 'index.php';
        header('Location: ' . $destino);
        exit;
    } else {
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="alternate icon" href="assets/img/favicon.ico">
<title>Entrar | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container login-page">
    <h1>Entrar</h1>
    <p class="sub">Acesse sua conta para comprar equipamentos</p>

    <?php if ($erro): ?>
        <div class="alerta"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" class="form-login" style="display:block">
        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>

        <button class="btn-entrar full">Entrar</button>
    </form>

    <div class="link-site">
        Ainda não tem conta? <a href="loja-cadastro.php">Criar conta</a><br>
        <a href="index.php">← Voltar para o site</a>
    </div>
</div>

</body>
</html>
