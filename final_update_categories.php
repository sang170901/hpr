<?php
// Cập nhật category cho những sản phẩm còn lại
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Cập nhật category dựa trên keyword cụ thể hơn
    $specificUpdates = [
        'vật liệu' => [
            'xi măng', 'xi măng', 'thép', 'gạch', 'sơn', 'kính', 'sàn', 'ống', 'dây', 'điện',
            'tấm', 'ván', 'gỗ', 'phào', 'nẹp', 'viên gạch', 'đá', 'cát', 'sỏi', 'bê tông'
        ],
        'thiết bị' => [
            'khoan', 'máy hàn', 'máy cắt', 'cưa máy', 'búa', 'kìm', 'tuốc nơ vít', 'thước',
            'máy đo', 'máy trộn', 'xe', 'bơm', 'quạt hút', 'máy nén'
        ]
    ];
    
    $totalUpdated = 0;
    
    foreach ($specificUpdates as $category => $keywords) {
        foreach ($keywords as $keyword) {
            $updateQuery = "UPDATE products SET category = :category 
                           WHERE (category = '' OR category IS NULL) 
                           AND (LOWER(name) LIKE :keyword)";
            $updateStmt = $pdo->prepare($updateQuery);
            $result = $updateStmt->execute([
                ':category' => $category,
                ':keyword' => '%' . strtolower($keyword) . '%'
            ]);
            
            $updated = $updateStmt->rowCount();
            if ($updated > 0) {
                echo "Cập nhật $updated sản phẩm chứa '$keyword' thành category '$category'\n";
                $totalUpdated += $updated;
            }
        }
    }
    
    // Cập nhật những sản phẩm còn lại thành 'vật liệu' (default)
    $defaultQuery = "UPDATE products SET category = 'vật liệu' WHERE category = '' OR category IS NULL";
    $defaultStmt = $pdo->prepare($defaultQuery);
    $defaultStmt->execute();
    $defaultUpdated = $defaultStmt->rowCount();
    
    if ($defaultUpdated > 0) {
        echo "Cập nhật $defaultUpdated sản phẩm còn lại thành category 'vật liệu' (mặc định)\n";
        $totalUpdated += $defaultUpdated;
    }
    
    echo "\nTổng số sản phẩm đã cập nhật thêm: $totalUpdated\n";
    
    // Kiểm tra lại cuối cùng
    echo "\nKiểm tra cuối cùng:\n";
    $finalQuery = "SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY count DESC";
    $finalStmt = $pdo->query($finalQuery);
    $categories = $finalStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $cat) {
        $catName = $cat['category'] === '' ? '[EMPTY STRING]' : "'" . $cat['category'] . "'";
        echo "- Category $catName: " . $cat['count'] . " sản phẩm\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>