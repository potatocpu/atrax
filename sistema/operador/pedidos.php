<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/pedidos.php';

$estadoFiltro = $_GET['estado'] ?? '';

if (!in_array($estadoFiltro, estados_pedido(), true)) {
    $estadoFiltro = '';
}

$pedidos = listar_pedidos(['estado' => $estadoFiltro]);

$titulo  = 'Pedidos';
$seccion = 'pedidos';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Pedidos</h1>
    <p>El sistema solo permite avanzar el pedido por los estados previstos en el flujo.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Filtrar por estado</h2></header>
  <nav class="atrax-pills" aria-label="Filtro de estados">
    <a class="pill<?= $estadoFiltro === '' ? ' active' : '' ?>" href="<?= url('operador/pedidos.php') ?>">Todos</a>
    <?php foreach (estados_pedido() as $estado): ?>
      <a class="pill<?= $estadoFiltro === $estado ? ' active' : '' ?>"
         href="<?= url('operador/pedidos.php?estado=' . urlencode($estado)) ?>"><?= $estado ?></a>
    <?php endforeach; ?>
  </nav>
</section>

<section class="panel">
  <header><h2>Pedidos (<?= count($pedidos) ?>)</h2></header>
  <div class="tabla-scroll">
    <table class="table align-middle">
      <thead>
        <tr><th>Pedido</th><th>Cliente</th><th>Zona</th><th>Entrega</th><th>Viandas</th><th>Estado</th><th>Cambiar estado</th></tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <?php $siguientes = transiciones($pedido['estado']); ?>
          <tr>
            <td class="fw-semibold">#<?= (int) $pedido['id'] ?></td>
            <td><?= e($pedido['cliente']) ?></td>
            <td><?= e($pedido['zona_codigo']) ?></td>
            <td><?= fecha_corta($pedido['fecha_entrega']) ?></td>
            <td><?= (int) $pedido['viandas'] ?></td>
            <td><span class="etiqueta <?= clase_estado($pedido['estado']) ?>"><?= e($pedido['estado']) ?></span></td>
            <td>
              <?php if ($siguientes): ?>
                <form class="d-flex gap-2" action="<?= url('acciones/pedidos.php') ?>" method="post"
                      data-confirmar-cuando="estado:Cancelado"
                      data-confirmar="Las viandas vuelven a sus lotes y se libera el vehiculo. Un pedido cancelado no se puede reactivar."
                      data-confirmar-titulo="Cancelar el pedido #<?= (int) $pedido['id'] ?>?"
                      data-confirmar-boton="Cancelar pedido" data-confirmar-tipo="peligro">
                  <?= campo_token() ?>
                  <input type="hidden" name="accion" value="estado">
                  <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                  <label class="visually-hidden" for="estado<?= (int) $pedido['id'] ?>">Nuevo estado del pedido <?= (int) $pedido['id'] ?></label>
                  <select class="form-select form-select-sm" id="estado<?= (int) $pedido['id'] ?>" name="estado">
                    <?php foreach ($siguientes as $siguiente): ?>
                      <option value="<?= $siguiente ?>"><?= $siguiente ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-sm btn-primary" type="submit">Aplicar</button>
                </form>
              <?php else: ?>
                <span class="text-secondary small">estado final</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$pedidos): ?>
          <tr><td colspan="7" class="text-secondary">No hay pedidos con ese estado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
