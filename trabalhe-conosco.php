<?php
$paginaAtiva = 'trabalhe';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Trabalhe Conosco - VaultCore</title>

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

/* FORMULÁRIO */
.form-box {
    background: #ffffff;
    padding: 40px;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    max-width: 700px;
}

.form-box h2 {
    font-size: 26px;
    color: #1e899e;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-size: 14px;
    color: #374151;
    margin-bottom: 6px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1e899e;
}

/* BOTÃO */
.btn-enviar {
    background: #1e899e;
    color: #ffffff;
    padding: 14px 30px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-enviar:hover {
    background: #65c9d1;
    color: #0f2435;
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
    <h1>Trabalhe Conosco</h1>

    <p>
        Buscamos profissionais comprometidos, com interesse em tecnologia,
        infraestrutura de TI, suporte técnico e atendimento ao cliente.
    </p>

    <p>
        Caso tenha interesse em oportunidades futuras, parcerias ou
        prestação de serviços, preencha o formulário abaixo.
    </p>

    <div class="form-box">
        <h2>Envie suas informações</h2>

        <form>
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" placeholder="Seu nome completo">
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" placeholder="seuemail@email.com">
            </div>

            <div class="form-group">
                <label>Telefone / WhatsApp</label>
                <input type="text" placeholder="(xx) xxxxx-xxxx">
            </div>

            <div class="form-group">
                <label>Área de interesse</label>
                <input type="text" placeholder="Ex: Suporte técnico, TI, Administrativo">
            </div>

            <div class="form-group">
                <label>Mensagem</label>
                <textarea placeholder="Conte um pouco sobre você ou sua experiência"></textarea>
            </div>

            <button type="submit" class="btn-enviar">Enviar</button>
        </form>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

</body>
</html>
