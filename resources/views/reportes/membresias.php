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
                    <h1><i class="bi bi-card-list"></i> Reporte de membresías</h1>
                    <p>Controla el estado de las membresías, pagos y el flujo mensual en un solo lugar.</p>
                </div>

                <a
                    href="<?= BASE_URL ?>/app/controllers/ReporteController.php?action=exportarMembresias&buscar=<?= urlencode($buscar) ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&estado=<?= urlencode($estado) ?>"
                    class="btn btn-outline-success"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Exportar CSV
                </a>
            </section>

            <section class="filter-panel">
                <form method="GET" action="<?= BASE_URL ?>/app/controllers/ReporteController.php" class="filter-form">
                    <input type="hidden" name="action" value="membresias">

                    <div class="filter-row">
                        <div style="flex: 1; position: relative;">
                            <input 
                                type="text" 
                                name="buscar" 
                                id="buscar-membresias" 
                                placeholder="Buscar socio, plan o referencia..." 
                                value="<?= htmlspecialchars($buscar) ?>"
                                autocomplete="off"
                            >
                            <div id="dropdown-membresias" class="dropdown-results" style="display: none;"></div>
                        </div>
                        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
                        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
                        <select name="estado">
                            <option value="" <?= $estado === '' ? 'selected' : '' ?>>Todas las membresías</option>
                            <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activas</option>
                            <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivas</option>
                            <option value="suspendido" <?= $estado === 'suspendido' ? 'selected' : '' ?>>Suspendidas</option>
                        </select>
                        <button type="submit" class="btn-primary">Filtrar</button>
                    </div>
                </form>
            </section>

            <section class="report-cards">
                <article class="report-card card-primary">
                    <div class="card-icon"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <p>Pagos registrados</p>
                        <strong><?= number_format((int)$resumen['total_pagos']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-secondary">
                    <div class="card-icon"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <p>Ingresos totales</p>
                        <strong>$<?= number_format((float)$resumen['total_ingresos'], 2) ?></strong>
                    </div>
                </article>

                <article class="report-card card-tertiary">
                    <div class="card-icon"><i class="bi bi-calendar2-week"></i></div>
                    <div>
                        <p>Ingresos este mes</p>
                        <strong>$<?= number_format((float)$resumen['ingresos_mes'], 2) ?></strong>
                    </div>
                </article>

                <article class="report-card card-accent">
                    <div class="card-icon"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <p>Pagos este mes</p>
                        <strong><?= number_format((int)$resumen['pagos_mes']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-success">
                    <div class="card-icon"><i class="bi bi-person-check"></i></div>
                    <div>
                        <p>Membresías activas</p>
                        <strong><?= number_format((int)$resumen['membresias_activas']) ?></strong>
                    </div>
                </article>

                <article class="report-card card-warning">
                    <div class="card-icon"><i class="bi bi-person-x"></i></div>
                    <div>
                        <p>Membresías inactivas</p>
                        <strong><?= number_format((int)$resumen['membresias_inactivas']) ?></strong>
                    </div>
                </article>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Historial de pagos</h2>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Socio</th>
                                <th>Estado</th>
                                <th>Plan</th>
                                <th>Monto</th>
                                <th>Pago</th>
                                <th>Fecha</th>
                                <th>Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pagos)): ?>
                                <?php foreach ($pagos as $pago): ?>
                                    <tr>
                                        <td><?= (int)$pago['id'] ?></td>
                                        <td><?= htmlspecialchars($pago['socio']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($pago['estado_socio'] ?: 'General')) ?></td>
                                        <td><?= htmlspecialchars($pago['membresia']) ?></td>
                                        <td>$<?= number_format((float)$pago['monto'], 2) ?></td>
                                        <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                        <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                                        <td><?= htmlspecialchars($pago['referencia']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-table">No se encontraron pagos en este periodo.</td>
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
document.getElementById('buscar-membresias')?.addEventListener('input', function() {
    const query = this.value.trim();
    const dropdown = document.getElementById('dropdown-membresias');

    if (query.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    fetch('<?= BASE_URL ?>/app/controllers/ReporteController.php?action=buscarMembresias&q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (!data.resultados || data.resultados.length === 0) {
                dropdown.innerHTML = '<div class="dropdown-item">No se encontraron resultados</div>';
                dropdown.style.display = 'block';
                return;
            }

            let html = '';
            data.resultados.forEach(item => {
                html += `<div class="dropdown-item" onclick="seleccionarBusqueda('${encodeURIComponent(item.socio)}')">${item.texto}</div>`;
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

function seleccionarBusqueda(socio) {
    document.getElementById('buscar-membresias').value = decodeURIComponent(socio);
    document.getElementById('dropdown-membresias').style.display = 'none';
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    const input = document.getElementById('buscar-membresias');
    const dropdown = document.getElementById('dropdown-membresias');
    
    if (input && dropdown && !input.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
