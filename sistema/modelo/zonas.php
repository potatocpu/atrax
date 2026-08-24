<?php

require_once __DIR__ . '/../includes/conexion.php';

function listar_zonas(bool $soloActivas = false): array
{
    $sql = 'SELECT z.id, z.codigo, z.nombre, z.barrios, z.activa,
                   (SELECT COUNT(*) FROM vehiculos v WHERE v.zona_id = z.id) AS vehiculos,
                   (SELECT COUNT(*) FROM clientes c WHERE c.zona_id = z.id) AS clientes
            FROM zonas z';

    if ($soloActivas) {
        $sql .= ' WHERE z.activa = 1';
    }

    return consultar($sql . ' ORDER BY z.codigo');
}

function zona_por_id(int $id): ?array
{
    return consultarFila('SELECT id, codigo, nombre, barrios, activa FROM zonas WHERE id = ?', [$id]);
}

function existe_zona(string $campo, string $valor, int $excluirId = 0): bool
{
    $columna = $campo === 'codigo' ? 'codigo' : 'nombre';

    return consultarValor(
        "SELECT COUNT(*) FROM zonas WHERE $columna = ? AND id <> ?",
        [$valor, $excluirId],
        0
    ) > 0;
}

function crear_zona(string $codigo, string $nombre, string $barrios): int
{
    return insertar('INSERT INTO zonas (codigo, nombre, barrios) VALUES (?, ?, ?)', [$codigo, $nombre, $barrios]);
}

function actualizar_zona(int $id, string $codigo, string $nombre, string $barrios, int $activa): int
{
    return ejecutar(
        'UPDATE zonas SET codigo = ?, nombre = ?, barrios = ?, activa = ? WHERE id = ?',
        [$codigo, $nombre, $barrios, $activa, $id]
    );
}

function listar_vehiculos(): array
{
    return consultar(
        'SELECT v.id, v.nombre, v.matricula, v.capacidad, v.estado, v.zona_id,
                z.codigo AS zona_codigo, z.nombre AS zona,
                COALESCE((SELECT SUM(p.viandas) FROM pedidos p
                          WHERE p.vehiculo_id = v.id AND p.estado IN ("Listo", "En distribucion")), 0) AS carga
         FROM vehiculos v
         INNER JOIN zonas z ON z.id = v.zona_id
         ORDER BY v.nombre'
    );
}

function vehiculo_por_id(int $id): ?array
{
    return consultarFila(
        'SELECT v.id, v.nombre, v.matricula, v.capacidad, v.estado, v.zona_id,
                COALESCE((SELECT SUM(p.viandas) FROM pedidos p
                          WHERE p.vehiculo_id = v.id AND p.estado IN ("Listo", "En distribucion")), 0) AS carga
         FROM vehiculos v WHERE v.id = ?',
        [$id]
    );
}

function existe_matricula(string $matricula, int $excluirId = 0): bool
{
    return consultarValor(
        'SELECT COUNT(*) FROM vehiculos WHERE matricula = ? AND id <> ?',
        [$matricula, $excluirId],
        0
    ) > 0;
}

function crear_vehiculo(string $nombre, string $matricula, int $capacidad, int $zonaId, string $estado): int
{
    return insertar(
        'INSERT INTO vehiculos (nombre, matricula, capacidad, zona_id, estado) VALUES (?, ?, ?, ?, ?)',
        [$nombre, $matricula, $capacidad, $zonaId, $estado]
    );
}

function actualizar_vehiculo(int $id, string $nombre, string $matricula, int $capacidad, int $zonaId, string $estado): int
{
    return ejecutar(
        'UPDATE vehiculos SET nombre = ?, matricula = ?, capacidad = ?, zona_id = ?, estado = ? WHERE id = ?',
        [$nombre, $matricula, $capacidad, $zonaId, $estado, $id]
    );
}
