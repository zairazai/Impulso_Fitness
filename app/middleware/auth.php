<?php

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN GENERAL:  Cargamos BASE_URL para usar rutas centralizadas.
*/
require_once __DIR__ . '/../../config/app.php';

/*
|--------------------------------------------------------------------------
| SESIÓN: Usamos sesiones PHP y un middleware de autenticación que valida 
|si existe un usuario autenticado en $_SESSION. Si no existe, 
|redirige automáticamente al login.
|--------------------------------------------------------------------------
| Iniciamos sesión solo si no existe una sesión activa.
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE AUTENTICACIÓN
|--------------------------------------------------------------------------
| Si no hay usuario autenticado, redirigimos al login.
*/
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/resources/views/auth/login.php");
    exit;
}