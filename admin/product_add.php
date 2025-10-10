<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/helpers.php';
require_login();
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
    <title>Add Product</title>
</head>

<body>
    <form method="post">
        <input name="sku" placeholder="SKU"><br>
        <input name="name" placeholder="Name" required><br>
        <textarea name="description" placeholder="Description"></textarea><br>
        <input name="price" type="number" step="0.01" required placeholder="Price"><br>
        <input name="stock" type="number" required placeholder="Stock"><br>
        <button type="submit">Add</button>
    </form>
</body>

</html>