<?php
require_once __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña</title>
    <link href="/Impulso_Fitness/public/css/auth.css" rel="stylesheet">
    <style>
        body { background: #081221; }
        .forgot-card { width: 100%; max-width: 420px; margin: 4rem auto; }
        .forgot-header { text-align: center; margin-bottom: 1.25rem; }
        .forgot-title { color: #ffffff; font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem; }
        .forgot-subtitle { color: #9fb0cb; margin: 0 auto; max-width: 320px; font-size: 0.92rem; line-height: 1.5; }
        .alert.success { background: rgba(34, 197, 94, 0.16); border-color: rgba(34, 197, 94, 0.25); color: #d9f99d; }
        .alert.warning { background: rgba(251, 191, 36, 0.16); border-color: rgba(251, 191, 36, 0.25); color: #facc15; }
        .alert.danger { background: rgba(239, 68, 68, 0.16); border-color: rgba(239, 68, 68, 0.25); color: #fecaca; }
        .btn-secondary { min-width: unset; }
    </style>
</head>
<body>
    <div class="login-bg">
        <div class="login-overlay"></div>
        <div class="login-container forgot-card">
            <div class="login-card">
                <div class="forgot-header">
                    <img src="/Impulso_Fitness/public/img/logo.png" alt="Logo Impulso Fitness" class="login-logo">
                    <h2 class="forgot-title">¿Olvidaste tu contraseña?</h2>
                    <p class="forgot-subtitle">Escribe tu correo registrado y recibirás una nueva contraseña segura directamente en tu email.</p>
                </div>

                <?php if (isset($_GET['success']) && $_GET['success'] === 'sent'): ?>
                    <div class="alert success">Si existe una cuenta asociada, recibirás un correo con tu nueva contraseña.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'campos'): ?>
                    <div class="alert danger">Completa el campo correo.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'mail'): ?>
                    <div class="alert warning">No se pudo enviar el correo. Revisa la configuración SMTP y vuelve a intentar.</div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/app/controllers/AuthController.php?action=forgot">
                    <div class="mb-3">
                        <label class="form-label login-label">Correo electrónico</label>
                        <div class="login-input-group">
                            <span class="input-group-text login-icon">📧</span>
                            <input type="email" name="email" class="login-input" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <button type="submit" class="login-btn-primary w-100 mb-3">Enviar nueva contraseña</button>
                    <a href="<?= BASE_URL ?>/resources/views/auth/login.php" class="login-btn-secondary w-100">Volver al login</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
