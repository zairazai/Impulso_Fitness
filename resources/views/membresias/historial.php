<?php

/*
|--------------------------------------------------------------------------
| MIDDLEWARE DE AUTENTICACIÓN
|--------------------------------------------------------------------------
| Validamos que el usuario tenga sesión activa antes de cargar la vista.
| Esto evita repetir la lógica de sesión en cada archivo.
*/
require_once __DIR__ . '/../../../app/middleware/auth.php';

/*
|--------------------------------------------------------------------------
| MODELO
|--------------------------------------------------------------------------
| Cargamos el modelo Membresia para obtener el historial de pagos.
*/
require_once __DIR__ . '/../../../app/models/Membresia.php';

$modelo = new Membresia();

/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADOS DE MEMBRESÍAS
|--------------------------------------------------------------------------
| Antes de mostrar el historial actualizamos estados para que la información
| esté sincronizada con las fechas de inicio y fin.
*/
$modelo->actualizarEstadosMembresias();

/*
|--------------------------------------------------------------------------
| HISTORIAL DE PAGOS
|--------------------------------------------------------------------------
| Recuperamos todos los pagos registrados.
*/
$historial = $modelo->historialPagos();

?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

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
            <div class="alert alert-danger">
                Ocurrió un problema al cargar la información solicitada.
            </div>
        <?php endif; ?>

        <!-- ========================================================= -->
        <!-- FILTROS VISUALES                                          -->
        <!-- ========================================================= -->
        <div class="page-card card-section mb-4">
            <div class="historial-filters">
                <div class="historial-filter-group">
                    <label for="buscarPagoInput" class="form-label">Buscar socio</label>
                    <input
                        type="text"
                        id="buscarPagoInput"
                        class="form-control custom-input"
                        placeholder="Juan Pérez"
                    >
                </div>

                <div class="historial-filter-group">
                    <label for="rangoFechas" class="form-label">Intervalo de fechas</label>
                    <input
                        type="text"
                        id="rangoFechas"
                        class="form-control custom-input"
                        placeholder="01-04-2026 a 30-04-2026"
                    >
                </div>

                <div class="historial-filter-actions">
                    <button type="button" class="btn btn-outline-info">
                        Exportar
                    </button>

                    <button type="button" class="btn btn-primary">
                        Filtrar
                    </button>
                </div>
            </div>
        </div>

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
                                    <td><?= htmlspecialchars($pago['socio'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($pago['fecha_pago'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(ucfirst($pago['metodo_pago'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars($pago['membresia'] ?? '-') ?></td>
                                    <td>$<?= number_format((float)($pago['monto'] ?? 0), 2) ?></td>
                                    <td>
                                        <a
                                            href="<?= BASE_URL ?>/resources/views/membresias/recibo.php?id=<?= urlencode($pago['id']) ?>"
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