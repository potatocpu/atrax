<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/usuarios.php';

$usuarios = listar_usuarios();
$roles    = listar_roles();

$titulo  = 'Usuarios';
$seccion = 'usuarios';
require __DIR__ . '/../includes/cabecera.php';
?>
<div class="encabezado-pagina">
  <div>
    <h1>Usuarios del sistema</h1>
    <p>Los clientes se registran solos desde el formulario publico. El administrador da de alta operadores y administradores.</p>
  </div>
</div>

<section class="panel">
  <header><h2>Usuarios registrados (<?= count($usuarios) ?>)</h2></header>
  <div class="tabla-scroll">
    <table class="table align-middle">
      <thead>
        <tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Zona</th><th>Ultimo acceso</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $fila): ?>
          <tr>
            <td class="fw-semibold"><?= e($fila['nombre']) ?></td>
            <td><?= e($fila['usuario']) ?></td>
            <td class="text-secondary"><?= e($fila['email']) ?></td>
            <td><span class="etiqueta acento"><?= e($fila['rol']) ?></span></td>
            <td><?= e($fila['zona'] ?? '-') ?></td>
            <td class="text-secondary"><?= fecha_hora($fila['ultimo_login']) ?></td>
            <td><span class="etiqueta <?= $fila['activo'] ? 'ok' : '' ?>"><?= $fila['activo'] ? 'activo' : 'inactivo' ?></span></td>
            <td class="text-end">
              <?php if ((int) $fila['id'] !== (int) usuario()['id']): ?>
                <form action="<?= url('acciones/usuarios.php') ?>" method="post">
                  <?= campo_token() ?>
                  <input type="hidden" name="accion" value="estado">
                  <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                  <input type="hidden" name="activo" value="<?= $fila['activo'] ? 0 : 1 ?>">
                  <button class="btn btn-sm btn-outline-secondary" type="submit"><?= $fila['activo'] ? 'Desactivar' : 'Activar' ?></button>
                </form>
              <?php else: ?>
                <span class="text-secondary small">sesion actual</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel">
  <header><h2>Nuevo usuario interno</h2></header>
  <form action="<?= url('acciones/usuarios.php') ?>" method="post" data-validar novalidate>
    <?= campo_token() ?>
    <input type="hidden" name="accion" value="crear">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label" for="nombre">Nombre y apellido</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?= e(viejo('nombre')) ?>">
        <div class="invalid-feedback">Obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="usuario">Usuario</label>
        <input class="form-control" type="text" id="usuario" name="usuario" maxlength="40" minlength="4"
               pattern="[A-Za-z0-9_.]+" required value="<?= e(viejo('usuario')) ?>">
        <div class="invalid-feedback">Entre 4 y 40 caracteres.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="email">Correo</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="150" required
               value="<?= e(viejo('email')) ?>">
        <div class="invalid-feedback">Correo invalido.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="telefono">Telefono</label>
        <input class="form-control" type="tel" id="telefono" name="telefono" maxlength="20" required
               value="<?= e(viejo('telefono')) ?>">
        <div class="invalid-feedback">Telefono invalido.</div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="rol_id">Rol</label>
        <select class="form-select" id="rol_id" name="rol_id" required>
          <?php foreach ($roles as $rol): ?>
            <?php if ($rol['nombre'] === 'cliente') { continue; } ?>
            <option value="<?= (int) $rol['id'] ?>"<?= (string) viejo('rol_id') === (string) $rol['id'] ? ' selected' : '' ?>>
              <?= e($rol['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="contrasena">Contrasena inicial</label>
        <div class="input-group">
          <input class="form-control" type="password" id="contrasena" name="contrasena" minlength="8" required>
          <button class="btn btn-outline-secondary" type="button" data-ver-clave="contrasena">ver</button>
          <div class="invalid-feedback">Minimo 8 caracteres.</div>
        </div>
      </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit">Crear usuario</button>
  </form>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
