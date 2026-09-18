<?php
session_start();
require __DIR__ . '/includes/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Já existe uma conta com esse e-mail.';
        } else {
            $pdo->beginTransaction();

            // Cria também o registro na tabela de clientes (mesma usada no CRM do admin),
            // já unificando quem compra na loja com a base de clientes da empresa.
            $stmt = $pdo->prepare("INSERT INTO clientes (razao_social, contato_principal) VALUES (?, ?)");
            $stmt->execute([$nome, $email]);
            $clienteId = $pdo->lastInsertId();

            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, cliente_id, perfil_completo) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$nome, $email, $hash, $clienteId]);
            $usuarioId = $pdo->lastInsertId();

            $pdo->commit();

            $_SESSION['loja_usuario_id']      = $usuarioId;
            $_SESSION['loja_usuario_nome']    = $nome;
            $_SESSION['loja_cliente_id']      = $clienteId;
            $_SESSION['loja_perfil_completo'] = false;

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Criar conta | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container login-page">
    <h1>Criar conta</h1>
    <p class="sub">Cadastre-se para comprar equipamentos no site</p>

    <?php if ($erro): ?>
        <div class="alerta"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" class="form-login" style="display:block">
        <div class="form-group">
            <label>Nome completo</label>
            <input type="text" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Senha (mínimo 6 caracteres)</label>
            <input type="password" name="senha" required minlength="6">
        </div>

        <button class="btn-entrar full">Criar conta</button>
    </form>

    <div class="link-site">
        Já tem conta? <a href="loja-login.php">Entrar</a><br>
        <a href="index.php">← Voltar para o site</a>
    </div>
</div>

</body>
</html>
