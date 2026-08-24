<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/pedidos.php';
require_once __DIR__ . '/../modelo/catalogo.php';
require_once __DIR__ . '/../modelo/stock.php';

$produccion = produccion_del_dia();
$productos  = listar_productos(true);
$lotesHoy   = array_filter(listar_lotes(), static fn ($lote) => $lote['fecha_produccion'] === date('Y-m-d'));

$titulo  = 'Produccion';
$seccion = 'produccion';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Produccion · tickets del dia</h1>
    <p>Cada produccion registrada crea un lote nuevo con su fecha de vencimiento y entra al stock.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Tickets de hoy</h2></header>
  <div class="row g-3">
    <?php foreach ($produccion as $fila): ?>
      <?php
      $falta  = max(0, (int) $fila['pedidas'] - (int) $fila['producidas']);
      $estado = (int) $fila['pedidas'] === 0 ? 'sin pedidos' : ($falta === 0 ? 'listo' : 'pendiente');
      ?>
      <div class="col-md-6 col-xl-4">
        <article class="tarjeta">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="tarjeta-titulo mb-0">Ticket T-<?= str_pad((string) $fila['id'], 4, '0', STR_PAD_LEFT) ?></span>
            <span class="etiqueta <?= $estado === 'listo' ? 'ok' : ($estado === 'pendiente' ? 'aviso' : '') ?>"><?= $estado ?></span>
          </div>
          <h3 class="h6"><?= e($fila['nombre']) ?></h3>
          <dl class="row small mb-0">
            <dt class="col-7">Pedidas para hoy</dt><dd class="col-5 text-end mb-1"><?= (int) $fila['pedidas'] ?></dd>
            <dt class="col-7">Producidas hoy</dt><dd class="col-5 text-end mb-1"><?= (int) $fila['producidas'] ?></dd>
            <dt class="col-7">Falta producir</dt><dd class="col-5 text-end mb-0 fw-semibold"><?= $falta ?></dd>
          </dl>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="panel">
  <header><h2>Registrar produccion</h2></header>
  <form action="<?= url('acciones/produccion.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label" for="producto_id">Plato producido</label>
        <select class="form-select" id="producto_id" name="producto_id" required>
          <option value="">Seleccionar</option>
          <?php foreach ($productos as $producto): ?>
            <option value="<?= (int) $producto['id'] ?>"<?= (string) viejo('producto_id') === (string) $producto['id'] ? ' selected' : '' ?>>
              <?= e($producto['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Elegi un plato.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="cantidad">Cantidad producida</label>
        <input class="form-control" type="number" id="cantidad" name="cantidad" min="1" max="500" required
               value="<?= e(viejo('cantidad')) ?>">
        <div class="invalid-feedback">Entre 1 y 500.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="fecha_vencimiento">Vence el</label>
        <input class="form-control" type="date" id="fecha_vencimiento" name="fecha_vencimiento" required
               min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
               value="<?= e(viejo('fecha_vencimiento', date('Y-m-d', strtotime('+3 days')))) ?>">
        <div class="invalid-feedback">Fecha invalida.</div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100" type="submit">Registrar lote</button>
      </div>
    </div>
  </form>
</section>

<section class="panel">
  <header><h2>Lotes producidos hoy</h2></header>
  <div class="tabla-scroll">
    <table class="table table-sm align-middle">
      <thead><tr><th>Lote</th><th>Plato</th><th>Cantidad</th><th>Disponible</th><th>Vence</th><th>Operador</th></tr></thead>
      <tbody>
        <?php foreach ($lotesHoy as $lote): ?>
          <tr>
            <td class="fw-semibold"><?= e($lote['numero_lote']) ?></td>
            <td><?= e($lote['producto']) ?></td>
            <td><?= (int) $lote['cantidad'] ?></td>
            <td><?= (int) $lote['disponible'] ?></td>
            <td><?= fecha_corta($lote['fecha_vencimiento']) ?></td>
            <td class="text-secondary"><?= e($lote['operador']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$lotesHoy): ?>
          <tr><td colspan="6" class="text-secondary">Todavia no se registro produccion hoy.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
