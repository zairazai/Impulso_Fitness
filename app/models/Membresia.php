<?php

/*
|--------------------------------------------------------------------------
| MODELO MEMBRESÍA
|--------------------------------------------------------------------------
| Este modelo se encarga de hablar con la base de datos para las operaciones
| del módulo de membresías.
|
| IMPORTANTE:
| Aquí ya no usamos consultas SELECT directas.
| Todas las consultas se hacen por medio de procedimientos almacenados.
*/

require_once __DIR__ . '/../../config/database.php';

class Membresia
{
    /*
    |--------------------------------------------------------------------------
    | CONEXIÓN PDO
    |--------------------------------------------------------------------------
    */
    private PDO $conn;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR MEMBRESÍAS
    |--------------------------------------------------------------------------
    | Devuelve las membresías disponibles.
    */
    public function listarMembresias(): array
    {
        $stmt = $this->conn->prepare("CALL sp_listar_membresias()");
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER MEMBRESÍA POR NOMBRE
    |--------------------------------------------------------------------------
    | Convierte el nombre del plan seleccionado en su id real.
    */
    public function obtenerMembresiaPorNombre(string $nombre): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_obtener_membresia_por_nombre(:nombre)");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR SOCIOS
    |--------------------------------------------------------------------------
    | Usa el procedimiento que ya tienes para buscar socios en membresías.
    | Si mandamos una cadena vacía, lista todos.
    */
    public function listarSocios(): array
    {
        $busqueda = '';

        $stmt = $this->conn->prepare("CALL sp_buscar_socios_para_membresia(:busqueda)");
        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER SOCIO POR ID
    |--------------------------------------------------------------------------
    | Recupera un socio específico.
    */
    public function obtenerSocioPorId(int $socioId): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_obtener_socio_por_id(:id)");
        $stmt->bindParam(':id', $socioId, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER MEMBRESÍA ACTIVA DEL SOCIO
    |--------------------------------------------------------------------------
    | Recupera la membresía más reciente del socio.
    */
    public function obtenerMembresiaActivaSocio(int $socioId): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_obtener_membresia_activa_socio(:socio_id)");
        $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR PAGO
    |--------------------------------------------------------------------------
    | Registra el pago de una membresía.
    | El procedimiento también activa o inactiva al socio según la fecha.
    */
    public function registrarPago(
        int $socioId,
        int $membresiaId,
        string $fechaInicio,
        float $monto,
        string $metodoPago
    ): bool {
        try {
            $stmt = $this->conn->prepare("
                CALL sp_registrar_pago(
                    :socio_id,
                    :membresia_id,
                    :fecha_inicio,
                    :monto,
                    :metodo_pago
                )
            ");

            $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
            $stmt->bindParam(':membresia_id', $membresiaId, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':metodo_pago', $metodoPago, PDO::PARAM_STR);

            $ok = $stmt->execute();
            $stmt->closeCursor();

            return $ok;
        } catch (PDOException $e) {
            die('Error en registrarPago: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE PAGOS
    |--------------------------------------------------------------------------
    | Consulta el historial de pagos desde procedimiento almacenado.
    */
    public function historialPagos(): array
    {
        $stmt = $this->conn->prepare("CALL sp_historial_pagos()");
        $stmt->execute();

        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER RECIBO DE MEMBRESÍA
    |--------------------------------------------------------------------------
    | Recupera la información completa de un pago para generar el recibo.
    */
    public function obtenerReciboMembresia(int $pagoId): array|false
{
    $stmt = $this->conn->prepare("CALL sp_obtener_recibo_membresia(:pago_id)");
    $stmt->bindParam(':pago_id', $pagoId, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetch();
    $stmt->closeCursor();

    return $data;
}

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ESTADOS DE MEMBRESÍAS
    |--------------------------------------------------------------------------
    | Este procedimiento:
    | - Marca como inactivas las membresías vencidas
    | - Activa socios con membresía vigente
    |
    | Se recomienda ejecutar:
    | - Antes de mostrar datos
    | - Después de registrar un pago
    */
    public function actualizarEstadosMembresias(): void
    {
        $stmt = $this->conn->prepare("CALL sp_actualizar_estados_membresias()");
        $stmt->execute();
        $stmt->closeCursor();
    }

}