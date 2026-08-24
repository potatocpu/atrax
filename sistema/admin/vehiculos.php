<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/zonas.php';

$vehiculos = listar_vehiculos();
$zonas     = listar_zonas(true);
$estados   = ['Disponible', 'En ruta', 'Mantenimiento'];
$editar    = isset($_GET['editar']) ? vehiculo_por_id((int) $_GET['editar']) : null;

$titulo  = 'Vehiculos de reparto';
$seccion = 'vehiculos';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Vehiculos de reparto</h1>
    <p>La capacidad de cada vehiculo limita cuantas viandas puede llevar en un reparto.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Flota</h2></header>
  <div class="tabla-scroll">
    <table class="table align-middle">
      <thead>
        <tr><th>Vehiculo</th><th>Matricula</th><th>Capacidad</th><th>Carga actual</th><th>Zona habitual</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($vehiculos as $vehiculo): ?>
          <tr>
            <td class="fw-semibold"><?= e($vehiculo['nombre']) ?></td>
            <td><?= e($vehiculo['matricula']) ?></td>
            <td><?= (int) $vehiculo['capacidad'] ?> viandas</td>
            <td><?= (int) $vehiculo['carga'] ?> / <?= (int) $vehiculo['capacidad'] ?></td>
            <td><?= e($vehiculo['zona_codigo'] . ' · ' . $vehiculo['zona']) ?></td>
            <td>
              <span class="etiqueta <?= $vehiculo['estado'] === 'Disponible' ? 'ok' : ($vehiculo['estado'] === 'En ruta' ? 'acento' : '') ?>">
                <?= e($vehiculo['estado']) ?>
              </span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/vehiculos.php?editar=' . (int) $vehiculo['id']) ?>">Editar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel">
  <header>
    <h2><?= $editar ? 'Editar vehiculo' : 'Nuevo vehiculo' ?></h2>
    <?php if ($editar): ?>
      <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/vehiculos.php') ?>">Cancelar edicion</a>
    <?php endif; ?>
  </header>

  <form action="<?= url('acciones/vehiculos.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>
    <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label" for="nombre">Nombre o identificador</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="40" required
               value="<?= e(viejo('nombre', $editar['nombre'] ?? '')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="matricula">Matricula</label>
        <input class="form-control" type="text" id="matricula" name="matricula" maxlength="15" required
               value="<?= e(viejo('matricula', $editar['matricula'] ?? '')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="capacidad">Capacidad</label>
        <input class="form-control" type="number" id="capacidad" name="capacidad" min="1" max="500" required
               value="<?= e(viejo('capacidad', $editar['capacidad'] ?? '')) ?>">
        <div class="invalid-feedback">Entre 1 y 500.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="zona_id">Zona habitual</label>
        <select class="form-select" id="zona_id" name="zona_id" required>
          <option value="">Seleccionar</option>
          <?php foreach ($zonas as $zona): ?>
            <option value="<?= (int) $zona['id'] ?>"<?= (string) viejo('zona_id', $editar['zona_id'] ?? '') === (string) $zona['id'] ? ' selected' : '' ?>>
              <?= e($zona['codigo'] . ' · ' . $zona['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Elegi una zona.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="estado">Estado</label>
        <select class="form-select" id="estado" name="estado">
          <?php foreach ($estados as $estado): ?>
            <option value="<?= $estado ?>"<?= viejo('estado', $editar['estado'] ?? '') === $estado ? ' selected' : '' ?>><?= $estado ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit"><?= $editar ? 'Guardar cambios' : 'Guardar vehiculo' ?></button>
  </form>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
