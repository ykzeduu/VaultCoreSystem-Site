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
