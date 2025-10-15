<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/helpers.php';
require_login();
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

    <link rel="stylesheet" href="../assets/css/style.css">
<style>

</style>
</head>

<body>
 <header>
  <div class="menu-toggle" id="menuToggle">☰</div>

  <nav id="sidebarNav">
  <h1>POS Dashboard</h1>
      <ul>
        <li><a href="manager_dashboard.php" class="activ">Dashboard</a></li>
        <li><a href="manager_products.php">Products</a></li>
        <li><a href="manager_inventory.php">Inventory</a></li>
        <li><a href="manager_reports.php">Reports</a></li>
        <li><a href="manager_users.php">View Users</a></li>
        <li><a href="../admin/messages/manager_sms.php">Notifications</a></li>
        <li><a href="/pos/pos.php">POS</a></li>
        <li><a href="/pos/auth/out.php">Logout</a></li>
      </ul>
    </nav>
</header>

    <main class="products">
    <h2>Products</h2>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
        </thead>
<tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td data-label="SKU"><?= htmlspecialchars($p['sku']) ?></td>
      <td data-label="Name"><?= htmlspecialchars($p['name']) ?></td>
      <td data-label="Price"><?= number_format($p['price'], 2) ?></td>
      <td data-label="Stock"><?= intval($p['stock']) ?></td>

      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
    </table>
    </main>

    <script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebarNav = document.getElementById('sidebarNav');

  menuToggle.addEventListener('click', () => {
    sidebarNav.classList.toggle('open');

    menuToggle.textContent = sidebarNav.classList.contains('open') ? '✖' : '☰';
  });

</script>
</body>

</html>