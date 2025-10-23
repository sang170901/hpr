<?php
// Chuẩn hóa categories
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Chuẩn hóa các category còn lại thành 'vật liệu'
    $normalizeQuery = "UPDATE products SET category = 'vật liệu' WHERE category IN ('Sàn gỗ', 'Nội thất', 'Gạch ốp lát')";
    $normalizeStmt = $pdo->prepare($normalizeQuery);
    $normalizeStmt->execute();
    $normalized = $normalizeStmt->rowCount();
    
    echo "Chuẩn hóa $normalized sản phẩm thành 'vật liệu'\n";
    
    // Kiểm tra cuối cùng
    $finalQuery = "SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY count DESC";
    $finalStmt = $pdo->query($finalQuery);
    $categories = $finalStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nPhân bố cuối cùng:\n";
    foreach ($categories as $cat) {
        echo "- Category '" . $cat['category'] . "': " . $cat['count'] . " sản phẩm\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>