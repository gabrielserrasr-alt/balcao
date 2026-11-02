<?php
declare(strict_types=1);

namespace Painel;

/** Todo formulário leva um token da sessão: outro site não consegue enviar formulários em nome do usuário. */
final class Csrf
{
    public static function token(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function validar(): void
    {
        $enviado = $_POST['_csrf'] ?? '';
        if (!is_string($enviado) || !hash_equals(self::token(), $enviado)) {
            http_response_code(403);
            exit('Formulário expirado. Volte e tente de novo.');
        }
    }
}
