<?php

require_once __DIR__ . '/../includes/auth.php';
requiere_rol('operador');

require_once __DIR__ . '/../modelo/pedidos.php';

solo_post();
validar_token();

$id     = entero(campo('id'));
$nuevo  = campo('estado');
$pedido = $id === null ? null : pedido_por_id($id);
$volver = 'operador/pedidos.php';

if ($pedido === null) {
    volver_con_errores($volver, ['El pedido indicado no existe.']);
}

if (!in_array($nuevo, estados_pedido(), true)) {
    volver_con_errores($volver, ['El estado indicado no es valido.']);
}

if (!in_array($nuevo, transiciones($pedido['estado']), true)) {
    volver_con_errores($volver, ['No se puede pasar el pedido #' . $id . ' de "' . $pedido['estado'] . '" a "' . $nuevo . '".']);
}

if ($nuevo === 'En distribucion' && $pedido['vehiculo_id'] === null) {
    volver_con_errores($volver, ['Antes de despachar el pedido #' . $id . ' hay que asignarle un vehiculo en Distribucion.']);
}

cambiar_estado($id, $pedido['estado'], $nuevo, (int) usuario()['id']);
volver_con_exito($volver, 'El pedido #' . $id . ' paso a "' . $nuevo . '".');
