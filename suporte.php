<?php
session_start();
$paginaAtiva = 'suporte';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Suporte Técnico - VaultCore</title>

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
        <a href="suporte.php" class="<?= $paginaAtiva == 'suporte' ? 'ativo' : '' ?>">Suporte</a>
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
            <h1>Suporte Técnico Especializado</h1>
            <p>
                A VaultCore oferece suporte técnico estruturado para garantir
                desempenho, segurança e continuidade da sua infraestrutura.
            </p>
            <p>
                Atuamos de forma preventiva e corretiva, reduzindo falhas e
                assegurando a operação do seu negócio.
            </p>
        </div>

        <div class="cta-box">
            <h3>Precisa de ajuda agora?</h3>
            <p>
                Entre em contato pelo WhatsApp ou acesse o painel
                para abrir um chamado diretamente no sistema.
            </p>

            <div class="cta-botoes">
                <a href="#" class="btn btn-whatsapp" onclick="abrirWhatsApp()">
                    Falar no WhatsApp
                </a>

                <a href="login.php" class="btn btn-login">
                    Abrir Chamado
                </a>
            </div>
        </div>
    </div>

    <div class="servicos">
        <h2>Como funciona o suporte</h2>

        <div class="grid-servicos">
            <div class="card">
                <h4>Suporte Remoto</h4>
                <p>Diagnóstico e resolução rápida de falhas à distância.</p>
            </div>

            <div class="card">
                <h4>Suporte Presencial</h4>
                <p>Atendimento no local conforme contrato.</p>
            </div>

            <div class="card">
                <h4>Horário de Atendimento</h4>
                <p>Segunda a sexta-feira, das 08h às 18h.</p>
            </div>

            <div class="card">
                <h4>Abertura de Chamados</h4>
                <p>Via painel, WhatsApp ou canais oficiais.</p>
            </div>
        </div>
    </div>

</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

<script>
function abrirWhatsApp() {
    const hora = new Date().getHours();
    let saudacao = "Olá";

    if (hora >= 5 && hora < 12) saudacao = "Bom dia";
    else if (hora >= 12 && hora < 18) saudacao = "Boa tarde";
    else saudacao = "Boa noite";

    const mensagem = `${saudacao}! Possuo um equipamento locado e gostaria de suporte.`;
    const telefone = "67993120653";

    window.open(`https://wa.me/${telefone}?text=${encodeURIComponent(mensagem)}`, "_blank");
}
</script>

</body>
</html>
