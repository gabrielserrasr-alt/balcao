<?php
declare(strict_types=1);

namespace Painel\Repositorios;

use Painel\Banco;

final class PedidoRepositorio
{
    public const STATUS = ['pendente' => 'Pendente', 'pago' => 'Pago', 'entregue' => 'Entregue', 'cancelado' => 'Cancelado'];
    public const POR_PAGINA = 10;

    /** @return array{itens: array<int, array<string, mixed>>, total: int} */
    public function listar(string $busca = '', string $status = '', int $pagina = 1): array
    {
        $filtros = ['(p.descricao LIKE :b1 OR c.nome LIKE :b2)'];
        $parametros = ['b1' => '%' . $busca . '%', 'b2' => '%' . $busca . '%'];
        if (isset(self::STATUS[$status])) {
            $filtros[] = 'p.status = :status';
            $parametros['status'] = $status;
        }
        $onde = 'WHERE ' . implode(' AND ', $filtros);
        $banco = Banco::conexao();

        $total = $banco->prepare("SELECT COUNT(*) FROM pedidos p JOIN clientes c ON c.id = p.cliente_id $onde");
        $total->execute($parametros);

        $consulta = $banco->prepare(
            "SELECT p.*, c.nome AS cliente FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
             $onde ORDER BY p.criado_em DESC LIMIT :limite OFFSET :inicio"
        );
        foreach ($parametros as $nome => $valor) {
            $consulta->bindValue($nome, $valor);
        }
        $consulta->bindValue('limite', self::POR_PAGINA, \PDO::PARAM_INT);
        $consulta->bindValue('inicio', (max(1, $pagina) - 1) * self::POR_PAGINA, \PDO::PARAM_INT);
        $consulta->execute();

        return ['itens' => $consulta->fetchAll(), 'total' => (int) $total->fetchColumn()];
    }

    public function buscar(int $id): ?array
    {
        $consulta = Banco::conexao()->prepare('SELECT * FROM pedidos WHERE id = ?');
        $consulta->execute([$id]);
        return $consulta->fetch() ?: null;
    }

    public function salvar(array $dados, ?int $id = null): int
    {
        $campos = [
            'cliente_id' => (int) $dados['cliente_id'],
            'descricao' => trim($dados['descricao']),
            'valor' => self::numero($dados['valor']),
            'status' => $dados['status'],
        ];
        if ($id === null) {
            $sql = 'INSERT INTO pedidos (cliente_id, descricao, valor, status) VALUES (:cliente_id, :descricao, :valor, :status)';
        } else {
            $sql = 'UPDATE pedidos SET cliente_id = :cliente_id, descricao = :descricao, valor = :valor, status = :status WHERE id = :id';
            $campos['id'] = $id;
        }
        Banco::conexao()->prepare($sql)->execute($campos);
        return $id ?? (int) Banco::conexao()->lastInsertId();
    }

    public function excluir(int $id): void
    {
        Banco::conexao()->prepare('DELETE FROM pedidos WHERE id = ?')->execute([$id]);
    }

    /** Aceita "1.234,56" (formato brasileiro) e "1234.56". */
    public static function numero(string|float|int $valor): float
    {
        $texto = trim((string) $valor);
        if (str_contains($texto, ',')) {
            $texto = str_replace(['.', ','], ['', '.'], $texto);
        }
        return is_numeric($texto) ? round((float) $texto, 2) : -1.0;
    }

    /** @return array<string, string> campo => mensagem */
    public static function validar(array $dados, array $clientes): array
    {
        $erros = [];
        if (!isset($clientes[(int) ($dados['cliente_id'] ?? 0)])) {
            $erros['cliente_id'] = 'Escolha um cliente.';
        }
        if (trim($dados['descricao'] ?? '') === '') {
            $erros['descricao'] = 'Descreva o pedido.';
        }
        $valor = self::numero($dados['valor'] ?? '');
        if ($valor <= 0 || $valor > 99_999_999) {
            $erros['valor'] = 'Informe um valor maior que zero.';
        }
        if (!isset(self::STATUS[$dados['status'] ?? ''])) {
            $erros['status'] = 'Status inválido.';
        }
        return $erros;
    }
}
