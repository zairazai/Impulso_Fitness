<?php
require_once __DIR__ . '/../../../app/middleware/auth.php';
require_once __DIR__ . '/../../../app/models/Socio.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: /Impulso_Fitness/resources/views/socios/index.php?error=datos_invalidos");
    exit;
}

$modeloSocio = new Socio();
$socio = $modeloSocio->obtenerSocioPorId($id);
$huella = $modeloSocio->obtenerHuellaPorSocio($id);

if (!$socio) {
    header("Location: /Impulso_Fitness/resources/views/socios/index.php?error=no_encontrado");
    exit;
}

$nombreCompleto = trim(($socio['nombres'] ?? '') . ' ' . ($socio['apellido_paterno'] ?? '') . ' ' . ($socio['apellido_materno'] ?? ''));
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-0">

        <div class="socio-page-header mb-3">
            <h1>Editar Socio</h1>
            <p>Actualiza la información del socio seleccionado.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Revisa los datos del formulario antes de guardar.
            </div>
        <?php endif; ?>

        <div class="page-card">
            <form method="POST" action="<?= BASE_URL ?>/routes/socios_update.php" id="formSocioEdit">

                <input type="hidden" name="id" value="<?= htmlspecialchars($socio['id']) ?>">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label" for="nombres">Nombre(s) *</label>
                        <input
                            type="text"
                            name="nombres"
                            id="nombres"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['nombres'] ?? '') ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="apellidoPaterno">Apellido paterno *</label>
                        <input
                            type="text"
                            name="apellido_paterno"
                            id="apellidoPaterno"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['apellido_paterno'] ?? '') ?>"
                            maxlength="80"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="apellidoMaterno">Apellido materno</label>
                        <input
                            type="text"
                            name="apellido_materno"
                            id="apellidoMaterno"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['apellido_materno'] ?? '') ?>"
                            maxlength="80"
                        >
                    </div>

                    <input
                        type="hidden"
                        name="nombre"
                        id="nombre"
                        value="<?= htmlspecialchars($nombreCompleto) ?>"
                    >

                    <div class="col-md-6">
                        <label class="form-label" for="fechaNacimiento">Fecha de nacimiento *</label>
                        <input
                            type="date"
                            name="fecha_nacimiento"
                            id="fechaNacimiento"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['fecha_nacimiento'] ?? '') ?>"
                            max="<?= date('Y-m-d'); ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="telefono">Teléfono *</label>
                        <input
                            type="text"
                            name="telefono"
                            id="telefono"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['telefono'] ?? '') ?>"
                            maxlength="10"
                            inputmode="numeric"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="email">Correo electrónico *</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['email'] ?? '') ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="genero">Género *</label>
                        <select name="genero" id="genero" class="form-select custom-input" required>
                            <option value="">Seleccionar</option>
                            <option value="Femenino" <?= (($socio['genero'] ?? '') === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
                            <option value="Masculino" <?= (($socio['genero'] ?? '') === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                            <option value="Otro" <?= (($socio['genero'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="nombreContactoEmergencia">Nombre del contacto de emergencia *</label>
                        <input
                            type="text"
                            name="nombre_contacto_emergencia"
                            id="nombreContactoEmergencia"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['contacto_emergencia_nombre'] ?? '') ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="telefonoEmergencia">Teléfono de emergencia *</label>
                        <input
                            type="text"
                            name="telefono_emergencia"
                            id="telefonoEmergencia"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['contacto_emergencia_telefono'] ?? '') ?>"
                            maxlength="10"
                            inputmode="numeric"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="huellaDemo">Huella demo</label>
                        <input
                            type="text"
                            name="huella_demo"
                            id="huellaDemo"
                            class="form-control custom-input"
                            placeholder="<?= $huella ? 'Huella registrada - escribe una nueva solo si deseas reemplazarla' : 'Registrar nueva huella demo' ?>"
                            maxlength="255"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado actual</label>
                        <input
                            type="text"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['estado'] ?? '-') ?>"
                            readonly
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="direccion">Dirección *</label>
                        <input
                            type="text"
                            name="direccion"
                            id="direccion"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['direccion'] ?? '') ?>"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="notas">Notas *</label>
                        <textarea
                            name="notas"
                            id="notas"
                            class="form-control custom-input"
                            rows="5"
                            maxlength="1000"
                            required
                        ><?= htmlspecialchars($socio['notas'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 flex-wrap">
                    <button type="submit" class="btn btn-success">
                        Guardar cambios
                    </button>

                    <a href="<?= BASE_URL ?>/resources/views/socios/index.php?id=<?= urlencode($socio['id']) ?>" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const nombre = document.getElementById('nombre');
    const nombres = document.getElementById('nombres');
    const apellidoPaterno = document.getElementById('apellidoPaterno');
    const apellidoMaterno = document.getElementById('apellidoMaterno');

    const telefono = document.getElementById('telefono');
    const telefonoEmergencia = document.getElementById('telefonoEmergencia');

    function actualizarNombreCompleto() {
        const valor = `${nombres.value.trim()} ${apellidoPaterno.value.trim()} ${apellidoMaterno.value.trim()}`
            .replace(/\s+/g, ' ')
            .trim();

        nombre.value = valor;
    }

    [nombres, apellidoPaterno, apellidoMaterno].forEach(input => {
        input.addEventListener('input', actualizarNombreCompleto);
    });

    actualizarNombreCompleto();

    if (telefono) {
        telefono.addEventListener('input', () => {
            telefono.value = telefono.value.replace(/\D/g, '').slice(0, 10);
        });
    }

    if (telefonoEmergencia) {
        telefonoEmergencia.addEventListener('input', () => {
            telefonoEmergencia.value = telefonoEmergencia.value.replace(/\D/g, '').slice(0, 10);
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>