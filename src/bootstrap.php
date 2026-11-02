<?php
declare(strict_types=1);

// Carrega as classes de src/ pelo nome: Painel\Repositorios\PedidoRepositorio -> src/Repositorios/PedidoRepositorio.php
spl_autoload_register(function (string $classe): void {
    $prefixo = 'Painel\\';
    if (str_starts_with($classe, $prefixo)) {
        $arquivo = __DIR__ . '/' . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
        if (is_file($arquivo)) {
            require $arquivo;
        }
    }
});

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

/** Escapa texto para HTML. Tudo que vem do usuário ou do banco passa por aqui. */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function dinheiro(float|string $valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function redirecionar(string $caminho): never
{
    header('Location: ' . $caminho);
    exit;
}

/** Mensagem que aparece uma vez, na próxima página. */
function avisar(string $mensagem, string $tipo = 'ok'): void
{
    $_SESSION['aviso'] = ['texto' => $mensagem, 'tipo' => $tipo];
}

function pegarAviso(): ?array
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}

function view(string $nome, array $dados = []): void
{
    extract($dados);
    $aviso = pegarAviso();
    ob_start();
    require __DIR__ . '/../views/' . $nome . '.php';
    $conteudo = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}
