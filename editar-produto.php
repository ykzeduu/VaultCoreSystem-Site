<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: produtos.php"); exit; }

/* PROCESSA EXCLUSÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir'])) {
    $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
    header("Location: produtos.php?msg=excluido");
    exit;
}

/* PROCESSA ATUALIZAÇÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar'])) {
    $nome           = trim($_POST['nome']);
    $descricao      = trim($_POST['descricao']);
    $especificacoes = trim($_POST['especificacoes']);
    $preco          = str_replace(',', '.', $_POST['preco']);
    $estoque        = (int)$_POST['estoque'];
    $imagem         = trim($_POST['imagem']);
    $categoria      = trim($_POST['categoria']) ?: 'desktop';
    $ativo          = isset($_POST['ativo']) ? 1 : 0;

    $sql = "UPDATE produtos SET nome=?, descricao=?, especificacoes=?, preco=?, estoque=?, imagem=?, categoria=?, ativo=? WHERE id=?";
    $pdo->prepare($sql)->execute([$nome, $descricao, $especificacoes, $preco, $estoque, $imagem, $categoria, $ativo, $id]);

    header("Location: produtos.php?msg=atualizado");
    exit;
}

$diretorio = "assets/img/";
$imagens_disponiveis = [];
if (is_dir($diretorio)) {
    $imagens_disponiveis = preg_grep('~\.(jpeg|jpg|png|webp|svg)$~i', scandir($diretorio));
    $imagens_disponiveis = array_values(array_diff($imagens_disponiveis, ['logo.svg']));
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) { header("Location: produtos.php"); exit; }

$nomeArquivoAtual = basename($p['imagem'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Produto | VaultCore Admin</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <a href="produtos.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <div class="header-form">
        <img src="<?= htmlspecialchars($p['imagem']) ?>" class="preview-img" style="width:100px;">
        <div>
            <h2 style="color: #152534;"><?= htmlspecialchars($p['nome']) ?></h2>
            <p style="color: #6b7280; font-size: 14px;"><?= htmlspecialchars($p['especificacoes']) ?></p>
        </div>
    </div>

    <form method="post">
        <div class="grid">
            <div class="full">
                <label>Nome do produto</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($p['nome']) ?>" required>
            </div>

            <div class="full">
                <label>Especificações</label>
                <input type="text" name="especificacoes" value="<?= htmlspecialchars($p['especificacoes']) ?>">
            </div>

            <div class="full">
                <label>Descrição</label>
                <textarea name="descricao" rows="3"><?= htmlspecialchars($p['descricao']) ?></textarea>
            </div>

            <div class="half">
                <label>Preço (R$)</label>
                <input type="text" name="preco" value="<?= $p['preco'] ?>" required>
            </div>

            <div class="half">
                <label>Estoque disponível</label>
                <input type="number" name="estoque" min="0" value="<?= $p['estoque'] ?>" required>
                <span class="info-secundaria">Ajuste aqui sempre que entrar ou sair equipamento do estoque da loja.</span>
            </div>

            <div class="half">
                <label>Categoria</label>
                <input type="text" name="categoria" value="<?= htmlspecialchars($p['categoria']) ?>">
            </div>

            <div class="half">
                <label>Visível na loja?</label>
                <select name="ativo">
                    <option value="1" <?= $p['ativo'] ? 'selected' : '' ?>>Sim, mostrar no site</option>
                    <option value="0" <?= !$p['ativo'] ? 'selected' : '' ?>>Não, manter oculto</option>
                </select>
            </div>

            <div class="full">
                <label>Imagem</label>
                <select name="imagem" id="imagem_escolhida" onchange="atualizarPreview()">
                    <?php foreach ($imagens_disponiveis as $img): ?>
                        <option value="assets/img/<?= htmlspecialchars($img) ?>" <?= $nomeArquivoAtual === $img ? 'selected' : '' ?>>
                            <?= htmlspecialchars($img) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" name="atualizar" class="btn-save">SALVAR ALTERAÇÕES</button>

        <button type="submit" name="excluir" class="btn-delete" onclick="return confirmarExclusao();">
            EXCLUIR PRODUTO DEFINITIVAMENTE
        </button>
    </form>
</main>

<script>
function confirmarExclusao() {
    return confirm("ATENÇÃO: Você tem certeza que deseja EXCLUIR este produto da loja? Esta ação não pode ser desfeita!");
}
function atualizarPreview() {
    const select = document.getElementById('imagem_escolhida');
    document.querySelector('.preview-img').src = select.value;
}
</script>

</body>
</html>
