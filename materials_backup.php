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
        
        .materials-hero {
            /* make background span full viewport width */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 120px 0 80px;
            overflow: hidden;
            min-height: 420px; /* ensure visible hero area */
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
            font-size: 3.5rem;
            font-weight: 800;
            color: #1565c0;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 8px rgba(21,101,192,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: #1976d2;
            max-width: 900px; /* allow wider subtitle */
            margin: 0 auto 2.5rem;
            font-weight: 400;
        }
        
        .search-form {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
            margin: 0 auto;
            max-width: 1000px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .search-input, .category-select {
            padding: 1rem 1.5rem;
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            font-size: 1rem;
            background: #f8fafc;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #64b5f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(100,181,246,0.12);
        }
        
        .category-select {
            padding: 1rem 1.5rem;
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            font-size: 1rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-select:focus {
            outline: none;
            border-color: #64b5f6;
            background: white;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(66, 165, 245, 0.3);
        }

        .search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(66, 165, 245, 0.4);
        }
        
        .search-field, .category-field {
            flex: 1;
            min-width: 200px;
        }
        
        .search-field label, .category-field label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1565c0;
        }
        
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 20px;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .results-count {
            font-size: 1.1rem;
            color: #64748b;
        }
        
        .materials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .material-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            min-height: 280px; /* ensure consistent card heights */
        }
        
        .material-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .material-header {
            padding: 1.25rem 1.25rem 0.75rem;
            position: relative;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        
        .material-image {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: cover;
            display: block;
            border: 3px solid white;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        
        .material-category {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(66, 165, 245, 0.3);
        }
        
        .material-body {
            padding: 1rem 1rem 1rem;
            flex: 1 1 auto; /* allow body to grow and push footer down */
            display: flex;
            flex-direction: column;
        }
        
        .material-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .material-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            flex: 1 1 auto; /* allow description to fill available space */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .material-details {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }
        
        .material-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
        }
        
        .material-detail i {
            width: 16px;
            color: #42a5f5;
            font-size: 1rem;
        }
        
        .material-footer {
            padding: 0.75rem 1rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        
        .view-material {
            background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%);
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
            box-shadow: 0 2px 8px rgba(66, 165, 245, 0.3);
        }

        .view-material:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(66, 165, 245, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .material-date {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            margin: 2rem 0;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }
        
        .no-results h3 {
            font-size: 1.5rem;
            color: #475569;
            margin-bottom: 1rem;
        }
        
        .no-results p {
            color: #64748b;
            font-size: 1rem;
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
            
            .search-field, .category-field {
                min-width: unset;
            }
            
            .materials-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .results-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="materials-hero">
        <div class="hero-content">
            <h1 class="hero-title">Vật Liệu Xây Dựng</h1>
            <p class="hero-subtitle">
                Khám phá bộ sưu tập vật liệu xây dựng đa dạng từ các nhà cung cấp uy tín. 
                Chất lượng cao, giá cả hợp lý cho mọi dự án xây dựng.
            </p>
            
            <form class="search-form" method="GET" action="">
                <div class="search-field">
                    <label for="search">Tìm kiếm vật liệu</label>
                    <input type="text" 
                           class="search-input" 
                           id="search"
                           name="search" 
                           placeholder="Nhập tên vật liệu..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="category-field">
                    <label for="category">Danh mục</label>
                    <select class="category-select" id="category" name="category">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['subcategory']); ?>"
                                    <?php echo $category === $cat['subcategory'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['subcategory']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
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
            <div class="materials-grid">
                <?php foreach ($materials as $material): ?>
                    <div class="material-card">
                        <div class="material-header">
                            <?php if (!empty($material['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($material['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($material['name']); ?>" 
                                     class="material-image">
                            <?php else: ?>
                                <div class="material-image" style="background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tools" style="color: white; font-size: 1.5rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($material['subcategory'])): ?>
                                <div class="material-category">
                                    <?php echo htmlspecialchars($material['subcategory']); ?>
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
                                    <div class="material-detail">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo number_format($material['price']); ?> VND</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($material['supplier_id'])): ?>
                                    <div class="material-detail">
                                        <i class="fas fa-building"></i>
                                        <span>Nhà cung cấp</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="material-footer">
                            <div class="material-date">
                                <?php echo date('d/m/Y', strtotime($material['created_at'])); ?>
                            </div>
                            
                            <a href="product-detail.php?id=<?php echo $material['id']; ?>" 
                               class="view-material" target="_blank">
                                <i class="fas fa-eye"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 3rem;">
                <div class="pagination" style="display: flex; gap: 0.5rem;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" 
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>"
                           style="padding: 0.75rem 1rem; background: <?php echo $i === $page ? '#42a5f5' : 'white'; ?>; 
                                  color: <?php echo $i === $page ? 'white' : '#42a5f5'; ?>; text-decoration: none; 
                                  border-radius: 8px; border: 2px solid #42a5f5; font-weight: 600;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'inc/footer-new.php'; ?>
</body>
</html>
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
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.page-link.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.page-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}

@media (max-width: 768px) {
    .materials-container {
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
    
    .materials-grid {
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

<div class="materials-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="materials-container">
            <h1 class="page-title">VẬT LIỆU</h1>
            <p class="page-subtitle">
                Vật liệu xây dựng bao gồm gỗ, thép, bê tông, gạch, đá và các vật liệu khác được sử dụng trong xây dựng.<br>
                Chúng được sử dụng để xây dựng móng, tường, mái, sàn và các kết cấu quan trọng khác của công trình.
            </p>
        </div>
    </div>

    <div class="materials-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <span class="breadcrumb-item"><a href="/vnmt/">Trang chủ</a></span>
            <span class="breadcrumb-item active">Vật liệu</span>
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
                    <button type="submit" class="filter-btn">
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

        <?php if (!empty($materials)): ?>
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
                <h3 class="no-results-title">Không tìm thấy vật liệu nào</h3>
                <p class="no-results-text">
                    Hãy thử thay đổi từ khóa tìm kiếm hoặc bộ lọc để tìm thấy vật liệu phù hợp.
                </p>
                <a href="materials.php" class="view-details">
                    <i class="fas fa-refresh"></i> Xem tất cả vật liệu
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/inc/footer-new.php'; ?>
