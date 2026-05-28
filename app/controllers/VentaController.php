<?php

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../../config/app.php';

class VentaController
{
    private Venta $modelo;

    public function __construct()
    {
        $this->modelo = new Venta();
    }

    private function validarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    private function validarRolesVentas(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';
        $rolesPermitidos = ['Admin', 'Recepcion'];

        if (!in_array($rol, $rolesPermitidos, true)) {
            header('Location: ' . BASE_URL . '/resources/views/dashboard.php?error=sin_permiso');
            exit;
        }
    }

    public function index(): void
    {
        $this->validarRolesVentas();

        $busqueda = trim($_GET['buscar'] ?? '');
        $busquedaSocios = trim($_GET['buscar_socio'] ?? '');

        $productos = $this->modelo->listarProductosDisponibles($busqueda);
        $socios = $this->modelo->listarSocios($busquedaSocios);

        require_once __DIR__ . '/../../resources/views/ventas/index.php';
    }

    public function store(): void
    {
        $this->validarRolesVentas();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=index');
            exit;
        }

        $socioId = (int)($_POST['socio_id'] ?? 0);
        $metodoPago = trim($_POST['metodo_pago'] ?? '');
        $total = (float)($_POST['total'] ?? 0);
        $itemsJson = trim($_POST['items_json'] ?? '');

        $items = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items) || $total <= 0 || $metodoPago === '') {
            header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=index&error=datos');
            exit;
        }

        $usuarioId = (int)($_SESSION['user']['id'] ?? 0);

        $ventaId = $this->modelo->registrarVenta(
            $usuarioId,
            $socioId > 0 ? $socioId : null,
            $total,
            $metodoPago,
            $items
        );

        if ($ventaId === false) {
            $errorMessage = $this->modelo->getLastError() ?? 'Error al generar la venta';
            header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=index&error=venta&reason=' . urlencode($errorMessage));
            exit;
        }

        header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=recibo&id=' . $ventaId);
        exit;
    }

    public function recibo(): void
    {
        $this->validarRolesVentas();

        $ventaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($ventaId <= 0) {
            header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=historial');
            exit;
        }

        $venta = $this->modelo->obtenerVentaRecibo($ventaId);

        if (!$venta) {
            header('Location: ' . BASE_URL . '/app/controllers/VentaController.php?action=historial');
            exit;
        }

        $detalle = $this->modelo->obtenerDetalleVenta($ventaId);

        require_once __DIR__ . '/../../resources/views/ventas/recibo.php';
    }

    public function historial(): void
    {
        $this->validarRolesVentas();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $ventas = $this->modelo->historialVentas($buscar, $fechaInicio, $fechaFin);

        require_once __DIR__ . '/../../resources/views/ventas/historial.php';
    }

    public function exportarHistorial(): void
    {
        $this->validarRolesVentas();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $ventas = $this->modelo->exportarHistorialCompleto($buscar, $fechaInicio, $fechaFin);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=historial_ventas_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Fecha', 'Cliente', 'Teléfono', 'Email', 'Usuario', 'Método', 'Total', 'Producto', 'Código', 'Cantidad', 'Precio Unitario', 'Subtotal']);

        foreach ($ventas as $venta) {
            fputcsv($output, [
                $venta['venta_id'],
                date('d/m/Y H:i', strtotime($venta['fecha'])),
                $venta['socio'],
                $venta['telefono'] ?? '',
                $venta['email'] ?? '',
                $venta['usuario'],
                $venta['metodo_pago'],
                number_format((float)$venta['total'], 2),
                $venta['producto'] ?? '',
                $venta['codigo'] ?? '',
                (int)($venta['cantidad'] ?? 0),
                number_format((float)($venta['precio_unitario'] ?? 0), 2),
                number_format((float)($venta['subtotal'] ?? 0), 2),
            ]);
        }

        fclose($output);
        exit;
    }
}

$controller = new VentaController();
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
