<?php
require __DIR__ . '/../inc/db.php';
$cols = array_column($pdo->query('SHOW COLUMNS FROM sales')->fetchAll(PDO::FETCH_ASSOC), 'Field');
if (in_array('total', $cols)) {
	$rows = $pdo->query("SELECT id, created_at, IFNULL(total,0) as total FROM sales ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} else {
	$rows = $pdo->query("SELECT s.id, s.created_at, IFNULL((SELECT SUM(si.qty*si.price) FROM sale_items si WHERE si.sale_id = s.id),0) as total FROM sales s ORDER BY s.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($rows, JSON_PRETTY_PRINT);
