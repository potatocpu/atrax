<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/catalogo.php';

$planes    = listar_planes();
$productos = listar_productos();

$titulo  = 'Menus y precios';
$seccion = 'menus';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Menus y precios</h1>
    <p>Los cambios se reflejan de inmediato en el catalogo del cliente.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Modalidades de plan</h2></header>
  <div class="row g-3">
    <?php foreach ($planes as $plan): ?>
      <div class="col-md-4">
        <form class="tarjeta" action="<?= url('acciones/planes.php') ?>" method="post">
          <?= campo_token() ?>
          <input type="hidden" name="id" value="<?= (int) $plan['id'] ?>">
          <h3 class="h5">Plan <?= e($plan['nombre']) ?></h3>
          <p class="tarjeta-pie"><?= e($plan['descripcion']) ?></p>

          <div class="mb-2">
            <label class="form-label" for="viandas<?= (int) $plan['id'] ?>">Viandas por plan</label>
            <input class="form-control" type="number" min="1" max="60" required
                   id="viandas<?= (int) $plan['id'] ?>" name="viandas" value="<?= (int) $plan['viandas'] ?>">
          </div>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" name="activo" value="1"
                   id="activo<?= (int) $plan['id'] ?>"<?= $plan['activo'] ? ' checked' : '' ?>>
            <label class="form-check-label" for="activo<?= (int) $plan['id'] ?>">Activo</label>
          </div>

          <button class="btn btn-primary w-100" type="submit">Guardar plan</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="panel">
  <header><h2>Platos del menu</h2></header>
  <form action="<?= url('acciones/productos.php') ?>" method="post">
    <?= campo_token() ?>
    <input type="hidden" name="accion" value="actualizar">
    <div class="tabla-scroll">
      <table class="table align-middle">
        <thead>
          <tr><th>Plato</th><th>Descripcion</th><th>Precio por vianda</th><th>Activo</th></tr>
        </thead>
        <tbody>
          <?php foreach ($productos as $producto): ?>
            <tr>
              <td class="fw-semibold"><?= e($producto['nombre']) ?></td>
              <td class="text-secondary"><?= e($producto['descripcion']) ?></td>
              <td style="max-width:160px">
                <label class="visually-hidden" for="precio<?= (int) $producto['id'] ?>">Precio de <?= e($producto['nombre']) ?></label>
                <input class="form-control form-control-sm" type="number" step="0.01" min="1" max="99999" required
                       id="precio<?= (int) $producto['id'] ?>" name="precio[<?= (int) $producto['id'] ?>]"
                       value="<?= e($producto['precio']) ?>">
              </td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" value="1"
                         id="activo<?= (int) $producto['id'] ?>" name="activo[<?= (int) $producto['id'] ?>]"
                         <?= $producto['activo'] ? ' checked' : '' ?>>
                  <label class="form-check-label" for="activo<?= (int) $producto['id'] ?>">Disponible</label>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Guardar precios y disponibilidad</button>
  </form>
</section>

<section class="panel">
  <header><h2>Agregar plato</h2></header>
  <form action="<?= url('acciones/productos.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>
    <input type="hidden" name="accion" value="crear">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label" for="nombre">Nombre</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?= e(viejo('nombre')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="descripcion">Descripcion</label>
        <input class="form-control" type="text" id="descripcion" name="descripcion" maxlength="255" required
               value="<?= e(viejo('descripcion')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="precio_nuevo">Precio</label>
        <input class="form-control" type="number" step="0.01" min="1" max="99999" id="precio_nuevo" name="precio" required
               value="<?= e(viejo('precio')) ?>">
        <div class="invalid-feedback">Mayor a 0.</div>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="stock_minimo">Stock minimo</label>
        <input class="form-control" type="number" min="0" max="999" id="stock_minimo" name="stock_minimo" required
               value="<?= e(viejo('stock_minimo', '10')) ?>">
        <div class="invalid-feedback">Entre 0 y 999.</div>
      </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit">Agregar plato</button>
  </form>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
