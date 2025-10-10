<?php
require_once __DIR__ . '/../inc/config.php';
try{
  $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $cols = $pdo->query("SHOW COLUMNS FROM sales LIKE 'subtotal'")->fetchAll(PDO::FETCH_ASSOC);
  if(empty($cols)){
    $pdo->exec("ALTER TABLE sales ADD COLUMN subtotal DECIMAL(12,2) NOT NULL DEFAULT 0");
    echo "Added subtotal column\n";
  } else {
    echo "subtotal exists\n";
  }
}catch(Exception $e){ echo 'ERR: '.$e->getMessage(); }
