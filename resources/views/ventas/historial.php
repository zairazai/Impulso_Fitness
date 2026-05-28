<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/ventas.css';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
<div class="ventas-module">
    <div class="layout-container">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">
            <section class="page-header">
                <div>
                    <h1><i class="bi bi-box-seam"></i> Historial de ventas</h1>
                    <p>Consulta las ventas registradas y filtra por fecha o cliente.</p>
                </div>
            </section>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'venta'): ?>
                <div class="alert success">Venta registrada correctamente.</div>
            <?php endif; ?>

            <section class="page-card card-section">
                <form method="GET" action="<?= BASE_URL ?>/app/controllers/VentaController.php" class="search-form">
                    <input type="hidden" name="action" value="historial">

                    <div class="input-grid ventas-filter-grid">
                        <input type="text" name="buscar" placeholder="Buscar ID, cliente o usuario" value="<?= htmlspecialchars($buscar ?? '') ?>">
                        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                        <button type="submit" class="btn-secondary">
                            <i class="bi bi-funnel-fill"></i>
                            Filtrar
                        </button>
                        <a href="<?= BASE_URL ?>/app/controllers/VentaController.php?action=exportarHistorial&buscar=<?= urlencode($buscar ?? '') ?>&fecha_inicio=<?= urlencode($fechaInicio ?? '') ?>&fecha_fin=<?= urlencode($fechaFin ?? '') ?>" class="btn btn-outline-success ventas-export-btn">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            Descargar Excel
                        </a>
                    </div>
                </form>
            </section>

            <section class="page-card card-section">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Usuario</th>
                                <th>Método</th>
                                <th>Total</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ventas)): ?>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td>#<?= (int)$venta['id'] ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($venta['fecha']))) ?></td>
                                        <td><?= htmlspecialchars($venta['socio']) ?></td>
                                        <td><?= htmlspecialchars($venta['usuario']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($venta['metodo_pago'])) ?></td>
                                        <td>$<?= number_format((float)$venta['total'], 2) ?></td>
                                        <td>
                                            <a
                                                href="<?= BASE_URL ?>/app/controllers/VentaController.php?action=recibo&id=<?= (int)$venta['id'] ?>"
                                                class="btn btn-secondary btn-sm"
                                                target="_blank"
                                            >
                                                <i class="bi bi-printer"></i>
                                                Reimprimir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-table">No se encontraron ventas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
