<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    private function generateRandomPassword(int $length = 10): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }

    public function forgotForm(): void
    {
        require_once __DIR__ . '/../../resources/views/auth/forgot.php';
    }

    public function sendReset(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            header('Location: ' . BASE_URL . '/resources/views/auth/forgot.php?error=campos');
            exit;
        }

        // Verificar que el email existe en users
        $stmt = $this->conn->prepare('SELECT id, username FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$user) {
            // No revelar si el email existe — mostrar mensaje genérico
            header('Location: ' . BASE_URL . '/resources/views/auth/forgot.php?success=sent');
            exit;
        }

        // Generar una contraseña aleatoria y actualizarla directamente en la base de datos
        $newPassword = $this->generateRandomPassword();
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare('UPDATE users SET password_hash = :hash WHERE email = :email');
        $stmt->bindValue(':hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        $subject = 'Nueva contraseña - Impulso Fitness';
        $message = "Hola,\n\nSe ha generado una nueva contraseña para tu cuenta. Utiliza la siguiente contraseña para iniciar sesión:\n\n$newPassword\n\nPor seguridad, cambia tu contraseña después de iniciar sesión.\n\nSaludos,\nImpulso Fitness";

        require_once __DIR__ . '/../helpers/Mailer.php';

        $mailSent = Mailer::send($email, $subject, $message);

        if ($mailSent) {
            header('Location: ' . BASE_URL . '/resources/views/auth/forgot.php?success=sent');
        } else {
            // En entornos locales o si falla el correo, no mostramos la contraseña en pantalla por seguridad.
            header('Location: ' . BASE_URL . '/resources/views/auth/forgot.php?error=mail');
        }

        exit;
    }

    public function resetForm(): void
    {
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            header('Location: ' . BASE_URL . '/resources/views/auth/login.php');
            exit;
        }

        // Validar token
        $stmt = $this->conn->prepare('SELECT email, expires_at FROM password_resets WHERE token = :token LIMIT 1');
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row || strtotime($row['expires_at']) < time()) {
            header('Location: ' . BASE_URL . '/resources/views/auth/login.php?error=token');
            exit;
        }

        $email = $row['email'];
        require_once __DIR__ . '/../../resources/views/auth/reset.php';
    }

    public function updatePassword(): void
    {
        $token = trim($_POST['token'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');

        if ($token === '' || $password === '' || $password !== $password2) {
            header('Location: ' . BASE_URL . '/app/controllers/AuthController.php?action=reset&token=' . urlencode($token) . '&error=campos');
            exit;
        }

        $stmt = $this->conn->prepare('SELECT email, expires_at FROM password_resets WHERE token = :token LIMIT 1');
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row || strtotime($row['expires_at']) < time()) {
            header('Location: ' . BASE_URL . '/resources/views/auth/login.php?error=token');
            exit;
        }

        $email = $row['email'];

        // Actualizar password en users
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('UPDATE users SET password_hash = :hash WHERE email = :email');
        $stmt->bindValue(':hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        // Eliminar tokens para ese email
        $stmt = $this->conn->prepare('DELETE FROM password_resets WHERE email = :email');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        header('Location: ' . BASE_URL . '/resources/views/auth/login.php?success=reset');
        exit;
    }
}

$controller = new AuthController();
$action = $_GET['action'] ?? $_POST['action'] ?? 'forgot';

switch ($action) {
    case 'forgot':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->sendReset();
        } else {
            $controller->forgotForm();
        }
        break;

    case 'reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updatePassword();
        } else {
            $controller->resetForm();
        }
        break;

    default:
        $controller->forgotForm();
        break;
}
