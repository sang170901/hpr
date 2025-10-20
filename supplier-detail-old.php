<?php 
include 'inc/header-new.php';
require_once 'inc/db_frontend.php';

// Get supplier slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: /vnmt/suppliers.php');
    exit;
}

try {
    $pdo = getFrontendPDO();
    
    // Get supplier details with category
    $stmt = $pdo->prepare("
        SELECT s.*, sc.name as category_name, sc.icon as category_icon, sc.color as category_color
        FROM suppliers s 
        LEFT JOIN supplier_categories sc ON s.category_id = sc.id 
        WHERE s.slug = ? AND s.status = 1
    ");
    $stmt->execute([$slug]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$supplier) {
        header('Location: /vnmt/suppliers.php');
        exit;
    }
    
    // Update view count
    $updateStmt = $pdo->prepare("UPDATE suppliers SET views_count = views_count + 1 WHERE id = ?");
    $updateStmt->execute([$supplier['id']]);
    
    // Get supplier products
    $productsStmt = $pdo->prepare("
        SELECT id, name, slug, short_description, primary_image, category, subcategory, 
               price_range, unit, is_featured, views_count, created_at
        FROM supplier_products 
        WHERE supplier_id = ? AND status = 1 
        ORDER BY is_featured DESC, created_at DESC
    ");
    $productsStmt->execute([$supplier['id']]);
    $supplierProducts = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get related suppliers (same category)
    $relatedStmt = $pdo->prepare("
        SELECT s.*, sc.name as category_name 
        FROM suppliers s 
        LEFT JOIN supplier_categories sc ON s.category_id = sc.id 
        WHERE s.category_id = ? AND s.id != ? AND s.status = 1 
        ORDER BY RAND() 
        LIMIT 3
    ");
    $relatedStmt->execute([$supplier['category_id'], $supplier['id']]);
    $relatedSuppliers = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    header('Location: /vnmt/suppliers.php');
    exit;
}

// Parse JSON fields
$services = json_decode($supplier['services'] ?? '[]', true) ?: [];
$deliveryAreas = json_decode($supplier['delivery_areas'] ?? '[]', true) ?: [];
$certifications = json_decode($supplier['certifications'] ?? '[]', true) ?: [];
?>

<style>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700;800&display=swap');

/* Supplier Detail Page Styles */
.supplier-detail-page {
    min-height: 100vh;
    background: #f8fcff;
    padding-top: 10px;
}

.supplier-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.breadcrumb {
    margin-bottom: 32px;
    font-size: 14px;
    color: #64748b;
}

.breadcrumb-item {
    display: inline-block;
    position: relative;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin: 0 8px;
    color: #cbd5e1;
}

.breadcrumb-item a {
    color: #4da6ff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #2f93f8;
}

.breadcrumb-item.active {
    color: #374151;
    font-weight: 500;
}

/* Header Section */
.supplier-header {
    background: white;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 32px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.supplier-header-content {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 32px;
    align-items: start;
}

.supplier-logo {
    width: 120px;
    height: 120px;
    object-fit: contain;
    background: #f8fafc;
    border-radius: 16px;
    padding: 16px;
    border: 1px solid #e2e8f0;
}

.supplier-info h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
    line-height: 1.2;
}

.supplier-category {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #4da6ff, #2f93f8);
    color: white;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 16px;
}

.supplier-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    margin-bottom: 20px;
    font-size: 14px;
    color: #64748b;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.meta-item i {
    color: #4da6ff;
}

.supplier-description {
    font-size: 1.125rem;
    line-height: 1.7;
    color: #374151;
    margin-bottom: 24px;
}

.supplier-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.action-btn.primary {
    background: linear-gradient(135deg, #4da6ff, #2f93f8);
    color: white;
}

.action-btn.secondary {
    background: #f8fafc;
    color: #4da6ff;
    border: 2px solid #e2e8f0;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(77, 166, 255, 0.3);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 32px;
    margin-bottom: 40px;
}

.main-content {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.info-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.info-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-card h3 i {
    color: #4da6ff;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-item i {
    color: #4da6ff;
    width: 20px;
}

.contact-item a {
    color: #374151;
    text-decoration: none;
}

.contact-item a:hover {
    color: #4da6ff;
}

.services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.service-tag {
    background: #f0f9ff;
    color: #0369a1;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Section Styles */
.content-section {
    margin-bottom: 32px;
}

.content-section h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e2e8f0;
}

/* Related Suppliers */
.related-suppliers {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    margin-bottom: 40px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.related-card {
    background: #f8fafc;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
}

.related-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.related-card h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
}

.related-card p {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 12px;
}

.related-card a {
    color: #4da6ff;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .supplier-header-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .supplier-header {
        padding: 24px;
    }
    
    .main-content {
        padding: 24px;
    }
    
    .supplier-container {
        padding: 0 16px;
    }
    
    .supplier-info h1 {
        font-size: 2rem;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
}

/* Supplier Products Styles */
.supplier-products {
    margin: 48px 0;
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.supplier-products h2 {
    color: #1e293b;
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.supplier-products h2 i {
    color: #4da6ff;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.product-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(77, 166, 255, 0.15);
    border-color: #4da6ff;
}

.product-image {
    height: 200px;
    background: #f8fafc;
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 14px;
}

.product-no-image i {
    font-size: 2rem;
    margin-bottom: 8px;
}

.featured-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-content {
    padding: 20px;
}

.product-meta {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.product-category, .product-subcategory {
    background: #e0f2fe;
    color: #0369a1;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.product-subcategory {
    background: #f0f9ff;
    color: #0284c7;
}

.product-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
    line-height: 1.4;
}

.product-description {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 16px;
}

.product-price {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #059669;
    font-weight: 600;
    margin-bottom: 16px;
    font-size: 1rem;
}

.product-price i {
    color: #10b981;
}

.product-price small {
    color: #6b7280;
    font-weight: 400;
}

.product-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    font-size: 0.75rem;
    color: #64748b;
}

.product-views, .product-date {
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-actions {
    display: flex;
    gap: 8px;
}

.btn-contact-product, .btn-product-details {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-contact-product {
    background: linear-gradient(135deg, #4da6ff, #2f93f8);
    color: white;
}

.btn-contact-product:hover {
    background: linear-gradient(135deg, #2f93f8, #1e7def);
    transform: translateY(-1px);
}

.btn-product-details {
    background: #f8fafc;
    color: #4da6ff;
    border: 1px solid #e2e8f0;
}

.btn-product-details:hover {
    background: #e0f2fe;
    border-color: #4da6ff;
}

.view-all-products {
    text-align: center;
    margin-top: 32px;
}

.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #4da6ff, #2f93f8);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view-all:hover {
    background: linear-gradient(135deg, #2f93f8, #1e7def);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(77, 166, 255, 0.3);
    color: white;
}

.no-products {
    margin: 48px 0;
    background: white;
    border-radius: 16px;
    padding: 48px 32px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.no-products-content i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.no-products h3 {
    color: #374151;
    font-size: 1.5rem;
    margin-bottom: 12px;
}

.no-products p {
    color: #64748b;
    line-height: 1.6;
    max-width: 500px;
    margin: 0 auto 12px;
}

/* Responsive for products */
@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .product-actions {
        flex-direction: column;
    }
    
    .btn-contact-product, .btn-product-details {
        flex: none;
    }
    
    .supplier-products {
        margin: 24px 0;
        padding: 24px 16px;
    }
    
    .supplier-products h2 {
        font-size: 1.5rem;
    }
}
</style>

<div class="supplier-detail-page">
    <div class="supplier-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <span class="breadcrumb-item"><a href="/vnmt/">Trang chủ</a></span>
            <span class="breadcrumb-item"><a href="/vnmt/suppliers.php">Nhà cung cấp</a></span>
            <span class="breadcrumb-item active"><?php echo htmlspecialchars($supplier['name']); ?></span>
        </nav>

        <!-- Supplier Header -->
        <div class="supplier-header">
            <div class="supplier-header-content">
                <img src="<?php echo $supplier['logo'] ? '/' . $supplier['logo'] : '/vnmt/assets/images/default-supplier.svg'; ?>" 
                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                     class="supplier-logo">
                
                <div class="supplier-info">
                    <h1><?php echo htmlspecialchars($supplier['name']); ?></h1>
                    
                    <?php if ($supplier['category_name']): ?>
                        <div class="supplier-category">
                            <i class="<?php echo $supplier['category_icon'] ?? 'fas fa-building'; ?>"></i>
                            <?php echo htmlspecialchars($supplier['category_name']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="supplier-meta">
                        <?php if ($supplier['location']): ?>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($supplier['location']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($supplier['established_year']): ?>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                Thành lập <?php echo $supplier['established_year']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($supplier['employees_count']): ?>
                            <div class="meta-item">
                                <i class="fas fa-users"></i>
                                <?php echo $supplier['employees_count']; ?> nhân viên
                            </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="fas fa-eye"></i>
                            <?php echo number_format($supplier['views_count'] ?? 0); ?> lượt xem
                        </div>
                        
                        <?php if ($supplier['is_verified']): ?>
                            <div class="meta-item">
                                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                Đã xác minh
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <p class="supplier-description">
                        <?php echo nl2br(htmlspecialchars($supplier['description'])); ?>
                    </p>
                    
                    <div class="supplier-actions">
                        <?php if ($supplier['phone']): ?>
                            <a href="tel:<?php echo $supplier['phone']; ?>" class="action-btn primary">
                                <i class="fas fa-phone"></i> Gọi ngay
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($supplier['email']): ?>
                            <a href="mailto:<?php echo $supplier['email']; ?>" class="action-btn secondary">
                                <i class="fas fa-envelope"></i> Gửi email
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($supplier['website']): ?>
                            <a href="<?php echo $supplier['website']; ?>" target="_blank" class="action-btn secondary">
                                <i class="fas fa-globe"></i> Website
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-content">
                <?php if ($supplier['specialties']): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-star"></i> Chuyên môn</h2>
                        <p><?php echo nl2br(htmlspecialchars($supplier['specialties'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($services)): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-cogs"></i> Dịch vụ</h2>
                        <div class="services-list">
                            <?php foreach ($services as $service): ?>
                                <span class="service-tag"><?php echo htmlspecialchars($service); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($deliveryAreas)): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-truck"></i> Khu vực phục vụ</h2>
                        <div class="services-list">
                            <?php foreach ($deliveryAreas as $area): ?>
                                <span class="service-tag"><?php echo htmlspecialchars($area); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($certifications)): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-certificate"></i> Chứng nhận</h2>
                        <div class="services-list">
                            <?php foreach ($certifications as $cert): ?>
                                <span class="service-tag"><?php echo htmlspecialchars($cert); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Contact Information -->
                <div class="info-card">
                    <h3><i class="fas fa-address-card"></i> Thông tin liên hệ</h3>
                    
                    <?php if ($supplier['phone']): ?>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo $supplier['phone']; ?>">
                                <?php echo htmlspecialchars($supplier['phone']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($supplier['email']): ?>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo $supplier['email']; ?>">
                                <?php echo htmlspecialchars($supplier['email']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($supplier['website']): ?>
                        <div class="contact-item">
                            <i class="fas fa-globe"></i>
                            <a href="<?php echo $supplier['website']; ?>" target="_blank">
                                Website
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($supplier['address']): ?>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo nl2br(htmlspecialchars($supplier['address'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Stats -->
                <div class="info-card">
                    <h3><i class="fas fa-chart-bar"></i> Thống kê</h3>
                    
                    <div class="contact-item">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Tham gia: <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?></span>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-eye"></i>
                        <span><?php echo number_format($supplier['views_count'] ?? 0); ?> lượt xem</span>
                    </div>
                    
                    <?php if ($supplier['is_featured']): ?>
                        <div class="contact-item">
                            <i class="fas fa-star" style="color: #f59e0b;"></i>
                            <span>Nhà cung cấp nổi bật</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Supplier Products Section -->
        <?php if (!empty($supplierProducts)): ?>
            <div class="supplier-products">
                <h2><i class="fas fa-box"></i> Sản phẩm của <?php echo htmlspecialchars($supplier['name']); ?></h2>
                <div class="products-grid">
                    <?php foreach ($supplierProducts as $product): ?>
                        <div class="product-card">
                            <?php if ($product['primary_image']): ?>
                                <div class="product-image">
                                    <img src="/vnmt/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         loading="lazy">
                                    <?php if ($product['is_featured']): ?>
                                        <span class="featured-badge">
                                            <i class="fas fa-star"></i> Nổi bật
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="product-image product-no-image">
                                    <i class="fas fa-image"></i>
                                    <span>Chưa có hình ảnh</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-content">
                                <div class="product-meta">
                                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                                    <?php if ($product['subcategory']): ?>
                                        <span class="product-subcategory"><?php echo htmlspecialchars($product['subcategory']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                
                                <?php if ($product['short_description']): ?>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(substr($product['short_description'], 0, 120)) . '...'; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($product['price_range']): ?>
                                    <div class="product-price">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($product['price_range']); ?></span>
                                        <?php if ($product['unit']): ?>
                                            <small>/<?php echo htmlspecialchars($product['unit']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-stats">
                                    <span class="product-views">
                                        <i class="fas fa-eye"></i>
                                        <?php echo number_format($product['views_count'] ?? 0); ?> lượt xem
                                    </span>
                                    <span class="product-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($product['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <div class="product-actions">
                                    <button class="btn-contact-product" data-product="<?php echo htmlspecialchars($product['name']); ?>">
                                        <i class="fas fa-phone"></i> Liên hệ
                                    </button>
                                    <button class="btn-product-details" data-slug="<?php echo htmlspecialchars($product['slug']); ?>">
                                        <i class="fas fa-info-circle"></i> Chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($supplierProducts) >= 6): ?>
                    <div class="view-all-products">
                        <a href="/vnmt/products.php?supplier=<?php echo urlencode($supplier['slug']); ?>" class="btn-view-all">
                            <i class="fas fa-th-large"></i> Xem tất cả sản phẩm (<?php echo count($supplierProducts); ?>)
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-content">
                    <i class="fas fa-box-open"></i>
                    <h3>Chưa có sản phẩm</h3>
                    <p><?php echo htmlspecialchars($supplier['name']); ?> chưa đăng tải sản phẩm nào.</p>
                    <?php if ($supplier['phone'] || $supplier['email']): ?>
                        <p>Hãy liên hệ trực tiếp để biết thêm thông tin về sản phẩm và dịch vụ.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Suppliers -->
        <?php if (!empty($relatedSuppliers)): ?>
            <div class="related-suppliers">
                <h2><i class="fas fa-building"></i> Nhà cung cấp cùng danh mục</h2>
                <div class="related-grid">
                    <?php foreach ($relatedSuppliers as $related): ?>
                        <div class="related-card">
                            <h4><?php echo htmlspecialchars($related['name']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($related['description'] ?? '', 0, 100)) . '...'; ?></p>
                            <a href="/vnmt/supplier-detail.php?slug=<?php echo urlencode($related['slug']); ?>">
                                Xem chi tiết →
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle product contact buttons
    const contactButtons = document.querySelectorAll('.btn-contact-product');
    contactButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.dataset.product;
            const supplierPhone = '<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>';
            const supplierEmail = '<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>';
            
            if (supplierPhone) {
                const message = `Tôi quan tâm đến sản phẩm "${productName}" của công ty. Vui lòng tư vấn chi tiết.`;
                const whatsappUrl = `https://wa.me/${supplierPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');
            } else if (supplierEmail) {
                const subject = `Quan tâm sản phẩm: ${productName}`;
                const body = `Tôi quan tâm đến sản phẩm "${productName}" của công ty. Vui lòng tư vấn chi tiết.`;
                const mailtoUrl = `mailto:${supplierEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
                window.location.href = mailtoUrl;
            } else {
                alert('Thông tin liên hệ không có sẵn');
            }
        });
    });
    
    // Handle product details buttons
    const detailButtons = document.querySelectorAll('.btn-product-details');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productSlug = this.dataset.slug;
            // For now, show an alert. Later can navigate to product detail page
            alert(`Tính năng xem chi tiết sản phẩm "${productSlug}" sẽ được phát triển trong phiên bản tiếp theo.`);
            // Future: window.location.href = `/vnmt/product-detail.php?slug=${productSlug}`;
        });
    });
});
</script>

<?php include 'inc/footer-new.php'; ?>