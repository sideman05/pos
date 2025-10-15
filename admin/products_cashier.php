<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/helpers.php';
require_login();
$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Products</title>
    <link rel="stylesheet" href="/pos/assets/css/styles.css">
</head>

<body>
    <h2>Products</h2>
    <a href="cashier_dashboard.php">Back Home</a>
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
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format($p['price'], 2) ?></td>
                    <td><?= intval($p['stock']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>