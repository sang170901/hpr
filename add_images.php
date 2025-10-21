<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== THÊM HÌNH ẢNH NGẪU NHIÊN CHO SẢN PHẨM VÀ NHÀ CUNG CẤP ===\n\n";

// Mảng hình ảnh sản phẩm theo danh mục
$productImages = [
    // Xi măng, vật liệu xây dựng
    'xi-mang' => [
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400',
        'https://images.unsplash.com/photo-1594736797933-d0fce76503e0?w=400',
        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400'
    ],
    
    // Thép
    'thep' => [
        'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?w=400',
        'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=400',
        'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=400'
    ],
    
    // Gạch, ceramic
    'gach' => [
        'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400',
        'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400',
        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400',
        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400',
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400'
    ],
    
    // Sơn
    'son' => [
        'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=400',
        'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?w=400',
        'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400',
        'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=400'
    ],
    
    // Kính
    'kinh' => [
        'https://images.unsplash.com/photo-1545558014-8692077e9b5c?w=400',
        'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=400',
        'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=400'
    ],
    
    // Nội thất
    'noi-that' => [
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400',
        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400',
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
        'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=400',
        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400',
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400',
        'https://images.unsplash.com/photo-1549497538-303791108f95?w=400',
        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400',
        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400',
        'https://images.unsplash.com/photo-1549497538-303791108f95?w=400',
        'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=400',
        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400'
    ],
    
    // Cảnh quan
    'canh-quan' => [
        'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400',
        'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400',
        'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=400',
        'https://images.unsplash.com/photo-1593018194758-086406bf2b21?w=400',
        'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400',
        'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=400'
    ],
    
    // Thiết bị điện
    'thiet-bi-dien' => [
        'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400',
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400',
        'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?w=400',
        'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400'
    ],
    
    // Thiết bị vệ sinh
    'thiet-bi-ve-sinh' => [
        'https://images.unsplash.com/photo-1620626011761-996317b8d101?w=400',
        'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=400',
        'https://images.unsplash.com/photo-1553688738-a278b9f063ad?w=400',
        'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=400',
        'https://images.unsplash.com/photo-1620626011761-996317b8d101?w=400',
        'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=400'
    ],
    
    // Sàn gỗ
    'san-go' => [
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400',
        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400',
        'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400'
    ],
    
    // Ống nhựa, phụ kiện
    'ong-nhua' => [
        'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400',
        'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=400',
        'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?w=400'
    ]
];

// Logo nhà cung cấp mẫu
$companyLogos = [
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    'https://images.unsplash.com/photo-1516802273409-68526ee1bdd6?w=200',
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200',
    'https://images.unsplash.com/photo-1560472355-536de3962603?w=200',
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    'https://images.unsplash.com/photo-1560472355-536de3962603?w=200',
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200',
    'https://images.unsplash.com/photo-1516802273409-68526ee1bdd6?w=200',
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    'https://images.unsplash.com/photo-1560472355-536de3962603?w=200',
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200',
    'https://images.unsplash.com/photo-1516802273409-68526ee1bdd6?w=200',
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    'https://images.unsplash.com/photo-1560472355-536de3962603?w=200',
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200',
    'https://images.unsplash.com/photo-1516802273409-68526ee1bdd6?w=200',
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    'https://images.unsplash.com/photo-1560472355-536de3962603?w=200',
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200',
    'https://images.unsplash.com/photo-1516802273409-68526ee1bdd6?w=200'
];

// 1. Cập nhật hình cho nhà cung cấp
echo "1. CẬP NHẬT LOGO CHO NHÀ CUNG CẤP:\n";
$suppliers = $pdo->query("SELECT id, name FROM suppliers ORDER BY id")->fetchAll();
foreach ($suppliers as $index => $supplier) {
    $logo = $companyLogos[$index % count($companyLogos)];
    $stmt = $pdo->prepare("UPDATE suppliers SET logo = ? WHERE id = ?");
    $stmt->execute([$logo, $supplier['id']]);
    echo "  ✓ {$supplier['name']} -> Logo cập nhật\n";
}

// 2. Cập nhật hình cho sản phẩm
echo "\n2. CẬP NHẬT HÌNH ẢNH CHO SẢN PHẨM:\n";
$products = $pdo->query("SELECT id, name, slug FROM products ORDER BY id")->fetchAll();

foreach ($products as $index => $product) {
    $productName = strtolower($product['name']);
    $slug = $product['slug'];
    
    // Xác định loại sản phẩm và chọn hình phù hợp
    $imageCategory = 'noi-that'; // default
    
    if (strpos($productName, 'xi măng') !== false) {
        $imageCategory = 'xi-mang';
    } elseif (strpos($productName, 'thép') !== false) {
        $imageCategory = 'thep';
    } elseif (strpos($productName, 'gạch') !== false || strpos($productName, 'granite') !== false || strpos($productName, 'mosaic') !== false) {
        $imageCategory = 'gach';
    } elseif (strpos($productName, 'sơn') !== false) {
        $imageCategory = 'son';
    } elseif (strpos($productName, 'kính') !== false) {
        $imageCategory = 'kinh';
    } elseif (strpos($productName, 'tủ') !== false || strpos($productName, 'bàn') !== false || strpos($productName, 'sofa') !== false || strpos($productName, 'giường') !== false || strpos($productName, 'mdf') !== false || strpos($productName, 'hdf') !== false || strpos($productName, 'cửa') !== false) {
        $imageCategory = 'noi-that';
    } elseif (strpos($productName, 'cây') !== false || strpos($productName, 'tưới') !== false || strpos($productName, 'đèn sân vườn') !== false || strpos($productName, 'hạt giống') !== false) {
        $imageCategory = 'canh-quan';
    } elseif (strpos($productName, 'máy lạnh') !== false || strpos($productName, 'điều hòa') !== false || strpos($productName, 'tủ điện') !== false) {
        $imageCategory = 'thiet-bi-dien';
    } elseif (strpos($productName, 'bồn cầu') !== false || strpos($productName, 'lavabo') !== false || strpos($productName, 'vòi') !== false || strpos($productName, 'gương') !== false) {
        $imageCategory = 'thiet-bi-ve-sinh';
    } elseif (strpos($productName, 'sàn') !== false || strpos($productName, 'thảm') !== false) {
        $imageCategory = 'san-go';
    } elseif (strpos($productName, 'ống') !== false || strpos($productName, 'phụ kiện') !== false) {
        $imageCategory = 'ong-nhua';
    }
    
    // Chọn hình ngẫu nhiên từ category
    $images = $productImages[$imageCategory];
    $randomImage = $images[array_rand($images)];
    
    // Cập nhật featured_image
    $stmt = $pdo->prepare("UPDATE products SET featured_image = ? WHERE id = ?");
    $stmt->execute([$randomImage, $product['id']]);
    
    echo "  ✓ " . ($index + 1) . ". {$product['name']} -> {$imageCategory}\n";
}

echo "\n=== THÊM HÌNH ẢNH CHO SẢN PHẨM CÓ SẴN (3 SẢN PHẨM ĐẦU) ===\n";

// Cập nhật thêm hình cho 3 sản phẩm đầu có sẵn
$existingProducts = [
    ['name' => 'Sàn gỗ công nghiệp HDF - Vân sồi Âu 12mm', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400'],
    ['name' => 'Gạch granite 60x60 Đồng Tâm - Vân đá tự nhiên', 'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400'],
    ['name' => 'Tủ bếp Acrylic cao cấp - Màu trắng bóng', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400']
];

$oldProducts = $pdo->query("SELECT id, name FROM products WHERE id <= 3 ORDER BY id")->fetchAll();
foreach ($oldProducts as $index => $oldProduct) {
    if (isset($existingProducts[$index])) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, featured_image = ? WHERE id = ?");
        $stmt->execute([
            $existingProducts[$index]['name'],
            $existingProducts[$index]['image'],
            $oldProduct['id']
        ]);
        echo "  ✓ Cập nhật: {$existingProducts[$index]['name']}\n";
    }
}

echo "\n=== THỐNG KÊ ===\n";
$supplierCount = $pdo->query("SELECT COUNT(*) FROM suppliers WHERE logo IS NOT NULL AND logo != ''")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products WHERE featured_image IS NOT NULL AND featured_image != ''")->fetchColumn();

echo "✅ Nhà cung cấp có logo: {$supplierCount}\n";
echo "✅ Sản phẩm có hình: {$productCount}\n";

echo "\n🌐 Truy cập để xem:\n";
echo "- Sản phẩm: http://localhost:8080/vnmt/backend/products.php\n";
echo "- Nhà cung cấp: http://localhost:8080/vnmt/backend/suppliers.php\n";
echo "\n✨ Bây giờ tất cả sản phẩm và nhà cung cấp đều có hình ảnh đẹp!\n";
?>