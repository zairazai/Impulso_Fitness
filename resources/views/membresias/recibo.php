<?php

/*
|--------------------------------------------------------------------------
| MIDDLEWARE DE AUTENTICACIÓN
|--------------------------------------------------------------------------
| Validamos que el usuario tenga sesión activa.
| Esta vista NO carga header ni sidebar porque debe imprimirse limpia.
*/
require_once __DIR__ . '/../../../app/middleware/auth.php';

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN Y MODELO
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/models/Membresia.php';

$modelo = new Membresia();

/*
|--------------------------------------------------------------------------
| ACTUALIZAR ESTADOS DE MEMBRESÍAS
|--------------------------------------------------------------------------
| Sincroniza estados antes de mostrar el recibo.
*/
$modelo->actualizarEstadosMembresias();

/*
|--------------------------------------------------------------------------
| OBTENER ID DEL PAGO
|--------------------------------------------------------------------------
*/
$pagoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($pagoId <= 0) {
    header('Location: ' . BASE_URL . '/resources/views/membresias/historial.php?error=recibo');
    exit;
}

/*
|--------------------------------------------------------------------------
| OBTENER DATOS DEL RECIBO
|--------------------------------------------------------------------------
*/
$recibo = $modelo->obtenerReciboMembresia($pagoId);

if (!$recibo) {
    header('Location: ' . BASE_URL . '/resources/views/membresias/historial.php?error=recibo');
    exit;
}

/*
|--------------------------------------------------------------------------
| REFERENCIA
|--------------------------------------------------------------------------
| La referencia ya viene desde la BD.
| Si algún pago antiguo no la tiene, se genera una temporal para mostrarla.
*/
$referencia = $recibo['referencia'] ?? '';

if ($referencia === '' || $referencia === null) {
    $referencia = 'REC-' .
        date('Ymd', strtotime($recibo['fecha_pago'])) .
        '-' .
        str_pad((string)$recibo['pago_id'], 6, '0', STR_PAD_LEFT);
}

/*
|--------------------------------------------------------------------------
| FUNCIÓN AUXILIAR PARA FECHAS
|--------------------------------------------------------------------------
*/
function formatoFechaRecibo(?string $fecha): string
{
    if (!$fecha) {
        return '-';
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return '-';
    }

    return date('d/m/Y H:i', $timestamp);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Membresía</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/recibo.css">
</head>
<body>

<div class="receipt-wrapper">
    <div class="receipt-card">

        <div class="receipt-header">
            <div class="receipt-brand">
                <h1>Impulso Fitness</h1>
                <p>Comprobante de pago de membresía</p>
            </div>

            <div class="receipt-info">
                <h2>Recibo #<?= htmlspecialchars($recibo['pago_id']) ?></h2>
                <p><?= formatoFechaRecibo($recibo['fecha_pago']) ?></p>
                <p class="receipt-reference"><?= htmlspecialchars($referencia) ?></p>
            </div>
        </div>

        <div class="receipt-grid">
            <div class="receipt-section">
                <h3>Datos del socio</h3>

                <p><strong>Nombre:</strong> <?= htmlspecialchars($recibo['socio'] ?? '-') ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($recibo['telefono'] ?? '-') ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($recibo['email'] ?? '-') ?></p>
                <p><strong>Estado actual:</strong> <?= htmlspecialchars(strtoupper($recibo['estado'] ?? '-')) ?></p>
            </div>

            <div class="receipt-section">
                <h3>Datos de la membresía</h3>

                <p><strong>Plan:</strong> <?= htmlspecialchars($recibo['membresia'] ?? '-') ?></p>
                <p><strong>Duración:</strong> <?= htmlspecialchars($recibo['duracion_dias'] ?? '-') ?> días</p>
                <p><strong>Inicio:</strong> <?= formatoFechaRecibo($recibo['fecha_inicio'] ?? null) ?></p>
                <p><strong>Fin:</strong> <?= formatoFechaRecibo($recibo['fecha_fin'] ?? null) ?></p>
            </div>
        </div>

        <div class="receipt-grid">
            <div class="receipt-section">
                <h3>Datos del pago</h3>

                <p><strong>Método:</strong> <?= htmlspecialchars(ucfirst($recibo['metodo_pago'] ?? '-')) ?></p>
                <p><strong>Referencia:</strong> <?= htmlspecialchars($referencia) ?></p>
                <p><strong>Fecha de pago:</strong> <?= formatoFechaRecibo($recibo['fecha_pago'] ?? null) ?></p>
            </div>

            <div class="receipt-section">
                <h3>Observaciones</h3>

                <p>Este comprobante confirma el registro del pago de membresía.</p>
                <p>La vigencia se calcula automáticamente de acuerdo con el plan adquirido.</p>
            </div>
        </div>

        <div class="receipt-total">
            <div>
                <span>Total pagado</span>
            </div>

            <strong>$<?= number_format((float)($recibo['monto'] ?? 0), 2) ?> MXN</strong>
        </div>

        <p class="receipt-note">
            Gracias por formar parte de Impulso Fitness.
        </p>

        <div class="receipt-actions">
            <a
                href="<?= BASE_URL ?>/resources/views/membresias/historial.php"
                class="btn btn-secondary"
            >
                Volver al historial
            </a>

            <button
                type="button"
                class="btn btn-primary"
                onclick="window.print()"
            >
                Imprimir recibo
            </button>
        </div>

    </div>
</div>

</body>
</html>