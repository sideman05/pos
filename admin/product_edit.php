<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if ($user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo 'Product not found';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    $up = $pdo->prepare('UPDATE products SET name=:n, price=:p, stock=:s WHERE id=:id');
    $up->execute([':n' => $name, ':p' => $price, ':s' => $stock, ':id' => $id]);

    header('Location: products.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #f5f6fa;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <h1>Edit Product</h1>
        <form method="post">
            <input name="name" value="<?= htmlspecialchars($row['name']); ?>" required placeholder="Product Name" /><br>
            <input name="price" type="number" step="0.01" value="<?= htmlspecialchars($row['price']); ?>" required placeholder="Price" /><br>
            <input name="stock" type="number" value="<?= htmlspecialchars($row['stock']); ?>" required placeholder="Stock" /><br>
            <button type="submit">Save Changes</button>
        </form>
        <a href="products.php">← Back to Products</a>
    </div>
</body>

</html>
