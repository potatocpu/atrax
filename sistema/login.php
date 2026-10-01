<?php

require_once __DIR__ . '/includes/auth.php';

if (autenticado()) {
    redirigir(panel_de(rol_actual()));
}

$titulo = 'Iniciar sesion';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="acceso-tarjeta">
  <header class="acceso-encabezado">
    <img class="acceso-logo" src="<?= url('assets/img/logo.svg') ?>" alt="" width="88" height="88">
    <h1><?= APP_NOMBRE ?></h1>
    <p>Sistema de gestion de viandas</p>
  </header>

  <form action="<?= url('acciones/acceso.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>

    <div class="mb-3">
      <label class="form-label" for="usuario">Usuario o correo</label>
      <input class="form-control" type="text" id="usuario" name="usuario" maxlength="150"
             value="<?= e(viejo('usuario')) ?>" required autofocus>
      <div class="invalid-feedback">Ingresa tu usuario o correo.</div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="contrasena">Contrasena</label>
      <div class="input-group">
        <input class="form-control" type="password" id="contrasena" name="contrasena" required>
        <button class="btn btn-outline-secondary" type="button" data-ver-clave="contrasena">ver</button>
        <div class="invalid-feedback">Ingresa tu contrasena.</div>
      </div>
    </div>

    <button class="btn btn-primary w-100" type="submit">Ingresar</button>
  </form>

  <hr class="my-4">

  <a class="btn btn-outline-secondary w-100" href="<?= url('registro.php') ?>">Crear cuenta de cliente</a>

  <div class="cuentas-demo mt-4">
    <p class="mb-1"><strong>Cuentas de prueba</strong></p>
    <ul class="mb-0 ps-3">
      <li>Administrador: <code>admin</code> / <code>Admin123</code></li>
      <li>Operador: <code>cocina</code> / <code>Operador123</code></li>
      <li>Cliente: <code>julio</code> / <code>Cliente123</code></li>
    </ul>
  </div>
</section>

<footer class="mt-3 text-center small acceso-pie">
  <p class="mb-0"><?= APP_NOMBRE ?> &copy; 2026 — Produccion y distribucion de viandas, Montevideo.</p>
</footer>
<?php require __DIR__ . '/includes/pie.php'; ?>
