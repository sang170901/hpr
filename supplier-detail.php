<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');

require_once 'inc/db_frontend.php';

// Get supplier slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: suppliers.php');
    exit;
}

try {
    $pdo = getFrontendPDO();
    
    // Get supplier details
    $supplierQuery = "SELECT * FROM suppliers WHERE slug = :slug AND status = 1";
    $supplierStmt = $pdo->prepare($supplierQuery);
    $supplierStmt->execute([':slug' => $slug]);
    $supplier = $supplierStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$supplier) {
        header('Location: suppliers.php');
        exit;
    }
    
    // Get products from this supplier
    $productsQuery = "SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 1 ORDER BY name ASC";
    $productsStmt = $pdo->prepare($productsQuery);
    $productsStmt->execute([':supplier_id' => $supplier['id']]);
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get related suppliers in same category
    $relatedQuery = "SELECT * FROM suppliers WHERE category = :category AND id != :id AND status = 1 ORDER BY name ASC LIMIT 4";
    $relatedStmt = $pdo->prepare($relatedQuery);
    $relatedStmt->execute([':category' => $supplier['category'], ':id' => $supplier['id']]);
    $relatedSuppliers = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    header('Location: suppliers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($supplier['name']); ?> - Chi tiết nhà cung cấp</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Site primary palette (match `inc/header-new.php`) */
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa; /* lighter blue */
            --accent-color: #7dd3fc; /* light cyan-blue to keep page light-blue */
            --bg-header: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 30%, #bae6fd 70%, #7dd3fc 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-light); /* Light blue tone for the entire page */
            color: #1f2937; /* Ensure text remains readable */
            line-height: 1.6;
        }
        
        .hero-section {
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            background: linear-gradient(135deg, #60a5fa 0%, #7dd3fc 100%); /* light blue gradient */
            padding: 120px 0 80px;
            overflow: hidden;
            min-height: 420px; /* ensure visible hero area */
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            max-width: 1400px; /* wider content */
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
            max-width: 900px; /* allow wider subtitle */
            margin: 0 auto 2.5rem;
            font-weight: 400;
        }
        
        .breadcrumb {
            margin-bottom: 2rem;
        }
        
        .breadcrumb a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb a:hover {
            color: white;
        }
        
        .breadcrumb span {
            color: rgba(255,255,255,0.6);
            margin: 0 0.5rem;
        }
        
        .supplier-hero {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: center;
        }
        
        .supplier-logo-section {
            text-align: center;
        }
        
        .supplier-logo-large {
            width: 150px;
            height: 150px;
            border-radius: 25px;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 1.5rem;
        }
        
        .supplier-category-hero {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .supplier-info {
            color: white;
        }
        
        .supplier-name-large {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .supplier-description-large {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.7;
        }
        
        .supplier-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: rgba(255,255,255,0.9);
        }
        
        .meta-item i {
            font-size: 1.2rem;
            width: 20px;
        }
        
        .main-content {
            max-width: 1400px; /* Increased width */
            margin: -40px auto 0;
            padding: 0 40px 4rem; /* Adjusted padding */
            position: relative;
            z-index: 10;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 3fr 1fr; /* Adjusted proportions for wider main content */
            gap: 4rem; /* Increased gap for better spacing */
        }
        
        .content-main {
            background: white; /* Keep content readable */
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
        
        .content-sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .product-card {
            background: #f8fafc;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            background: white;
        }
        
        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .product-category {
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.8rem;
        }
        
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #059669;
        }
        
        .sidebar-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }
        
        .contact-list {
            list-style: none;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .contact-item:last-child {
            border-bottom: none;
        }
        
        .contact-item i {
            width: 20px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .contact-item strong {
            color: #1e293b;
            font-weight: 600;
        }
        
        .related-suppliers {
            margin-top: 4rem;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .related-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .related-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            margin: 0 auto 1rem;
            border: 2px solid #f1f5f9;
        }
        
        .related-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .related-category {
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-primary {
            /* primary buttons use soft light-blue gradient */
            background: var(--primary-light); /* Match button tone */
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            background: var(--primary-color); /* Slightly darker blue on hover */
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary-light);
            border: 2px solid var(--primary-light);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: var(--primary-light);
            color: white;
        }
        
        @media (max-width: 968px) {
            .supplier-hero {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }
            
            .supplier-name-large {
                font-size: 2.5rem;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .content-main {
                padding: 2rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php 
    require_once 'inc/header-new.php';
    ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a>
                <span>></span>
                <a href="suppliers.php">Nhà cung cấp</a>
                <span>></span>
                <span style="color: white;"><?php echo htmlspecialchars($supplier['name']); ?></span>
            </div>
            
            <div class="supplier-hero">
                <div class="supplier-logo-section">
                    <img src="<?php echo htmlspecialchars($supplier['logo'] ?: 'https://via.placeholder.com/150x150/667eea/ffffff?text=' . urlencode(substr($supplier['name'], 0, 2))); ?>" 
                         alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                         class="supplier-logo-large">
                    
                    <?php if ($supplier['category']): ?>
                        <div class="supplier-category-hero">
                            <?php echo htmlspecialchars($supplier['category']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="supplier-info">
                    <h1 class="supplier-name-large"><?php echo htmlspecialchars($supplier['name']); ?></h1>
                    <p class="supplier-description-large">
                        <?php echo htmlspecialchars($supplier['description'] ?? 'Nhà cung cấp uy tín với nhiều năm kinh nghiệm trong lĩnh vực xây dựng và vật liệu.'); ?>
                    </p>
                    
                    <div class="supplier-meta">
                        <?php if ($supplier['address']): ?>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($supplier['address']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($supplier['phone']): ?>
                            <div class="meta-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($supplier['phone']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Tham gia từ <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-grid">
            <!-- Main Content -->
            <div class="content-main">
                <?php if (!empty($products)): ?>
                    <h2 class="section-title">Sản phẩm & Dịch vụ</h2>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                <div class="product-price">
                                    <?php echo number_format($product['price']); ?> VNĐ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h2 class="section-title">Giới thiệu</h2>
                    <div style="padding: 2rem; background: #f8fafc; border-radius: 15px; text-align: center; color: #64748b;">
                        <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                        <h3 style="margin-bottom: 1rem; color: #475569;">Thông tin sản phẩm đang được cập nhật</h3>
                        <p>Vui lòng liên hệ trực tiếp với nhà cung cấp để biết thêm chi tiết về sản phẩm và dịch vụ.</p>
                    </div>
                <?php endif; ?>
                
                <div class="action-buttons">
                    <a href="tel:<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>" class="btn-primary">
                        <i class="fas fa-phone"></i>
                        Gọi ngay
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>" class="btn-secondary">
                        <i class="fas fa-envelope"></i>
                        Gửi email
                    </a>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="content-sidebar">
                <!-- Contact Info -->
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Thông tin liên hệ</h3>
                    <ul class="contact-list">
                        <?php if ($supplier['email']): ?>
                            <li class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email:</strong><br>
                                    <a href="mailto:<?php echo htmlspecialchars($supplier['email']); ?>" 
                                       style="color: #667eea; text-decoration: none;">
                                        <?php echo htmlspecialchars($supplier['email']); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($supplier['phone']): ?>
                            <li class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Điện thoại:</strong><br>
                                    <a href="tel:<?php echo htmlspecialchars($supplier['phone']); ?>" 
                                       style="color: #667eea; text-decoration: none;">
                                        <?php echo htmlspecialchars($supplier['phone']); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($supplier['address']): ?>
                            <li class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Địa chỉ:</strong><br>
                                    <?php echo htmlspecialchars($supplier['address']); ?>
                                </div>
                            </li>
                        <?php endif; ?>
                        
                        <li class="contact-item">
                            <i class="fas fa-tag"></i>
                            <div>
                                <strong>Danh mục:</strong><br>
                                <?php echo htmlspecialchars($supplier['category'] ?? 'Chưa phân loại'); ?>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Stats -->
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Thống kê</h3>
                    <ul class="contact-list">
                        <li class="contact-item">
                            <i class="fas fa-box"></i>
                            <div>
                                <strong><?php echo count($products); ?> sản phẩm</strong><br>
                                <span style="color: #64748b; font-size: 0.9rem;">Đang kinh doanh</span>
                            </div>
                        </li>
                        <li class="contact-item">
                            <i class="fas fa-calendar-check"></i>
                            <div>
                                <strong><?php echo floor((time() - strtotime($supplier['created_at'])) / (365*24*3600)); ?> năm</strong><br>
                                <span style="color: #64748b; font-size: 0.9rem;">Kinh nghiệm</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Suppliers -->
        <?php if (!empty($relatedSuppliers)): ?>
            <div class="related-suppliers">
                <h2 class="section-title">Nhà cung cấp cùng danh mục</h2>
                <div class="related-grid">
                    <?php foreach ($relatedSuppliers as $related): ?>
                        <a href="supplier-detail.php?slug=<?php echo urlencode($related['slug']); ?>" class="related-card">
                            <img src="<?php echo htmlspecialchars($related['logo'] ?: 'https://via.placeholder.com/60x60/667eea/ffffff?text=' . urlencode(substr($related['name'], 0, 2))); ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>" 
                                 class="related-logo">
                            <div class="related-name"><?php echo htmlspecialchars($related['name']); ?></div>
                            <div class="related-category"><?php echo htmlspecialchars($related['category'] ?? 'Chưa phân loại'); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php 
    require_once 'inc/footer-new.php';
    ?>
</body>
</html>