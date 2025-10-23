<?php
// Kiểm tra bảng suppliers
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Kiểm tra tổng số suppliers
    $totalQuery = "SELECT COUNT(*) as total FROM suppliers";
    $totalStmt = $pdo->query($totalQuery);
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Tổng số suppliers trong database: " . $total . "\n";
    
    // Kiểm tra suppliers có status = 1
    $activeQuery = "SELECT COUNT(*) as active FROM suppliers WHERE status = 1";
    $activeStmt = $pdo->query($activeQuery);
    $active = $activeStmt->fetch(PDO::FETCH_ASSOC)['active'];
    echo "Suppliers với status = 1: " . $active . "\n";
    
    // Lấy vài suppliers mẫu
    $sampleQuery = "SELECT id, name, status FROM suppliers LIMIT 10";
    $sampleStmt = $pdo->query($sampleQuery);
    $suppliers = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nVài suppliers mẫu:\n";
    foreach ($suppliers as $supplier) {
        echo "ID: " . $supplier['id'] . 
             ", Name: " . $supplier['name'] . 
             ", Status: " . $supplier['status'] . "\n";
    }
    
    // Kiểm tra cấu trúc bảng suppliers
    $structureQuery = "DESCRIBE suppliers";
    $structureStmt = $pdo->query($structureQuery);
    $fields = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCấu trúc bảng suppliers:\n";
    foreach ($fields as $field) {
        echo "- " . $field['Field'] . " (" . $field['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>