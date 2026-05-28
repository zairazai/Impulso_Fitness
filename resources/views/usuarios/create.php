<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/socios.css';
require_once __DIR__ . '/../layouts/header.php';
?>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-0">

        <!-- TÍTULO DEL MÓDULO -->
        <div class="module-title-bar">
            <h1>Crear Nuevo Usuario</h1>
        </div>

        <!-- ALERTAS -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="register-layout">
            <div class="register-main">
                <div class="page-card card-section">

                    <div class="section-header mb-3">
                        <div>
                            <h3>Información del Usuario</h3>
                            <p>Completa todos los datos para crear un nuevo usuario en el sistema.</p>
                        </div>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=crear" id="formCrearUsuario" novalidate>

                        <div class="row g-3">

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label" for="username">Usuario (Username) *</label>
                                <input
                                    type="text"
                                    name="username"
                                    id="username"
                                    class="form-control custom-input"
                                    placeholder="Ej. jperez"
                                    maxlength="50"
                                    required
                                >
                            </div>

                            <!-- Email -->
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

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label class="form-label" for="password">Contraseña *</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control custom-input"
                                    placeholder="Ingresa una contraseña segura"
                                    minlength="6"
                                    required
                                >
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <label class="form-label" for="role">Rol *</label>
                                <select
                                    name="role"
                                    id="role"
                                    class="form-select custom-input"
                                    required
                                    onchange="mostrarEspecialidad()"
                                >
                                    <option value="">Seleccionar rol</option>
                                    <option value="Admin">Administrador</option>
                                    <option value="Recepcion">Recepcionista</option>
                                    <option value="Instructor">Instructor</option>
                                </select>
                            </div>

                            <!-- Especialidad (solo para instructores) -->
                            <div class="col-md-12" id="especialidad-field" style="display: none;">
                                <label class="form-label" for="especialidad">Especialidad</label>
                                <input
                                    type="text"
                                    name="especialidad"
                                    id="especialidad"
                                    class="form-control custom-input"
                                    placeholder="Ej. Musculación, Cardio, Yoga"
                                    maxlength="100"
                                >
                            </div>

                        </div>

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="action-row mt-4">
                            <a href="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=index" class="btn btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- COLUMNA DERECHA / INFORMACIÓN -->
            <div class="register-side">
                <div class="page-card side-card success-card">
                    <h4>Información importante</h4>
                    <p>
                        <strong>Roles disponibles:</strong>
                    </p>
                    <ul style="margin: 0.5rem 0 0; padding-left: 1.2rem; color: #cbd5e1;">
                        <li><strong>Administrador:</strong> Acceso completo al sistema</li>
                        <li><strong>Recepcionista:</strong> Gestión de ingresos y membresías</li>
                        <li><strong>Instructor:</strong> Gestión de clases y horarios</li>
                    </ul>
                </div>

                <div class="page-card side-card">
                    <h4>Requisitos</h4>
                    <ul style="margin: 0; padding-left: 1.2rem; color: #cbd5e1;">
                        <li>Usuario único (no puede repetirse)</li>
                        <li>Email válido y único</li>
                        <li>Contraseña mínimo 6 caracteres</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarEspecialidad() {
    const role = document.getElementById('role').value;
    const especialidadField = document.getElementById('especialidad-field');

    if (role === 'Instructor') {
        especialidadField.style.display = 'block';
        document.getElementById('especialidad').required = true;
    } else {
        especialidadField.style.display = 'none';
        document.getElementById('especialidad').required = false;
    }
}

// Validación básica de contraseña
document.getElementById('password').addEventListener('input', function() {
    if (this.value.length < 6) {
        this.classList.add('input-error');
    } else {
        this.classList.remove('input-error');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
