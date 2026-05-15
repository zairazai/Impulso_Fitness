document.addEventListener('DOMContentLoaded', () => {
    // =========================================================
    // ALERTAS AUTOMÁTICAS
    // =========================================================
    const alerts = document.querySelectorAll('.alert');

    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.4s ease';
                alert.style.opacity = '0';

                setTimeout(() => {
                    alert.style.display = 'none';
                }, 400);
            });
        }, 3000);
    }

    // =========================================================
    // REFERENCIAS
    // =========================================================
    const cards = document.querySelectorAll('.membership-card');
    const paymentOptions = document.querySelectorAll('.payment-option');
    const buscarSocioInput = document.getElementById('buscarSocioInput');
    const sociosList = document.getElementById('sociosList');

    const planSeleccionadoInput = document.getElementById('planSeleccionadoInput');
    const totalInput = document.getElementById('totalInput');
    const metodoPagoInput = document.getElementById('metodoPagoInput');

    const planSeleccionadoPreview = document.getElementById('planSeleccionadoPreview');
    const totalPreview = document.getElementById('totalPreview');
    const metodoPagoPreview = document.getElementById('metodoPagoPreview');

    const formMembresia = document.getElementById('formMembresia');
    const socioIdSeleccionado = document.getElementById('socioIdSeleccionado');
    const fechaInicio = document.getElementById('fechaInicio');

    // =========================================================
    // BÚSQUEDA DE SOCIOS
    // =========================================================
    if (buscarSocioInput && sociosList) {
        buscarSocioInput.addEventListener('keyup', () => {
            const filtro = buscarSocioInput.value.toLowerCase();
            const items = sociosList.querySelectorAll('.socio-list-item');

            items.forEach(item => {
                const texto = item.innerText.toLowerCase();
                item.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    }

    // =========================================================
    // SELECCIÓN DE PLAN
    // =========================================================
    if (cards.length > 0) {
        cards.forEach(card => {
            card.addEventListener('click', () => {
                cards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');

                const plan = card.dataset.plan || '';
                const price = parseFloat(card.dataset.price || '0');

                if (planSeleccionadoInput) {
                    planSeleccionadoInput.value = plan;
                }

                if (totalInput) {
                    totalInput.value = price.toFixed(2);
                }

                if (planSeleccionadoPreview) {
                    planSeleccionadoPreview.textContent = plan || 'Ninguno';
                }

                if (totalPreview) {
                    totalPreview.textContent = '$' + price.toFixed(2);
                }
            });
        });
    }

    // =========================================================
    // SELECCIÓN DE MÉTODO DE PAGO
    // =========================================================
    if (paymentOptions.length > 0) {
        paymentOptions.forEach(option => {
            option.addEventListener('click', () => {
                paymentOptions.forEach(o => o.classList.remove('active'));
                option.classList.add('active');

                const method = option.dataset.method || '';

                if (metodoPagoInput) {
                    metodoPagoInput.value = method;
                }

                if (metodoPagoPreview) {
                    metodoPagoPreview.textContent = method || 'Ninguno';
                }
            });
        });
    }

    // =========================================================
    // VALIDACIÓN DEL FORMULARIO
    // =========================================================
    if (formMembresia) {
        formMembresia.addEventListener('submit', (e) => {
            const socioId = socioIdSeleccionado ? socioIdSeleccionado.value.trim() : '';
            const plan = planSeleccionadoInput ? planSeleccionadoInput.value.trim() : '';
            const metodo = metodoPagoInput ? metodoPagoInput.value.trim() : '';
            const fecha = fechaInicio ? fechaInicio.value.trim() : '';

            let errores = [];

            if (!socioId || socioId === '0') {
                errores.push('Selecciona un socio.');
            }

            if (!plan) {
                errores.push('Selecciona un plan de membresía.');
            }

            if (!fecha) {
                errores.push('Selecciona una fecha de inicio.');
            }

            if (!metodo) {
                errores.push('Selecciona un método de pago.');
            }

            if (errores.length > 0) {
                alert(errores.join('\n'));
                e.preventDefault();
            }
        });
    }
});