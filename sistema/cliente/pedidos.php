<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('cliente');

require_once __DIR__ . '/../modelo/pedidos.php';

$pedidos = listar_pedidos(['cliente_id' => cliente_id()]);

$titulo  = 'Mis pedidos';
$seccion = 'pedidos';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Mis pedidos</h1>
    <p>Historial y estado de tus pedidos.</p>
  </div>
  <a class="btn btn-primary" href="<?= url('cliente/pedido.php') ?>">Nuevo pedido</a>
</div>

<section class="panel">
  <header><h2>Pedidos (<?= count($pedidos) ?>)</h2></header>

  <?php if (!$pedidos): ?>
    <p class="text-secondary mb-0">Todavia no hiciste ningun pedido.</p>
  <?php endif; ?>

  <div class="row g-3">
    <?php foreach ($pedidos as $pedido): ?>
      <div class="col-md-6">
        <article class="tarjeta">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h3 class="h6 mb-0">#<?= (int) $pedido['id'] ?></h3>
            <span class="etiqueta <?= clase_estado($pedido['estado']) ?>">
              <?= e($pedido['estado']) ?>
            </span>
          </div>
          <p class="tarjeta-pie mb-2">
            Entrega <?= fecha_corta($pedido['fecha_entrega']) ?> · <?= (int) $pedido['viandas'] ?> viandas · <?= e($pedido['zona_codigo']) ?>
          </p>
          <p class="fw-semibold"><?= moneda($pedido['total']) ?></p>
          <a class="btn btn-sm btn-outline-secondary" href="<?= url('cliente/seguimiento.php?id=' . (int) $pedido['id']) ?>">Ver seguimiento</a>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
