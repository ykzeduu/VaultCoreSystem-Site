<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';

$erro = '';

/* Cadastrar novo cupom */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $codigo = strtoupper(trim($_POST['codigo']));
    $tipo   = $_POST['tipo'] === 'fixo' ? 'fixo' : 'percentual';
    $valor  = str_replace(',', '.', $_POST['valor']);

    if ($codigo === '' || $valor === '') {
        $erro = 'Preencha o código e o valor do cupom.';
    } else {
        try {
            $pdo->prepare("INSERT INTO cupons (codigo, tipo, valor, ativo) VALUES (?, ?, ?, 1)")->execute([$codigo, $tipo, $valor]);
            header('Location: cupons.php');
            exit;
        } catch (PDOException $e) {
            $erro = ($e->getCode() == 23000) ? 'Já existe um cupom com esse código.' : 'Erro ao salvar o cupom.';
        }
    }
}

/* Ativar/desativar */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alternar_id'])) {
    $pdo->prepare("UPDATE cupons SET ativo = 1 - ativo WHERE id = ?")->execute([$_POST['alternar_id']]);
    header('Location: cupons.php');
    exit;
}

/* Excluir */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $pdo->prepare("DELETE FROM cupons WHERE id = ?")->execute([$_POST['excluir_id']]);
    header('Location: cupons.php');
    exit;
}

$cupons = $pdo->query("SELECT * FROM cupons ORDER BY criado_em DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cupons | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="clientes.php">Clientes</a>
        <a href="cupons.php" class="ativo">Cupons</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <h2>Cupons de desconto</h2>
    <p class="sub" style="margin-top:-14px; margin-bottom:20px;">O cliente aplica o código na tela do carrinho.</p>

    <?php if ($erro): ?><div class="alerta"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <div class="card" style="margin-bottom:26px; max-width:600px;">
        <h3>Novo cupom</h3>
        <form method="post" class="form-grid" style="margin-top:12px;">
            <div>
                <label>Código</label>
                <input type="text" name="codigo" placeholder="Ex: BLACKFRIDAY" required>
            </div>
            <div>
                <label>Tipo</label>
                <select name="tipo">
                    <option value="percentual">Percentual (%)</option>
                    <option value="fixo">Valor fixo (R$)</option>
                </select>
            </div>
            <div class="full">
                <label>Valor</label>
                <input type="text" name="valor" placeholder="Ex: 10 (para 10%) ou 100 (para R$100)" required>
            </div>
            <div class="full">
                <button type="submit" name="criar" class="btn-save">CRIAR CUPOM</button>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr><th>Código</th><th>Tipo</th><th>Valor</th><th>Status</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php if ($cupons): ?>
                <?php foreach ($cupons as $c): ?>
                <tr>
                    <td data-label="Código"><strong><?= htmlspecialchars($c['codigo']) ?></strong></td>
                    <td data-label="Tipo"><?= $c['tipo'] === 'percentual' ? 'Percentual' : 'Valor fixo' ?></td>
                    <td data-label="Valor"><?= $c['tipo'] === 'percentual' ? $c['valor'] . '%' : 'R$ ' . number_format($c['valor'], 2, ',', '.') ?></td>
                    <td data-label="Status">
                        <span class="status <?= $c['ativo'] ? 'disponivel' : 'locado' ?>"><?= $c['ativo'] ? 'Ativo' : 'Inativo' ?></span>
                    </td>
                    <td data-label="Ações" style="display:flex; gap:8px;">
                        <form method="post"><button type="submit" name="alternar_id" value="<?= $c['id'] ?>" class="btn-editar"><?= $c['ativo'] ? 'Desativar' : 'Ativar' ?></button></form>
                        <form method="post" onsubmit="return confirm('Excluir este cupom?');"><button type="submit" name="excluir_id" value="<?= $c['id'] ?>" class="btn-delete">Excluir</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:40px; color:#999;">Nenhum cupom cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
