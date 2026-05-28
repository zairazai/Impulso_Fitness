<?php

require_once __DIR__ . '/../../config/database.php';

class Acceso
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function obtenerAccesos(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null, string $resultado = ''): array
    {
        $sql = "SELECT
                    a.id,
                    COALESCE(CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno), 'Socio no registrado') AS socio,
                    COALESCE(s.estado, 'desconocido') AS estado_socio,
                    m.nombre AS membresia,
                    a.resultado,
                    a.motivo,
                    a.fecha_hora
                FROM accesos a
                LEFT JOIN socios s ON s.id = a.socio_id
                LEFT JOIN membresias m ON m.id = a.membresia_id
                WHERE 1=1";

        $params = [];

        if ($busqueda !== '') {
            $sql .= " AND (
                        a.id = :buscar_int
                        OR COALESCE(s.nombres, '') LIKE :buscar_nombres
                        OR COALESCE(s.apellido_paterno, '') LIKE :buscar_ap
                        OR COALESCE(s.apellido_materno, '') LIKE :buscar_am
                        OR COALESCE(s.email, '') LIKE :buscar_email
                        OR COALESCE(m.nombre, '') LIKE :buscar_membresia
                        OR a.resultado LIKE :buscar_resultado
                    )";
            $params[':buscar_int'] = is_numeric($busqueda) ? (int)$busqueda : 0;
            $like = '%' . $busqueda . '%';
            $params[':buscar_nombres'] = $like;
            $params[':buscar_ap'] = $like;
            $params[':buscar_am'] = $like;
            $params[':buscar_email'] = $like;
            $params[':buscar_membresia'] = $like;
            $params[':buscar_resultado'] = $like;
        }

        if ($resultado !== '') {
            $sql .= ' AND a.resultado = :resultado';
            $params[':resultado'] = $resultado;
        }

        if ($fechaInicio !== null) {
            $sql .= ' AND DATE(a.fecha_hora) >= :fecha_inicio';
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin !== null) {
            $sql .= ' AND DATE(a.fecha_hora) <= :fecha_fin';
            $params[':fecha_fin'] = $fechaFin;
        }

        $sql .= ' ORDER BY a.fecha_hora DESC';

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        $accesos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $accesos;
    }

    public function obtenerSocios(string $estado = ''): array
    {
        $sql = "SELECT id, nombres, apellido_paterno, apellido_materno, estado FROM socios";

        if ($estado !== '') {
            $sql .= ' WHERE estado = :estado';
        }

        $sql .= ' ORDER BY nombres ASC, apellido_paterno ASC, apellido_materno ASC';

        $stmt = $this->conn->prepare($sql);

        if ($estado !== '') {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->execute();
        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $socios;
    }

    public function buscarSocios(string $query): array
    {
        if ($query === '') {
            return [];
        }

        $sql = "SELECT
                    s.id,
                    s.nombres,
                    s.apellido_paterno,
                    s.apellido_materno,
                    CASE
                        WHEN s.estado = 'suspendido' THEN 'suspendido'
                        WHEN sm.id IS NOT NULL THEN 'activo'
                        ELSE COALESCE(s.estado, 'inactivo')
                    END AS estado,
                    COALESCE(m.nombre, 'Sin membresía activa') AS membresia,
                    sm.fecha_inicio,
                    sm.fecha_fin,
                    m.precio AS membresia_precio,
                    (SELECT pm.fecha_pago FROM pagos_membresia pm WHERE pm.socio_id = s.id ORDER BY pm.fecha_pago DESC LIMIT 1) AS ultimo_pago_fecha,
                    (SELECT pm.monto FROM pagos_membresia pm WHERE pm.socio_id = s.id ORDER BY pm.fecha_pago DESC LIMIT 1) AS ultimo_pago_monto
                FROM socios s
                LEFT JOIN socio_membresia sm ON sm.socio_id = s.id
                    AND sm.activa = 1
                    AND DATE(sm.fecha_fin) >= CURDATE()
                LEFT JOIN membresias m ON m.id = sm.membresia_id
                WHERE CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno) LIKE :q_name
                   OR s.email LIKE :q_email
                   OR s.telefono LIKE :q_tel
                ORDER BY s.nombres ASC, s.apellido_paterno ASC, s.apellido_materno ASC
                LIMIT 20";

        $stmt = $this->conn->prepare($sql);
        $like = '%' . $query . '%';
        $stmt->bindValue(':q_name', $like, PDO::PARAM_STR);
        $stmt->bindValue(':q_email', $like, PDO::PARAM_STR);
        $stmt->bindValue(':q_tel', $like, PDO::PARAM_STR);
        $stmt->execute();
        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $socios;
    }

    public function obtenerSocioConMembresiaActiva(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT
                    s.id,
                    s.nombres,
                    s.apellido_paterno,
                    s.apellido_materno,
                    CASE
                        WHEN s.estado = "suspendido" THEN "suspendido"
                        WHEN sm.id IS NOT NULL THEN "activo"
                        ELSE COALESCE(s.estado, "inactivo")
                    END AS estado,
                    COALESCE(m.nombre, NULL) AS membresia,
                    sm.fecha_inicio,
                    sm.fecha_fin,
                    m.precio AS membresia_precio,
                    (SELECT pm.fecha_pago FROM pagos_membresia pm WHERE pm.socio_id = s.id ORDER BY pm.fecha_pago DESC LIMIT 1) AS ultimo_pago_fecha,
                    (SELECT pm.monto FROM pagos_membresia pm WHERE pm.socio_id = s.id ORDER BY pm.fecha_pago DESC LIMIT 1) AS ultimo_pago_monto
                FROM socios s
                LEFT JOIN socio_membresia sm ON sm.socio_id = s.id
                    AND sm.activa = 1
                    AND DATE(sm.fecha_fin) >= CURDATE()
                LEFT JOIN membresias m ON m.id = sm.membresia_id
                WHERE s.id = :id
                LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $socio = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $socio ?: null;
    }

    public function obtenerSocioPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT
                s.id,
                s.nombres,
                s.apellido_paterno,
                s.apellido_materno,
                CASE
                    WHEN s.estado = "suspendido" THEN "suspendido"
                    WHEN EXISTS (
                        SELECT 1
                        FROM socio_membresia sm
                        WHERE sm.socio_id = s.id
                          AND sm.activa = 1
                          AND DATE(sm.fecha_inicio) <= CURDATE()
                          AND DATE(sm.fecha_fin) >= CURDATE()
                    ) THEN "activo"
                    ELSE "inactivo"
                END AS estado
            FROM socios s
            WHERE s.id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $socio = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $socio ?: null;
    }

    public function obtenerMembresiaActivaSocio(int $socioId): ?int
    {
        $stmt = $this->conn->prepare(
            'SELECT membresia_id FROM socio_membresia
             WHERE socio_id = :socio_id
               AND activa = 1
               AND fecha_inicio <= NOW()
               AND fecha_fin >= NOW()
             ORDER BY fecha_fin DESC
             LIMIT 1'
        );
        $stmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);
        $stmt->execute();
        $membresia = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $membresia['membresia_id'] ?? null;
    }

    public function registrarAcceso(int $socioId, ?int $membresiaId, string $resultado, ?string $motivo): bool
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO accesos (socio_id, membresia_id, resultado, motivo) VALUES (:socio_id, :membresia_id, :resultado, :motivo)'
        );
        $stmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);

        if ($membresiaId === null) {
            $stmt->bindValue(':membresia_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':membresia_id', $membresiaId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':resultado', $resultado, PDO::PARAM_STR);
        $stmt->bindValue(':motivo', $motivo, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
