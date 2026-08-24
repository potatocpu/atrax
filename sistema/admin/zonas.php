<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/zonas.php';

$zonas  = listar_zonas();
$editar = null;

if (isset($_GET['editar'])) {
    $editar = zona_por_id((int) $_GET['editar']);
}

$titulo  = 'Zonas de distribucion';
$seccion = 'zonas';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Zonas de distribucion</h1>
    <p>Las zonas agrupan los barrios de entrega y se asignan a clientes y vehiculos.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Zonas registradas</h2></header>
  <div class="tabla-scroll">
    <table class="table align-middle">
      <thead>
        <tr><th>Codigo</th><th>Zona</th><th>Barrios</th><th>Vehiculos</th><th>Clientes</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($zonas as $zona): ?>
          <tr>
            <td class="fw-semibold"><?= e($zona['codigo']) ?></td>
            <td><?= e($zona['nombre']) ?></td>
            <td class="text-secondary"><?= e($zona['barrios']) ?></td>
            <td><?= (int) $zona['vehiculos'] ?></td>
            <td><?= (int) $zona['clientes'] ?></td>
            <td>
              <span class="etiqueta <?= $zona['activa'] ? 'ok' : '' ?>"><?= $zona['activa'] ? 'activa' : 'inactiva' ?></span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/zonas.php?editar=' . (int) $zona['id']) ?>">Editar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel">
  <header>
    <h2><?= $editar ? 'Editar zona ' . e($editar['codigo']) : 'Nueva zona' ?></h2>
    <?php if ($editar): ?>
      <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/zonas.php') ?>">Cancelar edicion</a>
    <?php endif; ?>
  </header>

  <form action="<?= url('acciones/zonas.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>
    <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

    <div class="row g-3">
      <div class="col-md-2">
        <label class="form-label" for="codigo">Codigo</label>
        <input class="form-control" type="text" id="codigo" name="codigo" maxlength="5" required
               value="<?= e(viejo('codigo', $editar['codigo'] ?? '')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="nombre">Nombre de zona</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="60" required
               value="<?= e(viejo('nombre', $editar['nombre'] ?? '')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="barrios">Barrios incluidos</label>
        <input class="form-control" type="text" id="barrios" name="barrios" maxlength="150" required
               value="<?= e(viejo('barrios', $editar['barrios'] ?? '')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="activa">Estado</label>
        <select class="form-select" id="activa" name="activa">
          <option value="1"<?= (string) viejo('activa', $editar['activa'] ?? 1) === '1' ? ' selected' : '' ?>>Activa</option>
          <option value="0"<?= (string) viejo('activa', $editar['activa'] ?? 1) === '0' ? ' selected' : '' ?>>Inactiva</option>
        </select>
      </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit"><?= $editar ? 'Guardar cambios' : 'Crear zona' ?></button>
  </form>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
