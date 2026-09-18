-- Schema do VaultCore, reconstruído a partir das queries encontradas no código.
-- Rode isso uma vez no console SQL do TiDB Cloud (aba "Chat2Query" ou "SQL Editor").

CREATE TABLE IF NOT EXISTS clientes (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    razao_social        VARCHAR(150) NOT NULL,
    nome_fantasia       VARCHAR(150),
    cnpj                VARCHAR(20),
    endereco            VARCHAR(255),
    contato_principal   VARCHAR(150),
    contato_secundario  VARCHAR(150),
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS equipamentos (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    codigo_equipamento  VARCHAR(50) NOT NULL UNIQUE,
    numero_serie        VARCHAR(100),
    modelo              VARCHAR(150),
    geracao             VARCHAR(50),
    status              VARCHAR(30) DEFAULT 'disponivel', -- disponivel | locado | manutencao
    cliente_id          INT NULL,
    imagem              VARCHAR(255),
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS chamados (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    equipamento_id      INT NOT NULL,
    prioridade          VARCHAR(20) DEFAULT 'normal', -- baixa | normal | alta | urgente
    descricao           TEXT,
    status              VARCHAR(30) DEFAULT 'aberto', -- aberto | em_andamento | resolvido | fechado
    notas_tecnicas      TEXT,
    data_abertura       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS financeiro (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id          INT NOT NULL,
    descricao           VARCHAR(255),
    valor               DECIMAL(10,2) NOT NULL,
    data_vencimento     DATE,
    status              VARCHAR(20) DEFAULT 'pendente', -- pendente | pago | atrasado
    tipo                VARCHAR(20) DEFAULT 'receita',   -- receita | despesa
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- =========================================================================
-- LOJA (catálogo público, contas de cliente e pedidos fictícios)
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
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(150) NOT NULL,
    email        VARCHAR(150) NOT NULL UNIQUE,
    senha_hash   VARCHAR(255) NOT NULL,
    criado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pedidos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT NOT NULL,
    total        DECIMAL(10,2) NOT NULL DEFAULT 0,
    status       VARCHAR(30) DEFAULT 'confirmado', -- pedido fictício: já nasce "confirmado"
    criado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

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

-- Produtos iniciais (os 4 modelos que já existiam fixos no site).
-- INSERT IGNORE evita duplicar caso você rode este script mais de uma vez.
INSERT IGNORE INTO produtos (nome, descricao, especificacoes, preco, estoque, imagem, categoria) VALUES
('Essencial A',    'Ideal para tarefas administrativas do dia a dia.', 'i5 / 8 GB RAM / SSD 240 GB',  1590.00, 8, 'assets/img/pc-essencial-a.svg',    'desktop'),
('Essencial B',    'Um passo além em memória para multitarefa tranquila.', 'i5 / 16 GB RAM / SSD 480 GB', 1890.00, 5, 'assets/img/pc-essencial-b.svg',    'desktop'),
('Performance A',  'Para quem precisa de mais poder de processamento.', 'i7 / 16 GB RAM / SSD 240 GB', 2390.00, 3, 'assets/img/pc-performance-a.svg', 'desktop'),
('Performance B',  'O topo de linha para máxima produtividade.', 'i7 / 16 GB RAM / SSD 480 GB', 2690.00, 0, 'assets/img/pc-performance-b.svg', 'desktop');
