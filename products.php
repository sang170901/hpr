<?php
// Simple products listing with 2-column layout
$products = [];
$search = trim($_GET['q'] ?? '');
$selectedCats = $_GET['cat'] ?? [];

// Function to normalize Vietnamese text for better search
function normalizeText($text) {
    $text = mb_strtolower($text, 'UTF-8');
    // Remove Vietnamese accents
    $accents = [
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ'
    ];
    $no_accents = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd'
    ];
    return str_replace($accents, $no_accents, $text);
}

// Sample products
$sampleImg = 'assets/images/materials-icon.svg';
$products = [
    ['name' => 'Sàn nhựa vinyl Resilient A', 'slug' => 'san-nhua-vinyl-resilient-a', 'price'=>250000, 'category'=>'Sàn', 'images'=>$sampleImg, 'description'=>'Sàn nhựa cao cấp cho công trình', 'company'=>'Công ty ABC'],
    ['name' => 'Sàn vinyl cao cấp B', 'slug' => 'san-vinyl-cao-cap-b', 'price'=>320000, 'category'=>'Sàn', 'images'=>$sampleImg, 'description'=>'Sàn vinyl chống nước tốt', 'company'=>'Công ty ABC'],
    ['name' => 'Gạch Ceramic Cao Cấp', 'slug' => 'gach-ceramic-cao-cap', 'price'=>120000, 'category'=>'Gạch', 'images'=>$sampleImg, 'description'=>'Gạch ceramic chống trơn, kích thước 600x600', 'company'=>'Công ty XYZ'],
    ['name' => 'Sơn Chống Thấm Eco', 'slug' => 'son-chong-tham-eco', 'price'=>450000, 'category'=>'Sơn', 'images'=>$sampleImg, 'description'=>'Sơn chống thấm, thân thiện môi trường', 'company'=>'Công ty DEF'],
    ['name' => 'Xi Măng Siêu Bền', 'slug' => 'xi-mang-sieu-ben', 'price'=>95000, 'category'=>'Xi măng', 'images'=>$sampleImg, 'description'=>'Xi măng độ bền cao, thích hợp công trình lớn', 'company'=>'Công ty GHI'],
    ['name' => 'Tường Panel Cách Nhiệt', 'slug' => 'tuong-panel-cach-nhiet', 'price'=>180000, 'category'=>'Tường', 'images'=>$sampleImg, 'description'=>'Panel tường cách nhiệt hiện đại', 'company'=>'Công ty JKL'],
];

// Extract unique categories from products
$categories = array_unique(array_map(function($product) {
    return $product['category'];
}, $products));

// Apply search filter
if (!empty($search)) {
    $normalizedSearch = normalizeText($search);
    $products = array_filter($products, function($product) use ($search, $normalizedSearch) {
        $normalizedName = normalizeText($product['name']);
        $normalizedDesc = normalizeText($product['description']);
        
        // Check for exact match first, then normalized match
        return stripos($product['name'], $search) !== false || 
               stripos($product['description'], $search) !== false ||
               strpos($normalizedName, $normalizedSearch) !== false ||
               strpos($normalizedDesc, $normalizedSearch) !== false;
    });
}

// Apply category filter
if (!empty($selectedCats)) {
    $products = array_filter($products, function($product) use ($selectedCats) {
        return in_array($product['category'], $selectedCats);
    });
}

include __DIR__ . '/inc/header-new.php';
?>

<style>
/* Reset và override styles cũ */
.container { max-width: none !important; padding: 0 !important; }

/* Main layout container */
.products-container {
    width: 80%;
    margin: 120px auto 60px auto;
    display: flex;
    gap: 30px;
    min-height: 70vh;
}

/* Left sidebar - 1/4 width */
.products-sidebar {
    flex: 0 0 25%;
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    height: fit-content;
    border: 1px solid #e2e8f0;
}

.sidebar-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
}

.search-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.95rem;
    margin-bottom: 16px;
}

.filter-option {
    display: block;
    padding: 8px 0;
    color: #374151;
    font-size: 0.9rem;
}

.filter-option input {
    margin-right: 8px;
}

.apply-btn {
    width: 100%;
    padding: 10px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item {
    padding: 6px 0;
    color: #374151;
    font-size: 0.9rem;
    border-bottom: 1px solid #f3f4f6;
}

.category-item:last-child {
    border-bottom: none;
}

.result-count {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

/* Right content - 3/4 width */
.products-main {
    flex: 1;
}

.products-header {
    text-align: center;
    margin-bottom: 30px;
}

.products-header h1 {
    font-size: 2rem;
    color: #1e293b;
    margin-bottom: 8px;
}

.products-header p {
    color: #6b7280;
}

/* Product grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.product-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    display: block;
}

.product-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
}

.product-image {
    width: 100%;
    height: 160px;
    background: #f3f4f6;
    border-radius: 8px;
    margin-bottom: 12px;
    object-fit: cover;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
    line-height: 1.3;
}

.product-category {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 8px;
}

.product-company {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.product-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ef4444;
    margin-bottom: 12px;
}

.product-btn {
    width: 100%;
    padding: 8px 16px;
    background: transparent;
    color: #2563eb;
    border: 1px solid #2563eb;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.2s ease;
}

.product-btn:hover {
    background: #2563eb;
    color: white;
}

/* Responsive */
@media (max-width: 968px) {
    .products-container {
        width: 95%;
        flex-direction: column;
    }
    
    .products-sidebar {
        flex: none;
        order: 2;
    }
    
    .products-main {
        order: 1;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }
}
</style>

<div class="products-container">
    <!-- Left Sidebar -->
    <div class="products-sidebar">
        <div class="sidebar-section">
            <h4 class="sidebar-title">Tìm kiếm và Lọc</h4>
            <form method="get" action="products.php">
                <input type="search" name="q" class="search-input" placeholder="Tìm vật liệu, ví dụ: gạch, sơn..." value="<?php echo htmlspecialchars($search) ?>">
                
                <h4 class="sidebar-title">Bạn đang tìm</h4>
                <?php foreach ($categories as $category): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="cat[]" value="<?php echo htmlspecialchars($category) ?>" <?php echo in_array($category, $selectedCats) ? 'checked' : '' ?>>
                        <?php echo htmlspecialchars($category) ?>
                    </label>
                <?php endforeach; ?>
                <button type="submit" class="apply-btn">Áp dụng</button>
            </form>
        </div>
        
        <div class="sidebar-section">
            <ul class="category-list">
                <li class="category-item" style="font-weight: 600; color: #ef4444;">Kết Cấu</li>
                <li class="category-item">Sàn</li>
                <li class="category-item">Tường</li>
                <li class="category-item">Mái</li>
                <li class="category-item">Sơn</li>
                <li class="category-item">Xi măng</li>
            </ul>
            <p class="result-count">Tìm thấy <?php echo count($products) ?> vật tư</p>
        </div>
    </div>
    
    <!-- Right Content -->
    <div class="products-main">
        <div class="products-header">
            <h1>Danh mục Vật liệu</h1>
            <p>Lọc, tìm kiếm và duyệt các vật liệu xây dựng theo danh mục.</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <a href="product.php?slug=<?php echo urlencode($p['slug']) ?>" class="product-card">
                <img src="<?php echo htmlspecialchars($p['images']) ?>" alt="<?php echo htmlspecialchars($p['name']) ?>" class="product-image">
                <h3 class="product-title"><?php echo htmlspecialchars($p['name']) ?></h3>
                <div class="product-category"><?php echo htmlspecialchars($p['category']) ?></div>
                <div class="product-company"><?php echo htmlspecialchars($p['company']) ?></div>
                <div class="product-price"><?php echo number_format($p['price'],0,',','.') ?>₫</div>
                <button class="product-btn">TÌM HIỂU THÊM</button>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/inc/footer-new.php'; ?>
