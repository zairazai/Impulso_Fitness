<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /Impulso_Fitness/resources/views/auth/login.php");
    exit;
}
?>

<?php include __DIR__ . '/resources/views/layouts/header.php'; ?>

<link href="/Impulso_Fitness/public/css/dashboard.css" rel="stylesheet">

<?php include __DIR__ . '/resources/views/layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h1>Bienvenida, <?= htmlspecialchars($_SESSION['user']['username']) ?> 👋</h1>
        <p>Ya estás dentro del sistema Impulso Fitness.</p>

        <div class="stats-grid">
            <div class="stat-box">
                <h4>Socios activos</h4>
                <p>0</p>
            </div>

            <div class="stat-box">
                <h4>Ventas del día</h4>
                <p>0</p>
            </div>

            <div class="stat-box">
                <h4>Productos</h4>
                <p>0</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/resources/views/layouts/footer.php'; ?>