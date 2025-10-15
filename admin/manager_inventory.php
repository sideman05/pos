<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Inventory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Admin Inventory for POS System">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    main {
      padding: 2rem;
      background: #f8fafc;
      min-height: 100vh;
    }

    main h1 {
      font-size: 2rem;
      color: #1e293b;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    main a[href*="products.php"] {
      display: inline-block;
      margin-bottom: 1.5rem;
      background: var(--accent-color);
      color: #fff;
      text-decoration: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }

    main a[href*="products.php"]:hover {
      background: #af1eaaff;
    }


    main ul {
      list-style: none;
      padding: 0;
      margin: 0 auto;
      display: grid;
      gap: 1rem;
      max-width: 800px;
    }

    main ul li {
      background: #ffffff;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1rem;
      color: #334155;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    main ul li:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    main ul li::after {
      content: attr(data-stock);
      background: #e2e8f0;
      color: #0f172a;
      padding: 0.3rem 0.8rem;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    main>div {
      text-align: center;
      background: #fff;
      color: #64748b;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      max-width: 500px;
      margin: 2rem auto;
      font-size: 1rem;
    }

    @media (min-width: 768px) {
      main {
        padding: 3rem 4rem;
      }

      main ul {
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
      }

      main h1 {
        text-align: left;
      }
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

  <main>
    <h1>Inventory</h1>
    <a href="manager_products.php">Back to products</a>
    <?php
    try {
      $rows = $pdo->query('SELECT id,name,stock FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
      if ($rows) {
        echo '<ul>';
        echo '<ul>';
        foreach ($rows as $r) {
          $stockLevel = intval($r['stock']);
          $stockClass = $stockLevel < 5 ? 'low-stock' : ($stockLevel < 20 ? 'medium-stock' : 'high-stock');
          echo '<li class="' . $stockClass . '">' . htmlspecialchars($r['name']) . ' — ' . $stockLevel . '</li>';
        }
        echo '</ul>';
      } else echo '<div>No products</div>';
    } catch (Exception $e) {
      echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    ?>

  </main>

  <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebarNav = document.getElementById('sidebarNav');

    menuToggle.addEventListener('click', () => {
      sidebarNav.classList.toggle('open');

      menuToggle.textContent = sidebarNav.classList.contains('open') ? '✖' : '☰';
    });
  </script>
</body>

</html>