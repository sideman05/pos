<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
// Allow admin, manager and cashier to view reports
require_role('admin','manager','cashier');
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('export-csv').addEventListener('click', function() {
            window.location = 'export_report.php?format=csv';
        });

        document.getElementById('export-pdf').addEventListener('click', async function() {
            const res = await fetch('export_report.php?format=json');
            if (!res.ok) return alert('Failed to fetch sales for PDF');
            const data = await res.json();

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({unit: 'pt', format: 'a4'});
            const margin = 40;
            const pageWidth = doc.internal.pageSize.getWidth();

            // Header
            doc.setFontSize(18);
            doc.text('Sales Report', pageWidth/2, 50, {align: 'center'});
            doc.setFontSize(10);
            doc.text('Generated: ' + new Date().toLocaleString(), margin, 70);
            doc.text('Total: $' + (parseFloat(<?php echo json_encode($total); ?>) || 0).toFixed(2), pageWidth - margin, 70, {align: 'right'});

            const rows = [];
            data.forEach(s => {
                rows.push([s.id, s.invoice_no || '', s.created_at, (parseFloat(s.total) || 0).toFixed(2)]);
                if (Array.isArray(s.items) && s.items.length) {
                    s.items.forEach(it => {
                        rows.push(['', '', '  ' + it.name + ' x' + it.qty, (parseFloat(it.subtotal) || 0).toFixed(2)]);
                    });
                }
            });

            doc.autoTable({
                head: [['ID','Invoice','Date','Amount ($)']],
                body: rows,
                startY: 90,
                margin: {left: margin, right: margin},
                styles: {fontSize: 9},
                headStyles: {fillColor: [22, 160, 133]},
                theme: 'striped',
                didDrawPage: (dataArg) => {
                    const str = 'Page ' + doc.internal.getNumberOfPages();
                    doc.setFontSize(9);
                    doc.text(str, pageWidth/2, doc.internal.pageSize.getHeight() - 30, {align: 'center'});
                }
            });

            doc.save('sales_report.pdf');
        });
    </script>
</body>

</html>