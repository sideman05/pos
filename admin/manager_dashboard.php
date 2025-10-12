<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_role('manager','admin');
require_once __DIR__ . '/../inc/db.php';

// Inventory summary
$inv = $pdo->query('SELECT COUNT(*) as products, IFNULL(SUM(stock),0) as total_stock FROM products')->fetch(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="/pos/assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Manager Dashboard</h1>
        <nav> <a href="users.php">Manage Users</a> | <a href="export_report.php">Export Sales</a> | <a href="/pos/auth/logout.php">Logout</a> | <a href="../admin/messages/manager_sms.php">Notifications and messages</a></nav>
    </header>
    <main>

        <section style="margin-top:16px">
            <h2>Inventory Summary</h2>
            <div>Products: <?= intval($inv['products']) ?></div>
            <div>Total stock: <?= intval($inv['total_stock']) ?></div>
            <p><a href="inventory.php">View full inventory</a></p>
        </section>

        <section style="margin-top:16px">
            <h2>Quick actions</h2>
            <ul>
                <li><a href="users.php">Manage users</a></li>
                <li><a href="export_report.php?format=csv">Export sales CSV</a></li>
                <li><a href="export_report.php?format=json">Export sales JSON</a></li>
            </ul>
        </section>
    </main>
</body>
</html>
