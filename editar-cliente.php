<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO */
require __DIR__ . '/includes/config.php';

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
        $erro_exclusao = "Não é possível excluir um cliente que possui pedidos vinculados.";
    }
}

/* PROCESSA ATUALIZAÇÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar'])) {
    $razao_social = $_POST["razao_social"];
    $nome_fantasia = $_POST["nome_fantasia"];
    $cnpj = $_POST["cnpj"];
    $cep = $_POST["cep"];
    $endereco = $_POST["endereco"];
    $contato_principal = $_POST["contato_principal"];
    $contato_secundario = $_POST["contato_secundario"];

    $sql = "UPDATE clientes SET razao_social = ?, nome_fantasia = ?, cnpj = ?, cep = ?, endereco = ?, contato_principal = ?, contato_secundario = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$razao_social, $nome_fantasia, $cnpj, $cep, $endereco, $contato_principal, $contato_secundario, $id]);
    
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
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <a href="clientes.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2 style="color: #152534; margin-bottom: 25px;">Editar Cadastro do Cliente</h2>

    <?php if (isset($erro_exclusao)): ?>
        <div class="alert-error"><?= $erro_exclusao ?></div>
    <?php endif; ?>

    <form method="post">
        <p class="secao-titulo">Identificação</p>
        <div class="form-grid">
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
        <div class="form-grid">
            <div>
                <label>CEP</label>
                <input type="text" name="cep" value="<?= htmlspecialchars($cliente['cep'] ?? '') ?>" placeholder="00000-000">
            </div>
            <div class="full">
                <label>Endereço Completo</label>
                <textarea name="endereco" rows="3" required><?= htmlspecialchars($cliente['endereco']) ?></textarea>
            </div>
        </div>

        <p class="secao-titulo">Canais de Contato</p>
        <div class="form-grid">
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

        <button type="submit" name="excluir" class="btn-delete" onclick="return confirm('ATENÇÃO: Deseja excluir este cliente permanentemente? Isso também afeta o login dele na loja.');">
            EXCLUIR CLIENTE DO SISTEMA
        </button>
    </form>
</main>

</body>
</html>