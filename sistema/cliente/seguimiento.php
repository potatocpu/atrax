<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('cliente');

require_once __DIR__ . '/../modelo/pedidos.php';

$id     = entero($_GET['id'] ?? '');
$pedido = $id === null ? null : pedido_por_id($id);

if ($pedido === null || (int) $pedido['cliente_id'] !== (int) cliente_id()) {
    flash_guardar('errores', ['El pedido solicitado no existe o no te pertenece.']);
    redirigir('cliente/pedidos.php');
}

$detalle    = detalle_pedido($id);
$historial  = historial_pedido($id);
$secuencia  = ['Pendiente', 'Preparando', 'Listo', 'En distribucion', 'Entregado'];
$alcanzados = array_column($historial, 'estado_nuevo');

$titulo  = 'Seguimiento del pedido';
$seccion = 'pedidos';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Pedido #<?= (int) $pedido['id'] ?></h1>
    <p>Plan <?= e($pedido['plan']) ?> · entrega <?= fecha_corta($pedido['fecha_entrega']) ?> · <?= e($pedido['zona_codigo'] . ' · ' . $pedido['zona']) ?></p>
  </div>
  <a class="btn btn-outline-secondary" href="<?= url('cliente/pedidos.php') ?>">Volver</a>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Estado</h2></header>

      <?php if ($pedido['estado'] === 'Cancelado'): ?>
        <p class="etiqueta peligro">Pedido cancelado</p>
      <?php endif; ?>

      <ol class="linea-tiempo">
        <?php foreach ($secuencia as $indice => $paso): ?>
          <?php
          $hecho  = in_array($paso, $alcanzados, true);
          $actual = $pedido['estado'] === $paso;
          ?>
          <li>
            <span class="punto <?= $hecho && !$actual ? 'hecho' : ($actual ? 'actual' : '') ?>">
              <?= $hecho && !$actual ? '&#10003;' : $indice + 1 ?>
            </span>
            <div>
              <p class="mb-0 <?= $actual ? 'fw-semibold' : '' ?>"><?= $paso ?></p>
              <?php foreach ($historial as $evento): ?>
                <?php if ($evento['estado_nuevo'] === $paso): ?>
                  <p class="mb-0 small text-secondary"><?= fecha_hora($evento['fecha']) ?> · <?= e($evento['usuario']) ?></p>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>

      <?php if ($pedido['vehiculo']): ?>
        <p class="small text-secondary mb-0">Reparto asignado: <?= e($pedido['vehiculo']) ?></p>
      <?php endif; ?>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="panel">
      <header><h2>Detalle</h2></header>
      <div class="tabla-scroll">
        <table class="table table-sm align-middle">
          <thead><tr><th>Plato</th><th>Cant.</th><th class="text-end">Subtotal</th></tr></thead>
          <tbody>
            <?php foreach ($detalle as $linea): ?>
              <tr>
                <td><?= e($linea['producto']) ?></td>
                <td><?= (int) $linea['cantidad'] ?></td>
                <td class="text-end"><?= moneda($linea['subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2">Total</th>
              <th class="text-end"><?= moneda($pedido['total']) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <p class="small text-secondary mb-0">Entrega en <?= e($pedido['direccion']) ?>.</p>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
