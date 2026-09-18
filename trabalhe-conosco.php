<?php
session_start();
$paginaAtiva = 'trabalhe';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Trabalhe Conosco - VaultCore</title>

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
    <h1>Trabalhe Conosco</h1>

    <p>
        Buscamos profissionais comprometidos, com interesse em tecnologia,
        vendas e atendimento ao cliente.
    </p>

    <p>
        Caso tenha interesse em oportunidades futuras, parcerias ou
        prestação de serviços, preencha o formulário abaixo.
    </p>

    <div class="form-box">
        <h2>Envie suas informações</h2>

        <form>
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" placeholder="Seu nome completo">
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" placeholder="seuemail@email.com">
            </div>

            <div class="form-group">
                <label>Telefone / WhatsApp</label>
                <input type="text" placeholder="(xx) xxxxx-xxxx">
            </div>

            <div class="form-group">
                <label>Área de interesse</label>
                <input type="text" placeholder="Ex: Suporte técnico, TI, Administrativo">
            </div>

            <div class="form-group">
                <label>Mensagem</label>
                <textarea placeholder="Conte um pouco sobre você ou sua experiência"></textarea>
            </div>

            <button type="submit" class="btn-enviar">Enviar</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
