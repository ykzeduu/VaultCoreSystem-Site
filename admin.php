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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel Administrativo | VaultCore</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <nav>
        <a href="admin.php" class="ativo">Dashboard</a>
        <a href="equipamentos.php">Equipamentos</a>
        <a href="produtos.php">Produtos (Loja)</a>
        <a href="clientes.php">Clientes</a>
        <a href="chamados.php">Chamados</a>
        <a href="financeiro.php">Financeiro</a>
    </nav>
    <a href="admin.php?sair=1" class="logout-btn">Sair</a>
</header>

<section class="conteudo">
    <h1>Bem-vindo, Colaborador</h1>
    <p class="subtitulo">Central de comando para gestão de ativos e contratos de locação de TI.</p>

    <div class="cards">
        <div class="card">
            <div class="icon">💻</div>
            <h2>Equipamentos</h2>
            <p>Cadastre novos itens, monitore o que está em estoque ou locado e verifique a saúde do hardware.</p>
            <a href="equipamentos.php" class="btn-card">GERENCIAR ESTOQUE</a>
        </div>

        <div class="card">
            <div class="icon">🏢</div>
            <h2>Clientes</h2>
            <p>Controle a base de empresas, visualize contratos ativos e vincule novos equipamentos aos locatários.</p>
            <a href="clientes.php" class="btn-card">VER CLIENTES</a>
        </div>

        <div class="card">
            <div class="icon">🛠️</div>
            <h2>Chamados</h2>
            <p>Atenda solicitações de suporte, registre manutenções e mantenha o histórico técnico atualizado.</p>
            <a href="chamados.php" class="btn-card">ABRIR CHAMADO</a>
        </div>

        <div class="card">
            <div class="icon">💰</div>
            <h2>Financeiro</h2>
            <p>Acompanhe o faturamento mensal, verifique pendências de pagamento e gere novas parcelas de locação.</p>
            <a href="financeiro.php" class="btn-card">CONCILIAÇÃO</a>
        </div>
    </div>

    <div class="info-section">
        <div class="info-text">
            <h3>Como funciona o VaultCore?</h3>
            <p>
                O sistema foi projetado para automatizar o ciclo de vida da locação de TI. 
                Desde a entrada do equipamento no estoque até a cobrança mensal do cliente final.
            </p>
        </div>
        <ul class="info-list">
            <li><strong>Fluxo de Locação:</strong> Vincule uma TAG de equipamento a um cliente para gerar parcelas automáticas.</li>
            <li><strong>Manutenção Centralizada:</strong> Todo chamado aberto gera um histórico permanente no equipamento.</li>
            <li><strong>Segurança de Dados:</strong> Painel restrito a colaboradores autenticados com log de atividades.</li>
            <li><strong>Controle Financeiro:</strong> O sistema calcula o faturamento estimado com base nos contratos vigentes.</li>
        </ul>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura
</footer>

</body>
</html>