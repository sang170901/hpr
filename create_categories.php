<?php
// Tạo bảng categories và map category_id
require_once 'inc/db_frontend.php';

try {
    $pdo = getFrontendPDO();
    
    // Tạo bảng categories nếu chưa có
    $createCategoriesTable = "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($createCategoriesTable);
    echo "Tạo bảng categories thành công!\n";
    
    // Insert các categories
    $categories = [
        1 => 'Vật liệu xây dựng',
        2 => 'Nội thất',
        3 => 'Cảnh quan',
        4 => 'Thiết bị',
        5 => 'Công nghệ'
    ];
    
    foreach ($categories as $id => $name) {
        $insertQuery = "INSERT INTO categories (id, name) VALUES (:id, :name) 
                       ON DUPLICATE KEY UPDATE name = :name";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([':id' => $id, ':name' => $name]);
    }
    
    echo "Insert categories thành công!\n";
    
    // Kiểm tra kết quả
    $checkQuery = "SELECT * FROM categories";
    $checkStmt = $pdo->query($checkQuery);
    $result = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nDanh sách categories:\n";
    foreach ($result as $cat) {
        echo "ID: " . $cat['id'] . " - Name: " . $cat['name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>