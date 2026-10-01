<?php

define('BD_HOST', getenv('ATRAX_BD_HOST') ?: 'localhost');
define('BD_PUERTO', getenv('ATRAX_BD_PUERTO') ?: '3306');
define('BD_NOMBRE', getenv('ATRAX_BD_NOMBRE') ?: 'atrax');
define('BD_USUARIO', getenv('ATRAX_BD_USUARIO') ?: 'root');
define('BD_CLAVE', getenv('ATRAX_BD_CLAVE') ?: '');

define('APP_NOMBRE', 'ViandaSegura');
define('APP_RAIZ', dirname(__DIR__));

// Con ATRAX_MANTENIMIENTO=1 o con el archivo sistema/MANTENIMIENTO el sistema muestra la pantalla 503.
define('APP_MANTENIMIENTO', getenv('ATRAX_MANTENIMIENTO') === '1' || is_file(APP_RAIZ . '/MANTENIMIENTO'));
