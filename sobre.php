<?php
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
        <a href="suporte.php" class="<?= $paginaAtiva == 'suporte' ? 'ativo' : '' ?>">Suporte</a>
        <a href="trabalhe-conosco.php" class="<?= $paginaAtiva == 'trabalhe' ? 'ativo' : '' ?>">Trabalhe Conosco</a>
    </nav>

    <a href="login.php" class="admin-btn">Área Administrativa</a>
</header>

<section class="conteudo">
    <h1>Sobre a VaultCore</h1>

    <p>
        A VaultCore atua na gestão de infraestrutura de TI, oferecendo
        equipamentos padronizados, suporte técnico estruturado e contratos
        claros para empresas que buscam eficiência e previsibilidade.
    </p>

    <p>
        Nosso modelo elimina custos elevados com aquisição e manutenção
        de hardware, permitindo que nossos clientes foquem no crescimento
        do negócio sem preocupações operacionais.
    </p>

    <p>
        Trabalhamos com equipamentos revisados, monitorados e preparados
        para uso profissional, assegurando desempenho, segurança e
        continuidade dos serviços.
    </p>

    <div class="cards">
        <h2>Nossos pilares</h2>

        <div class="grid-cards">
            <div class="card">
                <h3>Missão</h3>
                <p>
                    Garantir infraestrutura de TI confiável por meio de
                    soluções práticas, suporte eficiente e gestão responsável.
                </p>
            </div>

            <div class="card">
                <h3>Visão</h3>
                <p>
                    Ser referência em gestão de infraestrutura de TI,
                    reconhecida pela confiabilidade e excelência operacional.
                </p>
            </div>

            <div class="card">
                <h3>Valores</h3>
                <p>
                    Transparência, compromisso com o cliente,
                    segurança da informação e melhoria contínua.
                </p>
            </div>
        </div>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

</body>
</html>
