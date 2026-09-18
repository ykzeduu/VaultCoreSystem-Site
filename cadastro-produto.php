<?php
session_start();

if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/includes/config.php';

$diretorio = "assets/img/";
$imagens_disponiveis = [];
if (is_dir($diretorio)) {
    $imagens_disponiveis = preg_grep('~\.(jpeg|jpg|png|webp|svg)$~i', scandir($diretorio));
    $imagens_disponiveis = array_values(array_diff($imagens_disponiveis, ['logo.svg']));
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome           = trim($_POST['nome']);
    $descricao      = trim($_POST['descricao']);
    $especificacoes = trim($_POST['especificacoes']);
    $preco          = str_replace(',', '.', $_POST['preco']);
    $estoque        = (int)$_POST['estoque'];
    $imagem         = 'assets/img/' . $_POST['imagem_escolhida'];
    $categoria      = trim($_POST['categoria']) ?: 'desktop';
    $ativo          = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $preco === '') {
        $erro = 'Preencha ao menos o nome e o preço do produto.';
    } else {
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

    <form method="post">
        <div class="grid">
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

            <div class="half">
                <label>Preço (R$)</label>
                <input type="text" name="preco" placeholder="Ex: 1590.00" required>
            </div>

            <div class="half">
                <label>Estoque disponível</label>
                <input type="number" name="estoque" min="0" value="0" required>
            </div>

            <div class="half">
                <label>Categoria</label>
                <input type="text" name="categoria" placeholder="desktop" value="desktop">
            </div>

            <div class="half">
                <label>Visível na loja?</label>
                <select name="ativo">
                    <option value="1">Sim, mostrar no site</option>
                    <option value="0">Não, manter oculto</option>
                </select>
            </div>

            <div class="full">
                <label>Imagem</label>
                <select name="imagem_escolhida" id="imagem_escolhida" onchange="atualizarPreview()" required>
                    <option value="">-- Escolha a imagem --</option>
                    <?php foreach ($imagens_disponiveis as $img): ?>
                        <option value="<?= htmlspecialchars($img) ?>"><?= htmlspecialchars($img) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="info-secundaria">As imagens vêm da pasta assets/img/. Adicione arquivos novos lá para aparecerem aqui.</span>
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
    const select = document.getElementById('imagem_escolhida');
    const preview = document.getElementById('img-preview');
    const pasta = 'assets/img/';
    preview.src = select.value ? pasta + select.value : 'https://placehold.co/160x120?text=Selecione';
}
</script>

</body>
</html>
