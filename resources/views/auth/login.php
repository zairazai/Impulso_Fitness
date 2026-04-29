<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: /Impulso_Fitness/dashboard.php");
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Impulso Fitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Impulso_Fitness/public/css/auth.css" rel="stylesheet">
</head>
<body>
    <div class="login-bg">
        <div class="login-overlay"></div>

        <div class="login-container">
            <div class="login-card">
                <div class="text-center mb-4">
                    <img src="/Impulso_Fitness/public/img/logo.png" alt="Logo Impulso Fitness" class="login-logo">
                    <h2 class="login-title">Iniciar Sesión</h2>
                     <p class="login-subtitle">Accede a tu cuenta para continuar</p>
                </div>

                <?php if ($error === 'credenciales'): ?>
                    <div class="alert alert-danger py-2">Usuario o contraseña incorrectos.</div>
                <?php elseif ($error === 'inactivo'): ?>
                    <div class="alert alert-warning py-2">Tu usuario está inactivo.</div>
                <?php elseif ($error === 'campos'): ?>
                    <div class="alert alert-warning py-2">Completa todos los campos.</div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="/Impulso_Fitness/login.php" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label login-label">Nombre de usuario</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text login-icon">👤</span>
                            <input type="text" id="username" name="username" class="form-control login-input" placeholder="Ingresa tu usuario" required>
                        </div>
                        <small class="field-error" id="error-username"></small>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label login-label">Contraseña</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text login-icon">🔒</span>
                            <input type="password" id="password" name="password" class="form-control login-input" placeholder="Ingresa tu contraseña" required>
                            <button class="btn toggle-password-btn" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                                👁️
                            </button>
                        </div>
                        <small class="field-error" id="error-password"></small>
                    </div>

                    <button type="submit" class="btn login-btn-primary w-100 mb-3">Entrar</button>

                    <button type="button" class="btn login-btn-secondary w-100 mb-3" id="btnFingerprint">
                        🖐️ Iniciar con huella
                    </button>

                    <div class="login-footer-row">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label login-remember" for="rememberMe">
                                Recuérdame
                            </label>
                        </div>

                        <a href="#" class="login-link">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/Impulso_Fitness/public/js/auth.js"></script>
</body>
</html>