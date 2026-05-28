<?php

require_once __DIR__ . '/../models/Socio.php';
require_once __DIR__ . '/../models/Acceso.php';
require_once __DIR__ . '/../../config/app.php';

class IngresoController
{
    private Socio $modeloSocio;
    private Acceso $modeloAcceso;

    public function __construct()
    {
        $this->modeloSocio = new Socio();
        $this->modeloAcceso = new Acceso();
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

    private function validarRolesIngreso(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';
        $rolesPermitidos = ['Admin', 'Recepcion'];

        if (!in_array($rol, $rolesPermitidos, true)) {
            header('Location: ' . BASE_URL . '/dashboard.php?error=sin_permiso');
            exit;
        }
    }

    public function index(): void
    {
        $this->validarRolesIngreso();

        // Actualizar estados de membresías antes de mostrar datos
        $this->modeloSocio->actualizarEstadosMembresias();

        $buscar = trim($_GET['buscar'] ?? '');
        $socios = $buscar !== '' ? $this->modeloSocio->listarSocios($buscar) : [];
        $socioSeleccionado = null;

        if (!empty($_GET['socio_id'])) {
            $socioId = (int)$_GET['socio_id'];
            $socioData = $this->modeloSocio->obtenerSocioPorId($socioId);
            
            if ($socioData) {
                $membresia = $this->modeloSocio->obtenerMembresiaActivaSocio($socioId);
                $socioSeleccionado = array_merge($socioData, ['membresia' => $membresia]);
            }
        }

        require_once __DIR__ . '/../../resources/views/ingreso/index.php';
    }

    public function buscar(): void
    {
        $this->validarRolesIngreso();

        // Actualizar estados de membresías
        $this->modeloSocio->actualizarEstadosMembresias();

        $buscar = trim($_GET['q'] ?? '');
        
        if ($buscar === '') {
            header('Content-Type: application/json');
            echo json_encode(['socios' => []]);
            exit;
        }

        $socios = $this->modeloSocio->listarSocios($buscar);
        
        header('Content-Type: application/json');
        echo json_encode(['socios' => $socios]);
        exit;
    }

    public function detalle(): void
    {
        $this->validarRolesIngreso();

        // Actualizar estados de membresías
        $this->modeloSocio->actualizarEstadosMembresias();

        $socioId = (int)($_GET['socio_id'] ?? 0);
        
        if ($socioId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio inválido']);
            exit;
        }

        $socio = $this->modeloSocio->obtenerSocioPorId($socioId);
        
        if (!$socio) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio no encontrado']);
            exit;
        }

        $membresia = $this->modeloSocio->obtenerMembresiaActivaSocio($socioId);

        header('Content-Type: application/json');
        echo json_encode([
            'socio' => $socio,
            'membresia' => $membresia
        ]);
        exit;
    }

    public function registrarIngreso(): void
    {
        $this->validarRolesIngreso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $socioId = (int)($_POST['socio_id'] ?? 0);
        
        if ($socioId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio inválido']);
            exit;
        }

        $socio = $this->modeloSocio->obtenerSocioPorId($socioId);
        
        if (!$socio) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio no encontrado']);
            exit;
        }

        $membresia = $this->modeloSocio->obtenerMembresiaActivaSocio($socioId);
        $membresiaId = $membresia && !empty($membresia['membresia_id']) ? (int)$membresia['membresia_id'] : null;
        $resultado = 'permitido';
        $motivo = 'Ingreso registrado';

        $ok = $this->modeloAcceso->registrarAcceso($socioId, $membresiaId, $resultado, $motivo);

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    }

    public function registrarDenegacion(): void
    {
        $this->validarRolesIngreso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $socioId = (int)($_POST['socio_id'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? 'Membresía vencida');
        
        if ($socioId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio inválido']);
            exit;
        }

        $socio = $this->modeloSocio->obtenerSocioPorId($socioId);
        
        if (!$socio) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Socio no encontrado']);
            exit;
        }

        $membresia = $this->modeloSocio->obtenerMembresiaActivaSocio($socioId);
        $membresiaId = $membresia && !empty($membresia['membresia_id']) ? (int)$membresia['membresia_id'] : null;
        $resultado = 'denegado';

        $ok = $this->modeloAcceso->registrarAcceso($socioId, $membresiaId, $resultado, $motivo);

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    }
}

$controller = new IngresoController();
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

switch ($action) {
    case 'buscar':
        $controller->buscar();
        break;

    case 'detalle':
        $controller->detalle();
        break;

    case 'registrar':
        $controller->registrarIngreso();
        break;

    case 'denegacion':
        $controller->registrarDenegacion();
        break;

    case 'index':
    default:
        $controller->index();
        break;
}
