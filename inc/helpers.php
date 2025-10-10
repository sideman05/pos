<?php
function is_logged_in() {
return isset($_SESSION['user']);
}


function require_login() {
if(!is_logged_in()) {
header('Location: /pos/auth/login.php'); exit;
}
}


function current_user() {
return $_SESSION['user'] ?? null;
}


function has_role($role) {
$u = current_user();
if(!$u) return false;
return $u['role'] === $role || $u['role'] === 'admin';
}