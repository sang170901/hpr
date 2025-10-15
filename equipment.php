<?php 
include 'inc/header-new.php'; 
require_once 'backend/inc/db.php';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    $pdo = getPDO();
    
    // Build query with filters
    $whereClause = "WHERE category = 'thiết bị'";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($category)) {
        $whereClause .= " AND subcategory = :category";
        $params[':category'] = $category;
    }
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM products $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get products with pagination
    $query = "SELECT * FROM products $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get equipment categories for filter
    $categoryQuery = "SELECT DISTINCT subcategory FROM products WHERE category = 'thiết bị' AND subcategory IS NOT NULL ORDER BY subcategory";
    $categoryStmt = $pdo->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $equipment = [];
    $categories = [];
    $totalItems = 0;
    $totalPages = 0;
    error_log("Lỗi khi truy xuất thiết bị: " . $e->getMessage());
}
?>

<style>
.equipment-page {
    min-height: 100vh;
    background: #f8f9fa;
    padding-top: 100px;
}

.equipment-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    text-align: center;
    margin-top: 10px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 30px;
}

.breadcrumb-item {
    color: #6c757d;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
    margin: 0 10px;
}

.breadcrumb-item.active {
    color: #495057;
    font-weight: 500;
}

.filter-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.filter-header {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.filter-controls {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.search-box {
    flex: 1;
    min-width: 250px;
}

.search-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
}

.category-filter {
    min-width: 200px;
}

.category-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    background: white;
    cursor: pointer;
}

.filter-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.filter-btn:hover {
    transform: translateY(-2px);
}

.results-info {
    margin-bottom: 30px;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.results-count {
    font-size: 1.1rem;
    color: #495057;
}

.equipment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.equipment-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.equipment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.card-image {
    height: 200px;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #6c757d;
    position: relative;
    overflow: hidden;
}

.card-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f8f9fa"/><circle cx="50" cy="50" r="20" fill="%23dee2e6"/></svg>');
    opacity: 0.3;
}

.card-content {
    padding: 25px;
}

.card-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.4;
}

.card-description {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.card-category {
    background: #e3f2fd;
    color: #1976d2;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.card-supplier {
    font-size: 0.9rem;
    color: #495057;
    font-weight: 500;
}

.card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.tag {
    background: #f8f9fa;
    color: #495057;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    border: 1px solid #e9ecef;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-details {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.view-details:hover {
    color: white;
    transform: translateX(5px);
}

.no-results {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 15px;
    margin: 40px 0;
}

.no-results-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.no-results-title {
    font-size: 1.5rem;
    color: #495057;
    margin-bottom: 10px;
}

.no-results-text {
    color: #6c757d;
    margin-bottom: 30px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 50px 0;
}

.page-link {
    padding: 10px 15px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    color: #495057;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.page-link.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.page-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}

@media (max-width: 768px) {
    .equipment-container {
        padding: 0 15px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .filter-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .search-box,
    .category-filter {
        min-width: auto;
        width: 100%;
    }
    
    .equipment-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .card-content {
        padding: 20px;
    }
    
    .pagination {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .page-link {
        padding: 8px 12px;
        font-size: 14px;
    }
}
</style>

<div class="equipment-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="equipment-container">
            <h1 class="page-title">THIẾT BỊ</h1>
            <p class="page-subtitle">
                Thiết bị xây dựng bao gồm máy móc, công cụ và dụng cụ chuyên dụng.
                Chúng được sử dụng để xây dựng, lắp đặt, vận hành và bảo trì các công trình.
                Thiết bị xây dựng không chỉ tăng hiệu quả thi công mà còn đảm bảo an toàn và chất lượng công trình.
            </p>
        </div>
    </div>

    <div class="equipment-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <span class="breadcrumb-item"><a href="/vnmt/">Trang chủ</a></span>
            <span class="breadcrumb-item active">Thiết bị</span>
        </nav>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <i class="fas fa-filter"></i> Bộ lọc tìm kiếm
            </div>
            <form method="GET" action="">
                <div class="filter-controls">
                    <div class="search-box">
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Tìm kiếm thiết bị..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="category-filter">
                        <select name="category" class="category-select">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['subcategory']); ?>" 
                                        <?php echo $category === $cat['subcategory'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['subcategory']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            <div class="results-count">
                Tìm thấy <strong><?php echo number_format($totalItems); ?></strong> thiết bị
                <?php if (!empty($search) || !empty($category)): ?>
                    <?php if (!empty($search)): ?>
                        cho từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
                    <?php if (!empty($category)): ?>
                        trong danh mục "<strong><?php echo htmlspecialchars($category); ?></strong>"
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($equipment)): ?>
            <!-- Equipment Grid -->
            <div class="equipment-grid">
                <?php foreach ($equipment as $item): ?>
                    <div class="equipment-card">
                        <div class="card-image">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                            
                            <div class="card-meta">
                                <?php if (!empty($item['subcategory'])): ?>
                                    <span class="card-category"><?php echo htmlspecialchars($item['subcategory']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['supplier_name'])): ?>
                                    <span class="card-supplier"><?php echo htmlspecialchars($item['supplier_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="card-description">
                                <?php echo htmlspecialchars(substr($item['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                <?php if (strlen($item['description'] ?? '') > 150): ?>...<?php endif; ?>
                            </p>
                            
                            <?php if (!empty($item['tags'])): ?>
                                <div class="card-tags">
                                    <?php 
                                    $tags = explode(',', $item['tags']);
                                    foreach (array_slice($tags, 0, 3) as $tag): 
                                    ?>
                                        <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-footer">
                                <a href="product.php?id=<?php echo $item['id']; ?>" class="view-details">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                           class="page-link">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                           class="page-link">
                            Sau <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- No Results -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="no-results-title">Không tìm thấy thiết bị nào</h3>
                <p class="no-results-text">
                    Hãy thử thay đổi từ khóa tìm kiếm hoặc bộ lọc để tìm thấy thiết bị phù hợp.
                </p>
                <a href="equipment.php" class="view-details">
                    <i class="fas fa-refresh"></i> Xem tất cả thiết bị
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>