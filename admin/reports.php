<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if ($user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reports - POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Reports for POS System">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .back-btn {
            display: inline-block;
            background: #16a085;
            color: #fff;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #13856b;
        }

        .report-actions {
            margin: 1.2rem 0;
        }

        .report-actions button {
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            margin-right: 10px;
            transition: background 0.3s ease;
        }

        .report-actions button:hover {
            background: #16a085;
        }
        .recent-sales li:last-child {
            border-bottom: none;
        }
.total-sales {
    background-color: #eafaf1;
    border: 1px solid #16a085;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 16px;
    margin: 15px 0;
    color: #145a32;
    font-weight: 500;
}

.recent-sales {
    list-style: none;
    padding: 0;
    margin-top: 10px;
}

.recent-sales li {
    background: #f8f9fa;
    border-left: 4px solid #16a085;
    margin-bottom: 8px;
    padding: 10px 12px;
    border-radius: 6px;
    font-size: 15px;
}

.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
}

.pagination a {
    padding: 8px 14px;
    border: 1px solid #16a085;
    border-radius: 5px;
    text-decoration: none;
    color: #16a085;
    transition: 0.3s;
    font-size: 14px;
}

.pagination a:hover {
    background-color: #16a085;
    color: #fff;
}

.pagination a.active {
    background-color: #16a085;
    color: #fff;
    pointer-events: none;
    font-weight: bold;
}
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
            <h1>Admin <br> POS Dashboard</h1>
            <ul>
                <li><a href="dashboard.php" >Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="reports.php"class="activ">Reports</a></li>
                <li><a href="users.php">Manage users</a></li>
                <li><a href="analys.php">Analyse</a></li>
                <li><a href="../admin/messages/admin_messages.php">Notifications</a></li>
                <li><a href="/pos/pos.php">POS</a></li>
                <li><a href="/pos/auth/out.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Sales Reports</h1>
        <a href="dashboard.php" class="back-btn">← Back</a>

        <div class="report-actions">
            <button id="export-csv">Export CSV</button>
            <button id="export-pdf">Export PDF</button>
        </div>

<?php
try {
    // Pagination setup
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $limit;

    // ✅ Count total sales
    $total_records = (int)$pdo->query('SELECT COUNT(*) FROM sales')->fetchColumn();
    $total_pages = max(1, ceil($total_records / $limit));

    // ✅ Try to use total from sales table if exists, else compute from sale_items
    $columns = $pdo->query("SHOW COLUMNS FROM sales")->fetchAll(PDO::FETCH_COLUMN);
    $has_total = in_array('total', $columns);

    if ($has_total) {
        // If sales table has a total column
        $total = $pdo->query('SELECT IFNULL(SUM(total),0) FROM sales')->fetchColumn();
        $stmt = $pdo->prepare('SELECT id, created_at, total FROM sales ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
    } else {
        // If not, calculate total from sale_items
        $total = $pdo->query('SELECT IFNULL(SUM(qty*price),0) FROM sale_items')->fetchColumn();
        $stmt = $pdo->prepare('
            SELECT s.id, s.created_at, IFNULL(SUM(si.qty*si.price),0) AS total
            FROM sales s
            LEFT JOIN sale_items si ON si.sale_id = s.id
            GROUP BY s.id
            ORDER BY s.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="total-sales">💰 Total Sales: <strong>Tsh. ' . number_format($total, 2) . '</strong></div>';

    if ($recent) {
        echo '<h3>Recent Sales</h3>';
        echo '<ul class="recent-sales">';
        foreach ($recent as $s) {
            echo '<li><strong>Sale #' . intval($s['id']) . '</strong> — ' .
                htmlspecialchars($s['created_at']) .
                ' — Tsh. ' . number_format($s['total'], 2) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No sales found.</p>';
    }

    // ✅ Pagination links
    if ($total_pages > 1) {
        echo '<div class="pagination">';
        if ($page > 1) echo '<a href="?page=' . ($page - 1) . '">« Prev</a>';
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a class="' . $active . '" href="?page=' . $i . '">' . $i . '</a>';
        }
        if ($page < $total_pages) echo '<a href="?page=' . ($page + 1) . '">Next »</a>';
        echo '</div>';
    }

} catch (Exception $e) {
    echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

<div id="overlay"></div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
     <script src="../assets/js/app.js"></script>


    <script>
        document.getElementById('export-csv').addEventListener('click', () => {
            window.location = 'export_report.php?format=csv';
        });

        document.getElementById('export-pdf').addEventListener('click', async () => {
            const res = await fetch('export_report.php?format=json');
            if (!res.ok) return alert('Failed to fetch sales for PDF');
            const data = await res.json();

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                unit: 'pt',
                format: 'a4'
            });
            const margin = 40;
            const pageWidth = doc.internal.pageSize.getWidth();

            doc.setFontSize(18);
            doc.text('Sales Report', pageWidth / 2, 50, {
                align: 'center'
            });
            doc.setFontSize(10);
            doc.text('Generated: ' + new Date().toLocaleString(), margin, 70);
            doc.text('Total: Tsh. ' + (parseFloat(<?php echo json_encode($total); ?>) || 0).toFixed(2), pageWidth - margin, 70, {
                align: 'right'
            });

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
                head: [
                    ['ID', 'Invoice', 'Date', 'Amount (Tsh.)']
                ],
                body: rows,
                startY: 90,
                margin: {
                    left: margin,
                    right: margin
                },
                styles: {
                    fontSize: 9
                },
                headStyles: {
                    fillColor: [22, 160, 133]
                },
                theme: 'striped',
                didDrawPage: (dataArg) => {
                    const str = 'Page ' + doc.internal.getNumberOfPages();
                    doc.setFontSize(9);
                    doc.text(str, pageWidth / 2, doc.internal.pageSize.getHeight() - 30, {
                        align: 'center'
                    });
                }
            });

            doc.save('sales_report.pdf');
        });

        // Sidebar toggle
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
</body>

</html>