<?php
/**
 * Setup MySQL database for suppliers system
 */

// MySQL connection settings for XAMPP
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'vnmt_db';

try {
    echo "Connecting to MySQL...\n";
    
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' created/verified\n";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop existing tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS supplier_products");
    $pdo->exec("DROP TABLE IF EXISTS suppliers");
    $pdo->exec("DROP TABLE IF EXISTS supplier_categories");
    echo "🗑️ Dropped existing tables\n";
    
    // Create supplier categories table
    $categoriesSql = "CREATE TABLE supplier_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        description TEXT,
        icon VARCHAR(100),
        color VARCHAR(7) DEFAULT '#4da6ff',
        order_index INT DEFAULT 0,
        status BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($categoriesSql);
    echo "📂 Created supplier_categories table\n";
    
    // Create suppliers table
    $suppliersSql = "CREATE TABLE suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        
        -- Basic Company Information
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        code VARCHAR(50) UNIQUE,
        company_type ENUM('corporation', 'limited', 'partnership', 'individual') DEFAULT 'limited',
        
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
        established_year INT,
        employees_count INT,
        
        -- Category and Services
        category_id INT,
        services JSON,
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
        delivery_areas JSON,
        certifications JSON,
        
        -- Meta Information
        status BOOLEAN DEFAULT 0,
        is_featured BOOLEAN DEFAULT 0,
        is_verified BOOLEAN DEFAULT 0,
        views_count INT DEFAULT 0,
        
        -- SEO
        meta_title VARCHAR(255),
        meta_description TEXT,
        
        -- Timestamps
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        FOREIGN KEY (category_id) REFERENCES supplier_categories(id) ON DELETE SET NULL,
        INDEX idx_status (status),
        INDEX idx_category (category_id),
        INDEX idx_featured (is_featured),
        INDEX idx_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($suppliersSql);
    echo "🏢 Created suppliers table\n";
    
    // Create supplier products table
    $productsSql = "CREATE TABLE supplier_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        supplier_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description TEXT,
        short_description VARCHAR(500),
        category VARCHAR(100),
        subcategory VARCHAR(100),
        
        -- Product specifications
        specifications JSON,
        features TEXT,
        applications TEXT,
        
        -- Pricing and availability
        price_range VARCHAR(100),
        unit VARCHAR(50),
        min_order_quantity VARCHAR(100),
        availability_status ENUM('in_stock', 'out_of_stock', 'pre_order', 'discontinued') DEFAULT 'in_stock',
        
        -- Media
        primary_image VARCHAR(255),
        gallery JSON,
        
        -- SEO and Meta
        meta_title VARCHAR(255),
        meta_description TEXT,
        
        -- Status
        status BOOLEAN DEFAULT 1,
        is_featured BOOLEAN DEFAULT 0,
        views_count INT DEFAULT 0,
        
        -- Timestamps
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
        INDEX idx_supplier (supplier_id),
        INDEX idx_status (status),
        INDEX idx_category (category),
        INDEX idx_featured (is_featured),
        INDEX idx_slug (slug),
        UNIQUE KEY unique_supplier_slug (supplier_id, slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($productsSql);
    echo "📦 Created supplier_products table\n";
    
    // Insert sample categories
    $categories = [
        ['Vật liệu xây dựng', 'vat-lieu-xay-dung', 'Cung cấp vật liệu xây dựng cơ bản như xi măng, sắt thép, gạch...', 'fas fa-building', '#e74c3c', 1],
        ['Nội thất', 'noi-that', 'Thiết kế và cung cấp nội thất cho công trình', 'fas fa-couch', '#9b59b6', 2],
        ['Cảnh quan', 'canh-quan', 'Thiết kế và thi công cảnh quan, sân vườn', 'fas fa-tree', '#27ae60', 3],
        ['Điện - Nước', 'dien-nuoc', 'Hệ thống điện, nước và các tiện ích kỹ thuật', 'fas fa-bolt', '#f39c12', 4],
        ['Sàn và tường', 'san-va-tuong', 'Vật liệu ốp lát, sàn gỗ, gạch men, giấy dán tường', 'fas fa-th-large', '#3498db', 5],
        ['Thiết bị máy móc', 'thiet-bi-may-moc', 'Máy móc thiết bị xây dựng và vận hành', 'fas fa-cogs', '#e67e22', 6]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO supplier_categories (name, slug, description, icon, color, order_index) VALUES (?, ?, ?, ?, ?, ?)");
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
            'description' => 'Stella Global chuyên cung cấp các giải pháp sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại. Sản phẩm của Stella Global nổi bật với độ bền cao, kháng khuẩn, khả năng chống trượt và thân thiện với môi trường.',
            'category_id' => 5,
            'services' => '["Phân phối", "Lắp đặt", "Tư vấn thiết kế"]',
            'specialties' => 'Sàn nhựa vinyl, Sàn cao su, Thảm trang trí, Giấy dán tường cao cấp',
            'status' => 1,
            'is_featured' => 1,
            'is_verified' => 1,
            'views_count' => 156,
            'delivery_areas' => '["Hồ Chí Minh", "Hà Nội", "Đà Nẵng", "Cần Thơ"]',
            'certifications' => '["ISO 9001:2015", "ISO 14001:2015", "CE Certification"]'
        ],
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
            'description' => 'Chuyên cung cấp xi măng, sắt thép, gạch block và các vật liệu xây dựng cơ bản chất lượng cao. Với hơn 10 năm kinh nghiệm, chúng tôi cam kết mang đến những sản phẩm chất lượng tốt nhất cho mọi công trình.',
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
            'description' => 'Thiết kế và thi công nội thất hiện đại cho căn hộ, văn phòng và không gian thương mại. Chúng tôi mang đến những giải pháp nội thất sáng tạo, tiện nghi và thẩm mỹ cao.',
            'category_id' => 2,
            'services' => '["Thiết kế", "Thi công", "Bảo hành"]',
            'specialties' => 'Nội thất văn phòng, Nội thất gia đình, Tủ bếp',
            'status' => 1,
            'is_featured' => 1,
            'views_count' => 134
        ]
    ];
    
    $supplierStmt = $pdo->prepare("INSERT INTO suppliers (name, slug, code, email, phone, website, address, city, province, location, description, category_id, services, specialties, status, is_featured, is_verified, views_count, delivery_areas, certifications) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($suppliers as $supplier) {
        $supplierStmt->execute([
            $supplier['name'],
            $supplier['slug'],
            $supplier['code'],
            $supplier['email'],
            $supplier['phone'],
            $supplier['website'] ?? null,
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
            $supplier['is_verified'] ?? 0,
            $supplier['views_count'],
            $supplier['delivery_areas'] ?? null,
            $supplier['certifications'] ?? null
        ]);
    }
    
    echo "🏢 Inserted " . count($suppliers) . " sample suppliers\n";
    
    // Insert sample products for Stella Global
    $stellaId = $pdo->lastInsertId() - 2; // Get Stella's ID (first inserted)
    $products = [
        [
            'supplier_id' => $stellaId,
            'name' => 'Sàn nhựa vinyl Resilient Flooring',
            'slug' => 'san-nhua-vinyl-resilient-flooring',
            'description' => 'Sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại. Sản phẩm nổi bật với độ bền cao, kháng khuẩn, khả năng chống trượt.',
            'short_description' => 'Sàn nhựa vinyl chất lượng cao cho nhiều ứng dụng',
            'category' => 'Sàn',
            'subcategory' => 'Sàn vinyl',
            'specifications' => '{"thickness": "2-8mm", "width": "200-1500mm", "length": "900-1500mm", "wear_layer": "0.2-0.7mm"}',
            'features' => 'Chống trượt, Kháng khuẩn, Chống ẩm, Dễ vệ sinh, Thân thiện môi trường',
            'applications' => 'Phòng GYM, SPA, Khu vui chơi, Văn phòng, Sân chơi, Công trình dân dụng',
            'price_range' => '200,000 - 800,000 VND/m²',
            'unit' => 'm²',
            'status' => 1,
            'is_featured' => 1
        ],
        [
            'supplier_id' => $stellaId,
            'name' => 'Sàn gỗ Laminate chịu lực',
            'slug' => 'san-go-laminate-chiu-luc',
            'description' => 'Sàn gỗ laminate của Bỉ với thiết kế sang trọng, độ bền cao, chịu nước tốt và dễ vệ sinh. Phù hợp cho không gian cao cấp như khách sạn, văn phòng và nhà ở.',
            'short_description' => 'Sàn gỗ laminate cao cấp từ Bỉ',
            'category' => 'Sàn',
            'subcategory' => 'Sàn gỗ',
            'specifications' => '{"thickness": "8-12mm", "width": "190-240mm", "length": "1200-1380mm", "AC_rating": "AC3-AC5"}',
            'features' => 'Chịu nước, Chống trầy xước, Dễ lắp đặt, Bảo hành dài hạn',
            'applications' => 'Sảnh hành lang, Phòng khách, Văn phòng, Nhà ở cao cấp',
            'price_range' => '300,000 - 1,200,000 VND/m²',
            'unit' => 'm²',
            'status' => 1
        ],
        [
            'supplier_id' => $stellaId,
            'name' => 'Giấy dán tường Fidelity cao cấp',
            'slug' => 'giay-dan-tuong-fidelity',
            'description' => 'Giấy dán tường Fidelity – thương hiệu cao cấp đến từ Hoa Kỳ. Sản phẩm nổi bật với độ bền cao, khả năng chống ẩm mốc, dễ vệ sinh và phù hợp với những không gian yêu cầu sự sang trọng.',
            'short_description' => 'Giấy dán tường cao cấp từ Hoa Kỳ',
            'category' => 'Tường',
            'subcategory' => 'Giấy dán tường',
            'specifications' => '{"width": "52-70cm", "length": "10-15m", "pattern_repeat": "32-64cm", "material": "Non-woven, Vinyl"}',
            'features' => 'Chống ẩm mốc, Dễ vệ sinh, Chống trầy xước, Màu sắc bền đẹp',
            'applications' => 'Sảnh hành lang, Phòng khách, Văn phòng, Công trình dân dụng',
            'price_range' => '150,000 - 500,000 VND/m²',
            'unit' => 'm²',
            'status' => 1
        ]
    ];
    
    $productStmt = $pdo->prepare("INSERT INTO supplier_products (supplier_id, name, slug, description, short_description, category, subcategory, specifications, features, applications, price_range, unit, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($products as $product) {
        $productStmt->execute([
            $product['supplier_id'],
            $product['name'],
            $product['slug'],
            $product['description'],
            $product['short_description'],
            $product['category'],
            $product['subcategory'],
            $product['specifications'],
            $product['features'],
            $product['applications'],
            $product['price_range'],
            $product['unit'],
            $product['status'],
            $product['is_featured'] ?? 0
        ]);
    }
    
    echo "📦 Inserted " . count($products) . " sample products for Stella Global\n";
    
    echo "\n✅ MySQL database setup complete!\n";
    echo "🎯 You can now test:\n";
    echo "   - Registration: http://localhost:8080/vnmt/supplier-register.php\n";
    echo "   - Suppliers list: http://localhost:8080/vnmt/suppliers.php\n";
    echo "   - Supplier detail: http://localhost:8080/vnmt/supplier-detail.php?slug=stella-global-viet-nam\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>