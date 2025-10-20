<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Set connection charset
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    
    echo "Fixing Vietnamese data...\n";
    
    // Clear existing data
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE suppliers");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Add suppliers with proper Vietnamese
    $suppliers = [
        ['Công ty TNHH Vật liệu XD Miền Nam', 'vat-lieu-mien-nam', 'info@vlmn.vn', '0901234567', 'TP.HCM', '', 'Chuyên cung cấp vật liệu xây dựng chất lượng cao', 'vật liệu'],
        ['Công ty Thiết bị Công nghiệp Việt', 'thiet-bi-viet', 'contact@tbviet.com', '0987654321', 'Hà Nội', '', 'Nhập khẩu và phân phối thiết bị công nghiệp', 'thiết bị'],
        ['Tập đoàn Công nghệ Xanh', 'cong-nghe-xanh', 'info@greentech.vn', '0912345678', 'Đà Nẵng', '', 'Giải pháp công nghệ thân thiện môi trường', 'công nghệ'],
        ['Công ty Cảnh quan Đô thị', 'canh-quan-do-thi', 'hello@landscape.vn', '0934567890', 'Cần Thơ', '', 'Thiết kế và thi công cảnh quan', 'cảnh quan'],
    ];
    
    foreach ($suppliers as $supplier) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, slug, email, phone, address, logo, description, category, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute($supplier);
    }
    
    // Add products with proper Vietnamese
    $products = [
        ['Gạch ceramic cao cấp', 'gach-ceramic', 'Gạch ceramic nhập khẩu chất lượng cao', 250000, 1, 1, '[]', 1, 'vật liệu'],
        ['Thép xây dựng Hòa Phát', 'thep-hoa-phat', 'Thép xây dựng chính hãng Hòa Phát', 18500, 1, 1, '[]', 1, 'vật liệu'],
        
        ['Máy trộn beton 350L', 'may-tron-beton', 'Máy trộn beton công suất 350L/mẻ', 12500000, 1, 1, '[]', 2, 'thiết bị'],
        ['Cần cẩu tháp 5 tấn', 'can-cau-thap', 'Cần cẩu tháp nâng tải 5 tấn', 450000000, 1, 1, '[]', 2, 'thiết bị'],
        
        ['Hệ thống BMS thông minh', 'he-thong-bms', 'Hệ thống quản lý tòa nhà thông minh', 85000000, 1, 1, '[]', 3, 'công nghệ'],
        ['Cảm biến IoT môi trường', 'cam-bien-iot', 'Cảm biến theo dõi chất lượng không khí', 2500000, 1, 1, '[]', 3, 'công nghệ'],
        
        ['Cây xanh công trình', 'cay-xanh-ct', 'Cây xanh dành cho các công trình lớn', 1200000, 1, 1, '[]', 4, 'cảnh quan'],
        ['Hệ thống tưới phun mưa', 'he-thong-tuoi', 'Hệ thống tưới tự động cho cảnh quan', 25000000, 1, 1, '[]', 4, 'cảnh quan'],
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, status, featured, images, supplier_id, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($product);
    }
    
    echo "✅ Vietnamese data fixed successfully!\n";
    
    // Test display
    echo "\n📋 Suppliers:\n";
    $stmt = $pdo->query("SELECT name, category FROM suppliers");
    while ($row = $stmt->fetch()) {
        echo "   - {$row['name']} ({$row['category']})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>