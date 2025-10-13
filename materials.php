<?php
// Simple material categories listing
$products = [];

// Sample products
$sampleImg = 'assets/images/materials-icon.svg';
$products = [
    ['name' => 'Sàn nhựa vinyl Resilient A', 'slug' => 'san-nhua-vinyl-resilient-a', 'price'=>250000, 'category'=>'Sàn'],
    ['name' => 'Gạch Ceramic Cao Cấp', 'slug' => 'gach-ceramic-cao-cap', 'price'=>120000, 'category'=>'Gạch'],
    ['name' => 'Sơn Chống Thấm Eco', 'slug' => 'son-chong-tham-eco', 'price'=>450000, 'category'=>'Sơn'],
    ['name' => 'Xi Măng Siêu Bền', 'slug' => 'xi-mang-sieu-ben', 'price'=>95000, 'category'=>'Xi măng'],
    ['name' => 'Tường Panel Cách Nhiệt', 'slug' => 'tuong-panel-cach-nhiet', 'price'=>180000, 'category'=>'Tường'],
];

// Extract unique categories from products
$categories = array_unique(array_map(function($product) {
    return $product['category'];
}, $products));

include __DIR__ . '/inc/header-new.php';
?>

<div class="products-container">
    <div class="products-sidebar">
        <div class="sidebar-section">
            <h4 class="sidebar-title">Danh mục vật liệu</h4>
            <ul class="category-list">
                <?php foreach ($categories as $category): ?>
                    <li class="category-item"> <?php echo htmlspecialchars($category); ?> </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="products-main">
        <h1>Phân Loại Vật Liệu</h1>
        <p>Chọn danh mục bên trái để xem chi tiết.</p>
    </div>
</div>

<style>
.products-container {
    width: 80%;
    margin: 120px auto;
    display: flex;
    gap: 30px;
    min-height: 70vh;
}

.products-sidebar {
    flex: 0 0 25%;
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
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

.category-list {
    list-style: none;
    padding: 0;
}

.category-item {
    font-size: 1rem;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.category-item:last-child {
    border-bottom: none;
}

.products-main {
    flex: 1;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.products-main h1 {
    font-size: 1.5rem;
    color: #1e293b;
    margin-bottom: 16px;
}
</style>

<?php include __DIR__ . '/inc/footer-new.php'; ?>
