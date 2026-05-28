<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../config/app.php';

class UsuarioController
{
    private Usuario $modeloUsuario;

    public function __construct()
    {
        $this->modeloUsuario = new Usuario();
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

    private function validarAdmin(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';

        if ($rol !== 'Admin') {
            header('Location: ' . BASE_URL . '/dashboard.php?error=sin_permiso');
            exit;
        }
    }

    public function index(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';
        $usuarios = [];
        $permitido = $rol === 'Admin';

        if ($permitido) {
            $usuarios = $this->modeloUsuario->obtenerTodos();
        }

        require_once __DIR__ . '/../../resources/views/usuarios/index.php';
    }

    public function crear(): void
    {
        $this->validarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../../resources/views/usuarios/create.php';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'Recepcion');
        $especialidad = trim($_POST['especialidad'] ?? '');

        if (!$username || !$password || !$email) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=create');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email inválido';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=create');
            exit;
        }

        $ok = $this->modeloUsuario->crear($username, $password, $email, $role, 1);

        if ($ok) {
            // Si es instructor, crear el registro en instructores
            if ($role === 'Instructor') {
                $usuarioCreado = $this->modeloUsuario->obtenerPorUsername($username);
                if ($usuarioCreado) {
                    $this->crearInstructor($usuarioCreado['id'], $especialidad);
                }
            }

            $_SESSION['success'] = 'Usuario creado exitosamente';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=index');
        } else {
            $_SESSION['error'] = 'El usuario ya existe o hubo un error al crear';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=create');
        }
        exit;
    }

    public function editar(): void
    {
        $this->validarAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=index');
            exit;
        }

        $usuario = $this->modeloUsuario->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../../resources/views/usuarios/edit.php';
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;

        if (!$email) {
            $_SESSION['error'] = 'El email es obligatorio';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=editar&id=' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email inválido';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=editar&id=' . $id);
            exit;
        }

        $ok = $this->modeloUsuario->actualizar($id, $email, $active);

        if ($ok) {
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=index');
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
            header('Location: ' . BASE_URL . '/app/controllers/UsuarioController.php?action=editar&id=' . $id);
        }
        exit;
    }

    public function cambiarPassword(): void
    {
        $this->validarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $newPassword = trim($_POST['new_password'] ?? '');

        if ($id <= 0 || !$newPassword) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }

        $usuario = $this->modeloUsuario->obtenerPorId($id);

        if (!$usuario) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        $ok = $this->modeloUsuario->cambiarPassword($id, $newPassword);

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    }

    public function eliminar(): void
    {
        $this->validarAdmin();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        $usuario = $this->modeloUsuario->obtenerPorId($id);

        if (!$usuario) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        // Prevenir eliminar al admin actual
        if ($id === ($_SESSION['user']['id'] ?? 0)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No puedes eliminar tu propia cuenta']);
            exit;
        }

        $ok = $this->modeloUsuario->eliminar($id);

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    }

    private function crearInstructor(int $userId, string $especialidad): bool
    {
        try {
            $database = new Database();
            $conn = $database->connect();

            $stmt = $conn->prepare(
                "INSERT INTO instructores (user_id, nombre, especialidad, horas_diarias, activo) 
                 SELECT id, username, :especialidad, 8, 1 FROM users WHERE id = :user_id"
            );

            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':especialidad', $especialidad, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}

require_once __DIR__ . '/../../config/database.php';

$controller = new UsuarioController();
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

switch ($action) {
    case 'crear':
        $controller->crear();
        break;

    case 'editar':
        $controller->editar();
        break;

    case 'cambiar-password':
        $controller->cambiarPassword();
        break;

    case 'eliminar':
        $controller->eliminar();
        break;

    case 'index':
    default:
        $controller->index();
        break;
}
