CREATE DATABASE montink;

USE montink;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL
);

CREATE TABLE variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variacao_id INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(100) NOT NULL UNIQUE,
    tipo ENUM('valor', 'percentual') NOT NULL DEFAULT 'valor',
    valor DECIMAL(10, 2) NULL DEFAULT NULL,
    percentual DECIMAL(5, 2) NULL DEFAULT NULL,
    validade DATETIME NOT NULL,
    minimo_subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cep VARCHAR(10),
    endereco_completo TEXT,
    subtotal DECIMAL(10, 2),
    frete DECIMAL(10, 2),
    desconto DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2),
    cupom_id INT NULL,
    status ENUM('pendente', 'pago', 'cancelado', 'enviado') DEFAULT 'pendente',
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
);

CREATE TABLE pedido_produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    variacao_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE RESTRICT
);