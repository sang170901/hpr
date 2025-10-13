<?php
// Script to fix voucher table structure
require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    // Check if supplier_id column exists in vouchers table
    $columns = $pdo->query("PRAGMA table_info(vouchers)")->fetchAll(PDO::FETCH_ASSOC);
    $hasSupplierColumn = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'supplier_id') {
            $hasSupplierColumn = true;
            break;
        }
    }
    
    if (!$hasSupplierColumn) {
        // Add supplier_id column if it doesn't exist
        $pdo->exec("ALTER TABLE vouchers ADD COLUMN supplier_id INTEGER");
        echo "Added supplier_id column to vouchers table\n";
    } else {
        echo "supplier_id column already exists in vouchers table\n";
    }
    
    echo "Voucher table structure fixed successfully!\n";
    
} catch (Exception $e) {
    echo "Error fixing voucher table: " . $e->getMessage() . "\n";
}
?>