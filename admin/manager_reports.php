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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Reports for POS System">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4); 
  backdrop-filter: blur(2px); 
  z-index: 5;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}


#overlay.active {
  opacity: 1;
  pointer-events: all;
}

    </style>
</head>

<body>
 <header>
  <div class="menu-toggle" id="menuToggle">☰</div>
<nav id="sidebarNav">
  <h1>POS Dashboard</h1>
      <ul>
        <li><a href="manager_dashboard.php" class="activ">Dashboard</a></li>
        <li><a href="manager_products.php">Products</a></li>
        <li><a href="manager_inventory.php">Inventory</a></li>
        <li><a href="manager_reports.php">Reports</a></li>
        <li><a href="manager_users.php">View Users</a></li>
        <li><a href="../admin/messages/manager_sms.php">Notifications</a></li>
        <li><a href="/pos/pos.php">POS</a></li>
        <li><a href="/pos/auth/out.php">Logout</a></li>
      </ul>
    </nav>
</header>

    <main class="users">
    <h1>Sales Reports</h1>
    <a href="manager_dashboard.php">Back</a>
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
            $total = $pdo->query('SELECT IFNULL(SUM(si.qty * si.price),0) FROM sale_items si')->fetchColumn();
            $recent = $pdo->query('SELECT s.id, s.created_at, IFNULL(SUM(si.qty*si.price),0) as total FROM sales s LEFT JOIN sale_items si ON si.sale_id = s.id GROUP BY s.id ORDER BY s.created_at DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
        }
        echo '<div>Total sales: Tsh. ' . number_format($total, 2) .  '</div>';
        if ($recent) {
            echo '<h3>Recent sales</h3><ul>';
            foreach ($recent as $s) echo '<li>Sale #' . intval($s['id']) . ' ' . htmlspecialchars($s['created_at']) . ' Tsh. ' . number_format($s['total'], 2) . '</li>';
            echo '</ul>';
        }
    } catch (Exception $e) {
        echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
     <script src="../assets/js/app.js"></script>

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
            doc.text('Total: Tsh. ' + (parseFloat(<?php echo json_encode($total); ?>) || 0).toFixed(2), pageWidth - margin, 70, {align: 'right'});

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
                head: [['ID','Invoice','Date','Amount (Tsh. )']],
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

const menuToggle = document.getElementById('menuToggle');
const sidebarNav = document.getElementById('sidebarNav');
const overlay = document.getElementById('overlay');

menuToggle.addEventListener('click', () => {
  sidebarNav.classList.toggle('open');
  const isOpen = sidebarNav.classList.contains('open');
  menuToggle.textContent = isOpen ? '✖' : '☰';
  
  overlay.classList.toggle('active', isOpen);
});
overlay.addEventListener('click', () => {
  sidebarNav.classList.remove('open');
  overlay.classList.remove('active');
  menuToggle.textContent = '☰';
});
    </script>
<div id="overlay"></div>
    </main>
</body>

</html>