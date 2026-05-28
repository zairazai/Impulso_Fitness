<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php");
    exit;
}

require_once __DIR__ . '/app/models/Dashboard.php';

$dashboard = new Dashboard();
$sociosActivos = $dashboard->contarSociosActivos();
$ventasDelDia = $dashboard->contarVentasDelDia();
$productosActivos = $dashboard->contarProductosActivos();
?>

<?php include __DIR__ . '/resources/views/layouts/header.php'; ?>

<link href="/Impulso_Fitness/public/css/dashboard.css" rel="stylesheet">

<?php include __DIR__ . '/resources/views/layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h1>Hola, <?= htmlspecialchars($_SESSION['user']['username']) ?> 👋</h1>
        <p>Ya estás dentro del sistema Impulso Fitness.</p>

        <div class="stats-grid">
            <div class="stat-box">
                <h4>Socios activos</h4>
                <p><?= $sociosActivos ?></p>
            </div>

            <div class="stat-box">
                <h4>Ventas del día</h4>
                <p><?= $ventasDelDia ?></p>
            </div>

            <div class="stat-box">
                <h4>Productos</h4>
                <p><?= $productosActivos ?></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/resources/views/layouts/footer.php'; ?>