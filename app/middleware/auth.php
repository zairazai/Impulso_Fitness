<?php

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN GENERAL
|--------------------------------------------------------------------------
| Cargamos BASE_URL para usar rutas centralizadas.
*/
require_once __DIR__ . '/../../config/app.php';

/*
|--------------------------------------------------------------------------
| SESIÓN
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