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

    </style>
</head>
<body>
 <header>
  <div class="menu-toggle" id="menuToggle">☰</div>

  <nav id="sidebarNav">
<h1>Admin <br> POS Dashboard</h1>
    <ul>
      <li><a href="../../admin/dashboard.php" >Dashboard</a></li>
      <li><a href="../../admin/products.php">Products</a></li>
      <li><a href="../../admin/inventory.php">Inventory</a></li>
      <li><a href="../../admin/reports.php">Reports</a></li>
      <li><a href="../../admin/users.php">Manage users</a></li>
                <li><a href="../../admin/analys.php">Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php" class="activ">Notifications</a></li>
      <li><a href="/pos/pos.php">POS</a></li>
      <li><a href="/pos/auth/out.php">Logout</a></li>
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

