<?php
/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN Y SEGURIDAD EXTRA
|--------------------------------------------------------------------------
| Cargamos la configuración general y mantenemos una validación adicional
| de sesión como respaldo del flujo del controlador.
| Esta vista NO carga header ni sidebar porque debe imprimirse limpia.
*/

require_once __DIR__ . '/../../../app/middleware/auth.php';
require_once __DIR__ . '/../../../config/app.php';

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
    <title>Nota de Venta</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/recibo.css">
</head>
<body>

<div class="receipt-wrapper">
    <div class="receipt-card">

        <div class="receipt-header">
            <div class="receipt-brand">
                <h1>Impulso Fitness</h1>
                <p>Nota de venta</p>
            </div>

            <div class="receipt-info">
                <h2>Venta #<?= htmlspecialchars($venta['id']) ?></h2>
                <p><?= formatoFechaRecibo($venta['fecha']) ?></p>
                <p class="receipt-reference"><?= htmlspecialchars($venta['metodo_pago']) ?></p>
            </div>
        </div>

        <div class="receipt-grid">
            <div class="receipt-section">
                <h3>Datos del cliente</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($venta['socio'] ?? 'Cliente general') ?></p>
                <p><strong>Usuario:</strong> <?= htmlspecialchars($venta['usuario']) ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($venta['telefono'] ?? '-') ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($venta['email'] ?? '-') ?></p>
            </div>

            <div class="receipt-section">
                <h3>Detalles de venta</h3>
                <p><strong>Método:</strong> <?= htmlspecialchars(ucfirst($venta['metodo_pago'])) ?></p>
                <p><strong>Total:</strong> $<?= number_format((float)$venta['total'], 2) ?> MXN</p>
            </div>
        </div>

        <div class="receipt-table">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalle as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['producto']) ?></td>
                            <td><?= htmlspecialchars($item['codigo']) ?></td>
                            <td><?= (int)$item['cantidad'] ?></td>
                            <td>$<?= number_format((float)$item['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format((float)$item['cantidad'] * (float)$item['precio_unitario'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-total">
            <div>
                <span>Total de la venta</span>
            </div>
            <strong>$<?= number_format((float)$venta['total'], 2) ?> MXN</strong>
        </div>

        <p class="receipt-note">
            Gracias por su compra. Esta nota puede utilizarse como comprobante de venta.
        </p>

        <div class="receipt-actions">
            <a
                href="<?= BASE_URL ?>/app/controllers/VentaController.php?action=historial"
                class="btn btn-secondary"
            >
                Volver al historial
            </a>
            <button
                type="button"
                class="btn btn-primary"
                onclick="window.print()"
            >
                Imprimir nota
            </button>
        </div>

    </div>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 300);
    });
</script>
</body>
</html>
