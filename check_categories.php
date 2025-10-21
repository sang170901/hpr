<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== KIỂM TRA BẢNG SUPPLIER_CATEGORIES ===\n";
try {
    $result = $pdo->query('DESCRIBE supplier_categories');
    echo "Bảng supplier_categories tồn tại:\n";
    while ($row = $result->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\nDữ liệu trong supplier_categories:\n";
    $cats = $pdo->query('SELECT * FROM supplier_categories')->fetchAll();
    foreach ($cats as $cat) {
        echo "ID: " . $cat['id'] . " - Name: " . $cat['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Bảng supplier_categories không tồn tại: " . $e->getMessage() . "\n";
}

echo "\n=== KIỂM TRA BẢNG PRODUCT_CATEGORIES ===\n";
try {
    $result = $pdo->query('DESCRIBE product_categories');
    echo "Bảng product_categories tồn tại:\n";
    while ($row = $result->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\nDữ liệu trong product_categories:\n";
    $cats = $pdo->query('SELECT * FROM product_categories')->fetchAll();
    foreach ($cats as $cat) {
        echo "ID: " . $cat['id'] . " - Name: " . $cat['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Bảng product_categories không tồn tại: " . $e->getMessage() . "\n";
}
?>