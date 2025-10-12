<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/timeout.php';
require_login();
$user = current_user();
if ($user['role'] !== 'admin') {
    // Option 1: Redirect to POS page or a "no access" page
    header('Location: login.php');
    exit;
}
// fetch quick stats
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="/pos/assets/css/styles.css">
</head>

<body>
    <header>
        <h1>POS Dashboard</h1>
        <div>Welcome, <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></div>
    </header>
    <main>
        <div class="cards">
            <div class="card">Products<br><strong><?= $total_products ?></strong></div>
            <div class="card">Sales<br><strong><?= $total_sales ?></strong></div>
            <div class="card">Today Revenue<br><strong><?= $today_total ?></strong></div>
            
        </div>
        <nav>
            <a href="products.php">Products</a> | <a href="inventory.php">Inventory</a> | <a href="reports.php">Reports</a> | <a href="/pos/pos.php">POS</a> | <a href="users.php">Manage users</a>  | <a href="../admin/messages/admin_messages.php">Notifications</a> | <a href="/pos/auth/out.php">Logout</a>
        </nav>
    </main>
</body>

</html>