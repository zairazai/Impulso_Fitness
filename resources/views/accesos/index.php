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
                    <h1><i class="bi bi-journal-check"></i> Registro de accesos</h1>
                    <p>Monitorea ingresos y rechazos de socios con una vista moderna y clara.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=index" class="btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Actualizar
                    </a>
                </div>
            </section>

            <section class="filter-panel">
                <form id="form-filtro-accesos" method="GET" action="<?= BASE_URL ?>/app/controllers/AccesoController.php" class="filter-form">
                    <input type="hidden" name="action" value="index">

                    <div class="filter-row">
                        <input class="custom-input" type="text" name="buscar" placeholder="Buscar ID, socio, membresía o resultado" value="<?= htmlspecialchars($buscar ?? '') ?>">
                        <select class="custom-input" name="resultado">
                            <option value="" <?= empty($resultado) ? 'selected' : '' ?>>Todos los resultados</option>
                            <option value="permitido" <?= $resultado === 'permitido' ? 'selected' : '' ?>>Permitido</option>
                            <option value="denegado" <?= $resultado === 'denegado' ? 'selected' : '' ?>>Denegado</option>
                        </select>
                        <input class="custom-input" type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                        <input class="custom-input" type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </section>

            <script>
            (function(){
                const form = document.getElementById('form-filtro-accesos');
                if (!form) return;

                // Enviar solo parámetros no vacíos para que el backend se adapte a cualquiera
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    const base = '<?= BASE_URL ?>/app/controllers/AccesoController.php';
                    const params = new URLSearchParams();
                    params.append('action', 'index');

                    const buscar = (form.querySelector('[name="buscar"]') || {}).value || '';
                    const resultado = (form.querySelector('[name="resultado"]') || {}).value || '';
                    const fechaInicio = (form.querySelector('[name="fecha_inicio"]') || {}).value || '';
                    const fechaFin = (form.querySelector('[name="fecha_fin"]') || {}).value || '';

                    if (buscar.trim() !== '') params.append('buscar', buscar.trim());
                    if (resultado.trim() !== '') params.append('resultado', resultado.trim());
                    if (fechaInicio.trim() !== '') params.append('fecha_inicio', fechaInicio.trim());
                    if (fechaFin.trim() !== '') params.append('fecha_fin', fechaFin.trim());

                    // Navegar a la URL construida
                    window.location = base + '?' + params.toString();
                });

                // Permitir enviar con Enter al estar en cualquier input
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(inp => {
                    inp.addEventListener('keydown', function(ev){
                        if (ev.key === 'Enter') {
                            ev.preventDefault();
                            form.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    });
                });
            })();
            </script>

            <section class="report-cards">
                <?php
                    $permitidos = 0;
                    $denegados = 0;
                    foreach ($accesos as $acceso) {
                        if ($acceso['resultado'] === 'permitido') {
                            $permitidos++;
                        } elseif ($acceso['resultado'] === 'denegado') {
                            $denegados++;
                        }
                    }
                ?>

                <article class="report-card card-success">
                    <div class="card-icon"><i class="bi bi-door-open"></i></div>
                    <div>
                        <p>Accesos permitidos</p>
                        <strong><?= number_format($permitidos) ?></strong>
                    </div>
                </article>

                <article class="report-card card-danger">
                    <div class="card-icon"><i class="bi bi-door-closed"></i></div>
                    <div>
                        <p>Accesos denegados</p>
                        <strong><?= number_format($denegados) ?></strong>
                    </div>
                </article>

                <article class="report-card card-info">
                    <div class="card-icon"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <p>Registros mostrados</p>
                        <strong><?= number_format(count($accesos)) ?></strong>
                    </div>
                </article>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Historial de accesos</h2>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Socio</th>
                                <th>Estado</th>
                                <th>Membresía</th>
                                <th>Resultado</th>
                                <th>Motivo</th>
                                <th>Fecha y hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($accesos)): ?>
                                <?php foreach ($accesos as $acceso): ?>
                                    <tr>
                                        <td><?= (int)$acceso['id'] ?></td>
                                        <td><?= htmlspecialchars($acceso['socio']) ?></td>
                                        <td><span class="status-pill <?= $acceso['estado_socio'] === 'activo' ? 'status-active' : 'status-warning' ?>"><?= htmlspecialchars(ucfirst($acceso['estado_socio'])) ?></span></td>
                                        <td><?= htmlspecialchars($acceso['membresia'] ?? 'N/A') ?></td>
                                        <td><span class="status-pill <?= $acceso['resultado'] === 'permitido' ? 'status-active' : 'status-inactive' ?>"><?= htmlspecialchars(ucfirst($acceso['resultado'])) ?></span></td>
                                        <td><?= htmlspecialchars($acceso['motivo']) ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($acceso['fecha_hora']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-table">No se encontraron registros de acceso.</td>
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