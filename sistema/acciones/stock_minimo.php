<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('administrador');

require_once __DIR__ . '/../modelo/catalogo.php';

solo_post();
validar_token();

$minimos = $_POST['minimo'] ?? [];
$errores = [];
$validos = [];

if (!is_array($minimos) || !$minimos) {
    volver_con_errores('admin/stock.php', ['No se recibieron valores para actualizar.']);
}

foreach ($minimos as $productoId => $valor) {
    $productoId = entero($productoId);
    $minimo     = entero(trim((string) $valor));

    if ($productoId === null || producto_por_id($productoId) === null) {
        $errores[] = 'Uno de los platos indicados no existe.';
        continue;
    }

    if ($minimo === null || $minimo < 0 || $minimo > 999) {
        $errores[] = 'El stock minimo debe ser un numero entre 0 y 999.';
        continue;
    }

    $validos[$productoId] = $minimo;
}

if ($errores) {
    volver_con_errores('admin/stock.php', array_values(array_unique($errores)));
}

foreach ($validos as $productoId => $minimo) {
    actualizar_stock_minimo($productoId, $minimo);
}

volver_con_exito('admin/stock.php', 'Stock minimo actualizado para ' . count($validos) . ' platos.');
