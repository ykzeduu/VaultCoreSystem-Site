<?php
$paginaAtiva = 'sobre';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Sobre Nós - VaultCore</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body {
    background: #f4f7fb;
    color: #1f2937;
}

/* HEADER */
header {
    position: fixed;
    top: 0;
    width: 100%;
    height: 110px;
    background: linear-gradient(90deg, #0f2435, #152534);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 70px;
    z-index: 1000;
}

.logo {
    display: flex;
    align-items: center;
}

.logo img {
    height: 136px;       /* tamanho ideal pro header */
    width: auto;
    object-fit: contain;
}


/* MENU */
nav {
    display: flex;
    align-items: center;
    gap: 35px;
}

nav a {
    color: #d1d5db;
    text-decoration: none;
    font-size: 16px;
    padding-bottom: 6px;
    border-bottom: 3px solid transparent;
    transition: 0.2s;
}

nav a:hover {
    color: #ffffff;
}

nav a.ativo {
    color: #65c9d1;
    border-bottom: 3px solid #65c9d1;
}

/* BOTÃO ADMIN */
.admin-btn {
    background: #1e899e;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s;
}

.admin-btn:hover {
    background: #65c9d1;
    color: #0f2435;
}

/* CONTEÚDO */
.conteudo {
    padding-top: 180px;
    padding-bottom: 100px;
    max-width: 1200px;
    margin: 0 auto;
    padding-left: 30px;
    padding-right: 30px;
}

.conteudo h1 {
    font-size: 40px;
    color: #0f2435;
    margin-bottom: 25px;
}

.conteudo p {
    font-size: 18px;
    line-height: 1.8;
    color: #4b5563;
    margin-bottom: 25px;
    max-width: 900px;
}

/* SEÇÃO VALORES */
.cards {
    margin-top: 70px;
}

.cards h2 {
    font-size: 30px;
    color: #0f2435;
    margin-bottom: 40px;
}

.grid-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
}

.card {
    background: #ffffff;
    padding: 35px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    transition: 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.card h3 {
    font-size: 20px;
    margin-bottom: 12px;
    color: #1e899e;
}

.card p {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.7;
}

/* FOOTER */
footer {
    background: #0f2435;
    color: #ffffff;
    text-align: center;
    padding: 30px;
    font-size: 14px;
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="VaultCore">
    </div>

    <nav>
        <a href="index.php" class="<?= $paginaAtiva == 'inicio' ? 'ativo' : '' ?>">Início</a>
        <a href="sobre.php" class="<?= $paginaAtiva == 'sobre' ? 'ativo' : '' ?>">Sobre nós</a>
        <a href="suporte.php" class="<?= $paginaAtiva == 'suporte' ? 'ativo' : '' ?>">Suporte</a>
        <a href="trabalhe-conosco.php" class="<?= $paginaAtiva == 'trabalhe' ? 'ativo' : '' ?>">Trabalhe Conosco</a>
    </nav>

    <a href="login.php" class="admin-btn">Área Administrativa</a>
</header>

<section class="conteudo">
    <h1>Sobre a VaultCore</h1>

    <p>
        A VaultCore atua na gestão de infraestrutura de TI, oferecendo
        equipamentos padronizados, suporte técnico estruturado e contratos
        claros para empresas que buscam eficiência e previsibilidade.
    </p>

    <p>
        Nosso modelo elimina custos elevados com aquisição e manutenção
        de hardware, permitindo que nossos clientes foquem no crescimento
        do negócio sem preocupações operacionais.
    </p>

    <p>
        Trabalhamos com equipamentos revisados, monitorados e preparados
        para uso profissional, assegurando desempenho, segurança e
        continuidade dos serviços.
    </p>

    <div class="cards">
        <h2>Nossos pilares</h2>

        <div class="grid-cards">
            <div class="card">
                <h3>Missão</h3>
                <p>
                    Garantir infraestrutura de TI confiável por meio de
                    soluções práticas, suporte eficiente e gestão responsável.
                </p>
            </div>

            <div class="card">
                <h3>Visão</h3>
                <p>
                    Ser referência em gestão de infraestrutura de TI,
                    reconhecida pela confiabilidade e excelência operacional.
                </p>
            </div>

            <div class="card">
                <h3>Valores</h3>
                <p>
                    Transparência, compromisso com o cliente,
                    segurança da informação e melhoria contínua.
                </p>
            </div>
        </div>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

</body>
</html>
