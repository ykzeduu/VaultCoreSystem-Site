<?php
session_start();

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
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
    body { background: #f2f4f6; color: #1f2937; padding-top: 140px; }

    header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 600; color: #ffffff; }

    .container { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 50px; }
    
    h2 { color: #152534; margin-bottom: 30px; border-left: 5px solid #1e899e; padding-left: 15px; }
    
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full { grid-column: span 2; }
    
    label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #1e899e; text-transform: uppercase; }
    input, textarea { width: 100%; padding: 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px; outline: none; background: #f9fafb; }
    input:focus, textarea:focus { border-color: #1e899e; background: #fff; }

    .btn-save { background: #1e899e; color: #fff; border: none; padding: 18px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; margin-top: 20px; transition: 0.3s; }
    .btn-save:hover { background: #152534; }
    
    .btn-cancelar { display: block; text-align: center; color: #6b7280; text-decoration: none; margin-top: 15px; font-size: 14px; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
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