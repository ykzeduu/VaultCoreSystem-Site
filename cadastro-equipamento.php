<?php
session_start();

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/config.php';

/* 1. BUSCA LISTA DE IMAGENS PRÉ-PRONTAS NA PASTA */
$diretorio = "assets/fotos-equipamentos/";
$fotos_disponiveis = [];
if (is_dir($diretorio)) {
    $fotos_disponiveis = preg_grep('~\.(jpeg|jpg|png|webp)$~i', scandir($diretorio));
}

/* 2. PROCESSA O CADASTRO */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo = $_POST['codigo_equipamento'];
    $serie = $_POST['numero_serie'];
    $modelo = $_POST['modelo'];
    $geracao = $_POST['geracao']; // NOVO CAMPO COLETADO
    $status = 'disponivel'; 
    $cliente_id = null;      
    $imagem = $_POST['foto_escolhida']; 

    // Adicionado 'geracao' na query SQL
    $sql = "INSERT INTO equipamentos (codigo_equipamento, numero_serie, modelo, geracao, status, cliente_id, imagem) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$codigo, $serie, $modelo, $geracao, $status, $cliente_id, $imagem]);

    header("Location: equipamentos.php?msg=cadastrado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Equipamento | VaultCore</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="equipamentos.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2>Cadastrar Equipamento</h2>
    
    <div class="preview-container">
        <label>Visualização do Modelo</label>
        <img id="img-preview" src="https://placehold.co/120x120?text=Selecione" alt="Preview">
    </div>

    <form method="post">
        <div class="grid">
            <div class="full">
                <label>Plano / Modelo Base</label>
                <select name="modelo" required>
                    <option value="">-- Selecione o Modelo --</option>
                    <option value="Essencial A">Essencial A (i5 / 8GB / 240GB)</option>
                    <option value="Essencial B">Essencial B (i5 / 16GB / 480GB)</option>
                    <option value="Performance A">Performance A (i7 / 16GB / 240GB)</option>
                    <option value="Performance B">Performance B (i7 / 16GB / 480GB)</option>
                    <option value="Personalizado">Personalizado / Outro</option>
                </select>
            </div>

            <div class="full">
                <label>Geração do Ativo</label>
                <select name="geracao" required>
                    <option value="Bronze">Bronze (Padrão)</option>
                    <option value="Prata">Prata (+ R$ 40,00)</option>
                    <option value="Ouro">Ouro (+ R$ 100,00)</option>
                </select>
            </div>

            <div class="full">
                <label>Selecionar Imagem (Foto)</label>
                <select name="foto_escolhida" id="foto_escolhida" onchange="atualizarPreview()" required>
                    <option value="">-- Escolha o arquivo de imagem --</option>
                    <?php foreach($fotos_disponiveis as $foto): ?>
                        <option value="<?= $foto ?>"><?= $foto ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="full">
                <label>Código do Equipamento (TAG)</label>
                <input type="text" name="codigo_equipamento" placeholder="Ex: NOT-001" required>
            </div>

            <div class="full">
                <label>Número de Série (S/N)</label>
                <input type="text" name="numero_serie" placeholder="Ex: ABC123XYZ" required>
            </div>

            <div class="full">
                <button type="submit" class="btn-save">FINALIZAR E ENVIAR AO ESTOQUE</button>
            </div>
        </div>
        <a href="equipamentos.php" class="btn-cancelar">Cancelar e Sair</a>
    </form>
</main>

<script>
function atualizarPreview() {
    const select = document.getElementById('foto_escolhida');
    const preview = document.getElementById('img-preview');
    const pasta = 'assets/fotos-equipamentos/';
    if(select.value) { preview.src = pasta + select.value; } 
    else { preview.src = 'https://placehold.co/120x120?text=Selecione'; }
}
</script>

</body>
</html>