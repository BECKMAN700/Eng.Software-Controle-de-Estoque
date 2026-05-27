CREATE DATABASE IF NOT EXISTS controle_estoque
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE controle_estoque;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    papel ENUM('admin', 'estoquista') NOT NULL DEFAULT 'estoquista',
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuarios (nome, email, senha, papel, status) VALUES
    ('Joao Pedro Admin', 'admin@controleestoque.local', '$2y$12$T6V1NMzxXIwJDXbFBmVWyOYrn6ULoxK/s609nM4uRuJf61rn3T1v6', 'admin', 'ativo'),
    ('Usuario Estoquista', 'estoquista@controleestoque.local', '$2y$12$WOtNrp4bMQEFP3tzSE5nQ.ZIJ5ThzMFR6KbsSiI.64QPUJzY6dQhi', 'estoquista', 'ativo')
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    senha = VALUES(senha),
    papel = VALUES(papel),
    status = VALUES(status);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    codigo VARCHAR(50) UNIQUE,
    categoria VARCHAR(100),
    unidade VARCHAR(30),
    quantidade INT NOT NULL DEFAULT 0,
    estoque_minimo INT NOT NULL DEFAULT 0,
    estoque_maximo INT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) NOT NULL DEFAULT 'ativo',
    descricao TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    motivo VARCHAR(50) NOT NULL,
    quantidade INT NOT NULL,
    observacao TEXT,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_movimentacoes_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NULL,
    status ENUM('aberto', 'em_conferencia', 'aprovado', 'cancelado') NOT NULL DEFAULT 'aberto',
    criado_por INT NOT NULL,
    aprovado_por INT NULL,
    observacao TEXT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    finalizado_em DATETIME NULL,
    CONSTRAINT fk_inventarios_criado_por
        FOREIGN KEY (criado_por) REFERENCES usuarios(id),
    CONSTRAINT fk_inventarios_aprovado_por
        FOREIGN KEY (aprovado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventario_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade_sistema INT NOT NULL,
    quantidade_contada INT NULL,
    diferenca INT NULL,
    observacao TEXT NULL,
    CONSTRAINT uq_inventario_produto UNIQUE (inventario_id, produto_id),
    CONSTRAINT fk_inventario_itens_inventario
        FOREIGN KEY (inventario_id) REFERENCES inventarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_inventario_itens_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE auditorias_estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventario_id INT NOT NULL,
    produto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    quantidade_anterior INT NOT NULL,
    quantidade_nova INT NOT NULL,
    diferenca INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditorias_inventario
        FOREIGN KEY (inventario_id) REFERENCES inventarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_auditorias_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_auditorias_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
