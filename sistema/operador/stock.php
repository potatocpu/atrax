<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/stock.php';

$lotes       = listar_lotes();
$disponibles = array_values(array_filter($lotes, static fn ($lote) => $lote['estado'] === 'Disponible' && (int) $lote['disponible'] > 0));
$alertas     = alertas_stock();
$movimientos = ultimos_movimientos();

$titulo  = 'Stock';
$seccion = 'stock';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Stock por lotes</h1>
    <p>Los lotes se consumen por orden de vencimiento: el primero de la lista es el primero que sale.</p>
  </div>
</div>

<?php if ($alertas): ?>
  <div class="alert alert-warning" role="alert">
    <strong><?= count($alertas) ?> alertas:</strong>
    <?php $textos = array_map(static fn ($a) => $a['nombre'] . ' (' . (int) $a['disponible'] . ' / min ' . (int) $a['stock_minimo'] . ')', $alertas); ?>
    <?= e(implode(' · ', $textos)) ?>.
    <a href="<?= url('operador/produccion.php') ?>">Generar produccion</a>
  </div>
<?php endif; ?>

<section class="panel">
  <header><h2>Lotes por orden de salida</h2></header>
  <div class="tabla-scroll">
    <table class="table align-middle">
      <thead>
        <tr><th>Orden</th><th>Lote</th><th>Plato</th><th>Producido</th><th>Vence</th><th>Cant.</th><th>Disponible</th><th>Estado</th></tr>
      </thead>
      <tbody>
        <?php $orden = 0; ?>
        <?php foreach ($lotes as $lote): ?>
          <?php
          $vigente = $lote['estado'] === 'Disponible' && (int) $lote['disponible'] > 0 && (int) $lote['dias_para_vencer'] >= 0;
          if ($vigente) {
              $orden++;
          }
          ?>
          <tr>
            <td><?= $vigente ? '<span class="etiqueta acento">' . $orden . '</span>' : '<span class="text-secondary">-</span>' ?></td>
            <td class="fw-semibold"><?= e($lote['numero_lote']) ?></td>
            <td><?= e($lote['producto']) ?></td>
            <td><?= fecha_corta($lote['fecha_produccion']) ?></td>
            <td><?= fecha_corta($lote['fecha_vencimiento']) ?></td>
            <td><?= (int) $lote['cantidad'] ?></td>
            <td><?= (int) $lote['disponible'] ?></td>
            <td>
              <?php if ($lote['estado'] !== 'Disponible'): ?>
                <span class="etiqueta <?= $lote['estado'] === 'Agotado' ? 'peligro' : '' ?>"><?= e(strtolower($lote['estado'])) ?></span>
              <?php elseif ((int) $lote['dias_para_vencer'] < 0): ?>
                <span class="etiqueta peligro">vencido</span>
              <?php elseif ((int) $lote['dias_para_vencer'] === 0): ?>
                <span class="etiqueta aviso">vence hoy</span>
              <?php else: ?>
                <span class="etiqueta ok">ok</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel">
  <header><h2>Registrar movimiento de stock</h2></header>
  <form action="<?= url('acciones/stock.php') ?>" method="post" data-validar novalidate
        data-confirmar-cuando="tipo:Descarte"
        data-confirmar="Se descarta todo lo disponible del lote {lote_id}. Esta accion no se puede deshacer."
        data-confirmar-titulo="Descartar el lote?" data-confirmar-boton="Descartar" data-confirmar-tipo="peligro">
    <?= campo_token() ?>

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label" for="lote_id">Lote</label>
        <select class="form-select" id="lote_id" name="lote_id" required>
          <option value="">Seleccionar</option>
          <?php foreach ($disponibles as $lote): ?>
            <option value="<?= (int) $lote['id'] ?>">
              <?= e($lote['numero_lote'] . ' · ' . $lote['producto'] . ' (' . (int) $lote['disponible'] . ' disp.)') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Elegi un lote.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="tipo">Tipo</label>
        <select class="form-select" id="tipo" name="tipo" required>
          <option value="Salida">Salida</option>
          <option value="Ajuste">Ajuste (suma)</option>
          <option value="Descarte">Descarte del lote</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="cantidad">Cantidad</label>
        <input class="form-control" type="number" id="cantidad" name="cantidad" min="1" max="500" value="1" required>
        <div class="invalid-feedback">Mayor a 0.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="motivo">Motivo</label>
        <input class="form-control" type="text" id="motivo" name="motivo" maxlength="150" required
               placeholder="Rotura, control de calidad, recuento...">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit">Registrar movimiento</button>
  </form>
</section>

<section class="panel">
  <header><h2>Ultimos movimientos</h2></header>
  <div class="tabla-scroll">
    <table class="table table-sm align-middle">
      <thead><tr><th>Fecha</th><th>Lote</th><th>Plato</th><th>Tipo</th><th>Cant.</th><th>Motivo</th><th>Usuario</th></tr></thead>
      <tbody>
        <?php foreach ($movimientos as $movimiento): ?>
          <tr>
            <td class="text-secondary"><?= fecha_hora($movimiento['fecha']) ?></td>
            <td><?= e($movimiento['numero_lote']) ?></td>
            <td><?= e($movimiento['producto']) ?></td>
            <td><span class="etiqueta <?= $movimiento['tipo'] === 'Entrada' ? 'ok' : ($movimiento['tipo'] === 'Ajuste' ? 'acento' : '') ?>"><?= e($movimiento['tipo']) ?></span></td>
            <td><?= (int) $movimiento['cantidad'] ?></td>
            <td class="text-secondary"><?= e($movimiento['motivo']) ?></td>
            <td class="text-secondary"><?= e($movimiento['usuario']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
