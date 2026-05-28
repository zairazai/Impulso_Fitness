<?php

require_once __DIR__ . '/../../config/database.php';

class Reporte
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function obtenerResumenGeneral(): array
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM socios) AS total_socios,
                    (SELECT COUNT(*) FROM socios WHERE estado = 'activo') AS socios_activos,
                    (SELECT COUNT(*) FROM productos WHERE activo = 1) AS productos_activos,
                    COALESCE((SELECT SUM(stock) FROM productos WHERE activo = 1), 0) AS stock_total,
                    (SELECT COUNT(*) FROM ventas) AS total_ventas,
                    COALESCE((SELECT SUM(total) FROM ventas), 0) AS ventas_totales,
                    (SELECT COUNT(*) FROM pagos_membresia) AS pagos_membresia,
                    COALESCE((SELECT SUM(monto) FROM pagos_membresia), 0) AS ingresos_membresia
                ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data ?: [];
    }

    public function obtenerResumenMembresias(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null, string $estado = ''): array
    {
        $sql = "SELECT
                    COUNT(pm.id) AS total_pagos,
                    COALESCE(SUM(pm.monto), 0) AS total_ingresos,
                    COALESCE(SUM(CASE WHEN DATE_FORMAT(pm.fecha_pago, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') THEN pm.monto ELSE 0 END), 0) AS ingresos_mes,
                    COALESCE(SUM(CASE WHEN DATE_FORMAT(pm.fecha_pago, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') THEN 1 ELSE 0 END), 0) AS pagos_mes
                FROM pagos_membresia pm
                LEFT JOIN socios s ON s.id = pm.socio_id
                INNER JOIN membresias m ON m.id = pm.membresia_id
                WHERE 1=1";

        $params = [];

        if ($busqueda !== '') {
            $sql .= " AND (
                        pm.referencia LIKE :busqueda_referencia
                        OR m.nombre LIKE :busqueda_membresia
                        OR COALESCE(s.nombres, '') LIKE :busqueda_socio
                        OR COALESCE(s.email, '') LIKE :busqueda_email
                    )";
            $params[':busqueda_referencia'] = '%' . $busqueda . '%';
            $params[':busqueda_membresia'] = '%' . $busqueda . '%';
            $params[':busqueda_socio'] = '%' . $busqueda . '%';
            $params[':busqueda_email'] = '%' . $busqueda . '%';
        }

        if ($estado !== '') {
            $sql .= ' AND s.estado = :estado';
            $params[':estado'] = $estado;
        }

        if ($fechaInicio !== null) {
            $sql .= ' AND DATE(pm.fecha_pago) >= :fecha_inicio';
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin !== null) {
            $sql .= ' AND DATE(pm.fecha_pago) <= :fecha_fin';
            $params[':fecha_fin'] = $fechaFin;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $data = $data ?: [
            'total_pagos' => 0,
            'total_ingresos' => 0,
            'ingresos_mes' => 0,
            'pagos_mes' => 0,
        ];

        $data['membresias_activas'] = $this->contarMembresiasActivas($estado);
        $data['membresias_inactivas'] = $this->contarMembresiasInactivas($estado);

        return $data;
    }

    private function contarMembresiasActivas(string $estado = ''): int
    {
        $sql = "SELECT COUNT(*) FROM socio_membresia sm
                INNER JOIN socios s ON s.id = sm.socio_id
                WHERE sm.activa = 1
                  AND sm.fecha_inicio <= NOW()
                  AND sm.fecha_fin >= NOW()";

        if ($estado !== '') {
            $sql .= ' AND s.estado = :estado';
        }

        $stmt = $this->conn->prepare($sql);

        if ($estado !== '') {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->execute();
        $count = (int)$stmt->fetchColumn();
        $stmt->closeCursor();

        return $count;
    }

    private function contarMembresiasInactivas(string $estado = ''): int
    {
        $sql = "SELECT COUNT(*) FROM socio_membresia sm
                INNER JOIN socios s ON s.id = sm.socio_id
                WHERE (sm.activa = 0 OR sm.fecha_fin < NOW())";

        if ($estado !== '') {
            $sql .= ' AND s.estado = :estado';
        }

        $stmt = $this->conn->prepare($sql);

        if ($estado !== '') {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->execute();
        $count = (int)$stmt->fetchColumn();
        $stmt->closeCursor();

        return $count;
    }

    public function obtenerPagosMembresia(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null, string $estado = ''): array
    {
        $sql = "SELECT
                    pm.id,
                    COALESCE(CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno), 'Cliente general') AS socio,
                    m.nombre AS membresia,
                    pm.monto,
                    pm.metodo_pago,
                    pm.fecha_pago,
                    pm.referencia,
                    COALESCE(s.estado, '') AS estado_socio
                FROM pagos_membresia pm
                LEFT JOIN socios s ON s.id = pm.socio_id
                INNER JOIN membresias m ON m.id = pm.membresia_id
                WHERE 1=1";

        $params = [];

        if ($busqueda !== '') {
            $sql .= " AND (
                pm.referencia LIKE :busqueda_ref
                OR m.nombre LIKE :busqueda_membresia
                OR COALESCE(s.nombres, '') LIKE :busqueda_socio
                OR COALESCE(s.email, '') LIKE :busqueda_email
            )";
            $params[':busqueda_ref'] = '%' . $busqueda . '%';
            $params[':busqueda_membresia'] = '%' . $busqueda . '%';
            $params[':busqueda_socio'] = '%' . $busqueda . '%';
            $params[':busqueda_email'] = '%' . $busqueda . '%';
        }

        if ($estado !== '') {
            $sql .= " AND s.estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($fechaInicio !== null) {
            $sql .= " AND DATE(pm.fecha_pago) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin !== null) {
            $sql .= " AND DATE(pm.fecha_pago) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $sql .= " ORDER BY pm.fecha_pago DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function obtenerResumenInventario(): array
    {
        $sql = "SELECT
                    COUNT(*) AS productos_activos,
                    COALESCE(SUM(stock), 0) AS stock_total,
                    COALESCE(SUM(stock * costo_compra), 0) AS valor_inventario,
                    COUNT(CASE WHEN stock <= stock_minimo THEN 1 END) AS productos_stock_bajo,
                    COUNT(CASE WHEN stock = 0 THEN 1 END) AS productos_agotados
                FROM productos
                WHERE activo = 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data ?: [];
    }

    public function obtenerProductosInventario(string $busqueda = ''): array
    {
        $sql = "SELECT
                    id,
                    codigo,
                    nombre,
                    categoria,
                    stock,
                    stock_minimo,
                    precio_venta,
                    costo_compra
                FROM productos
                WHERE activo = 1
                  AND (
                        :busqueda_main = ''
                        OR nombre LIKE CONCAT('%', :busqueda_nombre, '%')
                        OR codigo LIKE CONCAT('%', :busqueda_codigo, '%')
                        OR categoria LIKE CONCAT('%', :busqueda_categoria, '%')
                    )
                ORDER BY stock ASC, nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda_main', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_nombre', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_codigo', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_categoria', $busqueda, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function exportarPagosMembresia(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null, string $estado = ''): array
    {
        return $this->obtenerPagosMembresia($busqueda, $fechaInicio, $fechaFin, $estado);
    }

    public function exportarProductosInventario(string $busqueda = ''): array
    {
        return $this->obtenerProductosInventario($busqueda);
    }

    public function obtenerVentas(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT
                    v.id,
                    v.total,
                    v.metodo_pago,
                    v.fecha,
                    COALESCE(u.username, 'Sistema') AS usuario,
                    COALESCE(CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno), 'Cliente general') AS socio
                FROM ventas v
                LEFT JOIN users u ON u.id = v.user_id
                LEFT JOIN socios s ON s.id = v.socio_id
                WHERE (
                    :busqueda_main = ''
                    OR v.id = :busqueda_int
                    OR u.username LIKE CONCAT('%', :busqueda_username, '%')
                    OR COALESCE(s.nombres, '') LIKE CONCAT('%', :busqueda_socio, '%')
                    OR v.metodo_pago LIKE CONCAT('%', :busqueda_pago, '%')
                )";

        if ($fechaInicio !== null) {
            $sql .= " AND DATE(v.fecha) >= :fecha_inicio";
        }

        if ($fechaFin !== null) {
            $sql .= " AND DATE(v.fecha) <= :fecha_fin";
        }

        $sql .= " ORDER BY v.fecha DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda_main', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_int', is_numeric($busqueda) ? (int)$busqueda : 0, PDO::PARAM_INT);
        $stmt->bindValue(':busqueda_username', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_socio', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_pago', $busqueda, PDO::PARAM_STR);

        if ($fechaInicio !== null) {
            $stmt->bindValue(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
        }

        if ($fechaFin !== null) {
            $stmt->bindValue(':fecha_fin', $fechaFin, PDO::PARAM_STR);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function exportarVentas(string $busqueda = '', ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT
                    v.id AS venta_id,
                    v.fecha,
                    COALESCE(CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno), 'Cliente general') AS socio,
                    s.telefono,
                    s.email,
                    COALESCE(u.username, 'Sistema') AS usuario,
                    v.metodo_pago,
                    v.total,
                    p.nombre AS producto,
                    p.codigo AS codigo,
                    vd.cantidad,
                    vd.precio_unitario,
                    (vd.cantidad * vd.precio_unitario) AS subtotal
                FROM ventas v
                LEFT JOIN users u ON u.id = v.user_id
                LEFT JOIN socios s ON s.id = v.socio_id
                LEFT JOIN venta_detalle vd ON vd.venta_id = v.id
                LEFT JOIN productos p ON p.id = vd.producto_id
                WHERE (
                    :busqueda_main = ''
                    OR v.id = :busqueda_int
                    OR u.username LIKE CONCAT('%', :busqueda_username, '%')
                    OR COALESCE(s.nombres, '') LIKE CONCAT('%', :busqueda_socio, '%')
                    OR v.metodo_pago LIKE CONCAT('%', :busqueda_pago, '%')
                    OR p.nombre LIKE CONCAT('%', :busqueda_producto, '%')
                    OR p.codigo LIKE CONCAT('%', :busqueda_codigo, '%')
                )";

        if ($fechaInicio !== null) {
            $sql .= " AND DATE(v.fecha) >= :fecha_inicio";
        }

        if ($fechaFin !== null) {
            $sql .= " AND DATE(v.fecha) <= :fecha_fin";
        }

        $sql .= " ORDER BY v.fecha DESC, v.id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda_main', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_int', is_numeric($busqueda) ? (int)$busqueda : 0, PDO::PARAM_INT);
        $stmt->bindValue(':busqueda_username', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_socio', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_pago', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_producto', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_codigo', $busqueda, PDO::PARAM_STR);

        if ($fechaInicio !== null) {
            $stmt->bindValue(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
        }

        if ($fechaFin !== null) {
            $stmt->bindValue(':fecha_fin', $fechaFin, PDO::PARAM_STR);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }
}
