<?php
require_once __DIR__ . '/../../../config/app.php';
?><!-- reset password form -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Restablecer contraseña</title>
    <link href="/Impulso_Fitness/public/css/auth.css" rel="stylesheet">
    <style>.reset-card{max-width:420px;margin:6rem auto;} .small-note{font-size:0.92rem;color:#94a3b8;}</style>
</head>
<body>
    <div class="reset-card">
        <h2>Restablecer contraseña</h2>
        <p class="small-note">Introduce tu nueva contraseña.</p>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'campos'): ?>
            <div class="alert danger">Completa los campos o las contraseñas no coinciden.</div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/app/controllers/AuthController.php?action=reset">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password" name="password" class="custom-input" required>
            </div>
            <div class="form-group">
                <label>Confirmar contraseña</label>
                <input type="password" name="password2" class="custom-input" required>
            </div>

            <div style="margin-top:12px;">
                <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                <a href="<?= BASE_URL ?>/resources/views/auth/login.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
