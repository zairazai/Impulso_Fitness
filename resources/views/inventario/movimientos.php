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
| CONSULTAR MOVIMIENTOS
|--------------------------------------------------------------------------
*/
$inventario = new Inventario();

$busqueda = $_GET['buscar'] ?? '';
$movimientos = $inventario->listarMovimientos($busqueda);

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
                        <i class="bi bi-arrow-left-right"></i>
                        Movimientos de inventario
                    </h1>

                    <p>
                        Consulta entradas, salidas y ajustes realizados.
                    </p>
                </div>

                <a href="/Impulso_Fitness/resources/views/inventario/entrada.php" class="btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo movimiento
                </a>
            </section>

            <?php if (isset($_GET['movimiento'])): ?>
                <div class="alert success">
                    Movimiento registrado correctamente.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert danger">
                    Ocurrió un problema al cargar los movimientos.
                </div>
            <?php endif; ?>

            <section class="card-section">

                <form method="GET" class="search-form">

                    <div class="input-icon">
                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="buscar"
                            placeholder="Buscar por producto, código o tipo..."
                            value="<?= htmlspecialchars($busqueda) ?>"
                        >
                    </div>

                    <button type="submit" class="btn-secondary">
                        Buscar
                    </button>

                </form>

            </section>

            <section class="card-section">

                <div class="table-container">

                    <table class="table-custom">

                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Referencia</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($movimientos)): ?>

                            <?php foreach ($movimientos as $movimiento): ?>

                                <?php
                                    $tipo = $movimiento['tipo'] ?? '';

                                    $badgeTipo = 'badge-success';

                                    if ($tipo === 'salida') {
                                        $badgeTipo = 'badge-danger';
                                    }

                                    if ($tipo === 'ajuste') {
                                        $badgeTipo = 'badge-warning';
                                    }
                                ?>

                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($movimiento['producto']) ?>
                                        </strong>

                                        <small class="table-muted">
                                            <?= htmlspecialchars($movimiento['codigo']) ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimiento['categoria']) ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $badgeTipo ?>">
                                            <?= ucfirst(htmlspecialchars($tipo)) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= (int)$movimiento['cantidad'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimiento['referencia'] ?? 'Sin referencia') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimiento['fecha']) ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="empty-table">
                                    No hay movimientos registrados.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

    <script src="/Impulso_Fitness/public/js/inventario.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>