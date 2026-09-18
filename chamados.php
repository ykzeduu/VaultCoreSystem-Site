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
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
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