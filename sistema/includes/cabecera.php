<?php

require_once __DIR__ . '/auth.php';

$titulo  = $titulo ?? APP_NOMBRE;
$seccion = $seccion ?? '';

$menus = [
    'administrador' => [
        ['dashboard',     'Dashboard',       'admin/index.php'],
        ['pedidos',       'Pedidos',         'admin/pedidos.php'],
        ['stock',         'Stock minimo',    'admin/stock.php'],
        ['menus',         'Menus y precios', 'admin/menus.php'],
        ['zonas',         'Zonas',           'admin/zonas.php'],
        ['vehiculos',     'Vehiculos',       'admin/vehiculos.php'],
        ['estadisticas',  'Estadisticas',    'admin/estadisticas.php'],
        ['usuarios',      'Usuarios',        'admin/usuarios.php'],
    ],
    'operador' => [
        ['panel',        'Panel del dia', 'operador/index.php'],
        ['produccion',   'Produccion',    'operador/produccion.php'],
        ['stock',        'Stock FIFO',    'operador/stock.php'],
        ['pedidos',      'Pedidos',       'operador/pedidos.php'],
        ['distribucion', 'Distribucion',  'operador/distribucion.php'],
    ],
    'cliente' => [
        ['menu',     'Menu',        'cliente/index.php'],
        ['pedido',   'Armar pedido','cliente/pedido.php'],
        ['pedidos',  'Mis pedidos', 'cliente/pedidos.php'],
    ],
];

$rol   = rol_actual();
$menu  = $menus[$rol] ?? [];
$yo    = usuario();
$exito = flash_leer('exito');
$listaErrores = errores();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo) ?> — <?= APP_NOMBRE ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= url('assets/css/atrax.css') ?>">
</head>
<body class="rol-<?= e($rol ?: 'publico') ?>">
<?php if (autenticado()): ?>
<header class="atrax-topbar">
  <div class="container-fluid d-flex align-items-center gap-3">
    <?php if ($rol === 'administrador'): ?>
      <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button"
              data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral">
        <span class="visually-hidden">Abrir menu</span>&#9776;
      </button>
    <?php endif; ?>

    <a class="atrax-marca" href="<?= url(panel_de($rol)) ?>">
      <span class="atrax-logo" aria-hidden="true">A</span>
      <span><?= APP_NOMBRE ?></span>
    </a>

    <?php if ($rol !== 'administrador'): ?>
      <nav class="atrax-pills d-none d-md-flex" aria-label="Secciones">
        <?php foreach ($menu as [$clave, $texto, $ruta]): ?>
          <a class="pill<?= $seccion === $clave ? ' active' : '' ?>" href="<?= url($ruta) ?>"><?= e($texto) ?></a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>

    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="d-none d-sm-inline text-secondary small"><?= e($yo['nombre']) ?> · <?= e($rol) ?></span>
      <span class="atrax-avatar" aria-hidden="true"><?= e($yo['inicial']) ?></span>
      <a class="btn btn-sm btn-outline-secondary" href="<?= url('logout.php') ?>">Salir</a>
    </div>
  </div>
</header>

<div class="container-fluid atrax-cuerpo">
  <div class="row g-0">
    <?php if ($rol === 'administrador'): ?>
      <div class="col-lg-2">
        <aside class="atrax-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="menuLateral"
               aria-label="Menu del administrador">
          <div class="offcanvas-header d-lg-none">
            <h2 class="offcanvas-title h6">Menu</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral"></button>
          </div>
          <nav class="offcanvas-body">
            <ul class="nav flex-column">
              <?php foreach ($menu as [$clave, $texto, $ruta]): ?>
                <li class="nav-item">
                  <a class="nav-link<?= $seccion === $clave ? ' active' : '' ?>" href="<?= url($ruta) ?>"><?= e($texto) ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>
        </aside>
      </div>
      <div class="col-lg-10">
    <?php else: ?>
      <div class="col-12">
    <?php endif; ?>

      <main class="atrax-main">
        <?php if ($rol !== 'administrador'): ?>
          <nav class="atrax-pills d-md-none mb-3" aria-label="Secciones">
            <?php foreach ($menu as [$clave, $texto, $ruta]): ?>
              <a class="pill<?= $seccion === $clave ? ' active' : '' ?>" href="<?= url($ruta) ?>"><?= e($texto) ?></a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
<?php else: ?>
<main class="atrax-publico">
<?php endif; ?>

<?php if ($exito): ?>
  <div class="alert alert-success" role="status"><?= e($exito) ?></div>
<?php endif; ?>

<?php if ($listaErrores): ?>
  <div class="alert alert-danger" role="alert">
    <ul class="mb-0 ps-3">
      <?php foreach ($listaErrores as $mensaje): ?>
        <li><?= e($mensaje) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
