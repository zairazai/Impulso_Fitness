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
                    <h1><i class="bi bi-receipt"></i> Historial de ventas</h1>
                    <p>Descarga ventas por periodo y visualiza el historial completo de ventas.</p>
                </div>

                <a
                    href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=exportarVentas&buscar=<?= urlencode($buscar) ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>"
                    class="btn btn-outline-success"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Exportar a Excel
                </a>
            </section>

            <section class="filter-panel">
                <form method="GET" action="<?= BASE_URL ?>/app/controllers/ReporteController.php" class="filter-form">
                    <input type="hidden" name="action" value="ventas">

                    <div class="filter-row">
                        <div style="flex: 1; position: relative;">
                            <input 
                                type="text" 
                                name="buscar" 
                                id="buscar-ventas" 
                                placeholder="Buscar ID, cliente o usuario..." 
                                value="<?= htmlspecialchars($buscar) ?>"
                                autocomplete="off"
                            >
                            <div id="dropdown-ventas" class="dropdown-results" style="display: none;"></div>
                        </div>
                        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                        <button type="submit" class="btn-primary">Filtrar</button>
                    </div>
                </form>
            </section>

            <section class="report-cards">
                <?php $totalVentas = array_sum(array_column($ventas, 'total')); ?>
                <article class="report-card card-primary">
                    <div class="card-icon"><i class="bi bi-basket3"></i></div>
                    <div>
                        <p>Ventas encontradas</p>
                        <strong><?= number_format(count($ventas)) ?></strong>
                    </div>
                </article>

                <article class="report-card card-secondary">
                    <div class="card-icon"><i class="bi bi-currency-dollar"></i></div>
                    <div>
                        <p>Ingreso total</p>
                        <strong>$<?= number_format((float)$totalVentas, 2) ?></strong>
                    </div>
                </article>

                <article class="report-card card-info">
                    <div class="card-icon"><i class="bi bi-calendar2-week"></i></div>
                    <div>
                        <p>Periodo seleccionado</p>
                        <strong><?= htmlspecialchars($fechaInicio ?? 'Inicio') ?> - <?= htmlspecialchars($fechaFin ?? 'Fin') ?></strong>
                    </div>
                </article>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Ventas</h2>
                </div>

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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ventas)): ?>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?= (int)$venta['id'] ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($venta['fecha']))) ?></td>
                                        <td><?= htmlspecialchars($venta['socio']) ?></td>
                                        <td><?= htmlspecialchars($venta['usuario']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($venta['metodo_pago'])) ?></td>
                                        <td>$<?= number_format((float)$venta['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-table">No se encontraron ventas en este periodo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
document.getElementById('buscar-ventas')?.addEventListener('input', function() {
    const query = this.value.trim();
    const dropdown = document.getElementById('dropdown-ventas');

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    fetch('<?= BASE_URL ?>/app/controllers/ReporteController.php?action=buscarVentas&q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (!data.resultados || data.resultados.length === 0) {
                dropdown.innerHTML = '<div class="dropdown-item">No se encontraron resultados</div>';
                dropdown.style.display = 'block';
                return;
            }

            let html = '';
            data.resultados.forEach(item => {
                html += `<div class="dropdown-item" onclick="seleccionarBusquedaVenta('${encodeURIComponent(item.id)}')">${item.texto}</div>`;
            });

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
        })
        .catch(err => {
            console.error('Error:', err);
            dropdown.innerHTML = '<div class="dropdown-item" style="color: #ef4444;">Error en la búsqueda</div>';
            dropdown.style.display = 'block';
        });
});

function seleccionarBusquedaVenta(id) {
    document.getElementById('buscar-ventas').value = decodeURIComponent(id);
    document.getElementById('dropdown-ventas').style.display = 'none';
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    const input = document.getElementById('buscar-ventas');
    const dropdown = document.getElementById('dropdown-ventas');
    
    if (input && dropdown && !input.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
