<?php
// Kiểm tra chi tiết category
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Kiểm tra tất cả category values
    $categoryQuery = "SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY count DESC";
    $categoryStmt = $pdo->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Tất cả category hiện có:\n";
    foreach ($categories as $cat) {
        $catName = $cat['category'] === '' ? '[EMPTY STRING]' : "'" . $cat['category'] . "'";
        echo "- Category $catName: " . $cat['count'] . " sản phẩm\n";
    }
    
    echo "\n";
    
    // Kiểm tra những sản phẩm có category rỗng
    $emptyQuery = "SELECT id, name, category FROM products WHERE category = '' OR category IS NULL LIMIT 20";
    $emptyStmt = $pdo->query($emptyQuery);
    $emptyProducts = $emptyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Sản phẩm có category rỗng (20 đầu):\n";
    foreach ($emptyProducts as $product) {
        echo "ID: " . $product['id'] . ", Name: " . $product['name'] . ", Category: '" . $product['category'] . "'\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>