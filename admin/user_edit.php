<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_role('admin');
require_once __DIR__ . '/../inc/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? null;
    $role = $_POST['role'] ?? 'cashier';
    $full_name = $_POST['full_name'] ?? '';
    if ($username) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET username=:u,password=:p, full_name=:f, role=:r WHERE id=:id');
            $stmt->execute([':u' => $username, ':p' => $hash, ':f' => $full_name, ':r' => $role, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=:u, full_name=:f, role=:r WHERE id=:id');
            $stmt->execute([':u' => $username, ':f' => $full_name, ':r' => $role, ':id' => $id]);
        }
        header('Location: users.php');
        exit;
    } else {
        $error = 'Username required.';
    }
}

$user = $pdo->prepare('SELECT id,username,full_name,role FROM users WHERE id=:id');
$user->execute([':id' => $id]);
$user = $user->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('Location: users.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit User</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #f3f5f7;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .card {
      background: #fff;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  animation: fadeIn 0.3s ease;
      width: 100%;
      max-width: 420px;
    }
    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
      font-size: 24px;
    }
    form div {
      margin-bottom: 15px;
    }
    label {
      font-weight: 500;
      color: #555;
      display: block;
      margin-bottom: 5px;
    }
    input, select {
      width: 90%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.3s;
    }
    input:focus, select:focus {
      border-color: #007bff;
      outline: none;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
    a.back {
      display: inline-block;
      margin-bottom: 15px;
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }
    a.back:hover {
      text-decoration: underline;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
      font-size: 14px;
    }
    @media (max-width: 600px) {
      .card {
        padding: 25px;
        border-radius: 10px;
      }
      h1 {
        font-size: 20px;
      }
    }
    @keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
  </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
  <div class="card">
    <a href="users.php" class="back">← Back to Users</a>
    <h1>Edit User</h1>
    <?php if (!empty($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
    <form method="post">
      <div>
        <label>Username</label>
        <input name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
      </div>
      <div>
        <label>Full name</label>
        <input name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
      </div>
      <div>
        <label>New Password (leave blank to keep)</label>
        <input name="password" type="password">
      </div>
      <div>
        <label>Role</label>
        <select name="role">
          <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
          <option value="manager" <?php if ($user['role'] == 'manager') echo 'selected'; ?>>Manager</option>
          <option value="cashier" <?php if ($user['role'] == 'cashier') echo 'selected'; ?>>Cashier</option>
        </select>
      </div>
      <button type="submit">Save Changes</button>
    </form>
  </div>
</body>
</html>
