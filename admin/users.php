<?php
require_once __DIR__ . '/../inc/auth.php';
// allow admin and manager to view users
require_role('admin','manager');
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
    <div style="margin-top:8px"><a href="user_add.php">Add user</a></div>
    <?php
    try {
        $rows = $pdo->query('SELECT id,username,full_name,role,created_at FROM users')->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr>';
            foreach ($rows as $r) {
                $id = intval($r['id']);
                echo '<tr>';
                echo '<td>' . $id . '</td>';
                echo '<td>' . htmlspecialchars($r['username']) . '</td>';
                echo '<td>' . htmlspecialchars($r['full_name']) . '</td>';
                echo '<td>' . htmlspecialchars($r['role']) . '</td>';
                echo '<td>' . htmlspecialchars($r['created_at']) . '</td>';
                echo '<td><a href="user_edit.php?id=' . $id . '">Edit</a> | <a href="user_delete.php?id=' . $id . '" onclick="return confirm(\'Delete user?\')">Delete</a></td>';
                echo '</tr>';
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