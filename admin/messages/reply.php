<?php
require_once __DIR__ . '/../../inc/timeout.php';
require_once __DIR__ . '/../../inc/auth.php';
require_role('admin', 'manager');
require_once __DIR__ . '/../../inc/db.php';

$pdo->exec("
CREATE TABLE IF NOT EXISTS `manager_replies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `notification_id` INT UNSIGNED NOT NULL,
  `sender_id` INT UNSIGNED NOT NULL,
  `reply_text` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`notification_id`) REFERENCES `manager_notifications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'])) {
    $nid = intval($_POST['notification_id']);
    $reply = trim($_POST['reply_text']);
    $sender_id = $user['id'];

    if ($reply !== '') {
        $stmt = $pdo->prepare("INSERT INTO manager_replies (notification_id, sender_id, reply_text) VALUES (?, ?, ?)");
        $stmt->execute([$nid, $sender_id, $reply]);
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<form method="post" style="display:inline-block; margin-left:10px;">
    <input type="hidden" name="notification_id" value="<?= intval($n['id']) ?>">
    <textarea name="reply_text" placeholder="Type your reply..." rows="2" cols="40" required></textarea><br>
    <button type="submit">Send Reply</button>
</form>

<?php
$replies = $pdo->prepare("SELECT r.reply_text, r.created_at, u.username 
                          FROM manager_replies r 
                          LEFT JOIN users u ON r.sender_id = u.id 
                          WHERE r.notification_id = ?
                          ORDER BY r.created_at ASC");
$replies->execute([$n['id']]);
$all_replies = $replies->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($all_replies): ?>
    <ul style="margin-top:10px; padding-left:20px; border-left:2px solid #ccc;">
        <?php foreach ($all_replies as $r): ?>
            <li>
                <strong><?= htmlspecialchars($r['username'] ?? 'User') ?>:</strong>
                <?= nl2br(htmlspecialchars($r['reply_text'])) ?>
                <small>(<?= htmlspecialchars($r['created_at']) ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>