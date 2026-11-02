SET NAMES utf8mb4;

-- Dados fictícios para demonstração. Dois pedidos ficam no mês atual, para a visão geral nunca abrir vazia.
-- A última alteração é a própria criação: pedido de exemplo nenhum foi mexido depois.
INSERT INTO clientes (nome, email, telefone, cidade) VALUES
    ('Padaria Pão Quente', 'contato@paoquente.exemplo', '(11) 90000-0001', 'São Paulo'),
    ('Oficina do Zé', 'ze@oficina.exemplo', '(21) 90000-0002', 'Rio de Janeiro'),
    ('Studio Bela', 'agenda@studiobela.exemplo', '(31) 90000-0003', 'Belo Horizonte'),
    ('Mercadinho Central', NULL, '(51) 90000-0004', 'Porto Alegre');

INSERT INTO pedidos (cliente_id, descricao, valor, status, criado_em, atualizado_em) VALUES
    (1, 'Site institucional', 1800.00, 'pago',      NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 20 DAY),
    (1, 'Manutenção mensal', 250.00, 'pendente',    NOW() - INTERVAL 4 DAY,  NOW() - INTERVAL 4 DAY),
    (2, 'Sistema de ordens de serviço', 4200.00, 'entregue', NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 5 HOUR),
    (3, 'Agenda online', 2600.00, 'pago',           NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR),
    (3, 'Integração com WhatsApp', 900.00, 'cancelado', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY),
    (4, 'Controle de estoque', 3100.00, 'pendente', NOW() - INTERVAL 1 DAY,  NOW() - INTERVAL 1 DAY),
    (2, 'Relatórios em PDF', 700.00, 'pago',        NOW() - INTERVAL 40 DAY, NOW() - INTERVAL 40 DAY),
    (4, 'Cardápio digital', 6500.00, 'pendente',    NOW() - INTERVAL 45 DAY, NOW() - INTERVAL 45 DAY);
