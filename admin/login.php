<?php
require_once __DIR__ . '/../inc/db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
$username = $_POST['username'];
$password = $_POST['password'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
    if($user && password_verify($password, $user['password'])){
        unset($user['password']);
        $_SESSION['user'] = $user;
        // Redirect cashiers to the cashier-specific dashboard
        if (isset($user['role']) && $user['role'] === 'cashier') {
            header('Location: /pos/admin/cashier_dashboard.php'); exit;
        }
        header('Location: /pos/admin/dashboard.php'); exit;
} else {
$error = 'Invalid credentials';
}
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title></head><body>
<?php if(!empty($error)) echo "<p>$error</p>"; ?>
<form method="post">
    <h2>Login</h2>
<input name="username" required placeholder="Username"><br>
<input name="password" required type="password" placeholder="Password"><br>
<button type="submit">Login</button>
<p> Don't have an account?  <a href="register.php">register</a></p>
</form>
</body></html>