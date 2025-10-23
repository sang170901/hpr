<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
include 'inc/header-new.php'; 
?>

<?php
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
    $whereClause = "WHERE category = 'thiết bị' AND status = 1";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search OR brand LIKE :search)";
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
    
    // Get equipment with pagination
    $equipmentQuery = "SELECT * FROM products $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $equipmentStmt = $pdo->prepare($equipmentQuery);
    foreach ($params as $key => $value) {
        $equipmentStmt->bindValue($key, $value);
    }
    $equipmentStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $equipmentStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $equipmentStmt->execute();
    $equipment = $equipmentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get equipment categories for filter
    $categoriesQuery = "SELECT DISTINCT classification FROM products WHERE category = 'thiết bị' AND classification IS NOT NULL ORDER BY classification";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $equipment = [];
    $categories = [];
    $totalItems = 0;
    $totalPages = 0;
    error_log("Lỗi khi truy xuất thiết bị: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiết Bị Xây Dựng - VNMaterial</title>
    
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
        
        .materials-hero {
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            padding: 120px 0 80px;
            overflow: hidden;
            min-height: 420px;
        }
        
        .materials-hero::before {
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
            font-size: 3.5rem;
            font-weight: 800;
            color: #0284c7;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 8px rgba(56,189,248,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: #0ea5e9;
            max-width: 900px;
            margin: 0 auto 2.5rem;
            font-weight: 400;
        }
        
        .search-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin: -60px auto 0;
            max-width: 1000px;
            position: relative;
            z-index: 10;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            margin-bottom: 4rem;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 1rem 1.5rem;
            border: 2px solid #7dd3fc;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #38bdf8;
            background: white;
            box-shadow: 0 0 0 4px rgba(56,189,248,0.12);
        }
        
        .category-select {
            padding: 1rem 1.5rem;
            border: 2px solid #e0f2fe;
            border-radius: 12px;
            font-size: 1rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-select:focus {
            outline: none;
            border-color: #38bdf8;
            background: white;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
        }

        .search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.35);
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }
        
        .materials-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2.5rem;
            align-items: stretch;
        }
        
        .material-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            transition: all 0.28s ease;
            position: relative;
            border: 1px solid #f1f5f9;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .material-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .material-header {
            padding: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 160px;
            overflow: hidden;
        }
        
        .material-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .material-category {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 16px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
        }
        
        .material-body {
            padding: 0.85rem 0.85rem 0.75rem;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
        }
        
        .material-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.4rem;
            text-align: center;
            min-height: 2.3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .material-description {
            color: #64748b;
            margin-bottom: 0.65rem;
            line-height: 1.35;
            text-align: center;
            font-size: 0.82rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.2rem;
        }
        
        .material-details {
            margin-bottom: 0.6rem;
        }
        
        .material-detail {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #475569;
            font-size: 0.82rem;
            margin-bottom: 0.4rem;
        }
        
        .material-detail i {
            width: 14px;
            color: #38bdf8;
            font-size: 0.9rem;
        }
        
        .material-footer {
            padding: 0.65rem 0.85rem 0.85rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            border-top: 1px solid #f1f5f9;
        }
        
        .view-material {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.22s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
        }

        .view-material:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.35);
            text-decoration: none;
            color: white;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        .material-price {
            color: #059669;
            font-size: 0.85rem;
            font-weight: 700;
            display: none;
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
            .materials-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .materials-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 560px) {
            .materials-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="materials-hero">
        <div class="hero-content">
            <h1 class="hero-title">Thiết Bị Xây Dựng</h1>
            <p class="hero-subtitle">
                Cung cấp đầy đủ thiết bị xây dựng chuyên nghiệp từ máy móc, dụng cụ đến trang thiết bị an toàn - 
                Chất lượng đảm bảo, công nghệ hiện đại, hỗ trợ mọi công trình xây dựng
            </p>
        </div>
    </section>

    <!-- Search Section -->
    <div class="main-content">
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Tìm kiếm thiết bị (tên, mô tả, thương hiệu)..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category" class="category-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['classification']); ?>" 
                                <?php echo ($category == $cat['classification']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['classification']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>

        <!-- Results -->
        <?php if (!empty($search) || !empty($category)): ?>
            <div style="margin-bottom: 2rem; text-align: center;">
                <h2 style="color: #1e293b; margin-bottom: 0.5rem;">
                    Tìm thấy <?php echo $totalItems; ?> thiết bị
                </h2>
                <?php if ($search): ?>
                    <p style="color: #64748b;">Từ khóa: "<?php echo htmlspecialchars($search); ?>"</p>
                <?php endif; ?>
                <?php if ($category): ?>
                    <p style="color: #64748b;">Danh mục: <?php echo htmlspecialchars($category); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Equipment Grid -->
        <?php if (!empty($equipment)): ?>
            <div class="materials-grid">
                <?php foreach ($equipment as $item): ?>
                    <div class="material-card">
                        <?php if (!empty($item['classification'])): ?>
                            <div class="material-category">
                                <?php echo htmlspecialchars($item['classification']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="material-header">
                            <?php if (!empty($item['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="material-image">
                            <?php else: ?>
                                <div class="material-image" style="background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tools" style="color: white; font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="material-body">
                            <h3 class="material-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="material-description">
                                <?php echo htmlspecialchars(substr($item['description'] ?? 'Thiết bị xây dựng chuyên nghiệp', 0, 80)); ?>
                                <?php if (strlen($item['description'] ?? '') > 80): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="material-details">
                                <?php if ($item['brand']): ?>
                                    <div class="material-detail">
                                        <i class="fas fa-industry"></i>
                                        <span><?php echo htmlspecialchars($item['brand']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="material-footer">
                            <a href="product-detail.php?id=<?php echo $item['id']; ?>" class="view-material">
                                <span>Xem chi tiết</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php
                    $baseUrl = '?';
                    if ($search) $baseUrl .= 'search=' . urlencode($search) . '&';
                    if ($category) $baseUrl .= 'category=' . urlencode($category) . '&';
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
                <i class="fas fa-tools"></i>
                <h3>Không tìm thấy thiết bị</h3>
                <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc danh mục</p>
                <a href="equipment.php" style="color: #38bdf8; text-decoration: none; font-weight: 600; margin-top: 1rem; display: inline-block;">
                    ← Xem tất cả thiết bị
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include 'inc/footer-new.php'; ?>
