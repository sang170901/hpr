<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== TẠO BẢNG PRODUCT_CATEGORIES ===\n";

// Tạo bảng product_categories
$sql = "CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    parent_id INT DEFAULT NULL,
    description TEXT,
    icon VARCHAR(100),
    color VARCHAR(20),
    order_index INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_parent (parent_id),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$pdo->exec($sql);
echo "✅ Tạo bảng product_categories thành công\n";

// Thêm dữ liệu mẫu cho product_categories
$categories = [
    // Danh mục chính (parent_id = NULL)
    ['name' => 'Vật liệu', 'slug' => 'vat-lieu', 'parent_id' => NULL, 'icon' => 'fas fa-hammer', 'color' => '#e74c3c', 'order_index' => 1],
    ['name' => 'Thiết bị', 'slug' => 'thiet-bi', 'parent_id' => NULL, 'icon' => 'fas fa-cogs', 'color' => '#3498db', 'order_index' => 2],
    ['name' => 'Công nghệ', 'slug' => 'cong-nghe', 'parent_id' => NULL, 'icon' => 'fas fa-microchip', 'color' => '#9b59b6', 'order_index' => 3],
    ['name' => 'Cảnh quan', 'slug' => 'canh-quan', 'parent_id' => NULL, 'icon' => 'fas fa-tree', 'color' => '#27ae60', 'order_index' => 4],
];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("INSERT INTO product_categories (name, slug, parent_id, icon, color, order_index) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cat['name'], $cat['slug'], $cat['parent_id'], $cat['icon'], $cat['color'], $cat['order_index']]);
}

echo "✅ Thêm 4 danh mục chính thành công\n";

// Lấy ID của các danh mục chính
$vatlieuId = $pdo->lastInsertId() - 3;
$thietbiId = $pdo->lastInsertId() - 2;
$congnghеId = $pdo->lastInsertId() - 1;
$canhquanId = $pdo->lastInsertId();

// Thêm danh mục con cho Vật liệu
$subCategories = [
    // Vật liệu con
    ['name' => 'Sơn', 'slug' => 'son', 'parent_id' => $vatlieuId, 'icon' => 'fas fa-paint-brush', 'color' => '#e67e22'],
    ['name' => 'Gạch', 'slug' => 'gach', 'parent_id' => $vatlieuId, 'icon' => 'fas fa-th-large', 'color' => '#d35400'],
    ['name' => 'Ngói', 'slug' => 'ngoi', 'parent_id' => $vatlieuId, 'icon' => 'fas fa-home', 'color' => '#8e44ad'],
    ['name' => 'Kính', 'slug' => 'kinh', 'parent_id' => $vatlieuId, 'icon' => 'far fa-square', 'color' => '#3498db'],
    ['name' => 'Sàn gỗ', 'slug' => 'san-go', 'parent_id' => $vatlieuId, 'icon' => 'fas fa-grip-lines', 'color' => '#795548'],
    ['name' => 'Xi măng', 'slug' => 'xi-mang', 'parent_id' => $vatlieuId, 'icon' => 'fas fa-cube', 'color' => '#607d8b'],
    
    // Thiết bị con
    ['name' => 'Nội thất', 'slug' => 'noi-that', 'parent_id' => $thietbiId, 'icon' => 'fas fa-couch', 'color' => '#e91e63'],
    ['name' => 'Thiết bị vệ sinh', 'slug' => 'thiet-bi-ve-sinh', 'parent_id' => $thietbiId, 'icon' => 'fas fa-shower', 'color' => '#00bcd4'],
    ['name' => 'Thiết bị điện', 'slug' => 'thiet-bi-dien', 'parent_id' => $thietbiId, 'icon' => 'fas fa-plug', 'color' => '#ffc107'],
    ['name' => 'Máy móc xây dựng', 'slug' => 'may-moc-xay-dung', 'parent_id' => $thietbiId, 'icon' => 'fas fa-truck', 'color' => '#ff5722'],
];

foreach ($subCategories as $sub) {
    $stmt = $pdo->prepare("INSERT INTO product_categories (name, slug, parent_id, icon, color, order_index) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$sub['name'], $sub['slug'], $sub['parent_id'], $sub['icon'], $sub['color'], 0]);
}

echo "✅ Thêm 10 danh mục con thành công\n";

// Hiển thị cấu trúc danh mục
echo "\n=== CẤU TRÚC DANH MỤC SẢN PHẨM ===\n";
$result = $pdo->query("
    SELECT 
        pc1.id, pc1.name as main_category, pc1.slug, pc1.icon, pc1.color,
        pc2.id as sub_id, pc2.name as sub_category, pc2.slug as sub_slug
    FROM product_categories pc1
    LEFT JOIN product_categories pc2 ON pc1.id = pc2.parent_id
    WHERE pc1.parent_id IS NULL
    ORDER BY pc1.order_index, pc2.name
");

$current_main = '';
while ($row = $result->fetch()) {
    if ($row['main_category'] !== $current_main) {
        echo "\n📁 " . $row['main_category'] . " (" . $row['icon'] . " " . $row['color'] . ")\n";
        $current_main = $row['main_category'];
    }
    if ($row['sub_category']) {
        echo "   └── " . $row['sub_category'] . "\n";
    }
}

echo "\n=== THÊM TRƯỜNG CATEGORY_ID VÀO PRODUCTS ===\n";

// Thêm trường category_id vào bảng products
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT AFTER classification");
    $pdo->exec("ALTER TABLE products ADD INDEX idx_category_id (category_id)");
    echo "✅ Thêm trường category_id vào bảng products\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "⚠️  Trường category_id đã tồn tại\n";
    } else {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
    }
}

echo "\n=== ĐỀ XUẤT TỐI ƯU BẢNG PRODUCTS ===\n";
echo "Các trường có thể loại bỏ hoặc tối ưu:\n";
echo "❌ 'category' (text) -> thay bằng 'category_id' (int)\n";
echo "❌ 'material_type' -> gộp vào 'description'\n";
echo "❌ 'product_function' -> gộp vào 'description'\n";
echo "❌ 'application' -> gộp vào 'description'\n";
echo "✅ Giữ: id, name, slug, description, price, status, featured, images, supplier_id, manufacturer, origin, website, featured_image, category_id, thickness, color, warranty, stock, brand, classification\n";

echo "\n=== HOÀN THÀNH ===\n";
echo "✅ Đã tạo cấu trúc danh mục sản phẩm hoàn chỉnh\n";
echo "✅ Bảng supplier_categories đã có sẵn 5 danh mục\n";
echo "✅ Bảng product_categories mới có 4 danh mục chính + 10 danh mục con\n";
echo "✅ Sẵn sàng để cập nhật giao diện admin\n";
?>