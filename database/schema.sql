CREATE DATABASE IF NOT EXISTS controle_estoque
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE controle_estoque;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    codigo VARCHAR(50) UNIQUE,
    categoria VARCHAR(100),
    unidade VARCHAR(30),
    quantidade INT NOT NULL DEFAULT 0,
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