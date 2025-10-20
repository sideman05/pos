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
  display: flex;
  justify-content: space-around;
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
  overflow-y: auto;
  max-height: 500px; 
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

#checkout, #rists, #login {
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
a{
  text-decoration: none;
}
#checkout:hover, #rists:hover , #login{
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
  margin-top: 15px;
}

#pagination button {
  padding: 15px 20px;
  border: none;
  background: #eee;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.5s ease;
  font-weight: 500;
}

#pagination button:hover {
  background: var(--bg-main);
  color: white;
}

#pagination .active {
  background: #1e201eff;
  color: white;
  font-weight: bold;
}

@media print {
  body * {
    visibility: hidden !important;
  }

  #print-area, #print-area * {
    visibility: visible !important;
  }

  #print-area {
    position: fixed !important;
    top: 0 !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: 80mm !important; 
    max-height: 200mm !important; 
    margin: 0 !important;
    padding: 8px !important;
    background: #fff !important;
    color: #000 !important;
    font-family: "Courier New", monospace !important;
    font-size: 12px !important;
    text-align: center !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
  }
  html, body {
    width: 80mm !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: none !important;
  }

  @page {
    size: 80mm auto; 
    margin: 0;  
  }

  #print-area h2 {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 5px;
    border-bottom: 1px dashed #000;
    padding-bottom: 3px;
  }

  #print-area table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
  }

  #print-area th, #print-area td {
    padding: 3px 0;
    font-size: 12px;
  }

  #print-area th {
    border-bottom: 1px dashed #000;
    font-weight: bold;
  }

  #print-area td:nth-child(1) {
    text-align: left;
  }

  #print-area td:nth-child(2),
  #print-area td:nth-child(3),
  #print-area td:nth-child(4) {
    text-align: right;
  }

  #print-area tfoot td {
    border-top: 1px dashed #000;
    font-weight: bold;
    padding-top: 4px;
  }

  #print-area .footer {
    margin-top: 10px;
    border-top: 1px dashed #000;
    padding-top: 5px;
    font-size: 11px;
  }
}

  </style>
</head>

<body>
  <header class="header">
    <h1>POS System</h1>
    <a href="auth/login.php" id="login">Login</a>
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
    <button id="rists">Print Receipt</button>
  </div>
</div>
<div id="print-area" style="display:none;"></div>

<script src="assets/js/script.js"></script>
<script>
  document.getElementById('rists').addEventListener('click', () => {
  if (CART.length === 0) {
    alert("No items in cart to print.");
    return;
  }

  const printArea = document.getElementById('print-area');
  const now = new Date();
  const formattedDate = now.toLocaleString();

  let html = `
    <h2> Receipt</h2>
    <div style="text-align:center;font-size:13px;">Date: ${formattedDate}</div>
    <table>
      <thead>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
      </thead>
      <tbody>
  `;

  let total = 0;
  CART.forEach(item => {
    const itemTotal = item.qty * item.price;
    total += itemTotal;
    html += `
      <tr>
        <td>${item.name}</td>
        <td>${item.qty}</td>
        <td>${item.price.toFixed(2)}</td>
        <td>${itemTotal.toFixed(2)}</td>
      </tr>
    `;
  });

  html += `
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3">TOTAL</td>
          <td>${total.toFixed(2)}</td>
        </tr>
      </tfoot>
    </table>
    <div class="footer">Thank you for shopping! <br>Powered by Elly</div>
  `;

  printArea.innerHTML = html;
  printArea.style.display = "block";

window.scrollTo(0, 0);
document.body.style.overflow = "hidden";
window.print();
document.body.style.overflow = "auto";


  setTimeout(() => {
    printArea.style.display = "none";
  }, 1000);
});

</script>
</body>
</html>
