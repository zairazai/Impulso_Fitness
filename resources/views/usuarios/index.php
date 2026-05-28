<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/base.css';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
<div class="app-module">
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content">
        <?php $esAdmin = ($_SESSION['user']['role'] ?? '') === 'Admin'; ?>
        <section class="page-header">
            <div>
                <h1><i class="bi bi-people"></i> Usuarios</h1>
                <p>Administra las cuentas de usuarios del sistema.</p>
            </div>
            <?php if ($esAdmin): ?>
                <a href="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=crear" class="btn btn-outline-info">
                    <i class="bi bi-person-plus"></i> Nuevo usuario
                </a>
            <?php endif; ?>
        </section>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($esAdmin): ?>
            <section class="page-card card-section">
                <div class="section-header">
                    <h2>Usuarios del sistema</h2>
                    <p>Gestiona administradores, recepcionistas e instructores</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($usuario['username']) ?></strong>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($usuario['email'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= htmlspecialchars($usuario['role']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($usuario['active']): ?>
                                                <span class="badge badge-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=editar&id=<?= $usuario['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmarEliminar(<?= $usuario['id'] ?>, '<?= htmlspecialchars(addslashes($usuario['username'])) ?>')" 
                                                        class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 2rem; color: #94a3b8;">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else: ?>
            <section class="page-card card-section">
                <div class="section-header">
                    <h2>Acceso restringido</h2>
                </div>
                <div class="alert danger">
                    Usuario no tiene permisos para usar esta función
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<style>
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
}

.badge-info {
    background: rgba(59, 130, 246, 0.18);
    color: #60a5fa;
}

.badge-success {
    background: rgba(34, 197, 94, 0.18);
    color: #22c55e;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.18);
    color: #ef4444;
}

.btn-sm {
    padding: 0.5rem 0.75rem !important;
    font-size: 0.85rem !important;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-header h2 {
    margin-bottom: 0.5rem;
    color: #f8fafc;
}

.section-header p {
    color: #94a3b8;
    margin: 0;
}
</style>

<script>
function confirmarEliminar(id, username) {
    if (confirm(`¿Estás seguro de que deseas eliminar el usuario "${username}"?`)) {
        const data = new FormData();
        data.append('id', id);

        fetch('<?= BASE_URL ?>/app/controllers/UsuarioController.php?action=eliminar', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'No se pudo eliminar el usuario'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error al eliminar el usuario');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
