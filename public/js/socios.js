document.addEventListener('DOMContentLoaded', () => {
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

    const nombre = document.getElementById('nombre') 
    || document.querySelector('[name="nombre"]'); // hidden
    const nombres = document.getElementById('nombres') 
        || document.querySelector('[name="nombres"]');

    const apellidoPaterno = document.getElementById('apellidoPaterno') 
        || document.querySelector('[name="apellido_paterno"]');

    const apellidoMaterno = document.getElementById('apellidoMaterno') 
        || document.querySelector('[name="apellido_materno"]');

    const fechaNacimiento = document.getElementById('fechaNacimiento');
    const telefono = document.getElementById('telefono');
    const email = document.getElementById('email');
    const genero = document.getElementById('genero');
    const calle = document.getElementById('calle');
    const numero = document.getElementById('numero');
    const colonia = document.getElementById('colonia');
    const codigoPostal = document.getElementById('codigoPostal');

    const nombreContactoEmergencia = document.getElementById('nombreContactoEmergencia');
    const telefonoEmergencia = document.getElementById('telefonoEmergencia');

    const huellaDemo = document.getElementById('huellaDemo');
    const btnHuellaDemo = document.getElementById('btnHuellaDemo');
    const membresiaSeleccionada = document.getElementById('membresiaSeleccionada');
    const notas = document.querySelector('textarea[name="notas"]');

    const previewNombre = document.getElementById('previewNombre');
    const previewNacimiento = document.getElementById('previewNacimiento');
    const previewTelefono = document.getElementById('previewTelefono');
    const previewCorreo = document.getElementById('previewCorreo');
    const previewGenero = document.getElementById('previewGenero');
    const previewMembresia = document.getElementById('previewMembresia');
    const previewHuella = document.getElementById('previewHuella');

    function marcarError(input) {
        if (input) input.classList.add('input-error');
    }

    function limpiarError(input) {
        if (input) input.classList.remove('input-error');
    }

    function edadValida(fechaNacimientoValor) {
        const fecha = new Date(fechaNacimientoValor + 'T00:00:00');
        const hoy = new Date();

        if (isNaN(fecha.getTime()) || fecha > hoy) {
            return false;
        }

        let edad = hoy.getFullYear() - fecha.getFullYear();
        const mes = hoy.getMonth() - fecha.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < fecha.getDate())) {
            edad--;
        }

        return edad >= 14 && edad <= 100;
    }

    function soloNumeros(valor) {
        return /^\d+$/.test(valor);
    }

    function telefonoValido(valor) {
        return /^\d{10}$/.test(valor);
    }

    function codigoPostalValido(valor) {
        return /^\d{5}$/.test(valor);
    }

    function emailValido(valor) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
    }

    function numeroDireccionValido(valor) {
        return /^\d{1,20}$/.test(valor);
    }

    function actualizarNombreCompleto() {
        const valorNombres = nombres ? nombres.value.trim() : '';
        const valorPaterno = apellidoPaterno ? apellidoPaterno.value.trim() : '';
        const valorMaterno = apellidoMaterno ? apellidoMaterno.value.trim() : '';

        const nombreCompleto = `${valorNombres} ${valorPaterno} ${valorMaterno}`
            .replace(/\s+/g, ' ')
            .trim();

        if (nombre) {
            nombre.value = nombreCompleto;
            limpiarError(nombre);
        }

        if (previewNombre) {
            previewNombre.textContent = nombreCompleto || '-';
        }
    }

    [nombres, apellidoPaterno, apellidoMaterno].forEach(input => {
        if (input) {
            input.addEventListener('input', actualizarNombreCompleto);
        }
    });

    [calle, numero, colonia, codigoPostal].forEach(input => {
        if (input) {
            input.addEventListener('input', () => {
                if (input === codigoPostal) {
                    input.value = input.value.replace(/\D/g, '').slice(0, 5);
                }

                if (input === numero) {
                    input.value = input.value.replace(/\D/g, '').slice(0, 20);
                }

                limpiarError(input);
            });
        }
    });

    actualizarNombreCompleto();

    if (fechaNacimiento && previewNacimiento) {
        fechaNacimiento.addEventListener('change', () => {
            previewNacimiento.textContent = fechaNacimiento.value || '-';
            limpiarError(fechaNacimiento);
        });
    }

    if (telefono && previewTelefono) {
        telefono.addEventListener('input', () => {
            telefono.value = telefono.value.replace(/\D/g, '').slice(0, 10);
            previewTelefono.textContent = telefono.value.trim() || '-';
            limpiarError(telefono);
        });
    }

    if (email && previewCorreo) {
        email.addEventListener('input', () => {
            previewCorreo.textContent = email.value.trim() || '-';
            limpiarError(email);
        });
    }

    if (genero && previewGenero) {
        genero.addEventListener('change', () => {
            previewGenero.textContent = genero.value || '-';
            limpiarError(genero);
        });
    }

    if (huellaDemo && previewHuella) {
        huellaDemo.addEventListener('input', () => {
            previewHuella.textContent = huellaDemo.value.trim()
                ? 'Registrada (demo)'
                : 'No registrada';

            limpiarError(huellaDemo);
        });
    }

    if (telefonoEmergencia) {
        telefonoEmergencia.addEventListener('input', () => {
            telefonoEmergencia.value = telefonoEmergencia.value.replace(/\D/g, '').slice(0, 10);
            limpiarError(telefonoEmergencia);
        });
    }

    if (nombreContactoEmergencia) {
        nombreContactoEmergencia.addEventListener('input', () => {
            limpiarError(nombreContactoEmergencia);
        });
    }

    if (notas) {
        notas.addEventListener('input', () => {
            limpiarError(notas);
        });
    }

    if (btnHuellaDemo && huellaDemo) {
        btnHuellaDemo.addEventListener('click', () => {
            const valor = 'HUELLA-DEMO-' + Date.now();
            huellaDemo.value = valor;

            if (previewHuella) {
                previewHuella.textContent = 'Registrada (demo)';
            }

            limpiarError(huellaDemo);
            alert('Huella demo capturada correctamente.');
        });
    }

    const cards = document.querySelectorAll('.membership-card');

    if (cards.length > 0) {
        cards.forEach(card => {
            card.addEventListener('click', () => {
                cards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');

                const plan = card.dataset.plan || '';

                if (membresiaSeleccionada) {
                    membresiaSeleccionada.value = plan;
                    limpiarError(membresiaSeleccionada);
                }

                if (previewMembresia) {
                    previewMembresia.textContent = plan || 'Ninguno';
                }
            });
        });
    }

    const buscarSocioInput = document.getElementById('buscarSocioInput');
    const sociosList = document.getElementById('sociosList');

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

    const formSocioCreate = document.getElementById('formSocioCreate');

    if (formSocioCreate) {
        formSocioCreate.addEventListener('submit', (e) => {
            actualizarNombreCompleto();

            let hayErrores = false;

            const valorNombre = nombre ? nombre.value.trim() : '';
            const valorNombres = nombres ? nombres.value.trim() : '';
            const valorApellidoPaterno = apellidoPaterno ? apellidoPaterno.value.trim() : '';

            const valorFechaNacimiento = fechaNacimiento ? fechaNacimiento.value.trim() : '';
            const valorTelefono = telefono ? telefono.value.trim() : '';
            const valorEmail = email ? email.value.trim() : '';
            const valorGenero = genero ? genero.value.trim() : '';

            const valorNombreContactoEmergencia = nombreContactoEmergencia ? nombreContactoEmergencia.value.trim() : '';
            const valorTelefonoEmergencia = telefonoEmergencia ? telefonoEmergencia.value.trim() : '';
            const valorCalle = calle ? calle.value.trim() : '';
            const valorNumero = numero ? numero.value.trim() : '';
            const valorColonia = colonia ? colonia.value.trim() : '';
            const valorCodigoPostal = codigoPostal ? codigoPostal.value.trim() : '';

            const valorHuella = huellaDemo ? huellaDemo.value.trim() : '';
            const valorNotas = notas ? notas.value.trim() : '';
            const valorMembresia = membresiaSeleccionada ? membresiaSeleccionada.value.trim() : '';

            [
                nombres,
                apellidoPaterno,
                apellidoMaterno,
                fechaNacimiento,
                telefono,
                email,
                genero,
                nombreContactoEmergencia,
                telefonoEmergencia,
                calle,
                numero,
                colonia,
                codigoPostal,
                huellaDemo,
                notas,
                membresiaSeleccionada
            ].forEach(limpiarError);

            if (valorNombres.length < 2 || valorNombres.length > 100) {
                marcarError(nombres);
                hayErrores = true;
            }

            if (valorApellidoPaterno.length < 2 || valorApellidoPaterno.length > 80) {
                marcarError(apellidoPaterno);
                hayErrores = true;
            }

            if (valorNombre.length < 3 || valorNombre.length > 260) {
                marcarError(nombre);
                marcarError(nombres);
                marcarError(apellidoPaterno);
                hayErrores = true;
            }

            if (!edadValida(valorFechaNacimiento)) {
                marcarError(fechaNacimiento);
                hayErrores = true;
            }

            if (!telefonoValido(valorTelefono)) {
                marcarError(telefono);
                hayErrores = true;
            }

            if (!emailValido(valorEmail)) {
                marcarError(email);
                hayErrores = true;
            }

            if (valorGenero === '') {
                marcarError(genero);
                hayErrores = true;
            }

            if (valorNombreContactoEmergencia.length < 3 || valorNombreContactoEmergencia.length > 100) {
                marcarError(nombreContactoEmergencia);
                hayErrores = true;
            }

            if (!telefonoValido(valorTelefonoEmergencia)) {
                marcarError(telefonoEmergencia);
                hayErrores = true;
            }

            if (valorCalle.length < 2 || valorCalle.length > 120) {
                marcarError(calle);
                hayErrores = true;
            }

            if (!numeroDireccionValido(valorNumero)) {
                marcarError(numero);
                hayErrores = true;
            }

            if (valorColonia.length < 2 || valorColonia.length > 120) {
                marcarError(colonia);
                hayErrores = true;
            }

            if (!codigoPostalValido(valorCodigoPostal)) {
                marcarError(codigoPostal);
                hayErrores = true;
            }

            if (valorMembresia === '') {
                marcarError(membresiaSeleccionada);
                hayErrores = true;
            }

            if (valorHuella === '' || valorHuella.length > 255) {
                marcarError(huellaDemo);
                hayErrores = true;
            }

            if (valorNotas === '' || valorNotas.length > 1000) {
                marcarError(notas);
                hayErrores = true;
            }

            if (hayErrores) {
                alert('Revisa los campos marcados antes de continuar.');
                e.preventDefault();
            }
        });
    }

    const formSocioEdit = document.getElementById('formSocioEdit');

if (formSocioEdit) {
    formSocioEdit.addEventListener('submit', (e) => {
        let hayErrores = false;

        [
            nombres,
            apellidoPaterno,
            fechaNacimiento,
            telefono,
            email,
            genero,
            nombreContactoEmergencia,
            telefonoEmergencia,
            calle,
            numero,
            colonia,
            codigoPostal
        ].forEach(limpiarError);

        if (!nombres || nombres.value.trim().length < 2) {
            marcarError(nombres);
            hayErrores = true;
        }

        if (!apellidoPaterno || apellidoPaterno.value.trim().length < 2) {
            marcarError(apellidoPaterno);
            hayErrores = true;
        }

        if (!fechaNacimiento || !edadValida(fechaNacimiento.value.trim())) {
            marcarError(fechaNacimiento);
            hayErrores = true;
        }

        if (!telefono || !telefonoValido(telefono.value.trim())) {
            marcarError(telefono);
            hayErrores = true;
        }

        if (!email || !emailValido(email.value.trim())) {
            marcarError(email);
            hayErrores = true;
        }

        if (!genero || genero.value.trim() === '') {
            marcarError(genero);
            hayErrores = true;
        }

        if (!nombreContactoEmergencia || nombreContactoEmergencia.value.trim().length < 3) {
            marcarError(nombreContactoEmergencia);
            hayErrores = true;
        }

        if (!telefonoEmergencia || !telefonoValido(telefonoEmergencia.value.trim())) {
            marcarError(telefonoEmergencia);
            hayErrores = true;
        }

        if (!calle || calle.value.trim().length < 2) {
            marcarError(calle);
            hayErrores = true;
        }

        if (!numero || !numeroDireccionValido(numero.value.trim())) {
            marcarError(numero);
            hayErrores = true;
        }

        if (!colonia || colonia.value.trim().length < 2) {
            marcarError(colonia);
            hayErrores = true;
        }

        if (!codigoPostal || !codigoPostalValido(codigoPostal.value.trim())) {
            marcarError(codigoPostal);
            hayErrores = true;
        }

        if (hayErrores) {
            alert('Revisa los campos marcados antes de guardar.');
            e.preventDefault();
        }
    });
}

});