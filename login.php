<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Acesso ao Sistema</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #152534, #1e899e);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    background: #ffffff;
    width: 100%;
    max-width: 480px;
    border-radius: 18px;
    padding: 45px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

h1 {
    text-align: center;
    color: #152534;
    font-size: 26px;
    margin-bottom: 10px;
}

p.sub {
    text-align: center;
    color: #4b5563;
    font-size: 15px;
    margin-bottom: 35px;
}

/* ESCOLHA */
.escolha {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.btn-escolha {
    padding: 16px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-cliente {
    background: #1e899e;
    color: #ffffff;
}

.btn-cliente:hover {
    background: #65c9d1;
    color: #152534;
}

.btn-admin {
    background: #152534;
    color: #ffffff;
}

.btn-admin:hover {
    background: #376979;
}

/* FORM */
.form-login {
    display: none;
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

.form-group input {
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

.form-group input:focus {
    outline: none;
    border-color: #1e899e;
}

.btn-entrar {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #1e899e;
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}

.btn-entrar:hover {
    background: #65c9d1;
    color: #152534;
}

/* VOLTAR */
.voltar {
    margin-top: 22px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: #376979;
    font-size: 14px;
}

.voltar:hover {
    text-decoration: underline;
}

.voltar span {
    font-size: 18px;
}

.link-site {
    margin-top: 28px;
    text-align: center;
}

.link-site a {
    font-size: 14px;
    color: #376979;
    text-decoration: none;
}

.link-site a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="container">

    <!-- ESCOLHA -->
    <div id="escolha">
        <h1>Acesso ao Sistema</h1>
        <p class="sub">Selecione o tipo de acesso</p>

        <div class="escolha">
            <button class="btn-escolha btn-cliente" onclick="mostrarCliente()">
                Sou Cliente
            </button>

            <button class="btn-escolha btn-admin" onclick="mostrarAdmin()">
                Sou Colaborador
            </button>
        </div>

        <div class="link-site">
            <a href="index.php">← Voltar para o site</a>
        </div>
    </div>

    <!-- LOGIN CLIENTE (SEM SENHA) -->
    <div id="loginCliente" class="form-login">
        <h1>Acesso do Cliente</h1>
        <p class="sub">Informe o código do equipamento</p>

        <form method="post" action="processa-login.php">
            <input type="hidden" name="tipo" value="cliente">

            <div class="form-group">
                <label>Código do Equipamento</label>
                <input type="text" name="codigo_equipamento" placeholder="Ex: EQP-10293" required>
            </div>

            <button class="btn-entrar">Acessar</button>
        </form>

        <div class="voltar" onclick="voltarEscolha()">
            <span>←</span> Voltar
        </div>
    </div>

<!-- LOGIN COLABORADOR -->
<div id="loginAdmin" class="form-login">
    <h1>Acesso do Colaborador</h1>
    <p class="sub">Área restrita</p>

    <form method="post" action="processa-login.php">
        <input type="hidden" name="tipo" value="admin">

        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>

        <button class="btn-entrar">Entrar</button>
    </form>

    <div class="voltar" onclick="voltarEscolha()">
        <span>←</span> Voltar
    </div>
</div>

<script>
function mostrarCliente() {
    document.getElementById('escolha').style.display = 'none';
    document.getElementById('loginCliente').style.display = 'block';
}

function mostrarAdmin() {
    document.getElementById('escolha').style.display = 'none';
    document.getElementById('loginAdmin').style.display = 'block';
}

function voltarEscolha() {
    document.getElementById('loginCliente').style.display = 'none';
    document.getElementById('loginAdmin').style.display = 'none';
    document.getElementById('escolha').style.display = 'block';
}
</script>

</body>
</html>
