SET NAMES utf8mb4;

CREATE TABLE usuarios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    senha_hash  VARCHAR(255) NOT NULL,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clientes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(120) NOT NULL,
    email      VARCHAR(150) NULL,
    telefone   VARCHAR(30)  NULL,
    cidade     VARCHAR(80)  NULL,
    criado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_clientes_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedidos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id  INT UNSIGNED NOT NULL,
    descricao   VARCHAR(200) NOT NULL,
    valor       DECIMAL(10,2) NOT NULL,
    status      ENUM('pendente','pago','entregue','cancelado') NOT NULL DEFAULT 'pendente',
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_pedidos_status_data (status, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
