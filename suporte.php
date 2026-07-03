<?php
$paginaAtiva = 'suporte';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Suporte Técnico - VaultCore</title>

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
    height: 136px;
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

/* TOPO SUPORTE */
.topo-suporte {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 60px;
    margin-bottom: 80px;
}

.topo-suporte h1 {
    font-size: 40px;
    color: #0f2435;
    margin-bottom: 20px;
}

.topo-suporte p {
    font-size: 18px;
    line-height: 1.8;
    color: #4b5563;
    margin-bottom: 25px;
}

/* CTA */
.cta-box {
    background: #ffffff;
    border-radius: 18px;
    padding: 35px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.cta-box h3 {
    font-size: 22px;
    color: #1e899e;
    margin-bottom: 15px;
}

.cta-box p {
    font-size: 16px;
    color: #4b5563;
    margin-bottom: 25px;
}

.cta-botoes {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn {
    padding: 14px 22px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-whatsapp {
    background: #25D366;
    color: #ffffff;
}

.btn-whatsapp:hover {
    background: #1ebe5d;
}

.btn-login {
    background: #1e899e;
    color: #ffffff;
}

.btn-login:hover {
    background: #65c9d1;
    color: #0f2435;
}

/* SERVIÇOS */
.servicos h2 {
    font-size: 30px;
    color: #0f2435;
    margin-bottom: 40px;
}

.grid-servicos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
}

.card {
    background: #ffffff;
    border-radius: 16px;
    padding: 30px;
    border: 1px solid #e5e7eb;
    transition: 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.card h4 {
    font-size: 18px;
    color: #1e899e;
    margin-bottom: 12px;
}

.card p {
    font-size: 15px;
    line-height: 1.7;
    color: #4b5563;
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

    <div class="topo-suporte">
        <div>
            <h1>Suporte Técnico Especializado</h1>
            <p>
                A VaultCore oferece suporte técnico estruturado para garantir
                desempenho, segurança e continuidade da sua infraestrutura.
            </p>
            <p>
                Atuamos de forma preventiva e corretiva, reduzindo falhas e
                assegurando a operação do seu negócio.
            </p>
        </div>

        <div class="cta-box">
            <h3>Precisa de ajuda agora?</h3>
            <p>
                Entre em contato pelo WhatsApp ou acesse o painel
                para abrir um chamado diretamente no sistema.
            </p>

            <div class="cta-botoes">
                <a href="#" class="btn btn-whatsapp" onclick="abrirWhatsApp()">
                    Falar no WhatsApp
                </a>

                <a href="login.php" class="btn btn-login">
                    Abrir Chamado
                </a>
            </div>
        </div>
    </div>

    <div class="servicos">
        <h2>Como funciona o suporte</h2>

        <div class="grid-servicos">
            <div class="card">
                <h4>Suporte Remoto</h4>
                <p>Diagnóstico e resolução rápida de falhas à distância.</p>
            </div>

            <div class="card">
                <h4>Suporte Presencial</h4>
                <p>Atendimento no local conforme contrato.</p>
            </div>

            <div class="card">
                <h4>Horário de Atendimento</h4>
                <p>Segunda a sexta-feira, das 08h às 18h.</p>
            </div>

            <div class="card">
                <h4>Abertura de Chamados</h4>
                <p>Via painel, WhatsApp ou canais oficiais.</p>
            </div>
        </div>
    </div>

</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

<script>
function abrirWhatsApp() {
    const hora = new Date().getHours();
    let saudacao = "Olá";

    if (hora >= 5 && hora < 12) saudacao = "Bom dia";
    else if (hora >= 12 && hora < 18) saudacao = "Boa tarde";
    else saudacao = "Boa noite";

    const mensagem = `${saudacao}! Possuo um equipamento locado e gostaria de suporte.`;
    const telefone = "67993120653";

    window.open(`https://wa.me/${telefone}?text=${encodeURIComponent(mensagem)}`, "_blank");
}
</script>

</body>
</html>
