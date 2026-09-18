<?php
session_start();
require __DIR__ . '/includes/config.php';

$paginaAtiva = 'inicio';

$produtos = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY preco ASC")->fetchAll();

$flashErro = $_SESSION['flash_erro'] ?? null;
unset($_SESSION['flash_erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>VaultCore - Venda de Computadores</title>

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
        <a href="garantia.php">Garantia</a>
        <a href="trabalhe-conosco.php">Trabalhe Conosco</a>
        <?php if (isset($_SESSION['loja_usuario_id'])): ?>
            <a href="carrinho.php">Carrinho<?php if (!empty($_SESSION['carrinho'])): ?> (<?= array_sum($_SESSION['carrinho']) ?>)<?php endif; ?></a>
            <a href="meus-pedidos.php">Meus Pedidos</a>
        <?php else: ?>
            <a href="loja-login.php">Entrar / Cadastrar</a>
        <?php endif; ?>
    </nav>

    <a href="login.php" class="admin-btn">Área Administrativa</a>
</header>

<section class="hero">
    <h2>Computadores prontos para o seu dia a dia</h2>
    <p>Desktops revisados, testados e com garantia — compre diretamente pelo site, com entrega e procedência.</p>
</section>

<?php if ($flashErro): ?>
    <div class="container" style="padding-top:24px; padding-bottom:0;">
        <div class="alerta"><?= htmlspecialchars($flashErro) ?></div>
    </div>
<?php endif; ?>

<section class="modelos">
    <h2>Modelos disponíveis</h2>
    <p class="sub" style="text-align:center; margin-top:-30px; margin-bottom:40px;">
        Compre diretamente pelo site — este é um ambiente de demonstração, nenhuma cobrança real é feita.
    </p>

    <div class="grid-modelos">
        <?php foreach ($produtos as $prod): ?>
            <?php
                $semEstoque = $prod['estoque'] <= 0;
                $poucasUnidades = !$semEstoque && $prod['estoque'] <= 2;
            ?>
            <div class="modelo">
                <div style="position:relative;">
                    <img src="<?= htmlspecialchars($prod['imagem']) ?>" style="<?= $semEstoque ? 'opacity:.45; filter:grayscale(1);' : '' ?>">
                    <?php if ($semEstoque): ?>
                        <span class="status locado" style="position:absolute; top:10px; right:10px;">Esgotado</span>
                    <?php elseif ($poucasUnidades): ?>
                        <span class="status manutencao" style="position:absolute; top:10px; right:10px;">Últimas unidades</span>
                    <?php endif; ?>
                </div>

                <h3><?= htmlspecialchars($prod['nome']) ?></h3>
                <p><?= htmlspecialchars($prod['especificacoes']) ?></p>

                <p style="font-size:20px; font-weight:700; color:var(--azul-900); margin-top:8px;">
                    R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                </p>

                <p class="info-secundaria" style="margin-bottom:10px;">
                    <?= $semEstoque ? 'Sem unidades no momento' : $prod['estoque'] . ' unidade(s) disponível(is)' ?>
                </p>

                <?php if ($semEstoque): ?>
                    <button class="btn" disabled style="opacity:.5; cursor:not-allowed;">Esgotado</button>
                <?php elseif (isset($_SESSION['loja_usuario_id'])): ?>
                    <form method="post" action="adicionar-carrinho.php" style="display:flex; gap:8px; align-items:center;">
                        <input type="hidden" name="produto_id" value="<?= $prod['id'] ?>">
                        <input type="number" name="quantidade" value="1" min="1" max="<?= $prod['estoque'] ?>" style="width:64px;">
                        <button type="submit" class="btn">Adicionar ao carrinho</button>
                    </form>
                <?php else: ?>
                    <a href="loja-login.php?voltar=index.php" class="btn">Entrar para comprar</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!$produtos): ?>
            <p style="text-align:center; color:var(--texto-suave);">Nenhum produto disponível no momento.</p>
        <?php endif; ?>
    </div>
</section>


<section class="manutencao">
    <div class="box">
        <h2>Garantia e procedência</h2>
        <p>
            Todo computador vendido pela VaultCore passa por revisão e testes antes de ser anunciado.
            Você compra sabendo exatamente o que está levando, com garantia de 90 dias contra defeitos
            de fabricação — sem letras miúdas. Veja os detalhes na página de <a href="garantia.php">Garantia</a>.
        </p>
    </div>
</section>

<section class="planos">
    <h2>Categorias</h2>

    <div class="grid-planos">
        <div class="plano bronze">
            <h3>🥉 Categoria Bronze: Estabilidade e Economia</h3>
            <p>"O essencial para o dia a dia." Perfeito para quem busca funcionalidade e o melhor custo-benefício do mercado. Com equipamentos de 3ª e 4ª geração, você garante uma máquina sólida para tarefas rotineiras, mantendo a produtividade em dia com o menor investimento.</p>
        </div>

        <div class="plano prata">
            <h3>🥈 Categoria Prata: Modernidade e Versatilidade</h3>
            <p>"Equilíbrio ideal entre performance e tecnologia." Eleve o nível da sua máquina com processadores de 8ª e 9ª geração. Esta categoria oferece total compatibilidade com o Windows 11, garantindo mais segurança, interface moderna e agilidade para o dia a dia, com um investimento acessível.</p>
        </div>

        <div class="plano ouro">
            <h3>🥇 Categoria Ouro: Alta Performance e Inovação</h3>
            <p>"Potência máxima para quem não pode perder tempo." Projetada para quem exige o topo do desempenho. Equipados com processadores de 12ª geração ou superior, esses computadores entregam velocidade excepcional em processos pesados e softwares exigentes.</p>
        </div>
    </div>
</section>

<section class="kits">
    <div class="conteudo">
        <img src="assets/img/kit-completo.svg">
        <div>
            <h2>Kits completos</h2>
            <p>
                Combos prontos com CPU, monitor, teclado e mouse — ideais
                para quem quer montar um posto de trabalho completo em
                uma única compra, com tudo já compatível entre si.
            </p>
        </div>
    </div>
</section>

<footer>
    © 2026 — VaultCore | Gestão de Infraestrutura. Todos os direitos reservados.
</footer>

<?php require __DIR__ . '/includes/modal-completar-cadastro.php'; ?>

</body>
</html>
