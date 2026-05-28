<?php

require_once __DIR__ . '/../models/Acceso.php';
require_once __DIR__ . '/../../config/app.php';

class AccesoController
{
    private Acceso $modelo;

    public function __construct()
    {
        $this->modelo = new Acceso();
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

    private function validarRolesAccesos(): void
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
        $this->validarRolesAccesos();

        $buscar = trim($_GET['buscar'] ?? '');
        $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
        $resultado = trim($_GET['resultado'] ?? '');

        $accesos = $this->modelo->obtenerAccesos($buscar, $fechaInicio, $fechaFin, $resultado);

        require_once __DIR__ . '/../../resources/views/accesos/index.php';
    }

    public function biometrico(): void
    {
        $this->validarRolesAccesos();

        // Limitar y sanitizar la búsqueda para evitar consultas muy largas o inestables
        $buscar = mb_substr(trim($_GET['buscar'] ?? ''), 0, 200);
        $socioId = isset($_GET['socio_id']) && $_GET['socio_id'] !== '' ? (int)$_GET['socio_id'] : null;

        $socios = [];
        $buscarError = null;

        if ($buscar !== '') {
            try {
                $socios = $this->modelo->buscarSocios($buscar);
            } catch (\Exception $e) {
                // Guardamos el mensaje para mostrarlo en la vista y evitar que la página truene
                $socios = [];
                $buscarError = $e->getMessage();
                // Log para diagnóstico en server
                error_log('[AccesoController::biometrico] Error buscarSocios: ' . $buscarError);
            }
        }

        $socioSeleccionado = $socioId !== null && $socioId > 0 ? $this->modelo->obtenerSocioConMembresiaActiva($socioId) : null;

        require_once __DIR__ . '/../../resources/views/accesos/biometrico.php';
    }

    public function buscar(): void
    {
        $this->validarRolesAccesos();

        $q = trim($_GET['q'] ?? '');
        $q = mb_substr($q, 0, 200);

        header('Content-Type: application/json');

        if ($q === '') {
            echo json_encode(['resultados' => []]);
            exit;
        }

        try {
            $socios = $this->modelo->buscarSocios($q);
            $resultados = [];

            foreach ($socios as $s) {
                $texto = trim($s['nombres'] . ' ' . $s['apellido_paterno'] . ' ' . $s['apellido_materno']);
                $resultados[] = [
                    'id' => (int)$s['id'],
                    'texto' => $texto . ' — ' . ($s['membresia'] ?? 'Sin membresía'),
                    'estado' => $s['estado'] ?? '',
                ];
            }

            echo json_encode(['resultados' => array_slice($resultados, 0, 10)]);
            exit;
        } catch (\Exception $e) {
            error_log('[AccesoController::buscar] ' . $e->getMessage());
            echo json_encode(['resultados' => []]);
            exit;
        }
    }

    public function registrar(): void
    {
        $this->validarRolesAccesos();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/controllers/AccesoController.php?action=biometrico');
            exit;
        }

        $socioId = (int)($_POST['socio_id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? '');
        $motivo = trim($_POST['motivo'] ?? '');

        if ($socioId < 0) {
            header('Location: ' . BASE_URL . '/app/controllers/AccesoController.php?action=biometrico&error=socio');
            exit;
        }

        $socio = $socioId > 0 ? $this->modelo->obtenerSocioPorId($socioId) : null;
        $membresiaId = $socioId > 0 ? $this->modelo->obtenerMembresiaActivaSocio($socioId) : null;
        $resultado = 'denegado';

        $socioPuedeIngresar = $socio !== null
            && $socio['estado'] !== 'suspendido'
            && $membresiaId !== null;

        if ($tipo === 'diario') {
            $resultado = 'permitido';
            $motivo = $motivo !== '' ? $motivo : 'Acceso diario autorizado';
        } elseif ($socioPuedeIngresar) {
            $resultado = 'permitido';
            $motivo = $motivo !== '' ? $motivo : 'Acceso autorizado con membresía activa';
        } else {
            $motivo = $motivo !== '' ? $motivo : 'Socio sin membresía activa o estado inactivo';
        }

        if (!$this->modelo->registrarAcceso($socioId, $membresiaId, $resultado, $motivo)) {
            header('Location: ' . BASE_URL . '/app/controllers/AccesoController.php?action=biometrico&error=registro');
            exit;
        }

        $successKey = $resultado === 'permitido' ? 'acceso_permitido' : 'acceso_denegado';
        header('Location: ' . BASE_URL . '/app/controllers/AccesoController.php?action=biometrico&success=' . $successKey);
        exit;
    }
}

$controller = new AccesoController();
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

switch ($action) {
    case 'biometrico':
        $controller->biometrico();
        break;
    case 'buscar':
        $controller->buscar();
        break;

    case 'registrar':
        $controller->registrar();
        break;

    case 'index':
    default:
        $controller->index();
        break;
}
