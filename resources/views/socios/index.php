<?php

require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

/*
|--------------------------------------------------------------------------
| FUNCIÓN AUXILIAR PARA FORMATEAR FECHAS
|--------------------------------------------------------------------------
*/
function formatearFecha(?string $fecha): string
{
    if (!$fecha) {
        return '-';
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return '-';
    }

    return date('d/m/Y', $timestamp);
}

/*
|--------------------------------------------------------------------------
| HEADER GENERAL
|--------------------------------------------------------------------------
| Cargamos el CSS del módulo socios antes del header para que
| se inserte correctamente dentro del <head>.
*/
$extraCss = BASE_URL . "/public/css/socios.css";

include __DIR__ . '/../layouts/header.php';
/*SIDEBAR */
include __DIR__ . '/../layouts/sidebar.php';

?>


<div class="main-content">
    <div class="container-fluid px-0">

        <!-- ========================================================= -->
        <!-- ENCABEZADO DEL MÓDULO                                     -->
        <!-- ========================================================= -->
        <div class="socio-page-header mb-3">
            <h1>Buscar Socio</h1>
            <p>Consulta la información del socio y su estado actual.</p>
        </div>

        <!-- ========================================================= -->
        <!-- ALERTAS                                                   -->
        <!-- ========================================================= -->
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                Socio actualizado correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-warning">
                Socio desactivado correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Ocurrió un problema al procesar la solicitud.
            </div>
        <?php endif; ?>

        <div class="socio-search-grid">

            <!-- ===================================================== -->
            <!-- COLUMNA IZQUIERDA                                     -->
            <!-- ===================================================== -->
            <div class="left-column-search">

                <!-- ================================================= -->
                <!-- LISTA DE SOCIOS                                   -->
                <!-- ================================================= -->
                <div class="page-card">
                    <div class="search-toolbar mb-3">
                        <input
                            type="text"
                            id="buscarSocioInput"
                            class="form-control custom-input"
                            placeholder="Buscar por nombre, correo o ID..."
                        >
                    </div>

                    <div class="socios-list" id="sociosList">
                        <?php if (!empty($socios)): ?>
                            <?php foreach ($socios as $row): ?>
                                <?php
                                    $idSeleccionado = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                                    $esSeleccionado = $idSeleccionado === (int)$row['id'];
                                    $estado = strtolower($row['estado'] ?? 'activo');
                                ?>

                                <a
                                    id="socio-<?= (int)$row['id'] ?>"
                                    href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=index&id=<?= (int)$row['id'] ?>"
                                    class="socio-list-item <?= $esSeleccionado ? 'selected' : '' ?>"
                                >
                                    <div>
                                        <h5><?= htmlspecialchars($row['nombre']) ?></h5>

                                        <span class="status-pill <?= $estado === 'activo' ? 'status-active' : 'status-inactive' ?>">
                                            <?= strtoupper(htmlspecialchars($row['estado'] ?? 'activo')) ?>
                                        </span>
                                    </div>

                                    <span class="arrow">›</span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-box">
                                No hay socios registrados.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- NOTAS DEL SOCIO                                   -->
                <!-- ================================================= -->
                <div class="page-card mt-3">
                    <h4 class="mb-3">Notas</h4>

                    <textarea
                        class="form-control custom-input"
                        rows="6"
                        readonly
                    ><?= htmlspecialchars($socioSeleccionado['notas'] ?? 'Sin notas registradas.') ?></textarea>

                    <div class="text-end mt-3">
                        <?php if ($socioSeleccionado): ?>
                            <a
                                href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=edit&id=<?= (int)$socioSeleccionado['id'] ?>"
                                class="btn btn-primary btn-sm"
                            >
                                Editar notas
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- COLUMNA DERECHA / DETALLE                             -->
            <!-- ===================================================== -->
            <div class="detail-column">
                <?php if ($socioSeleccionado): ?>
                    <?php
                        $estadoSeleccionado = strtolower($socioSeleccionado['estado'] ?? 'activo');
                        $textoEstado = strtoupper($socioSeleccionado['estado'] ?? 'activo');
                    ?>

                    <div class="page-card detail-card">

                        <!-- ========================================= -->
                        <!-- CABECERA DEL DETALLE                      -->
                        <!-- ========================================= -->
                        <div class="detail-top mb-3">
                            <div>
                                <h2><?= htmlspecialchars($socioSeleccionado['nombre']) ?></h2>

                                <span class="status-pill <?= $estadoSeleccionado === 'activo' ? 'status-active' : 'status-inactive' ?>">
                                    <?= $textoEstado ?>
                                </span>
                            </div>
                        </div>

                        <div class="detail-sections">

                            <!-- ===================================== -->
                            <!-- INFORMACIÓN PERSONAL                  -->
                            <!-- ===================================== -->
                            <div class="detail-box">
                                <h4>Información Personal</h4>

                                <div class="detail-info-grid">
                                    <p>
                                        <span>Nombre completo</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['nombre'] ?? '-') ?></strong>
                                    </p>

                                    <p>
                                        <span>Fecha de nacimiento</span>
                                        <strong><?= formatearFecha($socioSeleccionado['fecha_nacimiento'] ?? null) ?></strong>
                                    </p>

                                    <p>
                                        <span>Teléfono</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['telefono'] ?? '-') ?></strong>
                                    </p>

                                    <p>
                                        <span>Correo</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['email'] ?? '-') ?></strong>
                                    </p>

                                    <p>
                                        <span>Género</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['genero'] ?? 'No especificado') ?></strong>
                                    </p>

                                    <p>
                                        <span>Dirección</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['direccion'] ?? 'No registrada') ?></strong>
                                    </p>
                                </div>
                            </div>

                            <!-- ===================================== -->
                            <!-- INFORMACIÓN ADICIONAL                 -->
                            <!-- ===================================== -->
                            <div class="detail-box">
                                <h4>Información Adicional</h4>

                                <div class="detail-info-grid">
                                    <p>
                                        <span>Fecha de registro</span>
                                        <strong><?= formatearFecha($socioSeleccionado['fecha_registro'] ?? null) ?></strong>
                                    </p>

                                    <p>
                                        <span>Estado</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['estado'] ?? '-') ?></strong>
                                    </p>

                                    <p>
                                        <span>Contacto de emergencia</span>
                                        <strong><?= htmlspecialchars($socioSeleccionado['contacto_emergencia'] ?? 'No registrado') ?></strong>
                                    </p>

                                    <p>
                                        <span>ID del socio</span>
                                        <strong>#<?= htmlspecialchars($socioSeleccionado['id'] ?? '-') ?></strong>
                                    </p>
                                </div>
                            </div>

                            <!-- ===================================== -->
                            <!-- MEMBRESÍA / HISTORIAL (PENDIENTE)     -->
                            <!-- ===================================== -->
                            <div class="detail-box">
                                <h4>Membresía e historial</h4>

                                <div class="empty-box">
                                    Esta sección se mostrará cuando conectemos el módulo de membresías y pagos reales.
                                </div>
                            </div>

                            <!-- ===================================== -->
                            <!-- ACCIONES                              -->
                            <!-- ===================================== -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=edit&id=<?= (int)$socioSeleccionado['id'] ?>"
                                    class="btn btn-warning"
                                >
                                    Editar
                                </a>

                                <?php if (($socioSeleccionado['estado'] ?? '') !== 'inactivo'): ?>
                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>/routes/socios_delete.php"
                                        onsubmit="return confirm('¿Deseas desactivar este socio?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= htmlspecialchars($socioSeleccionado['id']) ?>"
                                        >
                                        <button type="submit" class="btn btn-danger">
                                            Desactivar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-secondary d-flex align-items-center px-3">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php else: ?>
                    <div class="page-card">
                        <p>No hay socios registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/socios.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const seleccionado = document.querySelector('.socio-list-item.selected');

    if (seleccionado) {
        seleccionado.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>