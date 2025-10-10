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
<input name="username" required placeholder="Username"><br>
<input name="password" required type="password" placeholder="Password"><br>
<button type="submit">Login</button>
</form>
</body></html>