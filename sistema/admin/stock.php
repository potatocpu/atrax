<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/stock.php';

$productos = stock_por_producto();

$titulo  = 'Stock minimo';
$seccion = 'stock';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Configuracion de stock minimo</h1>
    <p>Cuando el stock disponible cae por debajo del minimo, el sistema genera una alerta en el dashboard y en el panel del operador.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Platos y umbrales</h2></header>

  <form action="<?= url('acciones/stock_minimo.php') ?>" method="post">
    <?= campo_token() ?>
    <div class="tabla-scroll">
      <table class="table align-middle">
        <thead>
          <tr><th>Plato</th><th>Disponible</th><th>Stock minimo</th><th>Estado</th></tr>
        </thead>
        <tbody>
          <?php foreach ($productos as $producto): ?>
            <?php
            $agotado = (int) $producto['disponible'] <= 0;
            $bajo    = (int) $producto['disponible'] < (int) $producto['stock_minimo'];
            ?>
            <tr>
              <td class="fw-semibold"><?= e($producto['nombre']) ?></td>
              <td><?= (int) $producto['disponible'] ?></td>
              <td style="max-width:140px">
                <label class="visually-hidden" for="minimo<?= (int) $producto['id'] ?>">Stock minimo de <?= e($producto['nombre']) ?></label>
                <input class="form-control form-control-sm" type="number" min="0" max="999" required
                       id="minimo<?= (int) $producto['id'] ?>"
                       name="minimo[<?= (int) $producto['id'] ?>]"
                       value="<?= (int) $producto['stock_minimo'] ?>">
              </td>
              <td><span class="etiqueta <?= $agotado ? 'peligro' : ($bajo ? 'aviso' : 'ok') ?>"><?= $agotado ? 'agotado' : ($bajo ? 'bajo' : 'ok') ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Guardar cambios</button>
  </form>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
