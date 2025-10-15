<?php
require_once __DIR__ . '/../inc/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];


    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)');
    $stmt->execute([$username, $hash, $full_name]);
    header('Location: login.php');
    exit;
}
?>
<!-- HTML form below -->
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
            <div style="  margin: 0;
  padding: 0;
  font-family: var(--font-family, 'Helvetica Neue', sans-serif);
  background: linear-gradient(135deg, #4a5568, #2d3748);
  height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
    
    ">
    <form method="post" class="login">

        <h2>Register</h2>
        <input name="username" required placeholder="Username"><br>
        <input name="full_name" placeholder="Full name"><br>
        <input name="password" type="password" required placeholder="Password"><br>
        <button type="submit">Register</button>

    </form>
</div>
</body>

</html>