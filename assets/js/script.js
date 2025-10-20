let PRODUCTS = [];
let CART = [];
let filteredProducts = [];
let currentPage = 1;
const perPage = 6;

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


function renderPagination() {
  const totalPages = Math.ceil(filteredProducts.length / perPage);
  const pagination = document.getElementById('pagination');
  pagination.innerHTML = '';

  if (totalPages <= 1) return;

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
          <button class="remove" data-id="${item.product_id}" style="background: #e03327e0; padding: .5rem; border: none; border-radius: .4rem; color: #fff; margin-left:5px;">Remove</button>
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

document.getElementById('search').addEventListener('input', e => {
  const q = e.target.value.toLowerCase().trim();
  filteredProducts = PRODUCTS.filter(p => p.name.toLowerCase().includes(q));
  currentPage = 1;
  renderProducts();
});

async function load() {
  PRODUCTS = await fetchProducts();
  filteredProducts = [...PRODUCTS];
  renderProducts();
  renderCart();
}
load();


