<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/pedidos.php';
require_once __DIR__ . '/../modelo/stock.php';
require_once __DIR__ . '/../modelo/zonas.php';

$resumen  = resumen_pedidos();
$alertas  = alertas_stock();
$zonas    = listar_zonas(true);
$ultimos  = array_slice(listar_pedidos(), 0, 8);

$titulo  = 'Dashboard';
$seccion = 'dashboard';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Hola, <?= e(usuario()['nombre']) ?></h1>
    <p>Resumen de hoy · <?= fecha_corta(date('Y-m-d')) ?></p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Pedidos de hoy</p>
      <p class="tarjeta-numero"><?= (int) $resumen['hoy'] ?></p>
      <p class="tarjeta-pie mb-0"><?= count($zonas) ?> zonas activas</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Viandas a producir</p>
      <p class="tarjeta-numero"><?= (int) $resumen['viandas_hoy'] ?></p>
      <p class="tarjeta-pie mb-0">pedidos pendientes y en preparacion</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta">
      <p class="tarjeta-titulo">En reparto</p>
      <p class="tarjeta-numero"><?= (int) $resumen['distribucion'] ?></p>
      <p class="tarjeta-pie mb-0">viandas en distribucion</p>
    </article>
  </div>
  <div class="col-6 col-xl-3">
    <article class="tarjeta <?= $alertas ? 'alerta' : '' ?>">
      <p class="tarjeta-titulo">Alertas de stock</p>
      <p class="tarjeta-numero"><?= count($alertas) ?></p>
      <p class="tarjeta-pie mb-0">por debajo del minimo</p>
    </article>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <section class="panel">
      <header><h2>Ultimos pedidos</h2><a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/pedidos.php') ?>">Ver todos</a></header>
      <div class="tabla-scroll">
        <table class="table table-sm align-middle">
          <thead>
            <tr><th>Pedido</th><th>Cliente</th><th>Zona</th><th>Entrega</th><th>Estado</th><th class="text-end">Total</th></tr>
          </thead>
          <tbody>
            <?php foreach ($ultimos as $pedido): ?>
              <tr>
                <td>#<?= (int) $pedido['id'] ?></td>
                <td><?= e($pedido['cliente']) ?></td>
                <td><?= e($pedido['zona_codigo']) ?></td>
                <td><?= fecha_corta($pedido['fecha_entrega']) ?></td>
                <td><span class="etiqueta <?= clase_estado($pedido['estado']) ?>"><?= e($pedido['estado']) ?></span></td>
                <td class="text-end"><?= moneda($pedido['total']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$ultimos): ?>
              <tr><td colspan="6" class="text-secondary">Todavia no hay pedidos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <div class="col-lg-5">
    <section class="panel">
      <header><h2>Alertas de stock</h2></header>
      <?php if ($alertas): ?>
        <ul class="list-unstyled mb-3">
          <?php foreach ($alertas as $alerta): ?>
            <li class="d-flex justify-content-between align-items-center border-bottom py-2">
              <span><?= e($alerta['nombre']) ?></span>
              <span class="etiqueta aviso"><?= (int) $alerta['disponible'] ?> / min <?= (int) $alerta['stock_minimo'] ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-secondary">Todos los platos estan por encima del stock minimo.</p>
      <?php endif; ?>
      <a class="btn btn-outline-secondary w-100" href="<?= url('admin/stock.php') ?>">Configurar stock minimo</a>
    </section>

    <section class="panel">
      <header><h2>Facturacion acumulada</h2></header>
      <p class="tarjeta-numero mb-0"><?= moneda($resumen['facturado']) ?></p>
      <p class="tarjeta-pie">pedidos no cancelados</p>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
