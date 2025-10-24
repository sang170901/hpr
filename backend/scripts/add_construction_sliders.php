<?php
/**
 * Thêm slider về công trường và vật liệu xây dựng
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "<h2>🏗️ Thêm Slider Công Trường & Vật Liệu</h2>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:green;} .info{color:#0284c7;background:#e0f2fe;padding:15px;border-radius:8px;margin:20px 0;}</style>";
    
    // Kiểm tra số slider hiện có
    $count = $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    echo "<p>Hiện có: <strong>$count slider</strong></p>";
    
    // Slider mới với theme công trường/vật liệu
    $newSliders = [
        [
            'title' => 'Giải Pháp Xây Dựng Thông Minh',
            'subtitle' => 'Công nghệ 4.0 cho ngành xây dựng',
            'description' => 'Ứng dụng công nghệ tiên tiến, tối ưu hóa chi phí và thời gian thi công',
            'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=1920&h=700&fit=crop', // Công trường hiện đại
            'link' => 'technology.php',
            'link_text' => 'Khám phá công nghệ',
            'status' => 1,
            'display_order' => 4
        ],
        [
            'title' => 'Vật Liệu Xây Dựng Cao Cấp',
            'subtitle' => 'Chất lượng đảm bảo - Giá cả cạnh tranh',
            'description' => 'Hơn 10,000+ sản phẩm vật liệu từ các thương hiệu hàng đầu thế giới',
            'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1920&h=700&fit=crop', // Vật liệu xây dựng
            'link' => 'materials.php',
            'link_text' => 'Xem vật liệu',
            'status' => 1,
            'display_order' => 5
        ],
        [
            'title' => 'Dự Án Công Trình Lớn',
            'subtitle' => 'Đồng hành cùng nhà thầu & chủ đầu tư',
            'description' => 'Cung cấp vật liệu cho hơn 500+ công trình trọng điểm toàn quốc',
            'image' => 'https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=1920&h=700&fit=crop', // Công trường lớn
            'link' => 'suppliers.php',
            'link_text' => 'Liên hệ ngay',
            'status' => 1,
            'display_order' => 6
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO sliders (title, subtitle, description, image, link, link_text, status, display_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    echo "<h3>📌 Đang thêm slider mới...</h3>";
    
    foreach ($newSliders as $slider) {
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
        echo "<p class='success'>✓ Đã thêm: <strong>" . htmlspecialchars($slider['title']) . "</strong></p>";
    }
    
    echo "<div class='info'>";
    echo "<h3>📷 Về hình ảnh:</h3>";
    echo "<ul>";
    echo "<li>✅ Đang dùng ảnh chất lượng cao từ <strong>Unsplash</strong> (miễn phí)</li>";
    echo "<li>📐 Kích thước: 1920x700px (tối ưu cho slider)</li>";
    echo "<li>🎨 Theme: Công trường, vật liệu xây dựng, công nghệ</li>";
    echo "<li>🔄 Bạn có thể thay ảnh khác trong backend/sliders.php</li>";
    echo "</ul>";
    echo "<h4>💡 Muốn dùng ảnh riêng?</h4>";
    echo "<ol>";
    echo "<li>Upload ảnh vào thư mục <code>assets/images/</code></li>";
    echo "<li>Đổi tên thành: <code>slider-4.jpg</code>, <code>slider-5.jpg</code>, ...</li>";
    echo "<li>Vào backend → Sửa slider → Đổi đường dẫn thành: <code>assets/images/slider-4.jpg</code></li>";
    echo "</ol>";
    echo "</div>";
    
    $newCount = $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    echo "<h3 class='success'>✅ Hoàn thành!</h3>";
    echo "<p>Tổng số slider: <strong>$count → $newCount</strong> (+3 slider mới)</p>";
    
    echo "<hr>";
    echo "<p style='text-align:center;'>";
    echo "<a href='../sliders.php' style='display:inline-block;padding:12px 24px;background:#38bdf8;color:white;text-decoration:none;border-radius:8px;margin:10px;'>📊 Quản lý Slider</a>";
    echo "<a href='../../index.php' style='display:inline-block;padding:12px 24px;background:#059669;color:white;text-decoration:none;border-radius:8px;margin:10px;'>👁️ Xem Trang Chủ</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
    exit(1);
}
?>

