<?php
/**
 * Test database connection and create enhanced suppliers table
 */

echo "Testing database connection...\n";

try {
    // Try SQLite first
    $db_path = 'backend/database/vnmt.db';
    
    // Create directory if it doesn't exist
    $dir = dirname($db_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created database directory: $dir\n";
    }
    
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful!\n";
    
    // Drop existing suppliers table if it exists
    $pdo->exec("DROP TABLE IF EXISTS suppliers");
    echo "🗑️ Dropped existing suppliers table\n";
    
    // Create enhanced suppliers table
    $sql = "CREATE TABLE suppliers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        
        -- Basic Company Information
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        code VARCHAR(50) UNIQUE,
        company_type VARCHAR(50) DEFAULT 'limited',
        
        -- Contact Information
        email VARCHAR(255),
        phone VARCHAR(20),
        fax VARCHAR(20),
        website VARCHAR(255),
        
        -- Address Information
        address TEXT,
        city VARCHAR(100),
        province VARCHAR(100),
        country VARCHAR(100) DEFAULT 'Vietnam',
        postal_code VARCHAR(20),
        location TEXT,
        
        -- Business Information
        description TEXT,
        business_license VARCHAR(100),
        tax_code VARCHAR(50),
        established_year INTEGER,
        employees_count INTEGER,
        
        -- Category and Services
        category_id INTEGER,
        services TEXT,
        specialties TEXT,
        
        -- Media Files
        logo VARCHAR(255),
        cover_image VARCHAR(255),
        brochure VARCHAR(255),
        
        -- Social Media
        facebook VARCHAR(255),
        linkedin VARCHAR(255),
        youtube VARCHAR(255),
        instagram VARCHAR(255),
        
        -- Business Details
        payment_terms TEXT,
        delivery_areas TEXT,
        certifications TEXT,
        
        -- Meta Information
        status BOOLEAN DEFAULT 0,
        is_featured BOOLEAN DEFAULT 0,
        is_verified BOOLEAN DEFAULT 0,
        views_count INTEGER DEFAULT 0,
        
        -- SEO
        meta_title VARCHAR(255),
        meta_description TEXT,
        
        -- Timestamps
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "📊 Created enhanced suppliers table\n";
    
    // Create categories table
    $categoriesSql = "CREATE TABLE IF NOT EXISTS supplier_categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        description TEXT,
        icon VARCHAR(100),
        color VARCHAR(7) DEFAULT '#4da6ff',
        order_index INTEGER DEFAULT 0,
        status BOOLEAN DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($categoriesSql);
    echo "📂 Created supplier categories table\n";
    
    // Insert sample categories
    $categories = [
        ['Vật liệu xây dựng', 'vat-lieu-xay-dung', 'Cung cấp vật liệu xây dựng cơ bản như xi măng, sắt thép, gạch...', 'fas fa-building', '#e74c3c'],
        ['Nội thất', 'noi-that', 'Thiết kế và cung cấp nội thất cho công trình', 'fas fa-couch', '#9b59b6'],
        ['Cảnh quan', 'canh-quan', 'Thiết kế và thi công cảnh quan, sân vườn', 'fas fa-tree', '#27ae60'],
        ['Điện - Nước', 'dien-nuoc', 'Hệ thống điện, nước và các tiện ích kỹ thuật', 'fas fa-bolt', '#f39c12'],
        ['Sàn và tường', 'san-va-tuong', 'Vật liệu ốp lát, sàn gỗ, gạch men, giấy dán tường', 'fas fa-th-large', '#3498db'],
        ['Thiết bị máy móc', 'thiet-bi-may-moc', 'Máy móc thiết bị xây dựng và vận hành', 'fas fa-cogs', '#e67e22']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO supplier_categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "📋 Inserted " . count($categories) . " sample categories\n";
    
    // Insert sample supplier data
    $sampleSupplier = [
        'name' => 'CÔNG TY TNHH STELLA GLOBAL VIỆT NAM',
        'slug' => 'stella-global-viet-nam',
        'code' => 'STELLA001',
        'company_type' => 'limited',
        'email' => 'info@stellaglobal.com',
        'phone' => '0932707188',
        'website' => 'www.stellaglobal.com',
        'address' => '34E Trần Khánh Dư, Phường Tân Định, Quận 1',
        'city' => 'Hồ Chí Minh',
        'province' => 'Hồ Chí Minh',
        'location' => 'Hồ Chí Minh',
        'description' => 'Stella Global chuyên cung cấp các giải pháp sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại. Sản phẩm của Stella Global nổi bật với độ bền cao, kháng khuẩn, khả năng chống trượt và thân thiện với môi trường.',
        'category_id' => 5,
        'services' => '["Phân phối", "Lắp đặt", "Tư vấn thiết kế"]',
        'specialties' => 'Sàn nhựa vinyl, Sàn cao su, Thảm trang trí, Giấy dán tường cao cấp',
        'established_year' => 2010,
        'employees_count' => 25,
        'status' => 1,
        'is_featured' => 1,
        'is_verified' => 1,
        'views_count' => 156,
        'delivery_areas' => '["Hồ Chí Minh", "Hà Nội", "Đà Nẵng", "Cần Thơ"]',
        'certifications' => '["ISO 9001:2015", "ISO 14001:2015", "CE Certification"]'
    ];
    
    $fields = implode(', ', array_keys($sampleSupplier));
    $placeholders = ':' . implode(', :', array_keys($sampleSupplier));
    $insertStmt = $pdo->prepare("INSERT INTO suppliers ($fields) VALUES ($placeholders)");
    $insertStmt->execute($sampleSupplier);
    
    echo "🏢 Inserted sample supplier: Stella Global\n";
    
    // Add a few more sample suppliers
    $moreSamples = [
        [
            'name' => 'CÔNG TY CỔ PHẦN VẬT LIỆU XÂY DỰNG VIỆT TIẾN',
            'slug' => 'viet-tien-building-materials',
            'code' => 'VTBM002',
            'email' => 'info@viettien.com',
            'phone' => '0287654321',
            'address' => '123 Đường Nguyễn Văn Cừ, Quận 5',
            'city' => 'Hồ Chí Minh',
            'province' => 'Hồ Chí Minh',
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
            'address' => '456 Lê Văn Sỹ, Quận 3',
            'city' => 'Hồ Chí Minh',
            'location' => 'Hồ Chí Minh',
            'description' => 'Thiết kế và thi công nội thất hiện đại cho căn hộ, văn phòng và không gian thương mại.',
            'category_id' => 2,
            'services' => '["Thiết kế", "Thi công", "Bảo hành"]',
            'specialties' => 'Nội thất văn phòng, Nội thất gia đình, Tủ bếp',
            'status' => 1,
            'views_count' => 134
        ]
    ];
    
    foreach ($moreSamples as $sample) {
        $fields = implode(', ', array_keys($sample));
        $placeholders = ':' . implode(', :', array_keys($sample));
        $stmt = $pdo->prepare("INSERT INTO suppliers ($fields) VALUES ($placeholders)");
        $stmt->execute($sample);
    }
    
    echo "🏢 Inserted " . count($moreSamples) . " additional sample suppliers\n";
    
    echo "\n✅ Enhanced suppliers database created successfully!\n";
    echo "📊 Database location: $db_path\n";
    echo "🚀 Ready for supplier registration and profile pages!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>