<?php
/**
 * Simple test to create suppliers table using existing database connection
 */

require_once 'backend/inc/db.php';

try {
    echo "Connecting to existing database...\n";
    $pdo = getPDO();
    echo "✅ Connected successfully!\n";
    
    // Check if suppliers table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='suppliers'");
    if ($stmt->fetchColumn()) {
        echo "📊 Suppliers table already exists, dropping it...\n";
        $pdo->exec("DROP TABLE suppliers");
    }
    
    // Create suppliers table (simplified for SQLite compatibility)
    $sql = "CREATE TABLE suppliers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        code TEXT UNIQUE,
        email TEXT,
        phone TEXT,
        website TEXT,
        address TEXT,
        city TEXT,
        province TEXT,
        location TEXT,
        description TEXT,
        category_id INTEGER,
        services TEXT,
        specialties TEXT,
        logo TEXT,
        status INTEGER DEFAULT 0,
        is_featured INTEGER DEFAULT 0,
        is_verified INTEGER DEFAULT 0,
        views_count INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "📊 Created suppliers table\n";
    
    // Create categories table
    $categoriesSql = "CREATE TABLE IF NOT EXISTS supplier_categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        icon TEXT,
        color TEXT DEFAULT '#4da6ff',
        order_index INTEGER DEFAULT 0,
        status INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($categoriesSql);
    echo "📂 Created categories table\n";
    
    // Insert sample categories
    $categories = [
        ['Vật liệu xây dựng', 'vat-lieu-xay-dung', 'Cung cấp vật liệu xây dựng cơ bản', 'fas fa-building', '#e74c3c'],
        ['Nội thất', 'noi-that', 'Thiết kế và cung cấp nội thất', 'fas fa-couch', '#9b59b6'],
        ['Cảnh quan', 'canh-quan', 'Thiết kế và thi công cảnh quan', 'fas fa-tree', '#27ae60'],
        ['Điện - Nước', 'dien-nuoc', 'Hệ thống điện nước', 'fas fa-bolt', '#f39c12'],
        ['Sàn và tường', 'san-va-tuong', 'Vật liệu ốp lát', 'fas fa-th-large', '#3498db'],
        ['Thiết bị máy móc', 'thiet-bi-may-moc', 'Máy móc xây dựng', 'fas fa-cogs', '#e67e22']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO supplier_categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "📋 Inserted " . count($categories) . " categories\n";
    
    // Insert sample suppliers
    $suppliers = [
        [
            'name' => 'CÔNG TY TNHH STELLA GLOBAL VIỆT NAM',
            'slug' => 'stella-global-viet-nam',
            'code' => 'STELLA001',
            'email' => 'info@stellaglobal.com',
            'phone' => '0932707188',
            'website' => 'www.stellaglobal.com',
            'address' => '34E Trần Khánh Dư, Phường Tân Định, Quận 1',
            'city' => 'Hồ Chí Minh',
            'province' => 'Hồ Chí Minh',
            'location' => 'Hồ Chí Minh',
            'description' => 'Stella Global chuyên cung cấp các giải pháp sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại.',
            'category_id' => 5,
            'services' => '["Phân phối", "Lắp đặt", "Tư vấn thiết kế"]',
            'specialties' => 'Sàn nhựa vinyl, Sàn cao su, Thảm trang trí, Giấy dán tường cao cấp',
            'status' => 1,
            'is_featured' => 1,
            'is_verified' => 1,
            'views_count' => 156
        ],
        [
            'name' => 'CÔNG TY CỔ PHẦN VẬT LIỆU XÂY DỰNG VIỆT TIẾN',
            'slug' => 'viet-tien-building-materials',
            'code' => 'VTBM002',
            'email' => 'info@viettien.com',
            'phone' => '0287654321',
            'address' => '123 Đường Nguyễn Văn Cừ, Quận 5',
            'city' => 'Hồ Chí Minh',
            'location' => 'Hồ Chí Minh',
            'description' => 'Chuyên cung cấp xi măng, sắt thép, gạch block và các vật liệu xây dựng cơ bản chất lượng cao.',
            'category_id' => 1,
            'services' => '["Phân phối", "Vận chuyển", "Tư vấn kỹ thuật"]',
            'specialties' => 'Xi măng, Sắt thép, Gạch block, Cát đá',
            'status' => 1,
            'is_featured' => 0,
            'views_count' => 89
        ],
        [
            'name' => 'CÔNG TY TNHH THIẾT KẾ NỘI THẤT MODERN HOME',
            'slug' => 'modern-home-furniture',
            'code' => 'MHF003',
            'email' => 'contact@modernhome.vn',
            'phone' => '0981234567',
            'address' => '456 Lê Văn Sỹ, Quần 3',
            'city' => 'Hồ Chí Minh',
            'location' => 'Hồ Chí Minh',
            'description' => 'Thiết kế và thi công nội thất hiện đại cho căn hộ, văn phòng và không gian thương mại.',
            'category_id' => 2,
            'services' => '["Thiết kế", "Thi công", "Bảo hành"]',
            'specialties' => 'Nội thất văn phòng, Nội thất gia đình, Tủ bếp',
            'status' => 1,
            'is_featured' => 1,
            'views_count' => 134
        ]
    ];
    
    $supplierStmt = $pdo->prepare("INSERT INTO suppliers (name, slug, code, email, phone, website, address, city, province, location, description, category_id, services, specialties, status, is_featured, views_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($suppliers as $supplier) {
        $supplierStmt->execute([
            $supplier['name'],
            $supplier['slug'],
            $supplier['code'],
            $supplier['email'],
            $supplier['phone'],
            $supplier['website'],
            $supplier['address'],
            $supplier['city'],
            $supplier['province'],
            $supplier['location'],
            $supplier['description'],
            $supplier['category_id'],
            $supplier['services'],
            $supplier['specialties'],
            $supplier['status'],
            $supplier['is_featured'],
            $supplier['views_count']
        ]);
    }
    
    echo "🏢 Inserted " . count($suppliers) . " sample suppliers\n";
    
    echo "\n✅ Database setup complete!\n";
    echo "🎯 You can now test:\n";
    echo "   - Registration: /vnmt/supplier-register.php\n";
    echo "   - Suppliers list: /vnmt/suppliers.php\n";
    echo "   - Supplier detail: /vnmt/supplier-detail.php?slug=stella-global-viet-nam\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>