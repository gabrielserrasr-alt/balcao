<?php
use Painel\Repositorios\PedidoRepositorio;

$meses_nomes = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
$mes_atual = $meses_nomes[(int) date('n') - 1];
$total_mes = array_sum(array_column($porStatus, 'quantidade'));
$maior_mes = max([1, ...array_map(fn ($m) => (float) $m['total'], $meses)]);
$icones = ['pendente' => 'hourglass-medium', 'pago' => 'currency-circle-dollar', 'entregue' => 'truck', 'cancelado' => 'x-circle'];
?>
<div class="bento">
  <section class="card heroi c8 entra">
    <div>
      <div class="pilulas">
        <span class="pilula"><svg><use href="#i-check"/></svg>Faturamento de <?= e($mes_atual) ?></span>
        <span class="pilula"><svg><use href="#i-receipt"/></svg><?= $total_mes ?> pedidos no mês</span>
      </div>
      <h1 class="titulo metal" style="margin-top:18px">Quanto entrou<br>este mês</h1>
      <p class="fraco" style="margin-top:8px">Vendas realizadas: pedidos pagos e entregues.</p>
    </div>
    <div>
      <div class="janela num"><span class="tecla p acesa"><svg><use href="#i-chart-line-up"/></svg></span><b class="metal"><?= dinheiro($faturamento) ?></b></div>
      <div class="pilulas" style="margin-top:18px">
        <span class="pilula num"><svg><use href="#i-hourglass-medium"/></svg>Pendentes: <?= dinheiro($porStatus['pendente']['valor']) ?></span>
        <span class="pilula num"><svg><use href="#i-x-circle"/></svg>Canceladas: <?= dinheiro($porStatus['cancelado']['valor']) ?></span>
      </div>
    </div>
    <?php if ($recentes): ?>
    <div class="recentes" aria-label="Pedidos mais recentes">
      <?php foreach ($recentes as $i => $p): ?>
        <a class="recente" href="/pedidos/form?id=<?= (int) $p['id'] ?>" style="--i:<?= $i ?>">
          <span class="tecla p"><svg><use href="#i-receipt"/></svg></span>
          <span><b><?= e($p['cliente']) ?></b><small><?= e(date('d/m H:i', strtotime($p['criado_em']))) ?> · <?= e($p['descricao']) ?></small></span>
          <span class="valor num"><?= dinheiro($p['valor']) ?><span class="selo <?= e($p['status']) ?>"><?= e(PedidoRepositorio::STATUS[$p['status']]) ?></span></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

  <section class="card c4 entra" style="--d:1">
    <div class="cab"><span class="tecla"><svg><use href="#i-receipt"/></svg></span><div><h2>Pedidos por status</h2><p>Mês atual</p></div></div>
    <div class="status-grade">
      <?php foreach (PedidoRepositorio::STATUS as $chave => $nome): ?>
        <div class="status-item">
          <span class="tecla p"><svg><use href="#i-<?= $icones[$chave] ?>"/></svg></span>
          <b class="num"><?= $porStatus[$chave]['quantidade'] ?></b>
          <small><?= e($nome) ?> · <?= dinheiro($porStatus[$chave]['valor']) ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card c7 entra" style="--d:2">
    <div class="cab"><span class="tecla"><svg><use href="#i-chart-line-up"/></svg></span><div><h2>Últimos meses</h2><p>Pagos e entregues</p></div></div>
    <?php if ($meses): ?>
      <div class="grafico">
        <?php foreach ($meses as $m): ?>
          <div class="col" title="<?= dinheiro($m['total']) ?>">
            <small class="num"><?= dinheiro($m['total']) ?></small>
            <div class="barra-v" style="height:<?= round((float) $m['total'] / $maior_mes * 100) ?>%"></div>
            <small><?= e($m['mes']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?><p class="vazio">Nenhum pedido pago nos últimos meses.</p><?php endif; ?>
  </section>

  <section class="card c5 entra" style="--d:3">
    <div class="cab"><span class="tecla"><svg><use href="#i-trophy"/></svg></span><div><h2>Melhores clientes</h2><p>Sem pedidos cancelados</p></div></div>
    <?php if ($melhores): ?>
      <ul class="ranking">
        <?php foreach ($melhores as $i => $c): ?>
          <li><span class="pos"><?= $i + 1 ?></span><div><?= e($c['nome']) ?><br><small class="fraco"><?= (int) $c['pedidos'] ?> <?= (int) $c['pedidos'] === 1 ? 'pedido' : 'pedidos' ?></small></div><span class="valor num"><?= dinheiro($c['total']) ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?><p class="vazio">Ainda não há pedidos.</p><?php endif; ?>
  </section>
</div>
