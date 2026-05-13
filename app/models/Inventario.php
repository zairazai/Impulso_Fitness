<?php

/*
|--------------------------------------------------------------------------
| MODELO INVENTARIO
|--------------------------------------------------------------------------
| Este modelo se encarga de comunicarse con la base de datos para el módulo
| de inventario.
| Todas las operaciones se hacen mediante procedimientos almacenados.
*/

require_once __DIR__ . '/../../config/database.php';

class Inventario
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR PRODUCTOS
    |--------------------------------------------------------------------------
    */
    public function listarProductos(string $busqueda = ''): array
    {
        $stmt = $this->conn->prepare("CALL sp_productos_listar(:busqueda)");
        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER PRODUCTO POR ID
    |--------------------------------------------------------------------------
    */
    public function obtenerProductoPorId(int $id): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_producto_obtener(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR PRODUCTO
    |--------------------------------------------------------------------------
    | Sirve tanto para agregar como para actualizar.
    */
    public function guardarProducto(
        int $id,
        string $codigo,
        string $nombre,
        string $categoria,
        string $descripcion,
        float $costoCompra,
        float $precioVenta,
        int $stock,
        int $stockMinimo,
        string $icono
    ): bool {
        try {
            $stmt = $this->conn->prepare("
                CALL sp_producto_guardar(
                    :id,
                    :codigo,
                    :nombre,
                    :categoria,
                    :descripcion,
                    :costo_compra,
                    :precio_venta,
                    :stock,
                    :stock_minimo,
                    :icono
                )
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':costo_compra', $costoCompra);
            $stmt->bindParam(':precio_venta', $precioVenta);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':stock_minimo', $stockMinimo, PDO::PARAM_INT);
            $stmt->bindParam(':icono', $icono, PDO::PARAM_STR);

            $ok = $stmt->execute();
            $stmt->closeCursor();

            return $ok;
        } catch (PDOException $e) {
            die('Error en guardarProducto: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BAJA LÓGICA DE PRODUCTO
    |--------------------------------------------------------------------------
    */
    public function bajaLogicaProducto(int $id): bool
    {
        $stmt = $this->conn->prepare("CALL sp_producto_baja_logica(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR MOVIMIENTO
    |--------------------------------------------------------------------------
    | Sirve para entrada, salida o ajuste de inventario.
    */
    public function registrarMovimiento(
        int $productoId,
        ?int $usuarioId,
        string $tipo,
        int $cantidad,
        string $referencia,
        string $observaciones
    ): bool {
        try {
            $stmt = $this->conn->prepare("
                CALL sp_inventario_registrar_movimiento(
                    :producto_id,
                    :usuario_id,
                    :tipo,
                    :cantidad,
                    :referencia,
                    :observaciones
                )
            ");

            $stmt->bindParam(':producto_id', $productoId, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
            $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);

            $ok = $stmt->execute();
            $stmt->closeCursor();

            return $ok;
        } catch (PDOException $e) {
            die('Error en registrarMovimiento: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR MOVIMIENTOS
    |--------------------------------------------------------------------------
    */
    public function listarMovimientos(string $busqueda = ''): array
    {
        $stmt = $this->conn->prepare("CALL sp_inventario_movimientos_listar(:busqueda)");
        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS CON STOCK BAJO
    |--------------------------------------------------------------------------
    */
    public function listarStockBajo(): array
    {
        $stmt = $this->conn->prepare("CALL sp_productos_stock_bajo()");
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }
}