<?php

/*
|--------------------------------------------------------------------------
| CONTROLADOR DE MEMBRESÍAS
|--------------------------------------------------------------------------
| Este archivo se encarga de manejar la lógica del módulo de membresías.
| Aquí procesamos:
| - registro de pago
| - validaciones
| - redirecciones
|
| NO se hacen consultas directas aquí → eso lo hace el modelo.
*/

require_once __DIR__ . '/../models/Membresia.php';
require_once __DIR__ . '/../../config/app.php';

class MembresiaController
{
    /*
    |--------------------------------------------------------------------------
    | RUTAS DE REDIRECCIÓN
    |--------------------------------------------------------------------------
    | Definimos rutas base para no repetir strings en todo el código.
    */
    private const LOGIN_VIEW = BASE_URL . '/resources/views/auth/login.php';
    private const MEMBRESIAS_INDEX_VIEW = BASE_URL . '/resources/views/membresias/index.php';
    private const SOCIOS_INDEX_VIEW = BASE_URL . '/resources/views/socios/index.php';

    /*
    |--------------------------------------------------------------------------
    | VALIDAR SESIÓN
    |--------------------------------------------------------------------------
    | Verifica que el usuario haya iniciado sesión antes de ejecutar cualquier acción.
    */
    private function validarSesion(): void
    {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si no hay usuario, redirigir al login
        if (!isset($_SESSION['user'])) {
            header("Location: " . self::LOGIN_VIEW);
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (REGISTRAR MEMBRESÍA / PAGO)
    |--------------------------------------------------------------------------
    | Este método:
    | 1. Recibe datos del formulario
    | 2. Valida datos
    | 3. Consulta el modelo
    | 4. Registra el pago
    | 5. Actualiza estados de membresías/socios
    | 6. Redirige según resultado
    */
    public function store(): void
    {
        // Validar sesión
        $this->validarSesion();

        // Validar que sea método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::MEMBRESIAS_INDEX_VIEW);
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER DATOS DEL FORMULARIO
        |--------------------------------------------------------------------------
        */
        $socioId = (int)($_POST['socio_id'] ?? 0);
        $planSeleccionado = trim($_POST['plan_seleccionado'] ?? '');
        $fechaInicioInput = trim($_POST['fecha_inicio'] ?? '');
        $total = (float)($_POST['total'] ?? 0);
        $metodoPago = trim($_POST['metodo_pago'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR FECHA DE INICIO
        |--------------------------------------------------------------------------
        | El input datetime-local llega así:
        | 2026-04-27T19:30
        |
        | MySQL DATETIME espera:
        | 2026-04-27 19:30:00
        */
        $fechaInicio = '';

        if ($fechaInicioInput !== '') {
            $fechaInicio = str_replace('T', ' ', $fechaInicioInput);

            // Si viene sin segundos, se agregan para que coincida con DATETIME.
            if (strlen($fechaInicio) === 16) {
                $fechaInicio .= ':00';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN BÁSICA
        |--------------------------------------------------------------------------
        | Verificamos que los datos obligatorios estén presentes.
        */
        if (
            $socioId <= 0 ||
            $planSeleccionado === '' ||
            $fechaInicio === '' ||
            $total <= 0 ||
            $metodoPago === ''
        ) {
            // Redirigir con error
            header("Location: " . self::MEMBRESIAS_INDEX_VIEW .
                "?id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=datos");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR FORMATO DE FECHA
        |--------------------------------------------------------------------------
        | Evita mandar fechas inválidas al procedimiento almacenado.
        */
        $fechaValida = DateTime::createFromFormat('Y-m-d H:i:s', $fechaInicio);

        if (!$fechaValida) {
            header("Location: " . self::MEMBRESIAS_INDEX_VIEW .
                "?id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=fecha");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | USO DEL MODELO
        |--------------------------------------------------------------------------
        */
        $modelo = new Membresia();

        // Antes de registrar un nuevo pago, actualizamos estados vencidos.
        $modelo->actualizarEstadosMembresias();

        // Obtener información de la membresía seleccionada
        $membresia = $modelo->obtenerMembresiaPorNombre($planSeleccionado);

        // Validar que exista el plan
        if (!$membresia) {
            header("Location: " . self::MEMBRESIAS_INDEX_VIEW . "?id=$socioId&error=plan");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR PAGO
        |--------------------------------------------------------------------------
        */
        $ok = $modelo->registrarPago(
            $socioId,
            (int)$membresia['id'],
            $fechaInicio,
            $total,
            $metodoPago
        );

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR ESTADOS DESPUÉS DEL PAGO
        |--------------------------------------------------------------------------
        | Esto asegura que:
        | - Si la membresía inicia hoy o ya inició, el socio quede activo.
        | - Si ya hay membresías vencidas, se marquen como inactivas.
        */
        if ($ok) {
            $modelo->actualizarEstadosMembresias();
        }

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA FINAL
        |--------------------------------------------------------------------------
        */
        if ($ok) {
            // Redirigir a socios con confirmación
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?id=$socioId&paid=1");
        } else {
            // Redirigir con error
            header("Location: " . self::MEMBRESIAS_INDEX_VIEW .
                "?id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=pago");
        }

        exit;
    }
}