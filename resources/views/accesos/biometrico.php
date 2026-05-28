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
                    <h1><i class="bi bi-fingerprint"></i> Control de acceso</h1>
                    <p>Busca por nombre, verifica membresía y toma la decisión de ingreso o acceso diario.</p>
                </div>
            </section>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'acceso_permitido'): ?>
                <div class="alert success">Acceso registrado y permitido.</div>
            <?php elseif (isset($_GET['success']) && $_GET['success'] === 'acceso_denegado'): ?>
                <div class="alert warning">Acceso denegado. Revisa el estado del socio o la membresía.</div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'socio'): ?>
                <div class="alert danger">Debes seleccionar un socio válido para registrar el acceso.</div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'registro'): ?>
                <div class="alert danger">Ocurrió un error al guardar el registro. Intenta de nuevo.</div>
            <?php endif; ?>

            <?php if (!empty($buscarError)): ?>
                <div class="alert danger">Error en la búsqueda: <?= htmlspecialchars($buscarError) ?></div>
            <?php endif; ?>

            <section class="filter-panel">
                <form method="GET" action="<?= BASE_URL ?>/app/controllers/AccesoController.php" class="filter-form">
                    <input type="hidden" name="action" value="biometrico">
                    <div class="filter-row">
                        <div style="position: relative; flex: 1;">
                            <input id="buscar-socio" class="custom-input" type="text" name="buscar" maxlength="200" placeholder="Buscar socio por nombre, email o teléfono" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                            <div id="dropdown-socios" class="dropdown-results" style="display:none; position: absolute; z-index: 60; width: 100%;"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </section>

            <?php if (!empty($socios)): ?>
                <section class="page-card card-section">
                    <div class="section-header">
                        <h2>Resultados</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Estado</th>
                                    <th>Membresía</th>
                                    <th>Vence</th>
                                    <th>Último pago</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($socios as $socio): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(trim($socio['nombres'] . ' ' . $socio['apellido_paterno'] . ' ' . $socio['apellido_materno'])) ?></td>
                                        <td><span class="status-pill <?= $socio['estado'] === 'activo' ? 'status-active' : 'status-warning' ?>"><?= htmlspecialchars(ucfirst($socio['estado'])) ?></span></td>
                                        <td><?= htmlspecialchars($socio['membresia']) ?></td>
                                        <td><?= $socio['fecha_fin'] ? htmlspecialchars(date('d/m/Y', strtotime($socio['fecha_fin']))) : '<span class="text-secondary">No activa</span>' ?></td>
                                        <td><?= $socio['ultimo_pago_fecha'] ? htmlspecialchars(date('d/m/Y', strtotime($socio['ultimo_pago_fecha']))) : '<span class="text-secondary">Sin pago</span>' ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=biometrico&buscar=<?= urlencode($_GET['buscar'] ?? '') ?>&socio_id=<?= (int)$socio['id'] ?>" class="btn btn-secondary btn-sm">Ver detalle</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php elseif (isset($_GET['buscar'])): ?>
                <section class="page-card card-section">
                    <div class="empty-box">No se encontró ningún socio con ese criterio. Usa acceso diario para invitados o prueba otra búsqueda.</div>
                </section>
            <?php endif; ?>

            <?php if (!empty($socioSeleccionado)): ?>
                <?php
                    $tieneMembresiaActiva = !empty($socioSeleccionado['membresia']);
                    $puedeIngresar = $socioSeleccionado['estado'] === 'activo' && $tieneMembresiaActiva;
                ?>
                <section class="page-card card-section">
                    <div class="section-header">
                        <div>
                            <h2><?= htmlspecialchars(trim($socioSeleccionado['nombres'] . ' ' . $socioSeleccionado['apellido_paterno'] . ' ' . $socioSeleccionado['apellido_materno'])) ?></h2>
                            <p>Detalles de socio y membresía</p>
                        </div>
                        <span class="status-pill <?= $puedeIngresar ? 'status-active' : 'status-inactive' ?>">
                            <?= $puedeIngresar ? 'Ingresar' : 'Acceso denegado' ?>
                        </span>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-card">
                            <h3>Información personal</h3>
                            <p><strong>Estado:</strong> <?= htmlspecialchars(ucfirst($socioSeleccionado['estado'])) ?></p>
                            <p><strong>Socio ID:</strong> #<?= (int)$socioSeleccionado['id'] ?></p>
                        </div>

                        <div class="detail-card">
                            <h3>Membresía activa</h3>
                            <p><strong>Plan:</strong> <?= htmlspecialchars($tieneMembresiaActiva ? $socioSeleccionado['membresia'] : 'Sin membresía activa') ?></p>
                            <p><strong>Inicio:</strong> <?= $socioSeleccionado['fecha_inicio'] ? htmlspecialchars(date('d/m/Y', strtotime($socioSeleccionado['fecha_inicio']))) : '<span class="text-secondary">N/A</span>' ?></p>
                            <p><strong>Vence:</strong> <?= $socioSeleccionado['fecha_fin'] ? htmlspecialchars(date('d/m/Y', strtotime($socioSeleccionado['fecha_fin']))) : '<span class="text-secondary">N/A</span>' ?></p>
                        </div>

                        <div class="detail-card">
                            <h3>Pago reciente</h3>
                            <p><strong>Fecha:</strong> <?= $socioSeleccionado['ultimo_pago_fecha'] ? htmlspecialchars(date('d/m/Y', strtotime($socioSeleccionado['ultimo_pago_fecha']))) : '<span class="text-secondary">Sin pago</span>' ?></p>
                            <p><strong>Monto:</strong> <?= $socioSeleccionado['ultimo_pago_monto'] ? '$' . number_format((float)$socioSeleccionado['ultimo_pago_monto'], 2) : '<span class="text-secondary">N/A</span>' ?></p>
                        </div>
                    </div>

                    <div class="action-row">
                        <?php if ($puedeIngresar): ?>
                            <form method="POST" action="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=registrar" class="inline-form">
                                <input type="hidden" name="socio_id" value="<?= (int)$socioSeleccionado['id'] ?>">
                                <input type="hidden" name="tipo" value="ingreso">
                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </form>
                        <?php else: ?>
                            <div class="alert warning">Este socio no tiene ingreso autorizado por membresía. Puedes registrar un acceso diario.</div>
                        <?php endif; ?>

                        <form method="POST" action="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=registrar" class="inline-form">
                            <input type="hidden" name="socio_id" value="<?= (int)$socioSeleccionado['id'] ?>">
                            <input type="hidden" name="tipo" value="diario">
                            <button type="submit" class="btn btn-secondary">Acceso diario</button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>

            <section class="page-card card-section">
                <div class="section-header">
                    <h2>Acceso rápido para invitados</h2>
                </div>
                <div class="quick-access-grid">
                    <article class="quick-card">
                        <div>
                            <h3>Invitado diario</h3>
                            <p>Registrar acceso rápido sin búsqueda.</p>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/app/controllers/AccesoController.php?action=registrar">
                            <input type="hidden" name="socio_id" value="0">
                            <input type="hidden" name="tipo" value="diario">
                            <button type="submit" class="btn btn-primary">Registrar invitado</button>
                        </form>
                    </article>
                </div>
            </section>
        </main>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<script>
document.getElementById('buscar-socio')?.addEventListener('input', function() {
    const q = this.value.trim();
    const dropdown = document.getElementById('dropdown-socios');

    if (q.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    fetch('<?= BASE_URL ?>/app/controllers/AccesoController.php?action=buscar&q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            if (!data.resultados || data.resultados.length === 0) {
                dropdown.innerHTML = '<div class="dropdown-item">No se encontraron socios</div>';
                dropdown.style.display = 'block';
                return;
            }

            let html = '';
            data.resultados.forEach(item => {
                html += `<div class="dropdown-item" data-id="${item.id}" onclick="seleccionarSocio(${item.id}, '${encodeURIComponent(item.texto)}')">${item.texto}</div>`;
            });

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
        })
        .catch(err => {
            console.error(err);
            dropdown.innerHTML = '<div class="dropdown-item">Error en búsqueda</div>';
            dropdown.style.display = 'block';
        });
});

function seleccionarSocio(id, texto) {
    const decoded = decodeURIComponent(texto);
    // Redirigir a la misma vista pasando socio_id para ver detalle
    const buscarVal = document.getElementById('buscar-socio').value.trim();
    window.location = '<?= BASE_URL ?>/app/controllers/AccesoController.php?action=biometrico&buscar=' + encodeURIComponent(buscarVal) + '&socio_id=' + id;
}

// cerrar dropdown al clicar fuera
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdown-socios');
    const input = document.getElementById('buscar-socio');
    if (!dropdown || !input) return;
    if (!dropdown.contains(e.target) && !input.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
