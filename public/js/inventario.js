document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | ELEMENTOS DEL MODAL DE PRODUCTO
    |--------------------------------------------------------------------------
    */
    const modalProducto = document.getElementById('modalProducto');
    const btnNuevoProducto = document.getElementById('btnNuevoProducto');
    const btnCerrarModal = document.getElementById('btnCerrarModal');
    const btnCancelarProducto = document.getElementById('btnCancelarProducto');
    const stockLabel = document.getElementById('stockLabel');
    const stockHelpText = document.getElementById('stockHelpText');
    const stockLockBtn = document.getElementById('stockLockBtn');

    const formProducto = modalProducto ? modalProducto.querySelector('form') : null;

    /*
    |--------------------------------------------------------------------------
    | CAMPOS DEL FORMULARIO
    |--------------------------------------------------------------------------
    */
    const productoId = document.getElementById('producto_id');
    const codigo = document.getElementById('codigo');
    const nombre = document.getElementById('nombre');
    const categoria = document.getElementById('categoria');
    const icono = document.getElementById('icono');
    const costoCompra = document.getElementById('costo_compra');
    const precioVenta = document.getElementById('precio_venta');
    const stock = document.getElementById('stock');
    const stockMinimo = document.getElementById('stock_minimo');
    const descripcion = document.getElementById('descripcion');

    /*
    |--------------------------------------------------------------------------
    | ABRIR MODAL
    |--------------------------------------------------------------------------
    */
    function abrirModalProducto() {
        if (!modalProducto) return;

        modalProducto.classList.add('active');
    }

    /*
    |--------------------------------------------------------------------------
    | CERRAR MODAL
    |--------------------------------------------------------------------------
    */
    function cerrarModalProducto() {
        if (!modalProducto) return;

        modalProducto.classList.remove('active');
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPIAR FORMULARIO
    |--------------------------------------------------------------------------
    */
    function limpiarFormularioProducto() {
        if (!formProducto) return;

        formProducto.reset();

        productoId.value = '0';
        stock.value = '0';
        stockMinimo.value = '5';
        costoCompra.value = '0';
        icono.value = 'bi-box-seam';
    }

    /*
    |--------------------------------------------------------------------------
    | NUEVO PRODUCTO
    |--------------------------------------------------------------------------
    */
    if (btnNuevoProducto) {

    btnNuevoProducto.addEventListener('click', () => {

        limpiarFormularioProducto();

        stock.readOnly = false;

        stockLabel.textContent = 'Stock inicial';

        stockHelpText.textContent =
            'Solo se usa al crear el producto.';

        stockLockBtn.classList.add('d-none');

        stock.value = '0';

        abrirModalProducto();

    });

}

    /*
    |--------------------------------------------------------------------------
    | CERRAR MODAL DESDE BOTONES
    |--------------------------------------------------------------------------
    */
    if (btnCerrarModal) {
        btnCerrarModal.addEventListener('click', cerrarModalProducto);
    }

    if (btnCancelarProducto) {
        btnCancelarProducto.addEventListener('click', cerrarModalProducto);
    }

    /*
    |--------------------------------------------------------------------------
    | CERRAR MODAL AL DAR CLIC FUERA
    |--------------------------------------------------------------------------
    */
    if (modalProducto) {
        modalProducto.addEventListener('click', (event) => {
            if (event.target === modalProducto) {
                cerrarModalProducto();
            }
        });
    }

    if (stockLockBtn) {

    stockLockBtn.addEventListener('click', () => {

        alert(
            'El stock no se edita aquí. Registra una entrada, salida o ajuste en movimientos.'
        );

    });

}
    /*
    |--------------------------------------------------------------------------
    | EDITAR PRODUCTO
    |--------------------------------------------------------------------------
    */
    const editButtons = document.querySelectorAll('.edit-product-btn');

    editButtons.forEach((button) => {
        button.addEventListener('click', () => {
            productoId.value = button.dataset.id || '0';
            codigo.value = button.dataset.codigo || '';
            nombre.value = button.dataset.nombre || '';
            categoria.value = button.dataset.categoria || '';
            descripcion.value = button.dataset.descripcion || '';
            costoCompra.value = button.dataset.costoCompra || '0';
            precioVenta.value = button.dataset.precioVenta || '0';
            stock.value = button.dataset.stock || '0';
            stockLabel.textContent = 'Stock actual';
            stock.readOnly = true;

            stockHelpText.textContent =
                'El stock no se edita aquí. Para cambiarlo, registra una entrada, salida o ajuste en movimientos.';

            stockLockBtn.classList.remove('d-none');

            stockMinimo.value = button.dataset.stockMinimo || '5';
            icono.value = button.dataset.icono || 'bi-box-seam';

            abrirModalProducto();
        });
    });
    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR ELIMINACIÓN
    |--------------------------------------------------------------------------
    */
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const confirmar = confirm('¿Seguro que deseas dar de baja este producto?');

            if (!confirmar) {
                event.preventDefault();
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | OCULTAR ALERTAS AUTOMÁTICAMENTE
    |--------------------------------------------------------------------------
    */
    const alerts = document.querySelectorAll('.alert');

    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach((alert) => {
                alert.style.transition = 'opacity 0.4s ease';
                alert.style.opacity = '0';

                setTimeout(() => {
                    alert.style.display = 'none';
                }, 400);
            });
        }, 3000);
    }
});