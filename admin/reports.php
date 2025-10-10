<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reports</title>
</head>

<body>
    <h1>Sales Reports</h1>
    <a href="dashboard.php">Back</a>
    <div style="margin-top:8px">
        <button id="export-csv">Export CSV</button>
        <button id="export-pdf">Export PDF</button>
    </div>
    <?php
    try {
        // Try using sales.total if it exists
        try {
            $total = $pdo->query('SELECT IFNULL(SUM(total),0) FROM sales')->fetchColumn();
            $recent = $pdo->query('SELECT id,created_at,total FROM sales ORDER BY created_at DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $inner) {
            // Fallback: compute totals from sale_items
            $total = $pdo->query('SELECT IFNULL(SUM(si.qty * si.price),0) FROM sale_items si')->fetchColumn();
            $recent = $pdo->query('SELECT s.id, s.created_at, IFNULL(SUM(si.qty*si.price),0) as total FROM sales s LEFT JOIN sale_items si ON si.sale_id = s.id GROUP BY s.id ORDER BY s.created_at DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
        }
        echo '<div>Total sales: $' . number_format($total, 2) . '</div>';
        if ($recent) {
            echo '<h3>Recent sales</h3><ul>';
            foreach ($recent as $s) echo '<li>Sale #' . intval($s['id']) . ' ' . htmlspecialchars($s['created_at']) . ' $' . number_format($s['total'], 2) . '</li>';
            echo '</ul>';
        }
    } catch (Exception $e) {
        echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    document.getElementById('export-csv').addEventListener('click', function(){
        window.location = 'export_report.php?format=csv';
    });
    document.getElementById('export-pdf').addEventListener('click', function(){
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(12);
        doc.text('Sales Report', 14, 20);
        let y = 30;
        const items = <?php echo json_encode($recent ?? []); ?>;
        items.forEach(it => {
            const line = 'Sale #' + it.id + ' ' + it.created_at + ' $' + (parseFloat(it.total)||0).toFixed(2);
            doc.text(line, 14, y);
            y += 8;
            if(y > 270){ doc.addPage(); y = 20; }
        });
        doc.save('sales_report.pdf');
    });
    </script>
</body>

</html>