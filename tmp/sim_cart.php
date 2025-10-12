<?php
require __DIR__ . '/../inc/db.php';

try {
    $items = [ ['product_id' => 1, 'qty' => 1] ];
    $pdo->beginTransaction();


    $total = 0;
    $stmtP = $pdo->prepare('SELECT id, price, stock FROM products WHERE id = :id FOR UPDATE');
    foreach ($items as $it) {
        $stmtP->execute([':id' => $it['product_id']]);
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        if (!$prod) throw new Exception('Product not found');
        $total += floatval($prod['price']) * intval($it['qty']);
    }


    $uid = $pdo->query('SELECT id FROM users LIMIT 1')->fetchColumn();
    if (!$uid) {
        $pdo->prepare("INSERT INTO users (username,password,role,created_at) VALUES ('pos_system','', 'system', NOW())")->execute();
        $uid = $pdo->lastInsertId();
    }

    $colsInfo = $pdo->query('SHOW COLUMNS FROM sales')->fetchAll(PDO::FETCH_ASSOC);
    $salesCols = array_column($colsInfo, 'Field');
    $preferred = ['invoice_no','user_id','subtotal','total_amount','paid_amount','change_amount','created_at'];

    $invoice = 'INV' . time() . rand(100,999);
    $cols = [];
    $placeholders = [];
    $saleData = [];
    foreach ($preferred as $col) {
        if (!in_array($col, $salesCols)) continue;
        $cols[] = $col;
        $placeholders[] = ':' . $col;
        switch ($col) {
            case 'invoice_no': $saleData[':'.$col] = $invoice; break;
            case 'user_id': $saleData[':'.$col] = intval($uid); break;
            case 'subtotal': $saleData[':'.$col] = $total; break;
            case 'total_amount': $saleData[':'.$col] = $total; break;
            case 'paid_amount': $saleData[':'.$col] = $total; break;
            case 'change_amount': $saleData[':'.$col] = 0.0; break;
            case 'created_at': $saleData[':'.$col] = date('Y-m-d H:i:s'); break;
            default: $saleData[':'.$col] = null; break;
        }
    }

    $sql = 'INSERT INTO sales ('.implode(',', $cols).') VALUES ('.implode(',', $placeholders).')';
    echo "SQL: $sql\n";
    echo "PARAMS: ".json_encode($saleData)."\n";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($saleData);
    $id = $pdo->lastInsertId();
    $pdo->commit();
    echo "OK ID:" . $id . "\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "ERR: " . $e->getMessage() . "\n";
    var_export($e->errorInfo ?? null);
}
