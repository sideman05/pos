<?php
require_once __DIR__ . '/../../inc/timeout.php';
require_once __DIR__ . '/../../inc/auth.php';
// require_role('admin', 'manager');
$user = current_user();
if ($user['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../../inc/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $nid = intval($_POST['mark_read']);
    $u = $pdo->prepare('UPDATE manager_notifications SET is_read=1, read_at=NOW() WHERE id=?');
    $u->execute([$nid]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$stmt = $pdo->prepare('
    SELECT id, type, payload, created_at 
    FROM manager_notifications 
    WHERE is_read = 0 AND type = ?
    ORDER BY created_at DESC
');
$stmt->execute(['message_admin']);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Notifications for POS System">
    <title>Notifications - Messages</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    main {
      padding: 2rem;
      background: #f8fafc;
      min-height: 2vh;
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
      <li><a href="../../admin/dashboard.php" ><i class="fa-solid fa-house-user"></i> Dashboard</a></li>
      <li><a href="../../admin/products.php"><i class="fa-solid fa-box"></i> Products</a></li>
      <li><a href="../../admin/inventory.php"><i class="fa-solid fa-warehouse"></i> Inventory</a></li>
      <li><a href="../../admin/reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
      <li><a href="../../admin/users.php"><i class="fa-solid fa-users-gear"></i> Manage Users</a></li>
      <li><a href="../../admin/analys.php"><i class="fa-solid fa-chart-pie"></i> Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php" class="activ"><i class="fa-solid fa-bell"></i> Notifications</a></li>
      <li><a href="/pos/pos.php"><i class="fa-solid fa-cash-register"></i> POS</a></li>
      <li><a href="/pos/auth/out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
  </nav>
</header>

<main>
  <section class="notifications-section">
    <h2>Notifications</h2>

    <?php if ($notes): ?>
      <ul class="notifications-list">
        <?php foreach ($notes as $n): ?>
          <li class="notification-card">
            <div class="notification-header">
              <strong><?= htmlspecialchars($n['type']) ?></strong>
              <small><?= htmlspecialchars($n['created_at']) ?></small>
            </div>

            <p><?= nl2br(htmlspecialchars($n['payload'])) ?></p>

            <form method="post">
              <input type="hidden" name="mark_read" value="<?= intval($n['id']) ?>">
              <button type="submit" class="mark-read-btn">Mark as Read</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="no-notifications">No new notifications 🎉</div>
    <?php endif; ?>
  </section>
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
</script>
</body>
</html>

