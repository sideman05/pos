<?php
require __DIR__ . '/../inc/db.php';
$username = 'faidha';
$new = password_hash('cashier123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
$stmt->execute([$new, $username]);
echo "Updated rows: " . $stmt->rowCount() . "\n";
?>
