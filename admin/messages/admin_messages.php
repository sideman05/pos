        <?php
        require_once __DIR__ . '/../..//inc/timeout.php';
        require_once __DIR__ . '/../../inc/auth.php';
        require_role('admin','manager');
        require_once __DIR__ . '/../../inc/db.php';

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

        // handle mark-as-read
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mark_read'])) {
            $nid = intval($_POST['mark_read']);
            $u = $pdo->prepare('UPDATE manager_notifications SET is_read=1, read_at=NOW() WHERE id=?');
            $u->execute([$nid]);
            header('Location: admin_messages.php'); exit;
        }

        $notes = $pdo->query('SELECT id,type,payload,created_at FROM manager_notifications WHERE is_read=0 ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <section>
                    <h2>Notifications</h2>
                    <?php if ($notes): ?>
                        <ul>
                        <?php foreach ($notes as $n): ?>
                            <li>
                                <strong><?= htmlspecialchars($n['type']) ?></strong>
                                <div><?= nl2br(htmlspecialchars($n['payload'])) ?></div>
                                <small><?= htmlspecialchars($n['created_at']) ?></small>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="mark_read" value="<?= intval($n['id']) ?>">
                                    <button type="submit">Mark read</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div>No new notifications</div>
                    <?php endif; ?>
                </section>
