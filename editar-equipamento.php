<?php
session_start();

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO */
require __DIR__ . '/config.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: equipamentos.php"); exit; }

/* PROCESSA EXCLUSÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir'])) {
    $sql_del = "DELETE FROM equipamentos WHERE id = ?";
    $pdo->prepare($sql_del)->execute([$id]);
    
    header("Location: equipamentos.php?msg=excluido");
    exit;
}

/* PROCESSA ATUALIZAÇÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar'])) {
    $status = $_POST["status"];
    $imagem = $_POST["nome_imagem"];

    // TRAVA AQUI: Removi o cliente_id do UPDATE para ninguém mudar pelo inspecionar elemento
    $sql = "UPDATE equipamentos SET status = ?, imagem = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$status, $imagem, $id]);
    
    header("Location: equipamentos.php?msg=atualizado");
    exit;
}

/* BUSCA DADOS ATUAIS */
$stmt = $pdo->prepare("SELECT e.*, c.razao_social, c.nome_fantasia, c.endereco, c.contato_principal, c.contato_secundario 
                       FROM equipamentos e 
                       LEFT JOIN clientes c ON e.cliente_id = c.id 
                       WHERE e.id = ?");
$stmt->execute([$id]);
$eq = $stmt->fetch();

if (!$eq) { header("Location: equipamentos.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Equipamento | VaultCore Admin</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="equipamentos.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <div class="header-form">
        <img src="assets/fotos-equipamentos/<?= $eq['imagem'] ?: 'placeholder.png' ?>" class="preview-img" onerror="this.src='https://placehold.co/100x100?text=S/F'">
        <div>
            <h2 style="color: #152534;"><?= htmlspecialchars($eq['codigo_equipamento']) ?></h2>
            <p style="color: #6b7280; font-size: 14px;">Série: <?= htmlspecialchars($eq['numero_serie']) ?></p>
        </div>
    </div>

    <form method="post" id="formEditar">
        <div class="grid">
            <div>
                <label>Código (Bloqueado)</label>
                <input type="text" class="readonly" value="<?= $eq['codigo_equipamento'] ?>" readonly>
            </div>
            <div>
                <label>Nº de Série (Bloqueado)</label>
                <input type="text" class="readonly" value="<?= $eq['numero_serie'] ?>" readonly>
            </div>
        </div>

        <p class="secao-titulo">Campos Editáveis</p>
        <div class="grid">
            <div class="full">
                <label>Nome do Arquivo da Imagem</label>
                <input type="text" name="nome_imagem" value="<?= htmlspecialchars($eq['imagem']) ?>" placeholder="Ex: dell-latitude.jpg">
            </div>
            <div>
                <label>Status do Equipamento</label>
                <select name="status">
                    <option value="disponivel" <?= $eq['status'] == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                    <option value="locado" <?= $eq['status'] == 'locado' ? 'selected' : '' ?>>Locado</option>
                    <option value="manutencao" <?= $eq['status'] == 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
                </select>
            </div>

            <div>
                <label>Cliente Vinculado (Bloqueado para alteração)</label>
                <input type="text" class="readonly" readonly 
                       value="<?= $eq['cliente_id'] ? '['.$eq['cliente_id'].'] '.($eq['nome_fantasia'] ?: $eq['razao_social']) : 'NENHUM CLIENTE' ?>">
            </div>
        </div>

        <p class="secao-titulo">Informações de Localização e Contato (Consulta Automática)</p>
        <div class="grid">
            <div class="full">
                <label>Endereço de Instalação</label>
                <textarea id="cli_endereco" class="readonly" rows="2" readonly><?= $eq['endereco'] ?></textarea>
            </div>
            <div>
                <label>Contato Principal</label>
                <input type="text" id="cli_c1" class="readonly" value="<?= $eq['contato_principal'] ?>" readonly>
            </div>
            <div>
                <label>Contato Secundário / Email</label>
                <input type="text" id="cli_c2" class="readonly" value="<?= $eq['contato_secundario'] ?>" readonly>
            </div>
        </div>

        <button type="submit" name="atualizar" class="btn-save">SALVAR ALTERAÇÕES</button>
        
        <button type="submit" name="excluir" class="btn-delete" onclick="return confirmarExclusao();">
            EXCLUIR EQUIPAMENTO DEFINITIVAMENTE
        </button>
    </form>
</main>

<script>
function confirmarExclusao() {
    return confirm("ATENÇÃO: Você tem certeza que deseja EXCLUIR este equipamento? Esta ação não pode ser desfeita!");
}
</script>

</body>
</html>