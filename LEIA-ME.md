# VaultCore — o que foi feito e o que falta

## ✅ O que eu já corrigi/mudei nesta versão

1. **Bug do painel admin (chamados/clientes/equipamentos não apareciam):**
   Cada página tinha sua própria conexão MySQL hardcoded, apontando pro banco
   antigo do InfinityFree. Centralizei tudo em um único arquivo `config.php`,
   que todas as páginas agora usam via `require`.

2. **Senha e usuário do banco expostos em texto puro em 9 arquivos** — removido.
   Agora a conexão lê `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
   de variáveis de ambiente (nunca ficam no código).

3. **Senha do admin também tirada do código-fonte** — agora vem de
   `ADMIN_PASSWORD` (variável de ambiente). Se você não configurar, ela cai
   no valor antigo só como fallback — troque isso assim que possível.

4. **Design totalmente refeito.** Cada uma das 19 páginas tinha um bloco
   `<style>` gigante e repetido. Agora existe um único arquivo
   `assets/css/style.css` com um design system consistente (cores, botões,
   cards, tabelas, formulários, tags de status, responsivo para celular).
   Isso deixa o site com visual mais moderno e profissional, e qualquer
   ajuste de cor/fonte agora é feito em um lugar só.

5. Criei `sql/schema.sql` com a estrutura das 4 tabelas que o sistema usa
   (`clientes`, `equipamentos`, `chamados`, `financeiro`), reconstruída a
   partir das queries que já existiam no código.

6. **Bug do botão "Sou Colaborador" que sumia no login** — a tela de login
   dependia de uma regra de CSS que escondia os dois formulários até você
   clicar em "Sou Cliente" ou "Sou Colaborador". Essa regra tinha ficado de
   fora do CSS novo. Corrigido.

7. **Estrutura de pastas organizada.** Antes eram ~30 arquivos soltos na
   raiz. Agora:
   - `includes/config.php` → conexão com o banco
   - `sql/schema.sql` → script de criação das tabelas
   - `assets/css/style.css` → design único do site
   - `assets/img/` → logo e ilustrações
   - os arquivos `.php` de cada página continuam na raiz (login.php,
     admin.php, clientes.php etc.) porque são os endereços que o site
     usa (ex: `seusite.com/clientes.php`) — mudar isso quebraria os links.

8. **Logo nova**, feita do zero em SVG (vetor — nunca fica pixelizada,
   funciona em qualquer tamanho): `assets/img/logo.svg`.

9. **Imagens dos equipamentos que estavam faltando** (`pc1.webp`, `pc2.webp`,
   `pc3.webp`, `pc4.webp`, `setup-completo.jpg` — nenhum desses arquivos
   existia no projeto original, por isso apareciam quebrados). Coloquei
   ilustrações vetoriais próprias no lugar (`assets/img/pc-essencial-a.svg`
   e as demais), sem nenhum problema de direitos autorais. Quando você tiver
   fotos reais dos equipamentos, é só substituir esses arquivos mantendo o
   mesmo nome (ou trocar o `src=""` correspondente no `index.php`).

## 🔧 Passo a passo para colocar no ar

### 1. Criar o cluster no TiDB Serverless (grátis, compatível com MySQL)
1. Crie uma conta em https://tidbcloud.com
2. Crie um cluster **Serverless** (grátis)
3. Vá em **Connect**, escolha "General", clique em **Generate Password** e
   copie: host, porta, usuário e senha (guarde a senha, só aparece uma vez)

*Não precisa criar o banco/database manualmente nem achar o SQL Editor —
o passo 3 abaixo faz isso tudo sozinho, sem precisar instalar nenhum programa.*

### 2. Configurar as variáveis de ambiente no Render
No seu serviço do Render → **Environment** → adicione:

| Variável | Valor |
|---|---|
| `DB_HOST` | host do TiDB (ex: `gateway01.xx-xxxxx.prod.aws.tidbcloud.com`) |
| `DB_PORT` | `4000` |
| `DB_NAME` | nome do banco (ex: `vaultcore`) |
| `DB_USER` | usuário do TiDB |
| `DB_PASSWORD` | senha do TiDB |
| `ADMIN_PASSWORD` | a senha que você quer usar pra entrar no painel admin |
| `SETUP_KEY` | invente uma chave secreta só sua, ex: `minhaChave2026` |

Depois disso é só fazer o deploy.

### 3. Criar as tabelas (sem precisar instalar nada)
Depois que o deploy terminar, acesse no navegador:

```
https://SEU-SITE.onrender.com/setup-db.php?key=minhaChave2026
```
(troque `minhaChave2026` pelo valor que você colocou em `SETUP_KEY`)

Essa página cria o banco `vaultcore` (se ainda não existir) e todas as
tabelas automaticamente. Se aparecer "OK" em cada linha e a mensagem
"Concluído", deu tudo certo.

**Depois que funcionar, apague o arquivo `setup-db.php` do projeto** (ou pelo
menos troque o `SETUP_KEY`), porque essa URL fica exposta pra qualquer um
enquanto o arquivo existir.

### 4. Testar o site
- Acesse `/login.php` → entre como colaborador com a `ADMIN_PASSWORD` nova
- Cadastre um cliente e um equipamento pra ver se o banco está gravando

## 🛒 Loja: cadastro de cliente, estoque ao vivo e compra fictícia

Você pediu pra se inspirar num site de referência (loja esportiva em React) sem trocar toda a tecnologia do site. Fiz isso: mantive PHP + TiDB (que já está funcionando no seu Render), mas implementei o mesmo tipo de funcionalidade:

- **Conta de cliente simples** (`loja-cadastro.php` / `loja-login.php`) — nome, e-mail e senha, com senha criptografada (nunca fica em texto puro no banco).
- **Catálogo dinâmico** — os produtos agora vêm do banco (tabela `produtos`), não são mais fixos no código. Cada card mostra o preço e quantas unidades ainda restam, com "Últimas unidades" quando resta pouco e "Esgotado" quando zera.
- **Compra fictícia sem WhatsApp** — o cliente logado clica em "Comprar", o site cria um pedido e desconta o estoque na hora, sem precisar falar com seu WhatsApp pessoal (que, aliás, estava exposto em texto puro no código antigo — removido).
- **Meus Pedidos** (`meus-pedidos.php`) — o cliente vê o histórico de tudo que "comprou".
- **Painel do admin** — nova aba **Produtos (Loja)** no menu do colaborador, com cadastro, edição de preço/estoque/imagem, e exclusão.

Testei manualmente todo esse fluxo (cadastro → login → compra → desconto de estoque → pedido salvo → admin editando estoque) rodando um banco de teste local antes de te entregar, incluindo o caso de tentar comprar mais do que tem em estoque (bloqueia certinho, sem deixar estoque negativo).

### Você precisa rodar a atualização do banco
As tabelas novas (`produtos`, `usuarios`, `pedidos`, `pedido_itens`) precisam ser criadas no seu TiDB. Como o `setup-db.php` já roda `CREATE TABLE IF NOT EXISTS`, é seguro rodar de novo sem apagar nada do que já existe:

1. Suba os arquivos novos (inclusive o `setup-db.php`, que está de volta no zip)
2. Acesse de novo: `https://seusite.onrender.com/setup-db.php?key=SUA_SETUP_KEY`
3. Isso cria as 4 tabelas novas e já cadastra os 4 modelos de PC como produtos de exemplo
4. **Apague o `setup-db.php` de novo** depois de confirmar que funcionou

## ⚠️ Ainda recomendo fazer

- **Trocar a senha antiga do InfinityFree** (`2kVHu71TF3ly`) em qualquer lugar
  onde ela ainda exista (histórico do Git, por exemplo), já que ficou exposta.
- Adicionar `.gitignore` pra nunca commitar um `.env` se você criar um no futuro.
- A compra é 100% fictícia (não processa pagamento de verdade, é só pra
  demonstração). Se um dia quiser cobrar de verdade, dá pra integrar um
  gateway tipo Mercado Pago ou Stripe no lugar do `comprar.php` — mas isso
  é um projeto à parte.
- Se quiser, posso continuar e revisar cada funcionalidade (chamados, financeiro,
  cadastro de equipamento) uma por uma pra confirmar que tudo está completo —
  é só me dizer por onde quer que eu continue.
