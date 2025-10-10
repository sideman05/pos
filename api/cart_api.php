<?php
require_once __DIR__ . '/../inc/db.php';
header('Content-Type: application/json');

// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get payload
$payload = json_decode(file_get_contents('php://input'), true);
if (empty($payload['items']) || !is_array($payload['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$items = $payload['items'];

try {
    $pdo->beginTransaction();

    // Compute total and validate stock
    $total = 0;
    $stmtP = $pdo->prepare('SELECT id, price, stock FROM products WHERE id = :id FOR UPDATE');
    foreach ($items as $it) {
        $stmtP->execute([':id' => $it['product_id']]);
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        if (!$prod) throw new Exception('Product not found: ' . $it['product_id']);
        if ($prod['stock'] < $it['qty']) throw new Exception('Insufficient stock for product ' . $it['product_id']);
        $total += floatval($prod['price']) * intval($it['qty']);
    }

    // Ensure user exists
    $uid = $pdo->query('SELECT id FROM users LIMIT 1')->fetchColumn();
    if (!$uid) {
        $pdo->prepare("INSERT INTO users (username,password,role,created_at) VALUES ('pos_system','', 'system', NOW())")->execute();
        $uid = $pdo->lastInsertId();
    }

    // Generate a unique invoice_no and insert the sale with prepared params.
    $maxAttempts = 5;
    $saleId = null;
    for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        $invoice = 'INV' . time() . rand(100, 999);
        $saleData = [
            ':invoice_no'   => $invoice,
            ':user_id'      => intval($uid),
            ':subtotal'     => $total,
            ':total_amount' => $total,
            ':paid_amount'  => $total,
            ':change_amount'=> 0.0,
            ':created_at'   => date('Y-m-d H:i:s')
        ];

        $sql = "INSERT INTO sales (invoice_no,user_id,subtotal,total_amount,paid_amount,change_amount,created_at)
                VALUES (:invoice_no,:user_id,:subtotal,:total_amount,:paid_amount,:change_amount,:created_at)";

        try {
            @file_put_contents('/tmp/cart_debug.log', json_encode(['time' => date('c'), 'sql' => $sql, 'params' => $saleData]) . PHP_EOL, FILE_APPEND);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($saleData);
            $saleId = $pdo->lastInsertId();
            break;
        } catch (PDOException $e) {
            // Log exception
            @file_put_contents('/tmp/cart_debug.log', json_encode(['time' => date('c'), 'error' => $e->getMessage(), 'attempt' => $attempt]) . PHP_EOL, FILE_APPEND);
            // If duplicate invoice, try again; otherwise fail
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && $attempt < $maxAttempts - 1) {
                // try another invoice
                usleep(100000); // 100ms
                continue;
            }
            throw $e;
        }
    }

    if (!$saleId) throw new Exception('Failed to create sale record');

    $insItem = $pdo->prepare('INSERT INTO sale_items (sale_id, product_id, qty, price) VALUES (:sale_id, :product_id, :qty, :price)');
    $updStock = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE id = :id');

    foreach ($items as $it) {
        $stmtP->execute([':id' => $it['product_id']]);
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        $insItem->execute([
            ':sale_id' => $saleId,
            ':product_id' => $it['product_id'],
            ':qty' => intval($it['qty']),
            ':price' => floatval($prod['price'])
        ]);
        $updStock->execute([
            ':qty' => intval($it['qty']),
            ':id' => $it['product_id']
        ]);
    }

    $pdo->commit();
    echo json_encode(['ok' => true, 'sale_id' => $saleId, 'total' => $total]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}