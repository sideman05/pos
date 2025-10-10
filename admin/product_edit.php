<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
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
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
</head>

<body>
    <h1>Edit Product</h1>
    <form method="post">
        <input name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required /><br>
        <input name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($row['price']); ?>" required /><br>
        <input name="stock" type="number" value="<?php echo htmlspecialchars($row['stock']); ?>" required /><br>
        <button>Save</button>
    </form>
</body>

</html>