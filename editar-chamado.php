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
if (!$id) { header("Location: chamados.php"); exit; }

/* PROCESSA EXCLUSÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir'])) {
    $sql_del = "DELETE FROM chamados WHERE id = ?";
    $pdo->prepare($sql_del)->execute([$id]);
    
    header("Location: chamados.php?msg=excluido");
    exit;
}

/* PROCESSA ATUALIZAÇÃO */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar'])) {
    $status = $_POST["status"];
    $prioridade = $_POST["prioridade"];
    $notas = $_POST["notas_tecnicas"];

    $sql = "UPDATE chamados SET status = ?, prioridade = ?, notas_tecnicas = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$status, $prioridade, $notas, $id]);
    
    header("Location: chamados.php?msg=atualizado");
    exit;
}

/* BUSCA DADOS DO CHAMADO + EQUIPAMENTO + CLIENTE 
   CORREÇÃO: LEFT JOIN no cliente para permitir visualizar chamados de equipamentos em estoque
*/
$stmt = $pdo->prepare("
    SELECT 
        ch.*, 
        e.codigo_equipamento, e.numero_serie, e.imagem, 
        c.razao_social, c.nome_fantasia, c.cnpj, c.endereco, c.contato_principal, c.contato_secundario 
    FROM chamados ch
    JOIN equipamentos e ON ch.equipamento_id = e.id
    LEFT JOIN clientes c ON e.cliente_id = c.id
    WHERE ch.id = ?
");
$stmt->execute([$id]);
$chamado = $stmt->fetch();

if (!$chamado) { header("Location: chamados.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Chamado | VaultCore Admin</title>
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
    .preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; background: #f2f4f6; }
    
    .secao-titulo { margin: 25px 0 10px; padding-left: 5px; border-left: 4px solid #1e899e; font-size: 14px; color: #152534; font-weight: bold; }
</style>
</head>
<body>

<header>
    <div class="logo">VaultCore System</div>
    <a href="chamados.php" style="color:#fff; text-decoration:none; font-weight: bold;">← VOLTAR</a>
</header>

<main class="container">
    <div class="header-form">
        <img src="assets/fotos-equipamentos/<?= $chamado['imagem'] ?: 'placeholder.png' ?>" class="preview-img" onerror="this.src='https://placehold.co/80x80?text=S/F'">
        <div>
            <h2 style="color: #152534;">Chamado #<?= $chamado['id'] ?></h2>
            <p style="color: #6b7280; font-size: 14px;">Status Atual: <strong><?= strtoupper(str_replace('_', ' ', $chamado['status'])) ?></strong></p>
        </div>
    </div>

    <form method="post">
        <p class="secao-titulo">Informações do Cliente e Localização</p>
        <div class="grid">
            <div class="full">
                <label>Nome Fantasia</label>
                <input type="text" class="readonly" value="<?= htmlspecialchars($chamado['nome_fantasia'] ?? 'Estoque / Sem Cliente') ?>" readonly>
            </div>
            <div class="full">
                <label>Razão Social / Nome</label>
                <input type="text" class="readonly" value="<?= htmlspecialchars($chamado['razao_social'] ?? 'N/A') ?>" readonly>
            </div>
            <div>
                <label>CNPJ</label>
                <input type="text" class="readonly" value="<?= htmlspecialchars($chamado['cnpj'] ?? '—') ?>" readonly>
            </div>
            <div>
                <label>Contato Principal</label>
                <input type="text" class="readonly" value="<?= htmlspecialchars($chamado['contato_principal'] ?? '—') ?>" readonly>
            </div>
            <div class="full">
                <label>Endereço de Atendimento</label>
                <textarea class="readonly" rows="2" readonly><?= htmlspecialchars($chamado['endereco'] ?? 'Equipamento em estoque') ?></textarea>
            </div>
        </div>

        <p class="secao-titulo">Dados do Equipamento</p>
        <div class="grid">
            <div>
                <label>Código do Ativo</label>
                <input type="text" class="readonly" value="<?= $chamado['codigo_equipamento'] ?>" readonly>
            </div>
            <div>
                <label>Nº de Série</label>
                <input type="text" class="readonly" value="<?= $chamado['numero_serie'] ?>" readonly>
            </div>
            <div class="full">
                <label>Descrição do Defeito (Relatado pelo Cliente)</label>
                <textarea class="readonly" rows="3" readonly><?= htmlspecialchars($chamado['descricao']) ?></textarea>
            </div>
        </div>

        <p class="secao-titulo">Gestão do Atendimento Técnico</p>
        <div class="grid">
            <div>
                <label>Status do Chamado</label>
                <select name="status">
                    <option value="aberto" <?= $chamado['status'] == 'aberto' ? 'selected' : '' ?>>🔴 Aberto / Pendente</option>
                    <option value="em_atendimento" <?= $chamado['status'] == 'em_atendimento' ? 'selected' : '' ?>>🔵 Em Atendimento</option>
                    <option value="concluido" <?= $chamado['status'] == 'concluido' ? 'selected' : '' ?>>🟢 Concluído / Resolvido</option>
                </select>
            </div>
            <div>
                <label>Prioridade</label>
                <select name="prioridade">
                    <option value="baixa" <?= $chamado['prioridade'] == 'baixa' ? 'selected' : '' ?>>Baixa</option>
                    <option value="media" <?= $chamado['prioridade'] == 'media' ? 'selected' : '' ?>>Média</option>
                    <option value="alta" <?= $chamado['prioridade'] == 'alta' ? 'selected' : '' ?>>Alta / Crítica</option>
                </select>
            </div>
            <div class="full">
                <label>Notas Técnicas / Diagnóstico / Resolução</label>
                <textarea name="notas_tecnicas" rows="5" placeholder="Registre aqui os detalhes técnicos do atendimento..."><?= htmlspecialchars($chamado['notas_tecnicas']) ?></textarea>
            </div>
            <div>
                <label>Data de Abertura</label>
                <input type="text" class="readonly" value="<?= date('d/m/Y H:i', strtotime($chamado['data_abertura'])) ?>" readonly>
            </div>
        </div>

        <button type="submit" name="atualizar" class="btn-save">ATUALIZAR E SALVAR CHAMADO</button>

        <button type="submit" name="excluir" class="btn-delete" onclick="return confirm('ATENÇÃO: Deseja apagar este registro de chamado permanentemente?');">
            EXCLUIR REGISTRO DO SISTEMA
        </button>
    </form>
</main>

</body>
</html>