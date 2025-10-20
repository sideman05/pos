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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      <li><a href="users.php" class="activ"><i class="fa-solid fa-users-gear"></i> Manage Users</a></li>
      <li><a href="analys.php"><i class="fa-solid fa-chart-pie"></i> Analyse</a></li>
      <li><a href="../admin/messages/admin_messages.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
      <li><a href="/pos/pos.php"><i class="fa-solid fa-cash-register"></i> POS</a></li>
      <li><a href="/pos/auth/out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
  </nav>
</header>
  <main class="users">
    <h1>Users</h1>
    <div style="margin-top:8px">
      <a href="user_add.php">Add user</a></div>
    <?php
    try {
      $rows = $pdo->query('SELECT id,username,full_name,role,created_at FROM users')->fetchAll(PDO::FETCH_ASSOC);
      if ($rows) {
        echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Username</th> <th> Full name</th> <th>Role</th><th>Created</th><th>Actions</th></tr>';
foreach ($rows as $r) {
  $id = intval($r['id']);
  echo "<tr>
          <td>{$id}</td>
          <td>" . htmlspecialchars($r['username']) . "</td>
          <td>" . htmlspecialchars($r['full_name']) . "</td>
          <td>" . htmlspecialchars($r['role']) . "</td>
          <td>" . htmlspecialchars($r['created_at']) . "</td>
          <td>
            <a href='user_edit.php?id={$id}'>Edit</a> | 
            <button class='deleteBtn' data-id='{$id}'>Delete</button>
          </td>
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
    <div id="overlay"></div>
  </main>
</body>
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