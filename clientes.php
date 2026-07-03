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
$pdo = new PDO(
    "mysql:host=sql311.infinityfree.com;dbname=if0_41023013_db_clientes;charset=utf8",
    "if0_41023013",
    "2kVHu71TF3ly",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

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
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
html, body {
    height: 100%;
}

body {
    background: #f2f4f6;
    color: #1f2937;
    overflow-y: scroll; /* barra sempre visível */
    display: flex;
    flex-direction: column;
}
/* HEADER PADRÃO VAULTCORE */
header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
.logo { font-size: 24px; font-weight: 600; color: #ffffff; }
nav { display: flex; align-items: center; gap: 30px; }
nav a { color: #d1d5db; text-decoration: none; font-size: 15px; padding-bottom: 6px; border-bottom: 3px solid transparent; transition: 0.2s; }
nav a:hover { color: #ffffff; }
nav a.ativo { color: #65c9d1; border-bottom: 3px solid #65c9d1; }
.logout-btn { background: #1e899e; color: #ffffff; padding: 8px 18px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s; }

main { padding: 170px 70px 50px; max-width: 1300px; margin: auto; width: 100%; flex: 1; }
.header-acoes { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
h2 { color: #152534; }

/* BARRA DE PESQUISA */
.search-box { background: #fff; padding: 20px; border-radius: 16px; margin-bottom: 40px; display: flex; gap: 10px; align-items: center; border: 2px solid #1e899e; }
.search-box input { flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; }
.search-box button { padding: 12px 25px; background: #152534; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

/* TABELA */
table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
th, td { padding: 16px; border-bottom: 1px solid #e5e7eb; text-align: left; }
th { background: #f9fafb; font-size: 13px; color: #6b7280; text-transform: uppercase; }

/* BADGES E INFO */
.badge-count { background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; }
.info-secundaria { display: block; font-size: 12px; color: #6b7280; margin-top: 4px; }

.btn-editar { background: #f59e0b; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; }
.btn-novo { background: #1e899e; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; }

footer { background: #152534; color: #ffffff; text-align: center; padding: 26px; font-size: 14px; margin-top: 40px; }
</style>
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