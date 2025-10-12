<?php
require_once __DIR__ . '/../../inc/timeout.php';
require_once __DIR__ . '/../../inc/auth.php';
require_login();
require_once __DIR__ . '/../../inc/db.php';

$user = current_user();
$status = null;
$error = null;

// Ensure notifications table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `manager_notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(100) NOT NULL,
  `sender_id` INT UNSIGNED NULL,
  `payload` TEXT NOT NULL,
  `meta` JSON NULL,
  `priority` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (`is_read`),
  INDEX (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if ($message === '') {
        $error = 'Message cannot be empty.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO manager_notifications (type, sender_id, payload) VALUES (?, ?, ?)');
        $stmt->execute(['message_admin', $user['id'] ?? null, $message]);
        $status = 'Message sent to admin.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Admin</title>
</head>
<body>
    <h2>Contact Admin</h2>
    <?php if ($status): ?><div style="color:green"><?= htmlspecialchars($status) ?></div><?php endif; ?>
    <?php if ($error): ?><div style="color:red"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" placeholder="Type your message here..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea><br><br>
        <button type="submit">Send</button>
    </form>
    <p><a href="/pos/admin/dashboard.php">Back</a></p>
</body>
</html>