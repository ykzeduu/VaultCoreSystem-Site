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
   `assets/style.css` com um design system consistente (cores, botões,
   cards, tabelas, formulários, tags de status, responsivo para celular).
   Isso deixa o site com visual mais moderno e profissional, e qualquer
   ajuste de cor/fonte agora é feito em um lugar só.

5. Criei `schema.sql` com a estrutura das 4 tabelas que o sistema usa
   (`clientes`, `equipamentos`, `chamados`, `financeiro`), reconstruída a
   partir das queries que já existiam no código.

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

## ⚠️ Ainda recomendo fazer
- **Trocar a senha antiga do InfinityFree** (`2kVHu71TF3ly`) em qualquer lugar
  onde ela ainda exista (histórico do Git, por exemplo), já que ficou exposta.
- Adicionar `.gitignore` pra nunca commitar um `.env` se você criar um no futuro.
- Se quiser, posso continuar e revisar cada funcionalidade (chamados, financeiro,
  cadastro de equipamento) uma por uma pra confirmar que tudo está completo —
  é só me dizer por onde quer que eu continue.
