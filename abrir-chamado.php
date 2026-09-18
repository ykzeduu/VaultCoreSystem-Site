<?php
session_start();

/* PROTEÇÃO */
if (!isset($_SESSION["colaborador"]) || $_SESSION["colaborador"] !== true) {
    header("Location: login.php");
    exit;
}

/* CONEXÃO MYSQL */
require __DIR__ . '/includes/config.php';

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
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="logo"><img src="assets/img/logo.svg" alt="VaultCore"></div>
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