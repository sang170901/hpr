<?php
/**
 * Seed sample sliders for demonstration
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "Adding sample sliders...\n\n";
    
    // Check if we already have sliders
    $count = $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    
    if ($count > 0) {
        echo "ℹ️  Database already has $count slider(s).\n";
        echo "Do you want to add more sample sliders anyway? (This won't delete existing ones)\n";
        // For automated script, we'll skip if sliders exist
        exit(0);
    }
    
    $sampleSliders = [
        [
            'title' => 'Chào mừng đến VNMaterial',
            'subtitle' => 'Nền tảng vật liệu xây dựng hàng đầu Việt Nam',
            'description' => 'Khám phá hàng ngàn sản phẩm vật liệu xây dựng chất lượng cao',
            'image' => 'assets/images/slider-1.jpg',
            'link' => 'products.php',
            'link_text' => 'Khám phá ngay',
            'status' => 1,
            'display_order' => 1
        ],
        [
            'title' => 'Vật liệu xanh - Tương lai bền vững',
            'subtitle' => 'Giải pháp thân thiện với môi trường',
            'description' => 'Hơn 500+ sản phẩm vật liệu xanh được chứng nhận quốc tế',
            'image' => 'assets/images/slider-2.jpg',
            'link' => 'materials.php',
            'link_text' => 'Tìm hiểu thêm',
            'status' => 1,
            'display_order' => 2
        ],
        [
            'title' => 'Đối tác tin cậy',
            'subtitle' => 'Kết nối với hơn 100+ nhà cung cấp uy tín',
            'description' => 'Chúng tôi hợp tác cùng các thương hiệu hàng đầu trong ngành',
            'image' => 'assets/images/slider-3.jpg',
            'link' => 'suppliers.php',
            'link_text' => 'Xem đối tác',
            'status' => 1,
            'display_order' => 3
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO sliders (title, subtitle, description, image, link, link_text, status, display_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($sampleSliders as $slider) {
        $stmt->execute([
            $slider['title'],
            $slider['subtitle'],
            $slider['description'],
            $slider['image'],
            $slider['link'],
            $slider['link_text'],
            $slider['status'],
            $slider['display_order']
        ]);
        echo "✓ Added: {$slider['title']}\n";
    }
    
    echo "\n✓ Successfully added " . count($sampleSliders) . " sample sliders!\n";
    echo "\n📌 Note: You may need to update the image paths to match your actual images.\n";
    echo "   Go to backend/sliders.php to edit the sliders.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

