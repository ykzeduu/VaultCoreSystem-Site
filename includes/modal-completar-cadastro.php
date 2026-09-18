<?php if (!empty($_SESSION['loja_usuario_id']) && empty($_SESSION['loja_perfil_completo'])): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <h2>Falta pouco, <?= htmlspecialchars(explode(' ', $_SESSION['loja_usuario_nome'])[0]) ?>!</h2>
        <p class="sub">Pra concluir seu cadastro e poder comprar, complete seus dados abaixo.</p>

        <form method="post" action="completar-cadastro.php">
            <div class="form-group">
                <label>CPF ou CNPJ</label>
                <input type="text" name="documento" placeholder="000.000.000-00" required>
            </div>
            <div class="form-group">
                <label>CEP</label>
                <input type="text" name="cep" placeholder="00000-000" required>
            </div>
            <div class="form-group">
                <label>Endereço completo</label>
                <input type="text" name="endereco" placeholder="Rua, número, bairro, cidade - UF" required>
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" placeholder="(00) 00000-0000" required>
            </div>
            <button type="submit" class="btn-entrar full">Concluir cadastro</button>
        </form>
    </div>
</div>
<?php endif; ?>
