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

<style>
/* ... Mantendo seus estilos globais e adicionando melhorias ... */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
html, body { height: 100%; }
body { background: #f2f4f6; color: #1f2937; overflow-y: scroll; display: flex; flex-direction: column; }

header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
.logo { font-size: 24px; font-weight: 600; color: #ffffff; }

nav { display: flex; align-items: center; gap: 30px; }
nav a { color: #d1d5db; text-decoration: none; font-size: 15px; padding-bottom: 6px; border-bottom: 3px solid transparent; transition: 0.2s; }
nav a:hover { color: #ffffff; }
nav a.ativo { color: #65c9d1; border-bottom: 3px solid #65c9d1; }

.logout-btn { background: #1e899e; color: #ffffff; padding: 8px 18px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s; }
.logout-btn:hover { background: #65c9d1; color: #152534; }

.conteudo { padding-top: 170px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; padding-left: 30px; padding-right: 30px; flex: 1; }
.conteudo h1 { font-size: 32px; color: #152534; margin-bottom: 10px; }
.subtitulo { color: #6b7280; margin-bottom: 40px; font-size: 16px; }

/* GRID DE CARDS */
.cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; margin-bottom: 50px; }
.card { background: #ffffff; border-radius: 16px; padding: 25px; border: 1px solid #e5e7eb; transition: 0.3s ease; display: flex; flex-direction: column; }
.card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-color: #65c9d1; }

.icon { font-size: 30px; margin-bottom: 15px; }
.card h2 { font-size: 19px; color: #152534; margin-bottom: 10px; }
.card p { font-size: 14px; color: #4b5563; line-height: 1.5; flex-grow: 1; margin-bottom: 20px; }

/* BOTÕES DOS CARDS */
.btn-card { 
    display: inline-block; 
    text-align: center; 
    background: #f3f4f6; 
    color: #152534; 
    text-decoration: none; 
    padding: 10px; 
    border-radius: 8px; 
    font-size: 13px; 
    font-weight: 700; 
    transition: 0.2s;
}
.card:hover .btn-card { background: #152534; color: #fff; }

/* SEÇÃO EXPLICATIVA */
.info-section { 
    background: #152534; 
    color: #fff; 
    padding: 40px; 
    border-radius: 20px; 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 40px;
    align-items: center;
}
.info-text h3 { color: #65c9d1; font-size: 24px; margin-bottom: 15px; }
.info-text p { font-size: 15px; line-height: 1.7; color: #d1d5db; }
.info-list { list-style: none; }
.info-list li { margin-bottom: 12px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
.info-list li::before { content: "✓"; color: #65c9d1; font-weight: bold; }

footer { background: #152534; color: #ffffff; text-align: center; padding: 26px; font-size: 14px; margin-top: auto; }
</style>
</head>

<body>

<header>
    <div class="logo">VaultCore System</div>
    <nav>
        <a href="admin.php" class="ativo">Dashboard</a>
        <a href="equipamentos.php">Equipamentos</a>
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