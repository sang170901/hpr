<?php
// Simple product detail placeholder
$slug = $_GET['slug'] ?? ''; 
$product = null;
$fromDb = false;
if ($slug && file_exists(__DIR__ . '/backend/inc/db.php')) {
    try {
        require __DIR__ . '/backend/inc/db.php';
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $fromDb = true;
    } catch (Exception $e) {
        $product = null;
    }
}

if (!$product) {
    // fallback sample
    $product = [
        'name' => $slug ? str_replace('-', ' ', ucfirst($slug)) : 'Sản phẩm mẫu',
        'description' => 'Mô tả chi tiết sản phẩm sẽ được hiển thị ở đây. Đây là trang placeholder chờ backend hoàn thiện.',
        'price' => 0,
        'images' => 'assets/images/materials-icon.svg'
    ];
}

include __DIR__ . '/inc/header-new.php';
?>
    <main class="container" style="padding:140px 0 60px">
        <div class="product-detail">
            <div style="display:flex; gap:1.25rem; align-items:flex-start;">
                <div style="flex:0 0 420px;">
                    <img src="<?php echo htmlspecialchars($product['images']); ?>" style="width:100%; border-radius:10px;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div style="flex:1;">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="meta"><?php echo $fromDb ? htmlspecialchars($product['category'] ?? '') : '' ?></p>
                    <h3 style="color:var(--primary-color)"><?php echo $product['price'] ? number_format($product['price'],0,',','.') . '₫' : '' ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <div style="margin-top:1rem">
                        <a class="btn btn-primary" href="#">Liên hệ nhà cung cấp</a>
                        <a class="btn btn-outline" href="materials.php" style="margin-left:0.5rem">Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php include __DIR__ . '/inc/footer-new.php'; ?>
