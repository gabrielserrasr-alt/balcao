<?php use Painel\Csrf; ?>
<div class="barra-acoes"><h1><?= e($titulo) ?></h1></div>
<form class="card form entra" method="post">
  <?= Csrf::campo() ?>
  <div class="campo"><label for="nome">Nome</label><input id="nome" name="nome" value="<?= e($cliente['nome'] ?? '') ?>" maxlength="120" required>
    <?php if (isset($erros['nome'])): ?><div class="erro"><?= e($erros['nome']) ?></div><?php endif; ?></div>
  <div class="linha">
    <div class="campo"><label for="email">E-mail</label><input id="email" name="email" type="email" value="<?= e($cliente['email'] ?? '') ?>">
      <?php if (isset($erros['email'])): ?><div class="erro"><?= e($erros['email']) ?></div><?php endif; ?></div>
    <div class="campo"><label for="telefone">Telefone</label><input id="telefone" name="telefone" value="<?= e($cliente['telefone'] ?? '') ?>" maxlength="30"></div>
  </div>
  <div class="campo"><label for="cidade">Cidade</label><input id="cidade" name="cidade" value="<?= e($cliente['cidade'] ?? '') ?>" maxlength="80"></div>
  <div style="display:flex;gap:10px"><button class="botao principal">Salvar</button><a class="botao" href="/clientes">Cancelar</a></div>
</form>
