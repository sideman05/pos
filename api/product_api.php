<?php
require_once __DIR__ . '/../inc/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query('SELECT id, name, price, stock FROM products');
    $products = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['price'] = floatval($row['price']); // always numeric
        $row['stock'] = intval($row['stock']);   // always numeric
        $products[] = $row;
    }

    echo json_encode($products);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products: '.$e->getMessage()]);
}
