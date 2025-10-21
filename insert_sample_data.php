<?php
/**
 * Script thêm dữ liệu mẫu với encoding UTF-8 đúng cách
 * Chạy sau khi đã reset database
 */

$config = require __DIR__ . '/backend/config.php';

try {
    $host = $config['db_host'] ?? 'localhost';
    $username = $config['db_user'] ?? 'root';
    $password = $config['db_password'] ?? '';
    $dbname = $config['db_name'] ?? 'vnmt_db';
    
    // Kết nối với UTF-8 encoding
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Thiết lập UTF-8 cho session
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "Bắt đầu thêm dữ liệu mẫu...\n";
    
    // 1. Thêm danh mục nhà cung cấp
    echo "Thêm danh mục nhà cung cấp...\n";
    $categories = [
        [1, 'Vật liệu xây dựng', 'vat-lieu-xay-dung', 'Cung cấp vật liệu xây dựng cơ bản như xi măng, sắt thép, gạch...', 'fas fa-building', '#e74c3c', 1, 1],
        [2, 'Nội thất', 'noi-that', 'Thiết kế và cung cấp nội thất cho công trình', 'fas fa-couch', '#9b59b6', 2, 1],
        [3, 'Cảnh quan', 'canh-quan', 'Thiết kế và thi công cảnh quan, sân vườn', 'fas fa-tree', '#27ae60', 3, 1],
        [4, 'Điện - Nước', 'dien-nuoc', 'Hệ thống điện, nước và các tiện ích kỹ thuật', 'fas fa-bolt', '#f39c12', 4, 1],
        [5, 'Sàn và tường', 'san-va-tuong', 'Vật liệu ốp lát, sàn gỗ, gạch men, giấy dán tường', 'fas fa-th-large', '#3498db', 5, 1]
    ];
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS supplier_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        slug VARCHAR(255),
        description TEXT,
        icon VARCHAR(100),
        color VARCHAR(20),
        order_index INT,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO supplier_categories (id, name, slug, description, icon, color, order_index, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    
    // 2. Thêm nhà cung cấp
    echo "Thêm nhà cung cấp...\n";
    $suppliers = [
        [
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
            'country' => 'Vietnam',
            'location' => 'Hồ Chí Minh',
            'description' => 'Stella Global chuyên cung cấp các giải pháp sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại.',
            'category_id' => 5,
            'services' => '["Phân phối", "Lắp đặt", "Tư vấn thiết kế"]',
            'specialties' => 'Sàn nhựa vinyl, Sàn cao su, Thảm trang trí, Giấy dán tường cao cấp',
            'delivery_areas' => '["Hồ Chí Minh", "Hà Nội", "Đà Nẵng", "Cần Thơ"]',
            'certifications' => '["ISO 9001:2015", "ISO 14001:2015", "CE Certification"]',
            'status' => 1,
            'is_featured' => 1,
            'is_verified' => 1,
            'views_count' => 156
        ],
        [
            'name' => 'CÔNG TY CỔ PHẦN VẬT LIỆU XÂY DỰNG VIỆT TIẾN',
            'slug' => 'viet-tien-building-materials',
            'code' => 'VTBM002',
            'company_type' => 'limited',
            'email' => 'info@viettien.com',
            'phone' => '0287654321',
            'address' => '123 Đường Nguyễn Văn Cừ, Quận 5',
            'city' => 'Hồ Chí Minh',
            'province' => 'Hồ Chí Minh',
            'country' => 'Vietnam',
            'location' => 'Hồ Chí Minh',
            'description' => 'Chuyên cung cấp xi măng, sắt thép, gạch block và các vật liệu xây dựng cơ bản chất lượng cao.',
            'category_id' => 1,
            'services' => '["Phân phối", "Vận chuyển", "Tư vấn kỹ thuật"]',
            'specialties' => 'Xi măng, Sắt thép, Gạch block, Cát đá',
            'status' => 1,
            'is_featured' => 0,
            'is_verified' => 0,
            'views_count' => 89
        ],
        [
            'name' => 'CÔNG TY TNHH THIẾT KẾ NỘI THẤT MODERN HOME',
            'slug' => 'modern-home-furniture',
            'code' => 'MHF003',
            'company_type' => 'limited',
            'email' => 'contact@modernhome.vn',
            'phone' => '0981234567',
            'address' => '456 Lê Văn Sỹ, Quận 3',
            'city' => 'Hồ Chí Minh',
            'province' => 'Hồ Chí Minh',
            'country' => 'Vietnam',
            'location' => 'Hồ Chí Minh',
            'description' => 'Thiết kế và thi công nội thất hiện đại cho căn hộ, văn phòng và không gian thương mại.',
            'category_id' => 2,
            'services' => '["Thiết kế", "Thi công", "Bảo hành"]',
            'specialties' => 'Nội thất văn phòng, Nội thật gia đình, Tủ bếp',
            'status' => 1,
            'is_featured' => 1,
            'is_verified' => 0,
            'views_count' => 134
        ]
    ];
    
    // Tạo bảng suppliers mở rộng - xóa và tạo lại
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS suppliers");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    $pdo->exec("CREATE TABLE suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        slug VARCHAR(255),
        code VARCHAR(50),
        company_type VARCHAR(50),
        email VARCHAR(255),
        phone VARCHAR(20),
        fax VARCHAR(20),
        website VARCHAR(255),
        address TEXT,
        city VARCHAR(100),
        province VARCHAR(100),
        country VARCHAR(100),
        postal_code VARCHAR(20),
        location VARCHAR(255),
        description TEXT,
        business_license VARCHAR(100),
        tax_code VARCHAR(50),
        established_year INT,
        employees_count INT,
        category_id INT,
        services TEXT,
        specialties TEXT,
        logo VARCHAR(255),
        cover_image VARCHAR(255),
        brochure VARCHAR(255),
        facebook VARCHAR(255),
        linkedin VARCHAR(255),
        youtube VARCHAR(255),
        instagram VARCHAR(255),
        payment_terms TEXT,
        delivery_areas TEXT,
        certifications TEXT,
        status TINYINT(1) DEFAULT 1,
        is_featured TINYINT(1) DEFAULT 0,
        is_verified TINYINT(1) DEFAULT 0,
        views_count INT DEFAULT 0,
        meta_title VARCHAR(255),
        meta_description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES supplier_categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, slug, code, company_type, email, phone, website, address, city, province, country, location, description, category_id, services, specialties, delivery_areas, certifications, status, is_featured, is_verified, views_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($suppliers as $supplier) {
        $stmt->execute([
            $supplier['name'], $supplier['slug'], $supplier['code'], $supplier['company_type'],
            $supplier['email'], $supplier['phone'], $supplier['website'] ?? null, $supplier['address'],
            $supplier['city'], $supplier['province'], $supplier['country'], $supplier['location'],
            $supplier['description'], $supplier['category_id'], $supplier['services'],
            $supplier['specialties'], $supplier['delivery_areas'] ?? null, $supplier['certifications'] ?? null,
            $supplier['status'], $supplier['is_featured'], $supplier['is_verified'], $supplier['views_count']
        ]);
    }
    
    // 3. Thêm sản phẩm của nhà cung cấp
    echo "Thêm sản phẩm...\n";
    
    // Tạo bảng supplier_products nếu chưa có
    $pdo->exec("CREATE TABLE IF NOT EXISTS supplier_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        supplier_id INT,
        name VARCHAR(255),
        slug VARCHAR(255),
        description TEXT,
        short_description TEXT,
        category VARCHAR(100),
        subcategory VARCHAR(100),
        specifications TEXT,
        features TEXT,
        applications TEXT,
        price_range VARCHAR(100),
        unit VARCHAR(50),
        min_order_quantity INT,
        availability_status VARCHAR(50),
        primary_image VARCHAR(255),
        gallery TEXT,
        meta_title VARCHAR(255),
        meta_description TEXT,
        status TINYINT(1) DEFAULT 1,
        is_featured TINYINT(1) DEFAULT 0,
        views_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $products = [
        [
            'supplier_id' => 1,
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
            'availability_status' => 'in_stock',
            'status' => 1,
            'is_featured' => 1,
            'views_count' => 0
        ],
        [
            'supplier_id' => 1,
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
            'availability_status' => 'in_stock',
            'status' => 1,
            'is_featured' => 0,
            'views_count' => 0
        ],
        [
            'supplier_id' => 1,
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
            'availability_status' => 'in_stock',
            'status' => 1,
            'is_featured' => 0,
            'views_count' => 0
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO supplier_products (supplier_id, name, slug, description, short_description, category, subcategory, specifications, features, applications, price_range, unit, availability_status, status, is_featured, views_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($products as $product) {
        $stmt->execute([
            $product['supplier_id'], $product['name'], $product['slug'], $product['description'],
            $product['short_description'], $product['category'], $product['subcategory'],
            $product['specifications'], $product['features'], $product['applications'],
            $product['price_range'], $product['unit'], $product['availability_status'],
            $product['status'], $product['is_featured'], $product['views_count']
        ]);
    }
    
    // 4. Thêm sản phẩm chính
    echo "Thêm sản phẩm chính...\n";
    
    // Cập nhật bảng products để có đầy đủ cột
    $pdo->exec("ALTER TABLE products 
        ADD COLUMN IF NOT EXISTS manufacturer VARCHAR(255) AFTER supplier_id,
        ADD COLUMN IF NOT EXISTS origin VARCHAR(255) AFTER manufacturer,
        ADD COLUMN IF NOT EXISTS material_type VARCHAR(255) AFTER origin,
        ADD COLUMN IF NOT EXISTS application TEXT AFTER material_type,
        ADD COLUMN IF NOT EXISTS website VARCHAR(255) AFTER application,
        ADD COLUMN IF NOT EXISTS featured_image VARCHAR(255) AFTER website,
        ADD COLUMN IF NOT EXISTS product_function TEXT AFTER featured_image,
        ADD COLUMN IF NOT EXISTS category VARCHAR(100) AFTER product_function,
        ADD COLUMN IF NOT EXISTS thickness VARCHAR(100) AFTER category,
        ADD COLUMN IF NOT EXISTS color VARCHAR(100) AFTER thickness,
        ADD COLUMN IF NOT EXISTS warranty VARCHAR(255) AFTER color,
        ADD COLUMN IF NOT EXISTS stock INT AFTER warranty,
        ADD COLUMN IF NOT EXISTS brand VARCHAR(255) AFTER stock");
    
    $mainProducts = [
        [
            'name' => 'Sàn gỗ công nghiệp Kaindl',
            'slug' => 'san-go-cong-nghiep-kaindl',
            'description' => 'Sàn gỗ công nghiệp Kaindl từ Áo với chất lượng vượt trội, độ bền cao và thiết kế hiện đại. Phù hợp cho mọi không gian từ gia đình đến văn phòng.',
            'price' => 750000.00,
            'status' => 1,
            'featured' => 1,
            'supplier_id' => 1,
            'manufacturer' => 'Kaindl Austria',
            'origin' => 'Áo',
            'material_type' => 'HDF + lớp decor',
            'application' => 'Gia đình, văn phòng, khách sạn',
            'category' => 'Sàn gỗ',
            'thickness' => '8mm-12mm',
            'color' => 'Nhiều màu sắc',
            'warranty' => '25 năm',
            'stock' => 500,
            'classification' => 'Vật liệu',
            'brand' => 'Kaindl'
        ],
        [
            'name' => 'Gạch ceramic Viglacera',
            'slug' => 'gach-ceramic-viglacera',
            'description' => 'Gạch ceramic Viglacera chất lượng cao, được sản xuất tại Việt Nam với công nghệ hiện đại. Đa dạng về mẫu mã và kích thước.',
            'price' => 450000.00,
            'status' => 1,
            'featured' => 0,
            'supplier_id' => 2,
            'manufacturer' => 'Viglacera',
            'origin' => 'Việt Nam',
            'material_type' => 'Ceramic',
            'application' => 'Ốp lát nền, tường',
            'category' => 'Gạch ốp lát',
            'thickness' => '8mm-10mm',
            'color' => 'Đa dạng',
            'warranty' => '10 năm',
            'stock' => 1000,
            'classification' => 'Vật liệu',
            'brand' => 'Viglacera'
        ],
        [
            'name' => 'Bàn làm việc hiện đại',
            'slug' => 'ban-lam-viec-hien-dai',
            'description' => 'Bàn làm việc thiết kế hiện đại với chất liệu gỗ MDF phủ melamine, chống xước và chống nước. Phù hợp cho văn phòng và nhà ở.',
            'price' => 2500000.00,
            'status' => 1,
            'featured' => 1,
            'supplier_id' => 3,
            'manufacturer' => 'Modern Home',
            'origin' => 'Việt Nam',
            'material_type' => 'MDF phủ melamine',
            'application' => 'Văn phòng, nhà ở',
            'category' => 'Nội thất',
            'thickness' => '25mm',
            'color' => 'Trắng, Nâu, Xám',
            'warranty' => '2 năm',
            'stock' => 50,
            'classification' => 'Thiết Bị',
            'brand' => 'Modern Home'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, status, featured, supplier_id, manufacturer, origin, material_type, application, category, thickness, color, warranty, stock, classification, brand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($mainProducts as $product) {
        $stmt->execute([
            $product['name'], $product['slug'], $product['description'], $product['price'],
            $product['status'], $product['featured'], $product['supplier_id'],
            $product['manufacturer'], $product['origin'], $product['material_type'],
            $product['application'], $product['category'], $product['thickness'],
            $product['color'], $product['warranty'], $product['stock'],
            $product['classification'], $product['brand']
        ]);
    }
    
    echo "Hoàn tất! Dữ liệu mẫu đã được thêm thành công với encoding UTF-8 đúng cách.\n";
    echo "Bạn có thể kiểm tra lại trang web để xem dữ liệu hiển thị đúng font tiếng Việt.\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>