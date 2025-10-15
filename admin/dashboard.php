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
    <style>

    </style>
</head>

<body>
    <header>
        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="sidebarNav">
            <h1>Admin <br> POS Dashboard</h1>
            <ul>
                <li><a href="dashboard.php" class="activ">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
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

    <main>
        <div class="welcome">Welcome, <h2 class="username"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h2>
        </div>
        <div class="cards">
            <div class="card card1">Products<br><strong><?= $total_products ?></strong></div>
            <div class="card card2">Sales<br><strong><?= $total_sales ?></strong></div>
            <div class="card card3">Today Revenue<br><strong> Tsh. <?= $today_total ?></strong></div>
            <div class="card">Products <br> <strong><?= intval($inv['products']) ?></strong></div>
            <div class="card">Total stock <br> <strong> <?= intval($inv['total_stock']) ?></strong></div>

        </div>
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