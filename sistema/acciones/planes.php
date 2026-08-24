<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/catalogo.php';

solo_post();
validar_token();

$id      = entero(campo('id'));
$viandas = entero(campo('viandas'));
$activo  = isset($_POST['activo']) ? 1 : 0;
$errores = [];

if ($id === null || plan_por_id($id) === null) {
    $errores[] = 'El plan indicado no existe.';
}

if ($viandas === null || $viandas < 1 || $viandas > 60) {
    $errores[] = 'La cantidad de viandas debe estar entre 1 y 60.';
}

if ($errores) {
    volver_con_errores('admin/menus.php', $errores);
}

actualizar_plan($id, $viandas, $activo);
volver_con_exito('admin/menus.php', 'Plan actualizado correctamente.');
