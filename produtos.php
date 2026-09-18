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

$query = "SELECT * FROM produtos";
if ($busca !== '') {
    $query .= " WHERE nome LIKE :busca OR categoria LIKE :busca";
}
$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
if ($busca !== '') {
    $stmt->execute(['busca' => "%$busca%"]);
} else {
    $stmt->execute();
}
$produtos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Produtos da Loja | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="produtos.php" class="ativo">Produtos</a>
        <a href="clientes.php">Clientes</a>
        <a href="cupons.php">Cupons</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <h2>Produtos vendidos na loja</h2>
        <a href="cadastro-produto.php" class="btn-novo">+ NOVO PRODUTO</a>
    </div>
    <p class="sub" style="margin-top:-14px; margin-bottom:20px;">
        Estes são os produtos que aparecem para o cliente na página inicial, com o estoque em tempo real.
    </p>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Filtrar por nome ou categoria..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Buscar</button>
            <?php if($busca): ?> <a href="produtos.php" style="align-self:center; color:#666; text-decoration:none;">Limpar</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Visível na loja</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $p): ?>
                <tr>
                    <td data-label="Imagem">
                        <img src="<?= htmlspecialchars($p['imagem'] ?: 'assets/img/pc-essencial-a.svg') ?>" class="img-tec">
                    </td>
                    <td data-label="Produto">
                        <strong><?= htmlspecialchars($p["nome"]) ?></strong>
                        <span class="info-secundaria"><?= htmlspecialchars($p["especificacoes"]) ?></span>
                    </td>
                    <td data-label="Preço">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                    <td data-label="Estoque">
                        <span class="status <?= $p['estoque'] > 0 ? 'disponivel' : 'locado' ?>">
                            <?= $p['estoque'] > 0 ? $p['estoque'] . ' un.' : 'Esgotado' ?>
                        </span>
                    </td>
                    <td data-label="Visível"><?= $p['ativo'] ? 'Sim' : 'Não (oculto)' ?></td>
                    <td data-label="Ações">
                        <a href="editar-produto.php?id=<?= $p["id"] ?>" class="btn-editar">EDITAR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 40px; color: #999;">Nenhum produto cadastrado ainda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
