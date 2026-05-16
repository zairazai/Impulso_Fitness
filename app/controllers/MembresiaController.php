<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../models/Membresia.php';


class MembresiaController
{
     private Membresia $modelo;

    public function __construct()
    {
        $this->modelo = new Membresia();
    }

    /*  RUTAS DE REDIRECCIÓN */
    private const LOGIN_ROUTE = BASE_URL . '/login';
    private const MEMBRESIAS_INDEX_ROUTE = BASE_URL . '/app/controllers/MembresiaController.php?action=index';
    private const SOCIOS_INDEX_ROUTE = BASE_URL . '/app/controllers/SocioController.php?action=index';
    private const MEMBRESIAS_HISTORIAL_ROUTE = BASE_URL . '/app/controllers/MembresiaController.php?action=historial';

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
            header("Location: " . self::LOGIN_ROUTE);
            exit;
        }
    }

   public function index(): void
    {
        $this->validarSesion();

        $this->modelo->actualizarEstadosMembresias();

        $idSeleccionado = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $planSeleccionado = trim($_GET['plan'] ?? '');
        $success = trim($_GET['success'] ?? '');

        $socios = $this->modelo->listarSocios();

        $socioSeleccionado = null;

        if ($idSeleccionado > 0) {
            $socioSeleccionado = $this->modelo->obtenerSocioPorId($idSeleccionado);
        }

        $planes = $this->modelo->listarMembresias();

        $totalInicial = 0;

        foreach ($planes as $plan) {
            if ($plan['nombre'] === $planSeleccionado) {
                $totalInicial = (float)$plan['precio'];
                break;
            }
        }

        $fechaActual = date('Y-m-d H:i:s');

        require_once __DIR__ . '/../../resources/views/membresias/index.php';
    }

   public function historial(): void
    {
        $this->validarSesion();

        $this->modelo->actualizarEstadosMembresias();

        $busqueda = trim($_GET['buscar'] ?? '');

        $fechaInicio = !empty($_GET['fecha_inicio'])
            ? $_GET['fecha_inicio']
            : null;

        $fechaFin = !empty($_GET['fecha_fin'])
            ? $_GET['fecha_fin']
            : null;

        $historial = $this->modelo->historialPagos(
            $busqueda,
            $fechaInicio,
            $fechaFin
        );

        require_once __DIR__ .
        '/../../resources/views/membresias/historial.php';
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (REGISTRAR MEMBRESÍA / PAGO)
    |--------------------------------------------------------------------------
    | Este método:
    | 1. Recibe datos del formulario, 2. Valida datos, 3. Consulta el modelo
    | 4. Registra el pago, 5. Actualiza estados de membresías/socios
    | 6. Redirige según resultado
    */
    public function store(): void
{
    $this->validarSesion();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . self::MEMBRESIAS_INDEX_ROUTE);
        exit;
    }

    $socioId = (int)($_POST['socio_id'] ?? 0);
    $planSeleccionado = trim($_POST['plan_seleccionado'] ?? '');
    $fechaInicioInput = trim($_POST['fecha_inicio'] ?? '');
    $total = (float)($_POST['total'] ?? 0);
    $metodoPago = trim($_POST['metodo_pago'] ?? '');

    $fechaInicio = '';

    if ($fechaInicioInput !== '') {
        $fechaInicio = str_replace('T', ' ', $fechaInicioInput);

        if (strlen($fechaInicio) === 16) {
            $fechaInicio .= ':00';
        }
    }

    if (
        $socioId <= 0 ||
        $planSeleccionado === '' ||
        $fechaInicio === '' ||
        $total <= 0 ||
        $metodoPago === ''
    ) {
        header("Location: " . self::MEMBRESIAS_INDEX_ROUTE .
            "&id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=datos");
        exit;
    }

    $fechaValida = DateTime::createFromFormat('Y-m-d H:i:s', $fechaInicio);

    if (!$fechaValida) {
        header("Location: " . self::MEMBRESIAS_INDEX_ROUTE .
            "&id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=fecha");
        exit;
    }

    $this->modelo->actualizarEstadosMembresias();

    $membresia = $this->modelo->obtenerMembresiaPorNombre($planSeleccionado);

    if (!$membresia) {
        header("Location: " . self::MEMBRESIAS_INDEX_ROUTE . "&id=$socioId&error=plan");
        exit;
    }

    $ok = $this->modelo->registrarPago(
        $socioId,
        (int)$membresia['id'],
        $fechaInicio,
        $total,
        $metodoPago
    );

    if ($ok) {

        $this->modelo->actualizarEstadosMembresias();

        $_SESSION['success'] = 'Pago registrado correctamente. Puedes generar el recibo desde el historial.';

        header("Location: " . BASE_URL . "/app/controllers/MembresiaController.php?action=historial");

        exit;
    }

    header("Location: " . self::MEMBRESIAS_INDEX_ROUTE .
        "&id=$socioId&plan=" . urlencode($planSeleccionado) . "&error=pago");
    exit;
}

    public function recibo(): void
{
    $this->validarSesion();

    $pagoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($pagoId <= 0) {
        header("Location: " . self::MEMBRESIAS_HISTORIAL_ROUTE . "&error=recibo");

        exit;
    }

    $this->modelo->actualizarEstadosMembresias();

    $recibo = $this->modelo->obtenerReciboMembresia($pagoId);

    if (!$recibo) {
        header("Location: " . self::MEMBRESIAS_HISTORIAL_ROUTE . "&error=recibo");
        exit;
    }

    $referencia = $recibo['referencia'] ?? '';

    if ($referencia === '' || $referencia === null) {
        $referencia = 'REC-' .
            date('Ymd', strtotime($recibo['fecha_pago'])) .
            '-' .
            str_pad((string)$recibo['pago_id'], 6, '0', STR_PAD_LEFT);
    }

    require_once __DIR__ . '/../../resources/views/membresias/recibo.php';
}

public function exportarHistorial(): void
    {
        $this->validarSesion();

        $busqueda = trim($_GET['buscar'] ?? '');

        $fechaInicio = !empty($_GET['fecha_inicio'])
            ? $_GET['fecha_inicio']
            : null;

        $fechaFin = !empty($_GET['fecha_fin'])
            ? $_GET['fecha_fin']
            : null;

        $historial = $this->modelo->historialPagos(
            $busqueda,
            $fechaInicio,
            $fechaFin
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=historial_pagos_membresias.csv');

        $salida = fopen('php://output', 'w');

        fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($salida, [
            'ID',
            'Socio',
            'Fecha de pago',
            'Método de pago',
            'Membresía',
            'Monto'
        ]);

        foreach ($historial as $pago) {
            fputcsv($salida, [
                $pago['id'] ?? '',
                $pago['socio_nombre'] ?? '',
                $pago['fecha_pago'] ?? '',
                $pago['metodo_pago'] ?? '',
                $pago['membresia_nombre'] ?? '',
                $pago['monto'] ?? ''
            ]);
        }

        fclose($salida);
        exit;
    }

}

$controller = new MembresiaController();

$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

switch ($action) {

    case 'index':
        $controller->index();
        break;

    case 'store':
        $controller->store();
        break;
    
    case 'historial':
    $controller->historial();
    break;

    case 'recibo':
    $controller->recibo();
    break;
    
    case 'exportarHistorial':
    $controller->exportarHistorial();
    break;

    default:
        $controller->index();
        break;
}