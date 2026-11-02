<?php
declare(strict_types=1);

// API JSON:
//   GET  /api/pedidos?status=pago&pagina=1   lista pedidos
//   POST /api/pedidos                        cria um pedido (corpo JSON)
// Toda chamada envia o cabeçalho: Authorization: Bearer <API_TOKEN>

use Painel\Repositorios\ClienteRepositorio;
use Painel\Repositorios\PedidoRepositorio;

header('Content-Type: application/json; charset=utf-8');

function responder(int $codigo, array $corpo): never
{
    http_response_code($codigo);
    echo json_encode($corpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$esperado = (string) getenv('API_TOKEN');
$cabecalho = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
$enviado = preg_replace('/^Bearer\s+/i', '', $cabecalho);
if ($esperado === '' || !hash_equals($esperado, (string) $enviado)) {
    responder(401, ['erro' => 'Token inválido.']);
}

$pedidos = new PedidoRepositorio();

if ($caminho === '/api/pedidos' && $metodo === 'GET') {
    $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
    $resultado = $pedidos->listar(trim($_GET['busca'] ?? ''), $_GET['status'] ?? '', $pagina);
    responder(200, [
        'pedidos' => array_map(fn (array $p) => [
            'id' => (int) $p['id'],
            'cliente' => $p['cliente'],
            'descricao' => $p['descricao'],
            'valor' => (float) $p['valor'],
            'status' => $p['status'],
            'criado_em' => $p['criado_em'],
            'atualizado_em' => $p['atualizado_em'],
        ], $resultado['itens']),
        'pagina' => $pagina,
        'total' => $resultado['total'],
    ]);
}

if ($caminho === '/api/pedidos' && $metodo === 'POST') {
    $dados = json_decode(file_get_contents('php://input') ?: '', true);
    if (!is_array($dados)) {
        responder(400, ['erro' => 'Envie um JSON válido.']);
    }
    $dados += ['status' => 'pendente'];
    $erros = PedidoRepositorio::validar($dados, (new ClienteRepositorio())->opcoes());
    if ($erros) {
        responder(422, ['erros' => $erros]);
    }
    $id = $pedidos->salvar($dados);
    responder(201, ['id' => $id]);
}

responder(404, ['erro' => 'Rota não encontrada.']);
