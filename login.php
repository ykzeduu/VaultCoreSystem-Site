<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Acesso ao Sistema</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container login-page">

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
