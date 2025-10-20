<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    // Set charset to utf8mb4 for proper Vietnamese support
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Adding sample data...\n";
    
    // Add suppliers (4 suppliers only) - with proper Vietnamese encoding
    $suppliers = [
        ['Công ty TNHH Vật liệu XD Miền Nam', 'vat-lieu-mien-nam', 'info@vlmn.vn', '0901234567', 'TP.HCM', '', 'Chuyên cung cấp vật liệu xây dựng chất lượng cao'],
        ['Công ty Thiết bị Công nghiệp Việt', 'thiet-bi-viet', 'contact@tbviet.com', '0987654321', 'Hà Nội', '', 'Nhập khẩu và phân phối thiết bị công nghiệp'],
        ['Tập đoàn Công nghệ Xanh', 'cong-nghe-xanh', 'info@greentech.vn', '0912345678', 'Đà Nẵng', '', 'Giải pháp công nghệ thân thiện môi trường'],
        ['Công ty Cảnh quan Đô thị', 'canh-quan-do-thi', 'hello@landscape.vn', '0934567890', 'Cần Thơ', '', 'Thiết kế và thi công cảnh quan'],
    ];
    
    foreach ($suppliers as $supplier) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, slug, email, phone, address, logo, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute($supplier);
    }
    
    echo "✅ Suppliers added successfully!\n";
    
    // Check supplier IDs
    $supplierIds = $pdo->query("SELECT id FROM suppliers ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
    echo "Supplier IDs: " . implode(", ", $supplierIds) . "\n";
    
    // Add products with proper categories (8 products total - 2 per category)
    // Use actual supplier IDs from database
    $supplierIds = $pdo->query("SELECT id FROM suppliers ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
    
    $products = [
        // Vật liệu (2 products) - supplier 1
        ['Gạch ceramic cao cấp', 'gach-ceramic', 'Gạch ceramic nhập khẩu chất lượng cao', 250000, 1, 1, '[]', $supplierIds[0], 'vật liệu'],
        ['Thép xây dựng Hòa Phát', 'thep-hoa-phat', 'Thép xây dựng chính hãng Hòa Phát', 18500, 1, 1, '[]', $supplierIds[0], 'vật liệu'],
        
        // Thiết bị (2 products) - supplier 2
        ['Máy trộn beton 350L', 'may-tron-beton', 'Máy trộn beton công suất 350L/mẻ', 12500000, 1, 1, '[]', $supplierIds[1], 'thiết bị'],
        ['Cần cẩu tháp 5 tấn', 'can-cau-thap', 'Cần cẩu tháp nâng tải 5 tấn', 450000000, 1, 1, '[]', $supplierIds[1], 'thiết bị'],
        
        // Công nghệ (2 products) - supplier 3
        ['Hệ thống BMS thông minh', 'he-thong-bms', 'Hệ thống quản lý tòa nhà thông minh', 85000000, 1, 1, '[]', $supplierIds[2], 'công nghệ'],
        ['Cảm biến IoT môi trường', 'cam-bien-iot', 'Cảm biến theo dõi chất lượng không khí', 2500000, 1, 1, '[]', $supplierIds[2], 'công nghệ'],
        
        // Cảnh quan (2 products) - supplier 4
        ['Cây xanh công trình', 'cay-xanh-ct', 'Cây xanh dành cho các công trình lớn', 1200000, 1, 1, '[]', $supplierIds[3], 'cảnh quan'],
        ['Hệ thống tưới phun mưa', 'he-thong-tuoi', 'Hệ thống tưới tự động cho cảnh quan', 25000000, 1, 1, '[]', $supplierIds[3], 'cảnh quan'],
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, status, featured, images, supplier_id, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($product);
    }
    
    echo "✅ Sample data added successfully!\n";
    
    // Check counts
    $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $supplierCount = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
    
    echo "\n📊 Database now contains:\n";
    echo "   Products: $productCount\n";
    echo "   Suppliers: $supplierCount\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>