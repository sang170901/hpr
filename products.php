<?php
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
require_once 'inc/db_frontend.php';
include __DIR__ . '/inc/header-new.php';

// Pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Main categories for filtering
$mainCategories = [
    'vật liệu' => 'Vật liệu',
    'cảnh quan' => 'Cảnh quan',
    'công nghệ' => 'Công nghệ',
    'thiết bị' => 'Thiết bị'
];

try {
    $pdo = getFrontendPDO();
    
    // Build query with filters
    $whereClause = "WHERE status = 1";
    $params = [];
    
if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search OR brand LIKE :search OR manufacturer LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($categoryFilter)) {
        $whereClause .= " AND category = :category";
        $params[':category'] = $categoryFilter;
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM products $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get products with pagination (include English columns)
    $productsQuery = "SELECT *, name_en, description_en FROM products $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $productsStmt = $pdo->prepare($productsQuery);
    foreach ($params as $key => $value) {
        $productsStmt->bindValue($key, $value);
    }
    $productsStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $productsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $productsStmt->execute();
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get category counts for sidebar
    $categoryCountsQuery = "SELECT category, COUNT(*) as count FROM products WHERE status = 1 GROUP BY category";
    $categoryCounts = [];
    foreach ($pdo->query($categoryCountsQuery) as $row) {
        if (!empty($row['category'])) {
            $categoryCounts[$row['category']] = $row['count'];
        }
    }
    
} catch (Exception $e) {
    $products = [];
    $categoryCounts = [];
    $totalItems = 0;
    $totalPages = 0;
    error_log("Lỗi khi truy xuất sản phẩm: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả Sản phẩm - VNMaterial</title>
    
    <!-- Fonts -->
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
            background: #f0f9ff;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .products-hero {
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            padding: 120px 0 80px;
            overflow: hidden;
            min-height: 320px;
        }
        
        .products-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(56,189,248,0.15)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.4;
        }
        
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            color: #0284c7;
            margin-bottom: 1rem;
            text-shadow: 0 2px 8px rgba(56,189,248,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: #0ea5e9;
            max-width: 900px;
            margin: 0 auto;
            font-weight: 400;
        }
        
        .main-layout {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 30px;
            display: flex;
            gap: 2rem;
        }
        
        /* Sidebar Styles */
        .sidebar {
            flex: 0 0 280px;
    background: white;
            border-radius: 16px;
            padding: 1.5rem;
            height: fit-content;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            position: sticky;
            top: 100px;
}

.sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
    color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .sidebar-title i {
            color: #38bdf8;
        }
        
        .search-box {
            margin-bottom: 2rem;
}

.search-input {
    width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0f2fe;
            border-radius: 10px;
    font-size: 0.95rem;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #38bdf8;
            background: white;
            box-shadow: 0 0 0 4px rgba(56,189,248,0.12);
        }
        
        .category-filters {
            margin-bottom: 1.5rem;
        }
        
        .filter-title {
            font-size: 0.95rem;
    font-weight: 600;
            color: #475569;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
    cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #475569;
        }
        
        .category-item:hover {
            background: #f0f9ff;
            color: #0284c7;
        }
        
        .category-item.active {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
        }
        
        .category-item .category-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .category-count {
            background: rgba(0,0,0,0.1);
            color: inherit;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .filter-actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f1f5f9;
        }
        
        .clear-btn {
            width: 100%;
            padding: 0.75rem;
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .clear-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        
        /* Main Content */
        .main-content {
    flex: 1;
}

        .content-header {
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }
        
        .content-header h2 {
    color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
}

        .filter-info {
            color: #64748b;
            font-size: 0.95rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
}

.product-card {
    background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            transition: all 0.28s ease;
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            height: 100%;
    text-decoration: none;
    color: inherit;
}

.product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .product-header {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
}

.product-image {
    width: 100%;
            height: 100%;
    object-fit: cover;
            display: block;
        }
        
        .product-category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
        }
        
        .product-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-name {
    font-size: 1.1rem;
            font-weight: 700;
    color: #1e293b;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8rem;
        }
        
        .product-description {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            min-height: 2.5rem;
        }
        
        .product-meta {
            margin-top: auto;
}

.product-price {
            font-size: 1.25rem;
    font-weight: 700;
            color: #059669;
            margin-bottom: 1rem;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-top: 1px solid #f1f5f9;
        }
        
        .view-product {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
    font-size: 0.85rem;
            transition: all 0.22s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
        }
        
        .view-product:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.35);
            text-decoration: none;
            color: white;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 3rem 0;
        }
        
        .pagination a, .pagination span {
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
    font-weight: 600;
            transition: all 0.3s;
        }
        
        .pagination a {
            background: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .pagination a:hover {
            background: #38bdf8;
            color: white;
            border-color: #38bdf8;
        }
        
        .pagination .current {
            background: #38bdf8;
    color: white;
            border: 1px solid #38bdf8;
        }
        
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
            background: white;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }
        
        .no-results i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }
        
        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #475569;
        }
        
        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .main-layout {
        flex-direction: column;
    }
    
            .sidebar {
        flex: none;
                position: static;
            }
        }
        
        @media (max-width: 650px) {
            .main-layout {
                width: 99%;
                margin-left: auto;
                margin-right: auto;
                padding: 1.5rem 0;
            }
            
            .sidebar {
                padding: 0 26px;
                max-width: 650px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .main-content {
                max-width: 650px;
                margin-left: auto;
                margin-right: auto;
                padding: 0 10px;
            }
            
            .content-header {
                padding: 1.5rem 1rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .product-header {
                height: 180px;
            }
            
            .product-body {
                padding: 1rem;
            }
            
            .products-hero {
                padding: 80px 0 50px;
                min-height: 250px;
            }
            
            .hero-content {
                max-width: 650px;
                padding: 0 15px;
            }
            
            .hero-title {
                font-size: 1.5rem;
            }
            
            .hero-subtitle {
                font-size: 0.9rem;
    }
}
</style>
</head>
<body>

    <!-- Hero Section -->
    <section class="products-hero">
        <div class="hero-content">
            <h1 class="hero-title">Tất cả Sản phẩm</h1>
            <p class="hero-subtitle">
                Khám phá bộ sưu tập đầy đủ các sản phẩm từ vật liệu xây dựng, thiết bị công trình, 
                công nghệ thông minh đến cảnh quan và kiến trúc xanh
            </p>
        </div>
    </section>

    <!-- Main Layout with Sidebar -->
    <div class="main-layout">
    <!-- Left Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">
                <i class="fas fa-filter"></i>
                <?php echo t('products_filter'); ?>
            </h3>
            
            <!-- Search Box -->
            <div class="search-box">
                <form method="GET" action="products.php">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="<?php echo t('products_search_placeholder'); ?>" 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <?php if (!empty($categoryFilter)): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                    <?php endif; ?>
                    <button type="submit" style="display: none;"></button>
                </form>
            </div>
            
            <!-- Category Filters -->
            <div class="category-filters">
                <div class="filter-title"><?php echo t('products_category_title'); ?></div>
                
                <a href="products.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>" 
                   class="category-item <?php echo empty($categoryFilter) ? 'active' : ''; ?>">
                    <span class="category-name">
                        <i class="fas fa-th"></i>
                        <?php echo t('all_categories'); ?>
                    </span>
                    <span class="category-count"><?php echo $totalItems; ?></span>
                </a>
                
                <?php foreach ($mainCategories as $catKey => $catName): ?>
                    <?php 
                    $count = isset($categoryCounts[$catKey]) ? $categoryCounts[$catKey] : 0;
                    $isActive = ($categoryFilter === $catKey);
                    $icon = '';
                    switch($catKey) {
                        case 'vật liệu': $icon = 'fa-boxes'; break;
                        case 'thiết bị': $icon = 'fa-tools'; break;
                        case 'công nghệ': $icon = 'fa-microchip'; break;
                        case 'cảnh quan': $icon = 'fa-tree'; break;
                    }
                    ?>
                    <a href="products.php?category=<?php echo urlencode($catKey); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                       class="category-item <?php echo $isActive ? 'active' : ''; ?>">
                        <span class="category-name">
                            <i class="fas <?php echo $icon; ?>"></i>
                            <?php echo htmlspecialchars($catName); ?>
                        </span>
                        <span class="category-count"><?php echo $count; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Filter Actions -->
            <?php if (!empty($search) || !empty($categoryFilter)): ?>
            <div class="filter-actions">
                <a href="products.php" class="clear-btn">
                    <i class="fas fa-times-circle"></i> Xóa bộ lọc
                </a>
        </div>
            <?php endif; ?>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Content Header -->
            <div class="content-header">
                <h2>
                    <?php if (!empty($categoryFilter)): ?>
                        <?php echo htmlspecialchars($mainCategories[$categoryFilter] ?? 'Sản phẩm'); ?>
                    <?php else: ?>
                        Tất cả Sản phẩm
                    <?php endif; ?>
                </h2>
                <div class="filter-info">
                    Tìm thấy <strong><?php echo $totalItems; ?></strong> sản phẩm
                    <?php if (!empty($search)): ?>
                        cho từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
        </div>
    </div>
    
            <!-- Products Grid -->
            <?php if (!empty($products)): ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
                            <?php if (!empty($product['category'])): ?>
                                <div class="product-category-badge">
                                    <?php echo htmlspecialchars($mainCategories[$product['category']] ?? $product['category']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-header">
                                <?php if (!empty($product['featured_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars(getTranslatedName($product)); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box-open" style="color: #cbd5e1; font-size: 3rem; opacity: 0.5;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-body">
                                <h3 class="product-name"><?php echo htmlspecialchars(getTranslatedName($product)); ?></h3>
                                <p class="product-description">
                                    <?php 
                                    $desc = getTranslatedDescription($product) ?: t('high_quality_product');
                                    echo htmlspecialchars(substr($desc, 0, 100)); 
                                    if (strlen($desc) > 100) echo '...';
                                    ?>
                                </p>
                                
                                <div class="product-meta">
                                    <?php if (!empty($product['brand'])): ?>
                                        <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem;">
                                            <i class="fas fa-industry"></i>
                                            <?php echo htmlspecialchars($product['brand']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($product['price'])): ?>
                                        <div class="product-price">
                                            <?php echo number_format($product['price']); ?>đ
                                        </div>
                                    <?php endif; ?>
                                </div>
        </div>
        
                            <div class="product-footer">
                                <span class="view-product">
                                    Xem chi tiết
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                                <span style="color: #94a3b8; font-size: 0.8rem;">
                                    <?php echo date('d/m/Y', strtotime($product['created_at'])); ?>
                                </span>
                            </div>
            </a>
            <?php endforeach; ?>
        </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        $baseUrl = '?';
                        if ($search) $baseUrl .= 'search=' . urlencode($search) . '&';
                        if ($categoryFilter) $baseUrl .= 'category=' . urlencode($categoryFilter) . '&';
                        ?>
                        
                        <?php if ($page > 1): ?>
                            <a href="<?php echo $baseUrl; ?>page=<?php echo $page - 1; ?>">
                                <i class="fas fa-chevron-left"></i> Trước
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $baseUrl; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo $baseUrl; ?>page=<?php echo $page + 1; ?>">
                                Sau <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3><?php echo t('products_not_found'); ?></h3>
                    <p><?php echo t('products_not_found_hint'); ?></p>
                    <a href="products.php" style="color: #38bdf8; text-decoration: none; font-weight: 600; margin-top: 1rem; display: inline-block;">
                        ← <?php echo t('products_view_all'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

<?php include __DIR__ . '/inc/footer-new.php'; ?>
