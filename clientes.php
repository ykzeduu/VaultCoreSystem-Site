<?php
session_start();

if (isset($_GET['sair'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';

$busca = $_GET['busca'] ?? '';

$query = "SELECT c.*, u.perfil_completo, u.email,
                 COUNT(p.id) AS total_pedidos,
                 COALESCE(SUM(p.total), 0) AS total_gasto
          FROM clientes c
          LEFT JOIN usuarios u ON u.cliente_id = c.id
          LEFT JOIN pedidos p ON p.usuario_id = u.id";

if ($busca !== '') {
    $query .= " WHERE c.razao_social LIKE :busca
                OR c.cnpj LIKE :busca
                OR c.contato_principal LIKE :busca
                OR u.email LIKE :busca";
}

$query .= " GROUP BY c.id ORDER BY c.criado_em DESC";

$stmt = $pdo->prepare($query);
if ($busca !== '') {
    $stmt->execute(['busca' => "%$busca%"]);
} else {
    $stmt->execute();
}
$clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Clientes | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="produtos.php">Produtos</a>
        <a href="clientes.php" class="ativo">Clientes</a>
        <a href="cupons.php">Cupons</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <h2>Base de Clientes</h2>
    </div>
    <p class="sub" style="margin-top:-14px; margin-bottom:20px;">
        Preenchida automaticamente quando alguém cria uma conta ou compra no site.
    </p>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Pesquisar por nome, e-mail, CPF/CNPJ ou contato..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Filtrar</button>
            <?php if($busca): ?> <a href="clientes.php" style="align-self:center; color:#666; text-decoration:none;">Limpar</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Contato</th>
                <th>Cadastro</th>
                <th>Pedidos</th>
                <th>Total gasto</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($clientes) > 0): ?>
                <?php foreach ($clientes as $cl): ?>
                <tr>
                    <td data-label="Cliente">
                        <strong><?= htmlspecialchars($cl['razao_social']) ?></strong>
                        <span class="info-secundaria"><?= htmlspecialchars($cl['email'] ?? '') ?></span>
                    </td>
                    <td data-label="Documento"><?= htmlspecialchars($cl['cnpj'] ?: '—') ?></td>
                    <td data-label="Contato">
                        <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($cl['contato_principal'] ?: '—') ?></span>
                        <span class="info-secundaria"><?= htmlspecialchars($cl['contato_secundario'] ?? '') ?></span>
                    </td>
                    <td data-label="Cadastro">
                        <?php if ($cl['perfil_completo']): ?>
                            <span class="status pago">Completo</span>
                        <?php elseif ($cl['email']): ?>
                            <span class="status pendente">Incompleto</span>
                        <?php else: ?>
                            <span class="info-secundaria">Cadastro manual</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Pedidos"><span class="badge-count"><?= $cl['total_pedidos'] ?></span></td>
                    <td data-label="Total gasto">R$ <?= number_format($cl['total_gasto'], 2, ',', '.') ?></td>
                    <td data-label="Ações">
                        <a href="editar-cliente.php?id=<?= $cl['id'] ?>" class="btn-editar">EDITAR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px; color: #999;">Nenhum cliente cadastrado ainda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    © 2026 — VaultCore | Loja de computadores
</footer>

</body>
</html>
