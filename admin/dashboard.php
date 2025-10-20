<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/timeout.php';
require_login();
$user = current_user();
if ($user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Dashboard for POS System">
    <title>Admin Dashboard - POS System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- <link rel="stylesheet" href="../assets/js/app.js"> -->
     <script src="../assets/js/app.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZmK9OlkOP+XkYDH5Rbf+e3hVQ4llJ6jI1bgUFKyew+OrCXaRkfj" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



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

.cards {
    margin: 2rem;
    display: flex;
    gap: var(--margin);
    flex-wrap: wrap;
}

.card {
  color: #fff;
  padding: 20px;
      border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: var(--padding);
    flex: 1 1 calc(33.333% - var(--margin) * 2);
    text-align: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card i {
  font-size: 32px;
  margin-bottom: 10px;
  display: block;
}

.card p {
  font-size: 16px;
  margin: 8px 0;
  font-weight: 500;
}

.card strong {
  font-size: 22px;
  display: block;
  margin-top: 5px;
}

/* Unique background colors */
.card1 { background: #007bff; }   /* Blue */
.card2 { background: #28a745; }   /* Green */
.card3 { background: #ffc107; color: #333; } /* Yellow with dark text */
.card4 { background: #17a2b8; }   /* Teal */
.card5 { background: #6f42c1; }   /* Purple */

/* Hover effect */
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}
.card {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease forwards;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
.card:nth-child(4) { animation-delay: 0.4s; }
.card:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

    </style>
</head>

<body>
<header>
  <div class="menu-toggle" id="menuToggle">☰</div>

  <nav id="sidebarNav">
    <h1>Admin <br> POS Dashboard</h1>
    <ul>
      <li><a href="dashboard.php" class="activ"><i class="fa-solid fa-house-user"></i> Dashboard</a></li>
      <li><a href="products.php"><i class="fa-solid fa-box"></i> Products</a></li>
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

    <main>
        <div class="welcome">Welcome, <h2 class="username"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h2>
        </div>
<div class="cards">
  <div class="card card1">
    <i class="fa-solid fa-box"></i>
    <p>Products</p>
    <strong><?= $total_products ?></strong>
  </div>

  <div class="card card2">
    <i class="fa-solid fa-receipt"></i>
    <p>Sales</p>
    <strong><?= $total_sales ?></strong>
  </div>

  <div class="card card3">
    <i class="fa-solid fa-sack-dollar"></i>
    <p>Today Revenue</p>
    <strong>Tsh. <?= $today_total ?></strong>
  </div>

  <div class="card card4">
    <i class="fa-solid fa-cubes"></i>
    <p>Products</p>
    <strong><?= intval($inv['products']) ?></strong>
  </div>

  <div class="card card5">
    <i class="fa-solid fa-layer-group"></i>
    <p>Total Stock</p>
    <strong><?= intval($inv['total_stock']) ?></strong>
  </div>
</div>

        <div id="overlay"></div>
    </main>
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