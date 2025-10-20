<?php
/**
 * Enhanced Suppliers Database Migration
 * Creates a comprehensive suppliers table with all necessary fields for registration and profile
 */

require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Drop existing table if it exists
    $pdo->exec("DROP TABLE IF EXISTS suppliers");
    
    // Create enhanced suppliers table
    $sql = "CREATE TABLE suppliers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        
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
        location TEXT, -- Combined location string for display
        
        -- Business Information
        description TEXT,
        business_license VARCHAR(100),
        tax_code VARCHAR(50),
        established_year INTEGER,
        employees_count INTEGER,
        
        -- Category and Services
        category_id INTEGER,
        services TEXT, -- JSON array of services
        specialties TEXT, -- Areas of expertise
        
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
        delivery_areas TEXT, -- JSON array of delivery locations
        certifications TEXT, -- JSON array of certifications
        
        -- Meta Information
        status BOOLEAN DEFAULT 0, -- 0=pending, 1=active
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
    
    // Create categories table if not exists
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
        'description' => 'Stella Global chuyên cung cấp các giải pháp sàn nhựa vinyl chất lượng cao, được ứng dụng rộng rãi trong các lĩnh vực như thể thao, giáo dục, y tế và thương mại.',
        'category_id' => 5, // Sàn và tường
        'services' => '["Phân phối", "Lắp đặt"]',
        'specialties' => 'Sàn nhựa vinyl, Sàn cao su, Thảm trang trí, Giấy dán tường cao cấp',
        'established_year' => 2010,
        'employees_count' => 25,
        'status' => 1,
        'is_featured' => 1,
        'is_verified' => 1
    ];
    
    $fields = implode(', ', array_keys($sampleSupplier));
    $placeholders = ':' . implode(', :', array_keys($sampleSupplier));
    $insertStmt = $pdo->prepare("INSERT INTO suppliers ($fields) VALUES ($placeholders)");
    $insertStmt->execute($sampleSupplier);
    
    echo "✅ Enhanced suppliers database created successfully!\n";
    echo "📊 Sample categories and supplier data inserted.\n";
    echo "🏢 Ready for supplier registration and profile pages.\n";
    
} catch (Exception $e) {
    echo "❌ Error creating suppliers database: " . $e->getMessage() . "\n";
}
?>