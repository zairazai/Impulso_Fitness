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
            <h1>Editar Usuario</h1>
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
                            <p>Actualiza los datos del usuario <?= htmlspecialchars($usuario['username']) ?></p>
                        </div>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=editar&id=<?= $usuario['id'] ?>" id="formEditarUsuario" novalidate>

                        <div class="row g-3">

                            <!-- Username (solo lectura) -->
                            <div class="col-md-6">
                                <label class="form-label" for="username">Usuario</label>
                                <input
                                    type="text"
                                    id="username"
                                    class="form-control custom-input"
                                    value="<?= htmlspecialchars($usuario['username']) ?>"
                                    disabled
                                >
                                <small style="color: #94a3b8;">No se puede cambiar</small>
                            </div>

                            <!-- Rol (solo lectura) -->
                            <div class="col-md-6">
                                <label class="form-label" for="role">Rol</label>
                                <input
                                    type="text"
                                    id="role"
                                    class="form-control custom-input"
                                    value="<?= htmlspecialchars($usuario['role']) ?>"
                                    disabled
                                >
                                <small style="color: #94a3b8;">No se puede cambiar</small>
                            </div>

                            <!-- Email -->
                            <div class="col-md-12">
                                <label class="form-label" for="email">Correo electrónico *</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control custom-input"
                                    value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                                    required
                                >
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <input 
                                        type="checkbox" 
                                        name="active" 
                                        value="1"
                                        <?= $usuario['active'] ? 'checked' : '' ?>
                                        style="margin-right: 0.5rem;"
                                    >
                                    Usuario Activo
                                </label>
                            </div>

                        </div>

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="action-row mt-4">
                            <a href="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=index" class="btn btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- COLUMNA DERECHA / OPCIONES -->
            <div class="register-side">
                <div class="page-card side-card">
                    <h4>Cambiar Contraseña</h4>
                    <p style="color: #cbd5e1; margin-bottom: 1rem;">
                        Para cambiar la contraseña de este usuario, usa el formulario que se abre a continuación:
                    </p>
                    <button type="button" class="btn btn-outline-warning btn-block" onclick="mostrarFormularioCambiarPassword()">
                        <i class="bi bi-key"></i> Cambiar contraseña
                    </button>
                </div>

                <div class="page-card side-card">
                    <h4>Información</h4>
                    <div style="color: #cbd5e1; font-size: 0.9rem;">
                        <p><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?></p>
                        <p><strong>ID:</strong> <?= $usuario['id'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CAMBIAR CONTRASEÑA -->
<div id="modal-cambiar-password" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cambiar Contraseña</h2>
            <button type="button" class="modal-close" onclick="cerrarModalPassword()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-cambiar-password" onsubmit="cambiarPassword(event)">
                <div class="form-group">
                    <label class="form-label">Nueva contraseña *</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        class="form-control custom-input" 
                        placeholder="Ingresa la nueva contraseña"
                        minlength="6"
                        required
                    >
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar contraseña *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        class="form-control custom-input" 
                        placeholder="Confirma la contraseña"
                        minlength="6"
                        required
                    >
                </div>
                <small style="color: #94a3b8;">Mínimo 6 caracteres</small>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalPassword()">
                Cancelar
            </button>
            <button type="button" class="btn btn-primary" onclick="cambiarPassword()">
                Cambiar contraseña
            </button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    padding: 1rem;
}

.modal-content {
    background: rgba(16, 28, 51, 0.98);
    border: 1px solid rgba(34, 211, 238, 0.18);
    border-radius: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    width: 100%;
    max-width: 450px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    border-bottom: 1px solid rgba(34, 211, 238, 0.18);
    border-radius: 1.5rem 1.5rem 0 0;
}

.modal-header h2 {
    margin: 0;
    color: #f8fafc;
}

.modal-close {
    background: none;
    border: none;
    color: #f8fafc;
    font-size: 1.8rem;
    cursor: pointer;
    padding: 0;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    padding: 2rem;
    border-top: 1px solid rgba(34, 211, 238, 0.18);
}

.form-group {
    margin-bottom: 1.2rem;
}

.form-group label {
    display: block;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
}

.btn-block {
    width: 100%;
}
</style>

<script>
function mostrarFormularioCambiarPassword() {
    document.getElementById('modal-cambiar-password').style.display = 'flex';
}

function cerrarModalPassword() {
    document.getElementById('modal-cambiar-password').style.display = 'none';
    document.getElementById('form-cambiar-password').reset();
}

function cambiarPassword() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (!newPassword || !confirmPassword) {
        alert('Ambos campos son obligatorios');
        return;
    }

    if (newPassword.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return;
    }

    if (newPassword !== confirmPassword) {
        alert('Las contraseñas no coinciden');
        return;
    }

    const data = new FormData();
    data.append('id', <?= $usuario['id'] ?>);
    data.append('new_password', newPassword);

    fetch('<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=cambiar-password', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Contraseña cambiada exitosamente');
            cerrarModalPassword();
        } else {
            alert('Error: ' + (data.error || 'No se pudo cambiar la contraseña'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error al cambiar la contraseña');
    });
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-cambiar-password')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalPassword();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
