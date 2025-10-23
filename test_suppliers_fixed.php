<?php
// Test suppliers.php sau khi sửa
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    echo "Test suppliers.php sau khi sửa...\n";
    
    // Test query chính
    $whereClause = "WHERE status = 1";
    $suppliersQuery = "SELECT * FROM suppliers $whereClause ORDER BY name ASC LIMIT 5";
    $suppliersStmt = $pdo->prepare($suppliersQuery);
    $suppliersStmt->execute();
    $suppliers = $suppliersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query suppliers thành công, số lượng: " . count($suppliers) . "\n";
    
    // Test query categories
    $categoriesQuery = "SELECT DISTINCT category_id as id, category_id as name FROM suppliers WHERE category_id IS NOT NULL AND status = 1 ORDER BY category_id";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query categories thành công, số lượng: " . count($categories) . "\n";
    
    // Test query stats
    $statsQuery = "SELECT 
        COUNT(*) as total_suppliers,
        COUNT(DISTINCT category_id) as total_categories,
        AVG(DATEDIFF(NOW(), created_at)) as avg_days
        FROM suppliers WHERE status = 1";
    $statsStmt = $pdo->query($statsQuery);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Query stats thành công:\n";
    echo "- Total suppliers: " . $stats['total_suppliers'] . "\n";
    echo "- Total categories: " . $stats['total_categories'] . "\n";
    echo "- Avg days: " . $stats['avg_days'] . "\n";
    
    echo "\nVài suppliers mẫu:\n";
    foreach ($suppliers as $supplier) {
        echo "- " . $supplier['name'] . " (Category ID: " . ($supplier['category_id'] ?? 'NULL') . ")\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>