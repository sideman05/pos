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

// 🧮 Pagination setup
$limit = 10; // products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Count total records
$total_records = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch paginated products
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
  
    .pagination {
      margin-top: 1rem;
      display: flex;
      justify-content: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .pagination a {
      padding: 6px 12px;
      background: #f5f5f5;
      color: #333;
      text-decoration: none;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      transition: background 0.3s;
    }

    .pagination a:hover {
      background: var(--accent-color);
      color: #fff;
    }

    .pagination .active {
      background: var(--accent-color);
      color: #fff;
      font-weight: bold;
    }


  </style>
</head>

<body>
<header>
  <div class="menu-toggle" id="menuToggle">☰</div>
  <nav id="sidebarNav">
               <h1>Admin <br> POS Dashboard</h1>

    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="products.php" class="activ">Products</a></li>
      <li><a href="inventory.php">Inventory</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="users.php">Manage users</a></li>
      <li><a href="analys.php">Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php">Notifications</a></li>
      <li><a href="/pos/pos.php">POS</a></li>
      <li><a href="/pos/auth/out.php">Logout</a></li>
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
          <td><?= htmlspecialchars($p['sku']) ?></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= number_format($p['price'], 2) ?></td>
          <td><?= intval($p['stock']) ?></td>
          <td>
            <a href="product_edit.php?id=<?= $p['id'] ?>">Edit</a> |
            <button class="deleteBtn" data-id="<?= $p['id'] ?>">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>


  <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">« Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next »</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>

<script>
const menuToggle = document.getElementById('menuToggle');
const sidebarNav = document.getElementById('sidebarNav');
menuToggle.addEventListener('click', () => {
  sidebarNav.classList.toggle('open');
  menuToggle.textContent = sidebarNav.classList.contains('open') ? '✖' : '☰';
});

const modal = document.getElementById('productModal');
const openBtn = document.getElementById('openAddProduct');
const closeBtn = document.querySelector('.close');

openBtn.addEventListener('click', (e) => {
  e.preventDefault();
  modal.style.display = 'flex';
});

closeBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

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
