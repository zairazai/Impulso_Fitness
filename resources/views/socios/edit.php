<?php

require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';


/*
|--------------------------------------------------------------------------
| NOMBRE COMPLETO
|--------------------------------------------------------------------------
| Se usa en el input hidden para conservar compatibilidad con el update.
*/
$nombreCompleto = trim(
    ($socio['nombres'] ?? '') . ' ' .
    ($socio['apellido_paterno'] ?? '') . ' ' .
    ($socio['apellido_materno'] ?? '')
);

/* HEADER GENERAL */
$extraCss = BASE_URL . "/public/css/socios.css";

include __DIR__ . '/../layouts/header.php';

?>

<?php
/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid px-0">

        <!-- ENCABEZADO -->
        <div class="socio-page-header mb-3">
            <h1>Editar Socio</h1>
            <p>Actualiza la información del socio seleccionado.</p>
        </div>

        <!-- ALERTAS -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Revisa los datos del formulario antes de guardar.
            </div>
        <?php endif; ?>

        <!-- FORMULARIO -->
        <div class="page-card card-section">
            <form method="POST" action="<?= BASE_URL ?>/app/controllers/SocioController.php?action=update" id="formSocioEdit" novalidate >

                <input type="hidden" name="id" value="<?= htmlspecialchars($socio['id']) ?>">
                <input type="hidden" name="nombre" id="nombre" value="<?= htmlspecialchars($nombreCompleto) ?>">

                <div class="row g-3">

                    <!-- Nombre(s) -->
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

                    <!-- Apellido paterno -->
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

                    <!-- Apellido materno -->
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

                    <!-- Fecha de nacimiento -->
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

                    <!-- Teléfono -->
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

                    <!-- Correo -->
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

                    <!-- Género -->
                    <div class="col-md-6">
                        <label class="form-label" for="genero">Género *</label>
                        <select name="genero" id="genero" class="form-select custom-input" required>
                            <option value="">Seleccionar</option>
                            <option value="Femenino" <?= (($socio['genero'] ?? '') === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
                            <option value="Masculino" <?= (($socio['genero'] ?? '') === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                            <option value="Otro" <?= (($socio['genero'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <!-- Contacto de emergencia -->
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

                    <!-- Teléfono de emergencia -->
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

                    <!-- Huella demo -->
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

                    <!-- Estado actual -->
                    <div class="col-md-6">
                        <label class="form-label">Estado actual</label>
                        <input
                            type="text"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['estado'] ?? '-') ?>"
                            readonly
                        >
                    </div>

                    <!-- Dirección completa -->
                    <!-- Calle -->
                    <div class="col-md-6">
                        <label class="form-label" for="calle">Calle *</label>
                        <input
                            type="text"
                            name="calle"
                            id="calle"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['calle'] ?? '') ?>"
                            maxlength="120"
                            required
                        >
                    </div>

                    <!-- Número -->
                    <div class="col-md-3">
                        <label class="form-label" for="numero">Número *</label>
                        <input
                            type="text"
                            name="numero"
                            id="numero"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['numero'] ?? '') ?>"
                            maxlength="20"
                            required
                        >
                    </div>

                    <!-- Código postal -->
                    <div class="col-md-3">
                        <label class="form-label" for="codigoPostal">Código postal *</label>
                        <input
                            type="text"
                            name="codigo_postal"
                            id="codigoPostal"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['codigo_postal'] ?? '') ?>"
                            maxlength="5"
                            inputmode="numeric"
                            pattern="\d{5}"
                            required
                        >
                    </div>

                    <!-- Colonia -->
                    <div class="col-12">
                        <label class="form-label" for="colonia">Colonia *</label>
                        <input
                            type="text"
                            name="colonia"
                            id="colonia"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($socio['colonia'] ?? '') ?>"
                            maxlength="120"
                            required
                        >
                    </div>

                    <!-- Notas -->
                    <div class="col-12">
                        <label class="form-label" for="notas">Notas *</label>
                        <textarea
                            name="notas"
                            id="notas"
                            class="form-control custom-input"
                            rows="5"
                            maxlength="1000"
                        ><?= htmlspecialchars($socio['notas'] ?? '') ?></textarea>
                    </div>

                </div>

                <!-- BOTONES -->
                <div class="d-flex gap-2 mt-4 flex-wrap">
                    <button type="submit" class="btn btn-success">
                        Guardar cambios
                    </button>

                    <a href="<?= BASE_URL ?>/app/controllers/SocioController.php?action=index" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

<!-- JS DEL MÓDULO DE SOCIOS -->
<script src="<?= BASE_URL ?>/public/js/socios.js?v=123"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>