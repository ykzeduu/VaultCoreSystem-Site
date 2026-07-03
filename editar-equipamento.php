<?php
session_start();

/* PROTEÇÃO */
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
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", sans-serif; }
    body { background: #f2f4f6; padding-top: 140px; color: #1f2937; }
    header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 600; color: #ffffff; }
    .container { max-width: 850px; margin: auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 50px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full { grid-column: span 2; }
    label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #1e899e; text-transform: uppercase; }
    input, select, textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px; outline: none; }
    .readonly { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; cursor: not-allowed; font-weight: 500; }
    .btn-save { background: #1e899e; color: #fff; border: none; padding: 16px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 30px; width: 100%; font-size: 16px; transition: 0.3s; }
    .btn-save:hover { background: #152534; }
    .btn-delete { background: #fff; color: #dc2626; border: 2px solid #dc2626; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 15px; width: 100%; font-size: 14px; transition: 0.3s; }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    .header-form { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
    .preview-img { width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 3px solid #f2f4f6; }
    .secao-titulo { margin: 20px 0 10px; padding-left: 5px; border-left: 4px solid #1e899e; font-size: 14px; color: #152534; }
    .search-focus { border: 2px solid #1e899e; background: #fff; font-weight: 600; }
</style>
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