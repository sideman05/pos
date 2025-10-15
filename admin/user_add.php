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
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Add new user - POS System">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 420px;
            margin: 80px auto;
            width: 100%;
  animation: fadeIn 0.3s ease;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }

        a.back-btn {
            display: inline-block;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        a.back-btn:hover {
            background: #0056b3;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        label {
            font-weight: 600;
            color: #444;
            display: block;
            margin-bottom: 0.3rem;
        }

        input, select {
            width: 90%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.7rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: #d9534f;
            text-align: center;
            background: #fbeaea;
            border: 1px solid #f5c2c0;
            padding: 0.5rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
            @keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
        @media (max-width: 600px) {
            main {
                margin: 40px 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <main>
        <h1>Add User</h1>
        <a href="users.php" class="back-btn">← Back</a>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div>
                <label>Username:</label>
                <input name="username" required>
            </div>

            <div>
                <label>Full name:</label>
                <input name="full_name" required>
            </div>

            <div>
                <label>Password:</label>
                <input name="password" type="password" required>
            </div>

            <div>
                <label>Role:</label>
                <select name="role">
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="cashier" selected>Cashier</option>
                </select>
            </div>

            <div>
                <button type="submit">Create User</button>
            </div>
        </form>
    </main>
</body>
</html>
