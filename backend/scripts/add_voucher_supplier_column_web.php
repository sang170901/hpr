<?php
// Web helper: add supplier_id column to vouchers table if missing
header('Content-Type: text/plain; charset=utf-8');
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    $cols = $pdo->query("PRAGMA table_info(vouchers)")->fetchAll(PDO::FETCH_ASSOC);
    $names = array_map(function($c){return $c['name'];}, $cols);
    if (in_array('supplier_id', $names)) {
        echo "Column 'supplier_id' already exists on vouchers.\n";
        exit;
    }

    // Add column
    $pdo->exec('ALTER TABLE vouchers ADD COLUMN supplier_id INTEGER');
    echo "Added column 'supplier_id' to vouchers.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
