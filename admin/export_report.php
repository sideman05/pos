<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
$format = strtolower($_GET['format'] ?? 'csv');
try {
    // Detect whether `total` column exists in `sales`
    $cols = array_column($pdo->query('SHOW COLUMNS FROM sales')->fetchAll(PDO::FETCH_ASSOC), 'Field');
    if (in_array('total', $cols)) {
        $baseRows = $pdo->query("SELECT id, invoice_no, created_at, IFNULL(total,0) as total FROM sales ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $baseRows = $pdo->query("SELECT s.id, s.invoice_no, s.created_at, IFNULL((SELECT SUM(si.qty*si.price) FROM sale_items si WHERE si.sale_id = s.id),0) as total FROM sales s ORDER BY s.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Attach items to each sale for JSON format
    $rows = [];
    foreach ($baseRows as $r) {
        $items = $pdo->prepare('SELECT si.product_id, p.name, si.qty, si.price, IFNULL(si.subtotal, si.qty*si.price) as subtotal FROM sale_items si LEFT JOIN products p ON p.id = si.product_id WHERE si.sale_id = :sale_id');
        $items->execute([':sale_id' => $r['id']]);
        $r['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
        $rows[] = $r;
    }
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'created_at', 'total']);
        foreach ($rows as $r) fputcsv($out, [$r['id'], $r['created_at'], $r['total']]);
        fclose($out);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode($rows);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
