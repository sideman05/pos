<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory</title>
</head>

<body>
    <h1>Inventory</h1>
    <a href="products.php">Back to products</a>
    <?php
    try {
        $rows = $pdo->query('SELECT id,name,stock FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            echo '<ul>';
            foreach ($rows as $r) echo '<li>' . htmlspecialchars($r['name']) . ' — ' . intval($r['stock']) . '</li>';
            echo '</ul>';
        } else echo '<div>No products</div>';
    } catch (Exception $e) {
        echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
</body>

</html>