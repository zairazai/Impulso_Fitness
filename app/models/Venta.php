<?php

require_once __DIR__ . '/../../config/database.php';

class Venta
{
    private PDO $conn;
    private ?string $lastError = null;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function listarProductosDisponibles(string $busqueda = ''): array
    {
        $sql = "SELECT id, codigo, nombre, categoria, precio_venta, stock, icono
                FROM productos
                WHERE activo = 1
                  AND ( :busqueda = ''
                        OR nombre LIKE CONCAT('%', :busqueda_like_1, '%')
                        OR codigo LIKE CONCAT('%', :busqueda_like_2, '%')
                        OR categoria LIKE CONCAT('%', :busqueda_like_3, '%') )
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_1', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_2', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_3', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function listarSocios(string $busqueda = ''): array
    {
        $this->actualizarEstadosMembresias();

        $sql = "SELECT id, CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno) AS nombre
                FROM socios
                WHERE estado = 'activo'
                  AND ( :busqueda = ''
                        OR nombres LIKE CONCAT('%', :busqueda_like_1, '%')
                        OR apellido_paterno LIKE CONCAT('%', :busqueda_like_2, '%')
                        OR apellido_materno LIKE CONCAT('%', :busqueda_like_3, '%')
                        OR email LIKE CONCAT('%', :busqueda_like_4, '%') )
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_1', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_2', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_3', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_4', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function actualizarEstadosMembresias(): void
    {
        $stmt = $this->conn->prepare("UPDATE socio_membresia SET activa = 0 WHERE DATE(fecha_fin) < CURDATE()");
        $stmt->execute();
        $stmt->closeCursor();

        $stmt = $this->conn->prepare(
            "UPDATE socios s
             SET s.estado = 'inactivo'
             WHERE s.estado <> 'suspendido'
               AND NOT EXISTS (
                   SELECT 1
                   FROM socio_membresia sm
                   WHERE sm.socio_id = s.id
                     AND sm.activa = 1
                     AND DATE(sm.fecha_inicio) <= CURDATE()
                     AND DATE(sm.fecha_fin) >= CURDATE()
               )"
        );
        $stmt->execute();
        $stmt->closeCursor();

        $stmt = $this->conn->prepare(
            "UPDATE socios s
             SET s.estado = 'activo'
             WHERE s.estado <> 'suspendido'
               AND EXISTS (
                   SELECT 1
                   FROM socio_membresia sm
                   WHERE sm.socio_id = s.id
                     AND sm.activa = 1
                     AND DATE(sm.fecha_inicio) <= CURDATE()
                     AND DATE(sm.fecha_fin) >= CURDATE()
               )"
        );
        $stmt->execute();
        $stmt->closeCursor();
    }

   public function registrarVenta(
    int $userId,
    ?int $socioId,
    float $total,
    string $metodoPago,
    array $items
): int|false {
    try {
        // Si no hay socio seleccionado, intentar obtener o crear un socio "Cliente general"
        if ($socioId === null || $socioId <= 0) {
            try {
                // Buscar un socio existente con el nombre exacto
                $stmtFind = $this->conn->prepare("SELECT id FROM socios WHERE nombres = :nombres LIMIT 1");
                $nombreGeneral = 'Cliente general';
                $stmtFind->bindValue(':nombres', $nombreGeneral, PDO::PARAM_STR);
                $stmtFind->execute();
                $found = $stmtFind->fetch(PDO::FETCH_ASSOC);
                $stmtFind->closeCursor();

                if ($found && !empty($found['id'])) {
                    $socioId = (int)$found['id'];
                } else {
                    // Usar el modelo Socio para insertar un registro básico y consistente
                    require_once __DIR__ . '/Socio.php';

                    $socioModel = new Socio();

                    $nuevoId = $socioModel->insertarSocioCompleto(
                        $nombreGeneral,
                        '',
                        '',
                        '',
                        '',
                        'cliente.general@local',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'Registro automático: cliente general'
                    );

                    if ($nuevoId > 0) {
                        $socioId = $nuevoId;
                    } else {
                        // No se pudo crear; dejar en null y continuar
                        $socioId = null;
                    }
                }
            } catch (Exception $e) {
                // Si falló la creación/búsqueda, no bloquear la venta
                $socioId = null;
            }
        }

        $this->conn->beginTransaction();

        $stmt = $this->conn->prepare(
            "INSERT INTO ventas (user_id, socio_id, total, metodo_pago)
             VALUES (:user_id, :socio_id, :total, :metodo_pago)"
        );

        if ($userId > 0) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
        }

        if ($socioId !== null && $socioId > 0) {
            $stmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':socio_id', null, PDO::PARAM_NULL);
        }

        $stmt->bindValue(':total', $total);
        $stmt->bindValue(':metodo_pago', $metodoPago, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        $ventaId = (int)$this->conn->lastInsertId();

        foreach ($items as $item) {
            $productoId = (int)($item['producto_id'] ?? 0);
            $cantidad = (int)($item['cantidad'] ?? 0);
            $precioUnitario = (float)($item['precio_unitario'] ?? 0);

            if ($productoId <= 0 || $cantidad <= 0 || $precioUnitario <= 0) {
                throw new Exception('Detalle de venta inválido.');
            }

            $stmtDetalle = $this->conn->prepare(
                "INSERT INTO venta_detalle (venta_id, producto_id, cantidad, precio_unitario)
                 VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)"
            );

            $stmtDetalle->bindValue(':venta_id', $ventaId, PDO::PARAM_INT);
            $stmtDetalle->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
            $stmtDetalle->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmtDetalle->bindValue(':precio_unitario', $precioUnitario);
            $stmtDetalle->execute();
            $stmtDetalle->closeCursor();

            $stmtMovimiento = $this->conn->prepare(
                "CALL sp_inventario_registrar_movimiento(
                    :producto_id,
                    :usuario_id,
                    :tipo,
                    :cantidad,
                    :referencia,
                    :observaciones
                )"
            );

            $tipo = 'salida';
            $referencia = 'Venta #' . $ventaId;
            $observaciones = 'Salida por venta';

            $stmtMovimiento->bindValue(':producto_id', $productoId, PDO::PARAM_INT);

            if ($userId > 0) {
                $stmtMovimiento->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
            } else {
                $stmtMovimiento->bindValue(':usuario_id', null, PDO::PARAM_NULL);
            }

            $stmtMovimiento->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $stmtMovimiento->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmtMovimiento->bindValue(':referencia', $referencia, PDO::PARAM_STR);
            $stmtMovimiento->bindValue(':observaciones', $observaciones, PDO::PARAM_STR);

            try {
                $stmtMovimiento->execute();
                $stmtMovimiento->closeCursor();
            } catch (PDOException $e) {
                throw new Exception('Stock insuficiente para realizar la venta.');
            }
        }

        $this->conn->commit();

        return $ventaId;
    } catch (Exception $e) {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }

        $this->lastError = $e->getMessage();
        return false;
    }
}

    public function historialVentas(
        string $busqueda = '',
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {
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
                    :busqueda = ''
                    OR v.id = :busqueda_int
                    OR u.username LIKE CONCAT('%', :busqueda_like_1, '%')
                    OR s.nombres LIKE CONCAT('%', :busqueda_like_2, '%')
                    OR s.apellido_paterno LIKE CONCAT('%', :busqueda_like_3, '%')
                    OR s.apellido_materno LIKE CONCAT('%', :busqueda_like_4, '%')
                )
                ";

        if ($fechaInicio !== null) {
            $sql .= " AND DATE(v.fecha) >= :fecha_inicio";
        }

        if ($fechaFin !== null) {
            $sql .= " AND DATE(v.fecha) <= :fecha_fin";
        }

        $sql .= " ORDER BY v.fecha DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_1', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_2', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_3', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_4', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_int', is_numeric($busqueda) ? (int)$busqueda : 0, PDO::PARAM_INT);

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

    public function exportarHistorialCompleto(
        string $busqueda = '',
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {
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
                    p.codigo,
                    vd.cantidad,
                    vd.precio_unitario,
                    (vd.cantidad * vd.precio_unitario) AS subtotal
                FROM ventas v
                LEFT JOIN users u ON u.id = v.user_id
                LEFT JOIN socios s ON s.id = v.socio_id
                LEFT JOIN venta_detalle vd ON vd.venta_id = v.id
                LEFT JOIN productos p ON p.id = vd.producto_id
                WHERE (
                    :busqueda = ''
                    OR v.id = :busqueda_int
                    OR u.username LIKE CONCAT('%', :busqueda_like_1, '%')
                    OR s.nombres LIKE CONCAT('%', :busqueda_like_2, '%')
                    OR s.apellido_paterno LIKE CONCAT('%', :busqueda_like_3, '%')
                    OR s.apellido_materno LIKE CONCAT('%', :busqueda_like_4, '%')
                    OR p.nombre LIKE CONCAT('%', :busqueda_like_5, '%')
                    OR p.codigo LIKE CONCAT('%', :busqueda_like_6, '%')
                )";

        if ($fechaInicio !== null) {
            $sql .= " AND DATE(v.fecha) >= :fecha_inicio";
        }

        if ($fechaFin !== null) {
            $sql .= " AND DATE(v.fecha) <= :fecha_fin";
        }

        $sql .= " ORDER BY v.fecha DESC, v.id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_1', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_2', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_3', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_4', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_5', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_like_6', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busqueda_int', is_numeric($busqueda) ? (int)$busqueda : 0, PDO::PARAM_INT);

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

    public function obtenerVentaRecibo(int $ventaId): ?array
    {
        $sql = "SELECT
                    v.id,
                    v.total,
                    v.metodo_pago,
                    v.fecha,
                    COALESCE(u.username, 'Sistema') AS usuario,
                    COALESCE(CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno), 'Cliente general') AS socio,
                    s.telefono,
                    s.email
                FROM ventas v
                LEFT JOIN users u ON u.id = v.user_id
                LEFT JOIN socios s ON s.id = v.socio_id
                WHERE v.id = :venta_id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':venta_id', $ventaId, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data ?: null;
    }

    public function obtenerDetalleVenta(int $ventaId): array
    {
        $sql = "SELECT
                    vd.cantidad,
                    vd.precio_unitario,
                    p.nombre AS producto,
                    p.codigo
                FROM venta_detalle vd
                LEFT JOIN productos p ON p.id = vd.producto_id
                WHERE vd.venta_id = :venta_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':venta_id', $ventaId, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }
}
