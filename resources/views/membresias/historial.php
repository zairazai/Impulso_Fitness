<?php


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN Y SEGURIDAD EXTRA
|--------------------------------------------------------------------------
| Cargamos la configuración general y mantenemos una validación adicional
| de sesión como respaldo del flujo del controlador.
*/
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . "/public/css/membresias.css";

include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

?>


<div class="main-content">
    <div class="container-fluid px-0">

        <!-- ========================================================= -->
        <!-- ENCABEZADO DEL MÓDULO                                     -->
        <!-- ========================================================= -->
        <div class="module-title-bar">
            <h1>Historial de Pagos de Membresía</h1>
        </div>

        <!-- ========================================================= -->
        <!-- ALERTAS                                                   -->
        <!-- ========================================================= -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert danger">
                Ocurrió un problema al cargar la información solicitada.
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- ========================================================= -->
        <!-- Form historial                                         -->
        <!-- ========================================================= -->
        <form method="GET" action="<?= BASE_URL ?>/app/controllers/MembresiaController.php">

        <input type="hidden" name="action" value="historial">

        <div class="page-card card-section mb-4">

            <div class="historial-filters">

                <div class="historial-filter-group">

                    <label for="buscarPagoInput" class="form-label">
                        Buscar socio
                    </label>

                    <input
                        type="text"
                        id="buscarPagoInput"
                        name="buscar"
                        class="form-control custom-input"
                        placeholder="Juan Pérez"
                        value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                    >

                </div>

                <div class="historial-filter-group">

                    <label class="form-label">
                        Fecha inicio
                    </label>

                    <input
                        type="date"
                        name="fecha_inicio"
                        class="form-control custom-input"
                        value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>"
                    >

                </div>

                <div class="historial-filter-group">

                    <label class="form-label">
                        Fecha fin
                    </label>

                    <input
                        type="date"
                        name="fecha_fin"
                        class="form-control custom-input"
                        value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>"
                    >

                </div>

                <div class="historial-filter-actions">

                    <a href="<?= BASE_URL ?>/app/controllers/MembresiaController.php?action=exportarHistorial&buscar=<?= urlencode($_GET['buscar'] ?? '') ?>&fecha_inicio=<?= urlencode($_GET['fecha_inicio'] ?? '') ?>&fecha_fin=<?= urlencode($_GET['fecha_fin'] ?? '') ?>" class="btn btn-outline-info">
                        <i class="bi bi-file-earmark-excel"></i> Exportar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Filtrar
                    </button>

                </div>

            </div>

        </div>

    </form>

        <!-- ========================================================= -->
        <!-- TABLA DE HISTORIAL                                        -->
        <!-- ========================================================= -->
        <div class="page-card historial-table-card">
            <div class="table-responsive">
                <table class="table table-dark table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Fecha del Pago</th>
                            <th>Método de Pago</th>
                            <th>Membresía</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="historialBody">
                        <?php if (!empty($historial)): ?>
                            <?php foreach ($historial as $pago): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pago['socio_nombre'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($pago['fecha_pago'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(ucfirst($pago['metodo_pago'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars($pago['membresia_nombre'] ?? '-') ?></td>
                                    <td>$<?= number_format((float)($pago['monto'] ?? 0), 2) ?></td>
                                    <td>
                                        <a
                                            href="<?= BASE_URL ?>/app/controllers/MembresiaController.php?action=recibo&id=<?= urlencode($pago['id']) ?>"
                                            class="btn btn-outline-info btn-sm"
                                        >
                                            Recibo
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No hay pagos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================= -->
<!-- JS SIMPLE PARA BÚSQUEDA VISUAL LOCAL                      -->
<!-- ========================================================= -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buscarPagoInput = document.getElementById('buscarPagoInput');
    const historialBody = document.getElementById('historialBody');

    if (buscarPagoInput && historialBody) {
        buscarPagoInput.addEventListener('keyup', () => {
            const filtro = buscarPagoInput.value.toLowerCase();
            const filas = historialBody.querySelectorAll('tr');

            filas.forEach(fila => {
                const texto = fila.innerText.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>