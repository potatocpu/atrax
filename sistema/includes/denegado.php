<?php
$titulo = 'Acceso denegado';
$seccion = '';
require APP_RAIZ . '/includes/cabecera.php';
?>
<section class="text-center py-5">
  <p class="display-6 mb-2">403</p>
  <h1 class="h4">Acceso denegado</h1>
  <p class="text-secondary">
    Tu rol (<strong><?= e(rol_actual()) ?></strong>) no tiene permiso para ver esta pantalla.
  </p>
  <a class="btn btn-primary" href="<?= url(panel_de(rol_actual())) ?>">Volver a mi panel</a>
</section>
<?php require APP_RAIZ . '/includes/pie.php'; ?>
