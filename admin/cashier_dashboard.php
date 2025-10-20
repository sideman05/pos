<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
// Allow cashier and admin
require_role('cashier','admin');
require_once __DIR__ . '/../inc/db.php';

try {
    $today_stmt = $pdo->prepare("SELECT COUNT(*) as orders, IFNULL(SUM(COALESCE(s.total_amount, s.subtotal, si.items_total)),0) as revenue
        FROM sales s LEFT JOIN (SELECT sale_id, SUM(qty*price) as items_total FROM sale_items GROUP BY sale_id) si ON si.sale_id = s.id
        WHERE DATE(s.created_at)=CURDATE() GROUP BY DATE(s.created_at)");
    $today_stmt->execute();
    $today = $today_stmt->fetch(PDO::FETCH_ASSOC) ?: ['orders'=>0,'revenue'=>0.00];
} catch (Exception $e) {
    $today = ['orders'=>0,'revenue'=>0.00];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
        <header>
        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="sidebarNav">
            <h1>Admin <br> POS Dashboard</h1>
            <ul>
                <li><a href="cashier_dashboard.php" >Dashboard</a></li>
                <li><a href="../admin/contacts/contact_admin.php">contact admin</a> </li>
                <li><a href="../admin/contacts/contact_manager.php">Contact Manager</a></li>
                <li><a href="/pos/pos.php">POS</a></li>
                <li><a href="/pos/auth/out.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="cards">
            <div class="card">Today's Orders<br><strong><?= intval($today['orders']) ?></strong></div>
            <div class="card">Today's Revenue<br><strong>Tsh. <?= number_format((float)$today['revenue'],2) ?></strong></div>
        </section>
    </main>
</body>
</html>
