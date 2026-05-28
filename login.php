<?php
session_start();

require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php");
    exit;
}

$login = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($login === '' || $password === '') {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php?error=campos");
    exit;
}

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM users WHERE username = :login OR email = :login_email LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':login', $login, PDO::PARAM_STR);
$stmt->bindParam(':login_email', $login, PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php?error=credenciales");
    exit;
}

if ((int)$user['active'] !== 1) {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php?error=inactivo");
    exit;
}

$_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role']
];

header("Location: /Impulso_Fitness/dashboard.php");
exit;