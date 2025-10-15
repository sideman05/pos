<?php

// if (!defined('SESSION_TIMEOUT')) {
//     define('SESSION_TIMEOUT', 100 * 30); 
// }

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// $timeout_duration = SESSION_TIMEOUT;

// if (!empty($_SESSION['LAST_ACTIVITY'])) {
//     $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
//     if ($elapsed_time > $timeout_duration) {
//         $_SESSION = [];
//         if (ini_get('session.use_cookies')) {
//             $params = session_get_cookie_params();
//             setcookie(session_name(), '', time() - 42000,
//                 $params['path'], $params['domain'], $params['secure'], $params['httponly']
//             );
//         }
//         session_destroy();

//         $loginUrl = 'login.php';
//         if (defined('BASE_URL')) {
//             $loginUrl = rtrim(BASE_URL, '/') . '/auth/login.php';
//         }
//         header('Location: ' . $loginUrl . '?session_expired=1');
//         exit;
//     }
// }

// $_SESSION['LAST_ACTIVITY'] = time();
?>
