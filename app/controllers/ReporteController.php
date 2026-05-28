<?php

require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../../config/app.php';

class ReporteController
{
    private Reporte $modelo;

    public function __construct()
    {
        $this->modelo = new Reporte();
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

    private function validarRolesReportes(): void
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
        $this->validarRolesReportes();

        $resumen = $this->modelo->obtenerResumenGeneral();

        require_once __DIR__ . '/../../resources/views/reportes/index.php';
    }

    public function membresias(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
        $estado = trim($_GET['estado'] ?? '');

        $resumen = $this->modelo->obtenerResumenMembresias($buscar, $fechaInicio, $fechaFin, $estado);
        $pagos = $this->modelo->obtenerPagosMembresia($buscar, $fechaInicio, $fechaFin, $estado);

        require_once __DIR__ . '/../../resources/views/reportes/membresias.php';
    }

    public function inventario(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');

        $resumen = $this->modelo->obtenerResumenInventario();
        $productos = $this->modelo->obtenerProductosInventario($buscar);

        require_once __DIR__ . '/../../resources/views/reportes/inventario.php';
    }

    public function ventas(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $ventas = $this->modelo->obtenerVentas($buscar, $fechaInicio, $fechaFin);

        require_once __DIR__ . '/../../resources/views/reportes/ventas.php';
    }

    public function exportarMembresias(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
        $estado = trim($_GET['estado'] ?? '');

        $pagos = $this->modelo->exportarPagosMembresia($buscar, $fechaInicio, $fechaFin, $estado);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=membresias_report_' . date('Ymd_His') . '.csv');

        $salida = fopen('php://output', 'w');
        fputcsv($salida, ['ID', 'Socio', 'Estado', 'Plan', 'Monto', 'Método de pago', 'Fecha de pago', 'Referencia']);

        foreach ($pagos as $pago) {
            fputcsv($salida, [
                $pago['id'],
                $pago['socio'],
                $pago['estado_socio'],
                $pago['membresia'],
                number_format((float)$pago['monto'], 2),
                $pago['metodo_pago'],
                $pago['fecha_pago'],
                $pago['referencia'],
            ]);
        }

        fclose($salida);
        exit;
    }

    public function exportarInventario(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');
        $productos = $this->modelo->exportarProductosInventario($buscar);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inventario_report_' . date('Ymd_His') . '.csv');

        $salida = fopen('php://output', 'w');
        fputcsv($salida, ['ID', 'Código', 'Nombre', 'Categoría', 'Stock', 'Stock mínimo', 'Precio', 'Costo']);

        foreach ($productos as $producto) {
            fputcsv($salida, [
                $producto['id'],
                $producto['codigo'],
                $producto['nombre'],
                $producto['categoria'],
                $producto['stock'],
                $producto['stock_minimo'],
                number_format((float)$producto['precio_venta'], 2),
                number_format((float)$producto['costo_compra'], 2),
            ]);
        }

        fclose($salida);
        exit;
    }

    public function exportarVentas(): void
    {
        $this->validarRolesReportes();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $ventas = $this->modelo->exportarVentas($buscar, $fechaInicio, $fechaFin);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=historial_ventas_' . date('Ymd_His') . '.csv');

        $salida = fopen('php://output', 'w');
        fputcsv($salida, ['ID', 'Fecha', 'Cliente', 'Teléfono', 'Email', 'Usuario', 'Método de pago', 'Total', 'Producto', 'Código', 'Cantidad', 'Precio unitario', 'Subtotal']);

        foreach ($ventas as $venta) {
            fputcsv($salida, [
                $venta['venta_id'],
                $venta['fecha'],
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

        fclose($salida);
        exit;
    }

    public function buscarMembresias(): void
    {
        $this->validarRolesReportes();

        $query = trim($_GET['q'] ?? '');

        if ($query === '') {
            header('Content-Type: application/json');
            echo json_encode(['resultados' => []]);
            exit;
        }

        $pagos = $this->modelo->obtenerPagosMembresia($query, null, null, '');

        $resultados = [];
        foreach ($pagos as $pago) {
            $resultados[] = [
                'id' => $pago['id'],
                'texto' => $pago['socio'] . ' - ' . $pago['membresia'] . ' ($' . number_format((float)$pago['monto'], 2) . ')',
                'socio' => $pago['socio'],
                'membresia' => $pago['membresia'],
                'monto' => $pago['monto']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['resultados' => array_slice($resultados, 0, 10)]);
        exit;
    }

    public function buscarVentas(): void
    {
        $this->validarRolesReportes();

        $query = trim($_GET['q'] ?? '');

        if ($query === '') {
            header('Content-Type: application/json');
            echo json_encode(['resultados' => []]);
            exit;
        }

        $ventas = $this->modelo->obtenerVentas($query, null, null);

        $resultados = [];
        foreach ($ventas as $venta) {
            $resultados[] = [
                'id' => $venta['id'],
                'texto' => 'ID: ' . $venta['id'] . ' - ' . $venta['socio'] . ' ($' . number_format((float)$venta['total'], 2) . ')',
                'socio' => $venta['socio'],
                'cliente' => $venta['socio'],
                'total' => $venta['total'],
                'fecha' => $venta['fecha']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['resultados' => array_slice($resultados, 0, 10)]);
        exit;
    }
}

$controller = new ReporteController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'membresias':
        $controller->membresias();
        break;

    case 'exportarMembresias':
        $controller->exportarMembresias();
        break;

    case 'buscarMembresias':
        $controller->buscarMembresias();
        break;

    case 'inventario':
        $controller->inventario();
        break;

    case 'ventas':
        $controller->ventas();
        break;

    case 'exportarInventario':
        $controller->exportarInventario();
        break;

    case 'exportarVentas':
        $controller->exportarVentas();
        break;

    case 'buscarVentas':
        $controller->buscarVentas();
        break;

    case 'index':
    default:
        $controller->index();
        break;
}
