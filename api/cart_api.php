<?php
require_once __DIR__ . '/../inc/db.php';
header('Content-Type: application/json');

// Request start log for debugging (use error_log)
error_log('CART_DEBUG REQUEST_START: ' . json_encode(['time' => date('c'), 'event' => 'request_start', 'uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD'], 'payload' => file_get_contents('php://input')]));
// Log the CREATE TABLE seen by this request (helps detect different DB/schema)
try {
    $ct = $pdo->query('SHOW CREATE TABLE sales')->fetch(PDO::FETCH_ASSOC);
    if (!empty($ct['Create Table'])) {
        error_log('CART_DEBUG CREATE_TABLE: ' . $ct['Create Table']);
    }
} catch (Exception $e) {
    error_log('CART_DEBUG SHOW_CREATE_ERROR: ' . $e->getMessage());
}

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

    $colsInfo = $pdo->query('SHOW COLUMNS FROM sales')->fetchAll(PDO::FETCH_ASSOC);
    $salesCols = array_column($colsInfo, 'Field');

    $preferred = ['invoice_no', 'user_id', 'subtotal', 'total_amount', 'paid_amount', 'change_amount', 'created_at'];
    $maxAttempts = 5;
    $saleId = null;
    for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        $invoice = 'INV' . time() . rand(100, 999);

        // Build columns and params according to what exists in the table
        $cols = [];
        $placeholders = [];
        $saleData = [];
        foreach ($preferred as $col) {
            if (!in_array($col, $salesCols)) continue;
            $cols[] = $col;
            $placeholders[] = ':' . $col;
            switch ($col) {
                case 'invoice_no':
                    $saleData[':' . $col] = $invoice;
                    break;
                case 'user_id':
                    $saleData[':' . $col] = intval($uid);
                    break;
                case 'subtotal':
                    $saleData[':' . $col] = $total;
                    break;
                case 'total_amount':
                    $saleData[':' . $col] = $total;
                    break;
                case 'paid_amount':
                    $saleData[':' . $col] = $total;
                    break;
                case 'change_amount':
                    $saleData[':' . $col] = 0.0;
                    break;
                case 'created_at':
                    $saleData[':' . $col] = date('Y-m-d H:i:s');
                    break;
                default:
                    $saleData[':' . $col] = null;
                    break;
            }
        }

        $sql = 'INSERT INTO sales (' . implode(',', $cols) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            error_log('CART_DEBUG SQL: ' . json_encode(['time' => date('c'), 'attempt' => $attempt, 'sql' => $sql, 'params' => $saleData]));
            $stmt = $pdo->prepare($sql);
            $stmt->execute($saleData);
            $saleId = $pdo->lastInsertId();
            break;
        } catch (PDOException $e) {
            $err = ['time' => date('c'), 'attempt' => $attempt, 'message' => $e->getMessage(), 'errorInfo' => $e->errorInfo ?? null, 'sql' => $sql, 'params' => $saleData];
            error_log('CART_DEBUG ERROR: ' . json_encode($err));
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && $attempt < $maxAttempts - 1) {
                usleep(100000);
                continue;
            }
            throw $e;
        }
    }

    if (!$saleId) throw new Exception('Failed to create sale record');

    // Prepare sale_items insert dynamically to include subtotal if column exists
    $siColsInfo = $pdo->query('SHOW COLUMNS FROM sale_items')->fetchAll(PDO::FETCH_ASSOC);
    $siCols = array_column($siColsInfo, 'Field');
    if (in_array('subtotal', $siCols)) {
        $insItem = $pdo->prepare('INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal) VALUES (:sale_id, :product_id, :qty, :price, :subtotal)');
        $insIncludesSubtotal = true;
    } else {
        $insItem = $pdo->prepare('INSERT INTO sale_items (sale_id, product_id, qty, price) VALUES (:sale_id, :product_id, :qty, :price)');
        $insIncludesSubtotal = false;
    }
    $updStock = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE id = :id');

    foreach ($items as $it) {
        $stmtP->execute([':id' => $it['product_id']]);
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        $itemQty = intval($it['qty']);
        $itemPrice = floatval($prod['price']);
        $itemSubtotal = $itemPrice * $itemQty;
        if ($insIncludesSubtotal) {
            $insItem->execute([
                ':sale_id' => $saleId,
                ':product_id' => $it['product_id'],
                ':qty' => $itemQty,
                ':price' => $itemPrice,
                ':subtotal' => $itemSubtotal
            ]);
        } else {
            $insItem->execute([
                ':sale_id' => $saleId,
                ':product_id' => $it['product_id'],
                ':qty' => $itemQty,
                ':price' => $itemPrice
            ]);
        }
        $updStock->execute([
            ':qty' => intval($it['qty']),
            ':id' => $it['product_id']
        ]);
    }

    $pdo->commit();
    echo json_encode(['ok' => true, 'sale_id' => $saleId, 'total' => $total]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    // Detailed debug to help find why subtotal is missing
    $err = [
        'time' => date('c'),
        'message' => $e->getMessage(),
        'class' => get_class($e),
        'trace' => $e->getTraceAsString(),
        'payload' => @json_decode(file_get_contents('php://input'), true)
    ];
    if ($e instanceof PDOException) {
        $err['errorInfo'] = $e->errorInfo ?? null;
    }
    error_log('CART_DEBUG OUTER_ERROR: ' . json_encode($err));

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
