<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
include 'inc/header-new.php'; 
require_once 'inc/db_frontend.php';

// Pagination and filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    $pdo = getFrontendPDO();
    
    // Build query with filters  
    $whereClause = "WHERE category = 'vật liệu'";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($category)) {
        $whereClause .= " AND classification = :category";
        $params[':category'] = $category;
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM products $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get materials with pagination
    $materialsQuery = "SELECT * FROM products $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $materialsStmt = $pdo->prepare($materialsQuery);
    foreach ($params as $key => $value) {
        $materialsStmt->bindValue($key, $value);
    }
    $materialsStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $materialsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $materialsStmt->execute();
    $materials = $materialsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get material categories for filter
    $categoryQuery = "SELECT DISTINCT classification FROM products WHERE category = 'vật liệu' AND classification IS NOT NULL ORDER BY classification";
    $categoryStmt = $pdo->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $materials = [];
    $categories = [];
    $totalItems = 0;
    $totalPages = 0;
    error_log("Lỗi khi truy xuất vật liệu: " . $e->getMessage());
}
?>

<style>
    .materials-hero {
        /* make background span full viewport width */
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        width: 100vw;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 60px 0 40px;
        overflow: hidden;
        min-height: 200px; /* ensure visible hero area */
    }
    
    .materials-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(21,101,192,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
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
        font-size: 2.45rem;
        font-weight: 800;
        color: #1565c0;
        margin-bottom: 1.05rem;
        text-shadow: 0 2px 8px rgba(21,101,192,0.2);
    }
    
    .hero-subtitle {
        font-size: 0.91rem;
        color: #1976d2;
        max-width: 900px; /* allow wider subtitle */
        margin: 0 auto 1.75rem;
        font-weight: 400;
    }
    
    .hero-search {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    .search-form {
        display: flex;
        gap: 10px;
        background: white;
        border-radius: 50px;
        padding: 8px;
        box-shadow: 0 8px 32px rgba(21,101,192,0.15);
        backdrop-filter: blur(10px);
    }
    
    .search-input {
        flex: 1;
        border: none;
        padding: 15px 20px;
        border-radius: 50px;
        font-size: 1rem;
        background: transparent;
        outline: none;
    }
    
    .search-btn {
        background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(66,165,245,0.3);
    }
    
    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(66,165,245,0.4);
    }

    /* Main content container */
    .main-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Filter section */
    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .filter-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1565c0;
    }

    .filter-controls {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box, .category-filter {
        flex: 1;
        min-width: 200px;
    }

    .search-box input, .category-filter select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e3f2fd;
        border-radius: 12px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .search-box input:focus, .category-filter select:focus {
        border-color: #42a5f5;
        outline: none;
    }

    .filter-btn {
        background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: transform 0.2s;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
    }

    /* Results header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .results-count {
        font-size: 1.1rem;
        color: #64748b;
    }

    /* Materials grid */
    .materials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .material-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .material-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .material-header {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .material-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .material-card:hover .material-image {
        transform: scale(1.05);
    }

    .material-category {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(66, 165, 245, 0.9);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .material-body {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .material-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
        line-height: 1.4;
        min-height: 3.3rem; /* Fixed height for 2 lines */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .material-description {
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 20px;
        font-size: 0.95rem;
        min-height: 4.8rem; /* Fixed height for 3 lines */
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .material-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
        min-height: 2.5rem; /* Fixed height for details */
    }

    .material-details > div {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #64748b;
    }

    .material-details i {
        color: #42a5f5;
        width: 16px;
    }

    .price {
        font-weight: 600;
        color: #059669;
    }

    .material-actions {
        display: flex;
        gap: 10px;
        margin-top: auto; /* Push buttons to bottom */
    }

    .view-btn, .supplier-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .view-btn {
        background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
        color: white;
    }

    .supplier-btn {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66,165,245,0.3);
    }

    .supplier-btn:hover {
        background: #f1f5f9;
        color: #42a5f5;
    }

    /* No results */
    .no-results {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        margin: 40px 0;
    }

    .no-results i {
        font-size: 4rem;
        color: #e2e8f0;
        margin-bottom: 20px;
    }

    .no-results h3 {
        font-size: 1.5rem;
        color: #64748b;
        margin-bottom: 10px;
    }

    .no-results p {
        color: #94a3b8;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 50px;
    }

    .page-link {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        color: #64748b;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.2s;
        font-weight: 500;
    }

    .page-link:hover, .page-link.active {
        background: #42a5f5;
        color: white;
        border-color: #42a5f5;
    }

    /* Responsive */
        @media (max-width: 768px) {
        .hero-title {
            font-size: 1.75rem;
        }        .search-form {
            flex-direction: column;
            padding: 15px;
        }
        
        .filter-controls {
            flex-direction: column;
        }
        
        .materials-grid {
            grid-template-columns: 1fr;
        }
        
        .material-details {
            grid-template-columns: 1fr;
        }
        
        .material-actions {
            flex-direction: column;
        }
    }
</style>

<!-- Hero Section -->
<section class="materials-hero">
    <div class="hero-content">
        <h1 class="hero-title">VẬT LIỆU XÂY DỰNG</h1>
        <p class="hero-subtitle">
            Vật liệu xây dựng bao gồm gỗ, thép, bê tông, gạch, đá và các vật liệu khác được sử dụng trong xây dựng.<br>
            Chúng được sử dụng để xây dựng móng, tường, mái, sàn và các kết cấu quan trọng khác của công trình.
        </p>
    </div>
</section>

<!-- Main Content -->
<div class="main-content">
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
                           placeholder="Tìm kiếm vật liệu..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="category-filter">
                    <select name="category">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['classification']); ?>" 
                                    <?php echo $category === $cat['classification'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['classification']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="filter-btn">
                    <i class="fas fa-search"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Results Header -->
    <div class="results-header">
        <div class="results-count">
            Tìm thấy <?php echo number_format($totalItems); ?> vật liệu
            <?php if (!empty($search) || !empty($category)): ?>
                <span style="color: #42a5f5; font-weight: 600;">
                    (<?php echo !empty($search) ? "tìm kiếm: '$search'" : ''; ?>
                    <?php echo !empty($category) ? "danh mục: '$category'" : ''; ?>)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($materials)): ?>
        <div class="no-results">
            <i class="fas fa-tools"></i>
            <h3>Không tìm thấy vật liệu nào</h3>
            <p>Hãy thử thay đổi từ khóa tìm kiếm hoặc danh mục để có kết quả tốt hơn.</p>
        </div>
    <?php else: ?>
        <div class="materials-grid">
            <?php foreach ($materials as $material): ?>
                <div class="material-card">
                    <div class="material-header">
                        <?php if (!empty($material['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($material['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($material['name']); ?>" 
                                 class="material-image">
                        <?php else: ?>
                            <div class="material-image" style="background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tools" style="color: white; font-size: 1.5rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($material['classification'])): ?>
                            <div class="material-category">
                                <?php echo htmlspecialchars($material['classification']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="material-body">
                        <h3 class="material-name"><?php echo htmlspecialchars($material['name']); ?></h3>
                        
                        <?php if (!empty($material['description'])): ?>
                            <p class="material-description">
                                <?php echo htmlspecialchars(mb_substr($material['description'], 0, 120)) . (mb_strlen($material['description']) > 120 ? '...' : ''); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="material-details">
                            <?php if (!empty($material['price'])): ?>
                                <div class="price">
                                    <i class="fas fa-tag"></i>
                                    <?php echo number_format($material['price']); ?>đ
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($material['brand'])): ?>
                                <div class="brand">
                                    <i class="fas fa-industry"></i>
                                    <?php echo htmlspecialchars($material['brand']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="material-actions">
                            <a href="product.php?id=<?php echo $material['id']; ?>" class="view-btn">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <?php if (!empty($material['supplier_id'])): ?>
                                <a href="suppliers.php?id=<?php echo $material['supplier_id']; ?>" class="supplier-btn">
                                    <i class="fas fa-handshake"></i> Nhà cung cấp
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
</div>

<?php include 'inc/footer-new.php'; ?>