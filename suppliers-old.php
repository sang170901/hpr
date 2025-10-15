<?php include 'inc/header-new.php'; ?>

<!-- Import Poppins font cho suppliers page -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Suppliers page CSS -->
<link rel="stylesheet" href="/vnmt/assets/css/suppliers-clean.css">

<?php
require_once 'backend/inc/db.php';

// Pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$letter = isset($_GET['letter']) ? trim($_GET['letter']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    $pdo = getPDO();
    
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

.suppliers-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    margin-bottom: 50px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.page-title {
    font-family: 'Poppins', sans-serif;
    font-size: 3.5rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
    letter-spacing: -0.02em;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.page-subtitle {
    font-size: 1.25rem;
    font-weight: 400;
    opacity: 0.95;
    text-align: center;
    margin-top: 16px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

.search-section {
    background: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 40px;
    border: 1px solid rgba(0,0,0,0.05);
}

.search-controls {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 24px;
}

.search-box {
    flex: 1;
    min-width: 280px;
}

.category-filter {
    min-width: 220px;
}

.search-input {
    width: 100%;
    padding: 14px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 400;
    transition: all 0.2s ease;
    background: #fafafa;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.category-select {
    width: 100%;
    padding: 14px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.2s ease;
}

.category-select:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.3);
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px 0 rgba(102, 126, 234, 0.4);
}

.alphabet-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.letter-btn {
    width: 44px;
    height: 44px;
    border: 2px solid #e2e8f0;
    background: white;
    color: #64748b;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
}

.letter-btn:hover,
.letter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.section-title {
    font-family: 'Poppins', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 50px 0 30px 0;
    padding-bottom: 12px;
    border-bottom: 3px solid;
    border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%) 1;
    letter-spacing: -0.01em;
}

.suppliers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 32px;
    margin-bottom: 50px;
}

.supplier-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0,0,0,0.05);
    position: relative;
}

.supplier-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.supplier-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 24px;
    text-align: center;
    position: relative;
}

.supplier-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    background: white;
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

.supplier-body {
    padding: 28px;
}

.supplier-category {
    background: var(--category-color, #667eea);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
    letter-spacing: 0.025em;
    text-transform: uppercase;
}

.supplier-category i {
    font-size: 0.7rem;
}

.supplier-name {
    font-family: 'Poppins', sans-serif;
    font-size: 1.375rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
    line-height: 1.3;
    letter-spacing: -0.01em;
}

.supplier-description {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 16px;
    font-size: 0.95rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.supplier-location {
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}

.supplier-location i {
    color: #ef4444;
    font-size: 0.8rem;
}

.supplier-footer {
    padding: 0 28px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-supplier {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    letter-spacing: 0.025em;
    box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.3);
}

.view-supplier:hover {
    color: white;
    transform: translateX(4px);
    box-shadow: 0 6px 20px 0 rgba(102, 126, 234, 0.4);
}

.supplier-date {
    font-size: 0.8rem;
    color: #9ca3af;
    font-weight: 500;
}

.no-results {
    text-align: center;
    padding: 80px 32px;
    background: white;
    border-radius: 20px;
    margin: 50px 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.no-results-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 24px;
}

.no-results h3 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 12px;
    font-weight: 600;
}

.no-results p {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 32px;
    line-height: 1.6;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    margin: 60px 0;
}

.page-link {
    padding: 12px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
    min-width: 44px;
    text-align: center;
}

.page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-1px);
}

.page-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}

.results-info {
    margin-bottom: 32px;
    color: #4b5563;
    font-weight: 500;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .suppliers-container {
        padding: 0 16px;
    }
    
    .page-title {
        font-size: 2.5rem;
    }
    
    .page-subtitle {
        font-size: 1.1rem;
    }
    
    .search-section {
        padding: 24px;
    }
    
    .search-controls {
        flex-direction: column;
        gap: 16px;
    }
    
    .search-box,
    .category-filter {
        min-width: auto;
        width: 100%;
    }
    
    .suppliers-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .alphabet-filter {
        gap: 6px;
    }
    
    .letter-btn {
        width: 40px;
        height: 40px;
        font-size: 13px;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .supplier-body {
        padding: 24px;
    }
    
    .supplier-footer {
        padding: 0 24px 24px;
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .view-supplier {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 2rem;
    }
    
    .search-section {
        padding: 20px;
        border-radius: 12px;
    }
    
    .supplier-card {
        border-radius: 16px;
    }
}
</style>
    font-size: 0.8rem;
    color: #6c757d;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    margin: 40px 0;
}

.no-results-icon {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 40px 0;
}

.page-link {
    padding: 8px 12px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    color: #495057;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #6f42c1;
    color: white;
    border-color: #6f42c1;
}

.page-link.active {
    background: #6f42c1;
    color: white;
    border-color: #6f42c1;
}

.results-info {
    margin-bottom: 20px;
    color: #495057;
    font-weight: 500;
}

@media (max-width: 768px) {
    .suppliers-container {
        padding: 0 15px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .search-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .search-box {
        min-width: auto;
        width: 100%;
    }
    
    .suppliers-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .alphabet-filter {
        gap: 4px;
    }
    
    .letter-btn {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
}
</style>

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
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 100)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 100): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết
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
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Default View: Featured, Random, Recent -->
            
            <!-- Debug Info (remove in production) -->
            <!-- <div style="background: #ffffcc; padding: 10px; margin: 10px 0; border: 1px solid #ccccaa;">
                Debug: Featured=<?php echo count($featuredSuppliers); ?>, Random=<?php echo count($randomSuppliers); ?>, Recent=<?php echo count($recentSuppliers); ?>
            </div> -->
            
            <!-- Featured Suppliers -->
            <h2 class="section-title">NHÀ CUNG CẤP NỔI BẬT</h2>
            <?php if (!empty($featuredSuppliers)): ?>
                <div class="suppliers-grid">
                    <?php foreach ($featuredSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 100)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 100): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Chưa có nhà cung cấp nổi bật</h3>
                    <p>Chúng tôi đang cập nhật danh sách nhà cung cấp nổi bật.</p>
                </div>
            <?php endif; ?>

            <!-- Random Suppliers -->
            <h2 class="section-title">NHÀ CUNG CẤP NGẪU NHIÊN</h2>
            <?php if (!empty($randomSuppliers)): ?>
                <div class="suppliers-grid">
                    <?php foreach ($randomSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 100)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 100): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-random"></i>
                    </div>
                    <h3>Chưa có nhà cung cấp khác</h3>
                    <p>Chúng tôi đang mở rộng mạng lưới nhà cung cấp.</p>
                </div>
            <?php endif; ?>

            <!-- Recent Suppliers -->
            <h2 class="section-title">ĐĂNG KÝ GẦN ĐÂY</h2>
            <?php if (!empty($recentSuppliers)): ?>
                <div class="suppliers-grid">
                    <?php foreach ($recentSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 100)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 100): ?>...<?php endif; ?>
                                </p>
                                <p class="supplier-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($supplier['location'] ?? 'Không rõ vị trí'); ?>
                                </p>
                            </div>
                            <div class="supplier-footer">
                                <a href="supplier.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                    Xem chi tiết
                                </a>
                                <span class="supplier-date">
                                    <?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Chưa có đăng ký mới</h3>
                    <p>Hãy trở lại sau để xem các nhà cung cấp mới.</p>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Call to Action -->
        <div style="text-align: center; margin: 60px 0; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 15px;">Bạn là nhà cung cấp vật liệu xây dựng?</h3>
            <p style="color: #6c757d; margin-bottom: 25px;">Hãy tham gia cùng chúng tôi để mở rộng thị trường và kết nối với nhiều khách hàng hơn.</p>
            <a href="/vnmt/backend/register-supplier.php" 
               style="background: linear-gradient(135deg, #6f42c1 0%, #5a67d8 100%); color: white; padding: 15px 30px; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; transition: transform 0.3s ease;">
                Đăng ký hợp tác ngay
            </a>
        </div>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>
