<?php
require_once __DIR__ . '/../inc/timeout.php';
require_once __DIR__ . '/../inc/auth.php';
require_role('admin');
require_once __DIR__ . '/../inc/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM  products WHERE id=:id');
    $stmt->execute([':id'=>$id]);
}
header('Location: products.php');
exit;