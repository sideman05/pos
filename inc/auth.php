<?php
require_once __DIR__ . '/db.php';
function require_login(){
  if(empty($_SESSION['user'])){ header('Location: /pos/auth/login.php'); exit; }
}
