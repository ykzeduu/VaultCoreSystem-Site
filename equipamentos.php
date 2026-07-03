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
/* HEADER PADRÃO */
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

/* ESTILOS ESPECÍFICOS */
.img-tec { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; background: #f2f4f6; border: 1px solid #eee; }
.status { padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
.disponivel { background: #d1fae5; color: #065f46; }
.locado { background: #fee2e2; color: #991b1b; }
.manutencao { background: #fef3c7; color: #92400e; }

.info-secundaria { display: block; font-size: 12px; color: #6b7280; margin-top: 4px; }
.btn-editar { background: #f59e0b; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; }
.btn-novo { background: #1e899e; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; transition: 0.3s; }
.btn-novo:hover { background: #152534; }

footer { background: #152534; color: #ffffff; text-align: center; padding: 26px; font-size: 14px; margin-top: 40px; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
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