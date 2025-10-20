<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
include 'inc/header-new.php'; 
?>

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

try {
    $pdo = getFrontendPDO();
    
    // Build simple query with current table structure
    $whereClause = "WHERE status = 1";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM suppliers $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get all suppliers with proper encoding
    $allSuppliersQuery = "SELECT id, slug, logo, name, description, address, email, phone, created_at, category FROM suppliers WHERE status = 1 ORDER BY name ASC";
    $allStmt = $pdo->query($allSuppliersQuery);
    $allSuppliers = $allStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set categories as product categories from products table
    $categoriesQuery = "SELECT DISTINCT category as name, category as slug FROM products WHERE category IS NOT NULL ORDER BY category";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $allCategories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $allSuppliers = [];
    $allCategories = [];
    $totalItems = 0;
    error_log("Lỗi khi truy xuất nhà cung cấp: " . $e->getMessage());
}
?>

<div class="suppliers-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="suppliers-container">
            <h1 class="page-title">NHÀ CUNG CẤP</h1>
            <p class="page-subtitle">
                Khám phá mạng lưới nhà cung cấp đáng tin cậy của chúng tôi, chuyên cung cấp vật liệu, thiết bị, 
                công nghệ và dịch vụ cảnh quan chất lượng cao cho ngành xây dựng.
            </p>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="suppliers-container">
            <div class="search-bar">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Tìm kiếm nhà cung cấp..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                        <button type="submit" class="search-btn">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <?php if (!empty($allCategories)): ?>
    <div class="categories-section">
        <div class="suppliers-container">
            <h3>Danh mục sản phẩm</h3>
            <div class="categories-grid">
                <?php foreach ($allCategories as $category): ?>
                    <a href="?category=<?php echo urlencode($category['slug']); ?>" class="category-card">
                        <i class="fas fa-cube"></i>
                        <span><?php echo htmlspecialchars($category['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Suppliers Grid -->
    <div class="suppliers-content">
        <div class="suppliers-container">
            
            <!-- Statistics -->
            <div class="stats-section">
                <div class="stat-item">
                    <i class="fas fa-building"></i>
                    <div>
                        <h4><?php echo count($allSuppliers); ?></h4>
                        <p>Nhà cung cấp</p>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-tags"></i>
                    <div>
                        <h4><?php echo count($allCategories); ?></h4>
                        <p>Danh mục</p>
                    </div>
                </div>
            </div>

            <?php if (!empty($search)): ?>
                <div class="search-results-header">
                    <h2>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search); ?>"</h2>
                    <p>Tìm thấy <?php echo count($allSuppliers); ?> nhà cung cấp</p>
                </div>
            <?php endif; ?>

            <!-- Suppliers Grid -->
            <div class="suppliers-grid">
                <?php if (!empty($allSuppliers)): ?>
                    <?php foreach ($allSuppliers as $supplier): ?>
                        <div class="supplier-card">
                            <div class="supplier-header">
                                <img src="<?php echo htmlspecialchars($supplier['logo'] ?: '/vnmt/assets/images/default-supplier.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                     class="supplier-logo">
                            </div>
                            <div class="supplier-body">
                                <?php if ($supplier['category']): ?>
                                    <div class="supplier-category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($supplier['category']); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                                <p class="supplier-description">
                                    <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                    <?php if (strlen($supplier['description'] ?? '') > 150): ?>...<?php endif; ?>
                                </p>
                                
                                <div class="supplier-contact">
                                    <?php if ($supplier['address']): ?>
                                    <p class="supplier-location">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo htmlspecialchars($supplier['address']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($supplier['phone']): ?>
                                    <p class="supplier-phone">
                                        <i class="fas fa-phone"></i> 
                                        <?php echo htmlspecialchars($supplier['phone']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
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
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>Không tìm thấy nhà cung cấp</h3>
                        <p>Thử lại với từ khóa khác hoặc <a href="suppliers.php">xem tất cả nhà cung cấp</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.suppliers-page {
    font-family: 'Poppins', sans-serif;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.page-subtitle {
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
    opacity: 0.9;
}

.search-section {
    padding: 40px 0;
    background: #f8f9fa;
}

.search-form {
    max-width: 600px;
    margin: 0 auto;
}

.search-input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 50px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.search-input-group i {
    position: absolute;
    left: 20px;
    color: #666;
}

.search-input {
    flex: 1;
    padding: 15px 20px 15px 50px;
    border: none;
    outline: none;
    font-size: 1rem;
}

.search-btn {
    background: #667eea;
    color: white;
    border: none;
    padding: 15px 30px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
}

.search-btn:hover {
    background: #5a6fd8;
}

.categories-section {
    padding: 40px 0;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.category-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-decoration: none;
    color: #333;
    transition: transform 0.3s, box-shadow 0.3s;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.stats-section {
    display: flex;
    gap: 30px;
    margin-bottom: 40px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-item i {
    font-size: 2rem;
    color: #667eea;
}

.stat-item h4 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.stat-item p {
    margin: 0;
    color: #666;
}

.suppliers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.supplier-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.supplier-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.supplier-header {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
}

.supplier-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border-radius: 10px;
}

.supplier-body {
    padding: 20px;
}

.supplier-category {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #667eea20;
    color: #667eea;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.supplier-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.supplier-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 15px;
}

.supplier-contact p {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 8px 0;
    color: #666;
    font-size: 0.9rem;
}

.supplier-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-supplier {
    background: #667eea;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s;
}

.view-supplier:hover {
    background: #5a6fd8;
}

.supplier-date {
    color: #999;
    font-size: 0.9rem;
}

.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-results i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #ddd;
}

.search-results-header {
    margin-bottom: 30px;
}

.search-results-header h2 {
    color: #333;
    margin-bottom: 5px;
}

.search-results-header p {
    color: #666;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .stats-section {
        flex-direction: column;
        gap: 15px;
    }
    
    .suppliers-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'inc/footer-new.php'; ?>