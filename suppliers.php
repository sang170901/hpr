<?php include 'inc/header-new.php'; ?>

<!-- Import Poppins font cho suppliers page -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Suppliers page CSS -->
<link rel="stylesheet" href="/vnmt/assets/css/suppliers-clean.css?v=<?php echo time(); ?>">

<?php
require_once 'inc/db_frontend.php';

// Pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$letter = isset($_GET['letter']) ? trim($_GET['letter']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    $pdo = getFrontendPDO();
    
    // Get all categories for filter
    $categoriesQuery = "SELECT id, name, slug, icon, color FROM supplier_categories ORDER BY order_index ASC";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $allCategories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build query with filters
    $whereClause = "WHERE s.status = 1";
    $params = [];
    $joins = "FROM suppliers s LEFT JOIN supplier_categories sc ON s.category_id = sc.id";
    
    if (!empty($search)) {
        $whereClause .= " AND (s.name LIKE :search OR s.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($letter)) {
        $whereClause .= " AND s.name LIKE :letter";
        $params[':letter'] = $letter . "%";
    }
    
    if (!empty($category)) {
        $whereClause .= " AND sc.slug = :category";
        $params[':category'] = $category;
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total $joins $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get featured suppliers (top 12)
    $featuredQuery = "SELECT s.slug, s.logo, s.name, s.description, s.location, s.created_at, sc.name as category_name, sc.color as category_color FROM suppliers s LEFT JOIN supplier_categories sc ON s.category_id = sc.id WHERE s.status = 1 AND s.is_featured = 1 ORDER BY s.name ASC LIMIT 12";
    $featuredStmt = $pdo->query($featuredQuery);
    $featuredSuppliers = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get random suppliers (12 more)
    $randomQuery = "SELECT s.slug, s.logo, s.name, s.description, s.location, s.created_at, sc.name as category_name, sc.color as category_color FROM suppliers s LEFT JOIN supplier_categories sc ON s.category_id = sc.id WHERE s.status = 1 AND s.is_featured != 1 ORDER BY RANDOM() LIMIT 12";
    $randomStmt = $pdo->query($randomQuery);
    $randomSuppliers = $randomStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent suppliers (last 10)
    $recentQuery = "SELECT s.slug, s.logo, s.name, s.description, s.location, s.created_at, sc.name as category_name, sc.color as category_color FROM suppliers s LEFT JOIN supplier_categories sc ON s.category_id = sc.id WHERE s.status = 1 ORDER BY s.created_at DESC LIMIT 10";
    $recentStmt = $pdo->query($recentQuery);
    $recentSuppliers = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all suppliers with pagination for search results
    if (!empty($search) || !empty($letter) || !empty($category)) {
        $searchQuery = "SELECT s.slug, s.logo, s.name, s.description, s.location, s.created_at, sc.name as category_name, sc.color as category_color $joins $whereClause ORDER BY s.name ASC LIMIT :limit OFFSET :offset";
        $searchStmt = $pdo->prepare($searchQuery);
        foreach ($params as $key => $value) {
            $searchStmt->bindValue($key, $value);
        }
        $searchStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $searchStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $searchStmt->execute();
        $searchResults = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    $featuredSuppliers = [];
    $randomSuppliers = [];
    $recentSuppliers = [];
    $searchResults = [];
    $allCategories = [];
    $totalItems = 0;
    $totalPages = 0;
    error_log("Lỗi khi truy xuất nhà cung cấp: " . $e->getMessage());
}
?>

<div class="suppliers-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="suppliers-container">
            <h1 class="page-title">NHÀ CUNG CẤP</h1>
            <p class="page-subtitle">
                VNBuilding là nguồn kiến thức xây dựng đáng tin cậy, chuyên cung cấp thông tin chính xác và hiểu biết sâu sắc về vật liệu, 
                kỹ thuật và thực hành bền vững. Chúng tôi hỗ trợ cho các kiến trúc sư, kỹ sư và những người đam mê xây dựng có cái nhìn 
                toàn diện về thị trường vật liệu Việt Nam để đưa ra những quyết định sáng suốt nhằm nâng cao chất lượng dự án của họ.
            </p>
        </div>
    </div>

    <div class="suppliers-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <span class="breadcrumb-item"><a href="/vnmt/">Trang chủ</a></span>
            <span class="breadcrumb-item active">Nhà cung cấp</span>
        </nav>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="">
                <div class="search-controls">
                    <div class="search-box">
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Tìm kiếm nhà cung cấp..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="category-filter">
                        <select name="category" class="category-select">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>" 
                                        <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Tìm Kiếm
                    </button>
                </div>
                
                <!-- Alphabet Filter -->
                <div class="alphabet-filter">
                    <a href="suppliers.php" class="letter-btn <?php echo empty($letter) ? 'active' : ''; ?>">Tất cả</a>
                    <?php for ($i = ord('A'); $i <= ord('Z'); $i++): ?>
                        <?php $char = chr($i); ?>
                        <a href="?letter=<?php echo $char; ?>" 
                           class="letter-btn <?php echo $letter === $char ? 'active' : ''; ?>">
                            <?php echo $char; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </form>
        </div>

        <?php if (!empty($search) || !empty($letter) || !empty($category)): ?>
            <!-- Search Results -->
            <div class="results-info">
                Tìm thấy <strong><?php echo number_format($totalItems); ?></strong> nhà cung cấp
                <?php if (!empty($search)): ?>
                    cho từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
                <?php if (!empty($letter)): ?>
                    bắt đầu bằng chữ "<strong><?php echo htmlspecialchars($letter); ?></strong>"
                <?php endif; ?>
                <?php if (!empty($category)): ?>
                    trong danh mục "<strong><?php 
                        $selectedCat = array_filter($allCategories, function($cat) use ($category) {
                            return $cat['slug'] === $category;
                        });
                        if (!empty($selectedCat)) {
                            echo htmlspecialchars(reset($selectedCat)['name']);
                        }
                    ?></strong>"
                <?php endif; ?>
            </div>

            <?php if (!empty($searchResults)): ?>
                <div class="suppliers-grid">
                    <?php foreach ($searchResults as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <?php if ($supplier['category_name']): ?>
                                    <div class="supplier-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($supplier['category_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 150): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier-detail.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                                <span class="supplier-date">
                                    <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($letter) ? '&letter=' . urlencode($letter) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                               class="page-link">‹ Trước</a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($letter) ? '&letter=' . urlencode($letter) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($letter) ? '&letter=' . urlencode($letter) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                               class="page-link">Sau ›</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Không tìm thấy nhà cung cấp nào</h3>
                    <p>Hãy thử thay đổi từ khóa tìm kiếm hoặc chọn chữ cái khác.</p>
                    <a href="suppliers.php" class="view-supplier">Xem tất cả nhà cung cấp</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Default View: Featured, Random, Recent -->
            <?php if (!empty($featuredSuppliers)): ?>
                <h2 class="section-title"><i class="fas fa-star"></i> Nhà Cung Cấp Nổi Bật</h2>
                <div class="suppliers-grid">
                    <?php foreach ($featuredSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <?php if ($supplier['category_name']): ?>
                                    <div class="supplier-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($supplier['category_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 150): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier-detail.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                                <span class="supplier-date">
                                    <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($randomSuppliers)): ?>
                <h2 class="section-title"><i class="fas fa-random"></i> Có Thể Bạn Quan Tâm</h2>
                <div class="suppliers-grid">
                    <?php foreach ($randomSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <?php if ($supplier['category_name']): ?>
                                    <div class="supplier-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($supplier['category_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 150): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier-detail.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                                <span class="supplier-date">
                                    <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($recentSuppliers)): ?>
                <h2 class="section-title"><i class="fas fa-clock"></i> Mới Thêm Gần Đây</h2>
                <div class="suppliers-grid">
                    <?php foreach ($recentSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <?php if ($supplier['category_name']): ?>
                                    <div class="supplier-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($supplier['category_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 150): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier-detail.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                                <span class="supplier-date">
                                    <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Call to Action -->
        <div class="cta-section">
            <h3>Bạn là nhà cung cấp vật liệu xây dựng?</h3>
            <p>Hãy tham gia cùng chúng tôi để mở rộng thị trường và kết nối với nhiều khách hàng hơn.</p>
            <a href="/vnmt/supplier-register.php" class="view-supplier">
                Đăng ký hợp tác ngay
            </a>
        </div>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>
