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

/* BUSCA EQUIPAMENTOS PARA O SELECT */
$stmt_eq = $pdo->query("SELECT e.id, e.codigo_equipamento, c.nome_fantasia, c.razao_social 
                        FROM equipamentos e 
                        LEFT JOIN clientes c ON e.cliente_id = c.id 
                        ORDER BY e.codigo_equipamento ASC");
$equipamentos = $stmt_eq->fetchAll();

/* PROCESSA A ABERTURA */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $equipamento_id = $_POST['equipamento_id'];
    $prioridade = $_POST['prioridade'];
    $descricao = $_POST['descricao'];
    $status = "aberto";
    $data_abertura = date('Y-m-d H:i:s');

    $sql = "INSERT INTO chamados (equipamento_id, prioridade, descricao, status, data_abertura) VALUES (?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$equipamento_id, $prioridade, $descricao, $status, $data_abertura]);

    header("Location: chamados.php?msg=aberto");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Abrir Chamado | VaultCore</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
    
    html, body { height: 100%; }
    
    body { 
        background: #f2f4f6; 
        color: #1f2937; 
        padding-top: 140px; 
        overflow-y: scroll; /* BARRINHA SEMPRE AQUI */
    }

    header { position: fixed; top: 0; width: 100%; height: 120px; background: #152534; display: flex; align-items: center; justify-content: space-between; padding: 0 70px; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 600; color: #ffffff; }

    .container { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 50px; }
    
    h2 { color: #152534; margin-bottom: 30px; border-left: 5px solid #1e899e; padding-left: 15px; }
    
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full { grid-column: span 2; }
    
    label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #1e899e; text-transform: uppercase; }
    input, select, textarea { width: 100%; padding: 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 15px; outline: none; background: #f9fafb; transition: 0.2s; }
    input:focus, select:focus, textarea:focus { border-color: #1e899e; background: #fff; box-shadow: 0 0 0 4px rgba(30, 137, 158, 0.1); }

    /* BOTÃO CORRIGIDO */
    .btn-save { 
        grid-column: span 2; /* Garante que o botão ocupe a largura toda no grid */
        background: #1e899e; 
        color: #fff; 
        border: none; 
        padding: 18px; 
        border-radius: 10px; 
        font-weight: bold; 
        cursor: pointer; 
        font-size: 16px; 
        margin-top: 10px; 
        transition: 0.3s; 
    }
    .btn-save:hover { background: #152534; transform: translateY(-2px); }
    
    .btn-cancelar { display: block; text-align: center; color: #6b7280; text-decoration: none; margin-top: 15px; font-size: 14px; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="chamados.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <h2>Abrir Novo Chamado (O.S)</h2>
    
    <form method="post">
        <div class="grid">
            <div class="full">
                <label>Equipamento Afetado</label>
                <input type="text" id="busca_equipamento" list="lista_equipamentos" placeholder="Digite a TAG ou nome do cliente..." required autocomplete="off">
                
                <input type="hidden" name="equipamento_id" id="equipamento_id">

                <datalist id="lista_equipamentos">
                    <?php foreach($equipamentos as $e): 
                        $label = htmlspecialchars($e['codigo_equipamento']) . " (" . htmlspecialchars($e['nome_fantasia'] ?: ($e['razao_social'] ?: 'Estoque')) . ")";
                    ?>
                        <option value="<?= $label ?>" data-id="<?= $e['id'] ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="full">
                <label>Prioridade da Urgência</label>
                <select name="prioridade" required>
                    <option value="baixa">BAIXA (Rotina)</option>
                    <option value="media" selected>MÉDIA (Importante)</option>
                    <option value="alta">ALTA (Crítico / Parado)</option>
                </select>
            </div>

            <div class="full">
                <label>Descrição do Problema / Solicitação</label>
                <textarea name="descricao" rows="5" placeholder="Descreva aqui o que está acontecendo com o equipamento..." required></textarea>
            </div>

            <button type="submit" class="btn-save">GERAR CHAMADO</button>
        </div>
        <a href="chamados.php" class="btn-cancelar">Cancelar e Sair</a>
    </form>
</main>
<script>
document.getElementById('busca_equipamento').addEventListener('input', function(e) {
    const input = e.target;
    const list = document.getElementById('lista_equipamentos');
    const hiddenInput = document.getElementById('equipamento_id');
    const inputValue = input.value;

    hiddenInput.value = ""; // Reseta o ID caso o usuário apague o texto

    // Percorre as opções do datalist para encontrar uma correspondente
    Array.from(list.options).forEach(function(option) {
        if (option.value === inputValue) {
            hiddenInput.value = option.getAttribute('data-id');
        }
    });
});

// Impede o envio do formulário se o ID não for encontrado (segurança extra)
document.querySelector('form').addEventListener('submit', function(e) {
    const hiddenInput = document.getElementById('equipamento_id');
    if (!hiddenInput.value) {
        alert("Por favor, selecione um equipamento válido da lista.");
        e.preventDefault();
    }
});
</script>
</body>
</html>