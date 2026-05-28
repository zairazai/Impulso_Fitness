<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/reportes.css';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
<div class="reportes-module">
    <div class="layout-container">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">
            <section class="page-header">
                <div>
                    <h1><i class="bi bi-bar-chart-line-fill"></i> Reporte general</h1>
                    <p>Visión rápida del estado del gimnasio, ventas, membresías e inventario.</p>
                </div>
            </section>

            <section class="page-card card-section">
                <div class="report-cards report-cards-grid">
                    <article class="report-card card-primary">
                        <div class="card-icon"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <p>Socios registrados</p>
                        <strong><?= number_format((int)$resumen['total_socios']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-success">
                    <div class="card-icon"><i class="bi bi-person-check-fill"></i></div>
                    <div>
                        <p>Socios activos</p>
                        <strong><?= number_format((int)$resumen['socios_activos']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-accent">
                    <div class="card-icon"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <p>Productos activos</p>
                        <strong><?= number_format((int)$resumen['productos_activos']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-warning">
                    <div class="card-icon"><i class="bi bi-stack"></i></div>
                    <div>
                        <p>Stock total</p>
                        <strong><?= number_format((int)$resumen['stock_total']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-info">
                    <div class="card-icon"><i class="bi bi-basket3"></i></div>
                    <div>
                        <p>Ventas registradas</p>
                        <strong><?= number_format((int)$resumen['total_ventas']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-primary-alt">
                    <div class="card-icon"><i class="bi bi-currency-dollar"></i></div>
                    <div>
                        <p>Ingreso por ventas</p>
                        <strong>$<?= number_format((float)$resumen['ventas_totales'], 2) ?></strong>
                    </div>
                </article>

                <article class="report-card card-secondary">
                    <div class="card-icon"><i class="bi bi-credit-card-2-back-fill"></i></div>
                    <div>
                        <p>Pagos de membresía</p>
                        <strong><?= number_format((int)$resumen['pagos_membresia']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-success-alt">
                    <div class="card-icon"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <p>Ingreso membresías</p>
                        <strong>$<?= number_format((float)$resumen['ingresos_membresia'], 2) ?></strong>
                    </div>
                </article>
            </div>
            </section>
        </main>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
