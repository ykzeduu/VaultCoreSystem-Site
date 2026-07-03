<?php
session_start();

if (!isset($_SESSION['codigo_equipamento'])) {
    header("Location: login.php");
    exit;
}

$codigo = $_SESSION['codigo_equipamento'];

try {
    $pdo = new PDO(
        "mysql:host=sql311.infinityfree.com;dbname=if0_41023013_db_clientes;charset=utf8",
        "if0_41023013",
        "2kVHu71TF3ly",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro de conexão.");
}

/* BUSCA DADOS COMPLETOS (Incluindo os novos campos de endereço e contato) */
$sql = "SELECT e.*, c.razao_social, c.cnpj, c.endereco, c.contato_principal, c.contato_secundario 
        FROM equipamentos e 
        JOIN clientes c ON e.cliente_id = c.id 
        WHERE e.codigo_equipamento = ? 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$codigo]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    die("Equipamento não encontrado.");
}

/* PROCESSA CHAMADO */
$mensagem_sucesso = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['abrir_chamado'])) {
    $descricao = $_POST['descricao'];
    $prioridade = $_POST['prioridade'];
    $equip_id = $dados['id'];

    $sql_chamado = "INSERT INTO chamados (equipamento_id, descricao, prioridade) VALUES (?, ?, ?)";
    $stmt_ch = $pdo->prepare($sql_chamado);
    if ($stmt_ch->execute([$equip_id, $descricao, $prioridade])) {
        $mensagem_sucesso = "Chamado aberto com sucesso!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel do Cliente | VaultCore</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", sans-serif; }
    body { background: #f0f2f5; color: #1a202c; }

    header { background: #152534; height: 100px; display: flex; align-items: center; justify-content: space-between; padding: 0 5%; }
    header h1 { color: #fff; font-size: 22px; }
    .btn-sair { color: #fff; text-decoration: none; padding: 10px 20px; background: rgba(255,255,255,0.1); border-radius: 8px; font-size: 14px; }

    .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

    .card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
    .img-box { width: 100%; border-radius: 15px; overflow: hidden; margin-bottom: 20px; border: 1px solid #eee; }
    .img-box img { width: 100%; height: auto; display: block; }
    
    .codigo-titulo { font-size: 32px; color: #152534; margin-bottom: 20px; font-weight: 800; }
    .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #edf2f7; }
    .label { color: #718096; font-size: 14px; }
    .valor { font-weight: 600; }

    .status { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .disponivel { background: #d1fae5; color: #065f46; }
    .locado { background: #ebf8ff; color: #3182ce; }
    .manutencao { background: #fef3c7; color: #92400e; }

    /* SEÇÃO ENDEREÇO E CONTATO (CONFORME SUA SOLICITAÇÃO) */
    .cliente-detalhes { margin-top: 20px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e0; }
    .cliente-detalhes p { font-size: 14px; margin-bottom: 8px; line-height: 1.4; color: #4a5568; }
    .cliente-detalhes strong { color: #152534; }

    /* CHAMADO E WHATSAPP */
    .form-chamado { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 15px rgba(0,0,0,0.05); border-top: 5px solid #1e899e; }
    textarea, select { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #d1d5db; margin-bottom: 15px; font-size: 15px; }
    .btn-abrir { width: 100%; padding: 15px; background: #1e899e; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-bottom: 15px; font-size: 16px; }
    .btn-abrir:hover { background: #152534; }
    
    /* BOTÃO WHATSAPP */
    .btn-wpp { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 15px; background: #25d366; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 700; transition: 0.3s; font-size: 16px; }
    .btn-wpp:hover { background: #128c7e; transform: translateY(-2px); }

    .alerta { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #34d399; }

    @media (max-width: 850px) { .container { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<header>
    <h1>Portal do Cliente | VaultCore</h1>
    <a href="logout.php" class="btn-sair">Sair</a>
</header>

<main class="container">
    <div class="col-info">
        <?php if ($mensagem_sucesso): ?>
            <div class="alerta"><?= $mensagem_sucesso ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="img-box">
                <img src="assets/fotos-equipamentos/<?= $dados['imagem'] ?: 'placeholder.png' ?>" onerror="this.src='https://placehold.co/400x300?text=Sem+Foto'">
            </div>
            <h2 class="codigo-titulo"><?= $dados['codigo_equipamento'] ?></h2>
            
            <div class="info-row">
                <span class="label">Status do Equipamento</span>
                <span class="status <?= $dados['status'] ?>"><?= ucfirst($dados['status']) ?></span>
            </div>

            <div class="info-row">
                <span class="label">Nº de Série</span>
                <span class="valor"><?= $dados['numero_serie'] ?></span>
            </div>

            <div class="cliente-detalhes">
                <p><strong>Empresa:</strong> <?= htmlspecialchars($dados['razao_social']) ?></p>
                <p><strong>CNPJ:</strong> <?= htmlspecialchars($dados['cnpj']) ?></p>
                <p><strong>Endereço:</strong> <?= htmlspecialchars($dados['endereco'] ?: 'Não cadastrado') ?></p>
                <p><strong>Contato Principal:</strong> <?= htmlspecialchars($dados['contato_principal'] ?: 'Não informado') ?></p>
                <?php if($dados['contato_secundario']): ?>
                    <p><strong>Outro Contato:</strong> <?= htmlspecialchars($dados['contato_secundario']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-chamado">
        <div class="form-chamado">
            <h3 style="margin-bottom: 5px; color: #152534;">Suporte Técnico</h3>
            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">Problemas com o equipamento? Mande uma mensagem.</p>
            
            <form method="post">
                <textarea name="descricao" rows="4" placeholder="Descreva o que está acontecendo..." required></textarea>
                <label style="font-size: 12px; font-weight: bold; color: #1e899e; display: block; margin-bottom: 5px;">URGÊNCIA:</label>
                <select name="prioridade">
                    <option value="baixa">Baixa (Dúvidas/Configuração)</option>
                    <option value="media" selected>Média (Lentidão/Software)</option>
                    <option value="alta">Alta (Não liga/Parado)</option>
                </select>
                <button type="submit" name="abrir_chamado" class="btn-abrir">Enviar Chamado</button>
            </form>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0 20px;">

            <a href="https://wa.me/5567993120653?text=Olá,%20estou%20com%20um%20problema%20no%20equipamento%20<?= $dados['codigo_equipamento'] ?>" target="_blank" class="btn-wpp">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93a7.898 7.898 0 0 0-2.327-5.594l.008-.008zM7.994 14.52a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                </svg>
                Chamar no WhatsApp
            </a>
        </div>
    </div>
</main>

</body>
</html>