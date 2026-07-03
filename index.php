<?php
$paginaAtiva = 'inicio';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>VaultCore - Locação de Equipamentos de TI</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body {
    background: #f4f7fb;
    color: #1f2937;
}

/* HEADER */
header {
    position: fixed;
    top: 0;
    width: 100%;
    height: 110px;
    background: linear-gradient(90deg, #0f2435, #152534);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 70px;
    z-index: 1000;
}

.logo {
    display: flex;
    align-items: center;
}

.logo img {
    height: 136px;       /* tamanho ideal pro header */
    width: auto;
    object-fit: contain;
}

/* MENU */
nav {
    display: flex;
    gap: 35px;
}

nav a {
    color: #d1d5db;
    text-decoration: none;
    font-size: 16px;
    padding-bottom: 6px;
    border-bottom: 3px solid transparent;
    transition: 0.2s;
}

nav a:hover {
    color: #ffffff;
}

nav a.ativo {
    color: #65c9d1;
    border-bottom: 3px solid #65c9d1;
}

/* BOTÃO ADMIN */
.admin-btn {
    background: #1e899e;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.admin-btn:hover {
    background: #65c9d1;
    color: #0f2435;
}

/* HERO */
.hero {
    padding-top: 190px;
    padding-bottom: 100px;
    background: #ffffff;
    text-align: center;
}

.hero h2 {
    font-size: 42px;
    color: #0f2435;
    margin-bottom: 20px;
}

.hero p {
    font-size: 18px;
    color: #4b5563;
    max-width: 760px;
    margin: 0 auto;
}

/* MODELOS */
.modelos {
    max-width: 1200px;
    margin: 90px auto;
    padding: 0 30px;
}

.modelos h2 {
    font-size: 32px;
    text-align: center;
    margin-bottom: 50px;
    color: #0f2435;
}

.grid-modelos {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.modelo {
    background: #ffffff;
    border-radius: 16px;
    padding: 30px;
    border: 1px solid #e5e7eb;
    text-align: center;
    transition: 0.25s;
}

.modelo:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.08);
}

.modelo img {
    width: 100%;
    max-height: 140px;
    object-fit: contain;
    margin-bottom: 20px;
}

.modelo h3 {
    font-size: 20px;
    color: #1e899e;
    margin-bottom: 10px;
}

.modelo p {
    font-size: 15px;
    color: #4b5563;
    margin-bottom: 18px;
}

.modelo a {
    display: inline-block;
    background: #1e899e;
    color: #ffffff;
    padding: 10px 22px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}

.modelo a:hover {
    background: #65c9d1;
    color: #0f2435;
}

/* MANUTENÇÃO */
.manutencao {
    background: #ffffff;
    padding: 80px 30px;
}

.manutencao .box {
    max-width: 1000px;
    margin: 0 auto;
    background: #f9fafb;
    padding: 50px;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
}

.manutencao h2 {
    font-size: 30px;
    margin-bottom: 20px;
    color: #0f2435;
}

.manutencao p {
    font-size: 17px;
    line-height: 1.8;
    color: #4b5563;
}

/* PLANOS */
.planos {
    max-width: 1200px;
    margin: 90px auto;
    padding: 0 30px;
}

.planos h2 {
    font-size: 32px;
    text-align: center;
    margin-bottom: 50px;
    color: #0f2435;
}

.grid-planos {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.plano {
    background: #ffffff;
    padding: 35px;
    border-radius: 16px;
    border: 5px solid;
}

.bronze { border-color: #cd7f32; }
.prata { border-color: #c0c0c0; }
.ouro { border-color: #d4af37; }

.plano h3 {
    font-size: 22px;
    margin-bottom: 15px;
}

.plano p {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.7;
}

/* KITS */
.kits {
    background: #ffffff;
    padding: 90px 30px;
}

.kits .conteudo {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
}

.kits img {
    width: 100%;
    border-radius: 18px;
}

.kits h2 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #0f2435;
}

.kits p {
    font-size: 17px;
    color: #4b5563;
    line-height: 1.8;
}

/* FOOTER */
footer {
    background: #0f2435;
    color: #ffffff;
    text-align: center;
    padding: 30px;
    font-size: 14px;
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="VaultCore">
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
            <img src="pc1.webp">
            <h3>Essencial A</h3>
            <p>i5 / 8 GB RAM / SSD 240 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Essencial A (i5 / 8GB / SSD 240GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="pc2.webp">
            <h3>Essencial B</h3>
            <p>i5 / 16 GB RAM / SSD 480 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Essencial B (i5 / 16GB / SSD 480GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="pc3.webp">
            <h3>Performance A</h3>
            <p>i7 / 16 GB RAM / SSD 240 GB</p>
            <a href="#" onclick="abrirWhatsApp('Olá! Tenho interesse no modelo Performance A (i7 / 16GB / SSD 240GB).')">
            Solicitar
            </a>
        </div>

        <div class="modelo">
            <img src="pc4.webp">
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
        <img src="setup-completo.jpg">
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
