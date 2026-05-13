<?php

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
$isSocios = str_contains($current, '/socios/');
$isSociosCreate = str_contains($current, '/socios/create.php');
$isSociosIndex = str_contains($current, '/socios/index.php');
$isSociosEdit = str_contains($current, '/socios/edit.php');

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
$isVentas = str_contains($current, '/ventas/');
$isVentasIndex = str_contains($current, '/ventas/index.php');
$isVentasHistorial = str_contains($current, '/ventas/historial.php');

/*
|--------------------------------------------------------------------------
| INVENTARIO
|--------------------------------------------------------------------------
*/
$isInventario = str_contains($current, '/inventario/') || str_contains($current, '/inventarios/');
$isProductos = str_contains($current, '/inventario/productos.php');
$isEntradaProductos = str_contains($current, '/entrada');
$isMovimientos = str_contains($current, '/movimientos');
/*
|--------------------------------------------------------------------------
| CONTROL DE ACCESO
|--------------------------------------------------------------------------
*/
$isControlAcceso = str_contains($current, '/accesos/') || str_contains($current, '/control-acceso/');

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
$isReportes = str_contains($current, '/reportes/');

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
                    <a href="/Impulso_Fitness/resources/views/socios/index.php"
                       class="submenu-link <?= ($isSociosIndex || $isSociosEdit) ? 'active-submenu-link' : '' ?>">
                        Buscar socio
                    </a>

                    <a href="/Impulso_Fitness/resources/views/socios/create.php"
                       class="submenu-link <?= $isSociosCreate ? 'active-submenu-link' : '' ?>">
                        Nuevo socio
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
                    <a href="/Impulso_Fitness/resources/views/membresias/index.php"
                       class="submenu-link <?= $isMembresiasIndex ? 'active-submenu-link' : '' ?>">
                        Registrar pago
                    </a>

                    <a href="/Impulso_Fitness/resources/views/membresias/historial.php"
                       class="submenu-link <?= ($isMembresiasHistorial || $isMembresiasRecibo) ? 'active-submenu-link' : '' ?>">
                        Historial de pagos
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
                    <a href="/Impulso_Fitness/resources/views/ventas/index.php"
                       class="submenu-link <?= $isVentasIndex ? 'active-submenu-link' : '' ?>">
                        Punto de venta
                    </a>

                    <a href="/Impulso_Fitness/resources/views/ventas/historial.php"
                       class="submenu-link <?= $isVentasHistorial ? 'active-submenu-link' : '' ?>">
                        Historial de ventas
                    </a>
                </div>
            </div>

            <!-- INVENTARIO -->
            <div class="menu-block <?= ($isInventario || $isProductos) ? 'active-block' : '' ?>">
                <button
                    class="menu-link sidebar-toggle-btn <?= ($isInventario || $isProductos) ? 'active-link' : '' ?>"
                    type="button"
                    data-target="inventario-submenu"
                >
                    <span>📦</span> Inventario
                </button>

                <div id="inventario-submenu" class="sidebar-submenu <?= ($isInventario || $isProductos) ? 'open' : '' ?>">
                    <a href="/Impulso_Fitness/routes/inventario_mostrar_productos.php"
                       class="submenu-link <?= $isProductos ? 'active-submenu-link' : '' ?>">
                        Productos
                    </a>

                    <a href="/Impulso_Fitness/resources/views/inventario/entrada.php"
                       class="submenu-link <?= $isEntradaProductos ? 'active-submenu-link' : '' ?>">
                        Entrada de productos
                    </a>

                    <a href="/Impulso_Fitness/resources/views/inventario/movimientos.php"
                       class="submenu-link <?= $isMovimientos ? 'active-submenu-link' : '' ?>">
                        Movimientos
                    </a>

                </div>
            </div>

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
                    <a href="/Impulso_Fitness/resources/views/accesos/index.php"
                       class="submenu-link">
                        Registro de accesos
                    </a>

                    <a href="/Impulso_Fitness/resources/views/accesos/biometrico.php"
                       class="submenu-link">
                        Biométrico
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
                    <a href="/Impulso_Fitness/resources/views/usuarios/index.php"
                       class="submenu-link <?= $isUsuarios ? 'active-submenu-link' : '' ?>">
                        Usuarios
                    </a>

                    <a href="/Impulso_Fitness/resources/views/instructores/index.php"
                       class="submenu-link <?= $isEntrenadores ? 'active-submenu-link' : '' ?>">
                        Entrenadores
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
                    <a href="/Impulso_Fitness/resources/views/reportes/index.php"
                       class="submenu-link">
                        Reporte general
                    </a>

                    <a href="/Impulso_Fitness/resources/views/reportes/membresias.php"
                       class="submenu-link">
                        Membresías
                    </a>

                    <a href="/Impulso_Fitness/resources/views/reportes/inventario.php"
                       class="submenu-link">
                        Inventario
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