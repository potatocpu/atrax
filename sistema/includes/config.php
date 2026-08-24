<?php

define('BD_HOST', getenv('ATRAX_BD_HOST') ?: 'localhost');
define('BD_PUERTO', getenv('ATRAX_BD_PUERTO') ?: '3306');
define('BD_NOMBRE', getenv('ATRAX_BD_NOMBRE') ?: 'atrax');
define('BD_USUARIO', getenv('ATRAX_BD_USUARIO') ?: 'root');
define('BD_CLAVE', getenv('ATRAX_BD_CLAVE') ?: '');

define('APP_NOMBRE', 'Atrax');
define('APP_RAIZ', dirname(__DIR__));
