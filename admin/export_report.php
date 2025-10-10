<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
$format = strtolower($_GET['format'] ?? 'csv');
try {
    $rows = $pdo->query("SELECT s.id, s.created_at, IFNULL(s.total, (SELECT SUM(si.qty*si.price) FROM sale_items si WHERE si.sale_id = s.id)) as total FROM sales s ORDER BY s.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'created_at', 'total']);
        foreach ($rows as $r) fputcsv($out, [$r['id'], $r['created_at'], $r['total']]);
        fclose($out);
        exit;
    }
    // other formats could be added
    header('Content-Type: application/json');
    echo json_encode($rows);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
