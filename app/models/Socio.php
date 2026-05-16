<?php

/*
|--------------------------------------------------------------------------
| MODELO SOCIO
|--------------------------------------------------------------------------
| Este modelo se encarga únicamente de comunicarse con la base de datos
| mediante procedimientos almacenados.
|
| IMPORTANTE:
| - Las validaciones de campos obligatorios van en el controlador y JS.
| - Aquí solo llamamos procedures y retornamos resultados.
*/

require_once __DIR__ . '/../../config/database.php';

class Socio
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR SOCIOS
    |--------------------------------------------------------------------------
    */
    public function listarSocios(): array
    {
        $stmt = $this->conn->prepare("CALL sp_listar_socios()");
        $stmt->execute();

        $socios = $stmt->fetchAll();
        $stmt->closeCursor();

        return $socios;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER SOCIO POR ID
    |--------------------------------------------------------------------------
    */
    public function obtenerSocioPorId(int $id): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_obtener_socio_por_id(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $socio = $stmt->fetch();
        $stmt->closeCursor();

        return $socio;
    }

    /*
    |--------------------------------------------------------------------------
    | INSERTAR SOCIO COMPLETO
    |--------------------------------------------------------------------------
    | El socio se registra como INACTIVO desde la BD.
    | Solo se activará cuando confirme pago.
    */
    public function insertarSocioCompleto(
        string $nombres,
        string $apellidoPaterno,
        string $apellidoMaterno,
        string $fechaNacimiento,
        string $telefono,
        string $email,
        string $genero,
        string $contactoEmergenciaNombre,
        string $contactoEmergenciaTelefono,
        string $calle,
        string $numero,
        string $colonia,
        string $codigoPostal,
        string $notas
    ): int {
        $stmt = $this->conn->prepare("
            CALL sp_insertar_socio_completo(
                :nombres,
                :apellido_paterno,
                :apellido_materno,
                :fecha_nacimiento,
                :telefono,
                :email,
                :genero,
                :contacto_emergencia_nombre,
                :contacto_emergencia_telefono,
                :calle,
                :numero,
                :colonia,
                :codigo_postal,
                :notas
            )
        ");

        $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_paterno', $apellidoPaterno, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_materno', $apellidoMaterno, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);
        $stmt->bindParam(':contacto_emergencia_nombre', $contactoEmergenciaNombre, PDO::PARAM_STR);
        $stmt->bindParam(':contacto_emergencia_telefono', $contactoEmergenciaTelefono, PDO::PARAM_STR);
        $stmt->bindParam(':calle', $calle, PDO::PARAM_STR);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam(':colonia', $colonia, PDO::PARAM_STR);
        $stmt->bindParam(':codigo_postal', $codigoPostal, PDO::PARAM_STR);
        $stmt->bindParam(':notas', $notas, PDO::PARAM_STR);

        $stmt->execute();

        $resultado = $stmt->fetch();
        $stmt->closeCursor();

        return isset($resultado['id']) ? (int)$resultado['id'] : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR SOCIO COMPLETO
    |--------------------------------------------------------------------------
    | No actualizamos estado aquí.
    | El estado se controla con pagos o baja lógica.
    */
    public function actualizarSocioCompleto(
        int $id,
        string $nombres,
        string $apellidoPaterno,
        string $apellidoMaterno,
        string $fechaNacimiento,
        string $telefono,
        string $email,
        string $genero,
        string $contactoEmergenciaNombre,
        string $contactoEmergenciaTelefono,
        string $calle,
        string $numero,
        string $colonia,
        string $codigoPostal,
        string $notas
    ): bool {
        $stmt = $this->conn->prepare("
            CALL sp_actualizar_socio_completo(
                :id,
                :nombres,
                :apellido_paterno,
                :apellido_materno,
                :fecha_nacimiento,
                :telefono,
                :email,
                :genero,
                :contacto_emergencia_nombre,
                :contacto_emergencia_telefono,
                :calle,
                :numero,
                :colonia,
                :codigo_postal,
                :notas
            )
        ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_paterno', $apellidoPaterno, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_materno', $apellidoMaterno, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);
        $stmt->bindParam(':contacto_emergencia_nombre', $contactoEmergenciaNombre, PDO::PARAM_STR);
        $stmt->bindParam(':contacto_emergencia_telefono', $contactoEmergenciaTelefono, PDO::PARAM_STR);
        $stmt->bindParam(':calle', $calle, PDO::PARAM_STR);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam(':colonia', $colonia, PDO::PARAM_STR);
        $stmt->bindParam(':codigo_postal', $codigoPostal, PDO::PARAM_STR);
        $stmt->bindParam(':notas', $notas, PDO::PARAM_STR);

        $resultado = $stmt->execute();
        $stmt->closeCursor();

        return $resultado;
    }

    /*
    |--------------------------------------------------------------------------
    | DESACTIVAR SOCIO
    |--------------------------------------------------------------------------
    | Baja lógica: cambia estado a inactivo.
    */
    public function desactivarSocio(int $id): bool
    {
        $stmt = $this->conn->prepare("CALL sp_desactivar_socio(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $resultado = $stmt->execute();
        $stmt->closeCursor();

        return $resultado;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR HUELLA
    |--------------------------------------------------------------------------
    */
    public function registrarHuella(int $socioId, string $huellaHash): bool
    {
        $stmt = $this->conn->prepare("
            CALL sp_registrar_huella_socio(:socio_id, :huella_hash)
        ");

        $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
        $stmt->bindParam(':huella_hash', $huellaHash, PDO::PARAM_STR);

        $resultado = $stmt->execute();
        $stmt->closeCursor();

        return $resultado;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER HUELLA POR SOCIO
    |--------------------------------------------------------------------------
    */
    public function obtenerHuellaPorSocio(int $socioId): array|false
    {
        $stmt = $this->conn->prepare("CALL sp_obtener_huella_socio(:socio_id)");
        $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
        $stmt->execute();

        $huella = $stmt->fetch();
        $stmt->closeCursor();

        return $huella;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER MEMBRESIA ACTIVA SOCIO 
    |--------------------------------------------------------------------------
    */

    public function obtenerMembresiaActivaSocio(int $socioId): ?array
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_obtener_membresia_activa_socio(:socio_id)");
            $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $data ?: null;
        } catch (PDOException $e) {
            die('Error en obtenerMembresiaActivaSocio: ' . $e->getMessage());
        }
    }

     /*
    |--------------------------------------------------------------------------
    | OBTENER HISTORIAL RECIENTE DE LOS PAGOS DE SOCIOS 
    |--------------------------------------------------------------------------
    */


    public function obtenerHistorialRecientePagosSocio(int $socioId): array
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_obtener_historial_reciente_pagos_socio(:socio_id)");
            $stmt->bindParam(':socio_id', $socioId, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $data;
        } catch (PDOException $e) {
            die('Error en obtenerHistorialRecientePagosSocio: ' . $e->getMessage());
        }
    }
}