<?php
session_start();

/* LOGOUT DIRETO */
if (isset($_GET['sair'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/config.php';

/* BUSCA E LISTAGEM DE CLIENTES */
$busca = $_GET['busca'] ?? '';

// Query que traz os dados do cliente + contagem de equipamentos que ele possui
$query = "SELECT c.*, COUNT(e.id) as total_equipamentos 
          FROM clientes c
          LEFT JOIN equipamentos e ON c.id = e.cliente_id";

if ($busca !== '') {
    $query .= " WHERE c.razao_social LIKE :busca 
                OR c.cnpj LIKE :busca 
                OR c.contato_principal LIKE :busca";
}

$query .= " GROUP BY c.id ORDER BY c.razao_social ASC";

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
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="equipamentos.php">Equipamentos</a>
        <a href="clientes.php" class="ativo">Clientes</a>
        <a href="chamados.php">Chamados</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <h2>Base de Clientes</h2>
        <a href="cadastro-cliente.php" class="btn-novo">+ NOVO CLIENTE</a>
    </div>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Pesquisar por Razão Social, CNPJ ou Contato..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Filtrar</button>
            <?php if($busca): ?> <a href="clientes.php" style="align-self:center; color:#666; text-decoration:none;">Limpar</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente / CNPJ</th>
                <th>Endereço</th>
                <th>Contatos</th>
                <th>Equipamentos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($clientes) > 0): ?>
                <?php foreach ($clientes as $cl): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($cl['razao_social']) ?></strong>
                        <span class="info-secundaria">CNPJ: <?= $cl['cnpj'] ?></span>
                    </td>
                    <td style="max-width: 250px; font-size: 13px;">
                        <?= htmlspecialchars($cl['endereco']) ?>
                    </td>
                    <td>
                        <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($cl['contato_principal']) ?></span>
                        <span class="info-secundaria"><?= htmlspecialchars($cl['contato_secundario']) ?></span>
                    </td>
                    <td>
                        <span class="badge-count"><?= $cl['total_equipamentos'] ?> Ativos</span>
                    </td>
                    <td>
                        <a href="editar-cliente.php?id=<?= $cl['id'] ?>" class="btn-editar">EDITAR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: #999;">Nenhum cliente cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura
</footer>

</body>
</html>