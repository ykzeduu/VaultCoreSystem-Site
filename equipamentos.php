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
require __DIR__ . '/includes/config.php';

/* BUSCA E LISTAGEM */
$busca = $_GET['busca'] ?? '';

// Query que traz os dados do equipamento + nome do cliente (se houver)
$query = "SELECT e.*, c.razao_social, c.nome_fantasia 
          FROM equipamentos e 
          LEFT JOIN clientes c ON c.id = e.cliente_id";

if ($busca !== '') {
    $query .= " WHERE e.codigo_equipamento LIKE :busca 
                OR e.numero_serie LIKE :busca 
                OR c.razao_social LIKE :busca
                OR c.nome_fantasia LIKE :busca";
}

$query .= " ORDER BY e.id DESC";

$stmt = $pdo->prepare($query);
if ($busca !== '') {
    $stmt->execute(['busca' => "%$busca%"]);
} else {
    $stmt->execute();
}
$equipamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Inventário de Equipamentos | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="equipamentos.php" class="ativo">Equipamentos</a>
        <a href="clientes.php">Clientes</a>
        <a href="chamados.php">Chamados</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <h2>Inventário de Ativos</h2>
        <a href="cadastro-equipamento.php" class="btn-novo">+ NOVO EQUIPAMENTO</a>
    </div>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Filtrar por código, série ou cliente..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Buscar</button>
            <?php if($busca): ?> <a href="equipamentos.php" style="align-self:center; color:#666; text-decoration:none;">Limpar</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Identificação</th>
                <th>Status</th>
                <th>Localização / Cliente</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($equipamentos) > 0): ?>
                <?php foreach ($equipamentos as $e): ?>
                <tr>
                    <td>
                        <img src="assets/fotos-equipamentos/<?= $e["imagem"] ?: 'placeholder.png' ?>" class="img-tec" onerror="this.src='https://placehold.co/100x100?text=S/F'">
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($e["codigo_equipamento"]) ?></strong>
                        <span class="info-secundaria">S/N: <?= htmlspecialchars($e["numero_serie"]) ?></span>
                    </td>
                    <td>
                        <span class="status <?= $e["status"] ?>">
                            <?= $e["status"] ?>
                        </span>
                    </td>
                    <td>
                        <?php if($e['razao_social']): ?>
                            <strong><?= htmlspecialchars($e["nome_fantasia"] ?: $e["razao_social"]) ?></strong>
                            <span class="info-secundaria"><?= htmlspecialchars($e["razao_social"]) ?></span>
                        <?php else: ?>
                            <span style="color: #9ca3af;">Disponível em Estoque</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="editar-equipamento.php?id=<?= $e["id"] ?>" class="btn-editar">EDITAR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: #999;">Nenhum equipamento encontrado.</td>
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