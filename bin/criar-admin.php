<?php
declare(strict_types=1);

// Cria (ou troca a senha de) um usuário do painel:
//   docker compose exec app php bin/criar-admin.php "Seu Nome" voce@email.com
// A senha é pedida no terminal, para não ficar salva no histórico.

require __DIR__ . '/../src/bootstrap.php';

use Painel\Banco;

[, $nome, $email] = $argv + [null, null, null];
if (!$nome || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Uso: php bin/criar-admin.php \"Nome\" email@exemplo.com\n");
    exit(1);
}

echo 'Senha (mínimo 10 caracteres): ';
$senha = trim((string) fgets(STDIN));
if (mb_strlen($senha) < 10) {
    fwrite(STDERR, "Senha curta demais.\n");
    exit(1);
}

Banco::conexao()->prepare(
    'INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE nome = VALUES(nome), senha_hash = VALUES(senha_hash)'
)->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);

echo "Usuário $email pronto.\n";
