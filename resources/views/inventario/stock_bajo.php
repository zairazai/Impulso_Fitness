<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/inventario.css';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
<div class="inventario-module">
    <div class="layout-container">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">
            <section class="page-header">
                <div>
                    <h1><i class="bi bi-exclamation-triangle"></i> Productos con stock bajo</h1>
                    <p>Revisa los productos que necesitan reposición inmediata.</p>
                </div>
            </section>

            <section class="page-card card-section">
                <div class="table-container">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Stock mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <i class="bi <?= htmlspecialchars($producto['icono'] ?? 'bi-box-seam') ?> product-icon"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                                    <small><?= htmlspecialchars($producto['codigo']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($producto['categoria']) ?></td>
                                        <td><?= (int)$producto['stock'] ?></td>
                                        <td><?= (int)$producto['stock_minimo'] ?></td>
                                        <td>
                                            <span class="badge badge-warning">Bajo</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-table">No hay productos con stock bajo.</td>
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
