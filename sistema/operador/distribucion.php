<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/pedidos.php';
require_once __DIR__ . '/../modelo/zonas.php';

$sinAsignar = listar_pedidos(['estado' => 'Listo', 'sin_vehiculo' => true]);
$vehiculos  = listar_vehiculos();
$asignados  = array_merge(listar_pedidos(['estado' => 'Listo']), listar_pedidos(['estado' => 'En distribucion']));

$titulo  = 'Distribucion';
$seccion = 'distribucion';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Distribucion por zonas</h1>
    <p>Un pedido solo se puede asignar a un vehiculo de su misma zona y mientras haya capacidad libre.</p>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-5">
    <section class="panel">
      <header><h2>Pedidos listos sin asignar (<?= count($sinAsignar) ?>)</h2></header>

      <?php if (!$sinAsignar): ?>
        <p class="text-secondary mb-0">Todos los pedidos listos ya tienen vehiculo asignado.</p>
      <?php endif; ?>

      <?php foreach ($sinAsignar as $pedido): ?>
        <article class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <p class="mb-0 fw-semibold">#<?= (int) $pedido['id'] ?> · <?= e($pedido['zona_codigo']) ?></p>
              <p class="mb-0 small text-secondary"><?= e($pedido['cliente']) ?> · <?= e($pedido['zona']) ?></p>
            </div>
            <span class="etiqueta acento"><?= (int) $pedido['viandas'] ?> viandas</span>
          </div>

          <form class="d-flex gap-2" action="<?= url('acciones/distribucion.php') ?>" method="post">
            <?= campo_token() ?>
            <input type="hidden" name="accion" value="asignar">
            <input type="hidden" name="pedido_id" value="<?= (int) $pedido['id'] ?>">
            <label class="visually-hidden" for="vehiculo<?= (int) $pedido['id'] ?>">Vehiculo para el pedido <?= (int) $pedido['id'] ?></label>
            <select class="form-select form-select-sm" id="vehiculo<?= (int) $pedido['id'] ?>" name="vehiculo_id" required>
              <option value="">Elegir vehiculo</option>
              <?php foreach ($vehiculos as $vehiculo): ?>
                <?php if ((int) $vehiculo['zona_id'] !== (int) $pedido['zona_id']) { continue; } ?>
                <option value="<?= (int) $vehiculo['id'] ?>">
                  <?= e($vehiculo['nombre'] . ' · ' . $vehiculo['zona_codigo'] . ' (' . (int) $vehiculo['carga'] . '/' . (int) $vehiculo['capacidad'] . ')') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-primary" type="submit">Asignar</button>
          </form>
        </article>
      <?php endforeach; ?>
    </section>
  </div>

  <div class="col-lg-7">
    <section class="panel">
      <header><h2>Vehiculos y carga</h2></header>
      <div class="row g-3">
        <?php foreach ($vehiculos as $vehiculo): ?>
          <?php
          $capacidad = max(1, (int) $vehiculo['capacidad']);
          $ocupacion = min(100, (int) round(100 * $vehiculo['carga'] / $capacidad));
          $lleno     = (int) $vehiculo['carga'] >= $capacidad;
          ?>
          <div class="col-md-6">
            <article class="tarjeta">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <h3 class="h6 mb-0"><?= e($vehiculo['nombre'] . ' · ' . $vehiculo['zona_codigo']) ?></h3>
                <span class="etiqueta <?= $lleno ? 'aviso' : ($vehiculo['estado'] === 'Disponible' ? 'ok' : ($vehiculo['estado'] === 'En ruta' ? 'acento' : '')) ?>">
                  <?= $lleno ? 'capacidad llena' : e($vehiculo['estado']) ?>
                </span>
              </div>
              <p class="tarjeta-pie">Carga: <?= (int) $vehiculo['carga'] ?> / <?= $capacidad ?> viandas</p>
              <div class="barra mb-3"><span class="<?= $lleno ? 'lleno' : '' ?>" style="width: <?= $ocupacion ?>%"></span></div>

              <ul class="list-unstyled small mb-0">
                <?php foreach ($asignados as $pedido): ?>
                  <?php if ((int) $pedido['vehiculo_id'] !== (int) $vehiculo['id']) { continue; } ?>
                  <li class="d-flex justify-content-between align-items-center border-top py-2">
                    <span>#<?= (int) $pedido['id'] ?> · <?= (int) $pedido['viandas'] ?> viandas</span>
                    <?php if ($pedido['estado'] === 'Listo'): ?>
                      <form action="<?= url('acciones/distribucion.php') ?>" method="post"
                            data-confirmar="El pedido #<?= (int) $pedido['id'] ?> vuelve a la lista de pedidos listos sin asignar."
                            data-confirmar-titulo="Quitar del <?= e($vehiculo['nombre']) ?>?" data-confirmar-boton="Quitar">
                        <?= campo_token() ?>
                        <input type="hidden" name="accion" value="quitar">
                        <input type="hidden" name="pedido_id" value="<?= (int) $pedido['id'] ?>">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">Quitar</button>
                      </form>
                    <?php else: ?>
                      <span class="etiqueta <?= clase_estado($pedido['estado']) ?>"><?= e($pedido['estado']) ?></span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
