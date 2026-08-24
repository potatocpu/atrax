<?php

require_once __DIR__ . '/includes/auth.php';

cerrar_sesion();
session_start();
session_regenerate_id(true);
flash_guardar('exito', 'Cerraste sesion correctamente.');
redirigir('login.php');
