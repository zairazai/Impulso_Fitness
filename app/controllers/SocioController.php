<?php
/*
|--------------------------------------------------------------------------
| CARGA DE DEPENDENCIAS
|--------------------------------------------------------------------------
| require_once incluye archivos necesarios para que el controlador funcione.
| __DIR__ obtiene la ruta actual del archivo y permite acceder a otros
| archivos usando rutas relativas sin importar desde dónde se ejecute.
|
| Socio.php hacer referencia al  Modelo del módulo de socios.
| app.php  es la Configuración global de la aplicación (BASE_URL).
*/
require_once __DIR__ . '/../models/Socio.php';
require_once __DIR__ . '/../../config/app.php';

class SocioController
{

/*
|--------------------------------------------------------------------------
| RUTAS CONSTANTES DEL CONTROLADOR
|--------------------------------------------------------------------------
| Se definen como constantes privadas porque:
| - Son rutas fijas que no cambian durante la ejecución.
| - Solo deben utilizarse dentro de este controlador.
| - Evitan repetir rutas manualmente en varias partes del código.
| - Facilitan el mantenimiento y la lectura del sistema.
*/

    private const LOGIN_VIEW =  BASE_URL . '/resources/views/auth/login.php';
    private const SOCIOS_INDEX_VIEW = BASE_URL . '/resources/views/socios/index.php';
    private const SOCIOS_CREATE_VIEW = BASE_URL . '/resources/views/socios/create.php';
    private const SOCIOS_EDIT_VIEW = BASE_URL . '/resources/views/socios/edit.php';
    private const MEMBRESIAS_INDEX_VIEW = BASE_URL . '/resources/views/membresias/index.php';

    private function validarSesion(): void
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: " . self::LOGIN_VIEW);
            exit;
        }
    }

    public function store(): void
    {
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::SOCIOS_CREATE_VIEW);
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

        $direccion = trim($_POST['direccion'] ?? '');
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
            $direccion === '' ||
            $huellaDemo === '' ||
            $plan === ''
        ) {
            header("Location: " . self::SOCIOS_CREATE_VIEW . "?error=campos");
            exit;
        }

        $socio = new Socio();

        $nuevoId = $socio->insertarSocioCompleto(
            $nombres,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimiento,
            $telefono,
            $email,
            $genero,
            $nombreContacto,
            $telefonoEmergencia,
            $direccion,
            $notas
        );

        if ($nuevoId <= 0) {
            header("Location: " . self::SOCIOS_CREATE_VIEW . "?error=insert_failed");
            exit;
        }

        $hash = hash('sha256', $huellaDemo);
        $socio->registrarHuella($nuevoId, $hash);

        header("Location: " . self::MEMBRESIAS_INDEX_VIEW . "?success=1&id=$nuevoId&plan=" . urlencode($plan));
        exit;
    }

    public function update(): void
    {
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::SOCIOS_INDEX_VIEW);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?error=id");
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

        $direccion = trim($_POST['direccion'] ?? '');
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
            $direccion === ''
        ) {
            header("Location: " . self::SOCIOS_EDIT_VIEW . "?id=$id&error=campos");
            exit;
        }

        $socio = new Socio();

        $ok = $socio->actualizarSocioCompleto(
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
            $direccion,
            $notas
        );

        if ($ok) {
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?success=1");
        } else {
            header("Location: " . self::SOCIOS_EDIT_VIEW . "?id=$id&error=update");
        }

        exit;
    }

    public function delete(): void
    {
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::SOCIOS_INDEX_VIEW);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?error=id");
            exit;
        }

        $socio = new Socio();
        $ok = $socio->desactivarSocio($id);

        if ($ok) {
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?deleted=1&id=$id");
        } else {
            header("Location: " . self::SOCIOS_INDEX_VIEW . "?error=delete");
        }

        exit;
    }
}