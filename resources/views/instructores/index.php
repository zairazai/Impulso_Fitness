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
        <section class="page-header">
            <div>
                <h1><i class="bi bi-person-badge"></i> Entrenadores</h1>
                <p>Administra perfiles de entrenadores y su información de contacto.</p>
            </div>
        </section>

        <section class="page-card card-section">
            <div class="empty-box">
                Aquí podrás ver y editar los entrenadores del gimnasio.
            </div>
        </section>
    </main>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>