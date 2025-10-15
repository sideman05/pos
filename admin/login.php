<?php
require_once __DIR__ . '/../inc/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        if (isset($user['role']) && $user['role'] === 'cashier') {
            header('Location: /pos/admin/cashier_dashboard.php');
            exit;
        } else if (isset($user['role']) && $user['role'] === 'manager') {
            header('Location: /pos/admin/manager_dashboard.php');
            exit;
        }
        header('Location: /pos/admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-family, 'Helvetica Neue', sans-serif);
            background: linear-gradient(135deg, #4a5568, #2d3748);
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;

        }
        form.login {
    background: white;
    padding: 2rem 2.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    text-align: center;
    width: 100%;
    /* height: 500px; */
    max-width: 350px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

form.login:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}


form.login h2 {
    margin-bottom: 1.5rem;
    color: #2d3748;
    font-size: 1.8rem;
}


form.login input {
    width: 90%;
    padding: 0.8rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

form.login input:focus {
    outline: none;
    border-color: var(--accent-color, #ed64a6);
}


form.login button {
    width: 100%;
    padding: 0.8rem;
    background: var(--accent-color, #ed64a6);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

form.login button:hover {
    background: #d53f8c;
}

p {
    color: #e53e3e;
    text-align: center;
    background: #ffe5e5;
    padding: 0.6rem;
    border-radius: 8px;
    width: 300px;
    margin: 1rem auto;
    font-size: 0.9rem;
}
    </style>
</head>

<body>
    <div>
        <form method="post" class="login">
            <?php if (!empty($error)) echo "<p>$error</p>"; ?>
            <h2>Login</h2>
            <input name="username" required placeholder="Username"><br>
            <input name="password" required type="password" placeholder="Password"><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>