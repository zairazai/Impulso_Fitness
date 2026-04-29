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
| Usamos el modelo Membresia porque este módulo debe manejar:
| - socios disponibles para membresía
| - planes de membresía
| - actualización de estados
*/
require_once __DIR__ . '/../../../app/models/Membresia.php';

$membresiaModel = new Membresia();

/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADOS DE MEMBRESÍAS
|--------------------------------------------------------------------------
| Antes de mostrar la vista actualizamos estados para que:
| - membresías vencidas pasen a inactivas
| - socios sin membresía vigente aparezcan inactivos
| - socios con membresía vigente aparezcan activos
*/
$membresiaModel->actualizarEstadosMembresias();

/*
|--------------------------------------------------------------------------
| LISTADO GENERAL DE SOCIOS
|--------------------------------------------------------------------------
*/
$socios = $membresiaModel->listarSocios();

/*
|--------------------------------------------------------------------------
| DATOS RECIBIDOS POR URL
|--------------------------------------------------------------------------
*/
$idSeleccionado = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$planSeleccionado = trim($_GET['plan'] ?? '');
$success = trim($_GET['success'] ?? '');

/*
|--------------------------------------------------------------------------
| SOCIO SELECCIONADO
|--------------------------------------------------------------------------
*/
$socioSeleccionado = null;

if ($idSeleccionado > 0) {
    $socioSeleccionado = $membresiaModel->obtenerSocioPorId($idSeleccionado);
}

/*
|--------------------------------------------------------------------------
| PLANES DISPONIBLES
|--------------------------------------------------------------------------
| Los planes ya no se queman en el código.
| Ahora vienen desde la tabla membresias mediante procedimiento almacenado.
*/
$planes = $membresiaModel->listarMembresias();

/*
|--------------------------------------------------------------------------
| TOTAL INICIAL
|--------------------------------------------------------------------------
*/
$totalInicial = 0;

foreach ($planes as $plan) {
    if ($plan['nombre'] === $planSeleccionado) {
        $totalInicial = (float)$plan['precio'];
        break;
    }
}

/*
|--------------------------------------------------------------------------
| FECHA ACTUAL
|--------------------------------------------------------------------------
*/
$fechaActual = date('Y-m-d H:i:s');

/*
|--------------------------------------------------------------------------
| FUNCIÓN AUXILIAR PARA FORMATEAR FECHAS
|--------------------------------------------------------------------------
*/
function formatearFechaMembresia(?string $fecha): string
{
    if (!$fecha) {
        return '-';
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return '-';
    }

    return date('d/m/Y H:i', $timestamp);
}

include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid px-0">

        <div class="module-title-bar">
            <h1>Registrar Pago de Membresía</h1>
        </div>

        <?php if ($success === 'socio_guardado'): ?>
            <div class="alert alert-success">
                Socio guardado correctamente. Ahora confirma el plan y registra el pago.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['paid'])): ?>
            <div class="alert alert-success">
                Pago registrado correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Ocurrió un problema al procesar el pago.
            </div>
        <?php endif; ?>

        <div class="register-layout">

            <div class="register-main">

                <div class="page-card card-section mb-4">
                    <input
                        type="text"
                        id="buscarSocioInput"
                        class="form-control custom-input"
                        placeholder="Buscar socio..."
                    >

                    <div id="sociosList" class="socios-list mt-4">
                        <?php if (!empty($socios)): ?>
                            <?php foreach ($socios as $socio): ?>
                                <?php
                                    $esSeleccionado = $idSeleccionado > 0 && (int)$socio['id'] === $idSeleccionado;
                                    $estado = strtolower($socio['estado'] ?? 'activo');
                                    $estadoTexto = strtoupper($socio['estado'] ?? 'ACTIVO');
                                ?>

                                <a
                                    href="<?= BASE_URL ?>/resources/views/membresias/index.php?id=<?= (int)$socio['id'] ?>&plan=<?= urlencode($planSeleccionado) ?>"
                                    class="socio-list-item <?= $esSeleccionado ? 'active' : '' ?>"
                                >
                                    <div>
                                        <h4><?= htmlspecialchars($socio['nombre']) ?></h4>

                                        <span class="status-badge <?= $estado === 'activo' ? 'status-active' : 'status-inactive' ?>">
                                            <?= htmlspecialchars($estadoTexto) ?>
                                        </span>
                                    </div>

                                    <div class="socio-arrow">›</div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-box">
                                No hay socios registrados.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <form
                    method="POST"
                    action="<?= BASE_URL ?>/routes/membresia_store.php"
                    id="formMembresia"
                    class="page-card card-section"
                >
                    <input
                        type="hidden"
                        name="socio_id"
                        id="socioIdSeleccionado"
                        value="<?= $socioSeleccionado['id'] ?? 0 ?>"
                    >

                    <input
                        type="hidden"
                        name="plan_seleccionado"
                        id="planSeleccionadoInput"
                        value="<?= htmlspecialchars($planSeleccionado) ?>"
                    >

                    <input
                        type="hidden"
                        name="total"
                        id="totalInput"
                        value="<?= number_format($totalInicial, 2, '.', '') ?>"
                    >

                    <input
                        type="hidden"
                        name="metodo_pago"
                        id="metodoPagoInput"
                        value=""
                    >

                    <div class="membership-block">
                        <div class="section-header mb-3">
                            <div>
                                <h3>Plan de Membresía</h3>
                                <p>Confirma o cambia el plan para el socio seleccionado.</p>
                            </div>
                        </div>

                        <div class="membership-grid">
                            <?php if (!empty($planes)): ?>
                                <?php foreach ($planes as $plan): ?>
                                    <?php $activo = $planSeleccionado === $plan['nombre']; ?>

                                    <div
                                        class="membership-card <?= $activo ? 'active' : '' ?>"
                                        data-plan="<?= htmlspecialchars($plan['nombre']) ?>"
                                        data-price="<?= number_format((float)$plan['precio'], 2, '.', '') ?>"
                                    >
                                        <h4><?= htmlspecialchars($plan['nombre']) ?></h4>
                                        <p><?= htmlspecialchars($plan['descripcion'] ?? '') ?></p>
                                        <strong>$<?= number_format((float)$plan['precio'], 2) ?> MXN</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-box">
                                    No hay planes de membresía registrados.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="membership-block mt-4">
                        <div class="section-header mb-3">
                            <div>
                                <h3>Fecha de inicio</h3>
                                <p>Indica la fecha y hora en la que comenzará la membresía.</p>
                            </div>
                        </div>

                        <input
                            type="datetime-local"
                            name="fecha_inicio"
                            id="fechaInicio"
                            class="form-control custom-input"
                            value="<?= date('Y-m-d\TH:i') ?>"
                            required
                        >
                    </div>

                    <div class="membership-block mt-4">
                        <div class="section-header mb-3">
                            <div>
                                <h3>Método de pago</h3>
                                <p>Selecciona cómo se realizará el cobro.</p>
                            </div>
                        </div>

                        <div class="payment-methods">
                            <button type="button" class="payment-option" data-method="Efectivo">
                                Efectivo
                            </button>

                            <button type="button" class="payment-option" data-method="Tarjeta">
                                Tarjeta
                            </button>

                            <button type="button" class="payment-option" data-method="Transferencia">
                                Transferencia
                            </button>
                        </div>
                    </div>

                    <div class="membership-block mt-4">
                        <div class="section-header mb-3">
                            <div>
                                <h3>Confirmación</h3>
                                <p>Verifica el plan, la fecha y el método de pago antes de continuar.</p>
                            </div>
                        </div>

                        <div class="empty-box">
                            El pago aún no ha sido registrado. Haz clic en <strong>Confirmar pago</strong> para finalizar este proceso.
                        </div>
                    </div>

                    <div class="action-row mt-4">
                        <a
                            href="<?= BASE_URL ?>/resources/views/socios/create.php"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Confirmar pago
                        </button>
                    </div>
                </form>
            </div>

            <div class="register-side">

                <div class="page-card side-card">
                    <h2 id="nombreSocioPreview">
                        <?= $socioSeleccionado ? htmlspecialchars($socioSeleccionado['nombre']) : 'Selecciona un socio' ?>
                    </h2>

                    <?php if ($socioSeleccionado): ?>
                        <?php
                            $estadoSocio = strtolower($socioSeleccionado['estado'] ?? 'activo');
                            $estadoTextoSocio = strtoupper($socioSeleccionado['estado'] ?? 'ACTIVO');
                        ?>

                        <span class="status-badge <?= $estadoSocio === 'activo' ? 'status-active' : 'status-inactive' ?>">
                            <?= htmlspecialchars($estadoTextoSocio) ?>
                        </span>
                    <?php endif; ?>

                    <div class="summary-block mt-4">
                        <h3>Registro del pago</h3>
                        <p class="summary-label">Fecha</p>
                        <p class="summary-value"><?= formatearFechaMembresia($fechaActual) ?></p>
                    </div>
                </div>

                <div class="page-card side-card">
                    <h3>Resumen</h3>

                    <p class="summary-label">Estado del proceso</p>
                    <p class="summary-value">Pago pendiente de confirmar</p>

                    <p class="summary-label mt-4">Plan seleccionado</p>
                    <p class="summary-value" id="planSeleccionadoPreview">
                        <?= $planSeleccionado !== '' ? htmlspecialchars($planSeleccionado) : 'Ninguno' ?>
                    </p>

                    <p class="summary-label mt-4">Método de pago</p>
                    <p class="summary-value" id="metodoPagoPreview">Ninguno</p>

                    <p class="summary-label mt-4">Total</p>
                    <p class="summary-value" id="totalPreview">
                        $<?= number_format($totalInicial, 2) ?>
                    </p>
                </div>

                <div class="page-card side-card">
                    <h3>Datos del socio</h3>

                    <div class="preview-list">
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($socioSeleccionado['telefono'] ?? '-') ?></p>
                        <p><strong>Correo:</strong> <?= htmlspecialchars($socioSeleccionado['email'] ?? '-') ?></p>
                        <p><strong>Nacimiento:</strong> <?= formatearFechaMembresia($socioSeleccionado['fecha_nacimiento'] ?? null) ?></p>
                        <p><strong>Género:</strong> <?= htmlspecialchars($socioSeleccionado['genero'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/membresias.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>