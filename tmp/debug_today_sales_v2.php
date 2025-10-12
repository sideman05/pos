<?php
require __DIR__ . '/../inc/db.php';
echo "--- sales table columns ---\n";
$cols = $pdo->query("SHOW COLUMNS FROM sales")->fetchAll(PDO::FETCH_COLUMN);
foreach($cols as $c) echo " - $c\n";

echo "\n--- sales rows for today (DATE(created_at)=CURDATE()) ---\n";
$stmt = $pdo->query("SELECT id, invoice_no, created_at, COALESCE(total_amount, subtotal, NULL) AS total_col FROM sales WHERE DATE(created_at)=CURDATE() ORDER BY created_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$rows) {
    echo "No sales rows found for today.\n";
} else {
    foreach($rows as $r) {
        echo "sale: id={$r['id']} invoice={$r['invoice_no']} created_at={$r['created_at']} total_col=" . ($r['total_col']===null? 'NULL': $r['total_col']) . "\n";
        $it = $pdo->prepare('SELECT IFNULL(SUM(qty*price),0) as items_total FROM sale_items WHERE sale_id=?');
        $it->execute([$r['id']]);
        $itv = $it->fetchColumn();
        echo "  items_total={$itv}\n";
    }
}

echo "\n--- aggregated totals (robust join) ---\n";
$agg = $pdo->query("SELECT COUNT(DISTINCT s.id) as orders, IFNULL(SUM(COALESCE(s.total_amount, s.subtotal, si.items_total)),0) as revenue
FROM sales s LEFT JOIN (SELECT sale_id, SUM(qty*price) as items_total FROM sale_items GROUP BY sale_id) si ON si.sale_id = s.id
WHERE DATE(s.created_at)=CURDATE()")->fetch(PDO::FETCH_ASSOC);
print_r($agg);
