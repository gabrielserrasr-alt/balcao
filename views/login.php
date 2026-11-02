<?php use Painel\Csrf; ?>
<div class="login">
  <form class="card entra" method="post" action="/login">
    <span class="tecla g acesa"><svg><use href="#i-storefront"/></svg></span>
    <h1>Entrar no Balcão</h1>
    <p class="fraco" style="margin-bottom:24px">Pedidos, clientes e faturamento num lugar só.</p>
    <?= Csrf::campo() ?>
    <?php if ($erro): ?><div class="aviso erro"><?= e($erro) ?></div><?php endif; ?>
    <div class="campo"><label for="email">E-mail</label><input id="email" name="email" type="email" value="<?= e($email) ?>" required autofocus></div>
    <div class="campo"><label for="senha">Senha</label><input id="senha" name="senha" type="password" required></div>
    <button class="botao principal" style="width:100%">Entrar</button>
  </form>
</div>
