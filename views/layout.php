<?php
use Painel\Auth;
use Painel\Csrf;

$usuario = Auth::usuario();
$atual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$aba = fn (string $rota): string => ($rota === '/' ? $atual === '/' : str_starts_with($atual, $rota)) ? 'ativa' : '';
?><!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($titulo ?? 'Balcão') ?> · Balcão</title>
<link rel="stylesheet" href="/assets/painel.css?v=<?= filemtime(__DIR__ . '/../public/assets/painel.css') ?>">
<script>document.documentElement.classList.add(location.hash === '#print' ? 'print' : 'anima')</script>
</head>
<body>
<?php require __DIR__ . '/icones.php'; ?>
<?php if ($usuario): ?>
<header class="topo">
  <a class="marca" href="/"><span class="tecla p acesa"><svg><use href="#i-storefront"/></svg></span>Balcão <small>pedidos</small></a>
  <nav class="abas">
    <a class="<?= $aba('/') ?>" href="/"><svg class="i"><use href="#i-chart-line-up"/></svg>Visão geral</a>
    <a class="<?= $aba('/pedidos') ?>" href="/pedidos"><svg class="i"><use href="#i-receipt"/></svg>Pedidos</a>
    <a class="<?= $aba('/clientes') ?>" href="/clientes"><svg class="i"><use href="#i-users"/></svg>Clientes</a>
  </nav>
  <div class="usuario">
    <span><?= e($usuario['nome']) ?></span>
    <form class="inline" method="post" action="/sair"><?= Csrf::campo() ?>
      <button class="botao icone" title="Sair"><svg class="i"><use href="#i-sign-out"/></svg></button>
    </form>
  </div>
</header>
<main>
  <?php if ($aviso): ?><div class="aviso <?= e($aviso['tipo']) ?>"><?= e($aviso['texto']) ?></div><?php endif; ?>
  <?= $conteudo ?>
</main>
<?php else: ?>
<?= $conteudo ?>
<?php endif; ?>
</body>
</html>
