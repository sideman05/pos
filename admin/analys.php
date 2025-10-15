<!doctype html>
<html>

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
    <style>
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
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
                <li><a href="reports.php">Reports</a></li>
                <li><a href="users.php">Manage users</a></li>
                <li><a href="analys.php" class="activ">Analyse</a></li>
                <li><a href="../admin/messages/admin_messages.php">Notifications</a></li>
                <li><a href="/pos/pos.php">POS</a></li>
                <li><a href="/pos/auth/out.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </main>
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebarNav = document.getElementById('sidebarNav');

        menuToggle.addEventListener('click', () => {
            sidebarNav.classList.toggle('open');

            menuToggle.textContent = sidebarNav.classList.contains('open') ? '✖' : '☰';
        });
        $(document).ready(function() {
            const ctx = document.getElementById('revenueChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue ($)',
                        data: [12500, 19000, 18000, 22000, 19500, 24000, 26000, 31500, 28000, 29500, 31200, 37400],
                        backgroundColor: 'rgba(193,154,107,0.2)',
                        borderColor: 'rgba(193,154,107,1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>