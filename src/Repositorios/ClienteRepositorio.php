<?php
declare(strict_types=1);

namespace Painel\Repositorios;

use Painel\Banco;
use PDOException;

final class ClienteRepositorio
{
    /** @return array<int, array<string, mixed>> */
    public function listar(string $busca = ''): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS total_pedidos
                FROM clientes c LEFT JOIN pedidos p ON p.cliente_id = c.id
                WHERE c.nome LIKE :b1 OR c.email LIKE :b2 OR c.cidade LIKE :b3
                GROUP BY c.id ORDER BY c.nome';
        $consulta = Banco::conexao()->prepare($sql);
        $termo = '%' . $busca . '%';
        $consulta->execute(['b1' => $termo, 'b2' => $termo, 'b3' => $termo]);
        return $consulta->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $consulta = Banco::conexao()->prepare('SELECT * FROM clientes WHERE id = ?');
        $consulta->execute([$id]);
        return $consulta->fetch() ?: null;
    }

    /** @return array<int, string> id => nome, para o campo de seleção do pedido */
    public function opcoes(): array
    {
        return Banco::conexao()->query('SELECT id, nome FROM clientes ORDER BY nome')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function salvar(array $dados, ?int $id = null): int
    {
        $campos = [
            'nome' => $dados['nome'],
            'email' => $dados['email'] ?: null,
            'telefone' => $dados['telefone'] ?: null,
            'cidade' => $dados['cidade'] ?: null,
        ];
        if ($id === null) {
            $sql = 'INSERT INTO clientes (nome, email, telefone, cidade) VALUES (:nome, :email, :telefone, :cidade)';
        } else {
            $sql = 'UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, cidade = :cidade WHERE id = :id';
            $campos['id'] = $id;
        }
        Banco::conexao()->prepare($sql)->execute($campos);
        return $id ?? (int) Banco::conexao()->lastInsertId();
    }

    /** Cliente com pedidos não é apagado: o banco recusa e a função devolve false. */
    public function excluir(int $id): bool
    {
        try {
            return Banco::conexao()->prepare('DELETE FROM clientes WHERE id = ?')->execute([$id]);
        } catch (PDOException $erro) {
            if ($erro->errorInfo[1] === 1451) {
                return false;
            }
            throw $erro;
        }
    }

    /** @return array<string, string> campo => mensagem */
    public static function validar(array $dados): array
    {
        $erros = [];
        if (trim($dados['nome'] ?? '') === '') {
            $erros['nome'] = 'Informe o nome.';
        } elseif (mb_strlen($dados['nome']) > 120) {
            $erros['nome'] = 'Use até 120 caracteres.';
        }
        if (($dados['email'] ?? '') !== '' && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'E-mail inválido.';
        }
        return $erros;
    }
}
