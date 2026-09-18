<?php
$paginaAtiva = 'inicio';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>VaultCore - Locação de Equipamentos de TI</title>

<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="assets/img/logo.svg" alt="VaultCore">
    </div>

    <nav>
        <a href="index.php" class="<?= $paginaAtiva == 'inicio' ? 'ativo' : '' ?>">Início</a>
        <a href="sobre.php">Sobre nós</a>
        <a href="suporte.php">Suporte</a>
        <a href="trabalhe-conosco.php">Trabalhe Conosco</a>
    </nav>

    <a href="login.php" class="admin-btn">Área Administrativa</a>
</header>

<section class="hero">
    <h2>Locação profissional de computadores</h2>
    <p>Soluções completas em infraestrutura de TI com previsibilidade, suporte e desempenho.</p>
</section>

<section class="modelos">
    <h2>Modelos disponíveis</h2>

    <div class="grid-modelos">
        <div class="modelo">
            <img src="assets/img/pc-essencial-a.svg">
            <h3>Essencial A</h3>
            <p>i5 / 8 GB RAM / SSD 240 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Essencial A (i5 / 8GB / SSD 240GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="assets/img/pc-essencial-b.svg">
            <h3>Essencial B</h3>
            <p>i5 / 16 GB RAM / SSD 480 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Essencial B (i5 / 16GB / SSD 480GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="assets/img/pc-performance-a.svg">
            <h3>Performance A</h3>
            <p>i7 / 16 GB RAM / SSD 240 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Performance A (i7 / 16GB / SSD 240GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="assets/img/pc-performance-b.svg">
            <h3>Performance B</h3>
            <p>i7 / 16 GB RAM / SSD 480 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Performance B (i7 / 16GB / SSD 480GB).')">
            Solicitar
            </a>
        </div>
    </div>
</section>

<section class="manutencao">
    <div class="box">
        <h2>Manutenção e suporte</h2>
        <p>
            Não deixe sua operação parar por problemas técnicos. Conheça nossos planos de gestão e manutenção de equipamentos: uma solução completa que une prevenção inteligente, especialistas prontos para o atendimento e reposição ágil de hardware. Mais do que suporte, entregamos a garantia de que sua infraestrutura estará sempre disponível e atualizada para os desafios do dia a dia.
        </p>
    </div>
</section>

<section class="planos">
    <h2>Categorias</h2>

    <div class="grid-planos">
        <div class="plano bronze">
            <h3>🥉 Categoria Bronze: Estabilidade e Economia</h3>
            <p>"O essencial para o dia a dia." Perfeito para empresas que buscam funcionalidade e o melhor custo-benefício do mercado. Com equipamentos de 3ª e 4ª geração, você garante uma estrutura sólida para tarefas rotinas e operacionais, mantendo a produtividade em dia sem nenhum acréscimo no valor do seu plano base.</p>
        </div>

        <div class="plano prata">
            <h3>🥈 Categoria Prata: Modernidade e Versatilidade</h3>
            <p>"Equilíbrio ideal entre performance e tecnologia." Eleve o nível da sua operação com processadores de 8ª e 9ª geração. Esta categoria oferece total compatibilidade com o Windows 11, garantindo mais segurança, uma interface moderna e a agilidade necessária para fluxos de trabalho multitarefa, com um investimento adicional extremamente acessível.</p>
        </div>

        <div class="plano ouro">
            <h3>🥇 Categoria Ouro: Alta Performance e Inovação</h3>
            <p>"Potência máxima para quem não pode perder tempo." Projetada para profissionais que exigem o topo do desempenho. Equipados com processadores de 12ª geração ou superior, esses ativos entregam velocidade excepcional em processos pesados e softwares exigentes. A escolha definitiva para garantir longevidade tecnológica e máxima fluidez em cada segundo de trabalho.</p>
        </div>
    </div>
</section>

<section class="kits">
    <div class="conteudo">
        <img src="assets/img/kit-completo.svg">
        <div>
            <h2>Kits completos</h2>
            <p>
                Soluções prontas com CPU, monitor, teclado e mouse,
                ideais para empresas que precisam de implantação rápida
                e padronização de ambientes.
            </p>
        </div>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

<script>
function abrirWhatsApp(mensagemCustomizada = null) {
    const hora = new Date().getHours();
    let saudacao = "Olá";

    if (hora >= 5 && hora < 12) saudacao = "Bom dia";
    else if (hora >= 12 && hora < 18) saudacao = "Boa tarde";
    else saudacao = "Boa noite";

    const mensagemPadrao = `${saudacao}! Gostaria de mais informações sobre a locação de computadores.`;
    const mensagem = mensagemCustomizada ?? mensagemPadrao;

    const telefone = "67993120653"; // seu WhatsApp
    window.open(
        `https://wa.me/${telefone}?text=${encodeURIComponent(mensagem)}`,
        "_blank"
    );
}
</script>


</body>
</html>
