<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== MIGRATE DỮ LIỆU HIỆN CÓ ===\n\n";

// 1. Migrate products category text -> category_id
echo "1. Migrate products category text -> category_id\n";
$stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
$existingCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($existingCategories as $catName) {
    // Tìm xem có danh mục nào phù hợp không
    $matchingCat = null;
    $catName = trim($catName);
    
    if (stripos($catName, 'sàn') !== false || stripos($catName, 'gỗ') !== false) {
        $matchingCat = 7; // Sàn gỗ (sub of Vật liệu)
    } elseif (stripos($catName, 'gạch') !== false) {
        $matchingCat = 6; // Gạch (sub of Vật liệu)
    } elseif (stripos($catName, 'nội thất') !== false) {
        $matchingCat = 11; // Nội thất (sub of Thiết bị)
    } else {
        $matchingCat = 1; // Vật liệu (main category)
    }
    
    if ($matchingCat) {
        $updateStmt = $pdo->prepare("UPDATE products SET category_id = ? WHERE category = ?");
        $updateStmt->execute([$matchingCat, $catName]);
        $affected = $updateStmt->rowCount();
        echo "  - Chuyển '$catName' -> category_id $matchingCat ($affected sản phẩm)\n";
    }
}

// 2. Migrate suppliers category_id based on existing data
echo "\n2. Migrate suppliers category_id dựa trên dữ liệu hiện có\n";
$suppliers = $pdo->query("SELECT id, name, services, specialties FROM suppliers")->fetchAll();

foreach ($suppliers as $supplier) {
    $name = strtolower($supplier['name']);
    $services = strtolower($supplier['services'] ?? '');
    $specialties = strtolower($supplier['specialties'] ?? '');
    
    $text = $name . ' ' . $services . ' ' . $specialties;
    
    $categoryId = 1; // Default: Vật liệu xây dựng
    
    if (stripos($text, 'nội thất') !== false || stripos($text, 'tủ bếp') !== false) {
        $categoryId = 2; // Nội thất
    } elseif (stripos($text, 'cảnh quan') !== false || stripos($text, 'sân vườn') !== false) {
        $categoryId = 3; // Cảnh quan
    } elseif (stripos($text, 'điện') !== false || stripos($text, 'nước') !== false) {
        $categoryId = 4; // Điện - Nước
    } elseif (stripos($text, 'sàn') !== false || stripos($text, 'tường') !== false || stripos($text, 'vinyl') !== false) {
        $categoryId = 5; // Sàn và tường
    }
    
    $updateStmt = $pdo->prepare("UPDATE suppliers SET category_id = ? WHERE id = ?");
    $updateStmt->execute([$categoryId, $supplier['id']]);
    echo "  - {$supplier['name']} -> category_id $categoryId\n";
}

echo "\n=== THỐNG KÊ SAU MIGRATE ===\n";

// Products by category
echo "\nSản phẩm theo danh mục:\n";
$result = $pdo->query("
    SELECT pc.name, COUNT(p.id) as count 
    FROM product_categories pc 
    LEFT JOIN products p ON pc.id = p.category_id 
    GROUP BY pc.id, pc.name 
    ORDER BY pc.parent_id IS NULL DESC, pc.order_index, pc.name
");
while ($row = $result->fetch()) {
    echo "  - {$row['name']}: {$row['count']} sản phẩm\n";
}

// Suppliers by category  
echo "\nNhà cung cấp theo danh mục:\n";
$result = $pdo->query("
    SELECT sc.name, COUNT(s.id) as count 
    FROM supplier_categories sc 
    LEFT JOIN suppliers s ON sc.id = s.category_id 
    GROUP BY sc.id, sc.name 
    ORDER BY sc.order_index, sc.name
");
while ($row = $result->fetch()) {
    echo "  - {$row['name']}: {$row['count']} nhà cung cấp\n";
}

echo "\n✅ HOÀN THÀNH MIGRATE!\n";
echo "Bây giờ bạn có thể truy cập:\n";
echo "- http://localhost/vnmt/backend/products.php\n";
echo "- http://localhost/vnmt/backend/suppliers.php\n";
?>