<?php
session_start();
$paginaAtiva = 'sobre';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Sobre Nós - VaultCore</title>

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
    <h1>Sobre a VaultCore</h1>

    <p>
        A VaultCore vende computadores revisados e prontos para uso,
        com procedência garantida e preços justos, para quem precisa de
        uma máquina confiável sem pagar o preço de um equipamento novo.
    </p>

    <p>
        Cada computador passa por testes antes de ser anunciado, e todo
        pedido feito no site sai com garantia — sem letras miúdas.
    </p>

    <p>
        Este é um ambiente de demonstração: as compras são fictícias e
        nenhuma cobrança real é feita, mas o catálogo, o estoque e o
        checkout funcionam como em uma loja de verdade.
    </p>

    <div class="cards">
        <h2>Nossos pilares</h2>

        <div class="grid-cards">
            <div class="card">
                <h3>Missão</h3>
                <p>
                    Vender computadores de qualidade, com transparência sobre
                    o que está sendo entregue e um processo de compra simples.
                </p>
            </div>

            <div class="card">
                <h3>Visão</h3>
                <p>
                    Ser a primeira opção para quem busca um computador revisado
                    com confiança, do anúncio até a garantia.
                </p>
            </div>

            <div class="card">
                <h3>Valores</h3>
                <p>
                    Transparência com o cliente, preço justo e compromisso
                    com a qualidade de cada equipamento vendido.
                </p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
