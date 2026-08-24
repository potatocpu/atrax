<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/catalogo.php';
require_once __DIR__ . '/../modelo/stock.php';

solo_post();
validar_token();

$productoId  = entero(campo('producto_id'));
$cantidad    = entero(campo('cantidad'));
$vencimiento = campo('fecha_vencimiento');
$datos       = ['producto_id' => campo('producto_id'), 'cantidad' => campo('cantidad'), 'fecha_vencimiento' => $vencimiento];
$errores     = [];
$producto    = $productoId === null ? null : producto_por_id($productoId);

if ($producto === null) {
    $errores[] = 'El plato seleccionado no existe.';
} elseif ((int) $producto['activo'] !== 1) {
    $errores[] = 'No se puede producir un plato dado de baja del menu.';
}

if ($cantidad === null || $cantidad < 1 || $cantidad > 500) {
    $errores[] = 'La cantidad producida debe ser un numero entre 1 y 500.';
}

if (!es_fecha($vencimiento)) {
    $errores[] = 'La fecha de vencimiento no es valida.';
} elseif ($vencimiento < date('Y-m-d')) {
    $errores[] = 'La fecha de vencimiento no puede ser anterior a hoy.';
} elseif ($vencimiento > date('Y-m-d', strtotime('+30 days'))) {
    $errores[] = 'La fecha de vencimiento no puede superar los 30 dias.';
}

if ($errores) {
    volver_con_errores('operador/produccion.php', $errores, $datos);
}

$numero = siguiente_numero_lote();
crear_lote($productoId, (int) usuario()['id'], $numero, $vencimiento, $cantidad);

volver_con_exito('operador/produccion.php', 'Lote ' . $numero . ' registrado: ' . $cantidad . ' viandas de ' . $producto['nombre'] . '.');
