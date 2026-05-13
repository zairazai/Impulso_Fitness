<?php

/*
|--------------------------------------------------------------------------
| MIDDLEWARE DE AUTENTICACIÓN
|--------------------------------------------------------------------------
| Protege la vista para que solo usuarios con sesión puedan entrar.
*/
require_once __DIR__ . '/../../../app/middleware/auth.php';

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN GLOBAL
|--------------------------------------------------------------------------
| Cargamos BASE_URL para usar rutas centralizadas.
*/
require_once __DIR__ . '/../../../config/app.php';

/*
|--------------------------------------------------------------------------
| CSS EXTRA DEL MÓDULO
|--------------------------------------------------------------------------
*/
$extraCss = BASE_URL . "/public/css/inventario.css";

/*
|--------------------------------------------------------------------------
| HEADER GLOBAL
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../layouts/header.php';

?>

<!DOCTYPE html>
<html lang="es">
<body>

<div class="inventario-module">

    <div class="layout-container">

        <!-- SIDEBAR -->
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">

            <!-- ENCABEZADO -->
            <section class="page-header">

                <div>
                    <h1>
                        <i class="bi bi-box-seam"></i>
                        Inventario de Productos
                    </h1>

                    <p>
                        Gestiona productos, stock y disponibilidad.
                    </p>
                </div>

                <button class="btn-primary" id="btnNuevoProducto">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo Producto
                </button>

            </section>

            <!-- ALERTAS -->
            <?php if (isset($_GET['created'])): ?>
                <div class="alert success">
                    Producto registrado correctamente.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['updated'])): ?>
                <div class="alert success">
                    Producto actualizado correctamente.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert success">
                    Producto dado de baja correctamente.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert danger">
                    Ocurrió un problema al procesar la solicitud.
                </div>
            <?php endif; ?>

            <!-- BÚSQUEDA -->
            <section class="card-section">

                <form method="GET" action="<?= BASE_URL ?>/routes/inventario_mostrar_productos.php" class="search-form">

                    <div class="input-icon">
                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="buscar"
                            placeholder="Buscar producto..."
                            value="<?= htmlspecialchars($busqueda) ?>"
                        >
                    </div>

                    <button type="submit" class="btn-secondary">
                        Buscar
                    </button>

                </form>

            </section>

            <!-- TABLA DE PRODUCTOS -->
            <section class="card-section">

                <div class="table-container">

                    <table class="table-custom">

                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($productos)): ?>

                            <?php foreach ($productos as $producto): ?>

                                <?php
                                    $estado = $producto['estado_stock'] ?? 'Stock OK';

                                    $claseEstado = 'badge-success';

                                    if ($estado === 'Por agotarse') {
                                        $claseEstado = 'badge-warning';
                                    }

                                    if ($estado === 'Agotado') {
                                        $claseEstado = 'badge-danger';
                                    }
                                ?>

                                <tr>

                                    <td>
                                        <div class="product-info">

                                            <div class="product-icon">
                                                <i class="bi <?= htmlspecialchars($producto['icono'] ?? 'bi-box-seam') ?>"></i>
                                            </div>

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars($producto['nombre']) ?>
                                                </strong>

                                                <small>
                                                    <?= htmlspecialchars($producto['codigo']) ?>
                                                </small>
                                            </div>

                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($producto['categoria']) ?>
                                    </td>

                                    <td>
                                        $<?= number_format((float)$producto['precio_venta'], 2) ?>
                                    </td>

                                    <td>
                                        <?= (int)$producto['stock'] ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $claseEstado ?>">
                                            <?= htmlspecialchars($estado) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="actions">

                                            <!-- BOTÓN EDITAR -->
                                            <button
                                                type="button"
                                                class="btn-icon edit-product-btn"
                                                title="Editar"

                                                data-id="<?= (int)$producto['id'] ?>"
                                                data-codigo="<?= htmlspecialchars($producto['codigo']) ?>"
                                                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                                data-categoria="<?= htmlspecialchars($producto['categoria']) ?>"
                                                data-descripcion="<?= htmlspecialchars($producto['descripcion'] ?? '') ?>"
                                                data-costo-compra="<?= htmlspecialchars($producto['costo_compra'] ?? 0) ?>"
                                                data-precio-venta="<?= htmlspecialchars($producto['precio_venta']) ?>"
                                                data-stock="<?= (int)$producto['stock'] ?>"
                                                data-stock-minimo="<?= (int)$producto['stock_minimo'] ?>"
                                                data-icono="<?= htmlspecialchars($producto['icono'] ?? 'bi-box-seam') ?>"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- FORMULARIO BAJA LÓGICA -->
                                            <form
                                                method="POST"
                                                action="<?= BASE_URL ?>/routes/inventario_delete.php"
                                                class="delete-form"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int)$producto['id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-icon danger"
                                                    title="Eliminar"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="empty-table">
                                    No se encontraron productos.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

    <!-- MODAL PRODUCTO -->
    <div class="modal-overlay" id="modalProducto">

        <div class="modal-card">

            <div class="modal-header">

                <h2>
                    <i class="bi bi-box-seam"></i>
                    Producto
                </h2>

                <button type="button" class="modal-close" id="btnCerrarModal">
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>

            <form
                method="POST"
                action="<?= BASE_URL ?>/routes/inventario_store.php"
                class="form-grid"
            >

                <input type="hidden" name="id" id="producto_id" value="0">

                <div class="form-group">
                    <label>Código</label>
                    <input type="text"
                    name="codigo"
                    id="codigo"
                    value="Automático"
                    readonly
                    >
                </div>

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Proteína Whey">
                </div>

                <div class="form-group">
                    <label>Categoría</label>
                    <select name="categoria" id="categoria" required>
                        <option value="">Seleccionar</option>
                        <option value="Suplementos">Suplementos</option>
                        <option value="Bebidas">Bebidas</option>
                        <option value="Snacks">Snacks</option>
                        <option value="Accesorios">Accesorios</option>
                        <option value="Ropa">Ropa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Icono</label>
                    <select name="icono" id="icono">
                        <option value="bi-box-seam">Producto general</option>
                        <option value="bi-capsule">Suplemento</option>
                        <option value="bi-cup-straw">Bebida</option>
                        <option value="bi-bag">Accesorio</option>
                        <option value="bi-lightning-charge">Energía</option>
                        <option value="bi-droplet">Agua</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Costo compra</label>
                    <input type="number" step="0.01" name="costo_compra" id="costo_compra" value="0">
                </div>

                <div class="form-group">
                    <label>Precio venta</label>
                    <input type="number" step="0.01" name="precio_venta" id="precio_venta" required>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" id="stock" value="0" min="0">
                </div>

                <div class="form-group">
                    <label>Stock mínimo</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" value="5" min="0">
                </div>

                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="3"
                        placeholder="Descripción opcional del producto"
                    ></textarea>
                </div>

                <div class="modal-actions full">

                    <button type="button" class="btn-secondary" id="btnCancelarProducto">
                        Cancelar
                    </button>

                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>

    </div>

    <script src="<?= BASE_URL ?>/public/js/inventario.js"></script>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
