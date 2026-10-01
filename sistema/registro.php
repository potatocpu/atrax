<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/modelo/zonas.php';

if (autenticado()) {
    redirigir(panel_de(rol_actual()));
}

$zonas  = listar_zonas(true);
$titulo = 'Crear cuenta';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="acceso-tarjeta ancha">
  <header class="acceso-encabezado">
    <img class="acceso-logo" src="<?= url('assets/img/logo.svg') ?>" alt="" width="72" height="72">
    <h1>Crear cuenta</h1>
    <p>Completa tus datos para empezar a pedir viandas</p>
  </header>

  <form action="<?= url('acciones/registro.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="nombre">Nombre y apellido</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="100"
               value="<?= e(viejo('nombre')) ?>" required>
        <div class="invalid-feedback">Ingresa tu nombre completo.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="tipo">Tipo de cliente</label>
        <select class="form-select" id="tipo" name="tipo" required>
          <?php foreach (['Particular', 'Empresa', 'Institucion'] as $tipo): ?>
            <option value="<?= $tipo ?>"<?= viejo('tipo') === $tipo ? ' selected' : '' ?>><?= $tipo ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="usuario">Nombre de usuario</label>
        <input class="form-control" type="text" id="usuario" name="usuario" maxlength="40" minlength="4"
               pattern="[A-Za-z0-9_.]+" value="<?= e(viejo('usuario')) ?>" required>
        <div class="form-text">Entre 4 y 40 caracteres: letras, numeros, punto o guion bajo.</div>
        <div class="invalid-feedback">Elegi un usuario valido.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="email">Correo electronico</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="150"
               value="<?= e(viejo('email')) ?>" required>
        <div class="invalid-feedback">Ingresa un correo valido.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="telefono">Telefono</label>
        <input class="form-control" type="tel" id="telefono" name="telefono" maxlength="20"
               placeholder="+598 99 123 456" value="<?= e(viejo('telefono')) ?>" required>
        <div class="invalid-feedback">Ingresa un telefono valido.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="zona_id">Zona de entrega</label>
        <select class="form-select" id="zona_id" name="zona_id" required>
          <option value="">Seleccionar</option>
          <?php foreach ($zonas as $zona): ?>
            <option value="<?= (int) $zona['id'] ?>"<?= (string) viejo('zona_id') === (string) $zona['id'] ? ' selected' : '' ?>>
              <?= e($zona['codigo'] . ' · ' . $zona['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Elegi tu zona.</div>
      </div>

      <div class="col-12">
        <label class="form-label" for="direccion">Direccion de entrega</label>
        <input class="form-control" type="text" id="direccion" name="direccion" maxlength="200"
               value="<?= e(viejo('direccion')) ?>" required>
        <div class="invalid-feedback">Ingresa tu direccion.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="contrasena">Contrasena</label>
        <div class="input-group">
          <input class="form-control" type="password" id="contrasena" name="contrasena" minlength="8" required>
          <button class="btn btn-outline-secondary" type="button" data-ver-clave="contrasena">ver</button>
          <div class="invalid-feedback">Minimo 8 caracteres.</div>
        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="contrasena2">Repetir contrasena</label>
        <input class="form-control" type="password" id="contrasena2" name="contrasena2" minlength="8" required>
        <div class="invalid-feedback">Repeti la contrasena.</div>
      </div>
    </div>

    <button class="btn btn-primary w-100 mt-4" type="submit">Crear cuenta</button>
  </form>

  <p class="text-center mt-3 mb-0 small">
    Ya tenes cuenta? <a href="<?= url('login.php') ?>">Iniciar sesion</a>
  </p>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
