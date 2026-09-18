# VaultCore — Loja de computadores (site de demonstração)

Este site foi convertido de "locação de equipamentos + suporte técnico" para
uma **loja de venda direta**, com carrinho, cupom e checkout fictício
(nenhuma cobrança real é processada).

## 🗂 Estrutura do projeto

```
├── includes/
│   ├── config.php              → conexão com o banco (via variáveis de ambiente)
│   ├── upload.php               → processa upload de imagens (produtos)
│   └── modal-completar-cadastro.php → popup obrigatório de dados do cliente
├── assets/
│   ├── css/style.css            → design único do site
│   ├── img/                     → logo e ilustrações
│   └── uploads/produtos/        → fotos enviadas pelo admin (ver aviso abaixo)
├── sql/schema.sql               → cria/atualiza as tabelas do banco
├── setup-db.php                 → roda o schema.sql direto pelo navegador
│
├── index.php, sobre.php, garantia.php, trabalhe-conosco.php   → páginas públicas
├── loja-cadastro.php, loja-login.php, loja-logout.php         → conta do cliente
├── completar-cadastro.php                                     → salva doc/CEP/endereço
├── carrinho.php, checkout.php, finalizar-pedido.php           → compra
├── meus-pedidos.php, pedido-confirmado.php                    → histórico do cliente
│
├── login.php, processa-login.php                              → login do colaborador
├── admin.php                                                  → dashboard
├── produtos.php, cadastro-produto.php, editar-produto.php     → catálogo (admin)
├── clientes.php, editar-cliente.php                           → base de clientes (admin)
├── cupons.php                                                 → cupons de desconto (admin)
└── financeiro.php                                             → painel de vendas (admin)
```

## ✅ O que o site faz hoje

1. **Catálogo dinâmico com estoque real** — os produtos vêm do banco
   (tabela `produtos`), mostrando "Últimas unidades" ou "Esgotado"
   automaticamente.

2. **Conta do cliente ligada à base de clientes.** Quando alguém cria conta
   em `loja-cadastro.php`, o sistema já cria também um registro na tabela
   `clientes` (a mesma que aparece pro admin em `clientes.php`) — não existe
   mais cadastro manual de cliente pelo admin.

3. **Popup obrigatório de cadastro completo.** Depois do cadastro básico
   (nome/e-mail/senha), o cliente volta pra home e vê um popup pedindo
   CPF/CNPJ, CEP, endereço e telefone. Enquanto não preencher, o popup
   volta a aparecer toda vez que ele visitar a home.

4. **Carrinho, cupom e checkout fictício.**
   - `carrinho.php`: adicionar/remover itens, aplicar cupom de desconto
   - `checkout.php`: escolha entre Pix (QR code fictício), Cartão (com aviso
     pra não usar dado real) ou Boleto (código de barras fictício)
   - `finalizar-pedido.php`: cria o pedido, aplica o desconto do cupom e
     desconta o estoque numa transação (trava a linha do produto pra evitar
     duas compras simultâneas venderem a última unidade duas vezes)

5. **Painel do admin atualizado:**
   - `produtos.php` — cadastrar/editar produtos, com upload de imagem real
   - `clientes.php` — lista quem se cadastrou, quantos pedidos fez e quanto gastou
   - `cupons.php` — criar cupons percentuais ou de valor fixo, ativar/desativar
   - `financeiro.php` — gráfico de faturamento por dia, total por forma de
     pagamento (Pix/Cartão/Boleto) e lista de pedidos do período

6. **Removido por completo** (a pedido): locação de equipamentos, chamados
   de suporte, e o antigo financeiro por contrato/fatura. As tabelas
   `equipamentos`, `chamados` e `financeiro` (antiga) são apagadas pelo
   `schema.sql` — isso é intencional.

## 🔧 Colocando as mudanças no ar

### 1. Suba os arquivos
Suba todo o conteúdo deste zip no seu repositório (substitui tudo).

### 2. Rode a atualização do banco
Como o `schema.sql` agora também **apaga tabelas antigas** (equipamentos,
chamados, financeiro antigo), reative o instalador uma vez:

1. Confirme que `setup-db.php` está no projeto (ele está neste zip)
2. Configure a variável `SETUP_KEY` no Render, se ainda não tiver uma
3. Depois do deploy, acesse: `https://seusite.onrender.com/setup-db.php?key=SUA_CHAVE`
4. Confirme que apareceu "Concluído" e que as tabelas antigas sumiram
5. **Apague o `setup-db.php` de novo** depois de confirmar

### 3. Variáveis de ambiente (sem mudanças desde a última vez)

| Variável | Valor |
|---|---|
| `DB_HOST` / `DB_PORT` / `DB_NAME` / `DB_USER` / `DB_PASSWORD` | dados do seu TiDB |
| `ADMIN_PASSWORD` | senha do login de colaborador |
| `SETUP_KEY` | senha só sua pra rodar o `setup-db.php` |

### 4. Teste o fluxo completo
1. Crie uma conta em `/loja-cadastro.php`
2. Complete o cadastro no popup que aparece
3. Adicione produtos ao carrinho, aplique o cupom de teste `BEMVINDO10`
4. Finalize a compra escolhendo Pix, Cartão ou Boleto
5. Confira em `/meus-pedidos.php` e, como admin, em `/financeiro.php`

## ⚠️ Avisos importantes

- **Upload de imagem e disco temporário do Render:** no plano gratuito, o
  disco do Render é apagado a cada novo deploy. Fotos de produto enviadas
  por upload podem sumir quando você atualizar o site de novo. Se isso for
  um problema, a solução correta é usar um storage externo (Cloudflare R2,
  AWS S3) — posso implementar depois, se quiser.
- **Tudo aqui é fictício:** nenhum pagamento é processado de verdade (Pix,
  Cartão e Boleto são apenas visuais), e isso está deixado claro pro
  visitante em várias telas.
- **Senha antiga do InfinityFree** (`2kVHu71TF3ly`), se ainda existir em
  algum histórico de commit do seu repositório, continua valendo a
  recomendação de trocá-la — mesmo não sendo mais usada pelo código.
