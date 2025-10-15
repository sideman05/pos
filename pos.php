<?php
require_once __DIR__ . '/inc/db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>POS System</title>
  <script src="assets/js/app.js"></script>
  <style>
:root {
  /* Theme Colors */
  --bg-main: linear-gradient(135deg, #1a1a2e, #16213e);
  --accent: #00d4ff;
  --accent-alt: #00ffc3;
  --text-color: #f5f5f5;
  --card-bg: rgba(255, 255, 255, 0.07);
  --card-hover: rgba(0, 212, 255, 0.15);
  --input-bg: rgba(255, 255, 255, 0.1);
  --shadow: rgba(0, 0, 0, 0.25);

  /*  Blur & Glass */
  --blur-light: blur(8px);
  --blur-heavy: blur(15px);

  --radius: 16px;
  --radius-lg: 20px;
  --shadow-md: 0 8px 25px var(--shadow);
  --shadow-lg: 0 10px 40px var(--shadow);

  /* Typography */
  --font: "Poppins", sans-serif;
  --h1-size: 2rem;
  --h2-size: 1.4rem;
  --text-size: 1rem;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: var(--font);
}

body {
  background: var(--bg-main);
  color: var(--text-color);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

header {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: var(--blur-light);
  box-shadow: 0 4px 15px var(--shadow);
  padding: 20px;
  text-align: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

header h1 {
  font-size: var(--h1-size);
  color: var(--accent);
  letter-spacing: 1.5px;
  font-weight: 600;
}

.layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  padding: 30px;
  flex: 1;
}

.products, .cart {
  background: var(--card-bg);
  border-radius: var(--radius);
  padding: 20px;
  box-shadow: var(--shadow-md);
  backdrop-filter: var(--blur-heavy);
  transition: all 0.3s ease;
}

.products:hover, .cart:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
}

h2 {
  margin-bottom: 15px;
  color: var(--accent);
  border-left: 4px solid var(--accent);
  padding-left: 10px;
}

#product-list {
  display: grid;
  gap: 12px;
}

.product {
  background: rgba(255, 255, 255, 0.08);
  padding: 15px;
  border-radius: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: 0.3s;
}

.product:hover {
  background: var(--card-hover);
  transform: scale(1.02);
}

.product strong {
  font-size: 1.1rem;
  color: #fff;
}

.product div {
  display: flex;
  flex-direction: column;
}

.product button {
  background: var(--accent);
  color: #111;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.product button:hover {
  background: #04b5e1;
}

.product input[type="number"] {
  border: none;
  border-radius: 6px;
  padding: 5px;
  width: 60px;
  text-align: center;
  background: var(--input-bg);
  color: #fff;
}

.cart {
  border-radius: var(--radius-lg);
  padding: 25px;
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
}

.cart::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top right, rgba(0, 212, 255, 0.25), transparent 70%);
  z-index: 0;
}

.cart h2 {
  position: relative;
  z-index: 2;
  margin-bottom: 20px;
  font-size: var(--h2-size);
  text-align: center;
  background: linear-gradient(90deg, var(--accent), var(--accent-alt));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

#cart-items {
  position: relative;
  z-index: 2;
  flex: 1;
  overflow-y: auto; /* enable vertical scroll */
  max-height: 500px; /* adjust as needed */
  padding-right: 8px;
  margin-bottom: 15px;
}
#cart-items div {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 12px;
  margin-bottom: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
  transition: transform 0.2s ease, background 0.3s ease;
}

#cart-items div:hover {
  background: var(--card-hover);
  transform: scale(1.02);
}

#cart-total {
  position: relative;
  z-index: 2;
  text-align: right;
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--accent-alt);
  margin-bottom: 15px;
}

#checkout {
  position: relative;
  z-index: 2;
  background: linear-gradient(135deg, var(--accent), var(--accent-alt));
  color: #111;
  font-weight: 700;
  border: none;
  border-radius: 10px;
  padding: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
}

#checkout:hover {
  background: linear-gradient(135deg, var(--accent-alt), var(--accent));
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 212, 255, 0.45);
}
#cart-items input[type="number"] {
  border: none;
  border-radius: 6px;
  padding: 5px;
  width: 50px;
  text-align: center;
  background: var(--input-bg);
  color: #fff;
}
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background: var(--accent);
  border-radius: 6px;
}

/* ===== Responsive ===== */
@media (max-width: 900px) {
  .layout {
    grid-template-columns: 1fr;
  }
  .products, .cart {
    width: 100%;
  }
}
#search {
  width: 100%;
  padding: 10px 14px;
  margin-bottom: 20px;
  border-radius: 8px;
  border: none;
  background: var(--input-bg);
  color: var(--text-color);
  font-size: 1rem;
  outline: none;
  transition: all 0.3s ease;
}

#search:focus {
  background: rgba(255,255,255,0.15);
  box-shadow: 0 0 10px var(--accent);
}

#pagination {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 10px;
}

#pagination button {
  padding: 6px 12px;
  border: none;
  background: #eee;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
}

#pagination button:hover {
  background: #4CAF50;
  color: white;
}

#pagination .active {
  background: #4CAF50;
  color: white;
  font-weight: bold;
}


  </style>
</head>

<body>
  <header class="header">
    <h1>POS System</h1>
  </header>
<div class="layout">
  <div class="products">
    <h2>Products</h2>
    <input type="text" id="search" placeholder="Search product..." />
    <div id="product-list"></div>
    <div id="pagination"></div>
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
let filteredProducts = [];
let currentPage = 1;
const perPage = 6;

// Fetch products from backend
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

// Render product grid with pagination
function renderProducts() {
  const el = document.getElementById('product-list');
  el.innerHTML = '';

  const start = (currentPage - 1) * perPage;
  const end = start + perPage;
  const paginated = filteredProducts.slice(start, end);

  if (paginated.length === 0) {
    el.innerHTML = '<p style="text-align:center;opacity:0.6;">No products found.</p>';
    document.getElementById('pagination').innerHTML = '';
    return;
  }

  paginated.forEach(p => {
    const div = document.createElement('div');
    div.className = 'product';
    div.innerHTML = `
      <div class="p-info">
        <strong>${p.name}</strong>
        <div>Tsh. ${p.price.toFixed(2)}</div>
      </div>
      <div class="p-action">
        <input type="number" min="1" max="${p.stock}" value="1" data-id="${p.id}" class="qty"/>
        <button data-id="${p.id}" class="add-btn">Add</button>
      </div>
    `;
    el.appendChild(div);
  });

  // Add button events
  el.querySelectorAll('.add-btn').forEach(b => {
    b.addEventListener('click', e => {
      const id = parseInt(e.target.dataset.id);
      const qtyInput = el.querySelector(`input.qty[data-id="${id}"]`);
      const qty = Math.min(parseInt(qtyInput.value) || 1, PRODUCTS.find(p => p.id === id).stock);
      addToCart(id, qty);
    });
  });

  renderPagination();
}

// Pagination buttons
function renderPagination() {
  const totalPages = Math.ceil(filteredProducts.length / perPage);
  const pagination = document.getElementById('pagination');
  pagination.innerHTML = '';

  if (totalPages <= 1) return;

  // Prev button
  if (currentPage > 1) {
    const prev = document.createElement('button');
    prev.textContent = 'Prev';
    prev.className = 'page-btn';
    prev.addEventListener('click', () => {
      currentPage--;
      renderProducts();
    });
    pagination.appendChild(prev);
  }

  // Page numbers
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement('button');
    btn.textContent = i;
    btn.className = (i === currentPage) ? 'active' : 'page-btn';
    btn.addEventListener('click', () => {
      currentPage = i;
      renderProducts();
    });
    pagination.appendChild(btn);
  }

  // Next button
  if (currentPage < totalPages) {
    const next = document.createElement('button');
    next.textContent = 'Next';
    next.className = 'page-btn';
    next.addEventListener('click', () => {
      currentPage++;
      renderProducts();
    });
    pagination.appendChild(next);
  }
}

// Cart management
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

function renderCart() {
  const el = document.getElementById('cart-items');
  el.innerHTML = '';
  let total = 0;
  CART.forEach(item => {
    total += item.qty * item.price;
    const div = document.createElement('div');
    div.innerHTML = `
      <div>${item.name} x 
          <input type="number" min="1" max="${PRODUCTS.find(p=>p.id===item.product_id).stock}" value="${item.qty}" data-id="${item.product_id}" class="cart-qty"/>
      </div>
      <div>Tsh. ${(item.qty*item.price).toFixed(2)} 
          <button class="remove" data-id="${item.product_id}">✕</button>
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

// Checkout
document.getElementById('checkout').addEventListener('click', async () => {
  if (CART.length === 0) return alert('Cart is empty');

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
      alert('Sale recorded: #' + data.sale_id + '\nTotal: Tsh. ' + data.total);
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

// Live search
document.getElementById('search').addEventListener('input', e => {
  const q = e.target.value.toLowerCase().trim();
  filteredProducts = PRODUCTS.filter(p => p.name.toLowerCase().includes(q));
  currentPage = 1;
  renderProducts();
});

// Initial load
async function load() {
  PRODUCTS = await fetchProducts();
  filteredProducts = [...PRODUCTS];
  renderProducts();
  renderCart();
}
load();

  </script>
</body>
</html>
