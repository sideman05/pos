<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/helpers.php';
require_login();
$user = current_user();
if ($user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    $stmt = $pdo->prepare('INSERT INTO products (sku, name, description, price, stock) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$sku, $name, $desc, $price, $stock]);
    header('Location: products.php');
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Products for POS System">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
<style>
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4); 
  backdrop-filter: blur(2px); 
  z-index: 5;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}


#overlay.active {
  opacity: 1;
  pointer-events: all;
}
#sidebarNav ul li a i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}


  
</style>
</head>

<body>
<header>
  <div class="menu-toggle" id="menuToggle">☰</div>

  <nav id="sidebarNav">
    <h1>Admin <br> POS Dashboard</h1>
    <ul>
      <li><a href="dashboard.php"><i class="fa-solid fa-house-user"></i> Dashboard</a></li>
      <li><a href="products.php" class="activ"><i class="fa-solid fa-box"></i> Products</a></li>
      <li><a href="inventory.php"><i class="fa-solid fa-warehouse"></i> Inventory</a></li>
      <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
      <li><a href="users.php"><i class="fa-solid fa-users-gear"></i> Manage Users</a></li>
      <li><a href="analys.php"><i class="fa-solid fa-chart-pie"></i> Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
      <li><a href="/pos/pos.php"><i class="fa-solid fa-cash-register"></i> POS</a></li>
      <li><a href="/pos/auth/out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
  </nav>
</header>
    <main class="products">
    <h2>Products</h2>

    <a href="#" id="openAddProduct">Add Product</a>

<!-- Popup Form -->
<div class="modal" id="productModal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add Product</h3>
    <form method="post" class="product-form">
      <input name="sku" placeholder="SKU"><br>
      <input name="name" placeholder="Name" required><br>
      <textarea name="description" placeholder="Description"></textarea><br>
      <input name="price" type="number" step="0.01" required placeholder="Price"><br>
      <input name="stock" type="number" required placeholder="Stock"><br>
      <button type="submit">Add Product</button>
    </form>
  </div>
</div>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
<tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td data-label="SKU"><?= htmlspecialchars($p['sku']) ?></td>
      <td data-label="Name"><?= htmlspecialchars($p['name']) ?></td>
      <td data-label="Price"><?= number_format($p['price'], 2) ?></td>
      <td data-label="Stock"><?= intval($p['stock']) ?></td>
      <td data-label="Actions">
        <a href="product_edit.php?id=<?= $p['id'] ?>">Edit</a> |
 <button class="deleteBtn" data-id="<?= $p['id'] ?>">Delete</button>

      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
    </table>
    <div id="overlay"></div>
    </main>
     <script src="../assets/js/app.js"></script>

    <script>
const menuToggle = document.getElementById('menuToggle');
const sidebarNav = document.getElementById('sidebarNav');
const overlay = document.getElementById('overlay');

menuToggle.addEventListener('click', () => {
  sidebarNav.classList.toggle('open');
  const isOpen = sidebarNav.classList.contains('open');
  menuToggle.textContent = isOpen ? '✖' : '☰';
  
  overlay.classList.toggle('active', isOpen);
});

overlay.addEventListener('click', () => {
  sidebarNav.classList.remove('open');
  overlay.classList.remove('active');
  menuToggle.textContent = '☰';
});

  const modal = document.getElementById('productModal');
  const openBtn = document.getElementById('openAddProduct');
  const closeBtn = document.querySelector('.close');

  openBtn.addEventListener('click', (e) => {
    e.preventDefault();
    modal.style.display = 'flex';
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });


  document.querySelectorAll('.deleteBtn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const productId = btn.dataset.id;

    const result = await Swal.fire({
      title: 'Are you sure?',
      text: "This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
      const response = await fetch(`product_delete.php?id=${productId}`);
      if (response.ok) {
        Swal.fire({
          title: 'Deleted!',
          text: 'Product has been removed.',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
        setTimeout(() => location.reload(), 1500);
      } else {
        Swal.fire('Error', 'Failed to delete product.', 'error');
      }
    }
  });
});

</script>


</body>

</html>