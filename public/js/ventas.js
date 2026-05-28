document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cartItems');
    const itemsJsonInput = document.getElementById('items_json');
    const totalInput = document.getElementById('venta_total');
    const cartTotalDisplay = document.getElementById('cartTotal');

    let cart = [];

    const formatMoney = (value) => {
        return `$${value.toFixed(2)}`;
    };

    const renderCart = () => {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<div class="empty-cart">Agrega productos al carrito para iniciar la venta.</div>';
            itemsJsonInput.value = '[]';
            totalInput.value = '0';
            cartTotalDisplay.textContent = '$0.00';
            return;
        }

        let total = 0;

        const rows = cart.map(item => {
            const subtotal = item.cantidad * item.precio_unitario;
            total += subtotal;

            return `
                <div class="cart-item" data-producto-id="${item.producto_id}">
                    <div class="cart-item-info">
                        <strong>${item.nombre}</strong>
                        <small>${formatMoney(item.precio_unitario)} cada uno</small>
                    </div>
                    <div class="cart-item-controls">
                        <input type="number" min="1" value="${item.cantidad}" class="cart-quantity" data-producto-id="${item.producto_id}">
                        <button type="button" class="btn-danger btn-remove-item" data-producto-id="${item.producto_id}">Eliminar</button>
                    </div>
                    <div class="cart-item-subtotal">${formatMoney(subtotal)}</div>
                </div>
            `;
        }).join('');

        cartItemsContainer.innerHTML = rows;
        itemsJsonInput.value = JSON.stringify(cart);
        totalInput.value = total.toFixed(2);
        cartTotalDisplay.textContent = formatMoney(total);
    };

    const addProductToCart = (product) => {
        const existing = cart.find(item => item.producto_id === product.producto_id);

        if (existing) {
            existing.cantidad += 1;
        } else {
            cart.push({
                producto_id: product.producto_id,
                nombre: product.nombre,
                cantidad: 1,
                precio_unitario: product.precio_unitario
            });
        }

        renderCart();
    };

    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                producto_id: parseInt(button.dataset.productoId, 10),
                nombre: button.dataset.nombre,
                precio_unitario: parseFloat(button.dataset.precio)
            };

            addProductToCart(product);
        });
    });

    cartItemsContainer.addEventListener('input', (event) => {
        if (event.target.matches('.cart-quantity')) {
            const productoId = parseInt(event.target.dataset.productoId, 10);
            const cantidad = parseInt(event.target.value, 10);

            const item = cart.find(row => row.producto_id === productoId);

            if (item) {
                item.cantidad = cantidad > 0 ? cantidad : 1;
                renderCart();
            }
        }
    });

    cartItemsContainer.addEventListener('click', (event) => {
        if (event.target.matches('.btn-remove-item')) {
            const productoId = parseInt(event.target.dataset.productoId, 10);
            cart = cart.filter(item => item.producto_id !== productoId);
            renderCart();
        }
    });

    const formVenta = document.getElementById('formVenta');

    if (formVenta) {
        formVenta.addEventListener('submit', (event) => {
            if (cart.length === 0) {
                event.preventDefault();
                alert('Agrega al menos un producto antes de completar la venta.');
            }
        });
    }

    renderCart();
});
