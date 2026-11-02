<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use Painel\Auth;
use Painel\Csrf;
use Painel\Repositorios\ClienteRepositorio;
use Painel\Repositorios\PedidoRepositorio;
use Painel\Repositorios\RelatorioRepositorio;

$metodo = $_SERVER['REQUEST_METHOD'];
$caminho = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: '/';

// A API responde JSON e usa token, não sessão
if (str_starts_with($caminho, '/api/')) {
    require __DIR__ . '/../src/api.php';
    exit;
}

if ($metodo === 'POST') {
    Csrf::validar();
}

$clientes = new ClienteRepositorio();
$pedidos = new PedidoRepositorio();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($caminho) {
    case '/login':
        if ($metodo === 'POST') {
            if (Auth::entrar(trim($_POST['email'] ?? ''), $_POST['senha'] ?? '')) {
                redirecionar('/');
            }
            $erro = Auth::bloqueado() ? 'Muitas tentativas. Espere 5 minutos.' : 'E-mail ou senha incorretos.';
        }
        view('login', ['titulo' => 'Entrar', 'erro' => $erro ?? null, 'email' => $_POST['email'] ?? '']);
        break;

    case '/sair':
        if ($metodo === 'POST') {
            Auth::sair();
        }
        redirecionar('/login');

    case '/':
        Auth::exigir();
        $relatorio = new RelatorioRepositorio();
        view('relatorio', [
            'titulo' => 'Visão geral',
            'faturamento' => $relatorio->faturamentoDoMes(),
            'porStatus' => $relatorio->porStatus(),
            'melhores' => $relatorio->melhoresClientes(),
            'meses' => $relatorio->ultimosMeses(),
            'recentes' => array_slice($pedidos->listar()['itens'], 0, 3),
        ]);
        break;

    case '/clientes':
        Auth::exigir();
        view('clientes/lista', ['titulo' => 'Clientes', 'clientes' => $clientes->listar(trim($_GET['busca'] ?? '')), 'busca' => $_GET['busca'] ?? '']);
        break;

    case '/clientes/form':
        Auth::exigir();
        $cliente = $id ? $clientes->buscar($id) : null;
        if ($id && !$cliente) {
            http_response_code(404);
            exit('Cliente não encontrado.');
        }
        $erros = [];
        if ($metodo === 'POST') {
            $erros = ClienteRepositorio::validar($_POST);
            if (!$erros) {
                $clientes->salvar($_POST, $id);
                avisar('Cliente salvo.');
                redirecionar('/clientes');
            }
            $cliente = $_POST;
        }
        view('clientes/form', ['titulo' => $id ? 'Editar cliente' : 'Novo cliente', 'cliente' => $cliente ?? [], 'erros' => $erros, 'id' => $id]);
        break;

    case '/clientes/excluir':
        Auth::exigir();
        if ($metodo === 'POST' && $id) {
            $clientes->excluir($id)
                ? avisar('Cliente excluído.')
                : avisar('Esse cliente tem pedidos: exclua ou transfira os pedidos antes.', 'erro');
        }
        redirecionar('/clientes');

    case '/pedidos':
        Auth::exigir();
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $resultado = $pedidos->listar(trim($_GET['busca'] ?? ''), $_GET['status'] ?? '', $pagina);
        view('pedidos/lista', [
            'titulo' => 'Pedidos',
            'pedidos' => $resultado['itens'],
            'paginas' => (int) ceil($resultado['total'] / PedidoRepositorio::POR_PAGINA),
            'pagina' => $pagina,
            'busca' => $_GET['busca'] ?? '',
            'status' => $_GET['status'] ?? '',
        ]);
        break;

    case '/pedidos/form':
        Auth::exigir();
        $pedido = $id ? $pedidos->buscar($id) : null;
        if ($id && !$pedido) {
            http_response_code(404);
            exit('Pedido não encontrado.');
        }
        $opcoes = $clientes->opcoes();
        $erros = [];
        if ($metodo === 'POST') {
            $erros = PedidoRepositorio::validar($_POST, $opcoes);
            if (!$erros) {
                $pedidos->salvar($_POST, $id);
                avisar('Pedido salvo.');
                redirecionar('/pedidos');
            }
            $pedido = $_POST;
        }
        view('pedidos/form', ['titulo' => $id ? 'Editar pedido' : 'Novo pedido', 'pedido' => $pedido ?? ['status' => 'pendente'], 'clientes' => $opcoes, 'erros' => $erros, 'id' => $id]);
        break;

    case '/pedidos/excluir':
        Auth::exigir();
        if ($metodo === 'POST' && $id) {
            $pedidos->excluir($id);
            avisar('Pedido excluído.');
        }
        redirecionar('/pedidos');

    default:
        http_response_code(404);
        echo 'Página não encontrada.';
}
