<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funciones.php';

function mostrar_error(int $codigo, ?Throwable $falla = null): void
{
    $pantallas = [
        404 => [
            'titulo'      => 'Pagina no encontrada',
            'globo'       => 'Esta no es la pagina que estabas buscando.',
            'texto'       => 'Puede que la direccion este mal escrita o que la pagina ya no exista. Nuestro reparto se perdio en el camino, pero desde aca te ayudamos a volver.',
            'ilustracion' => 'perdido',
        ],
        500 => [
            'titulo'      => 'Algo salio mal',
            'globo'       => 'Se nos quemo algo en la cocina.',
            'texto'       => 'Ocurrio un error inesperado al procesar tu solicitud. Ya quedo registrado para que lo revisemos. Volve a intentarlo en unos minutos.',
            'ilustracion' => 'caido',
        ],
        503 => [
            'titulo'      => 'Servidores caidos',
            'globo'       => 'Nuestros servidores estan caidos.',
            'texto'       => 'En este momento no podemos conectarnos con nuestros servidores, asi que el sistema no esta disponible. Ya estamos trabajando para solucionarlo: tus pedidos y tus datos estan a salvo.',
            'ilustracion' => 'caido',
            'reintentar'  => 30,
        ],
    ];

    if (APP_MANTENIMIENTO && $codigo === 503) {
        $pantallas[503]['titulo'] = 'Sistema en mantenimiento';
        $pantallas[503]['globo']  = 'Estamos haciendo mantenimiento.';
        $pantallas[503]['texto']  = 'Estamos mejorando el sistema y por unos minutos no vas a poder entrar. Tus pedidos y tus datos estan a salvo.';
        $pantallas[503]['reintentar'] = 60;
    }

    $pantalla = $pantallas[$codigo] ?? $pantallas[500];
    $detalle  = $falla !== null && ini_get('display_errors') ? $falla->getMessage() : '';

    if (headers_sent()) {
        // La pagina ya empezo a dibujarse: se avisa dentro de ella.
        echo '<div class="alert alert-danger m-3" role="alert"><strong>' . e($pantalla['titulo']) . '.</strong> ' . e($pantalla['texto']) . '</div>';
        exit;
    }

    http_response_code($codigo);

    if (isset($pantalla['reintentar'])) {
        header('Retry-After: ' . $pantalla['reintentar']);
    }

    require __DIR__ . '/pantalla_error.php';
    exit;
}

function es_caida_de_servidor(Throwable $falla): bool
{
    if (!$falla instanceof PDOException) {
        return false;
    }

    // Codigos de MySQL/MariaDB que indican que la base no esta disponible.
    $codigo = (int) ($falla->errorInfo[1] ?? $falla->getCode());

    return in_array($codigo, [1040, 1045, 1049, 2002, 2003, 2005, 2006, 2013], true);
}

set_exception_handler(static function (Throwable $falla): void {
    error_log((string) $falla);
    mostrar_error(es_caida_de_servidor($falla) ? 503 : 500, $falla);
});
