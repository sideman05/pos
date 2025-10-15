<?php
require_once __DIR__ . '/../inc/auth.php';
require_role('admin');
require_once __DIR__ . '/../inc/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    http_response_code(200);
} else {
    http_response_code(400);
}
