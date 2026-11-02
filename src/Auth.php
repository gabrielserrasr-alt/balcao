<?php
declare(strict_types=1);

namespace Painel;

final class Auth
{
    private const MAX_TENTATIVAS = 5;
    private const BLOQUEIO_SEGUNDOS = 300;

    public static function entrar(string $email, string $senha): bool
    {
        if (self::bloqueado()) {
            return false;
        }

        $consulta = Banco::conexao()->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);
        $usuario = $consulta->fetch();

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['tentativas'] = ($_SESSION['tentativas'] ?? 0) + 1;
            $_SESSION['ultima_tentativa'] = time();
            return false;
        }

        // Novo ID de sessão no login: impede que alguém reaproveite uma sessão antiga
        session_regenerate_id(true);
        unset($_SESSION['tentativas'], $_SESSION['ultima_tentativa']);
        $_SESSION['usuario'] = ['id' => (int) $usuario['id'], 'nome' => $usuario['nome']];
        return true;
    }

    public static function bloqueado(): bool
    {
        $tentativas = $_SESSION['tentativas'] ?? 0;
        $ultima = $_SESSION['ultima_tentativa'] ?? 0;
        if ($tentativas >= self::MAX_TENTATIVAS && time() - $ultima < self::BLOQUEIO_SEGUNDOS) {
            return true;
        }
        if (time() - $ultima >= self::BLOQUEIO_SEGUNDOS) {
            unset($_SESSION['tentativas']);
        }
        return false;
    }

    public static function sair(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function exigir(): void
    {
        if (!self::usuario()) {
            redirecionar('/login');
        }
    }
}
