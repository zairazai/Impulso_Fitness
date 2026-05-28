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
                    <h1><i class="bi bi-cart4"></i> Nueva venta</h1>
                    <p>Vende a público general o a un socio. Selecciona productos, define cantidades y completa la venta.</p>
                </div>
            </section>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'venta'): ?>
                <div class="alert success">La venta se registró correctamente.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert danger">
                    Ocurrió un error al generar la venta. Verifica los datos e intenta nuevamente.
                    <?php if (!empty($_GET['reason'])): ?>
                        <div style="margin-top: 0.75rem; font-size: 0.95rem; color: #fde68a;">
                            <?= htmlspecialchars(urldecode($_GET['reason'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <section class="page-card card-section ventas-grid">
                <div class="product-panel">
                    <div class="panel-header">
                        <h2>Productos disponibles</h2>
                        <form method="GET" action="<?= BASE_URL ?>/app/controllers/VentaController.php" class="filter-form">
                            <input type="hidden" name="action" value="index">
                            <input type="text" name="buscar" class="custom-input" placeholder="Buscar producto..." value="<?= htmlspecialchars($busqueda) ?>">
                            <button type="submit" class="btn-secondary">Buscar</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Acción</th>
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
                                            <td>$<?= number_format((float)$producto['precio_venta'], 2) ?></td>
                                            <td><?= (int)$producto['stock'] ?></td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn-secondary btn-add-cart"
                                                    data-producto-id="<?= (int)$producto['id'] ?>"
                                                    data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                                    data-precio="<?= htmlspecialchars($producto['precio_venta']) ?>"
                                                >Agregar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="empty-table">No hay productos disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cart-panel">
                    <div class="panel-header">
                        <h2>Resumen de venta</h2>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/app/controllers/VentaController.php?action=store" id="formVenta">
                        <input type="hidden" name="items_json" id="items_json" value="[]">
                        <input type="hidden" name="total" id="venta_total" value="0">

                        <div class="form-group">
                            <label>Cliente</label>
                            <select name="socio_id" class="custom-input">
                                <option value="0">Cliente general</option>
                                <?php foreach ($socios as $socio): ?>
                                    <option value="<?= (int)$socio['id'] ?>"><?= htmlspecialchars($socio['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Método de pago</label>
                            <select name="metodo_pago" class="custom-input" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>

                        <div class="cart-items" id="cartItems">
                            <div class="empty-cart">Agrega productos al carrito para iniciar la venta.</div>
                        </div>

                        <div class="cart-footer">
                            <div>
                                <span>Total</span>
                                <strong id="cartTotal">$0.00</strong>
                            </div>

                            <button type="submit" class="btn-primary btn-block">Completar venta</button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/ventas.js"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
