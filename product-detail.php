<?php
require 'backend/inc/db.php';
$pdo = getPDO();

// Get product ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: products.php');
    exit;
}

// Get product details with supplier info
$stmt = $pdo->prepare("
    SELECT p.*, pc.name as category_name, s.name as supplier_name, s.email as supplier_email, 
           s.phone as supplier_phone, s.address as supplier_address, s.logo as supplier_logo,
           s.website as supplier_website, s.description as supplier_description
    FROM products p 
    LEFT JOIN product_categories pc ON p.category_id = pc.id 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Get related products (same category, different product)
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, p.featured_image 
    FROM products p 
    WHERE p.category_id = ? AND p.id != ? AND p.status = 1 
    ORDER BY RAND() 
    LIMIT 6
");
$stmt->execute([$product['category_id'], $id]);
$relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Parse classification for display
$classifications = !empty($product['classification']) ? explode(',', $product['classification']) : [];

// Format price
$formattedPrice = number_format($product['price'], 0, ',', '.') . ' đ';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']) ?> - VNMaterial</title>
    <link rel="stylesheet" href="assets/css/global-styles.css">
    <link rel="stylesheet" href="assets/css/product-detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="product-detail-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span>/</span>
            <a href="products.php">Sản phẩm</a>
            <span>/</span>
            <a href="products.php?category_id=<?php echo $product['category_id'] ?>"><?php echo htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?></a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']) ?></span>
        </nav>

        <!-- Product Header -->
        <header class="product-header">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']) ?></h1>
            <p class="product-subtitle">
                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?>
                <?php if ($product['brand']): ?>
                    • <i class="fas fa-copyright"></i> <?php echo htmlspecialchars($product['brand']) ?>
                <?php endif; ?>
                <?php if ($product['featured']): ?>
                    • <i class="fas fa-star" style="color: #f39c12;"></i> Sản phẩm nổi bật
                <?php endif; ?>
            </p>
        </header>

        <!-- Main Product Content -->
        <div class="product-main">
            <!-- Left: Product Image & Description -->
            <div class="product-content">
                <div class="product-image-section">
                    <?php if ($product['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($product['featured_image']) ?>" 
                             alt="<?php echo htmlspecialchars($product['name']) ?>" 
                             class="product-main-image">
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #bdc3c7; font-size: 4rem;">
                            <i class="fas fa-image"></i>
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

                <!-- Product Description Tab -->
                <div class="product-tabs">
                    <div class="tab-nav">
                        <button class="tab-button active" onclick="showTab('description')">
                            <i class="fas fa-info-circle"></i> Mô tả sản phẩm
                        </button>
                        <button class="tab-button" onclick="showTab('specifications')">
                            <i class="fas fa-list-ul"></i> Thông số kỹ thuật
                        </button>
                        <button class="tab-button" onclick="showTab('supplier')">
                            <i class="fas fa-building"></i> Nhà cung cấp
                        </button>
                    </div>

                    <div id="description" class="tab-content active">
                        <h3><i class="fas fa-info-circle"></i> Thông tin sản phẩm</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?: 'Đang cập nhật thông tin mô tả sản phẩm.')) ?></p>
                        
                        <?php if ($product['application']): ?>
                            <h4><i class="fas fa-tools"></i> Ứng dụng</h4>
                            <p><?php echo nl2br(htmlspecialchars($product['application'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($product['product_function']): ?>
                            <h4><i class="fas fa-cogs"></i> Chức năng</h4>
                            <p><?php echo nl2br(htmlspecialchars($product['product_function'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div id="specifications" class="tab-content">
                        <h3><i class="fas fa-list-ul"></i> Thông số kỹ thuật</h3>
                        <table class="specifications-table">
                            <thead>
                                <tr>
                                    <th>Thông số</th>
                                    <th>Giá trị</th>
                                </tr>
                            </thead>
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

                    <div id="supplier" class="tab-content">
                        <h3><i class="fas fa-building"></i> Thông tin nhà cung cấp</h3>
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                            <?php if ($product['supplier_logo']): ?>
                                <img src="<?php echo htmlspecialchars($product['supplier_logo']) ?>" 
                                     alt="<?php echo htmlspecialchars($product['supplier_name']) ?>" 
                                     class="supplier-logo">
                            <?php endif; ?>
                            <div>
                                <h4 class="supplier-name"><?php echo htmlspecialchars($product['supplier_name'] ?: 'Đang cập nhật') ?></h4>
                                <div class="supplier-contact">
                                    <?php if ($product['supplier_phone']): ?>
                                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($product['supplier_phone']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($product['supplier_email']): ?>
                                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($product['supplier_email']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($product['supplier_address']): ?>
                                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($product['supplier_address']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($product['supplier_description']): ?>
                            <p><?php echo nl2br(htmlspecialchars($product['supplier_description'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Product Info & Actions -->
            <div class="product-sidebar">
                <!-- Price & Actions -->
                <div class="product-info-card">
                    <h4><i class="fas fa-tag"></i> Giá sản phẩm</h4>
                    <div class="product-price"><?php echo $formattedPrice ?></div>
                    <p class="product-price-note">
                        <i class="fas fa-info-circle"></i> Giá có thể thay đổi theo thời gian. Vui lòng liên hệ để có báo giá chính xác.
                    </p>
                    
                    <div class="product-actions">
                        <a href="tel:<?php echo htmlspecialchars($product['supplier_phone'] ?: '0123456789') ?>" class="btn-primary-large">
                            <i class="fas fa-phone"></i> Liên hệ báo giá
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($product['supplier_email'] ?: 'info@vnmaterial.com') ?>?subject=Yêu cầu báo giá: <?php echo urlencode($product['name']) ?>" class="btn-secondary-large">
                            <i class="fas fa-envelope"></i> Gửi email
                        </a>
                        <?php if ($product['supplier_website']): ?>
                        <a href="<?php echo htmlspecialchars($product['supplier_website']) ?>" target="_blank" class="btn-secondary-large">
                            <i class="fas fa-external-link-alt"></i> Website nhà cung cấp
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-info-card">
                    <h4><i class="fas fa-clipboard-list"></i> Thông tin cơ bản</h4>
                    <ul class="product-specs">
                        <li>
                            <span class="spec-label">Mã sản phẩm:</span>
                            <span>#SP<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT) ?></span>
                        </li>
                        <li>
                            <span class="spec-label">Danh mục:</span>
                            <span><?php echo htmlspecialchars($product['category_name'] ?: 'Chưa phân loại') ?></span>
                        </li>
                        <?php if (!empty($classifications)): ?>
                        <li>
                            <span class="spec-label">Phân loại:</span>
                            <span><?php echo htmlspecialchars(implode(', ', $classifications)) ?></span>
                        </li>
                        <?php endif; ?>
                        <li>
                            <span class="spec-label">Trạng thái:</span>
                            <span style="color: <?php echo $product['status'] ? '#27ae60' : '#e74c3c' ?>;">
                                <i class="fas fa-<?php echo $product['status'] ? 'check-circle' : 'times-circle' ?>"></i>
                                <?php echo $product['status'] ? 'Có sẵn' : 'Hết hàng' ?>
                            </span>
                        </li>
                        <li>
                            <span class="spec-label">Cập nhật:</span>
                            <span><?php echo date('d/m/Y', strtotime($product['created_at'])) ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Supplier Info Card -->
                <div class="product-info-card supplier-info">
                    <h4><i class="fas fa-handshake"></i> Nhà cung cấp</h4>
                    <div style="text-align: center;">
                        <?php if ($product['supplier_logo']): ?>
                            <img src="<?php echo htmlspecialchars($product['supplier_logo']) ?>" 
                                 alt="<?php echo htmlspecialchars($product['supplier_name']) ?>" 
                                 class="supplier-logo">
                        <?php endif; ?>
                        <div class="supplier-name"><?php echo htmlspecialchars($product['supplier_name'] ?: 'Đang cập nhật') ?></div>
                        <div class="supplier-contact">
                            <?php if ($product['supplier_phone']): ?>
                                <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($product['supplier_phone']) ?></div>
                            <?php endif; ?>
                            <?php if ($product['supplier_email']): ?>
                                <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($product['supplier_email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <section class="related-products">
            <h3><i class="fas fa-th-large"></i> Sản phẩm cùng danh mục</h3>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $related): ?>
                <div class="related-item">
                    <a href="product-detail.php?id=<?php echo $related['id'] ?>">
                        <?php if ($related['featured_image']): ?>
                            <img src="<?php echo htmlspecialchars($related['featured_image']) ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']) ?>">
                        <?php else: ?>
                            <div style="height: 150px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <i class="fas fa-image" style="font-size: 3rem; color: #bdc3c7;"></i>
                            </div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($related['name']) ?></h4>
                        <div class="price"><?php echo number_format($related['price'], 0, ',', '.') ?> đ</div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-button');
            
            tabContents.forEach(content => content.classList.remove('active'));
            tabButtons.forEach(button => button.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>