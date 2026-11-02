<?php use Painel\Csrf; ?>
<div class="barra-acoes">
  <h1>Clientes</h1>
  <form class="busca" method="get"><input name="busca" value="<?= e($busca) ?>" placeholder="Buscar por nome, e-mail ou cidade"><button class="botao icone" title="Buscar"><svg class="i"><use href="#i-magnifying-glass"/></svg></button></form>
  <a class="botao principal" href="/clientes/form"><svg class="i"><use href="#i-plus"/></svg>Novo cliente</a>
</div>
<section class="card tabela entra">
  <?php if (!$clientes): ?>
    <p class="vazio">Nenhum cliente encontrado.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>Nome</th><th>Contato</th><th>Cidade</th><th>Pedidos</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($clientes as $c): ?>
      <tr>
        <td><?= e($c['nome']) ?></td>
        <td class="fraco"><?= e($c['email'] ?: $c['telefone'] ?: '—') ?></td>
        <td class="fraco"><?= e($c['cidade'] ?: '—') ?></td>
        <td class="num"><?= (int) $c['total_pedidos'] ?></td>
        <td class="acoes">
          <a class="botao icone" href="/clientes/form?id=<?= (int) $c['id'] ?>" title="Editar"><svg class="i"><use href="#i-pencil-simple"/></svg></a>
          <form class="inline" method="post" action="/clientes/excluir?id=<?= (int) $c['id'] ?>"><?= Csrf::campo() ?>
            <button class="botao icone perigo" title="Excluir"><svg class="i"><use href="#i-trash"/></svg></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
