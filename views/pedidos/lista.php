<?php
use Painel\Csrf;
use Painel\Repositorios\PedidoRepositorio;

$link = fn (int $p) => '/pedidos?' . http_build_query(['busca' => $busca, 'status' => $status, 'pagina' => $p]);
?>
<div class="barra-acoes">
  <h1>Pedidos</h1>
  <form class="busca" method="get">
    <input name="busca" value="<?= e($busca) ?>" placeholder="Buscar por descrição ou cliente">
    <select name="status"><option value="">Todos</option>
      <?php foreach (PedidoRepositorio::STATUS as $chave => $nome): ?><option value="<?= $chave ?>" <?= $status === $chave ? 'selected' : '' ?>><?= $nome ?></option><?php endforeach; ?>
    </select>
    <button class="botao icone" title="Filtrar"><svg class="i"><use href="#i-magnifying-glass"/></svg></button>
  </form>
  <a class="botao principal" href="/pedidos/form"><svg class="i"><use href="#i-plus"/></svg>Novo pedido</a>
</div>
<section class="card tabela entra">
  <?php if (!$pedidos): ?>
    <p class="vazio">Nenhum pedido encontrado.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Cliente</th><th>Descrição</th><th>Valor</th><th>Status</th><th>Criado em</th><th>Atualizado em</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($pedidos as $p): ?>
      <tr>
        <td class="fraco num"><?= (int) $p['id'] ?></td>
        <td><?= e($p['cliente']) ?></td>
        <td><?= e($p['descricao']) ?></td>
        <td class="num"><?= dinheiro($p['valor']) ?></td>
        <td><span class="selo <?= e($p['status']) ?>"><?= e(PedidoRepositorio::STATUS[$p['status']]) ?></span></td>
        <td class="fraco num"><?= e(date('d/m/Y H:i', strtotime($p['criado_em']))) ?></td>
        <td class="fraco num"><?= e(date('d/m/Y H:i', strtotime($p['atualizado_em']))) ?></td>
        <td class="acoes">
          <a class="botao icone" href="/pedidos/form?id=<?= (int) $p['id'] ?>" title="Editar"><svg class="i"><use href="#i-pencil-simple"/></svg></a>
          <form class="inline" method="post" action="/pedidos/excluir?id=<?= (int) $p['id'] ?>"><?= Csrf::campo() ?>
            <button class="botao icone perigo" title="Excluir"><svg class="i"><use href="#i-trash"/></svg></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
<?php if ($paginas > 1): ?>
<nav class="paginas"><?php for ($i = 1; $i <= $paginas; $i++): ?><a class="<?= $i === $pagina ? 'ativa' : '' ?>" href="<?= e($link($i)) ?>"><?= $i ?></a><?php endfor; ?></nav>
<?php endif; ?>
