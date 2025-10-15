<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_role('manager', 'admin');
require_once __DIR__ . '/../inc/db.php';

$inv = $pdo->query('SELECT COUNT(*) as products, IFNULL(SUM(stock),0) as total_stock FROM products')->fetch(PDO::FETCH_ASSOC);
$total_products = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$total_sales = $pdo->query('SELECT COUNT(*) FROM sales')->fetchColumn();
$today_sales = $pdo->prepare('SELECT SUM(total_amount) FROM sales WHERE DATE(created_at)=CURDATE()');
$today_sales->execute();
$today_total =  $today_sales->fetchColumn() ?: 0;
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manager Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Dashboard for POS System">
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

    <main>

        <section style="margin-top:16px">
            <h2 style="font-size: 2rem;"> Summary</h2>
            <div class="cards">
                <div class="card card1">Products<br><strong><?= $total_products ?></strong></div>
                <div class="card card2">Sales<br><strong><?= $total_sales ?></strong></div>
                <div class="card card3">Today Revenue<br><strong> Tsh. <?= $today_total ?></strong></div>
                <div class="card">Products <br> <strong><?= intval($inv['products']) ?></strong></div>
                <div class="card">Total stock <br> <strong> <?= intval($inv['total_stock']) ?></strong></div>
        </section>
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
    </script>
</body>

</html>