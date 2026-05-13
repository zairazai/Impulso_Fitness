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
| LAYOUT GENERAL
|--------------------------------------------------------------------------
| Cargamos el CSS del módulo antes del header para que se inserte
| correctamente dentro del <head>.
*/
$extraCss = "/Impulso_Fitness/public/css/socios.css";

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

        <!-- ========================================================= -->
        <!-- TÍTULO DEL MÓDULO                                         -->
        <!-- ========================================================= -->
        <div class="module-title-bar">
            <h1>Registro de Socio</h1>
        </div>

        <!-- ========================================================= -->
        <!-- ALERTAS                                                   -->
        <!-- ========================================================= -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Socio registrado correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Revisa los campos marcados antes de continuar.
            </div>
        <?php endif; ?>

        <!-- ========================================================= -->
        <!-- CONTENEDOR PRINCIPAL                                      -->
        <!-- ========================================================= -->
        <div class="register-layout">

            <!-- ===================================================== -->
            <!-- COLUMNA IZQUIERDA / FORMULARIO                        -->
            <!-- ===================================================== -->
            <div class="register-main">
                <div class="page-card card-section">

                    <div class="section-header mb-3">
                        <div>
                            <h3>Información Personal</h3>
                            <p>
                                Captura todos los datos del nuevo socio y selecciona
                                un plan sugerido para continuar con el módulo de pago.
                            </p>
                        </div>
                    </div>

                    <!-- ================================================= -->
                    <!-- FORMULARIO PRINCIPAL                               -->
                    <!-- ================================================= -->
                    <form method="POST" action="<?= BASE_URL ?>/routes/socios_store.php" id="formSocioCreate">

                        <div class="row g-3">

                            <!-- Nombre(s) -->
                            <div class="col-md-4">
                                <label class="form-label" for="nombres">Nombre(s) *</label>
                                <input
                                    type="text"
                                    name="nombres"
                                    id="nombres"
                                    class="form-control custom-input"
                                    placeholder="Ej. Juan"
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
                                    placeholder="Ej. Pérez"
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
                                    placeholder="Ej. López"
                                    maxlength="80"
                                >
                            </div>

                            <input type="hidden" id="nombre" name="nombre">

                            <!-- Fecha de nacimiento -->
                            <div class="col-md-6">
                                <label class="form-label" for="fechaNacimiento">Fecha de nacimiento *</label>
                                <input
                                    type="date"
                                    name="fecha_nacimiento"
                                    id="fechaNacimiento"
                                    class="form-control custom-input"
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
                                    placeholder="6671234567"
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
                                    placeholder="ejemplo@correo.com"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <!-- Género -->
                            <div class="col-md-6">
                                <label class="form-label" for="genero">Género *</label>
                                <select
                                    name="genero"
                                    id="genero"
                                    class="form-select custom-input"
                                    required
                                >
                                    <option value="">Seleccionar</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Contacto de emergencia -->
                            <!-- Nombre del contacto de emergencia -->
                            <div class="col-md-6">
                                <label class="form-label" for="nombreContactoEmergencia">Nombre del contacto de emergencia *</label>
                                <input
                                    type="text"
                                    name="nombre_contacto_emergencia"
                                    id="nombreContactoEmergencia"
                                    class="form-control custom-input"
                                    placeholder="Ej. María López"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <!-- Teléfono del contacto de emergencia -->
                            <div class="col-md-6">
                                <label class="form-label" for="telefonoEmergencia">Teléfono de emergencia *</label>
                                <input
                                    type="text"
                                    name="telefono_emergencia"
                                    id="telefonoEmergencia"
                                    class="form-control custom-input"
                                    placeholder="6671234567"
                                    maxlength="10"
                                    inputmode="numeric"
                                    required
                                >
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label class="form-label" for="direccion">Dirección *</label>
                                <input
                                    type="text"
                                    name="direccion"
                                    id="direccion"
                                    class="form-control custom-input"
                                    placeholder="Calle, número, colonia, ciudad"
                                    maxlength="255"
                                    required
                                >
                            </div>
                        </div>

                        <!-- ================================================= -->
                        <!-- PRESELECCIÓN DE MEMBRESÍA                         -->
                        <!-- ================================================= -->
                        <div class="membership-block mt-4">
                            <div class="section-header mb-3">
                                <div>
                                    <h3>Preselección de Membresía</h3>
                                    <p>
                                        El plan se conservará como sugerencia y podrá
                                        confirmarse o cambiarse en el módulo de pago.
                                    </p>
                                </div>
                            </div>

                            <!--
                                Este input hidden guarda el plan seleccionado.
                                Será obligatorio porque queremos que el usuario
                                elija forzosamente una card antes de registrar.
                            -->
                            <input
                                type="hidden"
                                name="membresia_preseleccion"
                                id="membresiaSeleccionada"
                                value=""
                                required
                            >

                            <div class="membership-grid">

                                <!-- Pase Diario -->
                                <div class="membership-card" data-plan="Pase Diario">
                                    <h4>Pase Diario</h4>
                                    <p>Acceso por 1 día</p>
                                    <strong>$50.00 MXN</strong>
                                </div>

                                <!-- Pase Semanal -->
                                <div class="membership-card" data-plan="Pase Semanal">
                                    <h4>Pase Semanal</h4>
                                    <p>Acceso por 7 días</p>
                                    <strong>$200.00 MXN</strong>
                                </div>

                                <!-- Pase Mensual -->
                                <div class="membership-card" data-plan="Pase Mensual">
                                    <h4>Pase Mensual</h4>
                                    <p>Acceso por 30 días</p>
                                    <strong>$500.00 MXN</strong>
                                </div>
                            </div>
                        </div>

                        <!-- ================================================= -->
                        <!-- REGISTRO BIOMÉTRICO                               -->
                        <!-- ================================================= -->
                        <div class="membership-block mt-4">
                            <div class="section-header mb-3">
                                <div>
                                    <h3>Registro biométrico</h3>
                                    <p>Preparar captura de huella del socio.</p>
                                </div>
                            </div>

                            <label class="form-label" for="huellaDemo">Código de huella (demo) *</label>
                            <input
                                type="text"
                                name="huella_demo"
                                id="huellaDemo"
                                class="form-control custom-input"
                                placeholder="Ej. HUELLA-JUAN-001"
                                maxlength="255"
                                required
                            >

                            <div class="mt-3">
                                <button
                                    type="button"
                                    class="btn btn-outline-info btn-sm"
                                    id="btnHuellaDemo"
                                >
                                    Capturar huella demo
                                </button>
                            </div>
                        </div>

                        <!-- ================================================= -->
                        <!-- BOTONES DE ACCIÓN                                 -->
                        <!-- ================================================= -->
                        <div class="action-row mt-4">
                            <a
                                href="<?= BASE_URL ?>/resources/views/socios/index.php"
                                class="btn btn-secondary"
                            >
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Registrar Socio
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- COLUMNA DERECHA / VISTA PREVIA                        -->
            <!-- ===================================================== -->
            <div class="register-side">

                <!-- Tarjeta informativa -->
                <div class="page-card side-card success-card">
                    <h4>Vista rápida</h4>
                    <p>
                        Completa todos los datos del socio, selecciona un plan sugerido
                        y registra la huella demo. Después podrás continuar con el
                        módulo de membresías para confirmar el plan y el pago.
                    </p>
                </div>

                <!-- Vista previa -->
                <div class="page-card side-card">
                    <h4>Vista Previa</h4>

                    <div class="preview-list">
                        <p><strong>Nombre:</strong> <span id="previewNombre">-</span></p>
                        <p><strong>Nacimiento:</strong> <span id="previewNacimiento">-</span></p>
                        <p><strong>Correo:</strong> <span id="previewCorreo">-</span></p>
                        <p><strong>Teléfono:</strong> <span id="previewTelefono">-</span></p>
                        <p><strong>Género:</strong> <span id="previewGenero">-</span></p>

                        <!--
                            Aquí sigue siendo "Plan sugerido" porque todavía
                            no está confirmado ni pagado.
                        -->
                        <p>
                            <strong>Plan sugerido:</strong>
                            <span id="previewMembresia" class="badge-plan">Ninguno</span>
                        </p>

                        <p><strong>Huella:</strong> <span id="previewHuella">No registrada</span></p>
                    </div>
                </div>

                <!-- Notas -->
                <div class="page-card side-card">
                    <h4>Notas *</h4>
                    <textarea
                        class="form-control custom-input"
                        rows="6"
                        name="notas"
                        form="formSocioCreate"
                        placeholder="Notas adicionales sobre el socio..."
                        maxlength="1000"
                        required
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS DEL MÓDULO DE SOCIOS -->
<script src="<?= BASE_URL ?>/public/js/socios.js?v=123"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>