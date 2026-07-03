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
    input, select { width: 100%; padding: 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px; outline: none; background: #f9fafb; }
    input:focus, select:focus { border-color: #1e899e; background: #fff; }

    .btn-save { background: #1e899e; color: #fff; border: none; padding: 18px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; margin-top: 20px; transition: 0.3s; }
    .btn-save:hover { background: #152534; }
    
    .btn-cancelar { display: block; text-align: center; color: #6b7280; text-decoration: none; margin-top: 15px; font-size: 14px; }

    .preview-container { text-align: center; margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; }
    #img-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; }
</style>
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