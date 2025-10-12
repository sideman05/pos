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
            $stmt = $pdo->prepare('UPDATE users SET username=:u,password=:p, full_name = :f, role=:r WHERE id=:id');
            $stmt->execute([':u' => $username, ':p' => $hash,':f'=>$full_name,  ':r' => $role, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=:u,role=:r WHERE id=:id');
            $stmt->execute([':u' => $username, ':r' => $role, ':id' => $id]);
        }
        header('Location: users.php');
        exit;
    } else {
        $error = 'Username required.';
    }
}

// Load user
$user = $pdo->prepare('SELECT id,username,full_name,role FROM users WHERE id=:id');
$user->execute([':id' => $id]);
$user = $user->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('Location: users.php');
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit User</title>
</head>

<body>
    <h1>Edit User</h1>
    <a href="users.php">Back</a>
    <?php if (!empty($error)) echo '<div style="color:red">' . htmlspecialchars($error) . '</div>'; ?>
    <form method="post">
        <div><label>Username: <input name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></label></div>
        <div><label>Full name: <input name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required></label></div>
        <div><label>New Password (leave blank to keep): <input name="password" type="password"></label></div>
        <div><label>Role: <select name="role">
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>admin</option>
                    <option value="manager" <?php if ($user['role'] == 'manager') echo 'selected'; ?>>manager</option>
                    <option value="cashier" <?php if ($user['role'] == 'cashier') echo 'selected'; ?>>cashier</option>
                </select></label></div>
        <div><button type="submit">Save</button></div>
    </form>
</body>

</html>