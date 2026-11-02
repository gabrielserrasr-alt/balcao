<?php
declare(strict_types=1);

namespace Painel;

use PDO;

final class Banco
{
    private static ?PDO $conexao = null;

    public static function conexao(): PDO
    {
        if (self::$conexao === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_NOME'));
            self::$conexao = new PDO($dsn, getenv('DB_USUARIO'), getenv('DB_SENHA'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Prepared statements de verdade no MySQL, não emulados pelo PHP
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$conexao;
    }
}
