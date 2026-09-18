<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/upload.php';

$erro = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome           = trim($_POST['nome']);
    $descricao      = trim($_POST['descricao']);
    $especificacoes = trim($_POST['especificacoes']);
    $preco          = str_replace(',', '.', $_POST['preco']);
    $estoque        = (int)$_POST['estoque'];
    $categoria      = trim($_POST['categoria']) ?: 'desktop';
    $ativo          = isset($_POST['ativo']) ? 1 : 0;

    $upload = processarUploadImagem('imagem', 'assets/uploads/produtos');

    if ($upload['erro']) {
        $erro = $upload['erro'];
    } elseif ($nome === '' || $preco === '') {
        $erro = 'Preencha ao menos o nome e o preço do produto.';
    } else {
        $imagem = $upload['caminho'] ?: 'assets/img/pc-essencial-a.svg'; // imagem padrão se nada for enviado

        try {
            $sql = "INSERT INTO produtos (nome, descricao, especificacoes, preco, estoque, imagem, categoria, ativo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$nome, $descricao, $especificacoes, $preco, $estoque, $imagem, $categoria, $ativo]);

            header("Location: produtos.php");
            exit;
        } catch (PDOException $e) {
            $erro = ($e->getCode() == 23000)
                ? 'Já existe um produto com esse nome.'
                : 'Erro ao salvar o produto.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Produto | VaultCore</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
    <a href="produtos.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2>Cadastrar Produto da Loja</h2>

    <?php if ($erro): ?>
        <div class="alerta"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="preview-container">
        <label>Visualização</label>
        <img id="img-preview" src="https://placehold.co/160x120?text=Selecione" alt="Preview">
    </div>

    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="full">
                <label>Nome do produto</label>
                <input type="text" name="nome" placeholder="Ex: Essencial A" required>
            </div>

            <div class="full">
                <label>Especificações (aparece como legenda)</label>
                <input type="text" name="especificacoes" placeholder="Ex: i5 / 8 GB RAM / SSD 240 GB">
            </div>

            <div class="full">
                <label>Descrição</label>
                <textarea name="descricao" rows="3" placeholder="Breve descrição para o cliente"></textarea>
            </div>

            <div>
                <label>Preço (R$)</label>
                <input type="text" name="preco" placeholder="Ex: 1590.00" required>
            </div>

            <div>
                <label>Estoque disponível</label>
                <input type="number" name="estoque" min="0" value="0" required>
            </div>

            <div>
                <label>Categoria</label>
                <input type="text" name="categoria" placeholder="desktop" value="desktop">
            </div>

            <div>
                <label>Visível na loja?</label>
                <select name="ativo">
                    <option value="1">Sim, mostrar no site</option>
                    <option value="0">Não, manter oculto</option>
                </select>
            </div>

            <div class="full">
                <label>Foto do produto</label>
                <input type="file" name="imagem" id="imagem" accept="image/png,image/jpeg,image/webp,image/svg+xml" onchange="atualizarPreview()">
                <span class="info-secundaria">JPG, PNG, WEBP ou SVG — até 5 MB. Se deixar em branco, uso uma ilustração padrão.</span>
            </div>

            <div class="full">
                <button type="submit" class="btn-save">SALVAR PRODUTO</button>
            </div>
        </div>
        <a href="produtos.php" class="btn-cancelar">Cancelar e Sair</a>
    </form>
</main>

<script>
function atualizarPreview() {
    const input = document.getElementById('imagem');
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
    } else {
        preview.src = 'https://placehold.co/160x120?text=Selecione';
    }
}
</script>

</body>
</html>
