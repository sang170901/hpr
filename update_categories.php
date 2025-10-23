<?php
// Cập nhật category cho các sản phẩm
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Cập nhật category dựa trên tên sản phẩm
    $updates = [
        // Vật liệu xây dựng
        'vật liệu' => [
            'xi măng', 'thép', 'gạch', 'sơn', 'kính', 'sàn gỗ', 'tấm', 'ống', 'dây điện', 'đá',
            'cát', 'sỏi', 'cốt thép', 'bê tông', 'gỗ', 'phào', 'tường', 'mái', 'cửa', 'nẹp'
        ],
        // Thiết bị xây dựng  
        'thiết bị' => [
            'máy', 'thiết bị', 'dụng cụ', 'mũ bảo hiểm', 'găng tay', 'khoan', 'cưa', 'máy trộn',
            'cần cẩu', 'xe', 'thang', 'giàn giáo', 'bơm', 'máy hàn', 'máy cắt', 'máy đo'
        ],
        // Nội thất
        'vật liệu' => [
            'tủ bếp', 'tủ quần áo', 'bàn ăn', 'sofa', 'giường', 'kệ', 'ghế', 'đèn',
            'rèm', 'thảm', 'tranh', 'gương', 'chậu rửa', 'vòi', 'bồn tắm'
        ],
        // Công nghệ  
        'công nghệ' => [
            'hệ thống', 'điều hòa', 'máy lạnh', 'quạt', 'đèn led', 'camera', 'cảm biến',
            'điều khiển', 'thông minh', 'tự động', 'năng lượng mặt trời', 'pin', 'inverter'
        ],
        // Cảnh quan
        'cảnh quan' => [
            'cây', 'hoa', 'cỏ', 'đất', 'phân bón', 'chậu cây', 'tưới', 'vòi tưới',
            'hồ', 'đài phun nước', 'đèn sân vườn', 'ghế đá', 'hàng rào', 'cổng'
        ]
    ];
    
    $totalUpdated = 0;
    
    foreach ($updates as $category => $keywords) {
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
    
    echo "\nTổng số sản phẩm đã cập nhật: $totalUpdated\n";
    
    // Kiểm tra lại sau khi cập nhật
    echo "\nKiểm tra lại sau khi cập nhật:\n";
    $categoryQuery = "SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY count DESC";
    $categoryStmt = $pdo->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $cat) {
        $catName = $cat['category'] === '' ? '[EMPTY STRING]' : "'" . $cat['category'] . "'";
        echo "- Category $catName: " . $cat['count'] . " sản phẩm\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>