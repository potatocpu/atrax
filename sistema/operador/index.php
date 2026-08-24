<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/pedidos.php';
require_once __DIR__ . '/../modelo/stock.php';

$resumen    = resumen_pedidos();
$produccion = produccion_del_dia();
$despacho   = despacho_por_zona();
$alertas    = alertas_stock();
$pedidas    = array_sum(array_column($produccion, 'pedidas'));
$producidas = array_sum(array_column($produccion, 'producidas'));

$titulo  = 'Panel del dia';
$seccion = 'panel';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Cocina · panel del dia</h1>
    <p><?= fecha_corta(date('Y-m-d')) ?> · operador <?= e(usuario()['nombre']) ?></p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">A producir hoy</p>
      <p class="tarjeta-numero"><?= (int) $pedidas ?></p>
      <p class="tarjeta-pie mb-0">viandas pedidas para hoy</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Producidas hoy</p>
      <p class="tarjeta-numero"><?= (int) $producidas ?></p>
      <p class="tarjeta-pie mb-0">lotes registrados hoy</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Listas p/ despacho</p>
      <p class="tarjeta-numero"><?= (int) array_sum(array_column($despacho, 'viandas')) ?></p>
      <p class="tarjeta-pie mb-0">en <?= count($despacho) ?> zonas</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta <?= $alertas ? 'alerta' : '' ?>">
      <p class="tarjeta-titulo">Alertas</p>
      <p class="tarjeta-numero"><?= count($alertas) ?></p>
      <p class="tarjeta-pie mb-0">platos bajo el minimo</p>
    </article>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <section class="panel">
      <header>
        <h2>Produccion por plato</h2>
        <a class="btn btn-sm btn-primary" href="<?= url('operador/produccion.php') ?>">Registrar produccion</a>
      </header>
      <div class="tabla-scroll">
        <table class="table align-middle">
          <thead><tr><th>Plato</th><th>Pedidas</th><th>Producidas hoy</th><th>Avance</th></tr></thead>
          <tbody>
            <?php foreach ($produccion as $fila): ?>
              <?php
              $meta    = max(1, (int) $fila['pedidas']);
              $avance  = min(100, (int) round(100 * $fila['producidas'] / $meta));
              $cubierto = (int) $fila['producidas'] >= (int) $fila['pedidas'];
              ?>
              <tr>
                <td class="fw-semibold"><?= e($fila['nombre']) ?></td>
                <td><?= (int) $fila['pedidas'] ?></td>
                <td><?= (int) $fila['producidas'] ?></td>
                <td style="min-width:140px">
                  <?php if ($cubierto && (int) $fila['pedidas'] > 0): ?>
                    <span class="etiqueta ok">listo</span>
                  <?php else: ?>
                    <div class="barra"><span style="width: <?= $avance ?>%"></span></div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <div class="col-lg-5">
    <section class="panel">
      <header><h2>Despacho por zona</h2></header>
      <?php if ($despacho): ?>
        <ul class="list-unstyled mb-3">
          <?php foreach ($despacho as $zona): ?>
            <li class="d-flex justify-content-between border-bottom py-2">
              <span><?= e($zona['codigo'] . ' · ' . $zona['nombre']) ?></span>
              <span class="text-secondary"><?= (int) $zona['viandas'] ?> viandas · <?= (int) $zona['pedidos'] ?> pedidos</span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-secondary">No hay pedidos listos para despachar hoy.</p>
      <?php endif; ?>
      <a class="btn btn-outline-secondary w-100" href="<?= url('operador/distribucion.php') ?>">Ir a distribucion</a>
    </section>

    <section class="panel">
      <header><h2>Alertas de stock</h2></header>
      <?php if ($alertas): ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($alertas as $alerta): ?>
            <li class="d-flex justify-content-between border-bottom py-2">
              <span><?= e($alerta['nombre']) ?></span>
              <span class="etiqueta aviso"><?= (int) $alerta['disponible'] ?> / min <?= (int) $alerta['stock_minimo'] ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-secondary mb-0">Sin alertas de stock.</p>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
