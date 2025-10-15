<?php
require_once __DIR__ . '/../inc/auth.php';
// allow admin and manager to view users
require_role('admin', 'manager');
require_once __DIR__ . '/../inc/db.php';
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Admin Users for POS System">
  <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <h1>Users</h1>
    <div style="margin-top:8px">
    <?php
    try {
      $rows = $pdo->query('SELECT id,username,full_name,role,created_at FROM users')->fetchAll(PDO::FETCH_ASSOC);
      if ($rows) {
        echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Username</th> <th> Full name</th> <th>Role</th><th>Created</th></tr>';
foreach ($rows as $r) {
  $id = intval($r['id']);
  echo "<tr>
          <td>{$id}</td>
          <td>" . htmlspecialchars($r['username']) . "</td>
          <td>" . htmlspecialchars($r['full_name']) . "</td>
          <td>" . htmlspecialchars($r['role']) . "</td>
          <td>" . htmlspecialchars($r['created_at']) . "</td>
        </tr>";
}

        echo '</table>';
      } else {
        echo '<div>No users found.</div>';
      }
    } catch (Exception $e) {
      echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
  </main>
</body>
<script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebarNav = document.getElementById('sidebarNav');

  menuToggle.addEventListener('click', () => {
    sidebarNav.classList.toggle('open');

    menuToggle.textContent = sidebarNav.classList.contains('open') ? '✖' : '☰';
  });


document.querySelectorAll('.deleteBtn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const userId = btn.dataset.id;

    const result = await Swal.fire({
      title: 'Are you sure?',
      text: "This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
      const response = await fetch(`user_delete.php?id=${userId}`);
      if (response.ok) {
        Swal.fire({
          title: 'Deleted!',
          text: 'User has been removed.',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
        setTimeout(() => location.reload(), 1500);
      } else {
        Swal.fire('Error', 'Failed to delete User.', 'error');
      }
    }
  });
});

</script>

</html>