<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/pedidos.php';
require_once __DIR__ . '/../modelo/stock.php';

$porZona   = pedidos_por_zona();
$porEstado = pedidos_por_estado();
$platos    = platos_mas_pedidos();
$porMes    = pedidos_por_mes();
$resumen   = resumen_pedidos();
$maxZona   = max(1, (int) ($porZona[0]['pedidos'] ?? 1));
$maxPlato  = max(1, (int) ($platos[0]['viandas'] ?? 1));

$titulo  = 'Estadisticas';
$seccion = 'estadisticas';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Estadisticas</h1>
    <p>Reportes de ventas, demanda por plato y distribucion por zona.</p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Plato mas pedido</p>
      <p class="h4 mb-1"><?= e($platos[0]['nombre'] ?? '-') ?></p>
      <p class="tarjeta-pie mb-0"><?= (int) ($platos[0]['viandas'] ?? 0) ?> viandas vendidas</p>
    </article>
  </div>
  <div class="col-md-4">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Facturacion acumulada</p>
      <p class="h4 mb-1"><?= moneda($resumen['facturado']) ?></p>
      <p class="tarjeta-pie mb-0">pedidos no cancelados</p>
    </article>
  </div>
  <div class="col-md-4">
    <article class="tarjeta">
      <p class="tarjeta-titulo">Pedidos pendientes</p>
      <p class="h4 mb-1"><?= (int) $resumen['pendientes'] ?></p>
      <p class="tarjeta-pie mb-0">esperando produccion</p>
    </article>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Pedidos por zona</h2></header>
      <?php foreach ($porZona as $zona): ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between small">
            <span><?= e($zona['codigo'] . ' · ' . $zona['nombre']) ?></span>
            <span><?= (int) $zona['pedidos'] ?> pedidos · <?= moneda($zona['facturado']) ?></span>
          </div>
          <div class="barra mt-1">
            <span style="width: <?= (int) round(100 * $zona['pedidos'] / $maxZona) ?>%"></span>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Demanda por plato</h2></header>
      <?php foreach ($platos as $plato): ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between small">
            <span><?= e($plato['nombre']) ?></span>
            <span><?= (int) $plato['viandas'] ?> viandas</span>
          </div>
          <div class="barra mt-1">
            <span style="width: <?= (int) round(100 * $plato['viandas'] / $maxPlato) ?>%"></span>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Pedidos por estado</h2></header>
      <table class="table table-sm mb-0">
        <thead><tr><th>Estado</th><th class="text-end">Cantidad</th></tr></thead>
        <tbody>
          <?php foreach ($porEstado as $fila): ?>
            <tr><td><?= e($fila['estado']) ?></td><td class="text-end"><?= (int) $fila['cantidad'] ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Evolucion mensual</h2></header>
      <table class="table table-sm mb-0">
        <thead><tr><th>Mes</th><th class="text-end">Pedidos</th><th class="text-end">Facturado</th></tr></thead>
        <tbody>
          <?php foreach ($porMes as $fila): ?>
            <tr>
              <td><?= e($fila['mes']) ?></td>
              <td class="text-end"><?= (int) $fila['pedidos'] ?></td>
              <td class="text-end"><?= moneda($fila['facturado']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$porMes): ?>
            <tr><td colspan="3" class="text-secondary">Sin datos para el periodo.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
