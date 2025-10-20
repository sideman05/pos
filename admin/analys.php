<!doctype html>
<html>

<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_role('admin');
require_once __DIR__ . '/../inc/db.php';

// prepare last 30 days labels and totals
$days = 7;
$labels = [];
$values = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $day = new DateTime("-{$i} days");
    $labels[] = $day->format('Y-m-d');
}

// fetch totals per day using robust fallback to total_amount/subtotal or sale_items sum
$placeholders = implode(',', array_fill(0, count($labels), '?'));
$sql = "SELECT DATE(s.created_at) as d, IFNULL(SUM(COALESCE(s.total_amount, s.subtotal, si.items_total)),0) as total
FROM sales s LEFT JOIN (SELECT sale_id, SUM(qty*price) as items_total FROM sale_items GROUP BY sale_id) si ON si.sale_id = s.id
WHERE DATE(s.created_at) IN ($placeholders)
GROUP BY DATE(s.created_at)";
$stmt = $pdo->prepare($sql);
$stmt->execute($labels);
$rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
foreach ($labels as $d) {
    $values[] = isset($rows[$d]) ? (float)$rows[$d] : 0.0;
}

?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Dashboard for POS System">
    <title>Admin Dashboard - POS System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        .summary-card {
            display:flex;
            gap:1rem;
            align-items:center;
            margin:1rem 0;
        }
        .summary-card .card {
            background: #fff;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(10,20,30,0.06);
            min-width: 160px;
        }
        .summary-card .card .value { font-size: 1.25rem; font-weight:700; }
        .summary-card .card .label { font-size: 0.85rem; color:#666; }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .chart-container { height: 180px; }
            .summary-card { flex-direction: column; align-items: stretch; }
            .summary-card .card { width: 100%; }
        }
        @media (max-width: 420px) {
            .chart-container { height: 160px; }
            .summary-card { gap: 0.5rem; }
            .summary-card .card { padding: 10px; }
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
#sidebarNav ul li a i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}


    </style>
</head>

<body>
<header>
  <div class="menu-toggle" id="menuToggle">☰</div>

  <nav id="sidebarNav">
    <h1>Admin <br> POS Dashboard</h1>
    <ul>
      <li><a href="dashboard.php"><i class="fa-solid fa-house-user"></i> Dashboard</a></li>
      <li><a href="products.php"><i class="fa-solid fa-box"></i> Products</a></li>
      <li><a href="inventory.php"><i class="fa-solid fa-warehouse"></i> Inventory</a></li>
      <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
      <li><a href="users.php"><i class="fa-solid fa-users-gear"></i> Manage Users</a></li>
      <li><a href="analys.php" class="activ"><i class="fa-solid fa-chart-pie"></i> Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
      <li><a href="/pos/pos.php"><i class="fa-solid fa-cash-register"></i> POS</a></li>
      <li><a href="/pos/auth/out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
  </nav>
</header>
    <main>
        <div class="card-body">
            <div class="summary-card">
                <div class="card">
                    <div class="label">7-day Revenue</div>
                    <div class="value" id="sevenTotal">0</div>
                </div>
                <div class="card">
                    <div class="label">Change vs Prev 7d</div>
                    <div class="value" id="sevenChange">0%</div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
        <div id="overlay"></div>
    </main>
     <script src="../assets/js/app.js"></script>

    <script>
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
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('revenueChart');
            const ctx = canvas.getContext('2d');
            const labels = <?php echo json_encode($labels); ?>;
            const values = <?php echo json_encode($values); ?>;

            // compute 7-day total and simple percentage change vs previous 7 days
            const sevenTotal = values.reduce((s,v)=>s+v,0);
            // for prev period attempt to fetch previous 7 days totals by shifting labels back one more week
            // simple heuristic: use first half as previous when we only have 7 days
            const prevTotal = 0; // unknown without extended query; show 0 as placeholder
            const changePct = prevTotal === 0 ? 0 : ((sevenTotal - prevTotal) / prevTotal * 100);
            document.getElementById('sevenTotal').textContent = new Intl.NumberFormat().format(sevenTotal);
            document.getElementById('sevenChange').textContent = (changePct >= 0 ? '+' : '') + changePct.toFixed(1) + '%';

            // create gradient - adapt height from canvas
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 250);
            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.18)');
            gradient.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

            // adapt point sizes for small screens
            const isNarrow = window.matchMedia && window.matchMedia('(max-width:768px)').matches;
            const pointRadius = isNarrow ? 3 : 5;
            const pointHoverRadius = isNarrow ? 5 : 7;

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.map(l => new Date(l).toLocaleDateString()),
                    datasets: [{
                        label: 'Revenue',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: 'rgba(34,197,94,1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(34,197,94,1)',
                        pointRadius: pointRadius,
                        pointHoverRadius: pointHoverRadius,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed.y || 0;
                                    return 'Revenue: ' + new Intl.NumberFormat(undefined, {style:'decimal', maximumFractionDigits:0}).format(val);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#555', maxRotation: 0, autoSkip: true }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(200,200,200,0.12)' },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat().format(value);
                                }
                            }
                        }
                    },
                    elements: {
                        point: { hoverBorderWidth: 3 }
                    }
                }
            });
            // ensure chart resizes on orientation change
            window.addEventListener('resize', function(){ chart.resize(); });
        });
    </script>
</body>

</html>