<?php
session_start();
$paginaAtiva = 'garantia';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Garantia - VaultCore</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="assets/img/logo.svg" alt="VaultCore">
    </div>

    <nav>
        <a href="index.php" class="<?= $paginaAtiva == 'inicio' ? 'ativo' : '' ?>">Início</a>
        <a href="sobre.php" class="<?= $paginaAtiva == 'sobre' ? 'ativo' : '' ?>">Sobre nós</a>
        <a href="garantia.php" class="<?= $paginaAtiva == 'garantia' ? 'ativo' : '' ?>">Garantia</a>
        <a href="trabalhe-conosco.php" class="<?= $paginaAtiva == 'trabalhe' ? 'ativo' : '' ?>">Trabalhe Conosco</a>
        <?php if (isset($_SESSION['loja_usuario_id'])): ?>
            <a href="meus-pedidos.php">Meus Pedidos</a>
        <?php else: ?>
            <a href="loja-login.php">Entrar / Cadastrar</a>
        <?php endif; ?>
    </nav>

    <a href="login.php" class="admin-btn">Área Administrativa</a>
</header>

<section class="conteudo">

    <div class="topo-suporte">
        <div>
            <h1>Garantia VaultCore</h1>
            <p>
                Todo equipamento vendido pela VaultCore sai revisado e testado,
                e conta com garantia contra defeitos de fabricação.
            </p>
            <p>
                Este é um site de demonstração: nenhuma venda real é processada
                e esta página descreve a política de garantia de forma ilustrativa.
            </p>
        </div>

        <div class="cta-box">
            <h3>Prazo de garantia</h3>
            <p>
                90 dias corridos a partir da data da compra, cobrindo defeitos
                de fabricação em hardware (fonte, placa-mãe, memória e armazenamento).
            </p>

            <div class="cta-botoes">
                <a href="meus-pedidos.php" class="btn">Ver meus pedidos</a>
            </div>
        </div>
    </div>

    <div class="servicos">
        <h2>Como funciona</h2>

        <div class="grid-servicos">
            <div class="card">
                <h4>O que cobre</h4>
                <p>Defeitos de fabricação identificados em condições normais de uso.</p>
            </div>

            <div class="card">
                <h4>O que não cobre</h4>
                <p>Danos por mau uso, quedas, líquidos ou violação do lacre.</p>
            </div>

            <div class="card">
                <h4>Prazo</h4>
                <p>90 dias corridos a partir da data de emissão do pedido.</p>
            </div>

            <div class="card">
                <h4>Como acionar</h4>
                <p>Consulte o pedido em "Meus Pedidos" e entre em contato informando o número do pedido.</p>
            </div>
        </div>
    </div>

</section>

<footer>
    © 2026 — VaultCore | Todos os direitos reservados.
</footer>

</body>
</html>
