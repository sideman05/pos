<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function require_login(){
  if(empty($_SESSION['user'])){ header('Location: /pos/auth/login.php'); exit; }
}

// Require the current user to have at least one of the given roles
function require_role(...$roles) {
  require_login();
  $u = current_user();
  if (!$u) { header('Location: /pos/auth/login.php'); exit; }
  // admin bypass
  if (isset($u['role']) && $u['role'] === 'admin') return true;
  foreach ($roles as $r) {
    if (isset($u['role']) && $u['role'] === $r) return true;
  }
  http_response_code(403);
  echo 'Forbidden';
  exit;
}

function is_role($role) {
  $u = current_user();
  if (!$u) return false;
  if ($u['role'] === 'admin') return true;
  return isset($u['role']) && $u['role'] === $role;
}
