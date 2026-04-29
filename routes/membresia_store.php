<?php

/*
|--------------------------------------------------------------------------
| RUTA DE MEMBRESÍA (STORE)
|--------------------------------------------------------------------------
| Este archivo funciona como puente entre la vista y el controlador.
| NO contiene lógica, solo delega al controlador.
*/

require_once __DIR__ . '/../app/controllers/MembresiaController.php';

// Instanciar controlador
$controller = new MembresiaController();

// Ejecutar método store
$controller->store();