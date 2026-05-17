<?php
/*
|--------------------------------------------------------------------------
| CARGA DE DEPENDENCIAS
|--------------------------------------------------------------------------
| require_once incluye archivos necesarios para que el controlador funcione.
| __DIR__ obtiene la ruta actual del archivo y permite acceder a otros
| archivos usando rutas relativas sin importar desde dónde se ejecute.
| Socio.php hacer referencia al  Modelo del módulo de socios.
| app.php  es la Configuración global de la aplicación (BASE_URL).
*/
require_once __DIR__ . '/../models/Socio.php';
require_once __DIR__ . '/../../config/app.php';

class SocioController
{
    private Socio $modelo;

public function __construct()
{
    $this->modelo = new Socio();
}

/*
|--------------------------------------------------------------------------
| RUTAS CONSTANTES DEL CONTROLADOR
|--------------------------------------------------------------------------
*/

    private const SOCIOS_INDEX_ROUTE = BASE_URL . '/app/controllers/SocioController.php?action=index';
    private const SOCIOS_CREATE_ROUTE = BASE_URL . '/app/controllers/SocioController.php?action=create';
    private const SOCIOS_EDIT_ROUTE = BASE_URL . '/app/controllers/SocioController.php?action=edit';
    private const MEMBRESIAS_INDEX_ROUTE = BASE_URL . '/app/controllers/MembresiaController.php?action=index';
    private const LOGIN_ROUTE = BASE_URL . '/login';


    private function validarSesion(): void
    {
        // Iniciar sesión solo si aún no existe una activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar que exista un usuario autenticado
        if (!isset($_SESSION['user'])) {
            header("Location: " . self::LOGIN_ROUTE);
            exit;
        }
    }

    public function store(): void
    {
        $this->validarSesion();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . BASE_URL . "/app/controllers/SocioController.php?action=create");
        exit;
    }

        $nombres = trim($_POST['nombres'] ?? '');
        $apellidoPaterno = trim($_POST['apellido_paterno'] ?? '');
        $apellidoMaterno = trim($_POST['apellido_materno'] ?? '');

        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $genero = trim($_POST['genero'] ?? '');

        $nombreContacto = trim($_POST['nombre_contacto_emergencia'] ?? '');
        $telefonoEmergencia = trim($_POST['telefono_emergencia'] ?? '');

        $calle = trim($_POST['calle'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $colonia = trim($_POST['colonia'] ?? '');
        $codigoPostal = trim($_POST['codigo_postal'] ?? '');
        $notas = trim($_POST['notas'] ?? '');
        $huellaDemo = trim($_POST['huella_demo'] ?? '');
        $plan = trim($_POST['membresia_preseleccion'] ?? '');

        if (
            $nombres === '' ||
            $apellidoPaterno === '' ||
            $fechaNacimiento === '' ||
            $telefono === '' ||
            $email === '' ||
            $genero === '' ||
            $nombreContacto === '' ||
            $telefonoEmergencia === '' ||
            $calle === '' ||
            $numero === '' ||
            $colonia === '' ||
            $codigoPostal === '' ||
            $huellaDemo === '' ||
            $plan === ''
        ) {
            header("Location: " . self::SOCIOS_CREATE_ROUTE . "?error=campos");
            exit;
        }
        /*
        |--------------------------------------------------------------------------
        | VALIDAR email
        |--------------------------------------------------------------------------
        */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . self::SOCIOS_CREATE_ROUTE . "?error=email");
            exit;
        }
        /*
        |--------------------------------------------------------------------------
        | VALIDAR TELÉFONOS
        |--------------------------------------------------------------------------
        | Se valida que los teléfonos contengan exactamente 10 dígitos.
        */

        if (
            !preg_match('/^\d{10}$/', $telefono) ||
            !preg_match('/^\d{10}$/', $telefonoEmergencia)
        ) {
            header("Location: " . self::SOCIOS_CREATE_ROUTE . "?error=telefono");
            exit;
        }
        /*
        |--------------------------------------------------------------------------
        | VALIDAR CÓDIGO POSTAL
        |--------------------------------------------------------------------------
        | El CP mexicano debe contener exactamente 5 dígitos.
        */

        if (!preg_match('/^\d{5}$/', $codigoPostal)) {
            header("Location: " . self::SOCIOS_CREATE_ROUTE . "?error=cp");
            exit;
        }


        $nuevoId = $this->modelo->insertarSocioCompleto(
            $nombres,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimiento,
            $telefono,
            $email,
            $genero,
            $nombreContacto,
            $telefonoEmergencia,
            $calle,
            $numero,
            $colonia,
            $codigoPostal,
            $notas
        );

        if ($nuevoId <= 0) {
            header("Location: " . self::SOCIOS_CREATE_ROUTE . "?error=insert_failed");
            exit;
        }

        $hash = hash('sha256', $huellaDemo);
        $this->modelo->registrarHuella($nuevoId, $hash);

        $_SESSION['success'] = 'Socio registrado correctamente. Continúa con la asignación de membresía.';

        header("Location: " . self::MEMBRESIAS_INDEX_ROUTE . "&id=$nuevoId&plan=" . urlencode($plan));
        exit;
    }

    public function update(): void
    {
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::SOCIOS_INDEX_ROUTE);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::SOCIOS_INDEX_ROUTE . "?error=id");
            exit;
        }

        $nombres = trim($_POST['nombres'] ?? '');
        $apellidoPaterno = trim($_POST['apellido_paterno'] ?? '');
        $apellidoMaterno = trim($_POST['apellido_materno'] ?? '');

        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $genero = trim($_POST['genero'] ?? '');

        $nombreContacto = trim($_POST['nombre_contacto_emergencia'] ?? '');
        $telefonoEmergencia = trim($_POST['telefono_emergencia'] ?? '');

        $calle = trim($_POST['calle'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $colonia = trim($_POST['colonia'] ?? '');
        $codigoPostal = trim($_POST['codigo_postal'] ?? '');
        $notas = trim($_POST['notas'] ?? '');

        if (
            $nombres === '' ||
            $apellidoPaterno === '' ||
            $fechaNacimiento === '' ||
            $telefono === '' ||
            $email === '' ||
            $genero === '' ||
            $nombreContacto === '' ||
            $telefonoEmergencia === '' ||
            $calle === '' ||
            $numero === '' ||
            $colonia === '' ||
            $codigoPostal === ''
        ) {
            header("Location: " . self::SOCIOS_EDIT_ROUTE . "?id=$id&error=campos");
            exit;
        }
        /*
        |--------------------------------------------------------------------------
        | VALIDAR email
        |--------------------------------------------------------------------------
        */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . self::SOCIOS_EDIT_ROUTE . "?id=$id&error=email");
            exit;
        }
        /*
        |--------------------------------------------------------------------------
        | VALIDAR TELÉFONOS
        |--------------------------------------------------------------------------
        | Se valida que los teléfonos contengan exactamente 10 dígitos.
        */

        if (
            !preg_match('/^\d{10}$/', $telefono) ||
            !preg_match('/^\d{10}$/', $telefonoEmergencia)
        ) {
            header("Location: " . self::SOCIOS_EDIT_ROUTE . "?id=$id&error=telefono");
            exit;
        }
         /*
        |--------------------------------------------------------------------------
        | VALIDAR CÓDIGO POSTAL
        |--------------------------------------------------------------------------
        | El CP mexicano debe contener exactamente 5 dígitos.
        */

        if (!preg_match('/^\d{5}$/', $codigoPostal)) {
            header("Location: " . self::SOCIOS_EDIT_ROUTE . "?id=$id&error=cp");
            exit;
        }


        $ok = $this->modelo->actualizarSocioCompleto(
            $id,
            $nombres,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimiento,
            $telefono,
            $email,
            $genero,
            $nombreContacto,
            $telefonoEmergencia,
            $calle,
            $numero,
            $colonia,
            $codigoPostal,
            $notas
        );

       if ($ok) {

            $_SESSION['success'] = 'Socio actualizado correctamente.';

            header("Location: " . self::SOCIOS_INDEX_ROUTE . "&id=$id");
            exit;

        } else {

            $_SESSION['error'] = 'No se pudo actualizar el socio.';

            header("Location: " . self::SOCIOS_EDIT_ROUTE . "&id=$id");
            exit;
        }

        exit;
    }

    /*FUNCION PARA LISTAR SOCIOS*/
    public function index(): void
    {
        $socios = $this->modelo->listarSocios();

        $idSeleccionado = (int)($_GET['id'] ?? 0);

        $socioSeleccionado = null;
        $membresiaActiva = null;
        $historialPagosReciente = [];

        if ($idSeleccionado > 0) {
            $socioSeleccionado = $this->modelo->obtenerSocioPorId($idSeleccionado);
        }

        if (!$socioSeleccionado && !empty($socios)) {
            $socioSeleccionado = $this->modelo->obtenerSocioPorId(
                (int)$socios[0]['id']
            );
        }

        if ($socioSeleccionado) {
            $socioId = (int)$socioSeleccionado['id'];

            $membresiaActiva = $this->modelo->obtenerMembresiaActivaSocio($socioId);
            $historialPagosReciente = $this->modelo->obtenerHistorialRecientePagosSocio($socioId);
        }

        require_once __DIR__ . '/../../resources/views/socios/index.php';
    }

    public function create(): void
    {
        $this->validarSesion();

        require_once __DIR__ . '/../../resources/views/socios/create.php';
    }


    /*FUNCION PARA MOSTRAR PANTALLA PARA EDITAR SOCIOS*/

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::SOCIOS_INDEX_ROUTE . "?error=id");
            exit;
        }

        $socio = $this->modelo->obtenerSocioPorId($id);
        $huella = $this->modelo->obtenerHuellaPorSocio($id);

        if (!$socio) {
            header("Location: " . self::SOCIOS_INDEX_ROUTE . "?error=no_encontrado");
            exit;
        }

        require_once __DIR__ . '/../../resources/views/socios/edit.php';
    }

    /*FUNCION PARA DESACTIVAR SOCIOS*/

    public function delete(): void
    {
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::SOCIOS_INDEX_ROUTE);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::SOCIOS_INDEX_ROUTE . "&error=id");
            exit;
        }

        
       $ok = $this->modelo->desactivarSocio($id);

        if ($ok) {
            $_SESSION['success'] = 'Socio suspendido correctamente.';

            header("Location: " . self::SOCIOS_INDEX_ROUTE . "&id=$id");
            exit;
        } else {
            $_SESSION['error'] = 'No se pudo suspender el socio.';

            header("Location: " . self::SOCIOS_INDEX_ROUTE);
            exit;
        }



     exit;
    }

}

    $controller = new SocioController();

    $action = $_GET['action'] ?? $_POST['action'] ?? 'index';

    switch ($action) {

        case 'index':
            $controller->index();
            break;

        case 'create':
            $controller->create();
            break;

        case 'edit':
            $controller->edit();
            break;

        case 'store':
            $controller->store();
            break;

        case 'update':
            $controller->update();
            break;

        case 'destroy':
        $controller->delete();
        break;

        default:
            $controller->index();
            break;
    }