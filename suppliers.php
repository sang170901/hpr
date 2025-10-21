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
    $whereClause = "WHERE status = 1";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($category)) {
        $whereClause .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM suppliers $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get suppliers with pagination
    $suppliersQuery = "SELECT * FROM suppliers $whereClause ORDER BY name ASC LIMIT :limit OFFSET :offset";
    $suppliersStmt = $pdo->prepare($suppliersQuery);
    foreach ($params as $key => $value) {
        $suppliersStmt->bindValue($key, $value);
    }
    $suppliersStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $suppliersStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $suppliersStmt->execute();
    $suppliers = $suppliersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories
    $categoriesQuery = "SELECT DISTINCT category as name FROM suppliers WHERE category IS NOT NULL AND status = 1 ORDER BY category";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get stats
    $statsQuery = "SELECT 
        COUNT(*) as total_suppliers,
        COUNT(DISTINCT category) as total_categories,
        AVG(DATEDIFF(NOW(), created_at)) as avg_days
        FROM suppliers WHERE status = 1";
    $statsStmt = $pdo->query($statsQuery);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $suppliers = [];
    $categories = [];
    $totalItems = 0;
    $totalPages = 0;
    $stats = ['total_suppliers' => 0, 'total_categories' => 0, 'avg_days' => 0];
    error_log("Lỗi khi truy xuất nhà cung cấp: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà Cung Cấp - VNMaterial</title>
    
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
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .suppliers-hero {
            /* make background span full viewport width */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            background: linear-gradient(135deg, #60a5fa 0%, #7dd3fc 100%); /* light blue gradient */
            padding: 120px 0 80px;
            overflow: hidden;
            min-height: 420px; /* ensure visible hero area */
        }
        
        .suppliers-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
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
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
            max-width: 900px; /* allow wider subtitle */
            margin: 0 auto 2.5rem;
            font-weight: 400;
        }
        
        /* hero stats removed as requested */
        
        .search-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin: -60px auto 0;
            max-width: 1000px; /* wider search box */
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
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #60a5fa; /* light blue */
            background: white;
            box-shadow: 0 0 0 4px rgba(96,165,250,0.12);
        }
        
        .category-select {
            padding: 1rem 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-select:focus {
            outline: none;
            border-color: #60a5fa; /* light blue */
            background: white;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #60a5fa 0%, #7dd3fc 100%); /* light blue gradient */
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(96,165,250,0.22);
        }
        
        .main-content {
            max-width: 1400px; /* wider page content */
            margin: 0 auto;
            padding: 0 30px;
        }
        
        .suppliers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 cards per row on desktop */
            gap: 1.25rem;
            margin-bottom: 2.5rem;
            align-items: stretch; /* ensure grid items stretch to same height */
        }
        
        .supplier-card {
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
            height: 100%; /* allow equal height via grid align-items */
        }
        
        .supplier-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .supplier-header {
            padding: 1.25rem 1rem 0.75rem;
            background: linear-gradient(135deg, #f8fafc 0%, #eef8ff 100%);
            position: relative;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        
        .supplier-logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: cover;
            display: block;
            border: 3px solid white;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        
        .supplier-category {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #60a5fa 0%, #7dd3fc 100%); /* light blue */
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .supplier-body {
            padding: 1rem 1rem 1rem;
            flex: 1 1 auto; /* allow body to grow and push footer down */
            display: flex;
            flex-direction: column;
        }
        
        .supplier-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.35rem;
            text-align: center;
        }
        
        .supplier-description {
            color: #64748b;
            margin-bottom: 0.9rem;
            line-height: 1.4;
            text-align: center;
            font-size: 0.88rem;
            /* clamp description to two lines and show ellipsis */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .supplier-details {
            space-y: 0.8rem;
        }
        
        .supplier-detail {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #475569;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }
        
        .supplier-detail i {
            width: 16px;
            color: #60a5fa; /* light blue */
            font-size: 1rem;
        }
        
        .supplier-footer {
            padding: 0.75rem 1rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        
        .view-supplier {
            background: linear-gradient(135deg, #60a5fa 0%, #7dd3fc 100%); /* light blue */
            color: white;
            padding: 0.55rem 0.9rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.82rem;
            transition: all 0.22s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .view-supplier:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(96,165,250,0.22);
            text-decoration: none;
            color: white;
        }
        
        .supplier-date {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 500;
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
            background: #60a5fa; /* light blue */
            color: white;
            border-color: #60a5fa;
        }
        
        .pagination .current {
            background: #60a5fa; /* light blue */
            color: white;
            border: 1px solid #60a5fa;
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
            .suppliers-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .suppliers-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 560px) {
            .suppliers-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 2rem;
            }

            .supplier-logo {
                width: 64px;
                height: 64px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="suppliers-hero">
        <div class="hero-content">
            <h1 class="hero-title">Nhà Cung Cấp Uy Tín</h1>
            <p class="hero-subtitle">
                Khám phá mạng lưới đối tác tin cậy với các nhà cung cấp hàng đầu về vật liệu xây dựng, 
                thiết bị công nghiệp và giải pháp công nghệ tiên tiến
            </p>
            <!-- hero stats removed -->
        </div>
    </section>

    <!-- Search Section -->
    <div class="main-content">
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Tìm kiếm nhà cung cấp..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category" class="category-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                <?php echo ($category === $cat['name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucfirst($cat['name'])); ?>
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
                    Tìm thấy <?php echo $totalItems; ?> nhà cung cấp
                </h2>
                <?php if ($search): ?>
                    <p style="color: #64748b;">Từ khóa: "<?php echo htmlspecialchars($search); ?>"</p>
                <?php endif; ?>
                <?php if ($category): ?>
                    <p style="color: #64748b;">Danh mục: <?php echo htmlspecialchars($category); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Suppliers Grid -->
        <?php if (!empty($suppliers)): ?>
            <div class="suppliers-grid">
                <?php foreach ($suppliers as $supplier): ?>
                    <div class="supplier-card">
                        <?php if ($supplier['category']): ?>
                            <div class="supplier-category">
                                <?php echo htmlspecialchars($supplier['category']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="supplier-header">
                            <img src="<?php echo htmlspecialchars($supplier['logo'] ?: 'https://via.placeholder.com/80x80/667eea/ffffff?text=' . urlencode(substr($supplier['name'], 0, 2))); ?>" 
                                 alt="<?php echo htmlspecialchars($supplier['name']); ?>" 
                                 class="supplier-logo">
                        </div>
                        
                        <div class="supplier-body">
                            <h3 class="supplier-name"><?php echo htmlspecialchars($supplier['name']); ?></h3>
                            <p class="supplier-description">
                                <?php echo htmlspecialchars(substr($supplier['description'] ?? 'Nhà cung cấp uy tín với nhiều năm kinh nghiệm', 0, 120)); ?>
                                <?php if (strlen($supplier['description'] ?? '') > 120): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="supplier-details">
                                <?php if ($supplier['address']): ?>
                                    <div class="supplier-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($supplier['address']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($supplier['phone']): ?>
                                    <div class="supplier-detail">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($supplier['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($supplier['email']): ?>
                                    <div class="supplier-detail">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($supplier['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="supplier-footer">
                            <a href="supplier-detail.php?slug=<?php echo urlencode($supplier['slug']); ?>" class="view-supplier">
                                <span>Xem chi tiết</span>
                                <i class="fas fa-arrow-right"></i>
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
                <i class="fas fa-building"></i>
                <h3>Không tìm thấy nhà cung cấp</h3>
                <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc danh mục</p>
                <a href="suppliers.php" style="color: #667eea; text-decoration: none; font-weight: 600;">
                    ← Xem tất cả nhà cung cấp
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include 'inc/footer-new.php'; ?>