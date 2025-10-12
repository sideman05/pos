<?php
if (!function_exists('is_logged_in')) {
	function is_logged_in() {
		return isset($_SESSION['user']);
	}
}

if (!function_exists('require_login')) {
	function require_login() {
		if(!is_logged_in()) {
			header('Location: /pos/auth/login.php'); exit;
		}
	}
}

if (!function_exists('current_user')) {
	function current_user() {
		return $_SESSION['user'] ?? null;
	}
}

if (!function_exists('has_role')) {
	function has_role($role) {
		$u = current_user();
		if(!$u) return false;
		return $u['role'] === $role || $u['role'] === 'admin';
	}
}