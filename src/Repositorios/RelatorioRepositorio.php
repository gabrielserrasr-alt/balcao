<?php
declare(strict_types=1);

namespace Painel\Repositorios;

use Painel\Banco;

final class RelatorioRepositorio
{
    /**
     * Quanto a loja faturou no mês atual.
     *
     * Regra (definida pelo Gabriel): só conta venda realizada, ou seja, paga ou entregue.
     * Pendente fica separado até virar pago; cancelado é desistência e nunca entra.
     */
    public function faturamentoDoMes(): float
    {
        return (float) Banco::conexao()->query(
            "SELECT COALESCE(SUM(valor), 0)
             FROM pedidos
             WHERE status IN ('pago', 'entregue')
               AND criado_em >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )->fetchColumn();
    }

    /** @return array<string, array{quantidade: int, valor: float}> status => totais, do mês atual */
    public function porStatus(): array
    {
        $linhas = Banco::conexao()->query(
            "SELECT status, COUNT(*) AS quantidade, COALESCE(SUM(valor), 0) AS valor
             FROM pedidos
             WHERE criado_em >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
             GROUP BY status"
        )->fetchAll();

        $resultado = [];
        foreach (PedidoRepositorio::STATUS as $chave => $_) {
            $resultado[$chave] = ['quantidade' => 0, 'valor' => 0.0];
        }
        foreach ($linhas as $linha) {
            $resultado[$linha['status']] = ['quantidade' => (int) $linha['quantidade'], 'valor' => (float) $linha['valor']];
        }
        return $resultado;
    }

    /** @return array<int, array{nome: string, pedidos: int, total: float}> clientes que mais compraram, sem cancelados */
    public function melhoresClientes(int $limite = 5): array
    {
        $consulta = Banco::conexao()->prepare(
            "SELECT c.nome, COUNT(p.id) AS pedidos, SUM(p.valor) AS total
             FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
             WHERE p.status <> 'cancelado'
             GROUP BY c.id, c.nome
             ORDER BY total DESC
             LIMIT :limite"
        );
        $consulta->bindValue('limite', $limite, \PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    /** @return array<int, array{mes: string, total: float}> faturamento dos últimos 6 meses, sem cancelados */
    public function ultimosMeses(): array
    {
        return Banco::conexao()->query(
            "SELECT DATE_FORMAT(criado_em, '%m/%Y') AS mes, SUM(valor) AS total
             FROM pedidos
             WHERE status IN ('pago', 'entregue')
               AND criado_em >= DATE_FORMAT(CURDATE() - INTERVAL 5 MONTH, '%Y-%m-01')
             GROUP BY DATE_FORMAT(criado_em, '%Y-%m'), DATE_FORMAT(criado_em, '%m/%Y')
             ORDER BY DATE_FORMAT(criado_em, '%Y-%m')"
        )->fetchAll();
    }
}
