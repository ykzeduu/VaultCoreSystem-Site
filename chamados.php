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

/* BUSCA E LISTAGEM DE CHAMADOS */
$busca = $_GET['busca'] ?? '';

// O SEGREDO: Usamos LEFT JOIN em clientes para o chamado não sumir caso o equipamento não tenha dono
$query = "SELECT ch.*, e.codigo_equipamento, c.razao_social, c.nome_fantasia 
          FROM chamados ch
          JOIN equipamentos e ON ch.equipamento_id = e.id
          LEFT JOIN clientes c ON e.cliente_id = c.id"; 

if ($busca !== '') {
    $query .= " WHERE e.codigo_equipamento LIKE :busca 
                OR c.razao_social LIKE :busca 
                OR c.nome_fantasia LIKE :busca 
                OR ch.status LIKE :busca 
                OR ch.prioridade LIKE :busca";
}

// Ordenação: Abertos primeiro, depois Em Atendimento, depois Concluídos
$query .= " ORDER BY CASE ch.status 
            WHEN 'aberto' THEN 1 
            WHEN 'em_atendimento' THEN 2 
            WHEN 'concluido' THEN 3 
            ELSE 4 END, ch.data_abertura DESC";

$stmt = $pdo->prepare($query);
if ($busca !== '') {
    $stmt->execute(['busca' => "%$busca%"]);
} else {
    $stmt->execute();
}
$chamados = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Chamados | VaultCore</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
    
    html, body { height: 100%; }

    body {
        background: #f2f4f6;
        color: #1f2937;
        overflow-y: scroll; /* BARRINHA SEMPRE VISÍVEL */
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

    main { padding: 170px 70px 50px; max-width: 1400px; margin: auto; width: 100%; flex: 1; }
    
    .header-acoes { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    h2 { color: #152534; }

    /* BARRA DE PESQUISA */
    .search-box { background: #fff; padding: 20px; border-radius: 16px; margin-bottom: 40px; display: flex; gap: 10px; align-items: center; border: 2px solid #1e899e; }
    .search-box input { flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; }
    .search-box button { padding: 12px 25px; background: #152534; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

    /* TABELA */
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    th, td { padding: 16px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { background: #f9fafb; font-size: 13px; color: #6b7280; text-transform: uppercase; }

    /* STATUS E BADGES */
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block; white-space: nowrap; }
    
    .aberto { background: #fee2e2; color: #991b1b; }
    .em_atendimento { background: #fef3c7; color: #92400e; }
    .concluido { background: #d1fae5; color: #065f46; }

    /* Indicador de prioridade na linha */
    .alta { border-left: 5px solid #ef4444; }
    .media { border-left: 5px solid #f59e0b; }
    .baixa { border-left: 5px solid #10b981; }

    .info-secundaria { display: block; font-size: 12px; color: #6b7280; margin-top: 4px; }
    
    .btn-editar { background: #f59e0b; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; }
    .btn-editar:hover { background: #d97706; }
    
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
        <a href="equipamentos.php">Equipamentos</a>
        <a href="clientes.php">Clientes</a>
        <a href="chamados.php" class="ativo">Chamados</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<main>
    <div class="header-acoes">
        <h2>Fila de Atendimento (O.S)</h2>
        <a href="abrir-chamado.php" class="btn-novo">+ ABRIR CHAMADO</a>
    </div>

    <div class="search-box">
        <form method="get" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="busca" placeholder="Filtrar por TAG, cliente, status ou prioridade..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Filtrar</button>
            <?php if($busca): ?> <a href="chamados.php" style="align-self:center; color:#666; text-decoration:none;">Limpar</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data / ID</th>
                <th>Equipamento</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($chamados) > 0): ?>
                <?php foreach ($chamados as $ch): ?>
                <tr class="<?= $ch['prioridade'] ?>">
                    <td>
                        <span style="font-weight: 600; color: #152534;">#<?= $ch['id'] ?></span>
                        <span class="info-secundaria"><?= date('d/m/Y H:i', strtotime($ch['data_abertura'])) ?></span>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($ch['codigo_equipamento']) ?></strong>
                    </td>
                    <td>
                        <?php 
                            $nome_cliente = $ch['nome_fantasia'] ?: $ch['razao_social'];
                            echo htmlspecialchars($nome_cliente ?: 'Estoque / S. Cliente');
                        ?>
                    </td>
                    <td style="max-width: 250px;">
                        <span title="<?= htmlspecialchars($ch['descricao']) ?>">
                            <?= (mb_strlen($ch['descricao']) > 45) ? mb_substr(htmlspecialchars($ch['descricao']), 0, 45) . '...' : htmlspecialchars($ch['descricao']) ?>
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 11px; font-weight: bold; color: #374151;"><?= strtoupper($ch['prioridade']) ?></span>
                    </td>
                    <td>
                        <span class="badge <?= $ch['status'] ?>">
                            <?= str_replace('_', ' ', strtoupper($ch['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <a href="editar-chamado.php?id=<?= $ch['id'] ?>" class="btn-editar">GERENCIAR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px; color: #999;">Nenhum chamado encontrado na base de dados.</td>
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