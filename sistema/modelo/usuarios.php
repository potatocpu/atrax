<?php

require_once __DIR__ . '/../includes/conexion.php';

function usuario_por_acceso(string $identificador): ?array
{
    return consultarFila(
        'SELECT u.id, u.nombre, u.usuario, u.email, u.contrasena, u.activo, r.nombre AS rol
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.usuario = ? OR u.email = ?
         LIMIT 1',
        [$identificador, $identificador]
    );
}

function usuario_por_id(int $id): ?array
{
    return consultarFila(
        'SELECT u.id, u.nombre, u.usuario, u.email, u.telefono, u.activo, u.rol_id, r.nombre AS rol
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.id = ?',
        [$id]
    );
}

function existe_usuario(string $usuario): bool
{
    return consultarValor('SELECT COUNT(*) FROM usuarios WHERE usuario = ?', [$usuario], 0) > 0;
}

function existe_email(string $email): bool
{
    return consultarValor('SELECT COUNT(*) FROM usuarios WHERE email = ?', [$email], 0) > 0;
}

function rol_id(string $nombre): ?int
{
    $id = consultarValor('SELECT id FROM roles WHERE nombre = ?', [$nombre]);

    return $id === null ? null : (int) $id;
}

function listar_roles(): array
{
    return consultar('SELECT id, nombre, descripcion FROM roles ORDER BY id');
}

function crear_usuario(int $rolId, string $nombre, string $usuario, string $email, string $telefono, string $contrasena): int
{
    return insertar(
        'INSERT INTO usuarios (rol_id, nombre, usuario, email, telefono, contrasena)
         VALUES (?, ?, ?, ?, ?, ?)',
        [$rolId, $nombre, $usuario, $email, $telefono, password_hash($contrasena, PASSWORD_DEFAULT)]
    );
}

function crear_cliente(int $usuarioId, int $zonaId, string $tipo, string $direccion): int
{
    return insertar(
        'INSERT INTO clientes (usuario_id, zona_id, tipo, direccion) VALUES (?, ?, ?, ?)',
        [$usuarioId, $zonaId, $tipo, $direccion]
    );
}

function listar_usuarios(): array
{
    return consultar(
        'SELECT u.id, u.nombre, u.usuario, u.email, u.telefono, u.activo, u.fecha_registro, u.ultimo_login,
                r.nombre AS rol, z.nombre AS zona, c.tipo
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         LEFT JOIN clientes c ON c.usuario_id = u.id
         LEFT JOIN zonas z ON z.id = c.zona_id
         ORDER BY r.id, u.nombre'
    );
}

function cambiar_estado_usuario(int $id, int $activo): int
{
    return ejecutar('UPDATE usuarios SET activo = ? WHERE id = ?', [$activo, $id]);
}

function cliente_de_usuario(int $usuarioId): ?array
{
    return consultarFila(
        'SELECT c.id, c.tipo, c.direccion, c.zona_id, z.nombre AS zona, z.codigo
         FROM clientes c
         INNER JOIN zonas z ON z.id = c.zona_id
         WHERE c.usuario_id = ?',
        [$usuarioId]
    );
}

function cliente_por_id(int $id): ?array
{
    return consultarFila(
        'SELECT c.id, c.tipo, c.direccion, c.zona_id, z.nombre AS zona, z.codigo, u.nombre, u.email
         FROM clientes c
         INNER JOIN zonas z ON z.id = c.zona_id
         INNER JOIN usuarios u ON u.id = c.usuario_id
         WHERE c.id = ?',
        [$id]
    );
}
