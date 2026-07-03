<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO */
$pdo = new PDO("mysql:host=sql311.infinityfree.com;dbname=if0_41023013_db_clientes;charset=utf8", "if0_41023013", "2kVHu71TF3ly", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: clientes.php"); exit; }

/* PROCESSA EXCLUSÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir'])) {
    try {
        $sql_del = "DELETE FROM clientes WHERE id = ?";
        $pdo->prepare($sql_del)->execute([$id]);
        header("Location: clientes.php?msg=excluido");
        exit;
    } catch (PDOException $e) {
        $erro_exclusao = "Não é possível excluir um cliente que possui equipamentos ou chamados vinculados.";
    }
}

/* PROCESSA ATUALIZAÇÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar'])) {
    $razao_social = $_POST["razao_social"];
    $nome_fantasia = $_POST["nome_fantasia"]; // Novo campo
    $cnpj = $_POST["cnpj"];
    $endereco = $_POST["endereco"];
    $contato_principal = $_POST["contato_principal"];
    $contato_secundario = $_POST["contato_secundario"];

    $sql = "UPDATE clientes SET razao_social = ?, nome_fantasia = ?, cnpj = ?, endereco = ?, contato_principal = ?, contato_secundario = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$razao_social, $nome_fantasia, $cnpj, $endereco, $contato_principal, $contato_secundario, $id]);
    
    header("Location: clientes.php?msg=atualizado");
    exit;
}

/* BUSCA DADOS ATUAIS */
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) { header("Location: clientes.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Cliente | VaultCore Admin</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", sans-serif; }
    body { background: #f2f4f6; padding-top: 140px; color: #1f2937; }
    
    header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 600; color: #ffffff; }

    .container { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 50px; }
    
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full { grid-column: span 2; }
    
    label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #1e899e; text-transform: uppercase; }
    input, textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px; outline: none; transition: 0.2s; }
    input:focus, textarea:focus { border-color: #1e899e; box-shadow: 0 0 0 3px rgba(30, 137, 158, 0.1); }
    
    .btn-save { background: #1e899e; color: #fff; border: none; padding: 16px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 30px; width: 100%; font-size: 16px; transition: 0.3s; }
    .btn-save:hover { background: #152534; }

    .btn-delete { background: #fff; color: #dc2626; border: 2px solid #dc2626; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 15px; width: 100%; font-size: 14px; transition: 0.3s; }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    
    .secao-titulo { margin: 20px 0 10px; padding-left: 5px; border-left: 4px solid #1e899e; font-size: 14px; color: #152534; font-weight: bold; }
    
    .alert-error { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="clientes.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2 style="color: #152534; margin-bottom: 25px;">Editar Cadastro do Cliente</h2>

    <?php if (isset($erro_exclusao)): ?>
        <div class="alert-error"><?= $erro_exclusao ?></div>
    <?php endif; ?>

    <form method="post">
        <p class="secao-titulo">Identificação</p>
        <div class="grid">
            <div>
                <label>Razão Social (Nome Real)</label>
                <input type="text" name="razao_social" value="<?= htmlspecialchars($cliente['razao_social']) ?>" placeholder="Ex: Silva & Silva LTDA" required>
            </div>
            <div>
                <label>Nome Fantasia (Como é conhecido)</label>
                <input type="text" name="nome_fantasia" value="<?= htmlspecialchars($cliente['nome_fantasia'] ?? '') ?>" placeholder="Ex: Padaria do Silva">
            </div>
            <div class="full">
                <label>CNPJ / CPF</label>
                <input type="text" name="cnpj" value="<?= htmlspecialchars($cliente['cnpj']) ?>" placeholder="00.000.000/0000-00">
            </div>
        </div>

        <p class="secao-titulo">Localização</p>
        <div class="grid">
            <div class="full">
                <label>Endereço Completo</label>
                <textarea name="endereco" rows="3" required><?= htmlspecialchars($cliente['endereco']) ?></textarea>
            </div>
        </div>

        <p class="secao-titulo">Canais de Contato</p>
        <div class="grid">
            <div>
                <label>Contato Principal (Telefone/Whats)</label>
                <input type="text" name="contato_principal" value="<?= htmlspecialchars($cliente['contato_principal']) ?>" placeholder="(67) 99999-9999">
            </div>
            <div>
                <label>Contato Secundário / E-mail</label>
                <input type="text" name="contato_secundario" value="<?= htmlspecialchars($cliente['contato_secundario']) ?>" placeholder="email@empresa.com">
            </div>
        </div>

        <button type="submit" name="atualizar" class="btn-save">SALVAR ALTERAÇÕES</button>

        <button type="submit" name="excluir" class="btn-delete" onclick="return confirm('ATENÇÃO: Deseja excluir este cliente permanentemente? Isso pode afetar históricos de chamados.');">
            EXCLUIR CLIENTE DO SISTEMA
        </button>
    </form>
</main>

</body>
</html>