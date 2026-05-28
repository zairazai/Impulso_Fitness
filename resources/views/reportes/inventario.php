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
                    <h1><i class="bi bi-box-seam"></i> Reporte de inventario</h1>
                    <p>Monitorea el stock disponible y los productos con alerta por stock bajo.</p>
                </div>

                <a
                    href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=exportarInventario&buscar=<?= urlencode($buscar) ?>"
                    class="btn btn-outline-success"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Exportar a Excel
                </a>
            </section>

            <section class="filter-panel">
                <form method="GET" action="<?= BASE_URL ?>/app/controllers/ReporteController.php" class="filter-form">
                    <input type="hidden" name="action" value="inventario">

                    <div class="filter-row">
                        <input aria-label="Buscar productos" class="custom-input" type="text" name="buscar" placeholder="Buscar producto, código o categoría..." value="<?= htmlspecialchars($buscar) ?>">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </section>

            <section class="report-cards">
                <article class="report-card">
                    <h3>Productos activos</h3>
                    <strong><?= number_format((int)$resumen['productos_activos']) ?></strong>
                </article>

                <article class="report-card">
                    <h3>Stock total</h3>
                    <strong><?= number_format((int)$resumen['stock_total']) ?></strong>
                </article>

                <article class="report-card">
                    <h3>Valor inventario</h3>
                    <strong>$<?= number_format((float)$resumen['valor_inventario'], 2) ?></strong>
                </article>

                <article class="report-card">
                    <h3>Stock bajo</h3>
                    <strong><?= number_format((int)$resumen['productos_stock_bajo']) ?></strong>
                </article>

                <article class="report-card">
                    <h3>Agotados</h3>
                    <strong><?= number_format((int)$resumen['productos_agotados']) ?></strong>
                </article>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Productos en inventario</h2>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Mínimo</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $producto): ?>
                                    <?php 
                                        $clase = '';
                                        if ((int)$producto['stock'] === 0) {
                                            $clase = 'stock-critical';
                                        } elseif ((int)$producto['stock'] <= (int)$producto['stock_minimo']) {
                                            $clase = 'stock-low';
                                        }
                                    ?>
                                    <tr class="<?= $clase ?>">
                                        <td><?= (int)$producto['id'] ?></td>
                                        <td><?= htmlspecialchars($producto['codigo']) ?></td>
                                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                        <td><?= htmlspecialchars($producto['categoria']) ?></td>
                                        <td><?= (int)$producto['stock'] ?></td>
                                        <td><?= (int)$producto['stock_minimo'] ?></td>
                                        <td>$<?= number_format((float)$producto['precio_venta'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-table">No se encontró ningún producto.</td>
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
