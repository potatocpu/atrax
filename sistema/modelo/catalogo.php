<?php

require_once __DIR__ . '/../includes/conexion.php';

function listar_productos(bool $soloActivos = false): array
{
    $sql = 'SELECT id, nombre, descripcion, precio, stock_minimo, activo FROM productos';

    if ($soloActivos) {
        $sql .= ' WHERE activo = 1';
    }

    return consultar($sql . ' ORDER BY nombre');
}

function producto_por_id(int $id): ?array
{
    return consultarFila(
        'SELECT id, nombre, descripcion, precio, stock_minimo, activo FROM productos WHERE id = ?',
        [$id]
    );
}

function existe_producto(string $nombre, int $excluirId = 0): bool
{
    return consultarValor(
        'SELECT COUNT(*) FROM productos WHERE nombre = ? AND id <> ?',
        [$nombre, $excluirId],
        0
    ) > 0;
}

function crear_producto(string $nombre, string $descripcion, float $precio, int $stockMinimo): int
{
    return insertar(
        'INSERT INTO productos (nombre, descripcion, precio, stock_minimo) VALUES (?, ?, ?, ?)',
        [$nombre, $descripcion, $precio, $stockMinimo]
    );
}

function actualizar_producto(int $id, float $precio, int $activo): int
{
    return ejecutar('UPDATE productos SET precio = ?, activo = ? WHERE id = ?', [$precio, $activo, $id]);
}

function actualizar_stock_minimo(int $id, int $stockMinimo): int
{
    return ejecutar('UPDATE productos SET stock_minimo = ? WHERE id = ?', [$stockMinimo, $id]);
}

function listar_planes(bool $soloActivos = false): array
{
    $sql = 'SELECT id, nombre, descripcion, viandas, activo FROM planes';

    if ($soloActivos) {
        $sql .= ' WHERE activo = 1';
    }

    return consultar($sql . ' ORDER BY viandas');
}

function plan_por_id(int $id): ?array
{
    return consultarFila('SELECT id, nombre, descripcion, viandas, activo FROM planes WHERE id = ?', [$id]);
}

function actualizar_plan(int $id, int $viandas, int $activo): int
{
    return ejecutar('UPDATE planes SET viandas = ?, activo = ? WHERE id = ?', [$viandas, $activo, $id]);
}
