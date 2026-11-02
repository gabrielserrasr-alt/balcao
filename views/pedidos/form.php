<?php
use Painel\Csrf;
use Painel\Repositorios\PedidoRepositorio;

$valor = $pedido['valor'] ?? '';
if (is_numeric($valor)) {
    $valor = number_format((float) $valor, 2, ',', '.');
}
?>
<div class="barra-acoes"><h1><?= e($titulo) ?></h1></div>
<?php if (!$clientes): ?>
  <div class="aviso erro">Cadastre um cliente antes de criar pedidos. <a href="/clientes/form"><u>Novo cliente</u></a></div>
<?php else: ?>
<form class="card form entra" method="post">
  <?= Csrf::campo() ?>
  <div class="campo"><label for="cliente_id">Cliente</label>
    <select id="cliente_id" name="cliente_id"><option value="">Escolha</option>
      <?php foreach ($clientes as $cid => $nome): ?><option value="<?= (int) $cid ?>" <?= (int) ($pedido['cliente_id'] ?? 0) === (int) $cid ? 'selected' : '' ?>><?= e($nome) ?></option><?php endforeach; ?>
    </select>
    <?php if (isset($erros['cliente_id'])): ?><div class="erro"><?= e($erros['cliente_id']) ?></div><?php endif; ?></div>
  <div class="campo"><label for="descricao">Descrição</label><input id="descricao" name="descricao" value="<?= e($pedido['descricao'] ?? '') ?>" maxlength="200">
    <?php if (isset($erros['descricao'])): ?><div class="erro"><?= e($erros['descricao']) ?></div><?php endif; ?></div>
  <div class="linha">
    <div class="campo"><label for="valor">Valor</label>
      <div class="dinheiro"><span>R$</span><input id="valor" name="valor" inputmode="numeric" autocomplete="off" data-dinheiro value="<?= e((string) $valor) ?>" placeholder="0,00"></div>
      <?php if (isset($erros['valor'])): ?><div class="erro"><?= e($erros['valor']) ?></div><?php endif; ?></div>
    <div class="campo"><label for="status">Status</label>
      <select id="status" name="status">
        <?php foreach (PedidoRepositorio::STATUS as $chave => $nome): ?><option value="<?= $chave ?>" <?= ($pedido['status'] ?? '') === $chave ? 'selected' : '' ?>><?= $nome ?></option><?php endforeach; ?>
      </select></div>
  </div>
  <div style="display:flex;gap:10px"><button class="botao principal">Salvar</button><a class="botao" href="/pedidos">Cancelar</a></div>
</form>
<script src="/assets/dinheiro.js?v=<?= filemtime(__DIR__ . '/../../public/assets/dinheiro.js') ?>" defer></script>
<?php endif; ?>
