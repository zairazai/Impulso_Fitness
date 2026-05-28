<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../../app/middleware/auth.php';

$extraCss = BASE_URL . '/public/css/reportes.css';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
<div class="reportes-module">
    <div class="layout-container">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">
            <section class="page-header">
                <div>
                    <h1><i class="bi bi-ticket-detailed"></i> Ingreso de personal</h1>
                    <p>Busca al socio y registra su acceso al gimnasio.</p>
                </div>
            </section>

            <section class="filter-panel">
                <form id="frm-buscar-socio" class="filter-form" style="gap: 0; position: relative;">
                    <div class="filter-row" style="width: 100%; position: relative;">
                        <input 
                            class="custom-input" 
                            type="text" 
                            id="input-buscar" 
                            placeholder="Nombre, email o teléfono..." 
                            autocomplete="off"
                            style="flex: 1;"
                        >
                        <button type="button" class="btn btn-primary" onclick="limpiarBusqueda()" style="margin-left: 0.5rem;">Limpiar</button>
                        <button type="button" class="btn btn-outline-info" onclick="mostrarHuellaDigital()" style="margin-left: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-fingerprint"></i> Ingresar con Huella
                        </button>
                    </div>
                    <div id="resultados-busqueda" class="dropdown-results" style="display: none;"></div>
                </form>
            </section>

            <section class="page-card card-section">
                <div class="section-header">
                    <h2>Ingreso rápido</h2>
                </div>
                <div class="quick-access-grid">
                    <article class="quick-card">
                        <div>
                            <h3>🔍 Busca el socio</h3>
                            <p>Ingresa el nombre, email o teléfono en el buscador de arriba.</p>
                        </div>
                    </article>
                    <article class="quick-card">
                        <div>
                            <h3>📋 Revisa su membresía</h3>
                            <p>Se mostrará si su plan está vigente o vencido.</p>
                        </div>
                    </article>
                    <article class="quick-card" style="cursor: pointer;" onclick="registrarSocioGeneral()">
                        <div>
                            <h3>👤 Socio general</h3>
                            <p>Pase diario para invitados sin membresía.</p>
                        </div>
                    </article>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- MODAL ACCESO PERMITIDO -->
<div id="modal-acceso-permitido" class="modal" style="display: none;">
    <div class="modal-content modal-acceso">
        <div class="modal-header success">
            <h2><i class="bi bi-check-circle"></i> ¡ACCESO CONCEDIDO!</h2>
            <button type="button" class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="socio-card-modal">
                <h3 id="modal-socio-nombre" style="font-size: 1.5rem; margin: 0 0 1rem;"></h3>
                
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span id="modal-socio-email" class="value">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Teléfono:</span>
                    <span id="modal-socio-telefono" class="value">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Membresía:</span>
                    <span id="modal-socio-membresia" class="value" style="color: #22c55e;">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Vence:</span>
                    <span id="modal-socio-vencimiento" class="value">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Días restantes:</span>
                    <span id="modal-socio-dias" class="value" style="color: #22c55e; font-weight: bold;">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="volverAlInicio()">
                <i class="bi bi-house"></i> Nuevo ingreso
            </button>
        </div>
    </div>
</div>

<!-- MODAL ACCESO DENEGADO -->
<div id="modal-acceso-denegado" class="modal" style="display: none;">
    <div class="modal-content modal-acceso">
        <div class="modal-header danger">
            <h2><i class="bi bi-x-circle"></i> ACCESO RESTRINGIDO</h2>
            <button type="button" class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="socio-card-modal">
                <h3 id="modal-denegado-nombre" style="font-size: 1.5rem; margin: 0 0 1rem;"></h3>
                
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 0.85rem; padding: 1rem; margin-bottom: 1rem;">
                    <p id="modal-denegado-razon" style="margin: 0; color: #fca5a5;"></p>
                </div>

                <div class="info-row">
                    <span class="label">Email:</span>
                    <span id="modal-denegado-email" class="value">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Teléfono:</span>
                    <span id="modal-denegado-telefono" class="value">-</span>
                </div>
                <div class="info-row">
                    <span class="label">Membresía:</span>
                    <span id="modal-denegado-membresia" class="value" style="color: #ef4444;">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" onclick="irAPago()">
                <i class="bi bi-credit-card"></i> Actualizar membresía
            </button>
            <button type="button" class="btn btn-secondary" onclick="volverAlInicio()">
                <i class="bi bi-house"></i> Atrás
            </button>
        </div>
    </div>
</div>

<!-- MODAL SOCIO GENERAL -->
<div id="modal-socio-general" class="modal" style="display: none;">
    <div class="modal-content modal-acceso">
        <div class="modal-header success">
            <h2><i class="bi bi-person-check"></i> Registro de invitado</h2>
            <button type="button" class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="socio-card-modal">
                <p style="color: #cbd5e1; margin-bottom: 1.5rem;">Completa los datos para registrar el acceso diario:</p>
                
                <div class="form-group">
                    <label>Nombre o identificación:</label>
                    <input type="text" id="input-invitado-nombre" class="custom-input" placeholder="Ej: Juan García">
                </div>

                <div class="form-group">
                    <label>Teléfono (opcional):</label>
                    <input type="text" id="input-invitado-telefono" class="custom-input" placeholder="+1 234 567 8901">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="confirmarSocioGeneral()">
                <i class="bi bi-check-circle"></i> Registrar acceso
            </button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal()">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<!-- MODAL ÉXITO SOCIO GENERAL -->
<div id="modal-exito-general" class="modal" style="display: none;">
    <div class="modal-content modal-acceso">
        <div class="modal-header success">
            <h2><i class="bi bi-check-circle"></i> ¡Acceso registrado!</h2>
        </div>
        <div class="modal-body">
            <p style="text-align: center; font-size: 1.1rem; color: #cbd5e1;">
                <strong id="modal-exito-nombre"></strong> ha ingresado al gimnasio.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="volverAlInicio()">
                <i class="bi bi-house"></i> Nuevo ingreso
            </button>
        </div>
    </div>
</div>

<style>
.dropdown-results {
    background: rgba(15, 23, 70, 0.95);
    border: 1px solid rgba(34, 211, 238, 0.28);
    border-radius: 0.85rem;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 0.5rem;
}

.dropdown-item {
    padding: 0.85rem 1rem;
    cursor: pointer;
    color: #f8fafc;
    border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    transition: background 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-item:hover {
    background: rgba(34, 211, 238, 0.12);
}

.dropdown-item-name {
    font-weight: 500;
}

.dropdown-item-email {
    font-size: 0.85rem;
    color: #94a3b8;
}

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
    max-width: 500px;
}

.modal-content.modal-acceso {
    max-width: 550px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    border-bottom: 1px solid rgba(34, 211, 238, 0.18);
    border-radius: 1.5rem 1.5rem 0 0;
}

.modal-header.success {
    background: rgba(16, 185, 129, 0.08);
    border-bottom-color: rgba(16, 185, 129, 0.18);
}

.modal-header.danger {
    background: rgba(239, 68, 68, 0.08);
    border-bottom-color: rgba(239, 68, 68, 0.18);
}

.modal-header h2 {
    margin: 0;
    color: #f8fafc;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-header.success h2 {
    color: #22c55e;
}

.modal-header.danger h2 {
    color: #ef4444;
}

.modal-close {
    background: none;
    border: none;
    color: #f8fafc;
    font-size: 1.8rem;
    cursor: pointer;
    padding: 0;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.modal-close:hover {
    color: #22d3ee;
}

.modal-body {
    padding: 2rem;
}

.socio-card-modal {
    background: rgba(15, 23, 70, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 1rem;
    padding: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.12);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row .label {
    color: #94a3b8;
    font-size: 0.95rem;
}

.info-row .value {
    color: #e2e8f0;
    font-weight: 500;
    text-align: right;
}

.form-group {
    margin-bottom: 1.2rem;
}

.form-group label {
    display: block;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    padding: 2rem;
    border-top: 1px solid rgba(34, 211, 238, 0.18);
    justify-content: flex-end;
}

.modal-footer .btn {
    flex: 1;
}

.filter-row {
    position: relative;
}

#input-buscar {
    width: 100%;
}
</style>

<script>
let socios = [];
let socioSeleccionado = null;

document.getElementById('input-buscar').addEventListener('input', buscarSocios);
document.getElementById('input-buscar').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});

function buscarSocios() {
    const query = document.getElementById('input-buscar').value.trim();
    const resultados = document.getElementById('resultados-busqueda');

    if (query.length < 2) {
        resultados.style.display = 'none';
        return;
    }

    fetch('<?= BASE_URL ?>/app/controllers/IngresoController.php?action=buscar&q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (!data.socios || data.socios.length === 0) {
                resultados.innerHTML = '<div class="dropdown-item" style="justify-content: center; cursor: default;"><em>No se encontraron socios</em></div>';
                resultados.style.display = 'block';
                return;
            }

            socios = data.socios;
            let html = '';
            data.socios.forEach((socio, index) => {
                const estado = socio.estado === 'activo' ? '✅ Activo' : '⚠️ Inactivo';
                html += `
                    <div class="dropdown-item" onclick="seleccionarSocio(${index})">
                        <div>
                            <div class="dropdown-item-name">${socio.nombre}</div>
                            <div class="dropdown-item-email">${socio.email || '-'}</div>
                        </div>
                        <div style="font-size: 0.85rem; color: #94a3b8;">${estado}</div>
                    </div>
                `;
            });

            resultados.innerHTML = html;
            resultados.style.display = 'block';
        })
        .catch(err => {
            console.error('Error:', err);
            resultados.innerHTML = '<div class="dropdown-item" style="color: #ef4444;">Error en la búsqueda</div>';
            resultados.style.display = 'block';
        });
}

function seleccionarSocio(index) {
    const socio = socios[index];
    socioSeleccionado = socio;

    fetch('<?= BASE_URL ?>/app/controllers/IngresoController.php?action=detalle&socio_id=' + socio.id)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                mostrarError(data.error);
                return;
            }

            // Si tiene membresía vigente
            if (data.membresia && data.membresia !== null) {
                mostrarAccesoPermitido(data.socio, data.membresia);
                registrarIngresoAjax(data.socio.id);
            } else {
                // No tiene membresía vigente
                mostrarAccesoDenegado(data.socio);
            }

            // Limpiar buscador
            document.getElementById('resultados-busqueda').style.display = 'none';
            document.getElementById('input-buscar').value = '';
        })
        .catch(err => {
            console.error('Error:', err);
            mostrarError('Error al obtener información del socio');
        });
}

function mostrarAccesoPermitido(socio, membresia) {
    document.getElementById('modal-socio-nombre').textContent = socio.nombre || '-';
    document.getElementById('modal-socio-email').textContent = socio.email || '-';
    document.getElementById('modal-socio-telefono').textContent = socio.telefono || '-';
    document.getElementById('modal-socio-membresia').textContent = membresia.membresia_nombre || '-';
    document.getElementById('modal-socio-vencimiento').textContent = membresia.fecha_fin ? new Date(membresia.fecha_fin).toLocaleDateString('es-ES') : '-';
    document.getElementById('modal-socio-dias').textContent = (membresia.dias_restantes || 0) + ' días';

    document.getElementById('modal-acceso-denegado').style.display = 'none';
    document.getElementById('modal-socio-general').style.display = 'none';
    document.getElementById('modal-exito-general').style.display = 'none';
    document.getElementById('modal-acceso-permitido').style.display = 'flex';
}

function mostrarAccesoDenegado(socio) {
    document.getElementById('modal-denegado-nombre').textContent = socio.nombre || '-';
    document.getElementById('modal-denegado-email').textContent = socio.email || '-';
    document.getElementById('modal-denegado-telefono').textContent = socio.telefono || '-';
    document.getElementById('modal-denegado-membresia').textContent = 'Sin membresía vigente';
    document.getElementById('modal-denegado-razon').textContent = 'La membresía de este socio ha vencido o no existe. Para ingresar debe actualizar su plan.';

    document.getElementById('modal-acceso-permitido').style.display = 'none';
    document.getElementById('modal-socio-general').style.display = 'none';
    document.getElementById('modal-exito-general').style.display = 'none';
    document.getElementById('modal-acceso-denegado').style.display = 'flex';
    
    // Registrar denegación en la base de datos
    registrarDenegacionAjax(socio.id, 'Membresía vencida');
}

function registrarIngresoAjax(socioId) {
    const data = new FormData();
    data.append('socio_id', socioId);

    fetch('<?= BASE_URL ?>/app/controllers/IngresoController.php?action=registrar', {
        method: 'POST',
        body: data
    })
    .catch(err => console.error('Error registrando ingreso:', err));
}

function registrarDenegacionAjax(socioId, motivo) {
    const data = new FormData();
    data.append('socio_id', socioId);
    data.append('motivo', motivo);

    fetch('<?= BASE_URL ?>/app/controllers/IngresoController.php?action=denegacion', {
        method: 'POST',
        body: data
    })
    .catch(err => console.error('Error registrando denegación:', err));
}

function registrarSocioGeneral() {
    document.getElementById('input-invitado-nombre').value = '';
    document.getElementById('input-invitado-telefono').value = '';
    document.getElementById('modal-acceso-permitido').style.display = 'none';
    document.getElementById('modal-acceso-denegado').style.display = 'none';
    document.getElementById('modal-exito-general').style.display = 'none';
    document.getElementById('modal-socio-general').style.display = 'flex';
}

function confirmarSocioGeneral() {
    const nombre = document.getElementById('input-invitado-nombre').value.trim();
    const telefono = document.getElementById('input-invitado-telefono').value.trim();

    if (!nombre) {
        alert('Por favor ingresa un nombre o identificación');
        return;
    }

    const data = new FormData();
    data.append('socio_id', 0); // 0 = socio general
    data.append('nombre_invitado', nombre);
    data.append('telefono_invitado', telefono);

    fetch('<?= BASE_URL ?>/app/controllers/IngresoController.php?action=registrar', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('modal-exito-nombre').textContent = nombre;
            document.getElementById('modal-socio-general').style.display = 'none';
            document.getElementById('modal-exito-general').style.display = 'flex';
        } else {
            alert('Error al registrar el acceso');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error al registrar el acceso');
    });
}

function mostrarError(mensaje) {
    alert(mensaje);
}

function cerrarModal() {
    document.getElementById('modal-acceso-permitido').style.display = 'none';
    document.getElementById('modal-acceso-denegado').style.display = 'none';
    document.getElementById('modal-socio-general').style.display = 'none';
    document.getElementById('modal-exito-general').style.display = 'none';
}

function irAPago() {
    window.location.href = '<?= BASE_URL ?>/app/controllers/MembresiaController.php?action=index';
}

function volverAlInicio() {
    window.location.href = '<?= BASE_URL ?>/app/controllers/IngresoController.php?action=index';
}

function limpiarBusqueda() {
    document.getElementById('input-buscar').value = '';
    document.getElementById('resultados-busqueda').style.display = 'none';
    cerrarModal();
}

function mostrarHuellaDigital() {
    alert('No programado');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
