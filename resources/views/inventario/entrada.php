<?php

/*
|--------------------------------------------------------------------------
| MIDDLEWARE DE AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../../../app/middleware/auth.php';

/*
|--------------------------------------------------------------------------
| MODELO INVENTARIO
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../../../app/models/Inventario.php';

/*
|--------------------------------------------------------------------------
| OBTENER PRODUCTOS ACTIVOS
|--------------------------------------------------------------------------
*/
$inventario = new Inventario();
$productos = $inventario->listarProductos('');
?>

<!DOCTYPE html>
<html lang="es">

<?php

$extraCss = "/Impulso_Fitness/public/css/inventario.css";

require_once __DIR__ . '/../layouts/header.php';

?>

<body>

<div class="inventario-module">

    <div class="layout-container">

        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">

            <section class="page-header">
                <div>
                    <h1>
                        <i class="bi bi-box-arrow-in-down"></i>
                        Entrada de productos
                    </h1>

                    <p>
                        Registra entradas, salidas o ajustes de inventario.
                    </p>
                </div>

                <a href="/Impulso_Fitness/resources/views/inventario/productos.php" class="btn-secondary">
                    <i class="bi bi-box-seam"></i>
                    Ver productos
                </a>
            </section>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert danger">
                    Revisa los datos del movimiento.
                </div>
            <?php endif; ?>

            <section class="card-section">

                <form
                    method="POST"
                    action="/Impulso_Fitness/routes/inventario_movimiento_store.php"
                    class="form-grid"
                >

                    <div class="form-group full">
                        <label>Producto</label>

                        <select name="producto_id" required>
                            <option value="">Seleccionar producto</option>

                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= (int)$producto['id'] ?>">
                                    <?= htmlspecialchars($producto['codigo']) ?> -
                                    <?= htmlspecialchars($producto['nombre']) ?>
                                    | Stock actual: <?= (int)$producto['stock'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipo de movimiento</label>

                        <select name="tipo" required>
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad</label>

                        <input
                            type="number"
                            name="cantidad"
                            min="1"
                            required
                            placeholder="Ej. 10"
                        >
                    </div>

                    <div class="form-group full">
                        <label>Referencia</label>

                        <input
                            type="text"
                            name="referencia"
                            placeholder="Ej. Compra proveedor, venta manual, ajuste físico"
                        >
                    </div>

                    <div class="form-group full">
                        <label>Observaciones</label>

                        <textarea
                            name="observaciones"
                            rows="4"
                            placeholder="Notas opcionales del movimiento"
                        ></textarea>
                    </div>

                    <div class="modal-actions full">
                        <a href="/Impulso_Fitness/resources/views/inventario/productos.php" class="btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn-primary">
                            <i class="bi bi-save"></i>
                            Registrar movimiento
                        </button>
                    </div>

                </form>

            </section>

        </main>

    </div>

    <script src="/Impulso_Fitness/public/js/inventario.js"></script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>