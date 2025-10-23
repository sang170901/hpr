<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
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
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vật Liệu - VNMaterial</title>
    
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 4rem 0;
            text-align: center;
            color: #1565c0;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(21, 101, 192, 0.1);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        /* Search Form */
        .search-form {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .search-input {
            padding: 0.75rem 1rem;
            border: 2px solid #e3f2fd;
            border-radius: 25px;
            width: 300px;
            font-size: 1rem;
        }

        .search-btn {
            background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 165, 245, 0.3);
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-header {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 1rem;
        }

        .filter-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .category-select {
            padding: 0.5rem;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            background: white;
        }

        .search-btn-small {
            background: #42a5f5;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Results Info */
        .results-info {
            margin-bottom: 2rem;
        }

        .results-count {
            font-size: 1.1rem;
            color: #64748b;
        }

        /* Materials Grid */
        .materials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .material-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            position: relative;
        }
        
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .fallback-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: #6c757d;
            font-size: 3rem;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 0.75rem;
        }

        .card-meta {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .card-category {
            background: #e3f2fd;
            color: #1565c0;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .card-supplier {
            background: #f1f5f9;
            color: #64748b;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .card-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .card-price {
            margin-bottom: 1rem;
        }

        .price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #42a5f5;
        }

        .card-footer {
            margin-top: 1rem;
        }

        .view-details {
            background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s;
            display: inline-block;
        }

        .view-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 165, 245, 0.3);
        }

        /* No Results */
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

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .page-link {
            padding: 0.75rem 1rem;
            border: 1px solid #e3f2fd;
            color: #42a5f5;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .page-link:hover,
        .page-link.active {
            background: #42a5f5;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .search-form {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1 class="hero-title">VẬT LIỆU XÂY DỰNG</h1>
            <p class="hero-subtitle">Vật liệu xây dựng bao gồm gỗ, thép, bê tông, gạch, đá và các vật liệu khác được sử dụng trong xây dựng.</p>
            <p class="hero-subtitle">Chúng được sử dụng để xây dựng móng, tường, mái, sàn và các kết cấu quan trọng khác của công trình.</p>
            
            <form class="search-form" method="GET" action="">
                <input type="text" name="search" class="search-input" 
                       placeholder="Tìm kiếm vật liệu..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </section>

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
                               placeholder="Tìm kiếm vật liệu..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="category-filter">
                        <select name="category" class="category-select">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['classification']); ?>" 
                                        <?php echo $category === $cat['classification'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['classification']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-btn-small">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            <div class="results-count">
                Tìm thấy <strong><?php echo number_format($totalItems); ?></strong> vật liệu
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

        <?php if (empty($materials)): ?>
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="no-results-title">Không tìm thấy vật liệu nào</div>
                <div class="no-results-text">
                    Hãy thử thay đổi từ khóa tìm kiếm hoặc danh mục để có kết quả tốt hơn.
                </div>
            </div>
        <?php else: ?>
            <!-- Materials Grid -->
            <div class="materials-grid">
                <?php foreach ($materials as $item): ?>
                    <div class="material-card">
                        <div class="card-image">
                            <?php if (!empty($item['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="fallback-icon" style="display: none;">
                                    <i class="fas fa-cube"></i>
                                </div>
                            <?php else: ?>
                                <div class="fallback-icon">
                                    <i class="fas fa-cube"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                            
                            <div class="card-meta">
                                <?php if (!empty($item['classification'])): ?>
                                    <span class="card-category"><?php echo htmlspecialchars($item['classification']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['brand'])): ?>
                                    <span class="card-supplier"><?php echo htmlspecialchars($item['brand']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="card-description">
                                <?php echo htmlspecialchars(substr($item['description'] ?? 'Không có mô tả', 0, 150)); ?>
                                <?php if (strlen($item['description'] ?? '') > 150): ?>...<?php endif; ?>
                            </p>
                            
                            <?php if (!empty($item['price'])): ?>
                                <div class="card-price">
                                    <span class="price"><?php echo number_format($item['price']); ?>đ</span>
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
</body>
</html>