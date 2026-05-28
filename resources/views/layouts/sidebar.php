<?php

require_once __DIR__ . '/../../../config/app.php';
/*
|--------------------------------------------------------------------------
| SIDEBAR PRINCIPAL
|--------------------------------------------------------------------------
| Menú lateral del sistema.
| Se organiza por módulos para que cada integrante pueda ubicar fácilmente
| dónde trabajar su parte.
*/

$current = $_SERVER['REQUEST_URI'];

$isDashboard = str_contains($current, 'dashboard.php');

/*
|--------------------------------------------------------------------------
| SOCIOS
|--------------------------------------------------------------------------
*/
$isSociosIndex = str_contains($current, 'SocioController.php?action=index');
$isSociosCreate = str_contains($current, 'SocioController.php?action=create');
$isSociosEdit = str_contains($current, 'SocioController.php?action=edit');

$isSocios = $isSociosIndex || $isSociosCreate || $isSociosEdit;

/*
|--------------------------------------------------------------------------
| MEMBRESÍAS
|--------------------------------------------------------------------------
*/
$isMembresias = str_contains($current, '/membresias/');
$isMembresiasIndex = str_contains($current, '/membresias/index.php');
$isMembresiasHistorial = str_contains($current, '/membresias/historial.php');
$isMembresiasRecibo = str_contains($current, '/membresias/recibo.php');

/*
|--------------------------------------------------------------------------
| VENTAS
|--------------------------------------------------------------------------
*/
$isVentasIndex = str_contains($current, '/ventas/index.php') || str_contains($current, 'VentaController.php?action=index');
$isVentasHistorial = str_contains($current, '/ventas/historial.php') || str_contains($current, 'VentaController.php?action=historial');
$isVentas = $isVentasIndex || $isVentasHistorial;

/*
|--------------------------------------------------------------------------
| INVENTARIO
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| INVENTARIO
|--------------------------------------------------------------------------
*/
$isProductos = str_contains($current, 'InventarioController.php?action=productos');

$isEntradaProductos = str_contains($current, 'InventarioController.php?action=entrada');

$isMovimientos = str_contains($current, 'InventarioController.php?action=movimientos');

$isStockBajo = str_contains($current, 'InventarioController.php?action=stockBajo');

$isInventario = $isProductos || $isEntradaProductos || $isMovimientos || $isStockBajo;
/*
|--------------------------------------------------------------------------
| INGRESO DE PERSONAL
|--------------------------------------------------------------------------
*/
$isIngresoPersonal = str_contains($current, 'IngresoController.php') || str_contains($current, '/ingreso/');

/*
|--------------------------------------------------------------------------
| CONTROL DE ACCESO
|--------------------------------------------------------------------------
*/
$isControlAcceso = str_contains($current, '/accesos/') || str_contains($current, 'AccesoController.php');

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN
|--------------------------------------------------------------------------
*/
$isAdministracion = str_contains($current, '/usuarios/') || str_contains($current, '/instructores/');
$isUsuarios = str_contains($current, '/usuarios/');
$isEntrenadores = str_contains($current, '/instructores/');

/*
|--------------------------------------------------------------------------
| REPORTES
|--------------------------------------------------------------------------
*/
$isReportesIndex = str_contains($current, 'ReporteController.php?action=index');
$isReportesMembresias = str_contains($current, 'ReporteController.php?action=membresias');
$isReportesInventario = str_contains($current, 'ReporteController.php?action=inventario');
$isReportesVentas = str_contains($current, '/ventas/historial.php') || str_contains($current, 'VentaController.php?action=historial') || str_contains($current, 'ReporteController.php?action=ventas');
$isReportes = $isReportesIndex || $isReportesMembresias || $isReportesInventario || $isReportesVentas;

?>

<div class="sidebar">
    <div class="sidebar-top">

        <!-- LOGO DEL SISTEMA -->
        <div class="sidebar-logo-wrap">
            <img 
                src="/Impulso_Fitness/public/img/logo.png" 
                alt="Impulso Fitness"
                class="sidebar-logo-img"
            >
        </div>

        <div class="sidebar-user">
            <p class="sidebar-welcome">Bienvenido</p>
            <h4><?= htmlspecialchars($_SESSION['user']['username']) ?></h4>
            <small><?= htmlspecialchars($_SESSION['user']['role']) ?></small>
        </div>
    </div>

    <div class="sidebar-scroll">
        <p class="sidebar-section-title">MENÚ</p>

        <nav class="sidebar-menu">

            <!-- DASHBOARD -->
            <a href="/Impulso_Fitness/dashboard.php"
               class="menu-link <?= $isDashboard ? 'active-link' : '' ?>">
                <span>🏠</span> Dashboard
            </a>

            <!-- SOCIOS -->
            <div class="menu-block <?= $isSocios ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isSocios ? 'active-link' : '' ?>"
                    type="button"
                    data-target="socios-submenu"
                >
                    <span>👥</span> Socios
                </button>

                <div id="socios-submenu" class="sidebar-submenu <?= $isSocios ? 'open' : '' ?>">

                    <a href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=index"
                    class="submenu-link <?= $isSociosIndex ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-search"></i> Buscar socio
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=create"
                    class="submenu-link <?= $isSociosCreate ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-person-plus"></i> Nuevo socio
                    </a>

                </div>
            </div>

            <!-- MEMBRESÍAS -->
            <div class="menu-block <?= $isMembresias ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isMembresias ? 'active-link' : '' ?>"
                    type="button"
                    data-target="membresias-submenu"
                >
                    <span>💳</span> Membresías
                </button>

                <div id="membresias-submenu" class="sidebar-submenu <?= $isMembresias ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/MembresiaController.php?action=index"
                    class="submenu-link <?= $isMembresiasIndex ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-credit-card"></i> Registrar pago
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/MembresiaController.php?action=historial"
                    class="submenu-link <?= ($isMembresiasHistorial || $isMembresiasRecibo) ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-clock-history"></i> Historial de pagos
                    </a>
                </div>
            </div>

            <!-- VENTAS -->
            <div class="menu-block <?= $isVentas ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isVentas ? 'active-link' : '' ?>"
                    type="button"
                    data-target="ventas-submenu"
                >
                    <span>🛒</span> Ventas
                </button>

                <div id="ventas-submenu" class="sidebar-submenu <?= $isVentas ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/VentaController.php?action=index"
                       class="submenu-link <?= $isVentasIndex ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-basket"></i> Nueva venta
                    </a>
                </div>
            </div>

            <!-- INVENTARIO -->
            <div class="menu-block <?= $isInventario ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isInventario ? 'active-link' : '' ?>"
                    type="button"
                    data-target="inventario-submenu"
                >
                    <span>📦</span> Inventario
                </button>

                <div id="inventario-submenu" class="sidebar-submenu <?= $isInventario ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/InventarioController.php?action=productos"
                    class="submenu-link <?= $isProductos ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-box-seam"></i> Productos
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/InventarioController.php?action=entrada"
                    class="submenu-link <?= $isEntradaProductos ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-arrow-down-circle"></i> Entrada de productos
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/InventarioController.php?action=stockBajo"
                    class="submenu-link <?= $isStockBajo ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-exclamation-triangle"></i> Stock bajo
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/InventarioController.php?action=movimientos"
                    class="submenu-link <?= $isMovimientos ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-repeat"></i> Movimientos
                    </a>
                </div>
            </div>

            <!-- INGRESO DE PERSONAL -->
            <a href="<?= BASE_URL ?>/app/controllers/IngresoController.php?action=index"
               class="menu-link <?= $isIngresoPersonal ? 'active-link' : '' ?>">
                <span>🎫</span> Ingreso de personal
            </a>

            <!-- CONTROL DE ACCESO -->
            <div class="menu-block <?= $isControlAcceso ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isControlAcceso ? 'active-link' : '' ?>"
                    type="button"
                    data-target="acceso-submenu"
                >
                    <span>🚪</span> Control de acceso
                </button>

                <div id="acceso-submenu" class="sidebar-submenu <?= $isControlAcceso ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=index"
                       class="submenu-link <?= str_contains($current, 'AccesoController.php?action=index') ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-journal-check"></i> Registro de accesos
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=biometrico"
                       class="submenu-link <?= str_contains($current, 'AccesoController.php?action=biometrico') ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-fingerprint"></i> Biométrico
                    </a>
                </div>
            </div>

            <!-- ADMINISTRACIÓN -->
            <div class="menu-block <?= $isAdministracion ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isAdministracion ? 'active-link' : '' ?>"
                    type="button"
                    data-target="admin-submenu"
                >
                    <span>⚙️</span> Administración
                </button>

                <div id="admin-submenu" class="sidebar-submenu <?= $isAdministracion ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=index"
                       class="submenu-link <?= $isUsuarios ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-people"></i> Usuarios
                    </a>

                    <a href="/Impulso_Fitness/resources/views/instructores/index.php"
                       class="submenu-link <?= $isEntrenadores ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-person-badge"></i> Entrenadores
                    </a>
                </div>
            </div>

            <!-- REPORTES -->
            <div class="menu-block <?= $isReportes ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= $isReportes ? 'active-link' : '' ?>"
                    type="button"
                    data-target="reportes-submenu"
                >
                    <span>📊</span> Reportes
                </button>

                <div id="reportes-submenu" class="sidebar-submenu <?= $isReportes ? 'open' : '' ?>">
                    <a href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=index"
                       class="submenu-link <?= $isReportesIndex ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-bar-chart-line-fill"></i> Reporte general
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=membresias"
                       class="submenu-link <?= $isReportesMembresias ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-card-list"></i> Membresías
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=inventario"
                       class="submenu-link <?= $isReportesInventario ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-box-seam"></i> Inventario
                    </a>

                    <a href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=ventas"
                       class="submenu-link <?= $isReportesVentas ? 'active-submenu-link' : '' ?>">
                        <i class="bi bi-receipt"></i> Historial de ventas
                    </a>
                </div>
            </div>

            <!-- CERRAR SESIÓN -->
            <a href="/Impulso_Fitness/logout.php" class="menu-link logout-link">
                <span>🚪</span> Cerrar sesión
            </a>

        </nav>
    </div>

    <div class="sidebar-footer">
        <small>Rol: <?= htmlspecialchars($_SESSION['user']['role']) ?></small>
    </div>
</div>