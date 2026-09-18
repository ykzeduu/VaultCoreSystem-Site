-- Schema do VaultCore — modo LOJA (venda direta, fictícia).
-- Rode isso no console SQL do TiDB Cloud (ou via setup-db.php) sempre que
-- este arquivo mudar; todos os comandos são seguros de repetir.

-- =========================================================================
-- Remove o que sobrou do modelo antigo (locação de equipamentos + chamados
-- de suporte). Você confirmou que pode apagar: não há mais essa função.
-- =========================================================================
DROP TABLE IF EXISTS chamados;
DROP TABLE IF EXISTS equipamentos;
DROP TABLE IF EXISTS financeiro;

-- =========================================================================
-- CLIENTES — agora preenchida automaticamente pelo cadastro que o próprio
-- cliente faz no site (loja-cadastro.php), não mais por cadastro manual.
-- =========================================================================
CREATE TABLE IF NOT EXISTS clientes (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    razao_social        VARCHAR(150) NOT NULL,
    nome_fantasia       VARCHAR(150),
    cnpj                VARCHAR(20),   -- guarda CPF ou CNPJ
    cep                 VARCHAR(10),
    endereco            VARCHAR(255),
    contato_principal   VARCHAR(150),
    contato_secundario  VARCHAR(150),
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE clientes ADD COLUMN IF NOT EXISTS cep VARCHAR(10);

-- =========================================================================
-- LOJA (catálogo público, contas de cliente, carrinho/cupom e pedidos)
-- =========================================================================

CREATE TABLE IF NOT EXISTS produtos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(150) NOT NULL UNIQUE,
    descricao       TEXT,
    especificacoes  VARCHAR(255),      -- ex: "i5 / 8GB RAM / SSD 240GB"
    preco           DECIMAL(10,2) NOT NULL DEFAULT 0,
    estoque         INT NOT NULL DEFAULT 0,
    imagem          VARCHAR(255),
    categoria       VARCHAR(50) DEFAULT 'desktop',
    ativo           TINYINT(1) NOT NULL DEFAULT 1,
    criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nome             VARCHAR(150) NOT NULL,
    email            VARCHAR(150) NOT NULL UNIQUE,
    senha_hash       VARCHAR(255) NOT NULL,
    cliente_id       INT NULL,           -- liga com a tabela clientes
    perfil_completo  TINYINT(1) NOT NULL DEFAULT 0,
    criado_em        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS cliente_id INT NULL;
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS perfil_completo TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS pedidos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT NOT NULL,
    subtotal         DECIMAL(10,2) NOT NULL DEFAULT 0,
    desconto         DECIMAL(10,2) NOT NULL DEFAULT 0,
    total            DECIMAL(10,2) NOT NULL DEFAULT 0,
    cupom_codigo     VARCHAR(50) NULL,
    forma_pagamento  VARCHAR(20) NULL,   -- pix | cartao | boleto
    status           VARCHAR(30) DEFAULT 'confirmado', -- pedido fictício: já nasce "confirmado"
    criado_em        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS desconto DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS cupom_codigo VARCHAR(50) NULL;
ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS forma_pagamento VARCHAR(20) NULL;

CREATE TABLE IF NOT EXISTS pedido_itens (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id        INT NOT NULL,
    produto_id       INT NOT NULL,
    produto_nome     VARCHAR(150) NOT NULL, -- guardamos o nome também, caso o produto seja removido depois
    quantidade       INT NOT NULL DEFAULT 1,
    preco_unitario   DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cupons (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    codigo       VARCHAR(50) NOT NULL UNIQUE,
    tipo         VARCHAR(20) NOT NULL DEFAULT 'percentual', -- percentual | fixo
    valor        DECIMAL(10,2) NOT NULL,                    -- 10 (=10%) ou um valor fixo em R$
    ativo        TINYINT(1) NOT NULL DEFAULT 1,
    criado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produtos iniciais (os 4 modelos que já existiam fixos no site).
-- INSERT IGNORE evita duplicar caso você rode este script mais de uma vez.
INSERT IGNORE INTO produtos (nome, descricao, especificacoes, preco, estoque, imagem, categoria) VALUES
('Essencial A',    'Ideal para tarefas administrativas do dia a dia.', 'i5 / 8 GB RAM / SSD 240 GB',  1590.00, 8, 'assets/img/pc-essencial-a.svg',    'desktop'),
('Essencial B',    'Um passo além em memória para multitarefa tranquila.', 'i5 / 16 GB RAM / SSD 480 GB', 1890.00, 5, 'assets/img/pc-essencial-b.svg',    'desktop'),
('Performance A',  'Para quem precisa de mais poder de processamento.', 'i7 / 16 GB RAM / SSD 240 GB', 2390.00, 3, 'assets/img/pc-performance-a.svg', 'desktop'),
('Performance B',  'O topo de linha para máxima produtividade.', 'i7 / 16 GB RAM / SSD 480 GB', 2690.00, 0, 'assets/img/pc-performance-b.svg', 'desktop');

-- Um cupom de exemplo pra você testar (10% de desconto).
INSERT IGNORE INTO cupons (codigo, tipo, valor, ativo) VALUES ('BEMVINDO10', 'percentual', 10, 1);
