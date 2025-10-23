<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'inc/db_frontend.php';

// Get product ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: products.php');
    exit;
}

try {
    $pdo = getFrontendPDO();

// Get product details with supplier info
$stmt = $pdo->prepare("
        SELECT p.*, s.name as supplier_name, s.email as supplier_email, 
           s.phone as supplier_phone, s.address as supplier_address, s.logo as supplier_logo,
           s.website as supplier_website, s.description as supplier_description
    FROM products p 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}
    
    // Get products from same supplier
    $supplierProducts = [];
    if ($product['supplier_id']) {
        $stmt = $pdo->prepare("
            SELECT id, name, price, featured_image, category
            FROM products 
            WHERE supplier_id = ? AND id != ? AND status = 1 
            ORDER BY created_at DESC 
            LIMIT 6
        ");
        $stmt->execute([$product['supplier_id'], $id]);
        $supplierProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get related products (same category, different product)
    $relatedProducts = [];
    if ($product['category']) {
$stmt = $pdo->prepare("
            SELECT id, name, price, featured_image, category, brand
            FROM products 
            WHERE category = ? AND id != ? AND status = 1 
    ORDER BY RAND() 
    LIMIT 6
");
        $stmt->execute([$product['category'], $id]);
$relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Parse classification for display
$classifications = !empty($product['classification']) ? explode(',', $product['classification']) : [];

// Format price
    $formattedPrice = !empty($product['price']) ? number_format($product['price'], 0, ',', '.') . 'đ' : 'Liên hệ';
    
} catch (Exception $e) {
    error_log("Error loading product: " . $e->getMessage());
    header('Location: products.php');
    exit;
}

include __DIR__ . '/inc/header-new.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']) ?> - VNMaterial</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .page-container {
            max-width: 1400px;
            margin: 70px auto 3rem;
            padding: 0 30px;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .breadcrumb a {
            color: #0284c7;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover {
            color: #38bdf8;
        }
        
        .breadcrumb i {
            font-size: 0.85rem;
        }
        
        /* Product Main Section */
        .product-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }
        
        /* Left Column */
        .product-left-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        /* Image Section */
        .product-image-container {
            position: relative;
        }
        
        .product-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
        }
        
        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        /* Product Info */
        .product-info {
            display: flex;
            flex-direction: column;
        }
        
        .product-category-tag {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            width: fit-content;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .product-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .product-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
            flex-wrap: wrap;
        }
        
        .product-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .product-meta-item i {
            color: #38bdf8;
        }
        
        .product-price-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .price-label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #059669;
            margin-bottom: 0.5rem;
        }
        
        .price-note {
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .product-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .btn {
            flex: 1;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #0284c7;
            border: 2px solid #38bdf8;
        }
        
        .btn-secondary:hover {
            background: #f0f9ff;
        }
        
        .product-specs {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .specs-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e0f2fe;
        }
        
        .specs-title i {
            color: #38bdf8;
        }
        
        .specs-list {
            list-style: none;
        }
        
        .specs-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.85rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            gap: 1rem;
        }
        
        .specs-list li:last-child {
            border-bottom: none;
        }
        
        .spec-label {
            color: #64748b;
            font-weight: 500;
        }
        
        .spec-value {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }
        
        /* Tabs */
        .product-tabs {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }
        
        .tab-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .tab-button {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            font-size: 0.95rem;
        }
        
        .tab-button:hover {
            color: #0284c7;
        }
        
        .tab-button.active {
            color: #0284c7;
        }
        
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-content h3 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab-content p {
            color: #475569;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        
        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .specs-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .specs-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Product Lists */
        .product-list-section {
            margin-bottom: 2rem;
            position: relative;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        /* Scroll Container */
        .products-scroll-wrapper {
            position: relative;
        }
        
        /* Navigation Buttons */
        .scroll-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            background: white;
            border: 2px solid #38bdf8;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .scroll-nav-btn:hover {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.4);
        }
        
        .scroll-nav-btn.active {
            display: flex;
        }
        
        .scroll-nav-btn i {
            font-size: 1.2rem;
            color: #38bdf8;
        }
        
        .scroll-nav-btn:hover i {
            color: white;
        }
        
        .scroll-nav-btn.prev {
            left: -22px;
        }
        
        .scroll-nav-btn.next {
            right: -22px;
        }
        
        .scroll-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .scroll-nav-btn:disabled:hover {
            background: white;
            transform: translateY(-50%) scale(1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .scroll-nav-btn:disabled:hover i {
            color: #38bdf8;
        }
        
        .section-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title i {
            color: #38bdf8;
        }
        
        .section-title a {
            color: #0284c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .section-title a:hover {
            color: #38bdf8;
        }
        
        .view-all {
            color: #0284c7;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .view-all:hover {
            color: #38bdf8;
        }
        
        .products-grid {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 1rem;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        
        .products-grid::-webkit-scrollbar {
            height: 8px;
        }
        
        .products-grid::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .products-grid::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            border-radius: 10px;
        }
        
        .products-grid::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            flex: 0 0 280px;
            min-width: 280px;
            height: 340px;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .product-card-image {
            width: 100%;
            height: 180px;
            min-height: 180px;
            max-height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            flex-shrink: 0;
        }
        
        .product-card-body {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .product-card-category {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
        }
        
        .product-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            height: 2.6rem;
            flex-shrink: 0;
        }
        
        .product-card-brand {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-shrink: 0;
        }
        
        .product-card-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #059669;
            margin-top: auto;
            display: none;
        }
        
        .supplier-info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 2rem;
            border-radius: 16px;
            margin-top: 1.5rem;
            border: 2px solid #bae6fd;
        }
        
        .supplier-info-box h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0284c7;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #bae6fd;
        }
        
        .supplier-info-box h4 i {
            color: #38bdf8;
        }
        
        .supplier-info-box a {
            color: #0284c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .supplier-info-box a:hover {
            color: #38bdf8;
        }
        
        .supplier-contact {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .supplier-name-link {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .supplier-contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #475569;
            font-size: 0.9rem;
        }
        
        .supplier-contact-item i {
            color: #38bdf8;
            width: 20px;
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .product-main {
                grid-template-columns: 1fr;
            }
            
            .product-card {
                flex: 0 0 260px;
                min-width: 260px;
                height: 320px;
            }
            
            .product-card-image {
                height: 160px;
                min-height: 160px;
                max-height: 160px;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .scroll-nav-btn {
                width: 40px;
                height: 40px;
            }
            
            .scroll-nav-btn.prev {
                left: -15px;
            }
            
            .scroll-nav-btn.next {
                right: -15px;
            }
        }
        
        @media (max-width: 640px) {
            .page-container {
                padding: 0 15px;
                margin-top: 50px;
            }
            
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-price {
                font-size: 2rem;
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .product-card {
                flex: 0 0 240px;
                min-width: 240px;
                height: 300px;
            }
            
            .product-card-image {
                height: 150px;
                min-height: 150px;
                max-height: 150px;
            }
            
            .product-card-title {
                font-size: 0.9rem;
                height: 2.5rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .scroll-nav-btn {
                width: 35px;
                height: 35px;
            }
            
            .scroll-nav-btn i {
                font-size: 1rem;
            }
            
            .scroll-nav-btn.prev {
                left: -10px;
            }
            
            .scroll-nav-btn.next {
                right: -10px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container" data-product-category="<?php echo htmlspecialchars($product['category'] ?? '') ?>">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span>/</span>
            <a href="products.php">Sản phẩm</a>
            <?php if ($product['category']): ?>
            <span>/</span>
                <a href="products.php?category=<?php echo urlencode($product['category']) ?>">
                    <?php echo htmlspecialchars(ucfirst($product['category'])) ?>
                </a>
            <?php endif; ?>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']) ?></span>
        </nav>

        <!-- Main Product Content -->
        <div class="product-main">
            <!-- Left: Product Image & Basic Info -->
            <div class="product-left-column">
                <div class="product-image-container">
                    <?php if ($product['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($product['featured_image']) ?>" 
                             alt="<?php echo htmlspecialchars($product['name']) ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box-open" style="font-size: 4rem; color: #cbd5e1; opacity: 0.5;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($product['status']): ?>
                        <div class="product-badge">
                            <i class="fas fa-check-circle"></i> Có sẵn
                        </div>
                    <?php else: ?>
                        <div class="product-badge" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                            <i class="fas fa-times-circle"></i> Hết hàng
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Basic Product Info -->
                <div class="product-specs">
                    <div class="specs-title">
                        <i class="fas fa-clipboard-list"></i> Thông tin cơ bản
                    </div>
                    <ul class="specs-list">
                        <li>
                            <span class="spec-label">Mã sản phẩm</span>
                            <span class="spec-value">#SP<?php echo str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></span>
                        </li>
                        <?php if ($product['material_type']): ?>
                        <li>
                            <span class="spec-label">Loại vật liệu</span>
                            <span class="spec-value"><?php echo htmlspecialchars($product['material_type']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($product['thickness']): ?>
                        <li>
                            <span class="spec-label">Độ dày</span>
                            <span class="spec-value"><?php echo htmlspecialchars($product['thickness']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($product['color']): ?>
                        <li>
                            <span class="spec-label">Màu sắc</span>
                            <span class="spec-value"><?php echo htmlspecialchars($product['color']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($product['warranty']): ?>
                        <li>
                            <span class="spec-label">Bảo hành</span>
                            <span class="spec-value"><?php echo htmlspecialchars($product['warranty']) ?></span>
                        </li>
                        <?php endif; ?>
                        <li>
                            <span class="spec-label">Trạng thái</span>
                            <span class="spec-value" style="color: <?php echo $product['status'] ? '#10b981' : '#ef4444' ?>;">
                                <?php echo $product['status'] ? 'Có sẵn' : 'Hết hàng' ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="product-info">
                <?php if ($product['category']): ?>
                    <div class="product-category-tag">
                        <?php echo htmlspecialchars(ucfirst($product['category'])) ?>
                    </div>
                <?php endif; ?>
                
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']) ?></h1>
                
                <div class="product-meta">
                    <?php if ($product['brand']): ?>
                        <div class="product-meta-item">
                            <i class="fas fa-copyright"></i>
                            <?php echo htmlspecialchars($product['brand']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($product['manufacturer']): ?>
                        <div class="product-meta-item">
                            <i class="fas fa-industry"></i>
                            <?php echo htmlspecialchars($product['manufacturer']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($product['origin']): ?>
                        <div class="product-meta-item">
                            <i class="fas fa-globe"></i>
                            <?php echo htmlspecialchars($product['origin']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-price-section">
                    <div class="price-label">Giá sản phẩm</div>
                    <div class="product-price"><?php echo $formattedPrice ?></div>
                    <div class="price-note">
                        <i class="fas fa-info-circle"></i> Giá có thể thay đổi. Vui lòng liên hệ để có báo giá chính xác.
                    </div>
                </div>
                
                <div class="product-actions">
                    <a href="tel:<?php echo htmlspecialchars($product['supplier_phone'] ?: '0123456789') ?>" class="btn btn-primary">
                        <i class="fas fa-phone"></i> Liên hệ báo giá
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($product['supplier_email'] ?: 'info@vnmaterial.com') ?>?subject=<?php echo urlencode('Yêu cầu báo giá: ' . $product['name']) ?>" class="btn btn-secondary">
                        <i class="fas fa-envelope"></i> Gửi email
                    </a>
                </div>
                
                <!-- Supplier Info -->
                <?php if ($product['supplier_name']): ?>
                <div class="supplier-info-box">
                    <h4><i class="fas fa-handshake"></i> Nhà cung cấp</h4>
                    <div class="supplier-contact">
                        <div class="supplier-name-link">
                            <?php if ($product['supplier_id']): ?>
                                <a href="supplier-detail.php?id=<?php echo $product['supplier_id'] ?>">
                                    <?php echo htmlspecialchars($product['supplier_name']) ?>
                                    <i class="fas fa-external-link-alt" style="font-size: 0.75rem; margin-left: 0.3rem; opacity: 0.7;"></i>
                                </a>
                            <?php else: ?>
                                <span style="color: #1e293b;">
                                    <?php echo htmlspecialchars($product['supplier_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($product['supplier_phone']): ?>
                            <div class="supplier-contact-item">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($product['supplier_phone']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['supplier_email']): ?>
                            <div class="supplier-contact-item">
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($product['supplier_email']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['supplier_website']): ?>
                            <div class="supplier-contact-item">
                                <i class="fas fa-globe"></i>
                                <a href="<?php echo htmlspecialchars($product['supplier_website']) ?>" target="_blank" style="color: #0284c7;">
                                    Website
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Tabs -->
                <div class="product-tabs">
                    <div class="tab-nav">
                        <button class="tab-button active" onclick="showTab('description')">
                            <i class="fas fa-info-circle"></i> Mô tả sản phẩm
                        </button>
                        <button class="tab-button" onclick="showTab('specifications')">
                            <i class="fas fa-list-ul"></i> Thông số kỹ thuật
                        </button>
                    </div>

                    <div id="description" class="tab-content active">
                        <h3><i class="fas fa-info-circle"></i> Thông tin sản phẩm</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?: 'Đang cập nhật thông tin mô tả sản phẩm.')) ?></p>
                        
                        <?php if ($product['application']): ?>
                    <h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-tools"></i> Ứng dụng
                    </h4>
                            <p><?php echo nl2br(htmlspecialchars($product['application'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($product['product_function']): ?>
                    <h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-cogs"></i> Chức năng
                    </h4>
                            <p><?php echo nl2br(htmlspecialchars($product['product_function'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div id="specifications" class="tab-content">
                        <h3><i class="fas fa-list-ul"></i> Thông số kỹ thuật</h3>
                <table class="specs-table">
                            <tbody>
                                <?php if ($product['manufacturer']): ?>
                                <tr>
                                    <td><strong>Nhà sản xuất</strong></td>
                                    <td><?php echo htmlspecialchars($product['manufacturer']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['origin']): ?>
                                <tr>
                                    <td><strong>Xuất xứ</strong></td>
                                    <td><?php echo htmlspecialchars($product['origin']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['material_type']): ?>
                                <tr>
                                    <td><strong>Loại vật liệu</strong></td>
                                    <td><?php echo htmlspecialchars($product['material_type']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['thickness']): ?>
                                <tr>
                                    <td><strong>Độ dày</strong></td>
                                    <td><?php echo htmlspecialchars($product['thickness']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['color']): ?>
                                <tr>
                                    <td><strong>Màu sắc</strong></td>
                                    <td><?php echo htmlspecialchars($product['color']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['warranty']): ?>
                                <tr>
                                    <td><strong>Bảo hành</strong></td>
                                    <td><?php echo htmlspecialchars($product['warranty']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['stock']): ?>
                                <tr>
                                    <td><strong>Số lượng tồn kho</strong></td>
                                    <td><?php echo number_format($product['stock']) ?> sản phẩm</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
            </div>
                    </div>

        <!-- Supplier Products Section -->
        <?php if (!empty($supplierProducts)): ?>
        <section class="product-list-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-store"></i>
                    Sản phẩm khác từ 
                    <?php if ($product['supplier_id']): ?>
                        <a href="supplier-detail.php?id=<?php echo $product['supplier_id'] ?>">
                            <?php echo htmlspecialchars($product['supplier_name'] ?: 'nhà cung cấp này') ?>
                        </a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($product['supplier_name'] ?: 'nhà cung cấp này') ?>
                            <?php endif; ?>
                </h2>
                <?php if ($product['supplier_id']): ?>
                <a href="supplier-detail.php?id=<?php echo $product['supplier_id'] ?>" class="view-all">
                    Xem tất cả <i class="fas fa-arrow-right"></i>
                </a>
                                    <?php endif; ?>
                                </div>
            <div class="products-scroll-wrapper">
                <button class="scroll-nav-btn prev" data-scroll="supplier-products">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="products-grid" id="supplier-products">
                <?php foreach ($supplierProducts as $sp): ?>
                <a href="product-detail.php?id=<?php echo $sp['id'] ?>" class="product-card">
                    <?php if ($sp['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($sp['featured_image']) ?>" 
                             alt="<?php echo htmlspecialchars($sp['name']) ?>" 
                             class="product-card-image">
                    <?php else: ?>
                        <div class="product-card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box-open" style="font-size: 2.5rem; color: #cbd5e1; opacity: 0.5;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="product-card-body">
                        <?php if ($sp['category']): ?>
                            <div class="product-card-category"><?php echo htmlspecialchars(ucfirst($sp['category'])) ?></div>
                        <?php endif; ?>
                        <h3 class="product-card-title"><?php echo htmlspecialchars($sp['name']) ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
                </div>
                <button class="scroll-nav-btn next" data-scroll="supplier-products">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </section>
        <?php endif; ?>

        <!-- Related Products Section -->
        <?php if (!empty($relatedProducts)): ?>
        <section class="product-list-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-th-large"></i>
                    Sản phẩm cùng danh mục
                </h2>
                <?php if ($product['category']): ?>
                <a href="products.php?category=<?php echo urlencode($product['category']) ?>" class="view-all">
                    Xem tất cả <i class="fas fa-arrow-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
            <div class="products-scroll-wrapper">
                <button class="scroll-nav-btn prev" data-scroll="related-products">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="products-grid" id="related-products">
                <?php foreach ($relatedProducts as $rp): ?>
                <a href="product-detail.php?id=<?php echo $rp['id'] ?>" class="product-card">
                    <?php if ($rp['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($rp['featured_image']) ?>" 
                             alt="<?php echo htmlspecialchars($rp['name']) ?>" 
                             class="product-card-image">
                    <?php else: ?>
                        <div class="product-card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box-open" style="font-size: 2.5rem; color: #cbd5e1; opacity: 0.5;"></i>
                </div>
                        <?php endif; ?>
                    <div class="product-card-body">
                        <?php if ($rp['category']): ?>
                            <div class="product-card-category"><?php echo htmlspecialchars(ucfirst($rp['category'])) ?></div>
                        <?php endif; ?>
                        <h3 class="product-card-title"><?php echo htmlspecialchars($rp['name']) ?></h3>
                        <?php if ($rp['brand']): ?>
                            <div class="product-card-brand">
                                <i class="fas fa-copyright"></i> <?php echo htmlspecialchars($rp['brand']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    </a>
                <?php endforeach; ?>
                </div>
                <button class="scroll-nav-btn next" data-scroll="related-products">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <script>
        // Tab switching
        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-button');
            
            tabContents.forEach(content => content.classList.remove('active'));
            tabButtons.forEach(button => button.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        // Horizontal scroll with mouse drag and navigation buttons
        document.addEventListener('DOMContentLoaded', function() {
            const sliders = document.querySelectorAll('.products-grid');
            
            sliders.forEach(slider => {
                let isDown = false;
                let startX;
                let scrollLeft;
                
                // Mouse drag functionality
                slider.addEventListener('mousedown', (e) => {
                    isDown = true;
                    slider.style.cursor = 'grabbing';
                    startX = e.pageX - slider.offsetLeft;
                    scrollLeft = slider.scrollLeft;
                });
                
                slider.addEventListener('mouseleave', () => {
                    isDown = false;
                    slider.style.cursor = 'grab';
                });
                
                slider.addEventListener('mouseup', () => {
                    isDown = false;
                    slider.style.cursor = 'grab';
                });
                
                slider.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - slider.offsetLeft;
                    const walk = (x - startX) * 2;
                    slider.scrollLeft = scrollLeft - walk;
                });
                
                // Set initial cursor
                slider.style.cursor = 'grab';
                
                // Check if scrolling is needed
                function checkScrollButtons() {
                    const scrollerId = slider.id;
                    if (!scrollerId) return;
                    
                    const prevBtn = document.querySelector(`.scroll-nav-btn.prev[data-scroll="${scrollerId}"]`);
                    const nextBtn = document.querySelector(`.scroll-nav-btn.next[data-scroll="${scrollerId}"]`);
                    
                    if (!prevBtn || !nextBtn) return;
                    
                    // Check if content overflows
                    const hasOverflow = slider.scrollWidth > slider.clientWidth;
                    
                    if (hasOverflow) {
                        prevBtn.classList.add('active');
                        nextBtn.classList.add('active');
                        
                        // Update button states based on scroll position
                        prevBtn.disabled = slider.scrollLeft <= 0;
                        nextBtn.disabled = slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 1;
                    } else {
                        prevBtn.classList.remove('active');
                        nextBtn.classList.remove('active');
                    }
                }
                
                // Initial check
                checkScrollButtons();
                
                // Check on scroll
                slider.addEventListener('scroll', checkScrollButtons);
                
                // Check on window resize
                window.addEventListener('resize', checkScrollButtons);
            });
            
            // Navigation button click handlers
            document.querySelectorAll('.scroll-nav-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const scrollerId = this.getAttribute('data-scroll');
                    const slider = document.getElementById(scrollerId);
                    
                    if (!slider) return;
                    
                    const scrollAmount = 300; // Scroll by 300px
                    const isPrev = this.classList.contains('prev');
                    
                    slider.scrollBy({
                        left: isPrev ? -scrollAmount : scrollAmount,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>

<?php include __DIR__ . '/inc/footer-new.php'; ?>