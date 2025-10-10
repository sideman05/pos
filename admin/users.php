<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
require_once __DIR__ . '/../inc/db.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Users</title>
</head>

<body>
    <h1>Users</h1>
    <a href="dashboard.php">Back</a>
    <?php
    try {
        $rows = $pdo->query('SELECT id,username,role,created_at FROM users')->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th></tr>';
            foreach ($rows as $r) {
                echo '<tr><td>' . intval($r['id']) . '</td><td>' . htmlspecialchars($r['username']) . '</td><td>' . htmlspecialchars($r['role']) . '</td><td>' . htmlspecialchars($r['created_at']) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<div>No users found.</div>';
        }
    } catch (Exception $e) {
        echo '<div>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
</body>

</html>