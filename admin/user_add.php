<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
// only admin can create users
require_role('admin');
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = $_POST['full_name'];
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'cashier';
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username,password,full_name,role,created_at) VALUES (:u,:p,:f,:r,NOW())');
        $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$full_name,':r'=>$role]);
        header('Location: users.php');
        exit;
    } else {
        $error = 'Username and password required.';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add User</title></head>
<body>
    <h1>Add User</h1>
    <a href="users.php">Back</a>
    <?php if (!empty($error)) echo '<div style="color:red">'.htmlspecialchars($error).'</div>'; ?>
    <form method="post">
        <div><label>Username: <input name="username" required></label></div>
        <div><label >Full name: <input name="full_name" required> </label></div>
        <div><label>Password: <input name="password" type="password" required></label></div>
        <div><label>Role: <select name="role"><option value="admin">admin</option><option value="manager">manager</option><option value="cashier" selected>cashier</option></select></label></div>
        <div><button type="submit">Create</button></div>
    </form>
</body>
</html>
