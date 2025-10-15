<?php
require_once __DIR__ . '/inc/db.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>POS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .layout {
            display: flex;
            gap: 16px;
            padding: 16px
        }

        .products {
            flex: 2
        }

        .cart {
            flex: 1;
            background: #fff;
            padding: 12px;
            border-radius: 6px
        }

        .product {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>POS</h1>
    </header>
    <div class="layout">
        <div class="products">
            <h2>Products</h2>
            <div id="product-list"></div>
        </div>
        <div class="cart">
            <h2>Cart</h2>
            <div id="cart-items">(empty)</div>
            <div id="cart-total"></div>
            <button id="checkout">Checkout</button>
        </div>
    </div>
<script>
let PRODUCTS = [];
let CART = [];

async function fetchProducts() {
    try {
        const res = await fetch('api/product_api.php?action=list');
        if (!res.ok) throw new Error('Failed to fetch products');
        const data = await res.json();

        return data.map(p => ({
            id: parseInt(p.id),
            name: p.name,
            price: parseFloat(p.price),
            stock: parseInt(p.stock)
        }));
    } catch (err) {
        console.error(err);
        return [];
    }
}

function renderProducts() {
    const el = document.getElementById('product-list');
    el.innerHTML = '';
    PRODUCTS.forEach(p => {
        const div = document.createElement('div');
        div.className = 'product';
        div.innerHTML = `
            <div>
                <strong>${p.name}</strong>
                <div>Tsh. ${p.price.toFixed(2)}</div>
            </div>
            <div>
                <input type="number" min="1" max="${p.stock}" value="1" data-id="${p.id}" class="qty" style="width:70px"/>
                <button data-id="${p.id}">Add</button>
            </div>
        `;
        el.appendChild(div);
    });

    
    el.querySelectorAll('button').forEach(b => {
        b.addEventListener('click', e => {
            const id = parseInt(e.target.dataset.id);
            const qtyInput = el.querySelector(`input.qty[data-id="${id}"]`);
            const qty = Math.min(parseInt(qtyInput.value) || 1, PRODUCTS.find(p => p.id === id).stock);
            addToCart(id, qty);
        });
    });
}


function addToCart(id, qty) {
    const prod = PRODUCTS.find(p => p.id === id);
    if (!prod) return;
    const existing = CART.find(c => c.product_id === id);
    if (existing) {
        existing.qty = Math.min(existing.qty + qty, prod.stock);
    } else {
        CART.push({
            product_id: id,
            name: prod.name,
            price: prod.price,
            qty: qty
        });
    }
    renderCart();
}

// Render cart
function renderCart() {
    const el = document.getElementById('cart-items');
    el.innerHTML = '';
    let total = 0;
    CART.forEach(item => {
        total += item.qty * item.price;
        const div = document.createElement('div');
        div.innerHTML = `
            <div>${item.name} x 
                <input type="number" min="1" max="${PRODUCTS.find(p=>p.id===item.product_id).stock}" value="${item.qty}" data-id="${item.product_id}" class="cart-qty" style="width:60px"/>
            </div>
            <div>Tsh. ${(item.qty*item.price).toFixed(2)} 
                <button class="remove" data-id="${item.product_id}">Remove</button>
            </div>
        `;
        el.appendChild(div);
    });
    document.getElementById('cart-total').textContent = 'Total: Tsh. ' + total.toFixed(2);


    el.querySelectorAll('input.cart-qty').forEach(i => {
        i.addEventListener('change', e => {
            const id = parseInt(e.target.dataset.id);
            const val = Math.min(Math.max(parseInt(e.target.value) || 1, 1), PRODUCTS.find(p => p.id === id).stock);
            CART.find(c => c.product_id === id).qty = val;
            renderCart();
        });
    });


    el.querySelectorAll('button.remove').forEach(b => {
        b.addEventListener('click', e => {
            const id = parseInt(e.target.dataset.id);
            CART = CART.filter(c => c.product_id !== id);
            renderCart();
        });
    });
}

document.getElementById('checkout').addEventListener('click', async () => {
    if (CART.length === 0) return alert('Cart empty');

    const items = CART.map(i => ({
        product_id: parseInt(i.product_id),
        qty: parseInt(i.qty)
    }));

    try {
        const res = await fetch('api/cart_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items })
        });

        const data = await res.json();
        if (data.ok) {
            alert('Sale recorded: ' + data.sale_id + '\nTotal: Tsh. ' + data.total);
            CART = [];
            load();
        } else {
            alert('Error: ' + (data.error || 'Unknown'));
        }
    } catch (err) {
        console.error('Checkout error:', err);
        alert('Failed to complete checkout.');
    }
});


async function load() {
    PRODUCTS = await fetchProducts();
    renderProducts();
    renderCart();
}

load();
</script>

</body>

</html>