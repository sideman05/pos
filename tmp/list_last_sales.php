<?php
require __DIR__ . '/../inc/db.php';
$rows = $pdo->query('SELECT id,invoice_no,created_at, total_amount, subtotal FROM sales ORDER BY created_at DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
