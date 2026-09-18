<?php
session_start();

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/includes/config.php';

/* PROCESSA O CADASTRO */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $razao_social = $_POST['razao_social'];
    $nome_fantasia = $_POST['nome_fantasia'];
    $cnpj = $_POST['cnpj'];
    $endereco = $_POST['endereco'];
    $contato_principal = $_POST['contato_principal'];
    $contato_secundario = $_POST['contato_secundario'];

    $sql = "INSERT INTO clientes (razao_social, nome_fantasia, cnpj, endereco, contato_principal, contato_secundario) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $razao_social, 
        $nome_fantasia, 
        $cnpj, 
        $endereco, 
        $contato_principal, 
        $contato_secundario
    ]);

    header("Location: clientes.php?msg=cadastrado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Cliente | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <a href="clientes.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2>Cadastrar Novo Cliente</h2>

    <form method="post">
        <div class="grid">
            <div class="full">
                <label>Razão Social</label>
                <input type="text" name="razao_social" placeholder="Ex: VaultCore Tecnologia LTDA" required>
            </div>

            <div class="full">
                <label>Nome Fantasia</label>
                <input type="text" name="nome_fantasia" placeholder="Ex: VaultCore Admin">
            </div>

            <div>
                <label>CNPJ / CPF</label>
                <input type="text" name="cnpj" placeholder="00.000.000/0001-00" required>
            </div>

            <div>
                <label>Contato Principal (Telefone/WhatsApp)</label>
                <input type="text" name="contato_principal" placeholder="(00) 00000-0000" required>
            </div>

            <div class="full">
                <label>E-mail / Contato Secundário</label>
                <input type="text" name="contato_secundario" placeholder="financeiro@cliente.com.br">
            </div>

            <div class="full">
                <label>Endereço Completo</label>
                <textarea name="endereco" rows="3" placeholder="Rua, Número, Bairro, Cidade - UF" required></textarea>
            </div>

            <button type="submit" class="btn-save">FINALIZAR CADASTRO DE CLIENTE</button>
        </div>
        <a href="clientes.php" class="btn-cancelar">Cancelar e Sair</a>
    </form>
</main>

</body>
</html>