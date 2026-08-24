<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../modelo/usuarios.php';

solo_post();
validar_token();

$identificador = campo('usuario');
$contrasena    = (string) ($_POST['contrasena'] ?? '');
$errores       = [];

if ($identificador === '') {
    $errores[] = 'Ingresa tu usuario o correo.';
}

if ($contrasena === '') {
    $errores[] = 'Ingresa tu contrasena.';
}

if ($errores) {
    volver_con_errores('login.php', $errores, ['usuario' => $identificador]);
}

$usuario = usuario_por_acceso($identificador);

if ($usuario === null || !password_verify($contrasena, $usuario['contrasena'])) {
    volver_con_errores('login.php', ['Usuario o contrasena incorrectos.'], ['usuario' => $identificador]);
}

if ((int) $usuario['activo'] !== 1) {
    volver_con_errores('login.php', ['La cuenta esta inactiva. Consulta con el administrador.'], ['usuario' => $identificador]);
}

abrir_sesion($usuario);
redirigir(panel_de($usuario['rol']));
