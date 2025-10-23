<?php
// Test suppliers.php đơn giản
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    echo "Bắt đầu test suppliers.php...\n";
    
    // Test query cơ bản
    $testQuery = "SELECT id, name, status FROM suppliers WHERE status = 1 LIMIT 5";
    $testStmt = $pdo->query($testQuery);
    $testSuppliers = $testStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query thành công, số suppliers tìm được: " . count($testSuppliers) . "\n";
    
    foreach ($testSuppliers as $supplier) {
        echo "- ID: " . $supplier['id'] . ", Name: " . $supplier['name'] . "\n";
    }
    
    // Test truy cập field 'category' 
    $categoryTestQuery = "SELECT id, name, category FROM suppliers WHERE status = 1 LIMIT 1";
    echo "\nTest truy cập field 'category':\n";
    try {
        $categoryTestStmt = $pdo->query($categoryTestQuery);
        echo "Truy cập field 'category' thành công!\n";
    } catch (Exception $e) {
        echo "LỖISQLI: " . $e->getMessage() . "\n";
        echo "Field 'category' không tồn tại trong bảng suppliers!\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>